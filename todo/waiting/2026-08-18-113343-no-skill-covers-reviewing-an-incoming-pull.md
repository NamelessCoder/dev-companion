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

    Asked on 2026-08-19 whether the review would be given now, and the answer
    was yes. The first of the two questions of its own was answered in the same
    breath — `typo3-extension-patch-review` is the name — and the second was
    not, so where a review of a public pull request stands against `R-SKL-020`
    is still to be asked.

What the research established is at the foot of `D-SKL-063` rather than here,
because it outlives this card. The queue behind the answer: work the review in,
delete the `metadata` declaration, write the intent that routes to it into
`knowledge/task-intents.json` and the workflow into
`knowledge/server-scope.json` in the same commit, check the listing budget
`D-SKL-026` measures, and buy the baseline run `D-SKL-035` asks of a new skill.

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
