---
description: >-
  The JavaScript and CSS the core commits beside the sources they are built from: reading a change to one without building anything, rebuilding one where nothing of yours is at risk, and resolving one a backport conflicted in.
whenToUse: >-
  When a change touches Build/Sources/TypeScript or Build/Sources/Sass together with the generated file below Resources/Public/ that belongs to it, and the question is whether the committed file carries the source change, how to produce it after an edit, or what to do with a backport that came back with conflict markers in it. The checkGruntClean suite answers the first of those and stages the whole working tree on the way, so it is no way there from a checkout holding work of your own.
hints:
  - backend-typescript
---

# The Build Output the Core Commits

## Where the Committed Build Output Comes From

`Build/Sources/TypeScript/<ext-key>/` compiles and minifies into
`typo3/sysext/<ext>/Resources/Public/JavaScript/`, and `Build/Sources/Sass/`
into the same packages' `Resources/Public/Css/`. The source directory carries
the extension key with its underscores written as dashes, so `rte-ckeditor` is
built into `rte_ckeditor`. Both outputs are committed, which is why a patch that
edits a source carries the rebuilt file in the same commit and a review of
either half is a review of both. The core's own `AGENTS.md` states the rule that
follows from it: nothing below `Resources/Public/JavaScript` or
`Resources/Public/Css` is edited by hand.

The generated file is minified onto one line. EXT:form's `view-model.js` on 14.3
is twelve lines of licence header and one line of 24,240 characters, and
`backend.css` carries one of 33,918 (measured 2026-08-26). So `git diff` prints
that line twice and tells a reader nothing, which is what the next section is
for.

## Reading a Minified Diff Without Building

Split both sides on the separators the minifier keeps, and diff what comes out:

```bash
P=typo3/sysext/form/Resources/Public/JavaScript/backend/form-editor/view-model.js
tokenise() { sed 's/\([;{}]\)/\1\n/g'; }

# two revisions of it, the commit's parent against the commit
diff <(git show "$parent:$P" | tokenise) <(git show "$commit:$P" | tokenise)

# the committed one against the one a build has just written
diff <(git show "HEAD:$P" | tokenise) <(tokenise < "$P")
```

Measured on 14.3's `06dc629259`, a one-token null-safety fix: four lines of
output, `l=a.querySelector(…)` against `l=a?.querySelector(…)`.

Where the change lands in a token line too long to read, put `,` into the
character class as well: on that file the longest line falls from 1,961
characters to 263.

What this settles is whether the committed file carries the source change and
nothing besides it. It runs no build and writes nothing.

## Rebuilding It Where Nothing of Yours Is at Risk

Where the file has to be produced rather than read — the source was edited and
nothing built it, or the committed output is suspected of being stale — the
build runs in a worktree branched off the target branch, and your own checkout
is untouched:

```bash
git worktree add --detach ../build-check origin/14.3
cd ../build-check
CI=true ./Build/Scripts/runTests.sh -s "$suite"
git status --porcelain
```

`$suite` is the branch's frontend build, and which one that is belongs to the
branch rather than to this page: `typo3_test_run_guide`, given the changed paths
and that branch as `targetVersion`, names it and prints the whole command. It
runs npm inside `Build/`, whose `package.json` and `package-lock.json` are
tracked, so a fresh worktree needs no `composerInstall` first.

An empty `git status` says the branch's committed output is what its sources
produce, and therefore that anything the next build alters is yours. Measured on
2026-08-26 at the tips of 14.3 and main: both came back empty, and neither run
rewrote `Build/package-lock.json`.

Then apply the source half of the work in that worktree, build again, and read
the diff above. Reverting one commit's TypeScript hunk on 14.3 and rebuilding
altered two files: the source, and the generated file belonging to it.

Two things a build touches besides its output. `exec:stylefix` runs
`stylelint --fix` over `Build/Sources/Sass/**/*.scss`, so a Sass source can come
back rewritten, and `npm install` can rewrite `Build/package-lock.json`. Stage
what your own work owns and nothing else.

`git worktree remove ../build-check` when the answer is in hand. The
`node_modules` the build installed below `Build/` is gitignored and goes with
it.

## Output No Source Produces Any More

A build overwrites what it emits and deletes nothing below `typo3/sysext`, so a
generated file whose source was renamed or deleted survives it and leaves
`git status` empty. That is the one question the procedure above does not
answer, and it is why `checkGruntClean` deletes every generated `.js` before it
builds. In the same worktree it is asked by deleting them there:

```bash
find typo3/sysext -name '*.js' -not -path '*/theme_camino/*' -not -path '*/Fixtures/*' -not -path '*/Documentation/*' -delete
CI=true ./Build/Scripts/runTests.sh -s "$suite"
git status --porcelain
```

That is `checkGruntClean`'s own body without the `git add *` it ends in.
Measured on 2026-08-26 in a worktree at 14.3's tip: every deleted file came back
identical to the committed one, `git status` included nothing.

Running the suite itself there does not work. The same run's build succeeded and
each of its git calls failed with
`fatal: not a git repository: <the worktree's gitdir>`, because that directory
sits outside the one the container mounts, and the suite reported FAILURE over a
tree that was clean.

## A Backport That Conflicts in a Generated File

A cherry-pick onto a release branch conflicts in the generated file whenever the
two branches' sources have diverged anywhere in the same module, and it does so
where the source half applies cleanly. Gerrit's "Cherry pick" action commits the
conflict markers instead of refusing: change 95412's patch set 1 carried them in
EXT:form's `view-model.js`, the only record is a change message reading "The
following files contain Git conflicts", and CI answered `Verified-1` on it (read
from the review server on 2026-08-27). So a backport made that way is searched
for markers before anything else is done with it.

What resolves the conflict is the target branch's own build:

1. Put the target branch's committed file back —
   `git checkout --ours -- <the generated path>` while the cherry-pick is
   stopped, or `git checkout <the target branch> -- <the generated path>` where
   the markers are already committed in a patch set you fetched.
2. Build in a worktree branched off the target branch, as above, with the source
   half of the patch applied.
3. Stage the generated file the patch owns and nothing else.

The tokenised diff is what shows the resolution is right: exactly the change the
source makes, and no other generated file moved. It is also how a stale
committed output on the target branch is discovered.

## The Same Module Built on Two Branches

Identifier mangling is a property of the whole module, so one expression carries
a different variable name on each branch. Measured on 2026-08-26 for EXT:form's
`view-model.js`: 404 token lines on 14.3 against 426 on main, 364 of them
differing, where the sources differ in a handful of lines. The same import is
bound to `x` on one branch and to `k` on the other.

That is why neither side of the conflict resolves it. The newer side puts its
whole module onto the older branch while presenting as a resolved file, and the
older side keeps the output the fix was meant to replace.
