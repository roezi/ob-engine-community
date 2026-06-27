---
version: alpha
name: OBE Admin
description: Machine-readable design tokens for the OBE WordPress-native admin UI.
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

OBE — Optimization-Based Engine — uses WordPress-native admin UI values. This file is the persistent token source for coding agents that implement OBE admin screens, components, and documentation examples.

OBE should feel like a professional admin cockpit: clean, card-based, readable, consistent, and safety-focused. It should not feel like a heavy SaaS dashboard, and it must not introduce custom visual systems that fight WordPress admin conventions.

All admin UI work must preserve the OBE safety model: dangerous and write actions are visually explicit, dry-run and review states are clear, and default content write status remains `draft` or `needs_review`.

## Colors

Use the `colors` map in the YAML front matter as the design token source of truth. These values mirror WordPress admin defaults and the OBE UI Design System.

- Use `primary` and `primary-hover` for primary buttons and links.
- Use `text`, `muted`, `border`, `background`, and `card` for base admin surfaces.
- Use `success`, `warning`, `danger`, and `info` only for explicit status, validation, safety, and workflow states.
- Do not invent custom colors beyond the tokens in this file.

## Typography

Use the WordPress admin default font stack through the `system-ui` typography tokens. Do not introduce custom webfonts.

- Use `h1`, `h2`, and `h3` for page titles, section headings, and compact card headings.
- Use `body` for normal admin copy, labels, tables, and form text.
- Use `help` for secondary explanations, safe-default notes, and redaction notices.
- Use `badge` for compact status labels.

## Layout

Use the layout tokens to keep OBE pages consistent inside WordPress admin:

- `page-default` for dashboard, detail, and review pages.
- `page-form` for settings and mapping pages.
- `page-table` for Library, Activity, Addons, and workflow list pages.
- `section-gap` between major regions.
- `card-gap` between cards.
- `card-padding` for major cards.
- `form-row-gap` between form rows.

Use cards for major sections so workflow state, safety status, provider setup, Library previews, approvals, and addon detail panels remain scannable.

## Components

Use the `components` map for admin implementation defaults:

- Cards use the `card` component token and should include a visible border from the `border` color token.
- Primary buttons are for the next safe action only.
- Secondary buttons are for alternate, non-destructive actions.
- Small buttons are for compact table rows or secondary inline actions.
- Inputs and textareas use fixed WordPress-native widths unless a documented screen requires a narrower control.
- Badges communicate workflow, approval, write, addon, and safety states.

All dangerous or write actions must be visually explicit, require user intent, and use labels that describe the bounded outcome.

## Do's and Don'ts

### Do

- Use cards for major sections.
- Use status badges for state.
- Use clear action labels like Run dry-run, Save to Library, Review, Approve, and Write draft.
- Keep raw payloads out of list tables.
- Integrate addons inside the OBE admin experience.
- Store generated output in Library before any WordPress write.
- Make dry-run, approval, and write status visible before action buttons.

### Don't

- Do not use Auto publish, Run all, Turbo execute, Fix everything, or Write production now labels.
- Do not create separate top-level addon menus.
- Do not invent custom colors beyond the tokens.
- Do not expose raw API keys, private payloads, or private project labels.
- Do not hide destructive behavior behind generic labels.
- Do not make a dangerous action the default primary action.
- Do not add build tooling or runtime dependencies to consume this file.

## Future linting

`DESIGN.md` can be linted later with the design.md CLI when the project explicitly adds design-token tooling. Do not add that tooling until a future task requests it.
