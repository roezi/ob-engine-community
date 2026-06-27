# OBE UI Design System

OBE — Optimization-Based Engine — uses WordPress-native admin patterns. The interface should feel like a professional WordPress admin cockpit: clean, card-based, readable, consistent, and safety-focused, not a heavy SaaS dashboard.

## Foundation

### Page widths

| Context | Max width |
| --- | ---: |
| Default page | `1180px` |
| Form/settings page | `960px` |
| Table/list page | `1280px` |
| Detail/review page | `1180px` |

### Spacing

| Token | Value | Use |
| --- | ---: | --- |
| H1 to content | `16px` | First content block below page title |
| Section gap | `24px` | Between major sections |
| Card gap | `16px` | Between cards in a grid or stack |
| Form row gap | `14px` | Between form rows |
| Card padding | `20px` | Default cards |
| Small card padding | `16px` | Compact cards and summary cards |

### Typography

Use the WordPress admin default font stack. Do not introduce a custom webfont.

| Element | Size / line-height / weight |
| --- | --- |
| H1 | `24px / 32px / 600` |
| H2 | `18px / 24px / 600` |
| H3 | `15px / 22px / 600` |
| Body | `13px / 20px / 400` |
| Help text | `12px / 18px / muted` |
| Badge text | `11px / uppercase / 600` |

### Colors

Use WordPress admin color tokens and avoid brand-heavy custom palettes.

| Token | Hex | Use |
| --- | --- | --- |
| Text primary | `#1d2327` | Main text and headings |
| Text muted | `#646970` | Help text, metadata, secondary labels |
| Border | `#dcdcde` | Card, table, and form borders |
| Page background | `#f0f0f1` | Admin page background |
| Card background | `#ffffff` | Cards and panels |
| Primary blue | `#2271b1` | Primary buttons and links |
| Primary hover | `#135e96` | Primary hover state |
| Success green | `#00a32a` | Approved, enabled, completed |
| Warning yellow | `#dba617` | Needs review, dry-run, pending setup |
| Danger red | `#d63638` | Failed, rejected, destructive actions |
| Info blue | `#72aee6` | Informational states |

### Recommended CSS variables

These variables document the implementation contract for future CSS. Values may be declared in the OBE admin wrapper when UI implementation begins.

```css
:root {
  --obe-page-max-width: 1180px;
  --obe-form-max-width: 960px;
  --obe-table-max-width: 1280px;
  --obe-space-xs: 4px;
  --obe-space-sm: 8px;
  --obe-space-md: 16px;
  --obe-space-lg: 24px;
  --obe-space-xl: 32px;
  --obe-radius-sm: 4px;
  --obe-radius-md: 6px;
  --obe-border: #dcdcde;
  --obe-bg: #f0f0f1;
  --obe-card-bg: #ffffff;
  --obe-text: #1d2327;
  --obe-muted: #646970;
  --obe-primary: #2271b1;
  --obe-danger: #d63638;
  --obe-success: #00a32a;
  --obe-warning: #dba617;
}
```

## Buttons

### Sizes

| Button | Height | Padding | Minimum width |
| --- | ---: | ---: | ---: |
| Primary | `36px` | `0 14px` | `96px` |
| Secondary | `36px` | WordPress default or `0 14px` | Contextual |
| Small | `30px` | `0 10px` | Contextual |

### Allowed labels

Use clear, bounded, safety-first labels:

- Run dry-run
- Generate plan
- Save settings
- Save to Library
- Preview
- Review
- Approve
- Reject
- Write draft
- Archive
- Clear key

### Avoid labels

Do not use labels that imply unsafe automation or hidden writes:

- Auto publish
- Run all
- Turbo execute
- Fix everything
- Write production now

### Danger button rules

Danger actions must never be the primary default action. They must use clear language, require confirmation, and never perform hidden publish, delete, or destructive behavior. Prefer secondary placement and red text/border over a filled red button unless the confirmation screen is already active.

## Forms

| Field | Width |
| --- | ---: |
| Regular input | `420px` |
| Long input | `640px` |
| URL input | `640px` |
| Textarea | `100%`, max-width `760px` |
| Select | `320px` |

### Pattern

Each form row should use this order:

1. Label.
2. Required marker when applicable.
3. Input/control.
4. Help text explaining safe defaults, storage, or write impact.
5. Inline validation message when needed.

Required fields should use a visible `Required` text marker, not color alone. Disabled fields must look muted, include a reason, and avoid silently blocking the user.

## Cards

### Shared style

Cards use:

- background: `#ffffff`
- border: `1px solid #dcdcde`
- border-radius: `6px`
- padding: `20px`
- margin-bottom: `16px`
- subtle WordPress admin shadow only

### Card types

- **Status card:** health, setup, provider, safety, or addon status.
- **Action card:** one bounded action with explanation and safe next step.
- **Form card:** settings or mapping controls with help text.
- **Table card:** table wrapper with filters and pagination.
- **Review card:** generated plan/content with approval controls.
- **Empty-state card:** explains why content is absent and the next safe action.
- **Warning card:** non-destructive warning, limitation, or approval requirement.

## Status badges

| Badge | Color intent | Meaning |
| --- | --- | --- |
| DRAFT | muted/info | Stored but not approved or written |
| NEEDS REVIEW | warning | User review is required |
| DRY RUN | warning/info | Simulated action only |
| APPROVED | success | Approved by a user |
| REJECTED | danger | Rejected by a user |
| WRITTEN | success | Written to WordPress as `draft` or `needs_review` |
| PUBLISHED | success with warning context | Published only after explicit approval outside hidden automation |
| FAILED | danger | Action failed |
| ARCHIVED | muted | Hidden from active lists |
| ENABLED | success | Module enabled |
| DISABLED | muted | Module disabled |
| COMING SOON | info/muted | Not implemented in Community |
| PRO | info | Pro/private scope, not implemented in Community |

## Tables

Tables should use WordPress list-table conventions, link rows to detail pages, and avoid raw payloads inline. Show summaries, counts, redacted identifiers, and links to detail panels.

| Table | Columns |
| --- | --- |
| Library | Title, Type, Status, Source, Updated, Actions |
| Activity | Time, Actor, Action, Object, Status, Details |
| Workflow runs | Run, Template, Status, Step, Started, Updated, Actions |
| Auto Post sources | Source, Type, Items, Valid, Status, Updated, Actions |
| Addons | Addon, Scope, Status, Setup, Updated, Actions |

## Empty states

Every empty state must explain the next safe action:

- **Dashboard recent activity:** “No activity yet. Start with a dry-run or save a plan to Library.”
- **Library:** “No Library items yet. Generate a plan or save a reviewed result before writing to WordPress.”
- **Auto Post sources:** “No sources connected. Paste data, upload CSV/XLSX, or add a generic Partner API source for preview.”
- **Workflow runs:** “No workflow runs yet. Generate a plan or run a dry-run from a template.”
- **Addons:** “No addons enabled. Review available OBE modules and enable only the ones needed.”
- **Activity:** “No logged actions yet. Settings changes, dry-runs, approvals, and writes will appear here.”

## Confirmation behavior

Confirmation boxes are required for clear API key, delete/archive item, write draft, approve/reject, and destructive actions. Each confirmation must explain:

- what will happen;
- what data is used;
- where the result is stored;
- whether approval is required;
- whether the action writes to WordPress.

## Safety defaults

- Dry-run first.
- `draft` or `needs_review` first.
- No hidden auto-publish.
- No write without approval.
- No raw API keys, tokens, private prompts, private endpoints, or large raw private payloads in UI, logs, exports, or errors.
