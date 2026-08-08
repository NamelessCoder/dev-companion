# Price shipping the skills as an optional Claude Code plugin

**Serves:** R-DIS-013
**Priority:** low
**Branch:** todo/price-shipping-the-skills-as-an-optional-claude-code-plugin
**Claimed:** 2026-08-08

A Claude Code plugin is a directory with a `.claude-plugin/plugin.json` manifest
— `name` the only required field — that bundles skills, subagents, hooks,
commands and **MCP server declarations** together, distributed through a
marketplace catalogue a user adds once with
`/plugin marketplace add owner/repo`. Self-hosting is supported, and a plugin
may declare a local subprocess server with paths resolved from
`${CLAUDE_PLUGIN_ROOT}`
([plugins reference](https://code.claude.com/docs/en/plugins-reference.md),
[marketplaces](https://code.claude.com/docs/en/plugin-marketplaces.md)). That is
this package's shape: a server and the skills that route to it, delivered as one
thing the user does not have to assemble.

**It must never become the only path, and it must not be required.** This
installer writes into thirteen clients (`Installer::AGENTS`), a plugin serves
one, and `R-DIS-013` is what counts them. So the outcome this card is allowed to
reach is an additional door for Claude Code users who prefer it, with the
installer unchanged and no session, tool or skill behaving differently by which
door it came through. A plugin that makes the other twelve clients second-class
is the wrong answer to this card and is not a smaller version of the right one.

**Start by settling the blocker, because it decides whether the rest is worth
pricing.** `composer.json` declares this a library requiring `php >=8.2`,
`ext-curl`, `ext-dom`, `mcp/sdk ^0.7.0`, `symfony/finder` and `symfony/yaml`;
`vendor/` is not committed; and `src/bootstrap.php` exits 1 with "Composer
autoloader not found" when it cannot locate one. A plugin fetched from a git
source is a checkout without `vendor/`, so
`${CLAUDE_PLUGIN_ROOT}/bin/typo3-dev-companion` does not start. Three ways out,
and the step is to price them rather than pick one from here: ship the plugin as
an archive with the dependency tree vendored in, which is a second server pinned
to its own `mcp/sdk` beside the one the project already resolved; ship a plugin
carrying **only** the skills and leave the server to Composer and the existing
client configuration, which is the smallest thing that works and gives up the
bundling that was the point; or find that a plugin can point its `command` at
the project's own `vendor/bin`, which the documentation does not say it can and
which has to be established rather than assumed.

What a plugin is genuinely reported to buy is versioned updates that move the
skills as a unit. Two caveats belong in the pricing: auto-update is **off by
default for third-party and local marketplaces**, so the update is still a user
action, which is what `R-DIS-025` already prompts for; and the namespacing that
prevents collisions is slash-command namespacing, while a skill is chosen by
description match, so it does not settle what happens when two other publishers
ship `typo3-extension-upgrade` — both do, measured on 2026-08-08. Establish both
against a real install before either goes into a decision.
