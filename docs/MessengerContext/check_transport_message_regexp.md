
# Behat Custom Step: Transport JSON Message Assertion with Regexp Fields

This documentation outlines the purpose and usage of the custom Behat step definition for verifying if a Symfony Messenger transport contains a message with a specific JSON structure, using regular expressions to handle dynamic fields.
## Purpose

This function is designed to check if a given transport (such as an asynchronous queue) contains a message that matches a particular JSON structure, while allowing certain fields to be validated using regular expressions. It is particularly useful when testing messages with dynamic data, such as timestamps, unique identifiers, or payloads, where the exact value cannot be guaranteed.
## Function Overview

### Signature:
```php
/**
 * @Then transport :transportName should contain message with JSON and variable fields :variableFields:
 */
public function transportShouldContainMessageWithJsonAndVariableFields(
    string $transportName,
    string $variableFields,
    PyStringNode $expectedMessage
): void
```

### Parameters:
- `transportName` (string): The name of the transport (e.g., 'webhook') where the message is expected to be found.
- `variableFields` (string): A comma-separated list of field names where values should be matched using regular expressions.
- `expectedMessage` (PyStringNode): The expected message content in JSON format, where fields marked with `~` in their values will be treated as regular expressions.

```gherkin
And transport "webhook" should contain message with JSON and variable fields "time, payload":
    """
    {
        "event": "customer_agreement_status_updated",
        "time": "~^\\d{13}$",
        "payload": "~^\\{.*\\}$"
    }
    """
```
