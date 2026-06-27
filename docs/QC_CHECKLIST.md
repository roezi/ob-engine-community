# QC Checklist

**Do not publish before all pass.** v0.19.0 is a pre-publish hardening gate for OBE Community.

## Workflow QC

- [ ] Confirm Source Preview → Field Mapping → Auto Post Plan → Draft Candidate → Editorial Humanizer → Library → Approval → Dry-run → Write Draft is visible.
- [ ] Confirm generated output reaches Library before any WordPress write.
- [ ] Confirm the write path is only Library detail approval + dry-run + Write draft.

## UI/UX QC

- [ ] Confirm labels use review-first actions: Review, Run dry-run, Generate plan, Save to Library, Write draft, Back to Library.
- [ ] Confirm no unsafe action labels appear: Auto publish, Publish now, Write posts now, Run all, Fix everything.
- [ ] Confirm Auto Post / Import copy accurately says Source Intake Preview and Mapping + Validation do not call AI, while plan/draft generation call AI only after explicit admin action.

## Target alignment QC

- [ ] Confirm default WordPress write status is `draft` or `needs_review`/pending review.
- [ ] Confirm publish is rejected by the Write Draft path.
- [ ] Confirm no direct WordPress writes occur outside the controlled writer.

## Safety gate QC

- [ ] Confirm `source_preview`, `field_mapping`, and `auto_post_plan` cannot dry-run/write.
- [ ] Confirm only `content_draft` and `editorial_revision` are writeable Library types.
- [ ] Confirm write requires a private OBE Library item, approved approval record, approved Library status, writeable Library type, and latest passed dry-run.

## Addon boundary QC

- [ ] Confirm Community contains no Pro/private runtime, private adapters, partner API connectors, or client-specific business rules.
- [ ] Confirm addons remain inside the OBE admin experience and do not add separate top-level menus.

## Agent/workflow visualizer QC

- [ ] Confirm OBE → Workflow is visualization-only: no POST actions, AI calls, tool execution, background jobs, or workflow execution.
- [ ] Confirm Agent SDK / Workflow Prototype text is metadata-only and states future execution belongs to Workflow Pro/private tooling unless explicitly approved later.

## Provider/key redaction QC

- [ ] Confirm provider keys are BYOK and masked.
- [ ] Confirm Activity logs and errors do not expose raw keys, tokens, private prompts, private endpoints, or large private payloads.

## Manual WordPress test path

1. Activate/update plugin.
2. Open OBE → Workflow and verify visual flow.
3. Open OBE → Auto Post / Import and verify copy is accurate.
4. Create Source Preview.
5. Create Field Mapping.
6. Generate Auto Post Plan.
7. Generate Draft Candidate.
8. Generate Humanizer revision.
9. Approve only the content draft/editorial revision.
10. Run dry-run.
11. Write draft.
12. Confirm no publish.
13. Confirm Activity logs are redacted.
14. Confirm non-writeable item types cannot dry-run/write.
15. Confirm UI/UX matches target flow.
