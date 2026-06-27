# OBE Admin UI Blueprint

OBE — Optimization-Based Engine — is one cohesive WordPress optimization and automation engine. Its admin UI must stay inside a single OBE WordPress admin experience. Addons and modules are integrated OBE pages, not separate top-level WordPress admin menus.

Related documents:

- [UI Design System](UI_DESIGN_SYSTEM.md)
- [Addons Integration Blueprint](ADDONS_INTEGRATION_BLUEPRINT.md)
- [Auto Post Workflow Blueprint](AUTO_POST_WORKFLOW_BLUEPRINT.md)
- [Safety Contract](SAFETY_CONTRACT.md)

## Admin direction

- Use WordPress-native admin UI and list-table conventions.
- Keep the UI clean, card-based, readable, consistent, and safety-focused.
- Avoid a heavy SaaS dashboard style.
- Keep generated output in Library before any WordPress write.
- Require dry-run and explicit approval before write actions.
- Default WordPress content writes to `draft` or `needs_review`.
- Never auto-publish or hide destructive behavior.

## WordPress admin sidebar

OBE registers one top-level menu named **OBE** with these submenu pages:

1. Dashboard
2. Library
3. Auto Post / Import
4. Workflow
5. Addons
6. Activity
7. Settings

Addons must not register separate top-level WordPress admin menus. If an addon needs UI, it appears under **OBE → Addons** and may expose detail pages within the OBE route structure.

## Global layout

Each page uses:

- WordPress admin header with H1.
- `16px` spacing from H1 to first content block.
- Max widths from the [UI Design System](UI_DESIGN_SYSTEM.md): default `1180px`, settings `960px`, tables `1280px`, detail pages `1180px`.
- Cards with white background, `1px` border, `6px` radius, and `20px` padding.
- Status badges for safety state, approval state, provider state, and addon state.
- Confirmation boxes before approval, write, key clearing, archive/delete, or destructive actions.

## Dashboard

Purpose: provide a safe cockpit overview and guide users to the next non-destructive action.

### Cards

1. **Plugin Status card**
   - Shows plugin version, database/schema status placeholder, active modules count, and minimum WordPress/PHP compatibility state.
   - Primary action: Review settings.
   - Secondary action: View system info.

2. **Provider Status card**
   - Shows selected provider, masked key status, and provider readiness.
   - Never prints raw API keys.
   - Primary action: Save settings or Clear key when configured.

3. **Safety Status card**
   - Shows dry-run-first, draft-first, require approval before write, and disable auto-publish states.
   - Any disabled safety control should show a warning card explaining risk.

4. **Quick Actions card**
   - Allowed actions: Generate plan, Run dry-run, Save to Library, Review.
   - Do not include unsafe broad actions such as “Run all” or “Fix everything.”

5. **Recent Activity table/card**
   - Columns: Time, Actor, Action, Object, Status, Details.
   - Empty state: “No activity yet. Start with a dry-run or save a plan to Library.”

6. **Getting Started card**
   - Checklist: select provider, confirm safety settings, add source, generate plan, review, write draft.

## Library

Purpose: store generated plans, drafts, audit results, and reviewed outputs before any WordPress write.

### Tabs

- All
- Draft
- Needs Review
- Approved
- Written
- Archived

### Table

Columns: Title, Type, Status, Source, Updated, Actions.

Allowed actions:

- View
- Review
- Edit
- Archive
- Delete

Raw payloads must not appear inline in list tables. Use summaries and link to detail pages.

### Detail page layout

- Header: title, type, status badge, source label, updated time.
- Content summary card: readable generated plan or content preview.
- Source summary card: generic source metadata, redacted identifiers, no raw private payloads.
- Approval panel: Approve, Reject, Review notes, approval actor/time.
- Write panel: Write draft only after approval; explain target post type and resulting status (`draft` or `needs_review`).
- Activity panel: related events and redacted details.

## Auto Post / Import

Auto Post / Import is a flagship OBE workflow. See [Auto Post Workflow Blueprint](AUTO_POST_WORKFLOW_BLUEPRINT.md) for the full pipeline.

### Stepper

1. Source
2. Mapping
3. Validation
4. Research Plan
5. Generate
6. Review
7. Write Draft

### Source types

- Paste data
- Upload CSV
- Upload Excel/XLSX
- Spreadsheet export
- Partner API source
- Manual item

Public labels must remain generic, such as Spreadsheet Source, Partner Data Source, Travel Content Source, Accommodation Data Source, Affiliate Data Source, Product Data Source, Custom Field Adapter, Private Project Adapter, and Partner API Connector.

## Workflow

Purpose: expose safe workflow runs, templates, approvals, and a documented prototype flow without introducing autonomous background execution in Community.

### Tabs

- Runs
- Templates
- Approvals
- Prototype

### Run statuses

- `planned`
- `dry_run`
- `needs_review`
- `approved`
- `running`
- `completed`
- `failed`
- `cancelled`

### Template cards

- Auto Post from Spreadsheet
- SEO Audit
- Performance Review
- Translation Plan
- Content Improve

Each template card includes description, safety requirements, output destination, primary action “Generate plan,” and secondary action “Run dry-run” when applicable.

### Prototype flow

Start → Safety Guardrail → Source Classifier → Import Planner → Validation → Research Planner → Content Generator → Improve Agent → User Approval → Draft Writer → End

Community behavior: prototype/documented flow only unless a future task explicitly implements a bounded executor with WordPress safety controls.

## Addons

Purpose: manage OBE modules inside the OBE admin experience. See [Addons Integration Blueprint](ADDONS_INTEGRATION_BLUEPRINT.md).

### Tabs

- Overview
- Installed
- Available
- Settings
- Developer

### Addon cards

- SEO Audit & Fix
- Performance Audit
- Auto Content / Auto Post
- Translation
- Workflow Pro / Agent Builder Style Workflow

Each addon card includes name, short description, status badge, scope badge, primary action, and secondary action.

## Activity

Purpose: provide a redacted audit trail for settings changes, Library events, Auto Post actions, addon activity, workflow events, approvals, writes, and errors.

### Filters

- All
- Settings
- Library
- Auto Post
- Addons
- Workflow
- Errors

### Table

Columns: Time, Actor, Action, Object, Status, Details.

### Detail drawer/panel

The detail panel shows redacted context, related object link, user-visible message, status, and timestamps. It must not show raw API keys, tokens, private prompts, private endpoints, or large raw private payloads.

## Settings

Purpose: configure safe defaults, provider settings, addon visibility, data policy, and developer diagnostics.

### Tabs

- General
- Providers
- Safety
- Addons
- Data & Uninstall
- Developer

### Required settings

- Selected provider.
- Masked key status.
- Clear stored key.
- Dry-run-first.
- Draft-first.
- Require approval before write.
- Disable auto-publish.
- Addon list.
- Data retention / uninstall policy.
- Developer/system info placeholders.

## Confirmation boxes

Confirmation boxes are required for clear API key, delete/archive item, write draft, approve/reject, and destructive actions. They must explain what will happen, what data is used, where the result is stored, whether approval is required, and whether the action writes to WordPress.

## Hardening note

Final-stage security hardening will happen after core documentation and bounded implementations are stable. Minimum WordPress safety is still required for every future implementation: capability checks, nonce verification for writes, input sanitization, output escaping, BYOK masking, dry-run support, approval gates, redacted logs, and draft/needs_review defaults.
