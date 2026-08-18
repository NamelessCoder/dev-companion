---
id: D-SKL-053
date: 2026-08-18
status: open
---

# D-SKL-053 — An absence in the extension answer names the skill that owns it

**`typo3_extension_describe` names the skill that owns each artifact it reports
absent.**

A session read `manual: null`, `readme: null` and `tests: []` twice, wrote three
README files by hand, shipped no test at all and handed the package over
unaudited. The three skills that own those absences were named in the closing
sentence of the skill it was following, and it says it read that sentence.

## Evidence

- `feedback/2026-08-17-213027`. A v14 demo site built as a sitepackage plus a
  distribution extension on 14.3.6, `/home/benji/projects/site-demo`,
  `claude-opus-5`. It followed `typo3-content-element-development` to
  completion, quotes that skill's closing sentence, and reports that none of
  `typo3-extension-testing`, `typo3-extension-documentation` and
  `typo3-extension-conformance` activated. The user then reviewed by hand and
  listed ten defects, seven of them inside the conformance skill's own stated
  scope.
- **The sentence is unchanged.** Read in this checkout on 2026-08-18, the last
  paragraph of `skills/typo3-content-element-development/SKILL.md` carries the
  quote word for word. It is an imperative — "Activate" — it names three
  successors at once, it sits after the commit section, and it names no moment
  at which any of the three happens.
- **What the answer carries today.** `ExtensionDescribe::answer()` renders one
  `Ships:` line on every hit,
  `manual none, readme none, tests none, language files none` where an extension
  has none of them, and it names no skill. Below `src/Tool/` only `GerritLookup`
  and `FeedbackRecord` name one at all.
- **No extension-side crossing is written at its moment.** Read across `skills/`
  on 2026-08-18: every crossing out of the seven extension workflows stands in
  the closing ownership paragraph. Three of them carry a trigger clause —
  `typo3-backend-module-development`'s "before editing documentation" and
  "before changing test infrastructure", `typo3-extension-cleanup`'s numbered
  step 2 — and none is a step at the point the crossing happens, which is the
  form the two confirmed core crossings took.
- **That is the reading this report falsifies.** `D-SKL-022`'s pass of
  2026-08-09 counted `Activate typo3-extension-documentation` and
  `Activate typo3-extension-conformance` as already an act and left them out of
  the work; `R-SKL-018` was applied to three core crossings and to none of
  these. A session holding exactly that imperative and crossing nothing says the
  imperative is not the property. What the two crossings that fired carry beside
  it is the moment.
- **The answering side is decided prior art.** `D-SKL-038` puts the workflow a
  caller has begun into the answer of the tool it does call, at a moment a
  description cannot have, and `D-ANS-061` is why that channel rather than the
  one nobody invokes. `SkillTest::everySkillNamedByAToolIsPublished` already
  holds every name below `src/Tool/` to what the installer publishes.
- **The corpus is one build.** `bin/cli feedback:list` on 2026-08-18 reports 8
  open, all from `/home/benji/projects/site-demo` and all from that session.
  Three siblings of the same debrief are already judged: `D-SKL-050` names this
  card as what failed to deliver `typo3-development-installation`, `D-KNW-087`
  is the same mechanism one layer down in a hint's closing neighbour, and
  `D-SKL-049` is the terminal gate — proposed, and waiting on the maintainer in
  `todo/waiting/2026-08-17-212218`.
- **The testing skill has been missed before, from another project.**
  `feedback/archive/2026-08-01-003533`, `/home/benji/projects/site-new`, another
  model: rendered output verified by curling HTML, no browser test, an existing
  Playwright harness unused. That session had no skill active to cross out of,
  so it is a second arrival at the absence and not at this sentence.

## Decided

- **Step 2 for the answer and step 4 for the crossing, and both are queued.** A
  rendered answer and a published skill body are contracts, and
  `documentation/records/judging.rst` puts either on the todo side of the spot.
- **The three absences name their owner: `typo3-extension-documentation` where
  `manual` or `readme` is null, `typo3-extension-testing` where `tests` is
  empty.** Only where the artifact is absent, so an extension that ships all
  three reads as it does today, and the name arrives on the object the caller is
  already looking at rather than in a sentence it is leaving.
- **Not conformance.** No field of this answer reports that nobody audited the
  package, so there is no absence for it to hang on. That crossing stays with
  the skills half.
- **Not a sweep of the other tools.** `D-SKL-038`'s bullet stands: this is a
  second named moment carrying its own report, and the row nobody asked for is
  still what a route invented for symmetry costs.
- **The crossing half is `R-SKL-018` applied where it never was, and bounded to
  one crossing at its own moment.** What may not be written from here is a list
  of everything the workflow still owes: that is `D-SKL-049`'s gate, which is
  proposed rather than decided, and a card writing one now would answer the
  maintainer's question by building it.
- **Nothing about the descriptions or the routing.** The listing arrived and one
  of these skills was active when the crossing failed, so this is a crossing and
  not a selection — `D-SKL-033` weighed the wording and this adds no session to
  that side.
