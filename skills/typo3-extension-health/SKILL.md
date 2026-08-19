---
name: typo3-extension-health
description: 'Review a TYPO3 project, sitepackage or extension against its checkout and active installation and put it right — "look over my repository and fix it": TCA, services, backend modules, content elements, site sets, TypoScript, Fluid, labels, icons, security boundaries and deprecated APIs. The audit reports first; nothing is changed before the list is agreed.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Health

Establish what is wrong with a package against evidence, then work the agreed
list off until it is empty. Keep this skill as routing, assessment and
sequencing method; do not embed versioned TYPO3 facts.

The two halves are one workflow and the gate between them is step 5: the audit
answers a request that asked for a review, and a request that asked for changes
passes through the same report on its way to them. Nothing is edited before that
gate, whatever the request asked for.

## Establish scope and evidence

1. Work through [references/base.md](references/base.md) — it fixes the order
   every task here starts in, and an assessment is where that order matters
   most: a rule fetched after the reading confirms a verdict instead of testing
   it.
2. Read [references/checklist.md](references/checklist.md) for the audit
   surfaces, the finding gate, and the severity rubric.
3. Write the surface list down before opening a single file, from the
   checklist's surfaces narrowed to the ones this kind of checkout can have. It
   is the work list, and the coverage the report closes on is this same list
   with every entry answered.
4. Where the request names what it is about — security only, configuration only,
   one subsystem — read the surfaces it names and mark the rest **not
   requested** on that same list. The request narrows the reading, never the
   list: an entry nobody asked about costs one line, and dropping it instead is
   what leaves the reader unable to ask for the rest. A request that names no
   surface is not a focused one, and every entry is read.

A surface is in scope because the checklist names it, not because the file tree
shows it. Listing the files first inverts that: `find` cannot show a manual that
was never written, a test that does not exist, or a documentation tree that is
absent, so the surfaces it hides are exactly the ones whose absence is the
finding. Derive the list from the checklist and `typo3_extension_describe`, then
let reading answer it.

## Ask before judging, on every surface in scope

Scope says which surfaces are in play, and reading says what is there. Neither
says whether it is right. That comes from the owner of the convention, and it is
asked for **before** a view of the subsystem is formed rather than to confirm
one that already exists:

- `typo3_hint_lookup` with the subsystem's concrete paths and a short English
  description. One query per surface in scope; a single broad query is not
  subsystem evidence. A surface the checkout has no files for is asked by its
  hint id instead, because that is the surface whose absence is the finding and
  the one whose paths cannot be passed.
- The lookup that owns that surface's runtime facts, where one exists:
  `typo3_backend_module_lookup` for registered modules and routes,
  `typo3_icon_lookup` for icon identifiers, `typo3_label_lookup` for labels and
  overrides, `typo3_fluid_namespace_list` for globally available Fluid prefixes,
  `typo3_configuration_lookup` for effective runtime configuration.
- `typo3_documentation_lookup` with several short English queries and the target
  version where an official API or configuration detail decides the finding, and
  for every "does this still work here" a surface raises — the base says why the
  changelog cannot answer that one.

The first two answer different questions and neither stands in for the other. A
runtime lookup reports what is registered, what a path resolves to, what a value
really is — the facts of this installation. The conventions lookup reports
whether those facts are right. A surface can be confirmed by its own runtime
lookup and still break every rule that governs it, so a surface is not assessed
until both have been asked.

Read the checkout for what none of those can know: the files themselves, the
registrations, the tests, the documentation, and the conventions the project has
settled into.

Then read every returned rule in both directions. It says what new code should
do, and it says what this checkout is already doing wrong. A file that has
settled into the opposite of a rule is a finding, not a local style to preserve
— the project's own habits are part of what is being assessed, so consistency
with them establishes nothing.

Do not report the absence of an optional subsystem as a defect. But a surface
that is present and was never asked about is **unassessed**, and unassessed is
not clean: say so in the result. A defect nobody looked for and a defect that is
not there read identically in a report that does not separate them. Distinguish
a verified violation from a recommendation and from missing evidence.

The deprecation sweep the base fixes is reported the same way, and a review that
found nothing says the sweep ran and came back empty, with the majors it
covered. A sweep that is only visible when it produces a finding cannot be told
apart from one that never ran, and the surface it covers is the one whose
silence reads as a clean bill for the next major.

## Report

Order findings by severity and include:

1. the concrete file or runtime registration;
2. the observed behavior or configuration;
3. the applicable MCP or official-documentation evidence;
4. the consequence;
5. a scoped remediation and relevant project check.

Beside them, report what was raised while reading and dropped, with what dropped
it. A candidate let go in silence and a surface nobody opened leave the same
trace in the report, and the checklist's *What a dropped candidate owes* is the
bar each one is held to — including the one that could be neither established
nor disproved, which is reported as open rather than dropped.

Close on coverage rather than on a summary: the surface list written in step 3,
every entry marked assessed, unassessed or not requested, clean ones briefly. It
is that list and not a recollection at the end — a summary assembled from memory
reports what the session noticed it skipped, never what it never reached.
Without the list a thorough report and a narrow one look alike, and the cheapest
way to look thorough is to examine less. Unassessed and not requested both mean
nothing was established there, and they are not the same thing: one is this
review's gap, the other is what the request left out. Say which of the two per
entry, and let neither read as clean.

