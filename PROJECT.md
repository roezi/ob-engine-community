# Project Command Center

## Project

**OBE — Optimization-Based Engine** is the public, GPL-compatible community base for a safety-first WordPress optimization and automation plugin.

## Current status

- **Current plugin version:** `0.19.0`
- **Current stage:** Pre-publish hardening + workflow visualizer complete
- **Current mission status:** Hardening pass adds stricter service-level write gates, workflow visualization metadata, and QC/release gate documentation.
- **Next runtime target:** `v1.0` — Stable community base after Roezi manual QC

## MVP target summary

Provider settings → AI Engine → Library → Approval → Dry-run → Write Draft, with feature workflows owned by bundled Community addons

## Mission rule

**One mission = one PR.** Keep every PR focused on one documented mission with a clear goal, explicit out-of-scope notes, safety requirements, tests/checks, and version expectations.

## Safety-first rules

- No hidden auto-publish.
- No public WordPress write without approval.
- Generated output must land in Library before write.
- Default status is `needs_review` or `draft`.
- No raw keys or private payloads in UI/logs.
- No private project labels in public docs/code.

## Command center links

- [Project Status](docs/PROJECT_STATUS.md)
- [Versioning](docs/VERSIONING.md)
- [Milestones](docs/MILESTONES.md)
- [Next Missions](docs/NEXT_MISSIONS.md)
- [Project Board](docs/PROJECT_BOARD.md)
- [Build Sequence](docs/BUILD_SEQUENCE.md)
- [Roadmap](docs/ROADMAP.md)
