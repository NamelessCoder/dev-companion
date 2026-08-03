# Where every task starts

## Nothing starts until the server answers

A skill is a file the installer left behind: it loads and reads the same whether
the tools behind it are connected or not, and neither side notices. So the first
call below is also the check.

- No `typo3_` tool in this session, or a first call that errors: stop, say this
  workflow needs the server and it is not there, and name what came back.
- Do not fall back to general TYPO3 knowledge or start reading the checkout.
  That answer carries this workflow's order and confidence and none of its
  evidence, and nothing in it says which of the two it is.
- Continue only when asked to after saying so, and repeat it in the answer and
  in every finding a lookup would have carried.

## The order

This is the order, and it is an order rather than a list because each step
decides what the next one is worth. A convention fetched after the code has been
read confirms a view instead of testing it. A command recommended before the
project's own are known is a guess that sounds like advice.

1. **`typo3_project_scope`** — the installation, its TYPO3 and PHP version, the
   extensions that are the project's own, its sites, and the commands this
   repository actually declares. Every later answer is filtered by that version,
   and a check the repository does not declare is a wrong answer however
   sensible it sounds.
2. **`typo3_extension_scope`** for each extension in scope — what it registers,
   and what it ships beside that: its manual, its README, its test layers, its
   XLF files with the source language each one declares. What it does *not* ship
   is answered too, and that is the half no file listing can give you.
3. **`typo3_task_guide`** with a short English task, the affected area, the
   target version and the change type — the workflow this task belongs to and
   the checks that come with it.
4. **`typo3_hint_lookup`** for each subsystem in scope, with its concrete paths.
   One query per subsystem; a single broad query is not subsystem evidence.
5. **`typo3_changelog_lookup` with `type: deprecation`**, at each major the
   package declares, bounded by `tag` and with the query omitted. Those three
   are the changelog's own axes, and the extension's vocabulary is not among
   them: an entry carries a query only when its title carries every word of it
   at once, and the core titled those entries about its own code. Words taken
   from what step 2 reported are therefore matched against titles they were
   never written in, and the sweep comes back empty however right the query
   looks.

   Step 2 picks the tags instead. `ext:core`, `ext:frontend`, `ext:form` and the
   rest name the system extension a change is **in** — one call for each one the
   package requires, renders through or registers into, which is more than its
   manifest lists — and `TCA`, `TypoScript`, `Fluid`, `YAML`, `Backend`,
   `Frontend` name the surface, one for each kind of file it ships. An extension
   key of your own is not among them and matches nothing. Every call also
   returns every tag that version and type carry, so the second call onwards is
   read off the first rather than guessed at.

   Step 2 is what the answers are checked against, which is the other half the
   words were doing. Verify each identifier that comes back in the checkout — a
   deprecation nothing here calls is not a finding — and carry the
   `FullyScanned` / `PartiallyScanned` tag into the answer, because it says
   whether the Extension Scanner can find the remaining call sites or whether
   that reading is yours. Bounded this way the sweep is still writable before a
   file is opened, which is why it is a step of the order: one left to the
   reading reaches only what a finding stumbled into, and the deprecation that
   decides whether the package survives the next major is not usually the one a
   finding walks past.

   A changelog records change events, so a pattern nothing has touched for ten
   majors has no entry at all. An empty sweep is therefore not an answer about
   what still works. "Does this still work in version N" goes to
   `typo3_documentation_lookup` at that version — here, and whenever the reading
   raises it again. Where the manual has no page for it either, that is a result
   and not an answer. Undocumented is not unsupported.

**Then** read the checkout. Not before: listing the files first makes everything
after the listing look optional, and the conventions arrive as a footnote to a
verdict that has already formed.

## When the lookups run out

A behaviour question that survives the lookups above is read out of the
installed source rather than guessed at. What answers it is the class that
implements the behaviour and the one it inherits from. That reading is the step
after the lookups, and what it replaces is changing the code until it works.
What it settles is what this installation does and never what TYPO3 supports. So
a finding says the question could not be settled beyond the version installed,
and an answer built on the reading names the version it holds for.

## Two kinds of lookup, and neither stands in for the other

`typo3_backend_module_lookup`, `typo3_icon_lookup`, `typo3_label_lookup`,
`typo3_fluid_namespace_list` and `typo3_configuration_lookup` report what is
registered, what a path resolves to, what a value really is at runtime. They
establish the facts of this installation and they are never a verdict on it.
`typo3_hint_lookup` and `typo3_documentation_lookup` say whether those facts are
right. A subsystem confirmed by its own runtime lookup can still break every
rule that governs it, so it is not established until both were asked.

## A rule is read in both directions

It says what new code should do, and it says what this checkout is already doing
wrong. A file that has settled into the opposite of a rule is a finding, not a
local style to preserve: consistency with a project's own habit establishes
nothing about whether the habit is right.

## What the code is for is evidence, and the repository states it

A mechanism that costs something is not a defect for costing it. Before
reporting one, find what it is there for — the manual, the README, the
changelog, the setting it is driven by, the versions the package declares it
supports — and say so. Where a purpose is documented, what you have is a
trade-off to name with its cost and its alternative, not a defect; where you
cannot find one, the finding says that it could not be established rather than
that none exists. This is the other direction of the rule above, and skipping it
turns a review into a list of everything the author did on purpose.

## What a finding rests on is part of the finding

Three things carry one: a file that was read, at its path and its line; a
command that was run, with what it printed; a mechanism traced into an installed
package. Say which of the three it is. Leaving it unsaid gives a finding read
out of a CI file the weight of one with a verified line, and the reader has no
way to separate them again.

Where one of the project's own commands would settle it, run it.
`typo3_project_scope` marks each command it lists **check**, **change** or
**unknown**, read off the declared body: a check reports and hands the code back
as it was, so even a task told not to change files runs it, and the linter the
repository already declares is the cheapest evidence in it. A change is not run
under that instruction, and an unknown — a test suite, a shell pipeline, a
console command — is named in the answer as evidence that is available rather
than run unasked. What a check prints is not the finding: the configuration that
makes it fail is still what the finding is about, and the run is what takes that
finding from derived to established.

## What this server does not know

It does not read your working tree. Which files changed, which branch you are
on, and whether a path or an identifier still exists there are yours to
establish — then pass the concrete paths back, because that is what turns a
general convention into an answer about this code.

## Query it in English

The knowledge is written in English and matched lexically, so a query in another
language reaches the loanwords the two happen to share and nothing else.
Translate the subject before calling and the answer back afterwards, whatever
language you are speaking with the user.
