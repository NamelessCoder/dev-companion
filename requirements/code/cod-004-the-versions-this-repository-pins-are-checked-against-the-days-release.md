---
id: R-COD-004
title: "The versions this repository pins are checked against the day's release"
status: held
judged: 2026-08-29
---

# R-COD-004 — The versions this repository pins are checked against the day's release

**A version this repository pins — node, a GitHub Action, a Composer library, a
DDEV configuration — is checked against the current release on the day it is
touched.**

Nothing here names the version that has to be used. The current release moves,
so a target written into a rule or a check is right for a quarter and wrong
afterwards with nothing failing on it. What decides is what the session reads on
the day.

A pin behind the current release is raised, or the reason it stays is written
beside it. Where a runtime this package declares rules the newest release out,
the pin takes the newest version that declaration does allow — which is the
reason and the target at once, never a licence to leave it where it was. The
other reasons are a runtime this repository still supports, a library whose next
line needs a change, a version the render or the suite is known to break on.
Where that reason is not established, the raise is asked for rather than taken,
because a toolchain bump is a change the maintainer runs the risk of.

The same demand on what this server tells a caller is `R-ANS-037`, and on what a
published skill does with a project it reads is `R-SKL-029`.

## From

The maintainer's instruction of 2026-08-29, given while `documentation.yml`
pinned `actions/checkout@v4`, `actions/setup-node@v4` and node 24, and `ci.yml`
`actions/checkout@v4` beside a PHP matrix: each of those was current when it was
written — `D-DOC-019` recorded the node line as the active LTS — and nothing
here said when any of them is read again.

## Held by

- It is **not guarded**, and no test can hold it: an assertion naming the
  version that must be pinned is the fixed target this forbids, and one reading
  the network would fail on the day a release lands rather than on a defect.
- What stands in for a guard is the rule in `AGENTS.md` and how few files carry
  a pin at all — `.github/workflows/`, `composer.json`, and a
  `.ddev/config.yaml` where a checkout has one.
