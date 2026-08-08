# Price shipping the skills as an optional Claude Code plugin

**Serves:** R-DIS-013
**Priority:** low
**Branch:** todo/price-shipping-the-skills-as-an-optional-claude-code-plugin
**Claimed:** 2026-08-08
**Waiting on:** which of the three shapes below to build, or whether to build
    one at all. The blocker this card opened with is gone and all three work;
    what separates them is what this package is willing to own, and nothing here
    answers that. Asked 2026-08-08.

Build the shape that comes back: the plugin directory, its
`.claude-plugin/plugin.json`, the marketplace entry that lists it, and whatever
publishes the skills into it — with `Installer` and the twelve other clients
untouched.

## The blocker is gone

`${CLAUDE_PROJECT_DIR}` resolves to the project root and substitutes into an MCP
stdio server's `command`, `args` and `env`, which the plugins reference states
in its environment-variable table
([plugins reference](https://code.claude.com/docs/en/plugins-reference.md), read
2026-08-08). So a plugin may point its command at the project's own
`vendor/bin/typo3-dev-companion` and vendor nothing. This card called that the
way out the documentation does not offer; it does.

`archive` is a source type of its own — a zip over HTTPS with an optional
`sha256` pin, from Claude Code v2.1.224 — so the vendored shape needs no git
checkout on the user's machine either.

## The three shapes

**Skills alone.** `.claude-plugin/plugin.json` plus a `skills/` directory. The
published shape is already the plugin's shape: `Installer::publishSkill()`
writes `<skill>/SKILL.md` with `skills/base.md` copied to
`<skill>/references/base.md`, each `SKILL.md` carries the `name` the plugin
loader reads, and the twelve skills need no transformation at all. The server
stays on Composer and on `install`. What it gives up is the bundling that was
the point.

**Skills and the project's own server.** The same, plus an `mcpServers` block
whose command is `${CLAUDE_PROJECT_DIR}/vendor/bin/typo3-dev-companion`. It
works exactly where the project required this package through Composer — which
is where `install` already works — and where it did not, the plugin installs
cleanly and the tools are silently absent. That last failure is the one
`R-SKL-008` exists for, so the base catches it at the first call rather than in
the answer.

**Skills and a vendored server.** An archive carrying `src/`, `knowledge/`,
`skills/`, `bin/` and a production dependency tree: 6.0 MB across 983 files,
measured on 2026-08-08 from this checkout's `composer.lock` with
`composer install --no-dev`. It is the only shape that serves a project which
never required the package. Three costs, and the second is the one nobody has
written down:

- The tree is pinned to whatever the archive was built with and is never
  reconciled with the project's own resolution, so a dependency update is a
  plugin release.
- **The feedback channel comes on.** `Channel::isAvailable()` asks whether
  `InstalledVersions::getRootPackage()['name']` is `typo3/dev-companion`, and a
  `vendor/` built in this checkout bakes that name into
  `vendor/composer/installed.php` — so every project running the plugin is
  offered `typo3_feedback_record` and `typo3_feedback_list`, which `R-SCO-009`
  and `AGENTS.md` say are a development tool for building this server and never
  part of using it. They would write into the plugin cache, which Claude Code
  replaces on update and deletes fourteen days later, so the report is lost as
  well as unwanted. Guarding it is new code on a path nothing else exercises.
- `bin/cli`, `feedback/`, `scenarios/` and the rest of the upkeep half ship too
  unless the archive is built selectively, which is a build step this repository
  does not have.

Both server-carrying shapes share one limit: a manifest is static JSON, and
which entry a project needs is a property of the project.
`documentation/clients/installing.md` is where the installer's two forms are —
`ddev exec php vendor/bin/typo3-dev-companion` in a DDEV project that required
the package, the absolute path otherwise — and a plugin always writes the second
and always calls the host's `php`. A host whose PHP lives only inside DDEV gets
a server that does not start.

## What a plugin buys, and the two caveats

Both caveats hold, and both are documented rather than needing a run.

Auto-update is off: "Official Anthropic marketplaces have auto-update enabled by
default. Third-party and local development marketplaces have auto-update
disabled by default"
([discover plugins](https://code.claude.com/docs/en/discover-plugins.md), read
2026-08-08). A user toggles it per marketplace in `/plugin`, or an administrator
sets `autoUpdate: true` on an `extraKnownMarketplaces` entry in managed
settings. So the update stays a user act, which is what `R-DIS-025` already
prompts for — with one real difference: the toggle is set once per marketplace
and holds, while `update` is run once per project every time.

Namespacing is by plugin name and applies to the invocation name: "Plugin skills
are namespaced by the plugin name, so **commit-commands** provides skills like
`/commit-commands:commit`". Selection is a description match, which carries no
prefix, so the prefix removes the name clash and leaves the competition. Whether
another publisher ships a TYPO3 upgrade skill was not measured: there is no
`claude` on this machine, and the catalogues are not readable without one.

The collision worth pricing is ours rather than a stranger's. A project that has
both the installer's `.claude/skills/typo3-*` and the plugin's copy carries
twelve descriptions twice, and only the first half is in the record `R-DIS-025`
digests — the plugin's copy goes stale with nothing watching it.
