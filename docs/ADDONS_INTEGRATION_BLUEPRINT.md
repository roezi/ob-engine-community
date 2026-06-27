# OBE Addons Integration Blueprint

Addons are integrated modules inside OBE — Optimization-Based Engine. They are not separate UI products and must not create separate top-level WordPress admin menus. Addon UI appears under **OBE → Addons** and follows the [UI Design System](UI_DESIGN_SYSTEM.md).

## Integration rule

- One WordPress top-level menu: OBE.
- Addons appear as cards, tabs, and detail pages inside OBE.
- Addons use documented public contracts instead of private internals.
- Community docs may name private adapter categories generically, but private implementation details stay outside this repository.

## Addons page

### Tabs

- Overview
- Installed
- Available
- Settings
- Developer

### Required addon cards

1. SEO Audit & Fix
2. Performance Audit
3. Auto Content / Auto Post
4. Translation
5. Workflow Pro / Agent Builder Style Workflow

Each addon card includes:

- name;
- short description;
- status badge;
- scope badge;
- primary action;
- secondary action.

### Addon statuses

- Enabled
- Disabled
- Coming soon
- Pro
- Needs setup

### Addon scopes

- Community
- Basic
- Pro
- Private Adapter

## Card examples

| Addon | Description | Status | Scope | Primary action | Secondary action |
| --- | --- | --- | --- | --- | --- |
| SEO Audit & Fix | Reviews content metadata and suggests safe improvements. | Coming soon | Community | Review | Save to Library |
| Performance Audit | Reviews page performance signals without modifying server files. | Coming soon | Community | Preview | Review |
| Auto Content / Auto Post | Converts source data into reviewed Library items and drafts. | Coming soon | Community | Generate plan | Run dry-run |
| Translation | Plans translations and review queues without live autonomous translation. | Coming soon | Community | Generate plan | Review |
| Workflow Pro / Agent Builder Style Workflow | Documents advanced workflow prototypes for future Pro/private execution. | Pro | Pro | Preview | Review |

## Addon detail page blueprints

### SEO Audit & Fix

Tabs:

- Overview
- Audit
- Metadata Plan
- History
- Settings

Checks:

- title length;
- meta description;
- slug;
- H1/H2;
- keyword placement;
- internal links;
- schema suggestion;
- image alt text;
- duplicate title warning.

Community behavior:

- Suggest only.
- Save suggestions to Library.
- Require approval before any write.
- No direct RankMath/Yoast write in Community docs or implementation unless explicitly designed later.

### Performance Audit

Tabs:

- Overview
- Page Audit
- Assets
- Cache
- Reports
- Settings

Checks:

- cache detection;
- large images;
- script/style count;
- render-blocking hints;
- plugin/theme signals;
- basic Core Web Vitals checklist.

Community behavior:

- Read-only.
- No cache clear.
- No `.htaccess` edit.
- No destructive server or hosting changes.

### Auto Content / Auto Post

Tabs:

- Overview
- Sources
- Mappings
- Plans
- Generated Drafts
- Settings

Pipeline:

source intake → preview → field mapping → validation → research plan → draft plan → generate → improve → dry-run → approval → write draft/needs_review

Community behavior:

- Preview/review-first.
- No background autonomous worker.
- No auto-publish.
- Save generated output to Library before writing.

### Translation

Tabs:

- Overview
- Languages
- Strings
- Translation Plans
- Review Queue
- Hreflang
- Settings

Community behavior:

- Manual/mock translation planning.
- Review-first.
- No live translation runner by default.
- Save translation plans to Library before any write behavior is considered.

### Workflow Pro / Agent Builder Style Workflow

Tabs:

- Overview
- Prototype
- Templates
- Runs
- Settings

Community behavior:

- Document/prototype only.
- Advanced executor belongs to Pro/private later.
- Community UI may show prototype flows and template descriptions, but must not ship private workflows, private prompts, private field maps, or client-specific business rules.

## Settings integration

OBE Settings → Addons lists each addon with status, scope, setup state, safe defaults, and links to addon detail pages. Addon settings must inherit global safety controls: dry-run-first, draft-first, approval before write, disable auto-publish, and redacted logging.

## Developer integration

The Developer tab may document public addon contracts, hooks, capability requirements, data redaction expectations, and Library output contracts. It must not expose private internals or proprietary provider logic.
