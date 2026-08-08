# Move what a draft declares into a field the standard allows

**Serves:** R-SKL-006
**Priority:** normal

A draft says so with `status: draft` in its own front matter, and `status` is
not one of the six fields `agentskills.io` defines — `name` and `description`
required, `license`, `compatibility`, `metadata` and `allowed-tools` optional,
read there on 2026-08-08. A validator run over a draft therefore refuses the
file over the line that holds it back, which is the one skill in the directory
nobody has reviewed. Nothing fails today because the directory currently holds
no draft, and the next one brings the defect with it.

`metadata: {status: draft}` is the spec-legal form of the same declaration. Move
it there: `Installer::draft()` reads the block with a regex over `^status:`,
`SkillTest::aDraftSaysSoInItsOwnFrontMatter` asserts the same spelling twice,
and [writing-a-skill.md](../../documentation/clients/writing-a-skill.md) states
it in two places — the section on the draft being read by somebody, and the one
on publishing. Read the block with the parser `SkillTest::frontMatter` already
uses rather than a second regex, so what the installer sees and what a client
sees are one reading.

The front matter is the standard's from this side too, which nothing asserts:
`SkillTest::everySkillSaysWhichServerItNeeds` parses the block and reads one
field out of it, and a key outside the allowlist passes that. Whether to hold
the allowlist closed is the same question and is settled by the same move — a
draft is the only key that was ever outside it.
