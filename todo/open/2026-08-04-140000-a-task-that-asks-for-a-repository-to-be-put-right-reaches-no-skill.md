# A task that asks for a repository to be put right reaches no skill

**Serves:** R-GUI-006
**Priority:** normal

Draft the skill `D-SKL-016` decided on — the one a request worded as a change
reaches, which starts from the conformance report, writes the findings into a
worklist it commits, and then works that list off — and draft it the way
[writing-a-skill.md](../../documentation/clients/writing-a-skill.md) prescribes
rather than from the shape alone: ask this server what it already answers about
the domain before the first line, and read
`skills/typo3-extension-conformance/SKILL.md` whole, because the boundary is
narrow and is where this draft will go wrong. Conformance already names the
workflow each finding goes to and already hands over for the changes; what this
skill adds is the entry point, the order across many findings, and staying with
the list until it is empty. A draft that re-derives the findings is the first
**Wrong if** of `D-SKL-016` happening. The draft is shown to the maintainer and
feedback asked for by name before anything reaches `Installer::SKILLS`, and the
intent in `knowledge/task-intents.json` is the second half of publishing rather
than part of writing it, because
`SkillTest::everySkillNamedInKnowledgeIsPublished` holds every name there to
that list.

## What is settled, and what the reading found

The hole was re-measured in this checkout on 2026-08-04 rather than taken from
the card. `TaskIntents::detect()` returns nothing for "look over my repository
and put it right", "improve the code quality of my sitepackage", "clean up my
extension and fix what is wrong" and "make my TYPO3 project better", while
"review the TYPO3 project and site package" returns
`typo3-extension-conformance`. The intent works and the wording is what it does
not reach.

What such a task should reach was the open question, and the maintainer answered
it on 2026-08-04: a skill of its own, conformance as its precondition, a derived
worklist that is committed, then worked off. That sets aside this card's own
ruling that a skill was one of the two things it must not be. `D-SKL-016`
carries the answer, what it rests on and what would show it wrong.

Two facts in `D-SKL-013` had gone stale under the card and are corrected in that
entry's **Since then**: fifteen intents rather than thirteen, seven of them
routing rather than five, and of the three published skills its third **Wrong
if** calls unreached, one is routed and one no longer exists. The warning that
bullet carries is untouched, and it is what this draft has to answer to.
