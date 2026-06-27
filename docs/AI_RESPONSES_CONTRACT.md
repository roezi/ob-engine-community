# AI / Responses API Contract

This document is the public v0.5 contract for OBE AI execution. It is documentation only: no PHP runtime code, provider HTTP calls, live OpenAI execution, Gemini execution, workers, cron, REST/MCP routes, WP-CLI commands, Auto Post implementation, Library implementation, or WordPress writes are introduced by this contract.

## Core boundaries

- **AI Engine is not Provider Layer.** AI Engine owns task type, model profile, reasoning effort, verbosity, prompt contract, structured output schema, normalization, usage, redaction, and errors.
- **Provider Layer is not workflow runtime.** Providers translate normalized AI requests into provider transport payloads and normalize provider results back into AI responses.
- **Responses API is the default OpenAI integration.** OpenAI Provider should use Responses API as the default OpenAI path.
- **Chat Completions is not the default OpenAI path.** It may be considered only as a compatibility fallback in a future explicit design.
- **Gemini and custom providers must map to the same contracts.** Every provider returns normalized `AI_Response` values from normalized `AI_Request` inputs.
- **Contract comes before implementation.** Future code must implement these contracts without guessing or bypassing safety boundaries.

## Related contract documents

This overview is supported by focused contracts for normalized request/response shapes, provider behavior, model profiles, task types, structured outputs, usage, errors, and redaction:

- [AI Request / Response Contract](AI_REQUEST_RESPONSE_CONTRACT.md)
- [Provider Interface Contract](PROVIDER_INTERFACE_CONTRACT.md)
- [AI Task Types and Model Profiles](AI_TASK_TYPES_AND_MODEL_PROFILES.md)
- [Structured Output Schemas](STRUCTURED_OUTPUT_SCHEMAS.md)
- [AI Usage, Error, and Redaction Contract](AI_USAGE_ERROR_AND_REDACTION_CONTRACT.md)

## Target folder architecture

Future implementation targets are documented here only.

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

includes/Providers/
├── Provider_Interface.php
├── Provider_Settings.php
└── Provider_Result.php

includes/Providers/OpenAI/
├── OpenAI_Provider.php
├── OpenAI_Responses_Client.php
└── OpenAI_Error_Mapper.php

includes/Providers/Gemini/
├── Gemini_Provider.php
└── Gemini_Error_Mapper.php

includes/Providers/Custom/
└── Custom_OpenAI_Compatible_Provider.php
```

## Responses API mapping

| Normalized field | Responses API payload field |
| --- | --- |
| `AI_Request.model` | `model` |
| `AI_Request.input` | `input` |
| `AI_Request.instructions` | `instructions` |
| `AI_Request.reasoning_effort` | `reasoning.effort` |
| `AI_Request.verbosity` | `text.verbosity` |
| `AI_Request.output_schema` | `text.format` |
| `AI_Request.tools` | `tools` |
| `AI_Request.tool_choice` | `tool_choice` |
| `AI_Request.store` | `store` |
| `AI_Request.metadata` | `metadata` |
| `AI_Request.safety_identifier` | `safety_identifier` |
| `AI_Request.prompt_cache_key` | `prompt_cache_key` |
| `AI_Request.background` | `background` |

Community defaults:

- `tools`: disabled
- `tool_choice`: `none` or omitted
- `store`: `false` or configurable
- `background`: `false`
- `reasoning.effort`: task-driven
- `text.verbosity`: task-driven

## Library-first output rule

All generated AI output should land in Library before any WordPress write. The AI Engine may return output to a caller, but future implementation should create or update Library items through the Library Layer. Provider Layer must not create Library items directly and must never write WordPress posts.

## Tool strategy

Community v0.5 has no tool execution by default. Tools are contract-only.

Future namespaces:

- `source.*`
- `library.*`
- `content.*`
- `seo.*`
- `translation.*`
- `workflow.*`
- `wordpress.*`

Rules:

- read-only tools may run during `dry_run`
- write tools require dry-run output and explicit approval
- destructive tools are disabled in Community
- web search and file search require controlled context and user-facing permission

## Future testing expectations

Future implementation must include:

- `php -l` on changed PHP files
- no external request tests without mocks
- mocked OpenAI Responses API client
- redaction tests
- structured output validation tests
- provider error mapping tests
- no raw key display tests
- no WordPress write from provider tests
