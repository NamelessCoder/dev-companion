# no skill covers reviewing an incoming pull request against an extension or sitepackage repository

**Serves:** feedback/2026-08-18-113343-no-skill-covers-reviewing-an-incoming-pull.md
**Priority:** normal
**Waiting on:** the review `writing-a-skill.rst` asks for before a skill is
    published, which only the person who asked for it can give. The draft is
    `skills/typo3-extension-patch-review/SKILL.md` with its checklist beside it,
    and what has to be asked by name is whether this is how such a review is
    really done here: which step is missing, which one is wrong, what it claims
    that does not hold for a package of yours. "Does this look good" gets
    agreement rather than review. Two questions of its own go with it — whether
    `typo3-extension-patch-review` is the name a user's own words reach, and
    whether a review of a public pull request is a workflow that ends in public
    in the sense of `R-SKL-020`, which the author read as no, like
    `typo3-core-patch-review` and unlike the two skills that publish.

    The review was given on 2026-08-19 and is worked in, so what is left is
    not it. The name was confirmed in the same breath, and the finding was the
    crossing to `typo3-core-patch-review`: it never fires, because a change
    against the core arrives on Gerrit rather than as a pull request against a
    package, and the reviewer could name no case that would reach it. It is out
    of the body, out of the description and out of the crossing map in
    `SkillTest`. What the card waits on now is the baseline run `D-SKL-035` asks
    of a new skill, which needs a client session in an environment the skills
    are not installed into, and the room in the listing below. The second of the
    two questions of its own was put again and not answered: where a review of a
    public pull request stands against `R-SKL-020`.

What the research established is at the foot of `D-SKL-063` rather than here,
because it outlives this card. The queue behind the answer: work the review in,
delete the `metadata` declaration, write the intent that routes to it into
`knowledge/task-intents.json` and the workflow into
`knowledge/server-scope.json` in the same commit, check the listing budget
`D-SKL-026` measures, and buy the baseline run `D-SKL-035` asks of a new skill.

What the room costs, measured on 2026-08-19. The twelve published descriptions
came to 3570 characters against a ceiling of 3600, and this entry cost 394. Two
of the three step clauses `D-SKL-054` priced are out —
`typo3-core-patch- development` and `typo3-core-issue-triage`, which keep their
triggers and gave up 162 between them — and the third was left alone
deliberately, because `D-SKL-026` records a session that did not activate
`typo3-core-patch-checkout` after that very clause was cut. This skill's own
description gave up its list of the body's five sections and now costs 350. So
the twelve stand at 3408 and publishing would put the listing at 3759.

**The gap is structural rather than a wording**: a ratchet set to what twelve
descriptions cost forbids a thirteenth at any length. Put to the maintainer on
2026-08-19 against raising the ratchet to 3760 and against trimming further, and
the answer was to merge `typo3-extension-cleanup` and
`typo3-extension- conformance` into one skill. That frees the room the way
`D-SKL-054` named it, and it is its own work: `D-SKL-014` reads the two as two
workflows, so what has to be read before anything is merged is why.

Judged on 2026-08-18 as the ladder's step 1b and written up in `D-SKL-063`:
nothing owns judging one change proposed against a package that is not the core,
conformance's body would run a whole-repository audit on a one-line diff, and
the core's patch review is bound to Gerrit. The research
`documentation/contributing/writing-a-skill.rst` asks for before the first line
is done — the five checks the session named (whether the change is correct,
whether it holds on both sides of the version range the package's Composer
constraint declares, whether an idiomatic core API replaces the construct it
changes, whether the commit message matches the repository's own convention, and
whether CI is green and the branch mergeable) were put to the tools the skill
routes to, and the order is written from what came back rather than from the
feedback's list. The draft carries `typo3-dev-companion-status: draft` and there
is no route into it until it is published.
