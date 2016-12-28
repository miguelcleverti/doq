# joaoinacio/doq

Quickly setup and use multiple configurations for containerized services using docker compose.

doq is a configuration helper tool and wrapper for the [docker-compose](https://docs.docker.com/compose) utility.

The goal is to simplify the management and use of different configuration files, as well as workaround current docker-compose shortcomings
(such as mount point paths always being relative to the first config file directory, not the current working directory).

In addition, it allows configurations to be stored and used as templates, for a quick and easy setup:
```
doq init --template=my-uber-conf && doq start
```

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

There are commands for the following steps:

 * Initialization
   * [init](#init)
 * Handling of docker-compose configuration templates
   * [template:import](#templateimport)
   * [template:list](#templatelist)
   * [template:save](#templatesave)
 * Setting up custom service definitions
   * [service:add](#serviceadd)
   * [service:list](#servicelist)
   * [service:remove](#serviceremove)
 * starting, stopping, and interacting with containers
   * [start](#start)
   * [stop](#stop)
   * [status](#status)
   * [logs](#logs)
   * [exec](#exec)
   * [destroy](#destroy)


* #### init

    Setup a new environment configuration, optionally using a pre-existing template.

    `environment` name defaults to `'default'` if not specified.

    If no configuration template is specified, it will check if a template with the same
    name as the environment exists under `~/.docker-compose/`.

    If no template is found, a compose file will be created without any services.

    ```
    doq init [<environment>] [--template=<name, path or url>]
    ```

* #### template:import

    Copies a docker-compose configuration file from a path or url, and saves it as a template under `~/.docker-compose/` using the provided name.

    ``` bash
    doq template:import <template-name> <path or url>
    ```

* #### template:list

    List all configuration templates under the `~/.docker-compose/` folder, together with services and images defined in every configuration.

    ```sh
    doq template:list
    ```

* #### template:save

    Copy the compose file from the local configuration and stores it as a template under `~/.docker-compose/`, using the provided name.

    ``` bash
    doq template:save [-c,--config <name>] <template-name>
    ```

* #### service:add

   Adds a new service, or updates a existing one, to the compose configuration of the specified environment.

   ``` bash
   doq service:add [-c,--config <name>] <service-name> [--image] [--ports] [--command] [--mounts] [--links] [--envs]
   ```

 * #### service:list

   Lists all the defined services in the compose configuration file of the specified environment.

   ``` bash
   doq service:list [-c,--config <name>]
   ```

* #### service:remove

   Removes a service from the compose configuration of the specified environment.

   ``` bash
   doq service:remove [-c,--config <name>] <service-name>
   ```

 * #### start

   Starts all the docker service containers of the existing configuration environment, creating them if needed. If no config is specified, *'default'* will be used).

   ``` bash
   doq start [-c,--config <name>]
   ```

 * #### stop

   Stops all the specified environment services but does not destroy them, can be later restarted using the `start` command.

   ``` bash
   doq stop [-c,--config <name>]
   ```

 * #### status

   Shows the status of the running service containers for the config environment.

   ``` bash
   doq status  [-c,--config <name>]
   ```

 * #### destroy

   Stops and removes all containers, networks, volumes, and images for the chosen configuration environment (or *'default'* if none is specified).

   ``` bash
   doq destroy [-c,--config <name>]
   ```

## Templates

As a helper tool, doq does not provide any docker-compose configuration files/templates.
Pre-existing templates for a full PHP web stack can be found at ...
For full reference on docker-compose.yml file, see [compose-file](https://docs.docker.com/compose/compose-file/)


### Building doq

The `doq.phar` archive can be build from source using the [phar-composer](https://github.com/clue/phar-composer) utility:

```sh
php -d phar.readonly=0 bin/phar-composer.phar build . doq.phar
```
