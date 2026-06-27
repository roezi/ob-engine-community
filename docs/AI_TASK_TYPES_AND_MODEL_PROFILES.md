# AI Task Types and Model Profiles

This contract defines public-safe AI task types and internal model profiles. It is documentation only.

## Model profiles

| Profile | Defaults | Use for |
| --- | --- | --- |
| `fast` | `reasoning_effort: low`, `verbosity: low` | classification, extraction, routing, simple rewrite |
| `balanced` | `reasoning_effort: medium`, `verbosity: medium` | auto post plan, SEO suggestion, content draft, translation plan |
| `deep` | `reasoning_effort: high`, `verbosity: medium/high` | workflow planning, complex review, debugging, synthesis |
| `background` | `reasoning_effort: medium/high`, `verbosity: medium`, `background: future only` | batch or long-running tasks later |

Do not default user-facing BYOK flows to `xhigh`.

## Task type matrix

| Task type | Purpose | Default profile | Output destination | Library required | Approval before write | Schema |
| --- | --- | --- | --- | --- | --- | --- |
| `classify_intent` | Classify user/source intent and route workflow. | `fast` | Activity metadata or caller response | No | Yes, if classification leads to write | `intent_classification_v1` |
| `validate_source` | Check source completeness, risk, and usability. | `fast` | Library metadata or Activity summary | No | Yes, before any write path | `source_validation_v1` |
| `map_fields` | Propose generic field mappings from source to target schema. | `balanced` | Library metadata or mapping plan item | Yes for reusable/import plans | Yes | `field_mapping_plan_v1` |
| `generate_auto_post_plan` | Create a reviewable plan before content generation/writes. | `balanced` | Library `auto_post_plan` | Yes | Yes | `auto_post_plan_v1` |
| `generate_research_plan` | Produce research checklist and validation questions. | `balanced` | Library `workflow_plan` or auto post plan metadata | Yes | Yes, if results affect write | `research_plan_v1` |
| `generate_content_draft` | Draft content from approved plan/context. | `balanced` | Library `content_draft` | Yes | Yes | `content_draft_v1` |
| `improve_content_draft` | Suggest revisions or create draft revision. | `balanced` | Library `content_review` or `content_draft` revision | Yes | Yes | `content_review_v1` |
| `review_content` | Review content for quality, safety, gaps, and readiness. | `deep` | Library `content_review` | Yes | Yes | `content_review_v1` |
| `generate_seo_review` | Analyze SEO title, description, structure, and internal opportunities. | `balanced` | Library `seo_review` | Yes | Yes | `seo_review_v1` |
| `generate_translation_plan` | Plan translation cleanup and locale-safe review. | `balanced` | Library `translation_plan` | Yes | Yes | `translation_plan_v1` |
| `generate_performance_report` | Summarize public-safe performance observations and fixes. | `balanced` | Library `performance_report` | Yes | Yes for changes | `performance_report_v1` |
| `summarize_activity` | Summarize redacted Activity events. | `fast` | Activity summary or Library metadata | No | No direct write | None or `performance_report_v1` when relevant |
| `explain_error` | Explain safe remediation for normalized errors. | `fast` | UI-safe message or Activity metadata | No | No direct write | `error_explanation_v1` |
| `workflow_plan` | Plan multi-step safe workflow with gates. | `deep` | Library `workflow_plan` | Yes | Yes | `workflow_plan_v1` |

## Library integration map

- `generate_auto_post_plan` → `auto_post_plan`
- `generate_research_plan` → `workflow_plan` or `auto_post_plan` metadata
- `generate_content_draft` → `content_draft`
- `improve_content_draft` → `content_review` or `content_draft` revision
- `generate_seo_review` → `seo_review`
- `generate_translation_plan` → `translation_plan`
- `generate_performance_report` → `performance_report`
- `workflow_plan` → `workflow_plan`

AI Engine may return output to caller. Future implementation should create/update Library items through Library Layer. Provider Layer must not create Library items directly.
