# Route the boot branch to the extension-repository layout hint

**Serves:** feedback/2026-08-18-071435-the-hint-answering-the-extension-repository.md
**Priority:** normal

Judged as the ladder's step 2, delivery: the hint is verified, reachable from
the symptom and named in the create branch alone, so a booting session rules it
out by where it is filed — `D-SKL-058` has the evidence and what was rejected.
Add to `## Boot what the repository already declares` in
`skills/typo3-development-installation/SKILL.md` a bullet naming
`typo3_hint_lookup` with `id=extension-repository-installation` where the
repository is an extension with TYPO3 installed beneath it, saying what it owns
there — that the root package is loaded from the Composer root and an empty
`typo3conf/ext/` below the document root is the layout rather than a failed
install — and assert it beside the `id=installation-boot` expression in
`SkillTest::anInstallationIsBuiltInDependencyOrderAndHandsOverOnceItAnswers`.
