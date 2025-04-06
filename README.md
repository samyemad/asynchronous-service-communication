# Project Overview

## Introduction

This project is a comprehensive system designed to manage recipes and ingredients, utilizing a Docker-based setup to separate the backend, frontend, and database services. The project structure is designed to be scalable and easy to maintain, allowing for future enhancements and integrations.

## Architecture

The project is divided into several Docker containers, each serving a different role:

- **Backend**: A Symfony-based API that handles all backend logic and interactions with the database.
- **Nginx**: Serves as a reverse proxy, routing requests to the appropriate backend service.
- **Database**: A MySQL database that stores all data.

## Setup

To get the project up and running, follow these steps:

1. Clone the repository.
2. Navigate to the project directory.
3. Ensure Docker is installed on your system.
4. Run `make build` to build the Docker images.
5. Run `make up` to start all services.

## Directories

- `backend/`: Contains all the code for the Symfony backend.
- `nginx/`: Configuration files for the Nginx server.
- `docs/`: Documentation related to the two approaches.

## Documentation

Detailed information about the Many Approaches:
- [First Approach (Implemented) ](docs/FirstApproach.md) 
- [Second Approach with notification Context](docs/SecondApproach.md)

Detailed information about API:
- [Charging Session Request ](apiDocs/ChargingSessionRequestAPI.md)
- [Authorize Driver ](apiDocs/AuthorizeDriverAPI.md)



## Note on the Implementation

- I don't implement outbox pattern
- I don't implement saga choreography with failed events.
- I don't implement retry mechanism in callback


