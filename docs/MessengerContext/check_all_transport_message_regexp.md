# Behat Custom Step: Verify All Messages in Transport with Variable Fields

This documentation outlines the purpose and usage of the custom Behat step definition for verifying that all messages in a specific Symfony Messenger transport match a list of expected JSON structures, while allowing certain fields to vary across the messages.
## Purpose

This function is designed to validate that all messages in a specific transport (e.g., asynchronous queues) match a given set of expected JSON structures, while allowing certain fields (e.g., timestamps, unique IDs) to have variable values. This is useful in scenarios where specific fields in the messages may differ but the overall structure and format must remain consistent.## Function Overview

### Signature:
```php
/**
 * @Then all transport :transportName messages should be JSON with variable fields :variableFields:
 */
public function allTransportMessagesShouldBeJsonWithVariableFields(
    string $transportName,
    string $variableFields,
    PyStringNode $expectedMessageList
): void
```

### Parameters:
- `transportName` (string): The name of the transport (e.g., 'webhook') where the message is expected to be found.
- `variableFields` (string): A comma-separated list of fields where values may vary and should be compared using regular expressions.
- `expectedMessage` (PyStringNode): The expected message content in JSON format, where fields marked with `~` in their values will be treated as regular expressions.

```gherkin
Then all transport ":transportName" messages should be JSON with variable fields ":variableFields":
"""
    [
        {
            "event": "status_updated",
            "time": "~^\\d{13}$",
            "payload": "dqwdwdwdw"
        },
        {
            "event": "order_created",
            "time": "~^\\d{13}$",
            "payload": "otherPayload"
        }
    ]
"""
```
