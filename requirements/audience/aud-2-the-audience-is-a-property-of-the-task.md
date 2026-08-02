---
id: R-AUD-2
status: held
restsOn: [D-SCO-7]
---

# R-AUD-2 — The audience is a property of the task

**The audience is a property of the task, not of the directory.**

Extensions are routinely developed inside a site installation, a site package
is an extension, and a core checkout can be the place someone debugs their
site. Signals are combined, and where they disagree the answer says the
audience is uncertain instead of picking one silently.

The unit of that decision is the path, because a call is not one piece of work:
`Scope::of()` answers `core`, `project`, `extension` or `uncertain` for one
path, and the tools that take a `paths` array answer per path. `uncertain` is
the case above — nothing in the call placed the work, and an answer that picks
the core there is right half the time and says so never.

**From:** two paths of different audience in one session, where the first one
decided for both ([`D-SCO-7`](../../decisions/scope/sco-7-the-signals-are-combined-per-call-and-a-call-is-not-a-path.md),
2026-08-01).

**Held by:** `ScopeTest::twoPathsOfDifferentAudienceInOneCallStayApart`,
`ScopeTest::whereNothingPlacesTheWorkTheAnswerSaysSoRatherThanAssumingTheCore`,
`ScopeTest::whatTheCoreKeepsInBuildIsOnlyTheCoresWhereTheRepositoryCouldBeTheCore`
