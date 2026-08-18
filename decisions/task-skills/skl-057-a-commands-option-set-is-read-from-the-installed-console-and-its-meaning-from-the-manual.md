---
id: D-SKL-057
date: 2026-08-18
status: open
---

# D-SKL-057 — A command's option set is read from the installed console and its meaning from the manual

**Which options the setup command offers is read from the installation's own
console, and `typo3_documentation_lookup` keeps what a value means and what the
command refuses.**

The core composes one of those option descriptions from the packages the
installation has active, so the manual's option list and this installation's
differ by a package and no version-bound page can carry the difference.

## Evidence

- `feedback/2026-08-18-070611`, a boot of the t3g/blog DDEV installation on
  14.3.6 in `/home/benji/projects/blog`. Step 3 of *Create one where none is
  declared* in `typo3-development-installation` routes the option set to
  `typo3_documentation_lookup`; the session ran `ddev exec typo3 setup --help`
  instead and reports the option names, the defaults already matching DDEV, and
  `--distribution` rendered as
  `[disabled] Requires typo3/cms-impexp to be installed`. It filed the
  substitution as a strength rather than as a cost.
- `.checkouts/14.3/typo3/sysext/install/Classes/Command/SetupCommand.php:151`
  composes that description from
  `$this->packageManager->isPackageActive('impexp')`, so the string is a fact
  about the installation and not about the version. `.checkouts/main` carries
  the same composition; `.checkouts/13.4` has none, so the property starts at
  14.
- It is the only one of its kind. Swept on 2026-08-18, `isPackageActive` occurs
  in two of the core's own command classes under `.checkouts/14.3`, and only
  `SetupCommand` uses it to compose an option description — the other,
  `ExtensionListCommand:98`, is in what the command prints rather than in what
  it declares.
- The console is reachable at that step. Its own command is what the step runs,
  so the binary the option set is read from is the one the install is about to
  be performed with.
- The manual half is not redundant, and the same step names what `--help` does
  not carry: that the value a connection option accepts is not necessarily the
  value written into the settings afterwards, and that the command refuses a
  database that already holds tables.
- Step 4 is where the difference lands. `--distribution` is the seeding option,
  and a caller holding the manual's list would reach for the one option this
  installation reports as disabled.
- Both sources cost one call, so nothing here is a round-trip saving and
  `D-FBK-027`'s measure does not apply. What is at stake is whether the answer
  describes this installation.
- [AGENTS.md](../../AGENTS.md) already states the rule this is an instance of:
  facts owned by an installation are read from that installation, because no
  bundled answer could be right for it.

## Decided

- **Rung 3, routing, and queued.** The source that owns the fact exists and the
  step sends the question elsewhere. The change is a sentence in a published
  skill, which [judging.rst](../../documentation/records/judging.rst) reviews
  rather than improvises.
- **The boundary is the option set against what an option means.** Which options
  this console offers, and which of them its packages have disabled, is the
  installation's; what a value does to the settings, and what the command
  refuses, is the manual's. That is the split
  [`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)
  says a strength carries.
- **Rejected: a tool that answers a console command's options.** Both sources
  are one call, so nothing comes off the caller, and the surface would be
  Symfony's help output for every command an installation registers.
- **Rejected: replacing the lookup.** Two of the three things the step tells the
  caller to check are not in `--help`, so a routing that moved the whole
  question would take them with it.
- **The step names `--distribution` rather than stating a rule.** The sweep
  above found one option of this kind, so a general sentence would generalise a
  single case, and the one option is the one step 4 reaches for. That is
  [`D-SKL-048`](skl-048-a-build-workflow-says-a-symptom-is-a-lookup-trigger.md)'s
  *the example is the shape, not the id* read from the other side: here the
  concrete thing is what the caller needs and the rule is what would go stale.
- **The step says the property starts at 14.** A caller on 13.4 gets the same
  list from either source, and a distinction stated where there is none is what
  a reader takes for a version-independent rule.
- **No requirement.** What one would state is every routed lookup, and one
  option on one command is not that.

## Assumed

- That the output the session quotes is what the checkout composes. Nobody here
  has that installation; what was verified is the code that writes the string.
- That the fact arriving is worth the sentence. The session read `[disabled]`
  and stopped, so what is measured is a caller being told the truth about its
  own installation rather than a failure that was prevented.

## Wrong if

- A session reads the option set off the installed console and misses one of the
  two things the manual carries. The routing would then have moved the whole
  question where only half of it belonged.
- A second option of this kind appears, in `setup` or in another command a
  workflow here routes to. Naming `--distribution` would then be the narrow form
  of a rule that had earned being stated, and the step would be found teaching
  one case.
- The option set the console prints turns out to differ from the manual's in
  ways a caller acts on wrongly — an option present here and undocumented, a
  name the manual spells differently. The split would then be finer than two
  sources, and what the step owes is which one wins rather than which one is
  asked.
