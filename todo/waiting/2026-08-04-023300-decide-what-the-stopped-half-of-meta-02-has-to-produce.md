# Decide what the stopped half of `META-02` has to produce

**Serves:** META-02
**Priority:** normal
**Waiting on:** what does the `E-STOPPED` run of `META-02` have to produce, now
    that a stopped project answers its prompt whole — the icons from the booted
    installation, the labels from the package files — and neither answer moves
    when DDEV starts? Rewriting criteria 1, 2, 4 and 6 to what the server
    promises there keeps the case and gives up the "found but not running"
    reason it was written for, and leaves criterion 6 with only
    `typo3_server_scope` to be worded against. Giving the case a second prompt,
    one whose answer needs the services the declared runtime brings, keeps that
    reason and makes the case two prompts, which is the shape
    `scenarios/readme.md` says one file may not have — and it has to name the
    environment with the prompt, because the measurement below shows the case's
    current one cannot hold either option.

The `E-NONE` half ran on 2026-08-04 and came out as the case asks; what it
settled is on `D-ANS-005`. The other half never reached the state it names.
`typo3_icon_lookup` and `typo3_label_lookup` both fall back to the packages' own
files (`R-ANS-008`), so a stopped project answers the prompt rather than
reporting that it could not be asked, and the session driven in
`.environments/e-site-main` answered with icons and labels and said in its own
words that they came from the files because the console refused to boot. It
named neither `ddev start` nor the two settings: the caveat that carries both
sits in `typo3_server_scope` and in an `unsupported` diagnosis, and a lookup
that answered from the files reaches neither. Criterion 6 has nothing left to
observe once the first four go, because there is no negative to change.

That last sentence was written against `.environments/e-site-main` alone, and
that environment is out of the case: it declares no platform and its
`platform_check.php` wants `>= 80500`, so host PHP 8.3 cannot run its console
and the resolution fails rather than answering weakly. The three released
environments are not out of it — `e-site-13.4` pins `8.2.0`, `14.3` the same,
`12.4` pins `8.1.1`, and host PHP satisfies all three, so a stopped project
there resolves through host PHP and carries a caveat. Since `4b43734` that
caveated resolution is no longer remembered, and `typo3_server_scope` was
measured changing across a `ddev start` inside one process — 0.000s `via=php`
with a stale caveat before, 0.503s `via=ddev` php=8.4 with none after. So
something does change in one session in those three, which is what criterion 6
asks about, and the answer to this todo cannot be read off `e-site-main`.

What that leaves open, and what nobody has run: whether the change reaches the
**prompt's own two lookups** rather than only `typo3_server_scope`. In these
three a stopped project's console runs, so `typo3_icon_lookup` and
`typo3_label_lookup` answer from the console rather than from the package files
`R-ANS-008` falls back to, and what the `ddev start` alters may be the caveat
and the source rather than the icons and labels themselves. Driving the prompt
against `.environments/e-site-13.4` stopped, in one session, across a real
`ddev start` is what says which — and it is a different run from the one that
produced the paragraph above.

## Measured on 2026-08-04, in `.environments/e-site-13.4`

`bin/typo3-cms-mcp` over stdio, started in that directory with its DDEV project
stopped, one process throughout. Five calls, a real `ddev start` (17.9s, exit
0), then the same five: `typo3_project_describe`, `typo3_icon_lookup` with the
prompt's own `publish`, `typo3_icon_lookup` with `workspace` as a query that
returns content, `typo3_label_lookup` with the prompt's own `Publish page`, and
`typo3_server_scope`. The project was left running, as it was found.

**Nothing the prompt asks changes across the start.** Both icon answers and the
label answer are identical before and after, byte for byte, `structuredContent`
included. What moves is one sentence in `typo3_server_scope` and one parenthesis
inside the label caveat.

### `typo3_icon_lookup` answers from the installation while the project is stopped

`answeredBy` is `installation` in both halves, so this todo's premise is wrong
twice over: the tool neither falls back to the package files nor changes source
across the start. `Typo3Runtime` boots the installation in a subprocess through
the resolved interpreter, and while the project is stopped that is host PHP 8.3;
the icon registry needs no database, so the boot comes up full and answers what
the running project answers. The answer carries no caveat at all — that it was
read under an interpreter the project does not declare is said by
`typo3_server_scope` alone.

The prompt's own icon query returns nothing in either half:
`No icon in … matches "publish". Identifiers spell the shape, not the intent`.
The running installation says the same, because `publish` is not an icon name in
13.4 and `workspace` is the word that reaches `actions-version-swap-workspace`
and seven others. The icon half of the prompt therefore produces the literal
"there are no such icons" the first criterion forbids, in every environment, for
a reason that has nothing to do with DDEV.

