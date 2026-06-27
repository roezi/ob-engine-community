# Information Architecture

OBE means **Optimization-Based Engine**. Its WordPress admin experience must present one cohesive engine, not separate top-level products.

## Top-level menu

```text
OBE
├── Dashboard
├── Library
├── Auto Post / Import
├── Workflow
├── Addons
├── Activity
└── Settings
```

## Dashboard

Purpose:

- status overview
- provider status
- safety status
- quick actions
- recent activity
- incomplete approvals

## Library

Purpose: all generated, planned, reviewable, and written OBE items.

Library item types:

- `source_preview`
- `import_preview`
- `field_mapping`
- `auto_post_plan`
- `content_draft`
- `content_review`
- `seo_review`
- `translation_plan`
- `performance_report`
- `workflow_plan`
- `workflow_run`

Statuses:

- `draft`
- `needs_review`
- `approved`
- `rejected`
- `written`
- `archived`
- `failed`

## Auto Post / Import

Purpose: source intake through reviewed draft writing.

Flow:

```text
Spreadsheet / Excel / CSV / Partner API
→ source intake
→ source preview
→ field mapping
→ validation
→ research plan
→ draft plan
→ generate
→ improve
→ dry-run
→ approval
→ write draft/needs_review
→ publish only after approval
```

## Workflow

Purpose: template-based community workflows, workflow runs, dry-run results, approvals, and future Workflow Pro explanation.

Minimum runtime:

```text
input → guardrail → classify → validate → plan → dry-run → approval → write → log → end
```

Community has no visual builder and no autonomous background worker.

## Addons

Addons are integrated inside OBE, not separate top-level WordPress admin menus.

Addon cards:

- SEO Audit & Fix
- Performance Audit
- Auto Content / Auto Post
- Translation
- Workflow Pro / Agent Builder Style Workflow

## Activity

Purpose: audit trail.

Events include:

- `provider_settings_saved`
- `provider_key_saved`
- `provider_key_cleared`
- `source_preview_created`
- `mapping_saved`
- `validation_completed`
- `library_item_created`
- `ai_response_generated`
- `approval_requested`
- `item_approved`
- `item_rejected`
- `draft_written`

Never log raw API keys, tokens, private prompts, private endpoints, credentials, or large raw private payloads.

## Settings

Purpose: general settings, providers, safety, addons, data, and developer options.

Provider settings must support BYOK, masked keys, safe clear/save behavior, and Activity logging without raw secret values.

## UI design system

- default page max-width: `1180px`
- form/settings max-width: `960px`
- table/list max-width: `1280px`
- card padding: `20px`
- card gap: `16px`
- section gap: `24px`
- primary button height: `36px`
- small button height: `30px`
- regular input width: `420px`
- long input width: `640px`
- textarea max-width: `760px`

Patterns:

- status badges use uppercase labels such as `DRAFT`, `NEEDS REVIEW`, `DRY RUN`, `APPROVED`, `REJECTED`, `WRITTEN`, `FAILED`, `ARCHIVED`, `ENABLED`, `DISABLED`, and `PRO`
- empty states explain the next safe action
- tables link to detail pages rather than exposing raw payloads inline
- detail pages show source summary, generated output, risks, approval state, and activity
- confirmation boxes must describe what will happen, data used, risk, target storage, and approval requirement
