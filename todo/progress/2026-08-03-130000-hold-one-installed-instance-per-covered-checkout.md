# Hold one installed instance per covered checkout

**Serves:** scenarios/
**Priority:** normal
**Branch:** todo/hold-one-installed-instance-per-covered-checkout
**Claimed:** 2026-08-03

`bin/cli environment:create E-SITE` makes one installation, on one version, and
`Environments::PROJECT` is a single DDEV name global to the machine. So the
recording, the scenarios and every run that needs a booted TYPO3 share one
instance and one version, and the answer a client on another covered line would
get is shown by nothing.

What is wanted instead: one fully installed DDEV instance per covered checkout —
`knowledge/versions.json` says which — created once, kept, and reusable rather
than rebuilt per run. That means a project name and a directory per version, a
`ddev start` where the containers are stopped rather than a reinstall, and a
command that says which of them exist and which are missing.

Open before the work starts:

- Whether the second recording root then becomes every covered line or stays
  the newest released one. `D-DOC-006` measured what one root costs and settled
  on two; five roots is a different trade and needs the same measurement, not an
  assumption.
- What the disk cost is, and whether `.environments/` stays gitignored and
  re-creatable at that size.
- Whether `bin/cli environment:create` grows a version argument or a new command
  makes the whole set.

Asked for on 2026-08-03, while `documentation/clients/tools.md` was being split
into one page per tool.
