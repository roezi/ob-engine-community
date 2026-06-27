---
version: alpha
name: OBE Admin
description: Machine-readable design tokens for OBE admin UI implementation using WordPress-native admin values.
colors:
  primary: "#2271b1"
  primary-hover: "#135e96"
  text: "#1d2327"
  muted: "#646970"
  border: "#dcdcde"
  background: "#f0f0f1"
  card: "#ffffff"
  success: "#00a32a"
  warning: "#dba617"
  danger: "#d63638"
  info: "#72aee6"
typography:
  h1:
    fontFamily: system-ui
    fontSize: 24px
    lineHeight: 32px
    fontWeight: 600
  h2:
    fontFamily: system-ui
    fontSize: 18px
    lineHeight: 24px
    fontWeight: 600
  h3:
    fontFamily: system-ui
    fontSize: 15px
    lineHeight: 22px
    fontWeight: 600
  body:
    fontFamily: system-ui
    fontSize: 13px
    lineHeight: 20px
    fontWeight: 400
  help:
    fontFamily: system-ui
    fontSize: 12px
    lineHeight: 18px
    fontWeight: 400
  badge:
    fontFamily: system-ui
    fontSize: 11px
    lineHeight: 16px
    fontWeight: 600
rounded:
  sm: 4px
  md: 6px
  pill: 999px
spacing:
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  card-padding: 20px
  card-gap: 16px
  section-gap: 24px
  form-row-gap: 14px
components:
  page-default:
    width: 1180px
  page-form:
    width: 960px
  page-table:
    width: 1280px
  card:
    backgroundColor: card
    textColor: text
    rounded: md
    padding: 20px
  button-primary:
    backgroundColor: primary
    textColor: "#ffffff"
    rounded: sm
    height: 36px
    padding: "0 14px"
  button-secondary:
    backgroundColor: card
    textColor: primary
    rounded: sm
    height: 36px
    padding: "0 14px"
  button-small:
    height: 30px
    padding: "0 10px"
  input-regular:
    width: 420px
  input-long:
    width: 640px
  textarea:
    width: 760px
  badge:
    rounded: pill
    typography: badge
    padding: "3px 8px"
  badge-success:
    color: success
  badge-warning:
    color: warning
  badge-danger:
    color: danger
  badge-info:
    color: info
  badge-muted:
    color: muted
---

# OBE Admin Design Tokens

## Overview

OBE — Optimization-Based Engine — uses WordPress-native admin UI values. These tokens are the persistent, machine-readable source for admin UI implementation and should be read before adding or changing admin interface code.

OBE should feel like a professional admin cockpit: clear, compact, reviewable, and safe. It should not feel like a heavy SaaS dashboard or a separate product bolted onto WordPress.

All dangerous or write actions must be visually explicit, use safety-first labels, and preserve the OBE workflow contract: dry-run first, review before write, and default WordPress output status of `draft` or `needs_review`.

## Colors

Use the color tokens in the YAML front matter. They intentionally match WordPress admin values for primary actions, text, muted copy, borders, backgrounds, cards, and status states.

Do not invent custom colors for feature areas, addons, providers, or private labels. If a new UI state is needed, map it to `success`, `warning`, `danger`, `info`, or `muted`.

## Typography

Use `system-ui` and the WordPress admin scale defined in the tokens. Do not introduce custom webfonts or oversized dashboard headings.

Headings should structure the page clearly. Body and help text should explain safe defaults, write impact, approval requirements, and where generated output is stored.

## Layout

Use cards for major sections and keep page widths aligned with the tokenized layouts:

- `page-default` for dashboards, detail pages, and review screens.
- `page-form` for settings, setup, provider, and mapping pages.
- `page-table` for Library, Activity, workflow run lists, and addon tables.

Addons are integrated inside OBE. They should appear as OBE cards, tabs, detail routes, or settings panels, not as separate top-level WordPress admin menus.

## Components

Use the component tokens as implementation contracts for cards, buttons, inputs, textareas, and badges.

Status badges should make state visible for `draft`, `needs_review`, `dry_run`, `approval_required`, approved, rejected, written, failed, enabled, disabled, and coming-soon states.

Buttons must use clear action labels such as:

- Run dry-run
- Save to Library
- Review
- Approve
- Write draft

Keep raw payloads out of list tables. Use summaries, counts, redacted identifiers, status badges, and links to detail or review screens instead.

## Do's and Don'ts

### Do

- Use cards for major sections.
- Use status badges for state.
- Use clear action labels like Run dry-run, Save to Library, Review, Approve, Write draft.
- Keep raw payloads out of list tables.
- Make dangerous and write actions visually explicit.
- Keep addons integrated inside the OBE admin experience.

### Don't

- Do not use Auto publish, Run all, Turbo execute, Fix everything, or Write production now labels.
- Do not create separate top-level addon menus.
- Do not invent custom colors beyond the tokens.
- Do not expose raw API keys, private payloads, or private project labels.
- Do not add build tooling or dependencies just to consume this file.

## Future linting

`DESIGN.md` can be linted later with the design.md CLI after the project intentionally adopts that tooling. Do not add design token build tooling or npm dependencies until a future task explicitly requests it.
