# Load environment variables from .env file
ifneq (,$(wildcard .env))
    include .env
    export
endif

# Extract environment variables and convert them to build arguments
export BUILD_ARGS=$(shell cat .env | grep -v '^#' | sed '/^$$/d' | awk -F= '{print "--build-arg " $$1 "=" $$2}' | xargs)

# Define a target for building the containers
.PHONY: build
build:
	@echo "Building Docker containers..."
	@docker-compose build $(BUILD_ARGS)
	@docker-compose up

# Define a target for running the containers
.PHONY: up
up:
	@echo "Running Docker containers..."
	@docker-compose up

# Define a target for stopping the containers
.PHONY: down
down:
	@echo "Stopping Docker containers..."
	@docker-compose down
