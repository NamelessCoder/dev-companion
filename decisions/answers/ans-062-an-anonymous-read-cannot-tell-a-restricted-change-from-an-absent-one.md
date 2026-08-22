---
id: D-ANS-062
date: 2026-08-07
status: open
---

# D-ANS-062 — An anonymous read cannot tell a restricted change from an absent one

**`typo3_gerrit_lookup` reads review.typo3.org without credentials, so a private
or work-in-progress change comes back as `empty`, and `empty` is the word a
caller acts on as "no patch exists".**

## Evidence

- `feedback/2026-08-07-132416` is a review session that made the first finding
  of its review out of this. Lookup by the Change-Id in the commit it was
  reviewing returned `{"status":"empty","changes":[]}`; by issue `109572`,
  `empty`; by change number `95162`,
  `{"status":"unavailable","cause":"source-not-answering"}`.
- The change existed. `typo3_forge_lookup` on the same issue, in the same
  session, returned a journal entry from Gerrit Code Review naming patch set 1
  and the review URL ending in `95162`. The session read the two answers against
  each other and concluded that 95162 was somebody else's competing change.
- What it recommended on that reading was to coordinate with the other author
  before pushing, ranked under "what blocks the patch from being submitted at
  all". The user corrected it: the change is private and is the commit that was
  checked out.
- `Gerrit::` returns `empty` where the parsed change list is empty and
  `unavailable` with `source-not-answering` where the request itself fails, so
  one cause produces two statuses depending on whether the query was a search or
  a direct read. The feedback names that split without seeing the source.
- The tool description frames the call as "Find out whether a TYPO3 core patch
  already exists", which is the reading that fails here.
- It compounds with `typo3-core-patch-review`, which instructs that an answer of
  nothing is a result and is to be reported as one. That instruction is only
  safe while `empty` means absent.

## Decided

- `empty` is an overstatement wherever the query named a concrete Change-Id or
  change number. A search over commit messages that matches nothing is a real
  absence; a direct read that returns nothing is a permission effect or an
  absence and the reader cannot tell which.
- The answer says so rather than the tool description saying so. The description
  is read when a client is installed; the answer is read when the verdict is
  written, and `R-ANS-024` already holds that a field answered empty is one
  nothing could fill.
- The evidence the server already holds is worth spending: where
  `typo3_forge_lookup` would surface a review URL for the same issue, an empty
  Gerrit answer for that number is positive evidence of a restricted change
  rather than a missing one. That is the feedback's own observation and it needs
  no credentials.
- This is not a case for authenticating. Reading without credentials is what
  keeps the tool free of a secret to hold, and `D-FBK-042` puts the read-only
  boundary at the installation. What changes is what the answer claims.

## Assumed

- The two statuses have one cause here. `unavailable` for the direct read is
  consistent with Gerrit refusing an anonymous read of a restricted change, and
  no second case has been observed — but a review server that is simply down
  produces the same word, and nothing in this report separates them.
- A caller acts on the status rather than on the description. Three misreadings
  in one session support it and one session is one reading.

## Wrong if

- A change that is genuinely absent starts being reported as possibly
  restricted, and a session hesitates over a patch nobody has pushed — which
  would say the hedge costs more than the wrong verdict it prevents.
- The Forge cross-check turns out to fire on merged changes too, making the
  signal say "restricted" about something anybody can read.
- Gerrit is found to answer differently for a private change than for one that
  does not exist, which would mean the two were separable all along.

What that leaves standing is the first half, and it is confirmed rather than
weakened: 95162 is a change the user says exists, and an anonymous read of it is
`[]` — indistinguishable from a change nobody pushed, exactly as this entry
says. So there is no status split to repair, and the whole of the fix is what
the answer claims. `R-ANS-027` is held by
`GerritTest::anEmptyAnswerForANamedChangeSaysWhatItCannotSeparate`.

## Since then

, on 2026-08-07, the first **Assumed** was measured and half of it is wrong.
Asked of `review.typo3.org` directly, `change:95162`,
`change:I7701923d80dbd29377213fa71c74ecad88cf7d31` and a change number that
exists nowhere all answer `200` with `[]`. So the tool is consistent after all:
all three are `empty`, and the `source-not-answering` the report saw for 95162
was the review server not answering that once, not a second shape of the same
permission effect. The feedback inferred a rule from a single reading, which is
what one reading supports.
