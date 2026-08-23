---
id: D-KNW-084
title: The corpus states which placeholder spelling a relation value survives
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::aRelationValueSaysWhichPlaceholderSpellingSurvivesIt
---

# D-KNW-084 — The corpus states which placeholder spelling a relation value survives

**A NEW placeholder handed to a relation field may carry no underscore, and
`datahandler-relations` says so beside the sentence about placeholders resolving
in the same run.**

That sentence is what a reader checks when a relation does not resolve, and it
sent the reporting session looking at datamap ordering instead. The statement
names the symptom as well as the rule, because the rule is invisible at the call
site.

## Evidence

- The reading is
  [`D-KNW-081`](knw-081-what-a-new-placeholder-may-contain-is-a-subject-this-server-owns.md)'s
  **Confirmed on**, taken on `.checkouts/12.4`, `13.4`, `14.3` and `main`.
- The statement is unbound. The split block is identical on all four branches,
  and each of them has the same four remap entries that consume the value.
- The symptom query reaches it and nothing else. "inline children created but
  the relation is empty, parent counter 0, uid_foreign 0, nothing logged"
  returns `datahandler-relations` alone, on the words the `appliesTo` gained.

## Decided

- One statement in `datahandler-relations`, as `D-KNW-081` placed it.
- It says where the constraint does not reach — the datamap key, a `-NEW...`
  positioning pid, an MM parent id — because a reader who has just lost a
  relation would otherwise rename every placeholder in the script.
- `StringUtility::getUniqueId('NEW')` is named as the id that always conforms,
  rather than a rule about which characters are allowed. One call is cheaper to
  follow than a spelling rule, and it is what the core uses itself.
- The `appliesTo` gains the words the symptom is asked in — `uid_foreign`,
  `relation not saved`, `children not linked`, `counter stays 0` — because the
  session that needs this has the symptom and not the subject.
- Not stated in `datahandler-seeding` as well, though the feedback asked for it.
  `D-KNW-081` decided one statement, and a second copy of a rule is the thing
  that goes stale on one side.

## Assumed

- The four field types that consume a remapped value array are one rule to the
  caller. The statement says "a relation field" rather than naming `inline`,
  `file`, `group`, `select` and `category`, which is the vocabulary a session
  writing a datamap already has.

## Wrong if

- A placeholder from `getUniqueId('NEW')` is lost the same way, so the
  constraint is not the underscore alone.
- A core change resolves the value before the split, or logs the drop. The
  symptom half then describes a version nobody is on, and the statement is bound
  rather than corrected.
- A second feedback arrives from a session that named its datamap keys and never
  reached the relation hint. The rule then belongs where the placeholder is
  coined as well, which is `D-KNW-081`'s remaining **Wrong if**.
