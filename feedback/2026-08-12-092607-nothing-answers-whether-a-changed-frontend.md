---
date: 2026-08-12T09:26:07+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_changelog_lookup, typo3_rule_lookup, typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# nothing answers whether a changed frontend markup output is Breaking, which decides whether a pat...

## Observation

Task: review Gerrit change 95169 and say whether it is breaking. That was the user's actual question, in those words, so the classification was not a side note — it was the deliverable.

I established empirically that the patch changes rendered output for existing content: `<figcaption>Caption</figcaption>` becomes `<figcaption><p>Caption</p></figcaption>` on every installation with an RTE image or table caption. Then I had to decide what that means in TYPO3's terms — Breaking or Bugfix, changelog entry required or not, and above all whether a patch with that effect may be backported to 13.4 and 14.3 as its own commit message claims. I answered all three from my own knowledge of TYPO3 process and shipped "-1, Breaking ohne Changelog und nicht backportbar" on that basis. Nothing was verified against the server.

I passed over typo3_changelog_lookup deliberately. Its description reads as "find the changelog entry documenting feature X", which is a lookup of existing entries — not "what are the rules that decide which type a new entry gets". I never considered it for the classification question. I also read typo3_commit_message_guide in the deferred list, thought explicitly about calling it to check the body requirement, and decided against it because I was confident. My confidence held — the review's process points are, as far as I can tell, correct — but that is luck, not method, and the same reasoning would have shipped a wrong verdict just as readily.

There is a closed feedback from 2026-08-10 recording that a bugfix and a task go one line back from main (206b6ee1). That is the adjacent rule and it is now in the server. What is still absent is the one upstream of it: what makes a change Breaking in the first place when no API signature moved — specifically, that changed frontend HTML for unchanged content counts, and that counting makes the whole backport plan void.

## Query

Never asked. The question I would have put: "is a change to the rendered frontend HTML of existing content classified Breaking, and may such a change go into 13.4 and 14.3?" — arising while reviewing change 95169, which alters lib.parseFunc_RTE in typo3/sysext/frontend/ext_localconf.php and carries "Releases: main, 14.3, 13.4".

## Suggestion

Carry the changelog type taxonomy as a rule, not only as a corpus of past entries: what distinguishes Breaking / Feature / Deprecation / Important / Bugfix, with the non-obvious Breaking cases named — changed frontend markup for existing content, changed default TypoScript, a widened public signature (already recorded, d6eb1695). Then say which types are admissible on a maintained release branch, so "Releases: main, 14.3, 13.4" on a patch that changes rendered output can be refuted from the server instead of from memory. Also worth widening typo3_changelog_lookup's description: as written it promises entry retrieval, and a reviewer asking "which type is this" will not open it.
