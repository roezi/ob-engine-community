# Module Boundary

This document defines the clean-room boundary for rebuilding OB Engine Community from sanitized audit findings. It is intentionally public-safe: it does not include raw audit reports, private source code, private endpoints, credentials, production data, client-specific rules, or private field maps.

## Boundary principles

- Community code and documentation must be GPL-compatible, generic, and reusable.
- Documentation remains the source of truth until implementation is explicitly requested.
- Every write-capable path must support `dry_run` before execution.
- Content writes must default to `draft` or `needs_review`.
- Destructive actions require `approval_required` and explicit user approval.
- BYOK credentials must be stored safely and masked in UI, logs, exports, and errors.
- Addons must extend public contracts instead of private internals.
- Pro and private functionality must remain outside the community repository unless explicitly redesigned for public release.

## Community Core

Community Core is the minimal public foundation. It should define safe primitives and contracts, not broad automation behavior.

Belongs in Community Core:

- clean plugin bootstrap and module loader
- dashboard shell with capability checks and nonce expectations
- BYOK settings contract
- encrypted and masked API key storage contract
- safety layer contract
- `dry_run` contract for write-capable operations
- `approval_required` gate contract
- minimal activity/audit log contract
- workflow/task record contracts without automatic production execution
- read-focused MCP and boundary route contracts
- planner draft and recommendation contracts
- changeset contract for reviewable proposed changes
- addon loader and addon contract
- generic pause and lock primitives
- content sanitizer contract
- uninstall policy

Community Core must not include hidden auto-publish, production shortcut execution, private adapter behavior, private provider logic, or site-specific business assumptions.

## Community Basic Addons

Community Basic Addons may provide public-safe, mostly preview-oriented features that demonstrate the contracts without introducing unsafe production automation.

Belongs in Community Basic Addons:

- import `dry_run` preview
- CSV/XLSX preview
- source preview
- auto-content planning preview only
- read-only SEO audit
- translation planning support
- clean translation addon structure
- manual or mock translation workflow
- language enablement UI
- hreflang and language switcher features
- frontend translation filters behind feature flags
- link scanner preview
- SEO/cache detectors

Basic Addons should favor previews, planning, validation, and read-only analysis. Any future write path must use Community Core safety contracts and default to `draft`, `needs_review`, `dry_run`, or `approval_required` as appropriate.

## Pro Addons

Pro Addons may implement advanced execution features, production write operations, and paid access controls outside this community repository.

Belongs in Pro Addons:

- license/pro access service
- scheduled executor or worker
- production-safe apply/execute paths
- import draft writer
- auto-content generator
- auto-content draft importer
- live translation runner
- rollback executor
- WP-CLI turbo or fast-lane commit paths
- advanced production write operations

Pro Addons must still respect the public safety contracts: `dry_run` support for writes, explicit approvals for destructive actions, masked secrets, logs, snapshots, and rollback metadata.

## Private Adapter

Private adapters are site-specific integration packages. They must remain outside the community repository.

Belongs in a Private Adapter:

- site-specific CPT assumptions
- site-specific field maps
- private staging guards
- private prompts and workflows
- private campaign seeding or writing logic for translation plugins
- affiliate adapter logic and credentials
- private affiliate workflows
- production-only data transformations
- client-specific business rules

Private adapters may consume public contracts, but the community repository must not contain private source code, private endpoints, credentials, production data, private field details, or client-specific instructions.

## Cut from the rebuild

The clean-room rebuild should intentionally remove unsafe or messy prototype patterns.

Cut entirely:

- monolithic mixed-responsibility plugin entrypoint implementation
- temporary scan artifacts
- duplicated or messy UI features
- hidden auto-publish behavior
- unsafe production shortcuts
- raw audit report content
- copied private prototype code
- private endpoints, secrets, cookies, tokens, passwords, or credentials
- private field maps, prompts, workflows, schemas, and business rules

## Review checklist

Before adding a module or document, confirm:

- [ ] Is it generic and public-safe?
- [ ] Does it avoid private source code and raw audit text?
- [ ] Does it avoid secrets, credentials, production data, and private endpoints?
- [ ] Does it avoid site-specific field details and business rules?
- [ ] Does it preserve `dry_run`, `draft`, `needs_review`, and `approval_required` defaults?
- [ ] Does it belong in Community Core, a Basic Addon, a Pro Addon, a Private Adapter, or Cut?
