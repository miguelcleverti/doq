# joaoinacio/doq

Quickly setup and use multiple configurations for containerized services using docker compose.

**doq** is a configuration helper tool and wrapper for the [docker-compose](https://docs.docker.com/compose) utility.
The goals are to:
 * Simplify the management and use of multiple configuration files on a single project.
 * Workaround current docker-compose shortcomings (such as mount point paths always being relative to the first config file directory, not the current working directory).
 * Provide a way to retrieve, store and use configuration templates:
 * Be simple and easy to use, on any project:

   ```
   doq init --template=my-uber-docker-conf && doq start
   ```

As a helper tool, doq does not provide any docker-compose.yml configuration files or templates.
For full reference on docker-compose.yml file, see [compose-file](https://docs.docker.com/compose/compose-file/).


## Requirements

doq depends on docker-compose and docker:

* [docker](https://www.docker.com/products/docker) for service containerization.
* [docker-compose](https://docs.docker.com/compose/install/) to define and configure the needed services and their relations/dependencies.

## Installation

Get doq.phar

 ``` sh
  curl -OU https:/.../doq.phar

 ```

Installing globally: for ease of use, the rest of this doc assumes doq is installed system-wide:

  ``` sh
  chmod +x doq.phar
  sudo cp doq.phar /usr/local/bin/doq
 ```

 If that is not the case and doq is installed locally, commands should be run with `php doq.phar ...` instead.


## Quick start

 * Create a docker-compose configuration for the current project, optionally specifying a config name and/or a custom configuration template.

    ``` sh
    doq init [-c,--config <name>] [--template=<name, path or url>]
    ```

 * Start service containers.

    ``` sh
    doq start [-c,--config <name>]
    ```


## Command Reference

doq implements commands for managing configuration, templates and services, as well as interaction with containers:

#### Initialization:

 * **init** `[-c,--config <name>] [--template=<name, path or url>]`

   Setup a new environment configuration, optionally using a pre-existing template.

   `configuration` name defaults to `'default'` if not specified.
   If no configuration template is specified, it will check if a template with the same name as the configuration exists under `~/.docker-compose/`.

#### Handling of docker-compose configuration templates:

 * **template:import** `<template-name> <path or url>`

   Copies a docker-compose configuration file from a path or url, and saves it as a template under `~/.docker-compose/` using the provided name.

 * **template:list**

   List all configuration templates under the `~/.docker-compose/` folder, together with services and images defined in every configuration.

 * **template:save** `[-c,--config <name>] <template-name>`

   Copy the compose file from the local configuration and stores it as a template under `~/.docker-compose/`, using the provided name.

#### Setting up custom service definitions:

 * **service:add** `[-c,--config <name>] <service-name> [--image] [--ports] [--command] [--mounts] [--links] [--envs]`

   Adds a new service, or updates a existing one, to the compose configuration of the specified environment.

 * **service:list** `[-c,--config <name>]`

   Lists all the defined services in the compose configuration file of the specified environment.

 * **service:remove** `[-c,--config <name>]  <service-name>`

  Removes a service from the compose configuration of the specified environment.


#### Starting, stopping, and interacting with containers:

 * **start** `[-c,--config <name>]`

    Starts all the docker service containers of the existing configuration environment, creating them if needed. If no config is specified, *'default'* will be used).

 * **stop** `[-c,--config <name>]`

    Stops all the specified environment services but does not destroy them, can be later restarted using the `start` command.

 * **status** `[-c,--config <name>]`

    Shows the status of the running service containers for the config environment.

 * **logs** `[-c,--config <name>] [<service-name>]`

 * **exec** `[-c,--config <name>] [<service-name>] <command>`

 * **destroy** `[-c,--config <name>]`

    Stops and removes all containers, networks, volumes, and images for the chosen configuration environment (or *'default'* if none is specified).


### Building doq

The `doq.phar` archive can be build from source using the [phar-composer](https://github.com/clue/phar-composer) utility:

```sh
php -d phar.readonly=0 bin/phar-composer.phar build . doq.phar
```
