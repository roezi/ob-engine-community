# Project Status

## Current snapshot

- **Plugin version:** `0.11.0`
- **Stage:** Manual Generate to Library complete
- **Mission status:** AI output can be generated manually and saved to the private Library as `needs_review`.
- **Next runtime target:** `v0.12.0` — Dry-run / Write Draft Safety Runtime

## Completed milestones

| Version | Milestone | Status |
| --- | --- | --- |
| `v0.3.x` | Plugin skeleton / admin/settings/bootstrap foundation | Complete |
| `v0.4.0` | AI Request/Response contracts | Complete |
| `v0.5.0` | OpenAI Responses API client/provider | Complete |
| `v0.6.0` | Library Foundation | Complete |
| `v0.7.0` | Activity Log Foundation | Complete |
| `v0.8.0` | Multi-provider BYOK settings | Complete |
| `v0.9.0` | Approval Gate Foundation | Complete |
| `v0.10.0` | AI Engine Orchestrator | Complete |
| `v0.11.0` | Manual Generate to Library | Complete |

## Current capabilities

- OBE top-level admin menu.
- Provider settings with per-provider BYOK storage.
- OpenAI Responses client exists.
- AI Engine orchestrator exists.
- Manual Generate page exists.
- Generated output can be saved to private Library.
- Library items can be reviewed and approved/rejected.
- Activity Log records redacted events.

## Intentionally not implemented yet

- Write Draft runtime.
- Auto Post source intake.
- Field mapping.
- Source validation.
- Approval-to-write handoff.
- Gemini/Anthropic/OpenRouter runtime providers.
- Bricks/SEO/Performance addons.
- REST/MCP/WP-CLI/cron/workers.
- Publish runtime.

## Current safety guarantees

- Generated AI output is routed to private Library before any WordPress write path.
- Review states use `needs_review`, approval, and rejection concepts before write runtime exists.
- Provider settings follow BYOK storage and masking expectations.
- Activity events are redacted and must not expose raw keys, tokens, private prompts, private endpoints, or large private payloads.
- No hidden auto-publish or publish runtime is part of the current community base.

## Known next risks

- Dry-run and Write Draft runtime must not bypass Library review or approval gates.
- Approval-to-write handoff must be explicit, auditable, nonce-protected, capability-checked, and default to `draft` or `needs_review`.
- Source intake and field mapping must remain generic and must not introduce private field maps, private endpoints, credentials, production data, or client-specific business rules.
- Additional providers must preserve redaction, capability checks, error normalization, and safe UI masking.
