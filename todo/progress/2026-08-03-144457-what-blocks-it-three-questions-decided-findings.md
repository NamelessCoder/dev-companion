# Say where an idiom precedent is settled, beside the behaviour question

**Serves:** feedback/2026-08-03-144457-what-blocks-it-three-questions-decided-findings.md
**Priority:** normal
**Branch:** todo/what-blocks-it-three-questions-decided-findings
**Claimed:** 2026-08-03

`skills/base.md`'s `## When the lookups run out` answers a behaviour question
with the class that implements it and the one it inherits from. *Is
`#[Autowire(lazy: true)]` established in core* is not that shape: it is a sweep
for call sites with no class to start at, and a reviewer asks it of every
alternative it proposes. The boundary it falls outside is stated only in
`knowledge/server-scope.json` — *PHP source as code: a method signature, whether
a class or member is @internal or public API, an implementation to copy* — which
`typo3_server_scope` alone returns and no review order calls. The placement is
the first step: a clause in that base section, whose word budget `D-SKL-001`
watches, or a sentence in `skills/typo3-core-patch-review/SKILL.md` beside the
changelog-precedent step, which already says the checkout holds the precedent
and asks the review to say which of the two answered. Both are skill contracts,
so `documentation/clients/writing-a-skill.md` and `SkillTest` come with the
change, and `D-SKL-004` carries the reading that queued it.
