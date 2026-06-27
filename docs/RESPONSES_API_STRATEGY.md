# Responses API Strategy

OpenAI Provider must use the Responses API as the default OpenAI integration. Do not use Chat Completions as the default OpenAI path.

Responses API is the foundation for OpenAI model calls because it supports normalized text generation, structured outputs, multimodal input, future tools, metadata, and future background mode. Community Core starts with tool execution disabled by default.

## Provider boundary

Target folder: `includes/Providers/`.

OpenAI-specific target folder:

```text
includes/Providers/OpenAI/
├── OpenAI_Provider.php
├── OpenAI_Responses_Client.php
└── OpenAI_Error_Mapper.php
```

Provider Layer translates normalized `AI_Request` values into Responses API payloads and maps Responses API results into normalized `AI_Response` values.

## Payload mapping

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

## Tool strategy

Community v0.5 has no tool execution by default. Tools are contract-only.

Future tools are grouped by namespace:

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
- file search and web search require controlled workflow context and explicit user-facing permission

## OpenAI-compatible providers

Gemini and custom OpenAI-compatible providers may reuse the normalized OBE request/response contract, but must declare supported capabilities. Compatibility must not weaken redaction, approval, dry-run, or logging policy.

## Agents SDK position

Do not use Agents SDK for Community Core now. Use Responses API first.

Agents SDK belongs later in Workflow Pro or a separate service when OBE owns orchestration, tool execution, approvals, state, custom storage, and runtime behavior.
