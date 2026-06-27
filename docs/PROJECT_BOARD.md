# Project Board

## Recommended board

**Board name:** OBE v1 Build Board

## Columns

- Backlog
- Ready
- In Progress
- Review
- Merged
- Blocked

## Fields

- Target Version
- Mission Type
- Area
- Safety Risk
- Status
- PR
- Notes

## Mission types

- `feature`
- `chore`
- `docs`
- `hardening`
- `bug`
- `research`

## Areas

- AI Engine
- Providers
- Library
- Activity
- Approval
- Safety
- Auto Post
- Addons
- Admin UI
- Docs
- Release

## Safety risk

- `low`
- `medium`
- `high`

## Issue naming

Use a concise mission title with the target version when known:

```text
[v0.12.0] Dry-run / Write Draft Safety Runtime
[docs] Project Command Center
[hardening] Capability and nonce audit pass
```

## PR naming

Use conventional prefixes and keep the title tied to one mission:

```text
feat: add dry-run write draft safety runtime
chore: add project command center
hardening: audit admin write gates
```

## One mission = one PR

Each issue and PR should cover one bounded mission. Include goal, scope, out of scope, safety requirements, acceptance criteria, target version, and tests/checks.

## When to block a mission

Block a mission when:

- Safety requirements are unclear.
- The mission would bypass Library, approval, dry-run, or redaction contracts.
- Required architecture or versioning docs disagree.
- The scope depends on private endpoints, credentials, private prompts, private field maps, production data, or client-specific business rules.
- The task requires broad runtime infrastructure that has not been explicitly approved.

## When to split a mission

Split a mission when:

- It touches unrelated areas or multiple runtime layers without a single acceptance path.
- It combines docs, runtime implementation, provider execution, and release packaging in one PR.
- It cannot be reviewed with a small, focused safety checklist.
- It risks adding Auto Post, Write Draft, REST/MCP/WP-CLI, cron, workers, or provider execution beyond the stated target.
