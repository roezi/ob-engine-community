# Architecture

OB Engine Community is structured as a safety-first WordPress automation engine.

## Core concepts

### Safety Layer

Handles dry-run mode, review gates, capability checks, nonce validation, and production protection.

### Workflow Engine

Coordinates repeatable tasks such as audits, imports, content preparation, translation cleanup, and metadata mapping.

### Field Registry

Maps WordPress custom fields, SEO fields, taxonomy fields, and relation fields into a clear registry.

### Content Operations

Supports draft-first content preparation, rewrite queues, SEO validation, internal linking, and metadata validation.

### Logs

Every important action should be logged for review and debugging.

## Design goals

- predictable workflows
- safe defaults
- no hidden auto-publish
- clear logs
- reusable WordPress components
- maintainer-friendly automation
