---
id: D-KNW-066
date: 2026-08-10
status: open
---

# D-KNW-066 — The browser baseline is a release day, and core usage is not evidence of it

**`css-browser-target` states the policy as a release day and names what is not
evidence of it; it does not carry a feature-by-feature support table.**

A session reviewing a core patch asked the hint before choosing a technique and
got the policy alone. It then took the checkout as the answer, built on CSS
anchor positioning because the core already ships it, and shipped it. The
developer found it broken in Firefox.

## Evidence

- `feedback/2026-08-10-101627`. The session verified in the headless Chrome of
  the unitJavascript suite and in a Chromium-driven backend, both of which
  passed, and measured Firefox afterwards through Playwright: `CSS.supports`
  false for `anchor-name`, `position-anchor`, `anchor()` and `anchor-size()`.
  Cost was one wrong architecture decision carried through several rounds of
  rework, found by the developer rather than by the session.
- The precedent it read is real. `Build/Sources/Sass/component/_dropdown.scss`,
  `element/typo3-backend-workspace-selector.scss` and
  `element/typo3-formengine-element-datetime.scss` on `main` all use
  `anchor-name` and `position-anchor`, one of them `anchor-size(width)` as well.
  So the checkout says yes and the baseline says no, which is exactly the trap
  the hint has to name.
- `Build/.browserslistrc` is identical on `main`, `14.3`, `13.4` and `12.4` and
  was last touched in 2019. Nothing in `Build/package.json` or the Gruntfile
  configures it; `autoprefixer()` in the Gruntfile's postcss task picks it up
  from the build directory. It therefore decides prefixing and gates nothing — a
  feature no engine implements passes the build unchanged.
- The policy itself came from the maintainer of this repository during the
  judging run: the target is the browser versions that were current on the day
  the release in question appeared, per release line rather than per LTS. The
  hint said "on the day of the corresponding TYPO3 LTS release" and "of that
  target release year", both of which are the same rule read coarsely.

## Decided

- The hint carries the sharpened policy, the three engines with Gecko named as
  the one that usually decides, that existing core usage is not evidence, and
  that a Chrome-only verification is not evidence either. That is what would
  have stopped this session, and it holds for every feature rather than for the
  six the feedback listed.
- No per-feature table. Which of anchor positioning, `@starting-style`,
  `popover`, `:has`, container queries and `light-dark()` is inside a given
  release day is a fact this repository cannot verify from `.checkouts/` — it
  would be copied from support tables nobody here re-reads, and it turns without
  anything failing. The prose rule against a snapshot that reads as a fact long
  after it stopped being one is
  `HintsTest::noHintStatesSomethingThatOnlyHoldsOnOneBranch`, and a table of six
  is that rule's worst case.
- No tool either. A `lookup` answering "is feature X inside the v14 baseline"
  needs a support corpus this server does not have and a release-date axis it
  does not carry, so it would report `unavailable` for most of what it is asked
  — the case
  [`D-FBK-027`](../feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)
  says buys the caller nothing.
- `Build/.browserslistrc` is named in the hint as what it is. A session that
  goes looking for the target finds that file, and it reads as a declaration of
  support while being a prefixing configuration from before any covered branch.

## Assumed

- That Gecko is the engine that decides often enough for the sentence to be
  worth its line. It was here, and it was for `:has`, container queries and the
  Popover API before it. WebKit takes that place occasionally, which is why the
  hint says every engine has to ship it rather than naming one to check.
- That the reported Firefox measurement is correct. It was made in the session's
  own Playwright harness and nothing here re-ran it; the core call sites above
  are what makes it plausible rather than what confirms it.

## Wrong if

- A session reports having read this hint and still shipping a feature outside
  the baseline. Then the rule is not what was missing and the concrete table is,
  whatever it costs to keep.
- Somebody establishes that the core's declared target is a file rather than a
  date — a maintained browserslist, a documented support statement — and that
  file answers the question directly. Then the hint should name it as the source
  instead of teaching a procedure around it.

## Since then

On 2026-08-11 the hint was reported a second time, from the other side.
`feedback/2026-08-10-182543` reviewed Gerrit change 95163 in
`/home/benji/projects/typo3-cms` and reports it as what stopped
`@container scroll-state(stuck: top)` — a Blink-only feature — going into a core
patch under review with a rationale attached. The first **Wrong if** describes
that event with the sign reversed, and it did not fire: a second session, a
second feature, and the policy alone was enough.

