---
id: D-AUD-010
title: The content model is answered and the records stay with the installation
date: 2026-08-12
status: open
---

# D-AUD-010 — The content model is answered and the records stay with the installation

**The three audiences stay three: this server answers for the content model, a
record is the installation's own, and `doesNotCover` says so.**

The boundary was half crossed and written down nowhere. `typo3_schema_lookup`
answers what a table is, no tool touches a row of one, and a caller had to infer
the line from which tools happen to exist.

## Evidence

- The trust model is what decides it rather than taste. The client launches this
  server as a stdio subprocess, so the process boundary is the whole of its
  security: a record read puts the shell user's database access where a backend
  user's permissions belong.
- Opening the record side is a second server rather than a further tool. Backend
  identity, workspaces and the DataHandler come with it, and each of them is a
  question about who is asking.
- Nothing has arrived since the question was first put on 2026-08-04 to move it.
  The draft RFC read that day is a community proposal and settles nothing
  (`D-SCO-010`), and no session has asked for a record: what the archived
  feedback asked for is DataHandler *knowledge* — how code that writes records
  is written — which is the model side and is answered by a hint.
- `R-AUD-001` counts three audiences because somebody chose three, and this is
  that choice made again with the record question named.

## Decided

- One `doesNotCover` entry, naming the record and the reason, so the boundary is
  read rather than inferred from the tool list.
- No fourth audience, and `R-AUD-001` stands as written.
- Rejected: taking the record side. It is a different server with a different
  trust model, and this one would have to grow an identity before its first row.

## Assumed

- That an agent acting on records is served by the backend and the console,
  which is where the permissions are applied.
- That the model half stays enough for the three audiences. Extension and site
  work is written against the schema rather than against rows.

## Wrong if

- Sessions keep arriving at a task that needs a record and stop at the boundary,
  which is the evidence that was asked for and has not come.
- An interface contract on identity is adopted and makes the mapping from a
  backend user to a client session a solved problem rather than an open one.
- The model half turns out to be answered wrongly because it never sees a row —
  a schema question whose real answer depends on what is stored.

## Since then

The `doesNotCover` entry is there, naming the record, the trust model and where
the work goes instead — the backend, or the installation's own console.

The first **Wrong if** met its first real case and came out the other way.
`feedback/2026-08-21-074351` arrives at a task that wants a flex field resolved
against a real row and reads the boundary itself: "Loading a real row is a
record read and is outside the scope this server declares — an emulated record
with caller-supplied values stays inside it." What was built from it holds that
line: `typo3_flexform_lookup` takes `table`, `field` and a `record` of
caller-supplied column values, and no uid. So a session did arrive at the
boundary, and it neither stopped there nor crossed it.

The other two are unchanged. No session reports a schema answer that was wrong
for want of a row, and nothing about an identity contract has been adopted since
`D-SCO-010` read the draft.
