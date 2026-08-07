---
date: 2026-08-07T06:54:01+00:00
category: idea
status: closed
closed: 2026-08-07
model: claude-opus-5[1m]
tool: typo3_forge_lookup, typo3_gerrit_lookup, typo3_project_describe, typo3_test_run_guide, typo3_changelog_lookup, typo3-core-issue-triage
directory: /home/benji/projects/typo3-cms
---

# the forge issue note and the seen-it-fail rule are what kept this session honest

## Observation

Task: verify Forge 109572 against a 15.0.0-dev core checkout and write the patch. Recording what worked, because it is what must not be broken later.

typo3_forge_lookup(issue) returned the comments, and one note — "i work on that", posted by another contributor the day before — was the single fact that changed what I recommended. Without it I would have handed over a finished patch onto someone else's work with no warning. The notes are not in the description and nothing else I called would have surfaced them.

typo3_gerrit_lookup(issue) answered empty in one call. That is the cheap outcome the triage skill promises, it cost nothing, and it settled a question the verdict depended on.

typo3_test_run_guide returned the targeted runTests.sh forms including -d sqlite|mariadb|postgres and -b docker, plus the warning that a suite reporting success over no files is not a green. I ran every check on all three databases because of that option list, which is how I later proved the defect was not postgres-specific when the user challenged it. And the green-over-nothing warning caught a DataHandler test of mine that passed over zero rows — I added a row count because of that sentence and found the test had created nothing.

typo3_changelog_lookup matched nothing for my three-word query but returned termCounts and termSubsets, so the retry was targeted rather than guessed. A lookup that tells you which part of your query does reach entries is worth more than one that just says zero.

typo3_project_describe told me this is a core checkout with a DDEV project on PHP 8.5 and that the only declared composer scripts are Gerrit hooks — so I never went looking for a composer test script that does not exist here.

And the triage skill's rule that a reproduction must be seen failing before it is believed is why every assertion I wrote started with an impossible sentinel value. That is what produced the real numbers instead of my expectations, and it is why I could answer "is it really only postgres?" by stashing the fix and measuring it red on all three databases rather than arguing from the code.

## Query

typo3_forge_lookup(issue: "109572"); typo3_gerrit_lookup(issue: "109572"); typo3_project_describe(); typo3_test_run_guide(query: "functional", paths: ["typo3/sysext/extbase/Classes/Persistence/Generic/Storage/Typo3DbQueryParser.php", "typo3/sysext/extbase/Tests/Functional/Persistence/OperatorTest.php"], targetVersion: "15"); typo3_changelog_lookup(query: "Extbase query null") then (query: "extbase null"); Skill(typo3-core-issue-triage)

## Suggestion

Keep the notes in the forge_lookup answer — they carried the one fact that changed the recommendation. Keep gerrit_lookup's empty answer cheap. Keep the DBMS option list and the green-over-nothing warning in test_run_guide; both were load-bearing. Keep the termCounts/termSubsets fallback in changelog_lookup. Keep the seen-it-fail rule in typo3-core-issue-triage — it is the single instruction that most changed how this session behaved.
