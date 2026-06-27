# Release Gate

## v0.19 pre-publish hardening

`v0.19.0` is a pre-publish hardening release. It freezes major feature work and focuses on safety gates, trust copy, workflow visualization, smoke tests, and manual QC documentation.

## v1.0 manual gate

`v1.0` requires Roezi manual QC before publish. Do not publish unless every item in `docs/QC_CHECKLIST.md` passes.

## Required release blockers

- No publish unless workflow visualizer reviewed.
- No publish unless write gate verified.
- No publish unless public/private boundary verified.
- No publish unless Auto Post / Import copy accurately describes where AI calls happen.
- No publish unless smoke tests pass without external HTTP or provider calls.

## Out of scope for this gate

- New generation features.
- Pro/private runtime.
- Private adapters or partner API connectors.
- REST, MCP, WP-CLI, cron, workers, tools, or autonomous agents in Community runtime.
