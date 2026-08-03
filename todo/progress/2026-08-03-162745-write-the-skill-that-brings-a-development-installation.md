# Write the skill that brings a package's development installation into existence

**Serves:** feedback/2026-08-03-162745-task-give-a-standalone-typo3-extension.md
**Priority:** normal
**Branch:** todo/write-the-skill-that-brings-a-development-installation
**Claimed:** 2026-08-03
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

Publishing also waits on the four knowledge cards `D-SKL-012` put first, and on
this branch none of them has landed: `165606`, `185545`, `185618` and `185753`
are all in hand elsewhere. What the corpus answers today was measured from a
fixture repository with no installation, and the draft routes to what exists:
`environment-runtime-readers` names the variables `typo3 setup` reads,
`sitepackage-initial-content` and its two neighbours own the seeding,
`project-configuration-files` owns the environment's settings against the
installation's own, `project-build-and-scripts` owns what is not committed. The
Composer root package is the one question that reaches nothing at all — the
query returns no hint and falls back to the id index — so the draft routes it to
`typo3_documentation_lookup` and the installed installer package instead. When
`185618` lands, that step gets the id it should have had.

`knowledge/server-scope.json` is the other thing publishing touches:
`doesNotCover` still says "Running an installation: server and container setup",
which `D-SKL-012` read as running at the container and the webserver. A
published skill that creates a container makes that line worth rereading rather
than quoting.
