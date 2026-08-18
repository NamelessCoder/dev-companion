# The configuration lookup answers on two of the four covered lines

**Serves:** R-AUD-004, R-ANS-018, D-ANS-052
**Priority:** high

`ConfigurationLookup::answer()` runs `configuration:show --type=active --json`,
and that command arrived in TYPO3 14.2: `ConfigurationShowCommand` and the
changelog entry `Feature-108815-CLICommandsForSystemConfiguration` are in
`.checkouts/14.3` and `.checkouts/main`, and neither is in `.checkouts/12.4` or
`.checkouts/13.4`. On the two LTS lines the console answers that the command is
not defined, the guard in that method recognises only `No configuration found`,
and the caller is handed the console's own sentence as `unsupported` — a
could-not-ask naming nothing to do instead, which is what `R-ANS-018` forbids.
Read `$GLOBALS['TYPO3_CONF_VARS']` at the path as a topic in
`src/Installation/probe.php`, beside the `formDataGroups` this tool already asks
for, so the container answers the two LTS lines the way the console answers the
newer ones; where neither can, say which TYPO3 the command needs.

`D-ANS-052` settled what this tool is for and never asked which lines run it,
and the installation its evidence was measured against was `main`. `SITE-03` is
the contract case that asks for this value and is `held`: it runs on `E-SITE`,
which unnamed is the covered stable line, so the case passes on the one
environment where the command exists. Run it on the environment `SITE-02` names
once the repair is in, and say in its criteria which line is answered by which
source.
