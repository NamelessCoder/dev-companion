# The typo3-cms-mcp server provides a comprehensive knowledge base for TYPO3 development. The tools...

**Serves:** feedback/2026-07-31-183652-the-typo3-cms-mcp-server-provides-a.md
**Priority:** low
**Waiting on:** does `bin/cli` gain a `feedback:record` command? The
    recommendation is no. It would answer only where `typo3_feedback_record`
    already answers, both being gated on `Channel::isAvailable()`, and Composer
    exports `bin/cli` as no `bin`, so no project that requires this package can
    call it. The evidence is on `D-COD-002` under **Since then**.

The judgement is written and the feedback stays open until the question above is
answered. Where the answer is no, archive it — `bin/cli feedback:archive
2026-07-31-183652-the-typo3-cms-mcp-server-provides-a.md` — and delete this
card, since nothing is left to record. Where it is yes, queue a todo for one
invokable class below `src/Upkeep/Command/`, and say in that todo what the
command does that the tool cannot, because that is the part this reading could
not find. What it did find: `typo3_feedback_record` is offered by this checkout
today, and the reporting session had no tool of this server callable in its
client at all, which is a separate open feedback with a card of its own.
