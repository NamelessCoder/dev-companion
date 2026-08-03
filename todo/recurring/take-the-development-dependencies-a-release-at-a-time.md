# Take the development dependencies a release at a time

**Serves:** tests/
**Every:** 30 days
**Checked:** 2026-07-31
**Run:** composer outdated --direct

Read what the command lists and take each on its own merit rather than bumping
the file: a patch release of a tool that only runs in `composer ci` is worth
having, and a major one is a change to what the checks say, which is work and
not maintenance. What stood open when this was written down is `phpstan/phpstan`
2.2.6 → 2.2.7, a patch, and `phpunit/phpunit` 11.5 → 12.5, which is a major and
will move assertions rather than a constraint. `mcp/sdk` is not read here — it
has a todo of its own every seven days, because it is the protocol every answer
travels over rather than a tool this repository is checked with, and folding the
two would put that question on a monthly clock.
