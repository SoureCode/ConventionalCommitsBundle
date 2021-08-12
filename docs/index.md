
# The ConventionalCommits Bundle

- [Installation](#installation)
  - [Download](#download)
  - [Enable the Bundle](#enable-the-bundle)
  - [Configure the Bundle](#configure-the-bundle)
    - [Type](#types)
- [Usage](#usage)

> Conventional commits integration into symfony.

## Installation

### Download

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```shell script
composer require sourecode/conventional-commits-bundle
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

### Enable the Bundle

ConventionalCommitsBundle should be automatically enabled, thanks to [Flex](https://flex.symfony.com).

### Configure the Bundle

The bundle exposes a configuration that looks like this by default:

```yaml
soure_code_conventional_commits:
    type:
        min: 5
        max: 25
        extra: false
        values:
            - add
            - build
            - bump
            - chore
            - ci
            - cut
            - docs
            - enhance
            - feat
            - fix
            - make
            - optimize
            - perf
            - refactor
            - revert
            - style
            - test
    scope:
        min: 5
        max: 25
        extra: true
        required: false
        values: []
    description:
        min: 5
        max: 50
```

#### Types

**Hint**: *This is just for inspiration, just use it as you need.*

- **add**: changes to add new capability or functions
- **build**: changes to build system or external dependencies
- **bump**: increasing the versions or dependency versions
- **chore**: changes for housekeeping (avoiding this will force more meaningful message)
- **ci**: changes to CI configuration files and scripts
- **cut**: removing the capability or functions
- **docs**: changes to the documentation
- **feat**: addition of some new features
- **fix**: a bug fix
- **make**: change to the build process, or tooling, or infra
- **optimize**/**perf**/**enhance**: a code change that improves performance
- **refactor**: a code change that neither fixes a bug nor adds a feature
- **revert**: reverting an accidental commit
- **style**: changes to the code that do not affect the meaning
- **test**: adding missing tests or correcting existing tests

## Usage

This bundle actually serves a command to validate commits:

```shell script
$ php bin/console sourecode:conventional:commits:validate --help
Description:
  Validate commits

Usage:
  sourecode:conventional:commits:validate <commits>
  sourecode:conventional:commits:validate 8648bac1
  sourecode:conventional:commits:validate 8648bac1...8d0536a1,2662bdb9
  sourecode:conventional:commits:validate 3afed635...8648bac1,84c516b4...2662bdb9

Arguments:
  commits               The commit hash, hashes, range or ranges.

Options:
  -h, --help            Display help for the given command. When no command is given display help for the list command
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -e, --env=ENV         The Environment name. [default: "dev"]
      --no-debug        Switch off debug mode.
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

