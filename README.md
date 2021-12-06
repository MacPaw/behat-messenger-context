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

Step 2: Update Container config to load Messenger Context
----------------------------------
In the `config/services_test.yaml` file of your project:

```
    BehatMessengerContext\:
        resource: '../vendor/macpaw/behat-messenger-context/src/*'
        arguments:
            - '@test.service_container'
```

Step 3: Configure Messenger 
=============
Copying `config/packages/dev/messenger.yaml` and pasting that into `config/packages/test/`. This gives us messenger configuration that will only be used in the test environment. Uncomment the code, and replace sync with in-memory. Do that for both of the transports.

```yaml
framework:
    messenger:
        transports:
            async: 'in-memory://'
            async_priority_high: 'in-memory://'
            ...
...
```


Step 4: Configure Behat
=============
Go to `behat.yml`

```yaml
...
  contexts:
    - BehatMessengerContext\Context\MessengerContext
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
