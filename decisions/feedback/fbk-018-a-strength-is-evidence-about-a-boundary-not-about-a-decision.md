---
id: D-FBK-018
date: 2026-08-02
status: open
---

# D-FBK-018 — A strength is evidence about a boundary, not about a decision

**A feedback that reports no gap is not a confirmation; what it carries is where
a boundary runs, read against the costs other feedback report at the same one.**

The ladder has no rung for a report of what worked. Every step names something
missing, misplaced or misworded, so the one question has to be asked from the
other side: what is the strength evidence of?

## Evidence

- Three feedback from one debrief, five seconds apart, same directory
  (`/home/benji/projects/bootstrap_package`) and same model. `2026-07-31-174529`
  reports `typo3_changelog_lookup` as what made the review's first finding
  provable; `2026-07-31-174524` reports the same tool as unable to say whether
  the v14 Page module renders a backend layout without column identifiers; and
  `2026-07-31-174526` reports that no lookup says whether a
  `contentRenderingTemplates` registration is still consumed.
- The strength reproduces. Re-run on 2026-08-02 through `bin/typo3-cms-mcp` from
  that directory: `ext_tables.php` reaches *14.3 Deprecation: ext_tables.php in
  extensions* (#109438), `UpgradeWizard` reaches the 14.0 deprecation of the
  moved interfaces (#106947), `addPiFlexFormValue` reaches its 14.0 deprecation
  (#107047), and all three `.rst` files are in `.checkouts/main`.
- Two of its claims are looser than the answer. `typo3_project_scope` classifies
  six of ten declared commands as `check` or `change` and three as `unknown` —
  the phpunit suites, which the answer's own prose says it will not classify —
  rather than "every repo command"; and the platform reality it credits to that
  tool comes from `typo3_extension_scope`, whose footer reports that the
  installation was not asked because the host runs PHP 8.3.23 against a
  `>= 8.4.0` requirement.
- Nothing here can say which `base.md` that session read. Both installed copies
  of the skill in that repository were rewritten after the report — `.claude/`
  at 18:01 and `.agents/` at 20:04 against a 17:45 feedback — and the step 5
  deprecation sweep had landed 81 minutes before it.

## Decided

- The feedback is closed by this commit. There is nothing to queue: keeping
  something is not work, and the two costs it points at are on the board with
  cards of their own. [`D-FBK-017`](fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)
  makes "nothing to do" the close answer rather than a special case.
- A strength does not confirm a decision. It is a session's account of its own
  run, which is what
  [judging.md](../../documentation/feedback/judging.md) already refuses to
  assess in the other direction — the session was there and the reader was not.
  `D-SKL-001` is confirmed by recorded runs with timings in them, and a
  self-report cannot be read against its **Wrong if** the same way. Nothing was
  added to it here.
- What the strength is evidence for is where the corpus stops. It and the two
  costs are one boundary from both sides: the changelog answers what **changed**
  at a version, and both costs asked what still **holds** at one. A change
  carries an issue number and a state does not, which is why the same tool is
  precise in one direction and silent in the other.
- The gap is named and not the fix. What fills the state half belongs to the two
  cost cards, which are in hand elsewhere; naming their answer from here would
  be the copy-down that judging.md warns produces a guess with a reading's
  authority.

## Assumed

- That the three came from one session. They share a directory, a model and five
  seconds, and nothing in a feedback records a session.
- That a strength is worth a judging run at all. This one cost a re-run and
  bought the boundary above, which none of the three files states on its own —
  but the cheaper alternative, archiving it unread, would have looked identical
  from outside.

## Wrong if

- A positive feedback turns out to carry a lever nothing else does — praise that
  names what the session did instead. The ladder would then apply after all, and
  reading a strength as boundary-evidence only would have skipped it.
- The two cost cards are judged and land somewhere other than the change/state
  boundary. The pairing above would then be a reading of three files rather than
  a property of the corpus.
- Strengths accumulate unread, because closing one leaves nothing anybody can
  point at afterwards. This entry and its commit are the whole record; if
  neither is cited again, the run was a cost with no return.

## Since then

The first **Wrong if** fired on the next strength judged, `feedback/2026-07-31-192945`.
Read as boundary-evidence only it says the conformance skill's order is a good
one, which nothing here doubted. What it actually carries is a lever, and the
lever is the praise itself.

The strength recites the order it followed, and the recitation is this server's
order with one step gone. That can be checked against a file, which is what the
self-report this entry refused to assess could not offer. A recitation is an
artifact; an account of a run is not. So the rule established here holds with
one addition. Where a strength quotes something this repository owns, the
quotation is evidence about the file rather than about the session, and it is
read before the boundary is.

The judgement is on [`D-SKL-001`](../task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md),
whose **Wrong if** is where the omission lands. The rest of this entry stands.
The run that judged it established nothing about TYPO3 and named the gap rather
than the fix, and it left the feedback open behind an answer it may not give.
