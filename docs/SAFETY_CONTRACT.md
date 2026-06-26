# Safety Contract

This contract defines non-negotiable safety behavior for OB Engine Community.

## Required defaults

- Every write operation must support `dry_run` before real execution.
- Content writes must default to `draft` or `needs_review`.
- No workflow may secretly auto-publish content.
- Destructive actions require explicit user approval.
- API keys are BYOK and must be masked.
- Logs must redact secrets and sensitive operational data.

## Operation classes

| Class | Examples | Required behavior |
| --- | --- | --- |
| Read | scan posts, inspect metadata, list terms | capability checks and redacted logs |
| Write | create draft, update metadata, queue review item | dry-run, snapshot where practical, status defaults |
| Publish | change status to published, schedule publish | explicit approval and visible status change |
| Destructive | delete content, overwrite fields, bulk replace | dry-run, approval, snapshot/rollback contract |

## Approval rules

Approval must be explicit when an operation:

- deletes or overwrites existing data
- publishes or schedules publication
- changes many records in bulk
- changes settings that affect future automation
- cannot provide a reliable rollback path

Approval records should include actor, time, operation summary, affected resources, and mode.

## Secret handling

Secrets must never appear in:

- Git history
- screenshots
- logs
- exported run records
- exception messages
- telemetry payloads

Displayed keys should use masking such as `sk-...abcd` and should never expose the full value after save.

## Dry-run requirements

A dry-run should show:

- resources that would be touched
- proposed changes
- operation classification
- required approvals
- rollback availability
- warnings and blockers

A dry-run must not change WordPress content, options, metadata, terms, users, files, or external systems.

## Failure behavior

When safety checks fail, the engine should stop safely, log the blocker, and show an actionable message. It should not continue with partial writes unless the workflow explicitly supports resumable execution with snapshots.
