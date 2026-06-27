# Structured Output Schemas

This document defines documentation-only structured output schema contracts for future AI implementation. Examples are generic and public-safe.

## Common rules

- Structured workflows should use `output_json` when available.
- Schema validation failures produce `structured_output_invalid`.
- Defaults for content writes must be `draft` or `needs_review`.
- No schema may imply hidden auto-publish or destructive action.
- Private prompts, private field maps, production data, credentials, and client-specific business rules are not allowed.

## Schema index

### `intent_classification_v1`

Purpose: classify intent and routing risk.

Required fields: `intent`, `confidence`, `recommended_task_type`, `safety_notes`.
Optional fields: `requires_approval`, `missing_context`.
Target Library item type: none by default.
Safety notes: classification cannot write content.

```json
{
  "intent": "generate_content_plan",
  "confidence": 0.82,
  "recommended_task_type": "generate_auto_post_plan",
  "requires_approval": true,
  "missing_context": [],
  "safety_notes": ["Create a Library plan before any write."]
}
```

### `source_validation_v1`

Purpose: validate source usability before planning.

Required fields: `source_summary`, `is_usable`, `missing_fields`, `risk_flags`, `safety_notes`.
Optional fields: `normalization_notes`, `recommended_next_step`.
Target Library item type: source validation metadata.
Safety notes: do not store large raw source payloads in Activity.

```json
{
  "source_summary": "Generic source contains title and body fields.",
  "is_usable": true,
  "missing_fields": ["canonical_url"],
  "risk_flags": [],
  "normalization_notes": ["Trim empty fields."],
  "recommended_next_step": "map_fields",
  "safety_notes": ["Review mapping before generation."]
}
```

### `field_mapping_plan_v1`

Purpose: propose generic field mapping.

Required fields: `source_fields`, `target_fields`, `mapping`, `unmapped_fields`, `approval_question`.
Optional fields: `transform_notes`, `safety_notes`.
Target Library item type: mapping plan metadata.
Safety notes: private field maps must be redacted.

```json
{
  "source_fields": ["headline", "summary"],
  "target_fields": ["post_title", "post_excerpt"],
  "mapping": [{"source": "headline", "target": "post_title", "confidence": 0.9}],
  "unmapped_fields": [],
  "transform_notes": ["Use summary as excerpt only after review."],
  "safety_notes": ["No write until approved."],
  "approval_question": "Approve this generic field mapping for dry-run?"
}
```

### `auto_post_plan_v1`

Purpose: plan Auto Post output before generation or writing.

Required fields: `source_summary`, `missing_fields`, `proposed_post_type`, `title_options`, `slug_suggestion`, `meta_title`, `meta_description`, `outline`, `research_checklist`, `image_prompt_suggestions`, `custom_field_mapping`, `safety_notes`, `default_status`, `approval_question`.
Optional fields: `taxonomy_suggestions`, `internal_link_notes`, `content_risks`.
Target Library item type: `auto_post_plan`.
Safety notes: `default_status` must be `draft` or `needs_review`; never auto-publish.

```json
{
  "source_summary": "Public-safe summary of the source.",
  "missing_fields": ["primary_image_alt"],
  "proposed_post_type": "post",
  "title_options": ["Example Draft Title", "Alternative Draft Title"],
  "slug_suggestion": "example-draft-title",
  "meta_title": "Example Draft Title",
  "meta_description": "A concise public-safe draft description.",
  "outline": [{"heading": "Overview", "bullets": ["Explain the topic."]}],
  "research_checklist": ["Verify facts before drafting."],
  "image_prompt_suggestions": ["Neutral illustrative image concept."],
  "custom_field_mapping": [],
  "safety_notes": ["Generate Library draft before WordPress write."],
  "default_status": "needs_review",
  "approval_question": "Approve this plan for draft generation?"
}
```

### `research_plan_v1`

Purpose: list research questions and validation steps.

Required fields: `research_questions`, `sources_needed`, `validation_steps`, `safety_notes`.
Optional fields: `assumptions`, `blocked_until`.
Target Library item type: `workflow_plan` or `auto_post_plan` metadata.
Safety notes: web/file search requires controlled context and user-facing permission.

```json
{
  "research_questions": ["What facts need verification?"],
  "sources_needed": ["Public documentation or user-provided source"],
  "validation_steps": ["Confirm claims before writing."],
  "assumptions": [],
  "safety_notes": ["Do not browse or fetch unless the workflow explicitly allows it."]
}
```

### `content_draft_v1`

Purpose: represent a reviewable content draft.

Required fields: `title`, `body`, `excerpt`, `default_status`, `safety_notes`, `approval_question`.
Optional fields: `slug`, `meta_title`, `meta_description`, `taxonomy_suggestions`, `image_notes`.
Target Library item type: `content_draft`.
Safety notes: draft must land in Library before WordPress write.

