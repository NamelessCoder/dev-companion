---
id: D-KNW-065
date: 2026-08-09
status: open
---

# D-KNW-065 — What a public method on a non-final core class commits its author to is a gap this server owns

**That adding a parameter to a public method on a non-final core class breaks
every subclass overriding it is inside this server's boundary and missing from
it.**

The corpus states the rule for what a change removes and narrows, in two places,
and neither names the third move. A session widening a signature therefore
passes every convention lookup, every project check and the whole of the patch
workflow with nothing said, and meets the rule at commit-message time — after
the diff, the tests and the changelog entry are written.

## Evidence

- Re-run on 2026-08-09 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own query returns `fal-processing` at
  `appliesTo(10) + text(335)` and `fal-basics` at `appliesTo(3) + text(159)` and
  nothing else, which is the answer the feedback reported, unchanged. The same
  probe on the rule the session needed — "public method signature change on a
  non-final class breaks subclasses that override it" — matches nothing at all,
  and 86 hints come back as the index.
- The words are absent. `non-final`, `override point`, `optional parameter` and
  `signature-incompatible` occur nowhere below `knowledge/` or `skills/`.
- The gap is on the route the session was on.
  `skills/typo3-core-patch-development/SKILL.md` puts one convention call in its
  "Make the change" step — `typo3_hint_lookup` with the concrete paths — and
  that is the call the feedback made. The "Establish the blast radius" step
  above it asks how much of the pinned behaviour moves, which is about the
  suites and not about who overrides the class.
- The review route names the surface and enumerates it from the half that
  cannot carry this. `skills/typo3-core-patch-review/references/checklist.md`
  opens on "Public API. What the diff removes, renames, or changes the signature
  of", and then narrows it: "enumerated from the diff's deletions rather than
  from its additions". An added optional parameter is an addition, which is also
  why this session's functional, unit, cgl and phpstan runs were green on the
  breaking draft.
- The two places that do state the rule name two of the three moves.
  `breaking-not-assessed` in `src/Knowledge/CommitMessage.php` says "a removed
  or narrowed public or protected member makes the change breaking", and
  `## Breaking Changes` of
  `knowledge/documents/core/contribution/commit-messages.md` — what
  `typo3_rule_lookup(query "breaking change")` returns — says "A removed or
  narrowed PHP API gets an extension scanner matcher entry". Widening is in
  neither, and the feedback reports "narrowed" being read as not covering it.
- That the core calls a signature change breaking because of the subclasses
  holds, read in `.checkouts/main` at `c71b2bdb2f`.
  `Changelog/14.0/Breaking-106869-RemoveStaticFunctionParameterInAuthenticationService.rst`
  is a parameter change whose affected installations are the services
  "extending or overriding" the method, and
  `Changelog/14.0/Breaking-107777-UseStrictTypesInExtbaseArgument.rst` migrates
  by "Ensure all subclasses … declarations for overridden properties, method
  parameters, and return types". Both narrow. Whether the core files an *added*
  optional parameter the same way is the reading the todo owes.
- The one thing that did fire fires last. `typo3_commit_message_guide` is the
  final step of the development skill's order and of the core-patch `routing`
  entry in `knowledge/server-scope.json`, so the only statement of the rule
  reaches a session after the change has been characterised.
- One report from one session. `bin/cli feedback:list` on 2026-08-09 holds 12
  open feedback, all from `/home/benji/projects/typo3-cms` and all stamped
  within three minutes of each other. Nothing in the archive reports the public
  API surface of the class being edited.

## Decided

- Step 1a on the hint half and step 4 on the two wordings, queued rather than
  closed on the spot. Both tests for the spot fail: the wording sits in `src/`
  and in a skill's contract, and what the hint states is a claim about TYPO3
  that has to be read across `.checkouts/`.
- `normal` rather than the `low` the card arrived at. It is one session, so not
  more than that — but what the silence let through was a breaking change
  wearing a `[BUGFIX]`, on a patch whose point was reaching two release lines,
  with every check the project has green on it.
- Not `high`. Nothing is blocked on it, and the rule did arrive in the end.
- The correction is one word in each of the two wordings rather than a new
  check. Both name a closed list of moves, and what decides whether widening
  joins it as a rule or as a "consider" is the same reading the hint needs.
- Where the statement goes is the todo's. A hint of its own is the candidate
  rather than a domain that exists, because `appliesTo` on core `Classes/`
  reaches nearly every PHP path in a core checkout — which is either the point
  of a blanket rule or the objection to one, and
  [`D-KNW-038`](knw-038-a-hint-is-reached-by-the-role-of-a-file-rather-than-by-the-extension-it-sits-in.md)
  is what decides a hint by the role of a file.
- Not the feedback's own wording. Its author was guessing about this repository
  as much as a judging run guesses about TYPO3, and its proposed text asserts
  exactly what has to be read first.
- The backport half is here and never joined to the diff being written.
  `## Release Targets` says a breaking change goes to `main` and that a backport
  of one is the release managers' call. What no page states is the consequence
  in the direction a session meets it — that a fix owed to two release lines
  cannot carry the signature change at all. That is a sentence for the hint
  rather than a second gap.

## Assumed

- That the hint is the lever rather than the skill. The session made the hint
  call with the changed paths, so the placement is reachable by construction;
  whether a session that passes no path meets the rule is untested.
- That the silence produced the draft, rather than the model writing it and the
  corpus never having been consulted. Nothing here separates the two, and the
  lever is the same either way.
- That one session wrote this feedback and the eleven beside it. They share a
  directory, a model and three minutes, and nothing in a feedback records a
  session.

## Wrong if

- A precedent in `.checkouts/` files an added optional parameter on a public
  non-final method as a plain change. The wording correction would then be
  wrong, and the hint would be teaching a rule the project does not keep.
- The hint is written keyed on core `Classes/` and lands in the answer to every
  core PHP question. What it concerns is the class's own surface rather than the
  directory it sits in, and the placement would have to move to the role.
- A session meets the rule, hands the state over with the additive setter, and
  the reviewers reject that shape. The lever would be the API design and this
  entry would have answered the wrong question.
- The same surface is reported again from a review rather than from writing a
  patch. The checklist's "from its deletions" is then the placement that has to
  move, and one card carries both.
