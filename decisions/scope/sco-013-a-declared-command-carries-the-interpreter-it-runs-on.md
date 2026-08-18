---
id: D-SCO-013
date: 2026-08-19
status: open
---

# D-SCO-013 — A declared command carries the interpreter it runs on, Node as well as PHP

**The Node a repository declares, the Node its CI sets up and the Node its
environment carries join the three PHP numbers `typo3_project_describe` already
states.**

A sitepackage maintenance session reported that five sixths of it were Node, npm
and GitHub Actions work with no TYPO3 surface at all, and asked whether that
surface is meant to be in scope. Its defect was a Node major difference between
the machine and CI. That is the one thing about the surface this server was
already built to say for the other interpreter, and the npm half of its own
command list says none of it.

## Evidence

- `Project::commands()` reads `package.json` for `scripts` and for nothing else:
  no `engines`, no `.nvmrc` beside it. `node` occurs once in `src/`, in
  `DocumentationPreview`, where it runs this repository's own renderer.
- The same answer carries `phpConstraint`, `corePhpConstraint`,
  `environment.php` and the `phpRelation` between them, and
  [`D-ANS-082`](../answers/ans-082-the-project-answer-states-how-its-three-php-numbers-relate.md)
  is why: the line stated the numbers and the relation was the defect.
  `inEnvironment: above` reads "nothing configured here ever executes the
  version this project promises". The npm half of the same command list has no
  such sentence and no numbers to build one from.
- The reported bug was fantasticon's EOT generator writing 65536 bytes instead
  of 5828, because Node 24.19 raised `Buffer.poolSize` from 8 KiB to 64 KiB and
  the generator copies the pool rather than the view. Local Node was 24.16.
  Nothing here would ever state that; what it could have stated is that the two
  Nodes are different majors of each other's minor.
- Two tools send the caller to a CI configuration that nothing here reads.
  `TaskGuide` outside the core: "the scripts in its composer.json, its
  package.json, and its CI configuration are where its own suites are declared."
  `ScriptLookup` says the same in its own words.
- Three sessions read `.github/` by hand. `2026-07-29-094156` established the
  project's real checks from `.github/workflows/ci.yml` after the guide offered
  it `runTests.sh`; `2026-08-18-070333` listed `.github/` among four Bash calls
  it made over the files this tool reads; and this one.
- The `extension-asset-build` hint already carries the rule the feedback's
  second half asks for — "Decide whether generated assets are committed … The
  extension's package.json and CI are the executable record of that decision" —
  and `bin/cli hints:probe` returns it first for the feedback's own query. The
  rule is there and it is reachable. What nothing answers is the fact about the
  repository in front of the caller.

## Decided

- The boundary was never TYPO3's API surface. DDEV's hooks, its pull recipes,
  the Composer scripts and the PHP an environment runs are all covered because
  they are what the repository declares about how it runs itself, which is the
  exception the scope's own operations entry already names. A sitepackage's
  build is declared in those same files, so it is inside that boundary and
  reading it needs no new principle.
- What is built is the interpreter and not the pipeline: `engines.node` and
  `.nvmrc` as the repository declares them, the Node an `actions/setup-node`
  step sets up, and `nodejs_version` where a DDEV project states one — with the
  relation between them said out loud, in the shape `phpRelation` already has.
- Reading `.github/workflows/*.yml` is the one new source, and it is read for
  that field rather than for the workflow. What CI asserts as a whole is
  declined: a workflow file is one `Read` for the caller with no trap in it, so
  [`D-FBK-027`](../feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)'s
  measure does not clear it.
- Which of the build's outputs are committed artifacts is declined as well. That
  is tracked files held against `.gitignore`, which is git state and the first
  entry of the scope's `doesNotCover`, and the hint above already tells the
  caller to settle it in the repository.

## Assumed

- That the interpreter is where the cost sits and the rest of the pipeline is
  not. One session lost most of itself to one version difference; nothing in the
  corpus reports a session losing time to not knowing which bundler a repository
  builds with.
- That a workflow states its Node readably often enough to be worth the source.
  `actions/setup-node` takes `node-version` inline, `node-version-file`, or a
  matrix expression, and only the first is a lookup rather than a resolution.

## Wrong if

- The numbers agree in every repository this is run in, so the relation never
  tells a caller something the one number it already had did not.
- A session reports reading a workflow file by hand for something other than the
  interpreter — what a job installs, what it caches, which matrix it runs —
  which would say the field was cut one short rather than that the surface is
  out.
- The Node is resolved out of a matrix or a variable and stated as the one CI
  runs. A wrong number carries this server's authority, which is worse than the
  silence it replaced:
  [`D-FBK-050`](../feedback/fbk-050-a-packages-release-policy-is-asked-rather-than-derived.md)
  declined a field for that reason on the same board.
