# Say what a class count counted

**Serves:** feedback/2026-07-31-172754-typo3-extension-scope-for-bootstrap-package.md
**Priority:** normal

Settle whether `Extension::classes()` should count the whole subtree or only
what sits directly in a `Classes/` subdirectory, then make the answer say which.
`countPhpFiles()` builds a Finder without `depth(0)`, so it counts the subtree,
and `bootstrap_package` is the case that shows the difference: 21 files in
`Classes/Updates/` and six more in `Classes/Updates/Criteria/`, reported as
`Updates (27)`. Read what the section is for before choosing — it stands beside
the registrations an extension makes, so the question is whether a nested helper
is part of the surface being described or noise in it. Whichever way it goes,
both places the number appears have to say it: the rendered `Updates (27)` line
in `ExtensionScope`, which carries no qualifier at all, and the schema's
`PHP files below it`, which reads as "directly in" as readily as "anywhere
under". The same method counts every kind under `Classes/`, so this is one
change for all of them. `D-ANS-008` is the judgement and says why this was not
a wrong answer.
