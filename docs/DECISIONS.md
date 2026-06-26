# Decisions

This log records product and architecture decisions for OB Engine Community.

## 0001 — Community base is public and GPL-compatible

**Decision:** OB Engine Community is the public, GPL-compatible base of Optimized Builder Engine.

**Reason:** The project should be safe to publish, easy to review, and suitable for community contribution and portfolio use.

## 0002 — Safety-first before feature breadth

**Decision:** Dry-run, approvals, masking, logs, snapshots, and rollback contracts come before broad automation features.

**Reason:** WordPress automation can affect production content. Trust requires visible plans and reversible or reviewable actions.

## 0003 — Draft-first content operations

**Decision:** Generated or modified content defaults to `draft` or `needs_review`. No hidden auto-publish is allowed.

**Reason:** Users must remain in control of public content changes.

## 0004 — BYOK provider model

**Decision:** Provider credentials are bring-your-own-key and must be masked.

**Reason:** The community base should not require a proprietary hosted service and must avoid exposing secrets.

## 0005 — Addons extend through contracts

**Decision:** Addons use documented metadata, tool, workflow, provider, and field contracts instead of private internals.

**Reason:** Stable contracts make the community base maintainable and allow pro/private features to remain separate.

## 0006 — Public/private boundary is strict

**Decision:** Private prototype code, Destinasindo-specific field maps, production data, and pro-only features are excluded from this repository.

**Reason:** The public repo must remain clean, reusable, secure, and legally distributable.