- **Priority `normal` on both cards.** The cost is measured and it is large — no
  test written, three manuals by hand, an unaudited delivery — and it is one
  session, which is what keeps it off `high`, the same reading `D-KNW-087` made
  of the sibling report.
- **The feedback stays open behind both cards**, and whichever lands second
  archives it.

## Assumed

- That a session given a skill's name beside an absence loads it. Nothing here
  measures that; it is `D-SKL-038`'s first **Assumed** unchanged, and the tail
  it was written for has still not been read by anybody.
- That the moment is the property the imperative lacks. Two core crossings carry
  both and fired, this one carries the imperative alone and did not — one
  session, one model, one task.
- That a later report says which channel carried it. Two levers land against one
  report and nothing else separates them; what the corpus does have is sessions
  quoting the sentence or the field they acted on, this one included.

## Wrong if

- A session reports reading `tests none` with `typo3-extension-testing` beside
  it and writing no test. Then the answering side is not the lever either, and
  what is left is the gate `D-SKL-049` is waiting on.
- A session that asked a narrow question of `typo3_extension_describe` reports
  the names as noise on an answer about registrations. Then the condition has to
  be narrower than the artifact being absent, which is `D-SKL-038`'s second
  **Wrong if** arriving here.
- The extension crossings are rewritten at their moments and a session crosses
  none of them anyway. Then prose does not hold a crossing at all, and
  `D-SKL-022`'s third **Wrong if** — the lever is in the tools — is what is
  left.
- A session activates one of the three owners off the closing paragraph as it
  stands. Then this run read one session's momentum as a property of the
  sentence.

## Covered by

- `SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor`

**Since then**, the crossing half is built. The three successors
`typo3-content-element-development` named at once stand as three steps at three
moments: `typo3-extension-testing` where the layer the element needs has no
harness, `typo3-extension-documentation` where the verified element has to be
written up, `typo3-extension-conformance` where the request turns from this
element to the package. The last of those is the shape whose trigger is
something the reader says, so it names the sentence that fires it and the
question about the element that does not — `R-SKL-018`'s second half.

The other six extension skills were read in the same pass, and each got the
crossing its own workflow reaches. `typo3-backend-module-development` had two
already standing at a moment and both are now instructions — the installation
before the module is opened, the documentation once implementation is verified.
`typo3-extension-cleanup` invokes the audit at step 2 and again at step 12,
`typo3-extension-conformance` invokes the owner of a finding when the fixes are
asked for, `typo3-extension-documentation` invokes the code's owner where a page
would have to claim what the code does not do, `typo3-extension-testing` invokes
it where a failing assertion is that code's defect, and
`typo3-extension-upgrade` invokes it where the change in front of the session is
not on the work list. Where the successor is decided per case none of them can
name one skill, which is what the test reads for those five instead of a name.

Every ownership paragraph stayed, and none of them carries an instruction any
more: the test reads the last paragraph of every published skill for one, which
is the position half the imperative alone did not hold. What was not written is
a list of everything a workflow still owes — `D-SKL-049`'s gate is still waiting
on the maintainer, and each crossing here stands at its own moment instead.

The answering half landed first and in a commit of its own:
`typo3_extension_describe` names `typo3-extension-documentation` where it
reports no manual or no README and `typo3-extension-testing` where it reports no
test layer. Two levers now stand against one report, which is what the third
**Assumed** says a later session has to separate.

**A second session then read a closing crossing and did not act on it.**
`feedback/2026-08-18-074245`, `/home/benji/projects/blog`, a DDEV installation
on 14.3.6, `claude-opus-5[1m]`. `typo3-development-installation` was active and
carried the diagnosis; the session states it read "Where this stops" when the
skill loaded, at which point it held a 404 and no test. Forty minutes and
several user turns later it extended `Tests/Functional/` and built a second core
installation, and invoked neither `typo3-extension-testing` nor
`typo3-extension-upgrade`. That is the third **Assumed** arriving a second time
— another project, another workflow, another task — and it is the position and
not the imperative that is left: the paragraph names the successor, describes
the crossing in both directions, and says to activate it.

**What the pass left standing.** It read the seven extension workflows, and
`typo3-development-installation` is not one of them. Its crossing into
`typo3-extension-testing` is the last handover in `skills/` still written in the
paragraph the workflow is being left in.

**Both halves of the guard pass on it.** The `$crossings` map names
`typo3-extension-conformance` for that skill and not `typo3-extension-testing`,
so the imperative half never asks. The position half reads the last paragraph of
every skill for `nvoke`, and this one says "activate it" — the verb this entry's
own evidence was written to falsify. A crossing is held where somebody listed it
and where its author happened to pick one of two words.

The report is about this server rather than an older one: it was recorded at
07:42 UTC, an hour after the pass, and quotes the paragraph as it still reads.
Judged step 4 and queued at `normal` on the card that serves it, bounded to that
one crossing — `typo3-extension-upgrade` staying shut in the same session is a
description that reads from the maintainer's intent, and
`feedback/2026-08-18-080630` carries it on a card of its own.
