# Addon Contract

Addons extend OB Engine Community through documented contracts. They must not rely on private internals or client-specific assumptions.

## Addon principles

- Use public hooks, registries, and declared interfaces only.
- Declare capabilities, operation class, dry-run support, and rollback behavior.
- Respect the safety contract for all reads, writes, publishing, and destructive actions.
- Keep secrets masked and BYOK-friendly.
- Avoid bundled production data or private field maps.

## Addon metadata

An addon should declare:

- addon id and human-readable name
- version and compatible OB Engine version range
- provided tools, workflows, providers, field definitions, or UI panels
- required WordPress capabilities
- whether network/multisite behavior is supported
- license compatibility

## Tool registration contract

Each addon tool should declare:

- stable tool id
- description
- operation class: `read`, `write`, `publish`, or `destructive`
- supported modes: `dry_run`, `execute`
- required capabilities
- input schema and validation rules
- output schema
- log redaction rules
- snapshot and rollback behavior

## Workflow registration contract

Each addon workflow should declare:

- workflow id and version
- input schema
- classification rules
- guardrails
- tools it may call
- approval gates
- default content status
- log and snapshot requirements
- rollback limitations

## Compatibility expectations

The community core should prefer stable contracts over direct class access. Breaking changes should be documented in release notes and reflected in the roadmap or decisions log.
