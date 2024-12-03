[![Build status](https://dev.azure.com/APS-SD-Stewards/APS-SD/_apis/build/status/proscrumdev.battleship-cpp-CI)](https://dev.azure.com/APS-SD-Stewards/APS-SD/_build/latest?definitionId=21)

# Battleship PHP

A simple game of Battleship, written in PHP. The purpose of this repository is to serve as an entry point into coding exercises and it was especially created for scrum.orgs Applying Professional Scrum for Software Development course (www.scrum.org/apssd). The code in this repository is unfinished by design.
Created by Sergey https://github.com/2heoh 

## Getting started

This project requires a php 8.4 or higher. To prepare to work with it, pick one of these
options:

### Run locally

Run battleship with composer

```bash
composer run game
```

#### Execute tests with composer

Install dependencies
```bash
composer update
```

Run tests
```bash
composer run test
```

### Docker

If you don't want to install anything php-related on your system, you can
run the game inside Docker instead.

#### Run a Docker Container from the Image

```bash
docker run -it -v ${PWD}:/battleship -w /battleship composer bash
```

#### Launching the game

```bash
composer run game
```

#### Running the Tests

Don't forget to install dependencies ;)
```bash
composer update
```

Run tests:
```
composer run test
```

#### Or use simplified commands:

```bash
docker run -it -v ${PWD}:/battleship -w /battleship composer composer run test
```

```bash
docker run -it -v ${PWD}:/battleship -w /battleship composer composer run unittest
```

```bash
docker run -it -v ${PWD}:/battleship -w /battleship composer composer run game
```

## Troubleshooting 

1. On my ubuntu virtual server on DO I needed to install:
```bash
apt-get install composer php7.2-mbstring php7.2-dom
```
