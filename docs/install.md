
# Installation Guide

## Step 1: Install the Bundle
To begin, navigate to your project directory and use Composer to download the bundle.

### For applications using Symfony Flex:
Simply run the following command:

```bash
composer require --dev macpaw/behat-messenger-context
```

### For applications without Symfony Flex:
If your project doesn't use Symfony Flex, run the same command:

```bash
composer require --dev macpaw/behat-messenger-context
```

Make sure that Composer is installed globally on your machine. If not, refer to the [Composer installation guide](https://getcomposer.org/doc/00-intro.md) for assistance.

Next, you'll need to manually register the bundle in the `AppKernel.php` file. Add the following line to the `registerBundles` method:

```php
// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // Other bundles
            BehatMessengerContext\BehatMessengerContextBundle::class => ['test' => true],
        ];
    }
}
```

## Step 2: Configure Symfony Messenger for Testing
To ensure your messenger configuration is properly set up for testing, you need to copy the configuration from `config/packages/dev/messenger.yaml` to `config/packages/test/messenger.yaml`. Then, update the configuration to use in-memory transport for testing.

```yaml
framework:
    messenger:
        transports:
            async: 'in-memory://'
            async_priority_high: 'in-memory://'
```

OR in your global config file `config/packages/messenger.yaml`
```yaml
framework:
  messenger:
    transports:
      async: 'in-memory://'
      async_priority_high: 'in-memory://'
```

This ensures that your tests use an in-memory transport rather than actual services.

## Step 3: Behat Configuration
In your `behat.yml` file, make sure the Messenger context is registered:

```yaml
default:
    suites:
        default:
            contexts:
                - BehatMessengerContext\Context\MessengerContext
```

This completes the setup for integrating Behat with Symfony Messenger in your project. Now you're ready to run tests with the in-memory transport!
