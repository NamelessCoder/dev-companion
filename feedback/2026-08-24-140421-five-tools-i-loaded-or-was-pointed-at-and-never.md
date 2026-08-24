---
date: 2026-08-24T14:04:21+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_extension_describe, typo3_documentation_lookup, typo3_configuration_lookup, typo3_test_run_guide, typo3_commit_message_guide
directory: /home/benji/projects/ext-usercentrics
---

# Five tools I loaded or was pointed at and never called, and the one schema I guessed wrong

## Observation

Task: one extension version for TYPO3 v13.4 and v14, with tests and a browser-verifiable installation. This reports what the server cannot see: the calls I did not make.

typo3_extension_describe. I loaded its schema and never called it, in a session entirely about one extension. The server's own base.md makes it step 2 of a fixed order — "for each extension in scope — what it registers, and what it ships beside that: its manual, its README, its test layers, its XLF files". I skipped it because by the time I read that order I had already read every file in the extension by hand. What it answers that I established the slow way: which Fluid root directories the extension ships, its test layers, and that it has no XLF files at all. It also reports "a file it ships that core has stopped reading" including "ext_emconf.php beside a composer.json declaring neither providesPackages nor a version" — which was a real defect in this extension that I found through a different route, the extension-manifest hint. One call would have handed it to me at the start.

typo3_documentation_lookup. Never called. typo3-extension-testing explicitly says to use it "when dependency setup, bootstrapping, fixtures, browser configuration, or an API needs confirmation". I confirmed all of those by reading vendor sources and core git tags instead. I assumed a manual lookup would answer for documented surfaces and not for the PHP-level questions I had — that assumption held for the ones I checked but I never tested it.

typo3_configuration_lookup. Schema loaded, never called. The right moment was a frontend answering HTTP 500 with an empty log, where the question was whether SYS/trustedHostsPattern was set. I read the generated additional.php and curled the page instead. The tool exists precisely for "the value as it is at runtime after every extension has had its say".

typo3_test_run_guide. Named by typo3_task_guide's nextTools as "for the targeted invocation form". Never called. I ran whole suites every time, including a functional suite that takes about five seconds per run — cheap here, which is why I never reached for it, but I did not know that before I started.

typo3_commit_message_guide. My first call passed only workflow="project" and came back with InputValidationError: missing message, missing changeType. I had guessed the schema because ToolSearch had loaded several other tools of this server and I assumed this one was among them. It was not. One round trip lost, entirely my error, but worth recording as the one schema I guessed at. The second call, with changeType and summary and body, answered well and its only check was a subject-length warning I acted on.

## Query

Not called: typo3_extension_describe (extension="usercentrics" would have been the call), typo3_documentation_lookup, typo3_configuration_lookup (path "SYS/trustedHostsPattern"), typo3_test_run_guide. Failed then repeated: typo3_commit_message_guide{workflow:"project"} → InputValidationError, then typo3_commit_message_guide{workflow:"project",changeType:"TASK",summary:...,body:...} → answered.

## Suggestion

typo3_extension_describe is the one worth forcing. Its answer includes a defect check nobody would think to ask for — the four files core has stopped reading — and in this session that check was a real finding reached by another route. If typo3_project_describe already knows the extensions that are the project's own, and it does, consider folding the "files core has stopped reading" verdict into that first answer, so a session that skips step 2 still gets it. For typo3_configuration_lookup, the installation-exception-output hint would be the place to name it: where a 500 with an empty log is the symptom, the question is a runtime configuration value, and that is this tool.
