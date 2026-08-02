# Read what a directory with no installation is offered, for the second `D-AUD-2` half

**Serves:** decisions/

The first half of the `D-AUD-2` **Wrong if** was answered on 2026-08-02 and the
entry carries it; the second was left, and this is it — a deployment with no
installation to read at all, which is where a third profile that leaves out the
installation-backed tools would earn its name. `Profile::active()` returns `all`
there on purpose, its comment saying an installation may still appear when the
agent runs `composer install`, so such a deployment is offered all 23 tools
including the ones that can only answer from an installation. The step is to
read what that actually looks like: drive `bin/typo3-cms-mcp` over stdio from a
directory with no TYPO3 anywhere above it, take the `instructions`, the tool
list and `typo3_server_scope`, then call `typo3_icon_lookup` and
`typo3_label_lookup` and record what each says with nothing to read. That is a
protocol-level reading and needs no designated environment, which is what makes
it startable now; the forward session in `E-NONE` that
`todo/waiting/ask-a-client-what-it-does-with-the-d-ans-1-unavailable.md` is
blocked on is a different question and stays blocked. Write the result into
`D-AUD-2` as a **Since then** line, because the entry already spent its foot
line on the first half.
