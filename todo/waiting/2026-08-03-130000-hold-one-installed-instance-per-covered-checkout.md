# Hold one installed instance per covered checkout

**Serves:** scenarios/
**Priority:** normal
**Waiting on:** does the development line get an installation of its own shape,
    or stay declined? It is the one covered line `environment:create` does not
    make. `typo3/cms-base-distribution` publishes no release above `v14.3.0`,
    so it would come from that package's `dev-main` at a dev stability, and the
    core there declares PHP `^8.5` where the containers are pinned to 8.4 —
    another constraint, another stability, another PHP, which is a build rather
    than a version argument. What it would buy is the only line on which this
    server's answers about the next major could be seen at all; what it costs
    is about 260 MB and an installation that changes under the machine daily.

`bin/cli environment:create E-SITE <version>` now makes one installation per
covered version that has a release, each its own DDEV project and directory,
starts one that is already installed instead of building it again, and
`environment:status` is a row per version — `D-EVI-006`, with the numbers. The
12.4, 13.4 and 14.3 lines are made; 13.4 and 14.3 are installed in this
checkout. The step that is left is the development line, and it is the question
above rather than work: answered yes, it is `Environments::refusal()` becoming
a second build (`dev-main`, `--stability=dev`, PHP 8.5, and whether a daily
moving installation is re-made or kept); answered no, that refusal is the
answer and this file is deleted.

What the reading established, so the answer does not need it again:

- The recording is not one of the runs that shares an installation any more.
  `D-DOC-012` moved the second root to the installation written below
  `.fixtures/` on 2026-08-03 and rejected a third, so how many installed
  instances there are is a question about `scenarios/` alone. Five recording
  roots is not the trade `D-DOC-006` measured — it is not on offer.
- What the scenarios ask for is two, not four. Twenty contract cases name
  `E-SITE`; one of them names a version, `SITE-02` on the previous major. None
  names 12.4 or the development line, so the rest are made on the day a case
  asks and `status` is what says they are missing.
- The disk cost was measured on 2026-08-03: 258 MB of files for the two
  installations together, a 133 MB database volume each, and 0 B of unique
  docker image — the per-project DDEV images are tags on layers every project
  on the machine already shares. `.environments/` stays gitignored and
  re-creatable at that size: a build was 34 seconds on a warm Composer cache.
- The version is an argument on `environment:create` rather than a command of
  its own, and nothing makes the whole set. Which lines a machine holds is a
  property of that machine.
