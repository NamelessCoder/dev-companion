# no skill covers reviewing an incoming pull request against an extension or sitepackage repository

**Serves:** feedback/2026-08-18-113343-no-skill-covers-reviewing-an-incoming-pull.md
**Priority:** normal
**Branch:** todo/no-skill-covers-reviewing-an-incoming-pull
**Claimed:** 2026-08-18

Judged on 2026-08-18 as the ladder's step 1b and written up in `D-SKL-063`:
nothing owns judging one change proposed against a package that is not the core,
conformance's body would run a whole-repository audit on a one-line diff, and
the core's patch review is bound to Gerrit. The next step is the research
`documentation/contributing/writing-a-skill.rst` asks for before the first line
— establish what such a review actually checks, by putting the five the session
named (whether the change is correct, whether it holds on both sides of the
version range the package's Composer constraint declares, whether an idiomatic
core API replaces the construct it changes, whether the commit message matches
the repository's own convention, and whether CI is green and the branch
mergeable) to the tools the skill would route to, so the order is written from
what this server answers rather than from the feedback's list. What it lands as
is `skills/typo3-extension-patch-review/SKILL.md`, a working name to settle
against the words a user brings, carrying `typo3-dev-companion-status: draft`
and no route into it until it is published.
