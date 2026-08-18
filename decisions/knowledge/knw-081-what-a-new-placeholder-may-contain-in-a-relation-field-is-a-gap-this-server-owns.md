---
id: D-KNW-081
date: 2026-08-17
status: confirmed
---

# D-KNW-081 — What a NEW placeholder may contain in a relation field is a gap this server owns

**The spelling a NEW placeholder needs to survive a relation field is inside
this server's boundary and missing from it, so the feedback is queued.**

The corpus tells a session that NEW placeholders in a relation field "resolve
once the children have been created in the same run" and says nothing about how
one may be spelled. A session that names them readably loses every child
relation, and the run reports success.

## Evidence

- The query reaches the right hints and none of them constrains the placeholder.
  `bin/cli hints:probe "NEW placeholder underscore inline relation datamap DataHandler"`
  returns `datahandler-basics`, `environment-placeholders`,
  `datahandler-testing`, `datahandler-writing` and `datahandler-relations`. The
  two the feedback names are the last two.
- The constraint is in nothing else here either. Below `knowledge/` and
  `skills/`, "underscore" occurs about a plugin signature, an extension key and
  a Sass partial, and `getUniqueId` occurs nowhere.
- The feedback's claim about TYPO3 holds, and one method carries it, one step
  earlier than the feedback places it. On `.checkouts/14.3`,
  `DataHandler::processRemapStack()` explodes a relation value containing `NEW`
  on `_`, pops the last part as the uid and reads the rest as the table, then
  looks up `substNEWwithIDs` under the popped part alone. `NEW_card1` therefore
  resolves to nothing and is written back as `NEW_`, which
  `RelationHandler::readList()` discards because the id is not an integer.
  Neither site logs, which is the silence the feedback reports.
- The statement lands unbound. That block is identical on 12.4, 13.4, 14.3 and
  `main`.
- The constraint belongs to a relation value rather than to the datamap key. The
  pid path looks up the whole placeholder after stripping a leading `-`, so
  `NEW_card1` works as a key and as a `-NEW_card1` position.
- The safe default the feedback names holds. `StringUtility::getUniqueId()` is
  `uniqid($prefix, true)` with the dots removed, so `getUniqueId('NEW')` carries
  no underscore.

## Decided

- Step 1a of the ladder, and queued rather than closed on the spot. What lands
  is a corpus statement about TYPO3, and this run may not write one.
- The statement goes on `datahandler-relations`, beside the sentence about NEW
  placeholders resolving in the same run, because that is the sentence a reader
  checks when a relation does not resolve.
- One statement in an existing hint rather than a hint of its own. The
  DataHandler family is six questions by `D-KNW-030` and this is part of what a
  datamap does to a relation field, which is `D-KNW-018`.
- It names the symptom as well as the rule. The children are written, the
  parent's counter stays 0, and nothing is logged, so a reader who has the
  symptom and not the rule has no other way in.
- Not step 2, 3 or 4. There is no rule here to move to where the task passes, no
  entry to route the query at, and no wording to correct.

## Assumed

- One statement is enough. `datahandler-writing` defines the placeholder as
  "NEW" plus a unique suffix, and a session naming its keys there is assumed to
  reach the relation hint before it writes a relation field.

## Wrong if

- A session naming its datamap keys never asks about relations, and so meets the
  rule only after it has paid for breaking it. The constraint then belongs where
  the placeholder is coined as well.
- An underscore turns out to cost something outside a relation value — in a
  command map target, a group field, a FlexForm — so the rule is about the
  placeholder itself rather than about what a relation field is handed.
- The drop is logged somewhere neither method shows. Two code paths were read,
  not a run, and "nothing anywhere reports it" is the part of the statement
  least able to survive being wrong.

## Confirmed on 2026-08-18

Two of the three **Wrong if** were read out on `.checkouts/14.3` and the
statement went in as
[`D-KNW-084`](knw-084-the-corpus-states-which-placeholder-spelling-a-relation-value-survives.md).

The underscore costs nothing outside a relation value. The split on `_` happens
in one place, the value-array loop of `processRemapStack()`, and everything else
that reads a NEW id looks the whole string up: the datamap key, the pid and its
`-NEW...` form, the MM parent in `dbAnalysisStoreExec()`, the container id in
`updateFlexFormData()`. Five call sites push a value array onto the remap stack
and four of them carry a `func` that consumes it — `category`, `group` and
`select` with a `foreign_table`, `inline`, `file`. The fifth,
`checkValueForInternalReferences()` for `transOrigPointerField` and
`translationSource`, carries none, so the remapped array is never read and the
spelling makes no difference there. No core test writes a NEW value into
`l10n_parent`. So the rule is about what a relation field is handed, which is
where the statement puts it.

Nothing logs the drop, and the reading now covers every site rather than two.
`RelationHandler` carries no logger at all; `readList()` drops a non-integer id
without a word and `writeForeignField()` says nothing about an item that is no
longer there. `processRemapStack()`, `checkValue_inline_processDBdata()`,
`checkValue_file_processDBdata()` and `checkValue_group_select_processDBdata()`
make no `log()` call on this path. Nothing compares the list before the drop
with the list after it, so there is nothing that could report it. What the log
does hold is the ordinary entry for the parent's own update, with the counter at
0.

The first **Wrong if** stands: whether a session naming its datamap keys reaches
the relation hint before it writes a relation field is not something reading the
code can answer, and only a second feedback would show it.