The quotations are checked before the boundary is, which is what `D-FBK-018`
asks of a strength. All three reproduce verbatim in
`knowledge/hints/backend-css.json`, and the report says which of them did what:
the engines sentence turned the recommendation into a rejection, the core-usage
sentence pre-empted the argument the session was one step from building out of
the patch's own `animation-timeline: scroll(root)`, and the `.browserslistrc`
sentence stopped it quoting a file it had already grepped. That is this entry's
**Decided** read back by somebody who did not know it existed. The entry stays
`open` all the same: an account of a run is not a recorded one, and what would
confirm it is a run in `scenarios/runs/`.

The first **Wrong if** of `D-FBK-018` fires instead, and the lever is the second
half of the suggestion — press this hint the way the icon and label lookups are
pressed at initialize, because the moment it is needed is the moment a feature
is about to be written down, which is not a moment an agent thinks to ask about
browser support. Both reported sessions reached the hint by asking for it by id.
Neither would have reached it otherwise, and that is measured rather than
inferred: `bin/cli hints:probe` on the session's own task text — *backend
docheader sticky CSS box-shadow scroll-driven animation* — returned
`css-shadow-layering`, `css-motion-transitions`, `css-z-index-layering`,
`css-web-components` and `css-source-build-boundaries`, and not this hint. Nor
did *container query for a stuck element* or *scroll-driven animation in backend
Sass*. What reached it was *modern CSS feature in a backend patch*, which is the
policy named in the policy's own words.

**A policy hint has to be reachable by the vocabulary of the thing it governs,
and this one was reachable by its own.** `appliesTo` carried
`anchor positioning`, `anchor-name`, `position-anchor` and `popover` — the
features of the incident above, added by the run that wrote this entry. So each
incident teaches the hint the words of the feature that already got through, and
the next feature is unreachable again.

Worse, the query that names a feature reached a second copy of the policy in the
coarse wording this entry sharpened out of the first. `css-container-queries`
said container queries "may be introduced before an LTS release when they are
expected to be part of the LTS browser baseline" — per LTS rather than per
release line, and reading as permission. It is the one hint *container query for
a stuck element* returned, and the session's candidate was a container query.

Step 2, delivery, with a step 4 underneath it. Both are `knowledge/`, no
contract moves and nothing about TYPO3 was looked up, so both landed here: the
duplicate is replaced by a sentence sending the decision to
`css-browser-target`, and the vocabulary gains the feature words a session types
when it is about to adopt one. Measured on both sides — all three failing
queries now lead with the hint, *responsive backend component that reacts to its
container width* still ranks `css-container-queries` first, and
`bin/cli hints:coverage` is byte-identical before and after, so no hint became
unreachable.
`HintsTest::aQueryNamingAModernCssFeatureReachesTheBrowserTargetAndNotASecondPolicy`
holds it.

That list is vocabulary and not the table this entry refused, which is the
distinction that makes it affordable. A feature's name never stops being its
name, while whether the feature is inside a given release day turns without
anything failing. What the list costs is that it grows one incident at a time,
and it is wrong if a third feature gets through for want of its own word — then
the reachability belongs to the matcher rather than to the data.

The keep-request is answered in the only form this repository has for one. The
three sentences rested on nobody rewriting the hint: no test named any of them,
and a rewrite that kept the policy and dropped the two refusals would have left
a hint that reads complete and stops neither session.
`HintsTest::theBrowserTargetKeepsTheArgumentsItRefusesAsWellAsThePolicy` names
them.

The press itself is refused. This hint is `scope: core` and the `instructions`
are read by every caller, including the site developers and extension authors it
does not hold for. The three lookups pressed there are identifier lookups —
component, icon, label — where a guess is wrong at runtime and one call settles
it; this one answers a procedure the caller then has to carry out against an
engine nothing here can reach, which is what the session reported doing when it
told the user it could not verify Gecko support. Routing the feature's own words
to the hint costs the sessions that are about to write one, and nothing to the
rest.

The feedback is archived by this commit and nothing is queued. The half of the
corpus it neighbours is elsewhere: `feedback/2026-08-10-182451`, from the same
session, asks that `availableHints` be ranked by the domains a query
established, and its card waits on a question that changes `src/`.
