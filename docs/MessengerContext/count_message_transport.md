# Behat Custom Step: Verify Message Count in a Transport

This documentation outlines the purpose and usage of the custom Behat step definition for verifying that a specific number of messages exist in a given Symfony Messenger transport.

## Purpose

This function is designed to check that the number of messages in a specific transport (e.g., asynchronous queues) matches the expected count. It is useful in scenarios where you need to ensure that the correct number of messages have been dispatched into the transport during a Behat test.

## Function Overview

### Signature:
```php
/**
 * @Then there is :expectationMessageCount messages in transport :transportName
 */
public function thereIsCountMessagesInTransport(int $expectedMessageCount, string $transportName): void
```

### Parameters:
- `expectedMessageCount` (int): The expected number of messages that should be present in the transport.
- `transportName` (string): The name of the transport (e.g., 'async') where the messages are expected to be found.

```gherkin
Then there is 1 messages in transport "command"
```
