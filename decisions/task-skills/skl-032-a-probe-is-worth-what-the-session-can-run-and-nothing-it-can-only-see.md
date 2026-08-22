---
id: D-SKL-032
title: A probe is worth what the session can run, and nothing it can only see
date: 2026-08-10
status: open
---

# D-SKL-032 — A probe is worth what the session can run, and nothing it can only see

**The scratch-probe permission carried the review wherever a suite could be run
against the question, and the same session asserted for hours wherever the
answer had to be looked at.**

One debrief reported both, and they are one boundary rather than a success and
four failures.

## Evidence

- `feedback/2026-08-10-101751` is the strength. The session added throwaway
  tests below `Build/Sources/TypeScript/backend/tests/`, ran the JavaScript unit
  suite against them, read what they printed and put the tree back. Two reasoned
  findings became measured ones, and — the part worth more — one of its own
  predictions was refuted: it had called the `@starting-style` entry animation
  dead code, and the probe measured opacity 0.135 one frame after the class
  change.
- The dropped-candidate rule is what made that refutation land as a written
  disposition rather than a quiet deletion. Three of the five candidates it
  dropped were disproved by a lookup rather than by taste, which is `D-SKL-007`
  read from the other end.
- The costs in the same session are all on the other side of the same line.
  Whether a CSS feature is inside the browser baseline
  ([`D-KNW-066`](../knowledge/knw-066-the-browser-baseline-is-a-release-day-and-core-usage-is-not-evidence-of-it.md)),
  whether the component sits where it should while scrolling
  ([`D-KNW-068`](../knowledge/knw-068-looking-at-a-backend-change-is-a-suite-the-core-already-carries.md)),
  how to reach the installation that has the content
  ([`D-KNW-069`](../knowledge/knw-069-a-browser-in-a-container-reaches-a-ddev-site-on-the-routers-own-network.md))
  — none of them can be answered by running a suite and reading its output, and
  the session shipped three blind corrections into the first two before the
  developer stopped it.
- Where the session did have a command, it used it correctly with no further
  help: it held the Gerrit patch set's commit against `git rev-parse HEAD` to
  prove it was reviewing the current one, and read the reviewer comment on the
  previous patch set out of Forge rather than re-reporting a finding somebody
  had already made.

## Decided

- Nothing changes in the skill. The paragraph and the dropped-candidate section
  are what the session names, and a strength is evidence about a boundary rather
  than a decision to confirm
  ([`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)).
- The boundary is written down because the next editor of that paragraph cannot
  see it: what it grants is worth exactly as much as the commands the session
  can name, and it says "run a targeted suite" because that is the kind of
  measurement this server can point at. A permission to look at something is not
  the same sentence and does not follow from it.
- Where a suite cannot answer, what the session needs is a route rather than a
  permission, and the three entries above are that route. Whether the review
  skill should carry the looking step as well as the running one is a question
  for the run that has both — not a paragraph written from this reading.

## Assumed

- That the probe is what produced the two measured findings and the refutation.
  The session says so and nothing here re-ran the review.

## Wrong if

- A session with the route now written down still asserts a positional finding
  it could have looked at. Then the missing thing was the permission after all,
  and the skill is where it belongs.
