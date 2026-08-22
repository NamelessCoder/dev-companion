---
id: D-FBK-019
title: A secret pasted into a feedback is taken out on the way in
date: 2026-08-02
status: open
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

### The wording, 2026-08-02

The wording is in. `observation` and `query` in `FeedbackRecord::inputSchema()`
now say what a finding needs — the path a value was read at, the shape of what
came back, where it came from — and that the value is not part of it where the
installation keeps it secret, naming a key, a password, a token and the
credentials in a connection string. Both fields, because `query` is described as
the arguments that produced the result, and the second bullet of **Wrong if**
above expects a value to move there. On the wording alone `R-FBK-011` was
`not guarded`: the telling exists, and nothing reads either field or the corpus.
What moved it to `held` is the guard below, which landed the same day.

### The guard, 2026-08-02

**The guard was written on 2026-08-02, before the first "Wrong if" fired**, in
the commit that carries this paragraph: `src/Feedback/Redaction.php` takes a
value that looks like a credential out of `observation`, `query` and
`suggestion` before any of them is written, leaves `[redacted: …]` naming the
shape where it stood, and `typo3_feedback_record` says in its answer what it
took and from which field.

Not waiting is the whole of what changed, and the reason is in **Assumed**
above. What this entry doubted is that a sentence in a field description reaches
a session at call time, with `D-AUD-003` recording the opposite one channel
over. The session that pasted the key was in the middle of proving that the tool
returns the live runtime value, so a field description asking it not to would
have stood against what it was doing at that moment. The trade is not symmetric
either: waiting for the second occurrence means waiting for a second live
credential in a repository that is pushed, against a feedback that reads a
little vaguer. Both are still worth having — the wording is queued as its own
step, and what the guard cannot see is exactly what the wording is left to
carry, which
[`R-FBK-011`](../../requirements/feedback/fbk-011-a-recorded-feedback-carries-no-secret-out-of-the-installation.md)
now names on both sides.

**What the *rest* of this entry decided still stands, and one line of it was
wrong.** The recorded file was not rewritten, and this guard does not reach it:
it reads what a session hands the channel, not what is already committed. But
the key was pasted **once**, in the observation — read back in `77d242b`, the
commit that recorded the feedback, and unchanged in the archive since. The
evidence bullet above said twice, once in the observation and once in the query,
and `R-FBK-011` and the todo that carried this step both repeated it. Nothing
about the work turns on it — the value is taken out of every field a feedback is
written from, and the reason for reading `query` is that a session would put it
there, not that this one did — but the sentence was checked and it was false,
which is the half of it worth writing down.

**What the corpus said about the thresholds.** Every rule was run over all 207
recorded feedback before it was written down, because the cost of this guard is
a rule that redacts what feedback is made of. A hexadecimal run of 64 characters
takes the key and leaves every git revision, which is 7 to 40. The same
threshold in base64 leaves `ImportSiteConfigurationsOnPackageInitialization`,
`RemovedPublicMethodsRelatedToImageGeneration` and six more changelog and class
names, which a threshold of 40 takes. `password` and `token` are all over the
corpus as prose, so a name only counts where a value is assigned to it: a colon
alone matched `install:password:set`, a console command quoted in a feedback
about setting an installation up. Over the whole corpus the four rules together
take out one value, the key this was written for, and
`FeedbackTest::theRulesTakeNothingOutOfTheCorpusButTheKeyTheyWereWrittenFor` is
that measurement kept as a check.

Both halves were worked at once, in two sessions that could not see each other,
and both read the archived file and found the same false sentence — which is why
the correction is recorded once above and stands over both accounts.
