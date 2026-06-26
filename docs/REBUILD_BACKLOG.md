# Rebuild Backlog

This backlog converts sanitized audit findings into a public-safe rebuild plan for OB Engine Community. It is a clean-room plan only: do not copy raw audit reports, private source code, private endpoints, credentials, production data, private field maps, private prompts, private workflows, or client-specific business rules.

See [Module Boundary](MODULE_BOUNDARY.md), [Architecture](ARCHITECTURE.md), [Safety Contract](SAFETY_CONTRACT.md), [Addon Contract](ADDON_CONTRACT.md), and [Workflow Contract](WORKFLOW_CONTRACT.md).

## Rebuild goals

- Rebuild from contracts and neutral public designs, not from the broad private prototype.
- Keep Community Core small, safe, and addon-friendly.
- Ship preview and read-focused community features before write execution.
- Reserve scheduled execution, production writers, fast-lane commits, and advanced write automation for Pro Addons.
- Keep private adapters outside the public repository.

## Non-goals

- No PHP/runtime implementation until explicitly requested.
- No raw report content.
- No private prototype source code.
- No private endpoints or credentials.
- No production data, client-specific mappings, private prompts, or private workflows.
- No hidden auto-publish or unsafe production shortcuts.

## v0.2 — Clean repository foundation

Purpose: make the public repository safe to work in before implementation starts.

Backlog:

- [ ] Add contribution, security, coding, and documentation review guidance.
- [ ] Keep README aligned with the blueprint without duplicating every detail.
- [ ] Add issue and pull request templates focused on public-safe contributions.
- [ ] Add a documentation review checklist that blocks private details and raw audit artifacts.
- [ ] Confirm `docs/MODULE_BOUNDARY.md` is the source for Community/Core/Pro/Private/Cut classification.

Exit criteria:

- Documentation-only changes remain Markdown-only.
- Reviewers can reject private data, copied prototype code, and unsafe automation proposals quickly.

## v0.3 — Core safety contracts

Purpose: define safety primitives before any runnable write path exists.

Backlog:

- [ ] Finalize `dry_run` input/output shape for all future write-capable operations.
- [ ] Define operation classifications: read, preview, plan, write, destructive.
- [ ] Define `approval_required` gates for destructive and production-impacting operations.
- [ ] Define BYOK secret storage, masking, redaction, and export exclusion requirements.
- [ ] Define minimal activity/audit log fields with redacted inputs and outputs.
- [ ] Define generic pause and lock primitives for future workflows.
- [ ] Define uninstall policy and retained/deleted data expectations.

Exit criteria:

- Every future write path has an explicit `dry_run` and approval policy.
- Secret handling is documented before any provider settings are implemented.

## v0.4 — Workflow and changeset contracts

Purpose: describe reviewable planning without automatic execution.

Backlog:

- [ ] Define workflow/task records as contracts, not automatic execution workers.
- [ ] Define planner draft and recommendation record formats.
- [ ] Define changeset records for proposed creates, updates, deletes, and metadata edits.
- [ ] Define snapshot and rollback contract metadata without implementing rollback execution.
- [ ] Define guardrail and classification examples using fake data only.
- [ ] Define content sanitizer requirements for generated or imported content.

Exit criteria:

- Plans and changesets can be reviewed without writing to production content.
- Rollback expectations are documented as contracts and limitations, not hidden promises.

## v0.5 — Addon and module boundary freeze candidate

Purpose: prepare extension points while keeping the core small.

Backlog:

- [ ] Finalize addon metadata, compatibility, and loader contracts.
- [ ] Define public interfaces for tools, providers, workflows, fields, and UI panels.
- [ ] Document which features belong in Community Core, Basic Addons, Pro Addons, Private Adapters, and Cut.
- [ ] Add examples that use neutral names, fake domains, and fake data only.
- [ ] Document feature flag expectations for preview and frontend-facing addon behavior.

Exit criteria:

- Addons can be designed against public contracts without depending on private internals.
- Pro/private packages have a clear boundary and cannot leak into the community repository.

## v0.6 — Minimal community implementation plan

Purpose: prepare the first implementation scope without broad prototype behavior.

Backlog:

- [ ] Plan clean plugin bootstrap and module loader.
- [ ] Plan dashboard shell and settings navigation.
- [ ] Plan BYOK settings placeholder with masked display behavior.
- [ ] Plan safety service interfaces for `dry_run`, approvals, logging, pause, and locks.
- [ ] Plan read-focused boundary routes only.
- [ ] Plan addon loader discovery without executing private addon logic.

Exit criteria:

- Implementation scope is limited to shell, contracts, and safe primitives.
- No worker queues, scheduled executors, production writers, or fast-lane commit paths are included in Community Core.

## v0.7 — Basic addon preview plan

Purpose: add public-safe preview features without production automation.

Backlog:

- [ ] Plan import `dry_run` preview and source preview.
- [ ] Plan CSV/XLSX preview with validation and redacted sample output.
- [ ] Plan auto-content planning preview only, defaulting to `needs_review` recommendations.
- [ ] Plan read-only SEO audit and link scanner preview.
- [ ] Plan translation planning support, manual/mock translation workflow, and language enablement UI.
- [ ] Plan hreflang, language switcher, and frontend translation filters behind feature flags.
- [ ] Plan SEO/cache detectors as read-only compatibility helpers.

Exit criteria:

- Basic Addons remain preview/read-only/manual unless a future task explicitly adds a safe write path.
- Any proposed write path references Community Core safety contracts.

## v0.8 — Hardening and compatibility

Purpose: make the community base reviewable and stable before release candidates.

Backlog:

- [ ] Review capability, nonce, and permission expectations for all planned admin surfaces.
- [ ] Review multisite assumptions and document unsupported cases.
- [ ] Review import/export redaction and secret exclusion rules.
- [ ] Review accessibility expectations for dashboard and addon UI.
- [ ] Review backward compatibility and migration policy for public contracts.
- [ ] Confirm no raw audit artifacts or private adapter details are present.

Exit criteria:

- Security, accessibility, redaction, and compatibility expectations are documented.
- Public/private boundary review is repeatable.

## v0.9 — Release candidate

Purpose: freeze the public community contracts and prepare release notes.

Backlog:

- [ ] Freeze safety, addon, workflow, and module boundary contracts for v1.0.
- [ ] Prepare release notes and support policy.
- [ ] Prepare public demo data policy using fake data only.
- [ ] Document known limitations for execution, rollback, and pro/private features.
- [ ] Confirm Pro Addons and Private Adapters are described only as extension boundaries.

Exit criteria:

- The community repository is safe to publish and review.
- v1.0 scope is small, stable, and free of private details.

## v1.0 — Stable community base

Purpose: ship a minimal safety-first foundation for public users and addon authors.

Backlog:

- [ ] Stable safety contract.
- [ ] Stable addon contract.
- [ ] Stable workflow and changeset contracts.
- [ ] Stable module boundary.
- [ ] Minimal safe community workflows, limited to approved public scope.
- [ ] Release notes, support policy, and upgrade guidance.

Exit criteria:

- Community Core remains clean, minimal, secure, and addon-friendly.
- Write behavior is never hidden, auto-publish is not allowed, and destructive actions require explicit approval.
- Private adapter and pro execution features remain outside the community repository.
