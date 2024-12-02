[![License: Unlicense](https://img.shields.io/badge/license-Unlicense-blue.svg)](http://unlicense.org/)
[![Workflow: tests](https://github.com/PugKong/clibitica/actions/workflows/tests.yml/badge.svg)](https://github.com/PugKong/clibitica/actions/workflows/tests.yml)
[![Workflow: release](https://github.com/PugKong/clibitica/actions/workflows/release.yml/badge.svg)](https://github.com/PugKong/clibitica/actions/workflows/release.yml)
[![Coverage Status](https://coveralls.io/repos/github/PugKong/clibitica/badge.svg?branch=main)](https://coveralls.io/github/PugKong/clibitica?branch=main)

# clibitica

**clibitica** is a command-line interface (CLI) tool for interacting with [Habitica](https://habitica.com),
a gamified task management and productivity platform. With clibitica, you can manage tasks, tags, and other features
of your Habitica account directly from the terminal.

## Features

- **Task Management** (create, delete and list)
- **Tag Management** (create, delete and list)
- Shell completion support for popular shells

## Installation

Download the prebuilt binary from [Releases](https://github.com/PugKong/clibitica/releases) page.

## Usage

Set the `CLIBITICA_API_KEY` and `CLIBITICA_API_USER` environment variables.

Run `clibitica` to see the available commands:

```bash
clibitica 0.0.1

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display help for the given command. When no command is given display help for the list command
      --silent          Do not output any message
  -q, --quiet           Only errors are displayed. All other output is suppressed
  -V, --version         Display this application version
      --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  completion   Dump the shell completion script
  help         Display help for a command
  list         List commands
 tag
  tag:create   Create a new tag
  tag:delete   Delete a tag
  tag:list     Get tags
 task
  task:create  Create a new task
  task:delete  Delete a task
  task:list    List tasks
```

## Contributing

Contributions are welcome! Feel free to submit a pull request or open an issue on the GitHub repository.

## License

This project is licensed under the Unlicense License. See the [LICENSE](LICENSE) file for details.

## Acknowledgments

- Inspired by the productivity features of [Habitica](https://habitica.com).