```json
{
  "title": "Reviewable Draft Title",
  "slug": "reviewable-draft-title",
  "body": "Draft content for review.",
  "excerpt": "Short summary.",
  "meta_title": "Reviewable Draft Title",
  "meta_description": "Short SEO description.",
  "taxonomy_suggestions": [],
  "image_notes": [],
  "default_status": "draft",
  "safety_notes": ["Requires approval before writing."],
  "approval_question": "Approve dry-run write for this draft?"
}
```

### `content_review_v1`

Purpose: review or improve draft content.

Required fields: `summary`, `issues`, `recommendations`, `readiness`, `safety_notes`.
Optional fields: `suggested_revision`, `approval_question`.
Target Library item type: `content_review` or `content_draft` revision.
Safety notes: recommendations are not writes.

```json
{
  "summary": "Draft is understandable but needs source verification.",
  "issues": [{"severity": "medium", "message": "Add citation for key claim."}],
  "recommendations": ["Verify factual claims."],
  "readiness": "needs_review",
  "suggested_revision": "Optional revised passage.",
  "safety_notes": ["Reviewer approval required before write."]
}
```

### `seo_review_v1`

Purpose: review SEO metadata and content structure.

Required fields: `meta_title`, `meta_description`, `findings`, `recommendations`, `safety_notes`.
Optional fields: `keyword_notes`, `internal_link_suggestions`, `schema_notes`.
Target Library item type: `seo_review`.
Safety notes: SEO changes require dry-run and approval before write.

```json
{
  "meta_title": "Suggested Meta Title",
  "meta_description": "Suggested description for review.",
  "findings": ["Title length is acceptable."],
  "recommendations": ["Review internal link opportunities."],
  "keyword_notes": [],
  "internal_link_suggestions": [],
  "schema_notes": [],
  "safety_notes": ["Do not write metadata without approval."]
}
```

### `translation_plan_v1`

Purpose: plan translation cleanup.

Required fields: `source_locale`, `target_locale`, `segments`, `review_notes`, `safety_notes`.
Optional fields: `glossary_terms`, `untranslated_terms`, `approval_question`.
Target Library item type: `translation_plan`.
Safety notes: locale changes require review before write.

```json
{
  "source_locale": "en",
  "target_locale": "es",
  "segments": [{"source": "Example", "suggestion": "Ejemplo", "confidence": 0.8}],
  "glossary_terms": [],
  "untranslated_terms": [],
  "review_notes": ["Human review recommended."],
  "safety_notes": ["No automatic overwrite."],
  "approval_question": "Approve this translation plan for dry-run?"
}
```

### `performance_report_v1`

Purpose: summarize performance findings and safe recommendations.

Required fields: `summary`, `findings`, `recommendations`, `risk_level`, `safety_notes`.
Optional fields: `metrics`, `dry_run_actions`.
Target Library item type: `performance_report`.
Safety notes: destructive/cache-changing actions require approval.

```json
{
  "summary": "Performance review found optimization opportunities.",
  "metrics": [],
  "findings": ["Large images may affect load time."],
  "recommendations": ["Prepare image optimization dry-run."],
  "dry_run_actions": [],
  "risk_level": "low",
  "safety_notes": ["Do not execute changes from provider layer."]
}
```

### `workflow_plan_v1`

Purpose: define a gated workflow plan.

Required fields: `goal`, `steps`, `dry_run_required`, `approval_required`, `rollback_notes`, `safety_notes`.
Optional fields: `required_capabilities`, `library_items`.
Target Library item type: `workflow_plan`.
Safety notes: write/destructive steps require explicit approval.

```json
{
  "goal": "Prepare reviewable content update.",
  "steps": [{"name": "Validate source", "type": "read", "requires_approval": false}],
  "required_capabilities": ["structured_output"],
  "library_items": [],
  "dry_run_required": true,
  "approval_required": true,
  "rollback_notes": "Capture snapshot before write.",
  "safety_notes": ["Never auto-publish."]
}
```

### `error_explanation_v1`

Purpose: explain normalized errors safely.

Required fields: `public_summary`, `likely_cause`, `recommended_action`, `retryable`, `safety_notes`.
Optional fields: `support_code`, `docs_links`.
Target Library item type: none by default.
Safety notes: do not expose raw provider error bodies or secrets.

```json
{
  "public_summary": "The provider key is missing or not configured.",
  "likely_cause": "BYOK settings are incomplete.",
  "recommended_action": "Add a provider key in settings and retry.",
  "retryable": true,
  "support_code": "missing_provider_key",
  "docs_links": [],
  "safety_notes": ["Do not display raw keys or provider response bodies."]
}
```
