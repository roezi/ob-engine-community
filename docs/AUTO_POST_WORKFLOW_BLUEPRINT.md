# OBE Auto Post / Import Workflow Blueprint

Auto Post / Import is the flagship OBE workflow for safely turning structured sources into reviewed Library items and WordPress drafts. It must use generic public labels and never include private project names, private endpoints, credentials, private prompts, private field maps, production data, or client-specific rules.

## Safety contract

- The pipeline is review-first and dry-run-first.
- All generated output is saved to Library before WordPress writes.
- WordPress writes require explicit approval.
- Default write status is `draft` or `needs_review`.
- Publishing only happens after approval and never through hidden auto-publish behavior.
- Private adapters stay outside the community repository.

## End-to-end data pipeline

Spreadsheet / Excel / CSV / Partner API
→ source intake
→ preview
→ field mapping
→ validation
→ research plan
→ draft plan
→ generate
→ improve
→ dry-run
→ approval
→ write as draft/needs_review
→ publish only after approval

## Stepper

### 1. Source

Purpose: collect source data without writing anything.

Allowed source types:

- Paste data
- Upload CSV
- Upload Excel/XLSX
- Spreadsheet export
- Partner API source
- Manual item

Generic public source labels:

- Project Auto Post
- Partner Data Source
- Spreadsheet Source
- Travel Content Source
- Accommodation Data Source
- Affiliate Data Source
- Product Data Source
- Custom Field Adapter
- Private Project Adapter
- Partner API Connector

UI requirements:

- Explain that this step only previews source data.
- Show file/source type, item count, and redacted source identifier.
- Do not show raw tokens, credentials, private endpoints, or large raw payloads.
- Primary action: Preview.

### 2. Mapping

Purpose: map source fields to public OBE fields.

UI requirements:

- Show source field names, sample redacted values, target OBE field, required state, and notes.
- Allow Save settings or Save to Library for reusable mapping plans.
- Use generic target fields such as title, summary, canonical URL, category, image URL, location, price, source URL, and notes.
- Do not include private field maps in the community repository.

### 3. Validation

Purpose: detect missing required fields, unsafe values, duplicates, malformed URLs, and unsupported data types.

Validation output:

- Valid item count.
- Needs review count.
- Failed item count.
- Warnings with safe next action.
- Duplicate title/source warnings.
- URL and image checks as metadata only unless a future implementation explicitly performs network checks.

Primary action: Generate plan.

### 4. Research Plan

Purpose: create a bounded plan for enrichment or content generation.

The plan should include:

- Source summary.
- Intended content type.
- Required facts from source.
- Optional research questions.
- Safety constraints.
- Proposed Library output type.
- Approval requirements.

Community behavior: no private prompts, no private provider logic, and no background autonomous research runner unless explicitly implemented later.

### 5. Generate

Purpose: generate a draft plan or content candidate for review.

UI requirements:

- Show generation status and provider status.
- Save result to Library.
- Mark generated items as DRAFT or NEEDS REVIEW.
- Provide Improve and Review actions.
- Do not write to WordPress in this step.

### 6. Review

Purpose: let a user inspect, improve, approve, or reject generated output.

Review page includes:

- Content preview.
- Source summary.
- Validation warnings.
- Approval panel.
- Activity panel.
- Actions: Review, Approve, Reject, Save to Library, Run dry-run.

Approval must record actor, timestamp, summary, and resulting status.

### 7. Write Draft

Purpose: write approved content to WordPress as `draft` or `needs_review`.

Before writing, show a confirmation box explaining:

- what will happen;
- which Library item is used;
- target post type/status;
- where the draft will be stored;
- whether approval has been recorded;
- that this writes to WordPress but does not publish.

Allowed action: Write draft.

## Tables

### Sources table

Columns: Source, Type, Items, Valid, Status, Updated, Actions.

### Plans table

Columns: Plan, Source, Status, Items, Updated, Actions.

### Generated drafts table

Columns: Title, Source, Status, Library Item, Updated, Actions.

## Empty states

- Sources: “No sources connected. Paste data, upload CSV/XLSX, or add a generic Partner API source for preview.”
- Plans: “No plans yet. Preview and validate a source, then generate a plan.”
- Generated drafts: “No generated drafts yet. Generate a reviewed Library item before writing a WordPress draft.”

## Failure behavior

Failures should store a redacted error summary in Activity and keep the user on the current step with a safe next action. Do not expose raw provider responses, credentials, tokens, private endpoints, private prompts, or large raw source payloads.
