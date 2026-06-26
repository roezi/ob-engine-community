# Public/Private Boundary

OB Engine Community is the public, GPL-compatible base of Optimized Builder Engine. It must remain clean, reusable, and safe to publish.

## Allowed in the public repository

- GPL-compatible community core documentation and future code
- generic WordPress workflow contracts
- generic field registry concepts
- safe addon interfaces
- BYOK provider configuration patterns
- examples using fake data only
- public roadmap, release plan, and decisions

## Not allowed in the public repository

- private OBE prototype code unless intentionally cleaned and relicensed
- pro-only feature implementations
- customer records, exports, logs, screenshots, or credentials
- Destinasindo-specific field maps, schemas, prompts, workflows, or business rules
- production endpoints or environment details
- secrets, tokens, cookies, API keys, or private certificates
- hidden autopublish flows

## Pro/private separation

Pro and private packages may extend the community base with premium workflows, hosted services, or client-specific automation. Those packages must use public contracts where possible and must not require the community repository to contain private logic.

## Example handling rules

- Use neutral sample field names such as `seo_title` or `review_status`.
- Use fake domains such as `example.com`.
- Use placeholder keys such as `sk-...abcd`.
- Describe private capabilities as extension points, not as bundled implementation.
