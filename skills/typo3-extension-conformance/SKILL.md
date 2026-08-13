---
name: typo3-extension-conformance
description: 'Review or audit a TYPO3 project, sitepackage or extension against its checkout and active installation, and report what is wrong in priority order: TCA, services, backend modules, content elements, site sets, TypoScript, Fluid, labels, icons, security boundaries and deprecated APIs.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/benjaminkott/typo3-dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Conformance

Produce an evidence-backed audit against the active installation and the
checkout, not a generic checklist. Keep this skill as routing and assessment
method; do not embed versioned TYPO3 facts.

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

Stop after findings. This skill changes nothing, whatever the request asked for:
the fixes are the next workflow's, and the skill that owns each finding's area
is named below. Stopping at findings is not stopping at reading: the commands
`typo3_project_describe` marks as checks hand the code back as it was, and an
audit told not to change files runs them and reports what they printed.

Close on coverage rather than on a summary: the surface list written in step 3,
every entry marked assessed, unassessed or not requested, clean ones briefly. It
is that list and not a recollection at the end — a summary assembled from memory
reports what the session noticed it skipped, never what it never reached.
Without the list a thorough report and a narrow one look alike, and the cheapest
way to look thorough is to examine less.

Unassessed and not requested both mean nothing was established there, and they
are not the same thing: one is this review's gap, the other is what the request
left out. Say which of the two per entry, and let neither read as clean.

**The report is markdown the reader can copy, and the answer is where it goes.**
The findings and the coverage list together are what make it long, and length is
what makes the form matter: an audit is carried into an issue, into a ticket or
into a chat, and rendered output is what does not survive being moved. Write it
to a file only where the caller asks for one, at a path outside the checkout
being assessed, which this workflow changes nothing in.

This skill owns assessment and prioritization, and it owns saying who takes each
finding onward. Name the workflow the follow-up belongs to —
`typo3-extension-testing`, `typo3-extension-documentation`,
`typo3-backend-module-development`, `typo3-content-element-development` or
`typo3-extension-upgrade` — in the result itself, whether or not fixes were
requested: a reader deciding what to do next needs that as much as a session
that was told to do it. When fixes are requested, hand over to that skill for
the changes in its area and keep conformance responsible for re-checking the
resulting finding. What the sweep returned goes to `typo3-extension-upgrade`
whole: it owns crossing the package to another supported range, and a review
that hands over one deprecation at a time has decided the order that workflow
exists to establish.
