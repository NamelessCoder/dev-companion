# Run SKILL-13, the case that has no evidence of any kind

**Serves:** SKILL-13
**Priority:** normal

`SKILL-12` was run on 2026-08-07 in `E-CORE` and five of its six surfaces hold;
what it found is written into the case itself. `SKILL-13` is what is left, and
its stopping rules in `typo3-core-patch-checkout/references/checklist.md` are
written against no conflict anybody has hit — so the case needs a change from
review that **really** conflicts with the branch. One arranged to conflict
measures the arrangement.

**Change 84998 is that change.**
`[BUGFIX] Properly evaluate preview requirements for rootline`, patch set 5,
untouched on review since 2025-05-19. Measured on 2026-08-08 against
`.checkouts/main` at `ee251c96d5`: it touches one file and conflicts in that one
file, `typo3/sysext/frontend/Classes/Middleware/PreviewSimulator.php`. One
conflict in one file is what puts the session on the judgement the stopping
rules are for — a wall of them says stop without anybody having to decide.

It stops conflicting the day somebody rebases it, so check before the run and
find another the same way if it has moved. The stale changes are what to look
among:

    curl -s "https://review.typo3.org/changes/?q=status:open+project:Packages/TYPO3.CMS+branch:main+before:2025-06-01&n=40&o=CURRENT_REVISION"

Then fetch a candidate's `refs/changes/<ref>` into `.checkouts/main` and ask git
without touching the working tree:

    git merge-tree --write-tree --merge-base=FETCH_HEAD^ origin/main FETCH_HEAD

A non-zero exit and one `CONFLICT (content)` line is the shape to look for. Of
twelve stale changes tested that way on 2026-08-08, nine conflicted and three
still applied clean, so a candidate is measured rather than assumed.

`bin/cli scenarios:contract SKILL-13` prints the prompt — "Pull down that patch
from review and get it onto current main so I can run the tests against it" —
and it is pasted verbatim with the change named and nothing else.

`E-CORE` is ready: `.checkouts/main` carries its dependencies, this server is in
its `.mcp.json`, and the skills are published where the client reads them.
`install --agent=claude` is what put them there, and the run before it measured
nothing because `install` without that flag writes to `.agents/skills` while
Claude Code reads `.claude/skills`.

**Whoever runs it may not have read the case.** The criteria are what a session
does when the patch stops deciding for it, and a session that has read them
measures its own ability to satisfy them instead. That is why the session
working this repository cannot be the subject, and it is what
`scenarios/readme.md` opens with. Ask for the debrief afterwards — the prompt is
in `documentation/feedback/readme.md`, pasted verbatim and without saying the
session was a case.
