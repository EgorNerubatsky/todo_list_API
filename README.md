
# Todo List API

The application implements an API that allows managing the task list.

## Demands

- PHP version: 8.1 or higher
- Framework: Laravel 10 or higher
- installed Docker (https://docs.docker.com/desktop/install/ubuntu/)
- installed Docker-compose (https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-compose-on-ubuntu-20-04)
- IDE PhpStorm

## Installation

1. Clone a repository from GitHub
```
git clone https://github.com/EgorNerubatsky/todo_list_API.git
```
2. Run "Docker-compose.yml"

```
docker-compose up --build
```
3. Run composer
```
docker-compose exec app composer update
```
4. Run migration and seed`s
```
docker-compose exec app php artisan migrate:refresh --seed
```
5. Generate application key (if not already generated)
```
docker-compose exec app php artisan key:generate
```

## Usage

In the root of the project, there is API documentation (API_documentation.json) 
for complete information and further use.




