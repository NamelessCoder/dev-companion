---
date: 2026-08-17T20:57:59+00:00
category: missing-knowledge
status: closed
closed: 2026-08-17
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/site-demo
---

# a NEW placeholder containing an underscore silently drops every inline child, with no error anywhere

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. Seeding the demo content with a DataHandler script, following the datahandler-seeding hint.

I named the datamap keys readably: the parent 'NEWcards' with children 'NEW_card1', 'NEW_card2', 'NEW_card3', and file references 'NEW_ref_hero' and so on. process_datamap() then reported success. errorLog was empty. The child rows were created. And every relation was silently dropped: tx_sitepackage_cards stayed 0, and the children carried uid_foreign=0 with an empty tablename. The same happened for every sys_file_reference. Nothing in errorLog, nothing on the remap stack, no log entry — the only symptom was a frontend that rendered no cards.

I spent twelve tool calls and three probe scripts isolating it. Probe with alphanumeric ids (NEWprobeP / NEWprobeC1) linked correctly: counter 2, uid_foreign set, tablename tt_content, sorting_foreign 1..n. Probe with the same structure and underscore ids failed. Third probe removed the shared prefix (NEW_p / NEW_c1) and still failed, which ruled out prefix collision and left the underscore itself.

The cause is in the core: a relation field's value is parsed by RelationHandler, whose item notation is <table>_<uid>. DataHandler::checkValue_inline_processDBdata() at DataHandler.php:3113 passes the value array straight to $dbAnalysis->start() at :3119, and the same convention is visible at :3151, where a disallowed file reference is split with GeneralUtility::revExplode('_', $reference, 2). So "NEW_card1" is read as table "NEW", uid "card1", matches nothing, and is discarded.

Neither datahandler-seeding nor datahandler-relations mentions any constraint on the placeholder string. datahandler-relations does say that NEW placeholders "resolve once the children have been created in the same run", which is exactly the sentence a reader checks when the relation does not resolve — and it sent me looking at datamap ordering instead. I reordered the datamap twice on that basis before finding the real cause.

This is the most expensive single finding of the session and the one I would not find again quickly: it produces no error, no log line, and a database state that looks like a plausible half-success.

## Query

typo3_hint_lookup id=datahandler-seeding targetVersion=14; typo3_hint_lookup id=datahandler-relations targetVersion=14; then a DataHandler datamap with keys of the form NEW_card1 in a tt_content inline field of type=inline with foreign_field

## Suggestion

State in datahandler-seeding, and again in datahandler-relations beside the existing NEW sentence, that a NEW placeholder used in a relation field must be alphanumeric: the underscore is RelationHandler's table/uid separator, so an id like NEW_card1 is parsed as table "NEW" and discarded. Worth naming the symptom rather than only the rule, because the rule is invisible at the call site: the children are written as standalone records, the parent's counter stays 0, and neither errorLog nor the remap stack reports anything. StringUtility::getUniqueId('NEW') produces a conforming id and is worth naming as the safe default for a hand-written datamap.
