
# Behat Custom Step: Transport JSON Message Assertion with Regexp Fields

This documentation outlines the purpose and usage of the custom Behat step definition for verifying if a Symfony Messenger transport contains a message with a specific JSON mask for every message, using regular expressions to handle dynamic fields.

## Purpose

This function is designed to check if a given transport (such as an asynchronous queue) contains a message that matches a message with a specific JSON mask for every message, while allowing certain fields to be validated using regular expressions. It is particularly useful when testing messages with dynamic data, such as timestamps, unique identifiers, or payloads, where the exact value cannot be guaranteed.
## Function Overview

### Signature:
```php
/**
 * @Then all transport :transportName messages have JSON by :fields with mask :mask:
 */
public function allTransportMessagesHaveJsonByFieldsWithMask(
    string $transportName,
    string $variableFields,
    PyStringNode $expectedMessageList,
): void {
```

### Parameters:
- `transportName` (string): The name of the transport (e.g., 'webhook') where the message is expected to be found.
- `variableFields` (string): A comma-separated list of field names where values should be matched using regular expressions.
- `expectedMessage` (PyStringNode): The expected message content in JSON format, where fields marked with `~` in their values will be treated as regular expressions.

```gherkin
And all transport "webhook" messages should contain message with JSON and variable fields "time, payload" by mask:
    """
    {
        "event": "customer_agreement_status_updated",
        "time": "~^\\d{13}$",
        "payload": "~^\\{.*\\}$"
    }
    """
```
