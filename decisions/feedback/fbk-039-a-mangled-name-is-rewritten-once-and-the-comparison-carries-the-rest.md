---
id: D-FBK-039
title: A mangled name is rewritten once, and the comparison carries the rest
date: 2026-08-03
status: open
coveredBy:
  - FeedbackTest::aNameIsFoundHoweverItsSeparatorsAreSpelled
  - FeedbackTest::everyNameTheCorpusCarriesIsSpelledTheWayThisProjectSpellsIt
---

# D-FBK-039 — A mangled name is rewritten once, and the comparison carries the rest

**The 43 mangled names are rewritten once to the spelling this project uses, and
two names go on being compared with their separators ignored.**

Both, in the commit that stops the stripping. Keeping the given spelling makes
the stored form heterogeneous, so the filter has to reconcile spellings whatever
happens to the corpus. What the rewrite buys is the other half of the report: a
name a grep finds.

## Evidence

- `Channel::toolNames()` stripped everything outside `[a-z0-9_]`, so a hyphen
  went and an underscore stayed. 43 names in the corpus resolved to a tool this
  server registers or a skill this repository ships once their separators are
  ignored, and none of them was spelled that way in the file.
- One tool sat under two identifiers: `typo3_documentation_lookup` in the front
  matter of twelve feedback and `typo3documentationlookup` in five, both written
  by sessions naming the same tool.
- The mangled names are not guessed back. Each one's separatorless form matches
  exactly one name from `Registry::definitions()` or one directory under
  `skills/`, which is the same comparison the filter makes.
- What that comparison does not resolve stays as it was written: a name for a
  tool that has since been renamed away, a client's wrapper
  (`mcp_typo3cmsmcp_typo3_feedback_record`), and the run-together names an
  earlier version of the same stripping left behind.

## Decided

- Both, rather than one of them. Matching on the separatorless form is what
  `D-ANS-006` already does for an identifier a caller looks up, and it is the
  cost of storing what was written; the rewrite is what the report actually
  asked for, and it is a one-off edit rather than a rule anything carries.
- No migration command. It resolves names against a registry and a directory
  that move, so a second run a month from now would be a different rewrite —
  `FeedbackTest::everyNameTheCorpusCarriesIsSpelledTheWayThisProjectSpellsIt`
  reads the corpus instead and fails on a name that arrives mangled again.
- The front matter only. The prose of a feedback is a session's report and is
  not edited, and the one file that names a skill in its body already spells it
  with the hyphens the session typed.

## Assumed

- That a stored name resolving to exactly one tool or skill is that tool or
  skill. Two names collapsing onto one separatorless form would make the
  resolution wrong and the filter ambiguous with it; none of the names this
  server registers or this repository ships does.
- That the archive tolerates this edit. Nothing is deleted and no report is
  restated — what changes is a name back to what the session passed in.

## Wrong if

- A feedback filtered by tool comes back carrying another tool, because two
  names met on their separatorless form.
- The next mangling is a different one and the corpus needs a second sweep. Then
  rewriting the data was the cheaper half of a problem that lives in the writer.

## Since then

The second **Wrong if** happened: a session recorded a tool name from outside
this server and read it back folded, because the writer kept the separators and
still lowercased what was left — which is what this entry named as the sign that
rewriting the data had been the cheaper half. The comparison carried the other
half of it, stripping everything outside the alphabet so a stored spelling would
lose its capitals rather than fold them. Both were one repair and it was made
the same day, the corpus carrying exactly one damaged name.

What made this cheap to miss is worth naming: everything this server registers
is lower case, so nothing in its own vocabulary could show the fold. The one
kind of name that could is a name from outside it, which is the kind a session
writes down when it reached for something else instead.