### `typo3_label_lookup` answers from the package files in both halves

The same four labels, the same `structuredContent`, `answeredBy` `packages`
before and after. Only the reason inside the caveat moves, and it moves from one
failure to a different one:

- stopped —
  `the console could not be asked (… An exception occurred in the driver: could not find driver …)`.
  The host PHP 8.3 console ran and its PDO driver is absent.
- running —
  `the console could not be asked (There are no commands defined in the "language:domain" namespace … Failed to execute command /var/www/html/vendor/bin/typo3 'language:domain:search' …)`.
  The console ran inside the container and 13.4 has no such command.

The second is the environment rather than the state. `typo3/cms-lowlevel` on
13.4 ships no `TranslationDomainSearchCommand`, where 14.3 ships it beside
`TranslationDomainListCommand`; `ConfigurationShowCommand` is missing there too.
On this line `typo3_label_lookup` answers from the files whatever DDEV is doing,
which is the one thing `scenarios/readme.md` asks an `E-SITE` to avoid.

Those two failure texts are also the run's only tool-reported evidence of where
an answer came from, and they are unambiguous: `/var/www/html/` is inside the
container, so one process reached the console through host PHP before the start
and through DDEV after it.

### `typo3_server_scope` is the only answer that carries the state

The whole of what changes, stopped then running, and it took 0.927s then 0.053s:

- `Its console is reachable via php on PHP 8.3.23, so those answers come from the installation itself rather than from a bundled snapshot.`
  followed by
  `Reachable is not the same as ready here: the DDEV project is stopped — start it with "ddev start" in <root> to answer from the installation. … Ask again once it is up: this answer stands as it was given, and the runtime the project declares reaches only the calls that come after the start.`
- `Its console is reachable via ddev on PHP 8.4, so those answers come from the installation itself rather than from a bundled snapshot.`
  The caveat is gone.

It names `TYPO3_MCP_ROOT` and not `TYPO3_MCP_CONSOLE`, and names it for the
wrong-directory case rather than as the way out of the stopped one — a console
was found, so nothing asks for the variable that would name one.

### `typo3_project_describe` says nothing about it

Byte-identical across the start, naming neither the stopped project nor
`ddev start`. The server's own instructions open with "Start every task with
typo3_project_describe", so the first call a session makes here reports TYPO3
13.4.33, its site and its URL as though everything were reachable.

### The six criteria, against what was observed

1. **Fails.** The icon lookup answers exactly "no such icons" and the label
   lookup answers four labels. Neither says the installation could not be asked;
   the icon answer does not mention the console at all.
2. **Not observable from the prompt.** Only `typo3_server_scope` distinguishes
   the two reasons. The one console failure the prompt's lookups report is a
   missing PDO driver, which does not read as "found but not running".
3. **Not exercised here.** Discovery succeeded; this criterion belongs to the
   `E-NONE` half, which passed.
4. **Half met.** `typo3_server_scope` names `ddev start` with the root.
   `typo3_project_describe` does not, and it is the call the instructions send a
   session to first. Nothing was started as a side effect: the scope answer at
   the end of the stopped half still reported the project stopped, after both
   lookups had run.
5. **Fails.** One variable is named, for another purpose.
6. **Observable, but not where the criterion looks.** Across the start, in one
   process, `typo3_server_scope` goes from `via php on PHP 8.3.23` plus the
   stopped caveat to `via ddev on PHP 8.4` with none, and the label caveat's
   reason changes from the host-PHP driver error to the container's missing
   command. Every icon, every label and both `answeredBy` values are unchanged.
   There was no negative to remember, because both lookups answered in both
   halves.

### What the evidence does to the two options

Neither is impossible, and both are now harder than they were written.

Rewriting criteria 1, 2, 4 and 6 to what the server promises here has to be
written against a stopped project that answers the prompt from the installation
with no caveat on the answer. The only wording criterion 6 could hold is the
`typo3_server_scope` line and its caveat, quoted above — which is a criterion
about a tool the prompt does not reach, in a case whose criteria are otherwise
about the answer a user gets.

Giving the case a second prompt needs more than a lookup with no file fallback.
`typo3_icon_lookup` has one and did not use it; what decides the source is
whether the answer needs the services the declared runtime brings, the database
first. `typo3_label_lookup` on 14.3 is such a lookup and on 13.4 is not, so the
second prompt and the environment have to be chosen together.

That is the third thing this run found and neither option accounts for:
`.environments/e-site-13.4` cannot carry the `E-STOPPED` half of this case at
all. Its console lacks `language:domain:search` whatever DDEV is doing, so the
label half of the prompt is answered from the files in both states for a reason
the case is not about. `e-site-14.3` is where the same run would separate the
two states on the prompt's own lookup, and it has not been run.
