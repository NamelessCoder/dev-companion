---
id: D-FBK-039
title: A mangled name is rewritten once, and the comparison carries the rest
date: 2026-08-03
status: open
coveredBy:
  - FeedbackTest::everyNameTheCorpusCarriesIsSpelledTheWayThisProjectSpellsIt
  - FeedbackTest::aNameIsFoundHoweverItsSeparatorsAreSpelled
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

On 2026-08-04, the second **Wrong if** happened. `feedback/2026-08-04-180241`
recorded `ListMcpResourcesTool` beside two `typo3_*` names and read it back as
`listmcpresourcestool`, in the joined string and in the split array alike.
`Channel::toolNames()` keeps the separators the commit above restored and still
lowercases what is left, so the mangling is a different one and the writer is
where it lives — which is exactly what this entry named as the sign that
rewriting the data had been the cheaper half.

`Channel::comparable()` carries the other half of it: it strips everything
outside `[a-z0-9]`, so a stored name in the spelling it was given in would lose
its capitals rather than fold them, and the filter would not find the name it
holds. Both are one repair and neither has been made yet.

The report's second item is answered without a change. It asks whether
`feedback/2026-08-04-175804` should be recategorised from `bug` to
`missing-knowledge`; it should not, because the ladder is walked from the
observation and never from the front matter, and that feedback was judged as
`1a` regardless of what its category says — `D-KNW-061`.

The repair was made the same day. `Channel::toolNames()` keeps the case it was
given, `Channel::comparable()` folds it, and the corpus carried exactly one name
the fold had damaged — `listmcpresourcestool` in `feedback/2026-08-04-180133`,
rewritten to `ListMcpResourcesTool`, which is the spelling the session passed
and which its client's transcript still holds. Every other stored name this
server does not register is lower case as it was written, the wrapper
`mcp_typo3cmsmcp_typo3_feedback_record` among them.

What made this cheap to miss is worth naming: everything this server registers
is lower case, so nothing in its own vocabulary could show the fold. The one
kind of name that could is a name from outside it, which is also the kind a
session writes down when it reached for something else instead — the report this
channel is least able to afford losing.
