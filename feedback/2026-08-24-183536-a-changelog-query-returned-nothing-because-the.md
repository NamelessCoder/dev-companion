---
date: 2026-08-24T18:35:36+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_changelog_lookup
directory: /home/benji/projects/typo3-cms
---

# a changelog query returned nothing because the question was a rule, not a change event

## Observation

Task: reviewing Gerrit change 91127 against a 15.0.0-dev core checkout, I needed to know whether widening PageRenderer::reset() from protected to public owes a changelog entry.

I called typo3_changelog_lookup(query "visibility public", limit 25). It returned matchCount 0, with termCounts showing "visibility" matching 8 entries and "public" matching 34, but no entry carrying both. That is a correct answer to the query and no use at all for the question, and I did not repeat it with other arguments — I went to the checkout instead (grep over Documentation/Changelog/12-15 for phrasings like "now public" and "made public", then a sweep of 1405 commits for diffs that move a method from protected to public).

Two things worth reporting.

First, the tool's own description and the review skill both told me not to do what I did. The skill says: "List the kind before you search for words: `type` and `version`, and no query at all ... What makes an earlier change a precedent is its shape, and a shape has no vocabulary — the entry that settles a finding regularly shares no noun with the diff." I read that, and then queried with words anyway because "visibility" felt like it would be in a title. It is not, and the skill had already said why. So this is partly a report on myself — but the guidance being right and still not being followed is information about where it sits: it is in the skill, three paragraphs into a section about what a patch owes, and it is needed at the moment the tool is being called.

Second, and this is the actual finding: the question was never a changelog question. "Does this kind of change owe an entry" is a rule, and rules live in typo3_rule_lookup. The changelog records events, so the absence of an entry is exactly what I was trying to establish, and a search returning zero cannot distinguish "no such rule" from "the words were wrong" from "nobody ever did this". I could not tell those apart from matchCount 0, which is why I fell back to the checkout.

What I ended up establishing by hand: the core widens visibility in plain [TASK] commits with no changelog file at all (343e93a97826 "[TASK] Promote generateRandomBase64String to public API"). That single commit is the whole answer and it took a 1405-commit sweep to reach.

## Query

typo3_changelog_lookup(query: "visibility public", limit: 25) — returned matchCount 0, entries [], termCounts visibility:8 public:34. The real question behind it: does a core change that widens a method from protected to public owe a Breaking or Important changelog entry and an extension scanner matcher?

## Suggestion

Two small things.

On the zero answer: when matchCount is 0, say what kind of question the changelog cannot answer. A line such as "the changelog records change events; whether a kind of change owes an entry is a rule — ask typo3_rule_lookup" would have redirected me in one round trip instead of sending me to git. The termCounts are already doing half of this by showing which words exist; the other half is naming the tool that answers the other shape of question.

On the rule itself: typo3_rule_lookup(query "breaking change") should state where visibility widening falls, with the precedent named. It is a recurring core patch shape — promote an internal helper to public API — and it currently has to be settled by history sweep every time. See the separate feedback on typo3_commit_message_guide's breaking-not-assessed check, which asserts the opposite of what the history shows; the two are the same underlying gap seen from different tools.
