---
date: 2026-08-08T22:44:26+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3_rule_lookup, typo3-core-issue-triage
directory: /home/benji/projects/typo3-cms
---

# four answers that changed what I did, recorded so they are not refactored away

## Observation

Task: triage an old core bug and fix it — this is the other half of the debrief, the answers that changed what I did rather than the gaps. Filed because a later refactor cannot see which sentences were load-bearing.

1. `typo3_test_run_guide` said the suites run inside a container against the vendor/ of the directory the script is started from, and to prefix runs with `CI=true`. This checkout has a `vendor/` and a `bin/phpunit` on disk. Without that answer I would have reached for the host binary, got a green under whatever PHP the machine carries, and reported a verification nobody could repeat. Instead every run in this session went through `CI=true ./Build/Scripts/runTests.sh`, on the container's PHP 8.5 against sqlite, and I said so in the verdict.

2. `typo3_rule_lookup` settled the changelog question with "A casual bug fix owes no entry, because its commit message carries the information. Demanding one of a BUGFIX that removes nothing public is a review defect of its own." Left to my own judgement I would have written an `Important-58705-*.rst` to be safe, which the same paragraph names as a defect. The same answer told me `git branch -r` does not answer the release-targets question because it reaches back to `TYPO3_3-6` — I had run exactly that command one turn earlier and was about to read the branch list off it.

3. `typo3_forge_lookup(issue=…)` returning a populated `reviews` array is what let me rule out #82228 in one read: it named the abandoned change 53819 without a Gerrit search, and that patch's diff showed the request was for behaviour the `m` modifier already provides. On a stale issue that field is worth more than the description.

4. `references/base.md` in typo3-core-issue-triage: "A green that ran over no files is not a green." I acted on it — every suite result in this session is reported with what it inspected (25 tests, 3613 tests, 235 tests, 6300 files, 6265 files) rather than with the SUCCESS banner. The same file's "the reproduction has to be seen failing before it is believed" is why I wrote the functional test first and ran it red (`'Error: no file object'`) before touching `GifBuilder.php`. Both are the reason the verdict on #58705 is a reproduction rather than a reading of a method.

## Query

typo3_test_run_guide(paths=["typo3/sysext/frontend/Classes/Imaging/GifBuilder.php","typo3/sysext/frontend/Tests/Functional/Imaging/GifBuilderTest.php"], targetVersion="15", query="functional"); typo3_rule_lookup(query="bugfix changelog entry obligation and target branches"); typo3_forge_lookup(issue="82228"); typo3-core-issue-triage references/base.md

## Suggestion

Keep these four intact through any rewording. The two that carry the most per sentence are the container/`CI=true` precondition in `typo3_test_run_guide` and the "demanding a changelog entry of a BUGFIX is itself a review defect" sentence in `core/contribution/commit-messages`: both stop a plausible wrong action rather than merely enabling a right one, which is the kind of statement a summarising rewrite drops first.
