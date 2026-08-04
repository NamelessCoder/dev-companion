# A task that asks for a repository to be put right reaches no skill

**Serves:** R-GUI-006
**Priority:** normal
**Waiting on:** the review of the draft in
    `skills/typo3-extension-remediation/`, asked for on 2026-08-04 and
    unanswered: does the order match how the task is really done, which step is
    missing, which one is wrong, and what does it claim that is not true here?
    Four of its choices are the ones a reading here cannot settle. Is
    `typo3-extension-remediation` the name — the description is what a client
    routes on and the name is only what other skills call it by, but it is a
    word in a published file no release corrects. Is the worklist a committed
    file in the maintainer's own repository, which is what was asked for and is
    also this server writing into somebody's history, and if so where does it
    live and what becomes of it once the list is empty. Is committing the list
    before any change the checkpoint it is meant to be, or one commit somebody
    has to undo. And does step 9 — an item with no owning workflow worked here —
    belong at all, or is a finding nobody owns one that goes back to the report.
    Publishing needs the answers, because the copy in somebody else's project is
    not corrected by the next release.

The draft is written and `composer ci` is green on it. What is left is the
review, and then publishing: add it to `Installer::SKILLS`, to the lists in
`tests/Smoke/InstallerTest.php` that read the published directory back, and then
run the installer in the checkout that plays the environment, which
`todo/reference/` names.

Publishing also gains the route into it, and that route is what this card was
opened for. `knowledge/task-intents.json` carries the skill that owns each
recognized task since
[`D-SKL-013`](../../decisions/task-skills/skl-013-the-guide-names-the-skill-that-owns-the-task.md),
and the intent this needs does not exist yet: the four wordings measured on
2026-08-04 match nothing, and the `audit` intent must not be widened to catch
them, because every needle it has is a word for looking and
[`R-GUI-006`](../../requirements/guides/gui-006-a-review-is-not-answered-with-a-checklist-for-changing-something.md)
is what keeps a change request out of a review. So the new intent is written
with the change words and takes `skill` rather than `skillCore` — putting a
repository right is not core work — and it may not be written before the skill
is published, because `SkillTest::everySkillNamedInKnowledgeIsPublished` holds
every name there to `Installer::SKILLS`.

The boundary is where this draft is most likely to be wrong, and it is narrow on
purpose. `typo3-extension-conformance` already owns the surfaces, the evidence a
finding rests on, the severity and who each finding goes to, and it already
hands over for the changes and keeps the re-check. What the draft adds is the
entry point, the order across many findings and staying with the list — and a
draft that re-derives the findings is the first **Wrong if** of
[`D-SKL-016`](../../decisions/task-skills/skl-016-acting-on-a-conformance-report-earns-a-task-skill-of-its-own.md)
happening. That entry also records what nobody has measured: no filed session
has brought this wording, so the domain is earned by a hole this repository's
own decision made rather than by a run, which
[writing-a-skill.md](../../documentation/clients/writing-a-skill.md) asks for.
