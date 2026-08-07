---
date: 2026-08-07T13:24:46+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# no rule section says what each change type owes as a changelog entry; both queries landed on Rele...

## Observation

Task: review a local TYPO3 core commit ([BUGFIX] in Extbase persistence) against a v15 checkout, including whether it owes a changelog entry.

The typo3-core-patch-review checklist makes this a surface I have to answer either way, and warns that demanding an entry where the rules do not require one is itself a review defect. So I needed the obligation matrix per change type.

Two calls, both landed on the same section. The first returned exactly one match: "Release Targets" from core/contribution/commit-messages — good content about which branches take a patch, nothing about changelog entries. The second returned that same section again plus "TYPO3 Contribution Sources", which is a list of docs.typo3.org URLs and carries no rule text of its own. Neither answered which change types owe an entry in Documentation/Changelog, which directory or file name it goes in, or what restFiles has to cross-reference.

I settled it from my own knowledge plus a reading of the diff's deletions (nothing public removed or renamed, ColumnMap is @internal, so no Breaking or Deprecation file and no extension scanner matcher). That conclusion held, but it came from me rather than from the server, and a reviewer with less prior TYPO3 knowledge would have had nothing.

Both answers carried alsoInHints: [{"id":"documentation-changelog","title":"Documentation and Changelog"}]. So the content appears to live in a hint rather than in a rule. I saw that pointer twice and passed over it both times — partly because a query with the word "changelog" in it returning a section about release branches reads as "this is what the server has", and partly because nothing in the answer said the hint would carry what the rule did not.

## Query

typo3_rule_lookup query="changelog entry branches that take a patch today releases trailer breaking change"; then typo3_rule_lookup query="bugfix changelog entry obligation and review readiness"

## Suggestion

Either carry a rule section stating the changelog obligation per change type — bugfix, feature, deprecation, breaking, task — with the directory, the file naming convention and the restFiles cross-reference, or have typo3_rule_lookup inline the documentation-changelog hint body when the query contains "changelog", instead of naming its id at the bottom. As it stands the highest-scoring match for a query containing "changelog entry obligation" is a section that never uses the word in that sense, and the pointer to where the answer actually lives is the least prominent field in the response.
