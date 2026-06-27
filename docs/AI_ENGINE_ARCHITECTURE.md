# AI Engine Architecture

The AI Engine is the normalized OBE layer for AI tasks. It is not the Provider Layer, and the Provider Layer is not the workflow runtime.

Target folder: `includes/AI/`.

## Contract documents

- [AI / Responses API Contract](AI_RESPONSES_CONTRACT.md)
- [AI Request / Response Contract](AI_REQUEST_RESPONSE_CONTRACT.md)
- [Provider Interface Contract](PROVIDER_INTERFACE_CONTRACT.md)
- [AI Task Types and Model Profiles](AI_TASK_TYPES_AND_MODEL_PROFILES.md)
- [Structured Output Schemas](STRUCTURED_OUTPUT_SCHEMAS.md)
- [AI Usage, Error, and Redaction Contract](AI_USAGE_ERROR_AND_REDACTION_CONTRACT.md)

## Responsibilities

AI Engine owns:

- task type
- model profile
- reasoning effort
- verbosity
- prompt contract
- structured output schema
- response normalization
- usage tracking
- redaction
- error handling

Provider implementations own provider-specific HTTP transport and response mapping for OpenAI, Gemini, and custom OpenAI-compatible providers.

## Target folder architecture

```text
includes/AI/
├── AI_Engine.php
├── AI_Request.php
├── AI_Response.php
├── AI_Task_Type.php
├── Model_Profile.php
├── Prompt_Template.php
├── Prompt_Builder.php
├── Structured_Output.php
├── Usage_Record.php
└── AI_Error.php
```

## Internal request contract

`AI_Request` is defined in [AI Request / Response Contract](AI_REQUEST_RESPONSE_CONTRACT.md). Required v0.5 fields are:

- `task_type`
- `input`
- `instructions`
- `model_profile`
- `reasoning_effort`
- `verbosity`
- `metadata`

Future-only fields include `tools`, `tool_choice`, `prompt_cache_key`, `background`, and `safety_identifier`.

## Model profiles

OBE uses internal model profiles so user-facing workflows stay cost-conscious.

| Profile | Reasoning | Verbosity | Intended use |
| --- | --- | --- | --- |
| `fast` | low | low | classification, extraction, routing, simple rewrite |
| `balanced` | medium | medium | auto post plans, SEO suggestions, content drafts, translation plans |
| `deep` | high | medium/high | workflow planning, debugging, complex review, synthesis |
| `background` | medium/high | medium | future long-running batch or worker tasks |

Do not default user-facing BYOK flows to `xhigh`.

## Normalized response

Every provider response must become an `AI_Response` with safe normalized fields including `status`, `output_text`, `output_json`, `refusal`, `usage`, `model`, `provider`, `request_id`, `error`, `raw_response_redacted`, `library_item_id`, and `activity_id`.

Raw provider responses must not be passed directly to the UI. Raw provider errors must be mapped to safe user-facing errors, and raw API keys must never appear in responses, logs, exports, or Activity events.

## Prompt and schema policy

Prompt contracts must be public-safe and generic. Do not commit private prompts, private field maps, private workflows, production data, or client-specific business rules.

Structured outputs should be preferred for Library item creation, plans, reviews, validation results, and workflow steps.

## Library and Activity hooks

All generated AI output should land in Library before any WordPress write. The AI Engine may return output to a caller, but future implementation should create/update Library items through the Library Layer. Provider Layer must not create Library items directly.

Activity events should log summaries and redacted metadata for request creation, response completion/failure, structured output invalidation, provider key use/missing, Library item creation from AI, and AI output redaction.

## Safety hooks

Before an AI run, the engine should classify the operation, apply redaction, confirm provider settings, and attach metadata. After an AI run, it should normalize output, record usage, redact raw data, create or update a Library item through the Library Layer, and emit an Activity event.

Minimum WordPress safety and secret redaction are required from the start. Final hardening happens later, but no stage may display raw keys, bypass dry-run, or allow hidden auto-publish.
