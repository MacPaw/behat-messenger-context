# Behat Messenger Context Bundle

| Version | Build Status | Code Coverage |
|:---------:|:-------------:|:-----:|
| `master`| [![CI][master Build Status Image]][master Build Status] | [![Coverage Status][master Code Coverage Image]][master Code Coverage] |
| `develop`| [![CI][develop Build Status Image]][develop Build Status] | [![Coverage Status][develop Code Coverage Image]][develop Code Coverage] |

This repository provides custom Behat step definitions for working with Symfony Messenger transports. It includes functionality for checking messages in transports, validating them against expected JSON structures, and working with variable fields.

## Installation

To install the MessengerContext and integrate it with your Behat setup, follow the instructions provided in the [Installation Guide](docs/install.md).

## Available Features

### Check a Specific Message in a Transport
You can verify if a specific message exists in a given transport.
* Documentation: [Check Transport Message](docs/MessengerContext/check_transport_message.md)

### Check All Messages in a Transport
Verify if all messages in a given transport match the expected JSON structure.
* Documentation: [Check All Transport Messages](docs/MessengerContext/check_all_transport_message.md)

### Check Messages with Regular Expressions
You can use regular expressions to validate messages that contain dynamic or variable data.
* Documentation for specific message: [Check Transport Message with Regexp](docs/MessengerContext/check_transport_message_regexp.md)
* Documentation for all messages: [Check All Transport Messages with Regexp](docs/MessengerContext/check_all_transport_message_regexp.md)

### Verify Message Count in a Transport
Ensure that a specific number of messages exist in a given transport.

### Auto clean queue messages before scenario
Check details in [documentation](docs/MessengerContext/clear_transport_with_zentruck.md)

* Documentation: [Count Messages in Transport](docs/MessengerContext/count_message_transport.md)

[master Build Status]: https://github.com/macpaw/behat-messenger-context/actions?query=workflow%3ACI+branch%3Amaster
[master Build Status Image]: https://github.com/macpaw/behat-messenger-context/workflows/CI/badge.svg?branch=master
[develop Build Status]: https://github.com/macpaw/behat-messenger-context/actions?query=workflow%3ACI+branch%3Adevelop
[develop Build Status Image]: https://github.com/macpaw/behat-messenger-context/workflows/CI/badge.svg?branch=develop
[master Code Coverage]: https://codecov.io/gh/macpaw/behat-messenger-context/branch/master
[master Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/behat-messenger-context/master?logo=codecov
[develop Code Coverage]: https://codecov.io/gh/macpaw/behat-messenger-context/branch/develop
[develop Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/behat-messenger-context/develop?logo=codecov
