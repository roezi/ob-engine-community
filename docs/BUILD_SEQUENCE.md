# Build Sequence

OBE v1.0 must be built in an order that locks architecture before runtime behavior. Do not jump directly to Auto Post implementation before the AI, Library, Safety, and Activity contracts are ready.

## Sequence

1. Master Architecture
2. UI Blueprint
3. AI / Responses API Contract
4. AI / Responses API Implementation
5. Library
6. Activity Log
7. Dry-run + Approval
8. Auto Post Source Preview
9. Field Mapping
10. Auto Post Planner
11. AI Draft Generator
12. Draft Writer
13. Addon modules
14. Workflow Pro / Agents SDK
15. Final hardening

## Stage expectations

### 1. Master Architecture

Lock the public OBE identity as Optimization-Based Engine and define layers, boundaries, safety invariants, addon placement, and build order.

### 2. UI Blueprint

Design WordPress-native page layouts, widths, cards, tables, badges, forms, detail pages, empty states, and approval confirmations.

### 3. AI / Responses API Contract

Define `AI_Request`, `AI_Response`, model profiles, task types, structured outputs, redaction, usage tracking, Provider Interface, Library integration, Activity integration, and Responses API payload mapping. This is the contract step before implementation and must include no runtime PHP or live API calls. OpenAI defaults to Responses API.

### 4. AI / Responses API Implementation

Next implementation mission after the contract PR: `feat: add AI request/response contracts`. Implement the AI request/response classes and then the OpenAI provider wrapper after the contract is stable. Do not use Chat Completions as the default OpenAI integration.

### 5. Library

Implement private CPT-backed Library items and statuses so generated work is reviewable before writing.

### 6. Activity Log

Record provider settings, key save/clear, source preview, mapping, validation, Library creation, AI generation, approval, rejection, and draft write events with redaction.

### 7. Dry-run + Approval

Implement dry-run result models, approval states, write gates, and confirmation behavior.

### 8. Auto Post Source Preview

Add pasted text, CSV, XLSX/export, and generic Partner API Source preview concepts without private adapters.

### 9. Field Mapping

Map generic fields only. Do not include private field maps or client-specific business rules.

### 10. Auto Post Planner

Create plans in Library before any draft generation or writing.

### 11. AI Draft Generator

Generate `content_draft` Library items using the AI Engine and selected provider.

### 12. Draft Writer

Write only approved Library `content_draft` items to `draft` or `needs_review`. Publish only after separate explicit approval.

### 13. Addon modules

Add SEO Audit & Fix, Performance Audit, Auto Content / Auto Post, Translation, and Workflow Pro cards under OBE → Addons. Addons must not create separate top-level menus.

### 14. Workflow Pro / Agents SDK

Keep Agents SDK out of Community Core. Consider it later for Workflow Pro or a separate service when OBE owns orchestration, tool execution, approvals, state, custom storage, and runtime behavior.

### 15. Final hardening

Perform full security and reliability hardening: capability audit, nonce audit, SQL audit, REST permission audit, file upload audit, log redaction audit, rollback verification, rate limiting, dependency review, and compatibility checks.

## Minimum safety from the start

Final hardening happens late, but every implementation stage must include capability checks, nonce checks for writes, sanitization, escaping, masked keys, no raw secret display, no direct publish, and no destructive default.
