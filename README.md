# Real-Time NBA Simulation

A real-time NBA simulation project developed using Laravel and Vue technologies.

## Getting Started

### Prerequisites

- Docker and Docker Compose installed on your system

### Installation

1. Clone this repository to your local machine

```
git clone git@github.com:utkuyilgin/nba-simulation.git
```

2. Create environment files from the examples:
   ```
   cp backend/.env.example backend/.env
   cp frontend/.env.example frontend/.env
   ```

3. Start the application using Docker Compose:
   ```
   docker-compose up --build -d
   ```
   This command:
    - Builds and starts all necessary containers
    - Automatically runs composer install and npm install, but before the 4th step, wait for 3-5 seconds. Here, the composer install and npm install processes may take 3-5 seconds, so please wait. You may encounter an 'artisan command not found' error.
   

4. Enter the backend container to run migrations and seed the database:
   ```
   docker exec -it backend_app sh
   php artisan migrate && php artisan db:seed
   ```

5. Start the queue worker for real-time score updates and transactions:
   ```
   php artisan queue:work
   ```

## Accessing the Application

- **Backend API**: [http://localhost:9090](http://localhost:9090)
- **Frontend Interface**: [http://localhost:8020](http://localhost:8020)

## Important Notes

- **Port Configuration**: The project is configured to use ports that are less likely to conflict with existing services. If you need to modify ports, please make changes in both the `docker-compose.yaml` and `Dockerfile` files.

- **Database Storage**: Database files are stored in a `db_data` folder in the project directory via the following volume configuration:
  ```
  - ./my_db_data:/var/lib/mysql
  ```
  To change this behavior, you can remove the `./` prefix to use Docker's internal volume system instead.

- **Pusher Configuration**: This project uses my personal Pusher credentials for real-time updates. If you want to use your own Pusher account, please update the credentials in the `.env` files of both frontend and backend.

## Features

- Real-time score updates
- Transaction simulation
- Web interface built with Vue.js
- Backend API powered by Laravel

## Technologies

- Laravel (PHP Framework)
- Vue.js (JavaScript Framework)
- Docker (Containerization)
- MySQL (Database)
- Pusher (Real-time WebSockets)