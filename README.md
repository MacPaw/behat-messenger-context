Behat Messenger Context
=================================

| Version | Build Status | Code Coverage |
|:---------:|:-------------:|:-----:|
| `master`| [![CI][master Build Status Image]][master Build Status] | [![Coverage Status][master Code Coverage Image]][master Code Coverage] |
| `develop`| [![CI][develop Build Status Image]][develop Build Status] | [![Coverage Status][develop Code Coverage Image]][develop Code Coverage] |

Installation
============

Step 1: Install Context
----------------------------------
Open a command console, enter your project directory and execute:

```console
$ composer require --dev macpaw/behat-messenger-context
```

Step 2: Adding to Load Container Messenger Context
----------------------------------
In the `config/services_test.yaml` file of your project:

```
    BehatMessengerContext\:
        resource: '../vendor/macpaw/symfony-behat-context/src/*'
```

Step 3: Configuration Behat
=============
Go to `behat.yml`

```yaml
...
  contexts:
    - SymfonyBehatContext\Context\MessengerContext
...
```

[master Build Status]: https://github.com/macpaw/BehatMessengerContext/actions?query=workflow%3ACI+branch%3Amaster
[master Build Status Image]: https://github.com/macpaw/BehatMessengerContext/workflows/CI/badge.svg?branch=master
[develop Build Status]: https://github.com/macpaw/BehatMessengerContext/actions?query=workflow%3ACI+branch%3Adevelop
[develop Build Status Image]: https://github.com/macpaw/BehatMessengerContext/workflows/CI/badge.svg?branch=develop
[master Code Coverage]: https://codecov.io/gh/macpaw/BehatMessengerContext/branch/master
[master Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/BehatMessengerContext/master?logo=codecov
[develop Code Coverage]: https://codecov.io/gh/macpaw/BehatMessengerContext/branch/develop
[develop Code Coverage Image]: https://img.shields.io/codecov/c/github/macpaw/BehatMessengerContext/develop?logo=codecov

