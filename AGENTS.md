# AGENTS.md

Instructions for Codex and other agents working in this repository.

## Repository purpose

OB Engine Community is the public, GPL-compatible community base of OBE — Optimization-Based Engine: a safety-first WordPress optimization and automation plugin.

This repository must stay clean, minimal, secure, addon-friendly, and suitable as a public portfolio project. Do not import private prototype code, client-specific logic, production data, or experimental features without an explicit public design review.


## Project orientation before coding

Before starting any new task, read:

- `PROJECT.md`
- `docs/PROJECT_STATUS.md`
- `docs/VERSIONING.md`
- `docs/NEXT_MISSIONS.md`
- relevant architecture docs for the mission scope

Version and mission rules:

- Do not invent next version numbers.
- Use `docs/VERSIONING.md` as the source of truth for version decisions.
- Docs-only changes do not bump plugin version.
- Runtime feature PRs must mention the target version in the PR body.
- Every mission must say what is out of scope.

## Current implementation rule

Documentation is the source of truth for the current blueprint. Do not implement PHP features until a task explicitly asks for implementation.

For documentation-only tasks:

- edit Markdown files only
- do not add plugin runtime code
- do not add generated vendor assets
- do not add private datasets, fixtures, field maps, credentials, tokens, or logs

## Safety-first product rules

All future code and documentation must preserve these rules:

- All write operations must support dry-run before execution.
- Default content write status must be `draft` or `needs_review`.
- No hidden auto-publish behavior is allowed.
- Any destructive action requires explicit user approval.
- API keys must be bring-your-own-key (BYOK), stored safely, and masked in UI, logs, exports, and errors.
- Workflow execution must include guardrails, classification, approval gates, logs, snapshots, and a rollback contract.
- Addons must use documented public contracts instead of private internals.
- Pro/private features must remain separated from the community repository.

## Public/private boundary

Never commit:

- secrets, tokens, API keys, cookies, credentials, or private endpoints
- production-specific data or customer records
- private-project-specific field maps, prompts, workflows, schemas, or business rules
- private Optimization-Based Engine prototype code
- proprietary provider logic that cannot be distributed under the repository license

When in doubt, write a neutral public interface or documentation note instead of copying private implementation details.

## Admin UI design tokens

Before implementing or changing admin UI, read `DESIGN.md` and treat it as the machine-readable source of truth for OBE admin UI tokens. Keep implementation aligned with the WordPress-native values, safety-first labels, and addon integration rules documented there.

## Documentation style

- Be practical, concise, and specific.
- Prefer checklists, contracts, and explicit defaults.
- Use `draft`, `needs_review`, `dry_run`, and `approval_required` consistently.
- Keep README direction aligned with docs instead of duplicating every detail.
- Link related documents when useful.

## Git workflow

Before finishing a task:

1. Review `git status --short`.
2. Ensure changes match the requested scope.
3. Run an appropriate lightweight check, at minimum a Markdown/file review command when only docs changed.
4. Commit changes on the current branch with a clear message.
5. Prepare the pull request summary requested by the environment.
