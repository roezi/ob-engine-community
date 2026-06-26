# Product Brief

## What OB Engine Community is

OB Engine Community is the public, GPL-compatible base of Optimized Builder Engine: a WordPress automation plugin for maintainers who want safer, repeatable content and site-operation workflows.

The community edition focuses on a small, understandable core:

- dry-run-first workflow execution
- draft-first content operations
- review gates before risky writes
- masked bring-your-own-key (BYOK) provider settings
- addon contracts for extending tools and workflows
- logs, snapshots, and rollback expectations

## Who it is for

- WordPress maintainers managing repetitive site operations
- developers building addon workflows around safe automation
- agencies that need reviewable content, SEO, metadata, and translation cleanup workflows
- portfolio reviewers who need to see clean product thinking and maintainable architecture

## What it is not

OB Engine Community is not a private client automation dump, a prompt collection, or an autopublishing system. It must not include production data, private prototype code, private provider logic, or Destinasindo-specific field maps.

## Core product promise

The engine should help users answer three questions before anything changes:

1. What will happen?
2. Who approved it?
3. How can it be reviewed or rolled back?

## Community edition scope

Included in the public product direction:

- workflow definitions and execution contracts
- safety contract and approval gates
- addon registration rules
- field and tool registry concepts
- logs and run history concepts
- snapshots and rollback contract
- BYOK-friendly provider configuration design

Excluded from the public community base:

- pro-only automation packs
- client-specific field maps and business rules
- production import/export data
- private OBE prototype internals
- hidden autopublishing flows
- proprietary hosted services

## Default behavior

- Write operations start in `dry_run` unless the user explicitly runs them.
- New or changed content defaults to `draft` or `needs_review`.
- Destructive actions are blocked until explicit approval is captured.
- API keys are user-provided and masked.
- Logs should show decisions, tools, approvals, and outcomes without exposing secrets.
