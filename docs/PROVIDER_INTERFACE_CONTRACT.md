# Provider Interface Contract

This is a documentation-only contract for the future Provider Layer. It is not PHP implementation.

## Future interface shape

```php
interface Provider_Interface {
    public function provider_id(): string;
    public function supports( string $capability ): bool;
    public function generate( AI_Request $request ): AI_Response;
}
```

## Capabilities

Providers must declare whether they support:

- `text_generation`
- `structured_output`
- `image_input`
- `file_input`
- `tool_calling`
- `web_search`
- `file_search`
- `background`
- `streaming`

Unsupported capabilities must fail safely with `unsupported_capability` rather than silently downgrading workflow safety.

## Provider rules

- Provider must not write WordPress posts.
- Provider must not create Library items directly.
- Provider must not log raw secrets.
- Provider must return normalized `AI_Response` only.
- Provider must declare unsupported capabilities.
- Provider must map raw provider errors into safe `AI_Error` values.
- Provider must redact diagnostics before returning `raw_response_redacted`.

## Provider settings contract

Future `Provider_Settings` should be BYOK-first and must mask keys in UI, logs, exports, errors, and diagnostics. Settings may identify enabled providers, default model choices, storage preferences, and capability availability, but must not store private prompts, private field maps, or production payloads.
