# Clean with in-memory transport
Switch all your queue to in-memory transport

example:

```yaml
    # config/packages/messenger.yaml

    # ...

    when@test:
        framework:
            messenger:
                transports:
                    async: in-memory://
                    some-another: in-memory://
                    # ...
```

All this transport will be cleared automatically before start every scenario.

# Clear queues messages before scenario with zentruck

We also support auto clear message queue with [zenstruck/messenger-test](https://github.com/zenstruck/messenger-test)

## Installation

1. Install the library:

    ```bash
    composer require --dev zenstruck/messenger-test
    ```
2. If not added automatically by Symfony Flex, add the bundle in `config/bundles.php`:

    ```php
    Zenstruck\Messenger\Test\ZenstruckMessengerTestBundle::class => ['test' => true],
    ```

3. Update `config/packages/messenger.yaml` and override your transport(s)
   in your `test` environment with `test://`:

    ```yaml
    # config/packages/messenger.yaml

    # ...

    when@test:
        framework:
            messenger:
                transports:
                    async: test://
    ```

More details you can see in [origin package repository](https://github.com/zenstruck/messenger-test)

> **Note**:Zentruck will be used automatically after installation.


