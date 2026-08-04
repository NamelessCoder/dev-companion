# Move the documents under a scope directory

**Serves:** knowledge/documents/, src/Knowledge/
**Priority:** high

`D-KNW-058` is what this carries out, and it comes before the front matter card
because it decides what an id is. Move each document below
`knowledge/documents/<scope>/`, dropping the scope word its file name carried:
`core/contribution/rules.md`, `core/contribution/gerrit-workflow.md`,
`core/contribution/sources.md`, `core/testing/scripts.md`,
`any/contribution/commit-messages.md` and `extension/testing/phpunit.md` — three
segments each, scope, topic, name. Replace `depth(0)` in
`Documents::documents()` with a walk that publishes a file only where it sits
three segments deep below a directory naming a `Knowledge\Scope` case, so a
stray file is still not a resource; make the id the path below
`knowledge/documents/` without the extension, and have `Documents::scopeOf()`
read the first segment instead of `Documents::isCoreOnly()` asking the coverage.
Change the resource prefix to `typo3://guides/` in `ResourceHandler`, widen the
`[a-z0-9-]+` in `ScopeTest` to admit a segmented id, and rewrite every
`typo3://core/<id>` written in `knowledge/`, in `skills/`, in `documentation/`
and in `src/` — `bin/cli links:check` and `ToolNamingTest` are what find the
rest. `ScopeTest` keeps holding the path scope and the `covers` scope to each
other.
