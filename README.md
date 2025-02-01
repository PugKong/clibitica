[![License: Unlicense](https://img.shields.io/badge/license-Unlicense-blue.svg)](http://unlicense.org/)
[![Workflow: tests](https://github.com/PugKong/clibitica/actions/workflows/tests.yml/badge.svg)](https://github.com/PugKong/clibitica/actions/workflows/tests.yml)
[![Workflow: release](https://github.com/PugKong/clibitica/actions/workflows/release.yml/badge.svg)](https://github.com/PugKong/clibitica/actions/workflows/release.yml)
[![Coverage Status](https://coveralls.io/repos/github/PugKong/clibitica/badge.svg?branch=main)](https://coveralls.io/github/PugKong/clibitica?branch=main)
[![GitHub Release](https://img.shields.io/github/release/PugKong/clibitica.svg?style=flat)](https://github.com/PugKong/clibitica/releases/latest)

# clibitica

**clibitica** is a command-line interface (CLI) tool for interacting with [Habitica](https://habitica.com),
a gamified task management and productivity platform. With clibitica, you can manage tasks, tags, and other features
of your Habitica account directly from the terminal.

## Installation

Download the prebuilt binary from [Releases](https://github.com/PugKong/clibitica/releases) page or use `ghcr.io/pugkong/clibitica` docker image.

## Usage

Set the `CLIBITICA_API_KEY` and `CLIBITICA_API_USER` environment variables.

Run `clibitica list` to see the available commands:

```
$ clibitica list
clibitica 0.1.1

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display help for the given command. When no command is given display help for the task:list command
      --silent          Do not output any message
  -q, --quiet           Only errors are displayed. All other output is suppressed
  -V, --version         Display this application version
      --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  completion      Dump the shell completion script
  help            Display help for a command
  list            List commands
 cron
  cron:run        This causes cron to run, it will immediately apply damage for incomplete due Dailies
 tag
  tag:create      [tag:add] Create a new tag
  tag:delete      Delete a tag
  tag:list        Get tags
  tag:update      Update a tag
 task
  task:checklist  Manage checklist for a task
  task:create     [task:add] Create a new task
  task:delete     Delete a task
  task:info       Show task details
  task:list       List tasks
  task:score      Score task up or down
  task:tag        Manage tags for a task
  task:update     Update a task
 user
  user:stats      Show user stats
```

## Contributing

Contributions are welcome! Feel free to submit a pull request or open an issue on the GitHub repository.

## License

This project is licensed under the Unlicense License. See the [LICENSE](LICENSE) file for details.

## Acknowledgments

This project would not have been possible without the outstanding tools and libraries created by the open-source
community. I would like to extend my gratitude to the following projects and their maintainers for their invaluable
contributions:

- **[Symfony Components](https://symfony.com/components)**: For providing a robust and modular foundation to build this CLI tool
- **[PHPUnit](https://phpunit.de/)**: For providing a reliable and feature-rich framework for testing
- **[PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)**: For maintaining code style and ensuring consistency across the project
- **[PHPStan](https://phpstan.org/)**: For catching bugs early through static analysis and enforcing code quality
- **[Box Project](https://github.com/box-project/box)**: For simplifying the creation of PHAR files, making distribution effortless
- **[Static PHP CLI](https://github.com/crazywhalecc/static-php-cli)**: For enabling the building of standalone binaries, ensuring seamless deployment
- **[ChatGPT](https://openai.com/chatgpt)**: For assisting with writing bash scripts and offering guidance during development
