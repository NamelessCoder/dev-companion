---
date: 2026-08-24T17:31:16+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# nothing answers whether an old issue's defect still reproduces against the checkout

## Observation

Task: pick another old open issue from forge.typo3.org, branch, and work it off, against a core checkout on main (15.0.0-dev).

The whole session was spent on candidate selection because no call answers the only question that decides a candidate: does the reported defect still exist in this checkout. typo3_forge_lookup answers what the report says and what the tracker holds; it cannot answer what the code does now. That is correct as a boundary, but the consequence is that an agent triaging the old backlog has to pick blind and verify by hand.

I made 21 typo3_forge_lookup calls and evaluated ten issues. Four of them turned out already fixed, and I only learned that after reading code or writing a throwaway functional test:

- #82784 (copyRecord ignores negative destPid) — I wrote a probe functional test under DataScenarios/Regular/Modify and ran it against sqlite. Result: pid 89, sorting 896, correctly placed. Not reproducible. No fixing commit identifiable.
- #86711 (prependAtCopy instead of "Translated to") — fixed as a side effect of #109014, commit cc880c67777, which extracted localizeRecord() out of copyRecord(). Found by reading DataHandler::localize() and then git log -S.
- #88263 (replace file keeps old extension) — fixed by #106890, commit cf227b18e20. Found by reading ExtendedFileUtility::replaceFile() and seeing the explicit extension-rename branch.
- #93375 (superfluous p-tags from makelinks) — fixed by #93012, commit 90c2181b260. Cost three probe functional tests plus eight git archaeology calls before I could name the commit.

Two more were dropped not because they were fixed but because a maintainer comment in the issue itself made the fix unwelcome (#88705: Benni Mack "I recommend using maxItems instead, and we should remove limit completely"; #59225: Benni Mack "the check should NOT happen in PageRouter"). Those the server did answer, and well.

The ratio is the finding: one live issue per five candidates, and the four dead ones each cost a code read or a test run. An agent told to "work off an old issue" will hit this every time.

## Query

typo3_forge_lookup(open="oldest", tracker="Bug", limit=25) and then, in sequence, category="backend ui", "datahandler", "file abstraction layer", "typoscript", "fluid", "site handling", "link handling", each with createdBefore; plus issue=35069, 59225, 59822, 86711, 88263, 82784, 88705, 77598, 87667, 96421, 99132, 93375, 88486. Task text: "hole dir ein weiteres altes issue, erstelle einen branch und arbeite es ab" (fetch another old issue, create a branch, work it off).

## Suggestion

A call that takes a Forge issue number and answers, against the checkout the server was started in, whether the defect is still present — or at minimum a cheaper proxy for it. Two shapes would each have paid for themselves here:

1. Given an issue, name the code paths its description and comments reference (class names, method names, file paths, TypoScript properties are all in the text) and say which of them still exist in this checkout, which were renamed, and which were removed. #93375's comment named _parseFunc and http_makelinks; _parseFunc no longer exists. That one fact would have told me in one call what took eight.

2. Given an issue, search the merged history for commits whose message or diff touches the same area and that landed after the issue was last confirmed, and surface them as "candidate fixes, not linked from the tracker". All three of my fixed issues were fixed by a patch resolving a *different* issue, so the tracker's own relations were silent.

Failing both, a documented statement in typo3_server_scope that reproduction is out of scope and belongs to the checkout would at least stop an agent expecting it.