**The report is markdown the reader can copy, and the answer is where it goes.**
The findings and the coverage list together are what make it long, and length is
what makes the form matter: an audit is carried into an issue, into a ticket or
into a chat, and rendered output is what does not survive being moved. Write it
to a file only where the caller asks for one, at a path outside the checkout
being assessed.

**A request that asked for a review ends here.** Report, name the owning
workflow per finding as step 9 says, and stop. What asks for the rest is an
instruction to change the package — "fix it", "put that right", "do the first
three" — and it arrives after the report, which is where a review is being left.
**A question about a finding is not that instruction.** "Is that really
breaking", "why is that one high", "are you sure" — each asks the report to be
defended, and what it wants is the evidence rather than a change. Where the
sentence could be either, ask which was meant.

Stopping at findings is not stopping at reading: the commands
`typo3_project_describe` marks as checks hand the code back as it was, and an
audit told not to change files runs them and reports what they printed.

## The list is written down and agreed before anything changes

5. Write one item per finding, in the report's own severity order, each carrying
   what the finding is, the file or registration it is about, its severity, the
   workflow that owns it, and a state. A finding the report left open, and a
   surface it reported unassessed, are items too: their work is establishing
   what the audit could not.
6. Show that list whole and let the maintainer cut items, reorder them or stop,
   before a single change is made. That is what the whole order exists for, and
   it is the one step nothing downstream recovers. Do not begin the work while
   the list is being shown: a list arriving together with the changes it
   produced is one nobody had the chance to disagree with.
7. Keep the list in the session rather than in the repository. A worklist
    committed into somebody's history is a file nobody asked for, in a project
    this workflow is a guest in, and it has to be taken out again afterwards. So
    each item's state is reported as the work goes, and what the history keeps
    is the commits the items produced.

Where the request arrives carrying a report from an earlier session, read it
whole rather than running the audit again, and say which of the two the list was
built from. A report from an earlier session is evidence about the checkout as
it stood then.

## The list is worked off item by item

8. Take the items in the list's order, grouped by the workflow that owns them.
    One activation covering that owner's items costs less than one per finding,
    and the owner is what decides how its own area is changed.
9. **Invoke the skill that owns them** and carry across only the scope and the
    verified behaviour it needs: the finding, the evidence under it, the paths.
    Stop before editing files another owner has — the crossing is the transition
    itself, not a detail of the item. Which workflow that is is named in the
    report whether or not fixes were requested, because a reader deciding what
    to do next needs it as much as a session that was told to do it.
10. Where an item has no owning workflow, it is worked here only where the
    project's own suite, linter or static analysis proves the change: the
    change, the check that covers it, and nothing wider than the finding. An
    item nothing here can prove goes back unassigned in the closing report
    instead. A finding no workflow owns and no check covers is a hole in the
    map, and changing it on judgement is what hides the hole.
11. Settle where the change lands before the first commit: which branch these
    commits belong on, whether the pull request is squashed, and which released
    lines the fix is carried to. That is the repository's own policy and nothing
    here reads it. It is asked of the maintainer, before a branch is pushed and
    before a pull request is opened. A branch listing and a tag scheme are not
    that answer: they say which branches exist, never which are still supported.
    What the core does — a fix on the main branch, cherry-picked down — is the
    core's own process and the default nowhere else. Ask once and work from the
    answer, keeping it in the session the way the list is kept.
12. Commit per item, or per group of items in one owner's area, and say which
    item that commit closed — the message from `typo3_commit_message_guide` with
    `workflow="project"`. A session that ends halfway is read out of the log,
    which is why the state belongs in the commits rather than in the list alone,
    and a log that says which finding each commit closed is what makes it
    readable.

## What closes it

13. Re-run the audit above on the worked list rather than re-reading the files
    it changed: a file that reads correctly can still be rewritten by the
    environment that owns it, and the difference only shows once that
    environment runs again. Work that grades itself off its own diff has no
    evidence the finding is gone.
14. Report what is left: the items still open, the items dropped with what
    dropped them, the ones sent back unassigned, and every finding the audit
    reported as open or unassessed that this work did not settle. A finished
    list and an abandoned one read alike in a summary.

## Where this stops

This skill owns the state of a whole package — establishing what is wrong with
it, what each finding is worth, who takes it onward, and staying with the agreed
list until it is empty. It owns both halves of that one thing, and a request for
either arrives at the same door.

It does not own the changes in another workflow's area. Those cross to
`typo3-extension-testing`, `typo3-extension-documentation`,
`typo3-backend-module-development`, `typo3-content-element-development` or
`typo3-extension-upgrade`, and what this skill carries across the crossing is
the item and not the work.

What the sweep returned goes to `typo3-extension-upgrade` whole: it owns
crossing the package to another supported range, and handing over one
deprecation at a time decides the order that workflow exists to establish.

It does not own one change proposed against the package either. A pull request,
a patch or a branch somebody offers is judged against that diff rather than
against the repository, and running this surface list on a one-line change is
what `typo3-extension-patch-review` exists instead of.
