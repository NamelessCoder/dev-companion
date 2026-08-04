# Say what a named check has to be established about

**Serves:** feedback/2026-08-04-175840-task-establish-a-complete-check-layer-for-a.md, feedback/2026-08-04-175856-task-establish-a-check-layer-for-a-typo3-14-3-5.md, feedback/2026-08-04-180154-task-establish-a-check-layer-then-after-the.md, skills/typo3-extension-testing/
**Priority:** normal

`D-SKL-017` decided that
`skills/typo3-extension-testing/references/static-quality.md` names the check
and says what a session establishes about it on the package in hand. Reproduce
the three measurements first, against an installation this repository can make
again: require `helmich/typo3-typoscript-lint` on a 14.3 project and record what
Composer answers, run `symfony/translation`'s `XliffLintCommand` over
locale-prefixed `de.messages.xlf` files with `requireStrictFileNames` at its
default and at `false`, and run the named frontend linters over a package that
ships one script and a stylesheet. Then rewrite the two rows and "Establish one
command per check": the XLIFF answer named, the resolve-before-naming step, the
measure-what-it-finds step, and the isolated tool install as the option where a
tool's constraints cannot meet the project's. What the measurements found about
a version, a constructor default or a sniff's advice goes to `knowledge/` rather
than into the published page — the constants-era advice of `DuplicateAssignment`
and `RepeatingRValue` and the locale-prefix trap are statements that carry a
`since`.
