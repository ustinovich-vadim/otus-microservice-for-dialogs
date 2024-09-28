<?php

namespace App\Services\Token;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

readonly class TokenService
{
    private ?int $authUserId;
    public function isTokenValid(string $token): bool
    {
        $secretKey = new Key(env('JWT_SECRET'), 'HS256');
        $headers = null;
        try {
            $decoded = JWT::decode($token, $secretKey, $headers);
            $this->authUserId = $decoded->sub;

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAuthUserId(): ?int
    {
        return $this->authUserId;
    }
}
