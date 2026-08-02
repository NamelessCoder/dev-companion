# Say whether writing a site configuration is covered or declined

**Serves:** scenarios/, knowledge/
**Run:** `bin/cli scenarios:contract SITE-01`

`SITE-01` asks for "an honest boundary where the rest — the site configuration
file itself, the language setup, the installation steps — is not covered", and
the first hand reading of it on 2026-08-02 found that boundary stated nowhere.
`typo3_server_scope` names neither in `covers` nor in `doesNotCover`: the closest
entry is "Running an installation: server and container setup, deployment,
backups, the editorial use of the backend", which is about the machine rather
than about `config/sites/<identifier>/config.yaml`. What `covers` does name is
"its sites and their sets", read from the repository's files by
`typo3_project_scope` — reading an existing site configuration, not writing one —
and "TypoScript site sets and TSconfig" as conventions. So a caller asking for a
site with two languages falls between the two lists and gets whatever the hints
match.

The step is to decide which of the two it is and write it into
`knowledge/server-scope.json`: a `doesNotCover` entry naming the site
configuration file and the language setup with `docs.typo3.org` as the route, or
a `covers` entry saying at which depth they are answered and by which tool. Read
what `knowledge/` actually holds about site configuration first — the site sets
in `architecture-hints/typoscript.json` are the neighbouring subject and may
already answer half of it, in which case the boundary runs between the set and
the file that uses it rather than around both. `ScopeTest` holds the scope and
the tool list to each other; what a new entry needs beside it is the `SITE-01`
half that says the decline is guarded.
