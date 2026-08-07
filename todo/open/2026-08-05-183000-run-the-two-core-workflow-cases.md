# Run SKILL-13, the case that has no evidence of any kind

**Serves:** SKILL-13
**Priority:** normal

`SKILL-12` was run on 2026-08-07 in `E-CORE` and five of its six surfaces hold;
what it found is written into the case itself. `SKILL-13` is what is left, and
it is the harder of the two to arrange: its stopping rules in
`typo3-core-patch-checkout/references/checklist.md` are written against no
conflict anybody has hit, so the case needs a change from review that **really**
conflicts with the branch. One arranged to conflict measures the arrangement.

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
