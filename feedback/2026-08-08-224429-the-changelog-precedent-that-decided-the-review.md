---
date: 2026-08-08T22:44:29+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_changelog_lookup
directory: /home/benji/projects/typo3-cms
---

# the changelog precedent that decided the review was unreachable by words and came from ls in the ...

## Observation

Task: review Gerrit change 95179, a BUGFIX making stdWrap's "override" property apply the value "0" where PHP falsiness had suppressed it, targeting main, 14.3 and 13.4.

The central question of the review was whether a BUGFIX that flips 0-from-empty-to-defined, backported to two LTS lines, owes an Important changelog entry. typo3_rule_lookup answered the rule half well. The other half is precedent, and that is where the changelog tool could not reach.

typo3_changelog_lookup(query "stdWrap override") returned matchCount 0, with termCounts stdwrap 43 and override 22. That is documented behaviour (every word must be carried by one entry) and the skill warns about exactly this, so it is not a wrong answer. But no wording I would have thought of reaches the entry that settled the finding, because the connection is a shape rather than a vocabulary: "a BUGFIX that changed 0-is-empty semantics and got an Important entry on an LTS branch". The entry is titled Important-106401-Treat0AsADefinedValueForNullableDatetimeFields.rst and lives in 13.4.x; it shares no noun with stdWrap or override. I found it with `ls Documentation/Changelog/13.4.x/ | grep -i important` followed by Read, then confirmed the commit type with `git log --all --grep 106401` -> "[BUGFIX] Treat 0 as a defined value for nullable datetime fields". Checkout answered, server did not.

There is a second, smaller finding underneath it that is mine rather than the tool's, and worth recording because it is likely to repeat. The tool does support a query-omitted, type-and-version-bounded listing, and `type: important, version: 13.4` would have produced precisely the 20 filenames my `ls` produced. I did not reach for it, because that mode is described in the skill's base procedure only in the deprecation-sweep step ("at each major the package declares, bounded by tag and with the query omitted"), framed entirely around type: deprecation. I applied the pattern where it was taught and did not generalise it to type: important. The affordance existed and the framing hid it.

## Query

typo3_changelog_lookup(query: "stdWrap override") -> matchCount 0. The entry actually wanted: typo3/sysext/core/Documentation/Changelog/13.4.x/Important-106401-Treat0AsADefinedValueForNullableDatetimeFields.rst

## Suggestion

Two things. First, in the tool description, state the query-omitted listing mode as a first-class way to use it rather than leaving it implicit ("omit query to list a version or a type as a whole" is present but reads as a footnote): a reviewer looking for precedent wants "every Important entry in 13.4.x", and that is one call. Second, consider making precedent searchable by change type: the strongest argument a core review can make is "an earlier change of this kind owed X", and today that requires knowing the earlier change's vocabulary. Indexing each entry with the commit keyword of the commit that introduced it (BUGFIX/FEATURE/TASK) would let a query ask "which BUGFIX-introduced Important entries exist on an LTS line", which is the actual question.
