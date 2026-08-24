# State in site-sets which key a site names its sets under

**Serves:** feedback/2026-08-24-133540-typo3-configuration-lookup-reads-as-typo3-conf.md
**Priority:** normal

Judged on 2026-08-24 as the ladder's step 2 with a step 1a remainder, and
written up in `D-KNW-115`: the sentence saying a site's own `config.yaml` names
its sets under `dependencies` sits in `fresh-instance-seeding`, curated on
seeding phrases, and no site-configuration query reaches it.

Read the site entity's constructor and its accessors on `.checkouts/13.4` and
`.checkouts/main` for the key-to-accessor direction — `dependencies` to
`getSets()`, `settings` to `getSettings()`, and where the entity's own
TypoScript and page TSconfig come from — and settle whether `isTypoScriptRoot()`
is stated at all, which is `@internal` on every covered line.

Then put that into `site-sets` in `knowledge/hints/site-sets.json`, bound from
13, curated on both "sets" and "dependencies" so the search that spells it one
way reaches the file that spells it the other. `fresh-instance-seeding` keeps
its own sentence; whether it owes a pointer is decided against `D-KNW-087`.

The tool-description clause the feedback asks for is refused in `D-KNW-115`, and
the `doesNotCover` entry that already sends a site-configuration question to
`typo3_hint_lookup` with `id=site-sets` is what this placement makes correct.
