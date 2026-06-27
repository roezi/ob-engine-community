# Responses API Strategy

OpenAI Provider must use the Responses API as the default OpenAI integration. Do not use Chat Completions as the default.

Responses API is the foundation for OpenAI model calls because it supports text/image input, JSON output, custom code/function tools, and built-in tools such as web search and file search. Community Core still starts with tool execution disabled by default.

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

## OBE-modeled Responses API settings

OBE should model these settings:

- `model`
- `input`
- `instructions`
- `reasoning.effort`
- `text.verbosity`
- `text.format`
- `tools`
- `tool_choice`
- `store`
- `metadata`
- `safety_identifier`
- `prompt_cache_key`
- `background`

Community defaults:

- `store`: `false` or configurable
- `background`: `false`
- `tools`: disabled by default
- `tool_choice`: none unless a task explicitly enables tools
- `reasoning.effort`: task/profile-driven, usually low or medium
- `text.verbosity`: task/profile-driven, usually low or medium

## Tool strategy

Community starts with no tool execution by default.

Future tools are grouped by namespace:

- `source.*`
- `library.*`
- `content.*`
- `seo.*`
- `translation.*`
- `workflow.*`
- `wordpress.*`

Rules:

- read-only tools may run during dry-run
- write tools require dry-run output and explicit approval
- destructive tools are disabled in Community
- file search and web search require controlled workflow context and explicit user-facing permission

## OpenAI-compatible providers

Custom OpenAI-compatible providers may reuse the normalized OBE request/response contract, but must declare supported capabilities. Compatibility must not weaken redaction, approval, dry-run, or logging policy.

## Agents SDK position

Do not use Agents SDK for Community Core now. Use Responses API first.

Agents SDK belongs later in Workflow Pro or a separate service when OBE owns orchestration, tool execution, approvals, state, custom storage, and runtime behavior.
