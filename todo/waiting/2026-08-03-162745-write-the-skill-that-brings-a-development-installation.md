# Write the skill that brings a package's development installation into existence

**Serves:** feedback/2026-08-03-162745-task-give-a-standalone-typo3-extension.md
**Priority:** normal
**Waiting on:** the review of the draft in
    `skills/typo3-development-installation/`, asked for on 2026-08-03 and
    unanswered: does the order match how the task is really done, which step is
    missing, which one is wrong, and what does it claim that is not true here?
    Publishing is the second half and needs the answer, because the copy in
    somebody else's project is not corrected by the next release.

The draft is written and `composer ci` is green on it. What is left is the
review, and then publishing: add it to `Installer::SKILLS`, to the lists in
`tests/Smoke/InstallerTest.php` that read the published directory back, and a
pointer from `typo3-extension-testing` to it for a project with no runnable
installation — that pointer may not be written before the skill is published,
because it would name a skill nobody has installed. Then run the installer in
the checkout that plays the environment, which `todo/reference/` names.

The four knowledge cards `D-SKL-012` put before publishing have all landed —
`165606`, `185545`, `185618` and `185753` — so the review is the only thing
publishing still waits on. Two of them change the draft rather than merely
unblocking it, and that is the step after the answer. The Composer root package
was the one question reaching nothing when the draft was written, so step 1
routes to `typo3_documentation_lookup` and the installed installer package;
`extension-repository-installation` in `knowledge/hints/extension.json` answers
it now and is first on that query, which is the id that step should carry.
`installation-setup` in `knowledge/hints/configuration.json` is the same case for
step 3, which currently names only `environment-runtime-readers` beside a
documentation lookup for the options. What the draft already routes to holds:
`sitepackage-initial-content` and its two neighbours own the seeding,
`project-configuration-files` the environment's settings against the
installation's own, `project-build-and-scripts` what is not committed.

`knowledge/server-scope.json` is the other thing publishing touches:
`doesNotCover` still says "Running an installation: server and container setup",
which `D-SKL-012` read as running at the container and the webserver. A
published skill that creates a container makes that line worth rereading rather
than quoting.
