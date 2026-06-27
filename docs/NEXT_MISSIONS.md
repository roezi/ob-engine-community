# Next Missions

This queue is ordered. Each mission should be delivered as one PR with explicit out-of-scope notes and safety requirements.

## Current next mission

After the `v0.16.1` addon-boundary patch, the next runtime target is `v0.17.0` — Auto Post Draft Generator inside the bundled Auto Post / Import Community addon, not unrestricted OBE Core. Auto Post Plans are generated into Library only and remain review-first.

## 1. `v0.12.0` — Dry-run / Write Draft Safety Runtime

- **Goal:** Add the safety runtime foundation that can model a Write Draft operation as a dry-run before any WordPress write.
- **Scope:** Dry-run result model, operation classification, write intent summary, approval requirement metadata, redacted Activity event shape, and admin-facing safety copy.
- **Out of scope:** Actual draft writing, publish runtime, Auto Post source intake, provider execution changes, REST/MCP/WP-CLI/cron/workers.
- **Safety requirements:** No hidden auto-publish; no WordPress write without explicit approval; default status must be `draft` or `needs_review`; no raw keys or private payloads in UI/logs.
- **Expected version bump:** `0.11.0` → `0.12.0`.
- **Definition of done:** A reviewer can inspect a dry-run summary for a potential Write Draft action without any public content being created or changed.

## 2. `v0.13.0` — Write Draft from Approved Library Item

- **Goal:** Allow an approved Library item to be written as a WordPress draft or `needs_review` item through a guarded runtime path.
- **Scope:** Approval-to-write handoff, capability and nonce checks, draft/needs_review status enforcement, Activity logging, and safe success/failure UI messages.
- **Out of scope:** Auto-publish, source intake, field mapping, bulk writes, REST/MCP/WP-CLI/cron/workers, provider-specific changes.
- **Safety requirements:** Only approved Library items are eligible; writes require explicit user action; no publish status; output remains auditable.
- **Expected version bump:** `0.12.0` → `0.13.0`.
- **Definition of done:** An approved Library item can be written to WordPress as `draft` or `needs_review`, with redacted logs and clear user confirmation.

## 3. `v0.14.0` — Source Intake Preview

- **Goal:** Add a generic, public-safe source preview step for future Auto Post workflows.
- **Scope:** Pasted text or generic file/source preview concepts, redacted sample display, source summary, and validation placeholders.
- **Out of scope:** Private adapters, private endpoints, credentials, field mapping, AI generation, WordPress writes, workers, cron, REST/MCP/WP-CLI.
- **Safety requirements:** Preview only; no external API calls; no private project labels; no production data fixtures; redacted display for sensitive-looking values.
- **Expected version bump:** `0.13.0` → `0.14.0`.
- **Definition of done:** Users can preview generic source input safely without generating content or writing to WordPress.

## 4. `v0.15.0` — Field Mapping + Validation

- **Goal:** Add generic field mapping and validation for source preview data.
- **Scope:** Public OBE field targets, required/optional field definitions, validation result model, review UI copy, and redacted Activity events.
- **Out of scope:** Private field maps, client-specific business rules, provider execution, draft generation, Write Draft changes, REST/MCP/WP-CLI/cron/workers.
- **Safety requirements:** Generic fields only; validation before generation; no private source assumptions; no WordPress writes.
- **Expected version bump:** `0.14.0` → `0.15.0`.
- **Definition of done:** A generic source preview can be mapped and validated with visible errors before any AI generation or write path.

## 5. `v0.16.0` — Auto Post Plan Generator

- **Goal:** Generate an Auto Post plan as a Library item from validated generic source data.
- **Scope:** Plan prompt contract, AI Engine request construction, structured plan output, Library save as `needs_review`, and Activity logging.
- **Out of scope:** Draft generation, publish runtime, private prompts, private provider adapters, direct WordPress writes, REST/MCP/WP-CLI/cron/workers.
- **Safety requirements:** Generated plans land in Library; no direct write; raw provider payloads are redacted; errors are normalized.
- **Expected version bump:** `0.15.0` → `0.16.0`.
- **Definition of done:** A validated source can produce a reviewable Auto Post plan in Library without creating WordPress content.

## 6. `v0.17.0` — Auto Post Draft Generator

- **Goal:** Generate a draft content Library item from an approved Auto Post plan inside the bundled Auto Post / Import Community addon.
- **Scope:** Plan-to-draft generation flow, structured draft output, Library save as `needs_review`, approval state handling, and redacted Activity events.
- **Out of scope:** OBE Core monolith expansion, Direct Write Draft runtime changes, auto-publish, private prompts, private adapters, partner connectors, bulk generation, external workers, REST/MCP/WP-CLI/cron.
- **Safety requirements:** Approved plan required; generated draft stays in Library; no WordPress write; no raw private payloads in UI/logs.
- **Expected version bump:** `0.16.0` → `0.17.0`.
- **Definition of done:** An approved plan can produce a reviewable Library draft without touching public WordPress content.

## 7. `v0.18.0` — Addons UI Hardening

- **Goal:** Harden the addon admin UI foundation added in `v0.16.1` and expand safe public addon metadata only where needed.
- **Scope:** Addons page/cards, module status labels, settings links/placeholders, safety copy, and public addon metadata shape.
- **Out of scope:** Separate top-level WordPress menus, Bricks/SEO/Performance runtime implementation, license/update channel, private adapters.
- **Safety requirements:** Addons stay inside OBE; disabled modules cannot run actions; labels must not imply unsafe automation or hidden publishing.
- **Expected version bump:** `0.17.0` → `0.18.0`.
- **Definition of done:** Maintainers can see addon/module cards inside OBE with safe statuses and no runtime side effects.

## 8. `v0.19.0` — Hardening pass 1

- **Goal:** Perform the first cross-cutting safety, reliability, and documentation hardening pass.
- **Scope:** Capability review, nonce review, sanitization/escaping review, redaction checks, documentation alignment, lightweight manual checks.
- **Out of scope:** New broad features, provider runtime expansion, Auto Post implementation, publish runtime, REST/MCP/WP-CLI/cron/workers.
- **Safety requirements:** Preserve dry-run-first, Library-first, approval-first, draft/needs_review defaults, and secret masking.
- **Expected version bump:** `0.18.0` → `0.19.0` if runtime hardening changes are included; no bump for docs-only hardening.
- **Definition of done:** The current MVP runtime has a documented safety review and fixes for any high-priority gaps found in scope.
