---
date: 2026-08-24T14:04:21+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_extension_describe
directory: /home/benji/projects/ext-usercentrics
---

# Five tools I loaded or was pointed at and never called, and the one schema I guessed wrong

## Observation

Task: one extension version for TYPO3 v13.4 and v14, with tests and a browser-verifiable installation. This reports what the server cannot see: the calls I did not make.

Trimmed to the one of the five that is still open. `typo3_configuration_lookup` is answered on 2026-08-25 by `D-KNW-120`, which puts the call into `project-configuration-files`, the hint that had already diagnosed the 500 for me. The three others are judged there too and produced no lever: I report no wrong answer from skipping `typo3_documentation_lookup`, I price the `typo3_test_run_guide` omission at seconds myself, and the `typo3_commit_message_guide` call I guessed the schema of was refused with both ways in named and succeeded on the retry.

typo3_extension_describe. I loaded its schema and never called it, in a session entirely about one extension. The server's own base.md makes it step 2 of a fixed order — "for each extension in scope — what it registers, and what it ships beside that: its manual, its README, its test layers, its XLF files". I skipped it because by the time I read that order I had already read every file in the extension by hand. What it answers that I established the slow way: which Fluid root directories the extension ships, its test layers, and that it has no XLF files at all. It also reports "a file it ships that core has stopped reading" including "ext_emconf.php beside a composer.json declaring neither providesPackages nor a version" — which was a real defect in this extension that I found through a different route, the extension-manifest hint. One call would have handed it to me at the start.

## Query

Not called: typo3_extension_describe (extension="usercentrics" would have been the call), in a session where its schema was loaded and base.md's fixed order named it as step 2.

## Suggestion

typo3_extension_describe is the one worth forcing. Its answer includes a defect check nobody would think to ask for — the four files core has stopped reading — and in this session that check was a real finding reached by another route. If typo3_project_describe already knows the extensions that are the project's own, and it does, consider folding the "files core has stopped reading" verdict into that first answer, so a session that skips step 2 still gets it.
