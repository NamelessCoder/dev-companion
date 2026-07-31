---
id: R-SKL-2
status: held
---

# R-SKL-2 — A testing task verifies the harness before relying on it

**A project or extension testing task verifies the harness for the behavior's
required layer before relying on it.**

Missing or broken infrastructure is established or repaired when changes are in
scope, then the requested coverage is added or extended without replacing
working tests and commands. Unit and functional harnesses stay with the
extension; browser harnesses stay with the runnable project. Every newly
established layer has a meaningful local proof before CI calls the same
command, and review-only work reports setup defects without changing them.

**From:** `EXT-05`, `SITE-06`, `SKILL-05`, `SKILL-06`; and a request for one
testing skill that can add or extend PHPUnit and Playwright coverage while
checking and repairing its setup (2026-07-30).

**Held by:** `SkillTest`, `InstallerTest`
