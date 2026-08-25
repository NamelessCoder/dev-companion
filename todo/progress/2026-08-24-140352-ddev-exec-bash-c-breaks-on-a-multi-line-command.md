# ddev exec bash -c breaks on a multi-line command, one case past what installation-setup covers

**Serves:** feedback/2026-08-24-140352-ddev-exec-bash-c-breaks-on-a-multi-line-command.md
**Priority:** normal
**Branch:** todo/ddev-exec-bash-c-breaks-on-a-multi-line-command
**Claimed:** 2026-08-25

Measure in an installation made by `bin/cli environment:create E-SITE` what
`ddev exec bash -c` does with a command written across several lines with
backslash continuations inside the quoted string, and write what it settles into
`installation-setup` beside the `ddev exec` statements already there — that
form, the admin-password rejection, and the empty flow mappings `--create-site`
leaves in `config.yaml`.
[`D-KNW-119`](../../decisions/knowledge/knw-119-the-corpus-tells-apart-the-failures-one-usage-synopsis-presents-alike.md)
is the judgement: it carries what was read for the last two and what still has
to be bound against `.checkouts/12.4` and `.checkouts/13.4`.
