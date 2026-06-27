# Project Status

## Current snapshot

- **Plugin version:** `0.16.1`
- **Stage:** Addon Registry foundation complete
- **Mission status:** Auto Post / Import is now a bundled Community addon/module in the addon registry while keeping existing review-first plan generation behavior.
- **Next runtime target:** `v0.17.0` — Auto Post Draft Generator

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
| `v0.12.0` | Dry-run / Write Draft Safety Runtime | Complete |
| `v0.13.0` | Write Draft from Approved Library Item | Complete |
| `v0.14.0` | Source Intake Preview | Complete |
| `v0.15.0` | Field Mapping + Validation | Complete |
| `v0.16.0` | Auto Post Plan Generator | Complete |
| `v0.16.1` | Addon Registry + Auto Post bundled addon boundary | Complete |

## Current capabilities

- OBE top-level admin menu.
- Provider settings with per-provider BYOK storage.
- OpenAI Responses client exists.
- AI Engine orchestrator exists.
- Manual Generate page exists.
- Generated output can be saved to private Library.
- Source previews can be created from generic pasted input.
- Field mappings can be validated and saved.
- Auto Post Plans can be generated explicitly from Field Mapping items and saved as `needs_review`.
- OBE → Addons lists bundled Community addon metadata and marks Auto Post / Import as enabled/community.
- Library items can be reviewed and approved/rejected.
- Activity Log records redacted events.

## Intentionally not implemented yet

- Auto Post draft generation.
- Bulk Auto Post generation.
- Auto Post plan approval-to-draft handoff.
- Gemini/Anthropic/OpenRouter runtime providers.
- SEO Review, Performance Review, and Translation Plan runtime addons.
- Pro/private addon runtime, private adapters, private prompts, private field maps, partner connectors, or client-specific workflows.
- REST/MCP/WP-CLI/cron/workers.
- Publish runtime.

## Current safety guarantees

- Generated AI output is routed to private Library before any WordPress write path.
- Review states use `needs_review`, approval, and rejection concepts before write runtime executes.
- Provider settings follow BYOK storage and masking expectations.
- Activity events are redacted and must not expose raw keys, tokens, private prompts, private endpoints, or large private payloads.
- No hidden auto-publish or publish runtime is part of the current community base.

## Known next risks

- Dry-run and Write Draft runtime must not bypass Library review or approval gates.
- Approval-to-write handoff must be explicit, auditable, nonce-protected, capability-checked, and default to `draft` or `needs_review`.
- Source intake and field mapping must remain generic and must not introduce private field maps, private endpoints, credentials, production data, or client-specific business rules.
- Additional providers must preserve redaction, capability checks, error normalization, and safe UI masking.
