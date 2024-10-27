<?php

namespace App\Services\CounterService;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use App\Services\Token\TokenService;

class CounterService
{
    private TokenService $tokenService;
    private string $counterServiceUrl;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
        $this->counterServiceUrl = config('services.counter-service.url');
    }

    /**
     * @throws ConnectionException
     */
    public function incrementUnreadCounter(int $userId): bool
    {
        $token = $this->tokenService->getTokenForService($userId);

        $response = Http::withToken($token)
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->post("{$this->counterServiceUrl}/counters/{$userId}/increment");

        return $response->successful();
    }

    /**
     * @throws ConnectionException
     */
    public function decrementUnreadCounter(int $userId): bool
    {
        $token = $this->tokenService->getTokenForService($userId);

        $response = Http::withToken($token)
            ->withHeaders(['Accept' => 'application/json'])
            ->post("{$this->counterServiceUrl}/counters/{$userId}/decrement");

        return $response->successful();
    }

    /**
     * @throws ConnectionException
     */
    public function setUnreadCounter(int $userId, int $countOfUnreadMessages): bool
    {
        $token = $this->tokenService->getTokenForService($userId);

        $response = Http::withToken($token)
            ->withHeaders(['Accept' => 'application/json'])
            ->post("{$this->counterServiceUrl}/counters/{$userId}/sync", [
                "count-of-unread-messages" => $countOfUnreadMessages,
            ]);

        return $response->successful();
    }
}
