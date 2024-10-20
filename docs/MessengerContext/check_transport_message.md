
# Behat Custom Step: Transport JSON Message Assertion

This documentation outlines the purpose and usage of the custom Behat step definition for verifying if a Symfony Messenger transport contains a message with a specific JSON structure.

## Purpose

This function is designed to check if a given transport (such as an asynchronous queue) contains a message that matches a particular JSON structure. It is useful in scenarios where you need to validate that a message was correctly dispatched with the expected content during Behat tests.

## Function Overview

### Signature:
```gherkin
Then transport ":transportName" should contain message with JSON:
    """
    {
        "event": "status_updated",
        "time": "123123123",
        "payload": "dqwdwdwdw"
    }
    """
```

### Parameters:
- `transportName` (string): The name of the transport (e.g., 'async') where the message is expected to be found.
- `expectedMessage` (PyStringNode): The expected message content in JSON format that should be present in the transport.
