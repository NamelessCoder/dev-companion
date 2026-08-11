---
date: 2026-08-10T18:24:35+00:00
category: idea
status: closed
closed: 2026-08-11
model: claude-opus-5
tool: typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# a path-scoped suite list goes stale when the patch grows a file in a new domain, and nothing says...

## Observation

Task: review and then rework TYPO3 core Gerrit change 95163, which began as a Sass/Fluid/generated-CSS change and grew a TypeScript file during the rework.

I called typo3_test_run_guide once, early, with the eight paths the patch touched at that time (five .scss, two .fluid.html, one generated .css). The answer was correctly narrowed to domains css and fluid and returned functional, lintHtml, build, lintScss, build-css, e2e, e2e-prepare, e2e-browser, composerInstall, npm. That list was accurate for the patch as it stood, and its `CI=true` note plus the exact `runTests.sh -s npm -- run build-css` string were immediately useful — I had already failed one round trip on `npm -- run build:css` before calling it.

Mid-session the patch grew Build/Sources/TypeScript/backend/module-docheader.ts and a generated .js. The suite list I was holding named neither lintTypescript nor unitJavascript, correctly, because no .ts path had been in the call. I did not re-call the guide with the new path. Instead I ran `./Build/Scripts/runTests.sh -h` and grepped runTests.sh for "wtr|test-runner" to discover that the JavaScript unit suite is called unitJavascript. That is two round trips against the checkout for something the server owns and would have answered.

Nothing prompted the re-call. The answer reads as a complete list of the suites for this change, not as a snapshot of a path set that can change under it — and in a rework session the path set changes routinely.

## Query

typo3_test_run_guide paths=[5 .scss, DocHeader.fluid.html, backend.css, MainLayout.fluid.html] targetVersion=15.0 — then the patch grew Build/Sources/TypeScript/backend/module-docheader.ts and the guide was never re-called.

## Suggestion

Have the answer say what it is scoped to and when it stops holding: one line naming the domains it derived from the paths, and that a path in another domain means asking again. It costs a sentence and it is the difference between one extra call to this server and two greps through runTests.sh. Optionally name the suites that were withheld for lack of a matching path — "no typescript path was given, so lintTypescript and unitJavascript are not listed" — which turns the omission into information rather than an apparent absence.
