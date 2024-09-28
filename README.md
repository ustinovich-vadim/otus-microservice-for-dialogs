# OTUS homework-8

This project is a Laravel application set up to run in Docker containers. Follow the steps below to get started.

## Prerequisites

Make sure you have the following software installed on your machine:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)
- [Git](https://git-scm.com/)

## Installation

1. **Clone and configure the monolith repository**

   ```bash
   git clone --branch feature/microservice-for-dialogs https://github.com/ustinovich-vadim/otus-homeworks.git
   cd otus-homeworks
    ```
    Follow the setup instructions provided in the README.md of the monolith repository to properly configure and run the project.


1. **Clone the repository**

    ```bash
    git clone git@github.com:ustinovich-vadim/otus-microservice-for-dialogs.git
    cd dialog-microservice
   ```

2. **Copy the .env.example file to .env**

    ```bash
   cp .env.example .env

3. **Update the .env file with your configuration**
   Make sure to set the necessary environment variables, especially the database connection details. Example:
    ```env
    DB_CONNECTION=pgsql
    DB_HOST=svc-db
    DB_PORT=5432
    DB_DATABASE=postgres
    DB_USERNAME=postgres
    DB_PASSWORD=secret
    POSTGRESS_PORT=5434
    COUNT_OF_USERS=150
    JWT_SECRET=secret_for_token

4. **Build and start the Docker containers**
    ```bash
    docker-compose up -d
5. **Install PHP dependencies**
    ```bash
    docker-compose exec app composer install
6. **Run migrations and seeders**
    ```bash
    docker-compose exec app php artisan migrate:fresh

7. **Usage API Endpoints**

- **Send Message to user - POST /api/messages/{user_id}/send**

  **Headers:**
    - `Accept: application/json`
    - `Content-Type: application/json`
    - `Authorization': 'Bearer token`
```json
  {
    "text": "Text of message"
  }
```

- **Get list of messages from dialog with user - GET /api/messages/{user_id}/list**

  **Headers:**
    - `Accept: application/json`
    - `Content-Type: application/json`
    - `Authorization': 'Bearer token`
