# Release Plan

## Release principles

- Ship small, reviewable increments.
- Keep the community edition GPL-compatible.
- Prefer documented contracts before implementation.
- Never ship secrets, production data, or private field maps.
- Treat safety regressions as release blockers.

## Pre-release checklist

Before each release:

- Review public/private boundary compliance.
- Confirm all write paths support dry-run.
- Confirm default content status is `draft` or `needs_review`.
- Confirm destructive actions require explicit approval.
- Confirm API keys are masked in UI, logs, exports, and errors.
- Confirm addon and workflow contracts are updated when behavior changes.
- Confirm documentation examples use fake data only.

## Versioning approach

Before v1.0, minor versions may change contracts while they are being designed. Each breaking contract change should be recorded in [Decisions](DECISIONS.md) or release notes.

At v1.0, the safety, addon, and workflow contracts should be treated as stable public interfaces.

## Release artifacts

A release should include:

- version tag
- changelog or release notes
- compatibility notes
- contract changes
- known limitations
- security notes when relevant

## Release blockers

Do not release if:

- secrets or production data are present
- private/pro-only code is mixed into the community base
- dry-run behavior is missing for writes
- hidden auto-publish behavior exists
- destructive actions can run without explicit approval
- logs expose API keys or sensitive data
