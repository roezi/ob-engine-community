# Versioning

## Current version

The plugin runtime version is currently `0.19.0`.

## Version rules

- Runtime feature PRs bump the minor version while OBE is in `0.x`.
- Patch/cleanup PRs bump the patch version only when they affect runtime or release packaging.
- Docs-only/project-planning PRs do not bump the plugin version.
- Never downgrade the plugin version.
- Do not bump the version just because a planning document changed.
- The next runtime target after `v0.19.0` is `v1.0` only after hardening and manual QC pass.
- `v1.0` is reserved for a stable community base after hardening.

## Examples

| Change type | Example |
| --- | --- |
| Feature runtime | `0.16.0` → `0.17.0` |
| Fix after `0.11.0` | `0.16.0` → `0.16.1` |
| Docs only | no plugin version change |
| Project board/docs only | no plugin version change |

## Mission notes

- Do not invent next version numbers; use this document and the project status docs.
- Runtime feature PRs must mention the target version in the PR body.
- Planning-only PRs should state that no plugin version bump is included.
