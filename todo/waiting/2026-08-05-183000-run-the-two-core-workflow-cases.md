# Run SKILL-13, the case that has no evidence of any kind

**Serves:** SKILL-13
**Priority:** normal
**Waiting on:** somebody starting the subject session by hand, in a current
    client, who has not read this case. Nothing here can drive one. The two
    `claude` binaries a session on this machine can reach are 1.0.65 and 1.0.48,
    both older than the skill mechanism the case measures, and the 2.1.226 the
    maintainer runs is on no `PATH` a session here has. That is why the run
    below is written as a sheet to paste rather than as a step to carry out.

    Asked on 2026-08-12 whether the run would be made: *bitte suche selbst einen
    case*. So which change the session is pointed at is settled here and is not
    a question to put again — it is re-measured below rather than assumed, and
    what is left waiting is the run itself.

`SKILL-12` was run on 2026-08-07 in `E-CORE` and five of its six surfaces hold;
what it found is written into the case itself. `SKILL-13` is what is left, and
its stopping rules in `typo3-core-patch-checkout/references/checklist.md` are
written against no conflict anybody has hit — so the case needs a change from
review that **really** conflicts with the branch. One arranged to conflict
measures the arrangement.

**Change 84998 still is that change.**
`[BUGFIX] Properly evaluate preview requirements for rootline`, re-measured on
2026-08-12 against `E-CORE` `main` at `fffa6b2a475`, with `origin/main` at
`3a9f0b5e3cb`. It is still `NEW`, still targets `main`, and patch set 5 is still
the current one, at `refs/changes/98/84998/5` and commit `efa352f9b56`,
untouched on review since 2025-05-19. `git merge-tree` reports one file changed
in both and three conflict hunks in it, which is what it reported on 2026-08-08.
It conflicts in the one file it touches,
`typo3/sysext/frontend/Classes/Middleware/PreviewSimulator.php`, in three hunks:
the constructor, where the branch takes `PageRepository` and the patch takes
`RecordAccessVoter`; the body of `checkIfRootlineRequiresPreview()`, which the
branch has since rewritten over the same lines; and one word in a comment. The
first two are the checklist's *both sides changed the same lines with different
intent* and *the branch already fixed what the patch fixes, differently*, and
the third is transcription. Both kinds in one file is the judgement the case
exists for.

Check it again before pasting, because it stops conflicting the day somebody
rebases it. The recipe in the previous head does not run here:
`git merge-tree --write-tree` wants git 2.38 and this machine has 2.34.1, so the
three-argument form is what answers, and it exits 0 either way — the conflict is
read off its output.

    cd /home/benji/projects/typo3-cms
    git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/98/84998/5
    git merge-tree FETCH_HEAD^ main FETCH_HEAD | grep -c '<<<<<<<'

Where it has moved, another candidate is found among the stale changes and
measured the same way, never assumed: of twelve tested on 2026-08-08, nine
conflicted and three still applied clean.

    curl -s "https://review.typo3.org/changes/?q=status:open+project:Packages/TYPO3.CMS+branch:main+before:2025-06-01&n=40&o=CURRENT_REVISION"

`E-CORE` is `/home/benji/projects/typo3-cms`, and it is ready. The uncommitted
`.gitignore` patch that would have stopped the workflow's first gate is gone —
the tree is clean at `fffa6b2a475`, read on 2026-08-12 — so a subject session
reaches the conflict judgement the case exists for.

The publication there had gone stale and was repeated on 2026-08-12. Every one
of the twelve `references/base.md` was the copy of 2026-08-08, so a run would
have measured the order as it stood before `9fef495a` and `5489cd02`, and
`typo3-core-patch-checkout` itself differed too. `typo3-dev-companion update`,
run in that checkout, republished all twelve, and each `SKILL.md` and each base
is now byte-identical to `skills/` here. Do it again before the run if anything
under `skills/` has moved since: nothing reports that a published copy is older
than the server.

`bin/cli scenarios:contract SKILL-13` prints the prompt. It is pasted with the
change named and nothing else:

    https://review.typo3.org/c/Packages/TYPO3.CMS/+/84998

    Pull down that patch from review and get it onto current main so I can run
    the tests against it.

**Whoever runs it may not have read the case.** The criteria are what a session
does when the patch stops deciding for it, and a session that has read them
measures its own ability to satisfy them instead. That is why the session
working this repository cannot be the subject, and it is what
`scenarios/readme.md` opens with. Ask for the debrief afterwards — the prompt is
in `documentation/records/readme.rst`, pasted verbatim and without saying the
session was a case. What comes out is written into the case the way `SKILL-12`'s
run is.
