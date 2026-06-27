# OB Engine Community

OB Engine Community is the public base of OBE — Optimization-Based Engine — an open-source WordPress optimization and automation engine for safe content operations, SEO workflows, translation cleanup, metadata mapping, and maintainer-friendly website automation.

The project is built for WordPress maintainers and developers who need to reduce repetitive operational work while keeping safety controls such as dry-runs, audit logs, review gates, role checks, and secret masking.

## Why OB Engine exists

WordPress maintenance often involves repetitive but risky tasks:

- auditing content and metadata
- preparing SEO-ready drafts
- mapping custom fields
- validating structured data
- managing translation strings
- preventing accidental publishing
- keeping staging and production workflows reviewable

OB Engine aims to make these workflows more structured, predictable, and safe.

## Current focus

This repository is currently defining the public product blueprint before PHP implementation work begins. The community version focuses on:

- safety-first workflow contracts
- dry-run-first write operations
- draft or `needs_review` content defaults
- explicit approval for publishing and destructive actions
- BYOK provider settings with masked API keys
- addon-friendly contracts
- logs, snapshots, and rollback expectations
- a strict public/private boundary

## Safety principles

OB Engine is designed around safety gates:

- all writes must support dry-run first
- content writes default to `draft` or `needs_review`
- no hidden auto-publish
- destructive actions require explicit user approval
- capability checks and nonce validation
- masked BYOK secrets
- audit logs, snapshots, and rollback contracts
- predictable workflow execution
- staging-first mindset

## Status

This repository is the community/open-source version of OB Engine. The project is early, active, and under ongoing development.

## Product blueprint

- [Product brief](docs/PRODUCT_BRIEF.md)
- [Architecture](docs/ARCHITECTURE.md)
- [Master Architecture OBE v1.0](docs/MASTER_ARCHITECTURE_OBE_V1.md)
- [AI Engine Architecture](docs/AI_ENGINE_ARCHITECTURE.md)
- [Responses API Strategy](docs/RESPONSES_API_STRATEGY.md)
- [Information Architecture](docs/INFORMATION_ARCHITECTURE.md)

- [Admin UI Blueprint](docs/ADMIN_UI_BLUEPRINT.md) — WordPress-native OBE admin cockpit, menu structure, pages, and safety-first UI flows.
- [UI Design System](docs/UI_DESIGN_SYSTEM.md) — page widths, spacing, typography, colors, cards, forms, badges, tables, and confirmations.
- [Addons Integration Blueprint](docs/ADDONS_INTEGRATION_BLUEPRINT.md) — addon placement inside OBE and module detail page contracts.
- [Auto Post Workflow Blueprint](docs/AUTO_POST_WORKFLOW_BLUEPRINT.md) — source-to-draft import pipeline and review-first workflow.
- [Build Sequence](docs/BUILD_SEQUENCE.md)
- [Safety contract](docs/SAFETY_CONTRACT.md)
- [Addon contract](docs/ADDON_CONTRACT.md)
- [Workflow contract](docs/WORKFLOW_CONTRACT.md)
- [Public/private boundary](docs/PUBLIC_PRIVATE_BOUNDARY.md)
- [Roadmap](docs/ROADMAP.md)
- [Release plan](docs/RELEASE_PLAN.md)
- [Decisions](docs/DECISIONS.md)

## Contributing

Contributions, bug reports, and ideas are welcome. See [CONTRIBUTING.md](CONTRIBUTING.md).

## Security

If you find a security issue, please do not open a public issue. See [SECURITY.md](SECURITY.md).

## License

This project is licensed under GPL-2.0-or-later.
