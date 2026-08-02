# Revoke the two confirmed entries whose gap is closed

**Serves:** decisions/
**Priority:** normal

The rule written into
[`writing-a-decision.md`](../../documentation/feedback/writing-a-decision.md) on
2026-08-02 — a statement that has stopped being true is revoked and a successor
is written for what holds — was applied to `D-ANS-023` and to nothing else, and
two entries were already on the wrong side of it. `D-KNW-014` says the record
variable a v14 preview template is handed **is a gap this server owns**, while
its own **Confirmed on** says the statements are on `content-elements` and names
`R-KNW-041`; `D-KNW-015` says a Fluid preview template replaces the content half
**and nothing here says so**, while `main` carries "Say what the default preview
renderer draws around a Fluid preview". Read each against the corpus as it is
now rather than against its own confirmation, because a statement written into
`content-elements` and one still missing look the same from inside the entry —
then, where the gap is closed, revoke in place with `revokedBy` and write the
successor for what holds, with its **Wrong if** against what can go wrong now.
`D-ANS-026` is the worked shape. Two entries are two successors and not one:
what a `{record}` path resolves to and what a preview template replaces fail in
different ways.
