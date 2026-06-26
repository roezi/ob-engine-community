# Workflow Contract

A workflow is a reviewable sequence of classified steps that may inspect, prepare, or change WordPress resources.

## Required workflow stages

1. **Input validation**: validate request shape, actor capability, and required settings.
2. **Classification**: classify the run as read, write, publish, or destructive.
3. **Guardrails**: enforce safety limits, status defaults, secret redaction, and environment checks.
4. **Plan**: create a dry-run plan that lists affected resources and proposed changes.
5. **Approval gates**: request approval when the plan includes publishing, destructive actions, bulk writes, or rollback limitations.
6. **Tool execution**: execute bounded tools in the approved mode.
7. **Logs**: record decisions, inputs, outputs, warnings, approvals, and results with redaction.
8. **Snapshots**: capture before-state where practical before writes.
9. **Rollback contract**: state how changes can be reverted or why rollback is limited.

## Run modes

- `dry_run`: plan only; no persistent changes.
- `execute`: perform approved non-destructive writes.
- `execute_with_destructive_approval`: perform approved destructive actions.

## Content status rules

Generated or modified content must default to `draft` or `needs_review`. Publishing or scheduling requires a visible approval gate and an explicit status transition.

## Logs

Workflow logs should include:

- run id
- workflow id and version
- actor and timestamp
- run mode
- operation class
- guardrail results
- tool calls and redacted outputs
- approvals
- affected resources
- final status

## Snapshots

Snapshots should capture enough pre-change data to support review or rollback where practical. Snapshot data must avoid secrets and unnecessary personal data.

## Rollback contract

Each workflow must declare one of these rollback levels:

- `automatic`: the engine can revert captured changes.
- `guided`: the engine provides step-by-step recovery instructions.
- `manual`: an administrator must restore from backups or external records.
- `not_available`: rollback is not practical and must be approved before execution.
