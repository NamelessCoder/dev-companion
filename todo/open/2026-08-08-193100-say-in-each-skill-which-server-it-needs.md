# Say in each skill which server it needs, in the field the standard has for it

**Serves:** R-SKL-008
**Priority:** high

All twelve `SKILL.md` files link `references/base.md`, and no skill directory in
this repository contains that file — `Installer::BASE` writes it at publication,
one copy per skill, and `Installer::digest()` hashes `skills/base.md` once
because it is the only copy here. That is correct for the installer and wrong
for every other way the tree is read. This package is public at
`benjaminkott/typo3-cms-mcp`, and a tree installer such as `npx skills add`
copies `skills/` as it stands: twelve skills whose first instruction is a link
that 404s, whose routing names twelve `typo3_` tools nothing has connected, and
any draft `Installer::skills()` would have filtered. The guard in `base.md` is
written for a session whose tools do not answer; it is not written for a session
whose base was never delivered.

The standard has the field for saying so. `agentskills.io` defines six
frontmatter fields — `name` and `description` required, `license`,
`compatibility`, `metadata` and `allowed-tools` optional — and its reference
validator treats unknown keys as a hard error against that closed allowlist.
`compatibility` is a string capped at 500 characters. Every one of the twelve
carries `name` and `description` and nothing else today, measured 2026-08-08, so
they are conformant and the field is free.

Write a `compatibility` line into each of the twelve naming the server the skill
needs and how it is installed, and hold it with an assertion over the directory
the way the other nine rules are held. It names no version, so the prohibition
in [writing-a-skill.md](../../documentation/clients/writing-a-skill.md) stands —
the package name is what the reader needs and the constraint is what they must
not be given. Shopware and WordPress both use this field for the same purpose.

Two things this does not settle, and neither should be guessed at here. The
first is `status: draft`, which is **not** in the standard's allowlist and makes
a draft fail conformance the moment the tree is read by anything but this
installer; `metadata: {status: draft}` is the spec-legal form and moving it is a
change to `SkillTest::aDraftSaysSoInItsOwnFrontMatter` and to
`Installer::skills()`. The second is whether the tree should be installable at
all: a `compatibility` line tells a reader what is missing, and it does not put
`references/base.md` there. Whether this repository wants a tree install to
work, to fail loudly, or to be nothing it supports is a decision, and the answer
decides whether the twelve base copies get committed — which the digest and the
single-copy rule currently both refuse.
