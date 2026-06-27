# Architecture

OB Engine Community is a safety-first WordPress automation engine. The public architecture favors a small core with explicit contracts over many built-in features.

## Architecture goals

- safe defaults for every write operation
- clear separation between community core, addons, and pro/private features
- predictable workflow execution
- BYOK-friendly provider configuration
- reviewable logs and snapshots
- GPL-compatible public code and documentation

## Layers

### 1. Safety layer

The safety layer is the first boundary around every operation. It owns:

- `dry_run` support for all writes
- capability and nonce checks
- approval requirements for destructive actions
- default content statuses of `draft` or `needs_review`
- prevention of hidden auto-publish behavior
- secret masking in UI, logs, exports, and errors

See [Safety Contract](SAFETY_CONTRACT.md).

### 2. Workflow engine

The workflow engine coordinates repeatable operations. A workflow run should include:

- input classification
- guardrail checks
- planned tool execution
- approval gates
- execution logs
- snapshots for changed resources
- rollback contract metadata

See [Workflow Contract](WORKFLOW_CONTRACT.md).

### 3. Tool registry

Tools are bounded capabilities exposed to workflows, such as reading content, preparing draft changes, validating metadata, or generating a dry-run report.

Each tool must declare:

- name and version
- read/write/destructive classification
- required capabilities
- whether it supports dry-run
- log and snapshot behavior
- rollback expectations

### 4. Field registry

The field registry describes public, generic WordPress field mappings for posts, terms, metadata, SEO fields, and addon-owned fields. It must not include production-specific or private-project-specific field maps.

### 5. Addon layer

Addons extend tools, workflows, providers, field definitions, and UI panels through documented contracts. Addons must not depend on private internals.

See [Addon Contract](ADDON_CONTRACT.md).

### 6. Provider layer

Provider integrations are BYOK-friendly. Users bring their own API keys. Keys must be masked and must not be written to logs, exported run data, or error messages.

### 7. Logs, snapshots, and rollback

Workflow runs should produce reviewable records:

- run id, actor, timestamp, and mode
- classification and guardrail decisions
- tool inputs and outputs with secrets redacted
- approvals and approver identity
- resource snapshots before writes where practical
- rollback instructions or explicit rollback limitations

## Community/pro boundary

The community core defines contracts and minimal safe primitives. Pro/private packages may add premium workflows or provider integrations, but they must live outside this public repository unless explicitly released under compatible terms.

See [Public/Private Boundary](PUBLIC_PRIVATE_BOUNDARY.md).
