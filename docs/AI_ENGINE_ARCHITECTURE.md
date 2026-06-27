# AI Engine Architecture

The AI Engine is the normalized OBE layer for AI tasks. It is not the Provider Layer.

Target folder: `includes/AI/`.

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

Provider implementations own provider-specific HTTP transport and response mapping.

## Internal request contract

An `AI_Request` should identify:

- `task_type`
- `input`
- `instructions`
- `model_profile`
- `reasoning_effort`
- `verbosity`
- `output_schema`
- `tools`
- `metadata`
- `safety_identifier`
- `prompt_cache_key`
- `store`
- `background`

## Model profiles

OBE uses internal model profiles so user-facing workflows stay cost-conscious.

| Profile | Reasoning | Verbosity | Intended use |
| --- | --- | --- | --- |
| `fast` | low | low | classification, extraction, compact rewrite |
| `balanced` | medium | medium | auto post plans, SEO suggestions, content drafts |
| `deep` | high | medium/high | workflow planning, debugging, complex review |
| `background` | medium/high | medium | future long-running batch or worker tasks |

Do not default user-facing BYOK flows to `xhigh`.

## Runtime defaults by task

| Task | Reasoning | Verbosity |
| --- | --- | --- |
| classification | low | low |
| validation | low/medium | low |
| auto post plan | medium | medium |
| content draft | medium | medium/high |
| SEO review | medium | medium |
| workflow planning | high | medium |
| debugging | high | medium |

## Normalized response

Every provider response must become an `AI_Response` with:

- `status`
- `output_text`
- `output_json`
- `refusal`
- `usage`
- `model`
- `provider`
- `request_id`
- `error`
- `raw_response_redacted`

Raw provider responses must not be passed directly to the UI.

## Prompt and schema policy

Prompt contracts must be public-safe and generic. Do not commit private prompts, private field maps, private workflows, production data, or client-specific business rules.

Structured outputs should be preferred for Library item creation, plans, reviews, validation results, and workflow steps.

## Safety hooks

Before an AI run, the engine should classify the operation, apply redaction, confirm provider settings, and attach metadata. After an AI run, it should normalize output, record usage, redact raw data, create or update a Library item through the Library layer, and emit an Activity event.
