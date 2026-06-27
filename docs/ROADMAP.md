# Roadmap

This roadmap keeps OB Engine Community focused on a minimal, safe public foundation before larger automation features. The detailed clean-room rebuild order lives in [Rebuild Backlog](REBUILD_BACKLOG.md), and module placement rules live in [Module Boundary](MODULE_BOUNDARY.md).

## v0.1 — Public blueprint

- Master Architecture OBE v1.0 lock
- Product brief and architecture docs
- Safety, addon, and workflow contracts
- Public/private boundary
- Initial release plan and decisions log
- README alignment with the blueprint

## v0.2 — Repository foundation

- Contribution guide
- Security policy
- Coding standards for future WordPress work
- Issue and pull request templates
- Documentation review checklist
- Clean-room rebuild backlog and module boundary

## v0.3 — AI, Responses API, and core safety design

- AI Engine contract
- Responses API provider strategy
- Model profiles and task defaults
- Library-first generation contract
- Activity event contract

## v0.4 — Core safety design

- Dry-run data model design
- Operation classification design
- Approval gate design
- Secret masking design
- Log redaction design

## v0.5 — Workflow engine design

- Workflow schema draft
- Tool registry schema draft
- Guardrail and classification examples
- Snapshot and rollback examples
- Minimal fake-data workflow examples

## v0.6 — Addon developer preview

- Addon metadata contract
- Tool registration examples
- Workflow registration examples
- Compatibility policy
- Public hooks/interface proposal

## v0.7 — Minimal community implementation

- Plugin skeleton
- Admin settings shell
- BYOK settings placeholder with masking
- Dry-run-only sample workflow
- Basic log viewer concept

## v0.8 — Basic addon preview plan

- Import and source preview concepts
- CSV/XLSX preview concept
- Auto-content planning preview only
- Read-only SEO audit and link scanner preview
- Translation planning and manual/mock translation workflow concepts

## v0.9 — Hardening and compatibility

- Capability and nonce coverage
- Multisite considerations
- Import/export redaction checks
- Documentation and developer examples
- Backward compatibility review

## v0.10 — Release candidate

- Security review
- Accessibility review for admin UI
- Addon contract freeze candidate
- Upgrade and migration notes
- Public demo data only

## v1.0 — Stable community base

- Stable safety contract
- Stable addon contract
- Stable workflow contract
- Minimal safe automation workflows
- Release notes and support policy

## Admin UI blueprint v0.4

- Define the WordPress-native OBE sidebar: Dashboard, Library, Auto Post / Import, Workflow, Addons, Activity, and Settings.
- Treat addons as integrated OBE modules, not separate top-level WordPress admin products.
- Use the UI design system for spacing, cards, tables, forms, badges, confirmations, and safety-first labels.
- Keep Auto Post / Import review-first: source intake → preview → field mapping → validation → research plan → draft plan → generate → improve → dry-run → approval → write draft/needs_review.
- Acknowledge final-stage hardening while requiring minimum WordPress safety for every implementation.

See [Admin UI Blueprint](ADMIN_UI_BLUEPRINT.md), [UI Design System](UI_DESIGN_SYSTEM.md), [Addons Integration Blueprint](ADDONS_INTEGRATION_BLUEPRINT.md), and [Auto Post Workflow Blueprint](AUTO_POST_WORKFLOW_BLUEPRINT.md).
