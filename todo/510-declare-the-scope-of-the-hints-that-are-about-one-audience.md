# Declare the scope of the hints that are about one audience

**Serves:** requirements/, decisions/

`D-KNW-007` gave four hints their `scope` and left the rest: seven read as being
about one audience and declare nothing, which the corpus reads as `any` — it
holds wherever TYPO3 is written. They are `extension-documentation`,
`extension-asset-build`, `extension-static-analysis`, `sitepackage-layout` and
`sitepackage-initial-content` in `general.json` and `php.json`,
`installation-upgrade` in `general.json`, and `site-sets` in `typoscript.json`,
whose one statement already carries `core` while the hint around it does not.

Each is a judgment about the statements rather than about the title, so read the
hint before writing the field: a sitepackage is a package in a project and reads
as `extension`, upgrading an installation is the repository around it and reads
as `project`, and a hint whose statements are half one and half the other keeps
the field on the statements — which is the shape `fluid-viewhelpers` already
uses for its single core sentence. Where the reading says a hint holds wherever
TYPO3 is written after all, that is the answer and it stays undeclared.

The mechanism needs nothing: `Hints::scopeNotice()` and `Hints::statement()`
label a declared scope in both directions already, `Schema::obliges()` carries
the four values, and `KnowledgeTest` holds every written scope to
`Scope::ofKnowledge()`. What each entry earns is a line in `HintsTest` only
where the notice it produces is worth holding — the mechanism is held once and
does not need holding per hint.
