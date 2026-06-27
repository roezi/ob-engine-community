# AI Usage, Error, and Redaction Contract

This document defines usage tracking, error mapping, redaction, and Activity integration for future AI implementation. It is documentation only.

## Usage tracking fields

A normalized usage record includes:

- `input_tokens`
- `output_tokens`
- `reasoning_tokens`
- `total_tokens`
- `model`
- `provider`
- `task_type`
- `request_id`
- `library_item_id`
- `estimated_cost_placeholder`
- `created_at`

Rules:

- Usage tracking must not expose API keys.
- Cost can be placeholder until pricing implementation.
- Usage should be attached to Activity and/or Library metadata later.

## AI_Error fields

- `code`
- `provider_code`
- `message_public`
- `message_internal_redacted`
- `retryable`
- `severity`
- `request_id`
- `provider`
- `task_type`

## Error categories

- `missing_provider_key`
- `unsupported_provider`
- `unsupported_capability`
- `invalid_request`
- `provider_auth_failed`
- `provider_rate_limited`
- `provider_timeout`
- `provider_server_error`
- `structured_output_invalid`
- `redaction_failed`
- `unknown_error`

Rules:

- UI gets `message_public` only.
- Logs get redacted internal details only.
- Raw provider error body must not be shown directly.

## Redaction policy

Must redact:

- API keys
- bearer tokens
- cookies
- passwords
- private endpoints
- private prompts
- private field maps
- production data
- large raw source payloads
- OAuth secrets
- partner API credentials

Placeholders:

- `[REDACTED_SECRET]`
- `[REDACTED_TOKEN]`
- `[REDACTED_ENDPOINT]`
- `[REDACTED_PRIVATE_PROMPT]`
- `[REDACTED_PRIVATE_FIELD_MAP]`
- `[REDACTED_PRIVATE_PAYLOAD]`

Redaction must run before UI display, Activity logging, exports, diagnostics, and provider error normalization. If redaction fails, return `redaction_failed` and do not expose the unsafe payload.

## Activity integration contract

Events:

- `ai_request_created`
- `ai_response_completed`
- `ai_response_failed`
- `ai_structured_output_invalid`
- `provider_key_missing`
- `provider_key_used`
- `library_item_created_from_ai`
- `ai_output_redacted`

Rules:

- Do not log raw prompts if they contain private/source data.
- Log summaries and redacted metadata.
- Provider key use may be logged as an event, but the key value must never be logged.
