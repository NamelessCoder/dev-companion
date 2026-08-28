---
id: D-FBK-019
title: A secret pasted into a feedback is taken out on the way in
date: 2026-08-02
status: open
coveredBy:
  - FeedbackTest::aLongBase64ValueIsTakenOutAndAWordIsNot
  - FeedbackTest::aPasswordInADatabaseUrlGoesWithoutTheHostGoingWithIt
  - FeedbackTest::aValueGoesAndTheNameThatSaysWhatItWasStays
---

# D-FBK-019 — A secret pasted into a feedback is taken out on the way in

**`typo3_feedback_record` copies the observation it is handed into a tracked
file unchanged, and nothing tells the session that a value the installation
keeps secret may not go in there.**

The feedback judged on 2026-08-02 praised a tool and, in praising it, pasted the
live encryption key of the site it had just audited into this repository.

## Evidence

- What the feedback says.
  `feedback/2026-07-31-185900-after-the-audit-i-invoked-typo3-dev-companion.md`
  reports a success: `typo3_configuration_lookup` with path `SYS/encryptionKey`
  returned the effective runtime value, which turned an inferred audit finding
  into an established one. Its observation quotes the 96-character key verbatim
  — once, and this entry said twice until 2026-08-02, which **Since then** reads
  back.
- The behaviour it praises still stands. Re-run on 2026-08-02 against the server
  as it is now, over stdio with
  `TYPO3_DEV_COMPANION_ROOT=/home/benji/projects/site-new`: `SYS/encryptionKey`
  came back `found: true`, `answeredBy: installation`, a 96-character string;
  `SYS/trustedHostsPattern` came back `.*.*`. Nothing about the observation half
  is out of date.
- The suggestion half is already delivered. It asks for the tool to be
  advertised more prominently, and for the conformance skill in particular. That
  skill already names it — `skills/typo3-extension-conformance/SKILL.md` under
  the lookup that owns a surface's runtime facts, `skills/base.md` in "Two kinds
  of lookup", and `knowledge/server-scope.json` in the routing entry "Needing a
  configuration value as it really is at runtime". Step 2 of the ladder,
  delivery, is where this one stops.
- What actually cost that session the calls is a different report. The same
  audit, at the same minute, filed
  `feedback/2026-07-31-185900-during-an-audit-of-the-printworks-3d-site.md`: no
  tool was callable in its environment at all, and the binary was found only
  after the audit was written. That feedback is open and has its own card. The
  suggestion here names the cause the session could see from where it stood.
- The file is tracked and pushed. `git ls-files feedback/` counts 208 entries,
  this one among them, and the checkout has a GitHub remote.
- One occurrence, not a pattern. A search of `feedback/` for hexadecimal runs of
  40 characters or more returns two hits: this key, and a git revision quoted in
  an archived entry. That is why it is written down rather than treated as a
  standing leak.
- The tool invites it. `FeedbackRecord::inputSchema()` asks the session to be
  "specific enough to act on later" in `observation`, and describes `query` as
  the arguments that produced the result. Neither says anything about values
  read out of the installation, and there is no other field for a session to put
  its evidence in.

## Decided

- The feedback is archived. Both halves are answered: the observation by the
  re-run, the suggestion by finding the advertisement already in the skill that
  was active. Nothing it asks for is open, so the *close* answer applies and no
  todo serves it.
- The secret is queued as work of its own rather than closed on the spot. It
  changes a tool's declared schema and description, which a judging run does not
  improvise —
  [`R-FBK-011`](../../requirements/feedback/fbk-011-a-recorded-feedback-carries-no-secret-out-of-the-installation.md)
  says what must hold and a todo carries the next step.
- What the guard is stays open. Wording on the fields, a refusal in
  `Channel::record()`, a redaction pass, or the wording alone — a judging run
  has established the gap, not the fix.
- The recorded file was not rewritten. Editing the key out would leave every
  commit that carries it untouched and would alter a session's report, which the
  archive exists to keep. Rotating the key belongs to whoever owns that site,
  and it is not an action this repository takes on its own.

## Assumed

- That the session pasted the key because `observation` read to it as the place
  its evidence goes. Nothing asked it, and the session has ended.
- That one occurrence is the first rather than the first noticed. The search
  behind that was a hexadecimal-length grep, which finds a key and would walk
  past a password, a host name or a token that is shorter.
- That a sentence in a field description reaches a session that is writing
  feedback. `D-AUD-003` records the opposite for tool descriptions at
  initialize, and this field is read at call time rather than at startup, which
  is a different position and not a measured one.

## Wrong if

- A second recorded feedback carries a value out of an installation. Then saying
  it is not enough, and the channel has to refuse or redact rather than ask.
- A session works around the wording by moving the value into `query`, which is
  the other field it would read as the place its evidence goes.
- Feedback gets vaguer after the change — observations that name no value, no
  path and no version — because the guard was read as "paste nothing". Then it
  cost more than the leak it prevents.
- The suggestion half turns out to have been right after all: a later session
  reaches the conformance skill with every tool callable and still does not ask
  `typo3_configuration_lookup` about a configuration claim. Then this was step
  4, wording, and the skill names the tool without saying when it decides a
  finding.

## Since then

**The guard was written before the first Wrong if fired**, in the commit that
carries this paragraph, and not waiting is the whole of what changed. What this
entry doubted is that a field description reaches a session at call time, and
the session that pasted the key was in the middle of proving the tool returns
the live value — so a sentence asking it not to would have stood against what it
was doing. The trade is not symmetric either: waiting means waiting for a second
live credential in a repository that is pushed.

One line of the evidence was wrong: the key was pasted once rather than twice.
Every threshold was run over the whole corpus first, because the cost of this
guard is a rule that redacts what feedback is made of, and
`FeedbackTest::theRulesTakeNothingOutOfTheCorpusButTheKeyTheyWereWrittenFor`
keeps that measurement.
