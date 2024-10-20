# Behat Custom Step: Verify All Messages in a Transport

This documentation outlines the purpose and usage of the custom Behat step definition for verifying that all messages in a specific Symfony Messenger transport match a list of expected JSON structures.

## Purpose

This function is designed to validate that all messages in a specific transport (e.g., asynchronous queues) match a given set of expected JSON structures. It is useful in scenarios where you need to ensure that every message in the transport adheres to a specific structure or content format.

## Function Overview

### Signature:
```gherkin
Then all transport ":transportName" messages should be JSON:
"""
    [
        {
            "event": "status_updated",
            "time": "123123123",
            "payload": "dqwdwdwdw"
        },
        {
            "event": "order_created",
            "time": "456456456",
            "payload": "otherPayload"
        }
    ]
    """

```

### Parameters:
- `transportName` (string): The name of the transport (e.g., 'async') where the message is expected to be found.
- `expectedMessage` PyStringNode): A JSON array representing the expected content of all messages that should be present in the transport.
