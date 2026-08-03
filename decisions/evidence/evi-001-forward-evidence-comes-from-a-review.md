---
id: D-EVI-001
date: 2026-07-31
status: confirmed
---

# D-EVI-001 — Forward evidence comes from a review, not from a prompt that knows the answer

**Only an open review that names the working context and the user's intent is
recorded as forward evidence; everything that names a task shape is a targeted
contract case.**

The scenario suite was written to describe what the three audiences need, and it
was then also used as the evidence that an agent finds those needs on its own.
Those are two different tests, and one file shape hid it.

## Evidence

- Every one of the 32 prompts named its own subsystem, and several named the
  implementation — a status list with a refresh action, a carousel with inline
  children. The one recorded forward run, `EXT-04` on 2026-07-30, met all five
  criteria it was judged against and produced six defects none of them measured,
  which is what a prompt that already contains the answer buys. Five prompts
  additionally named one project on one machine, so nobody else could run them.

## Decided

- The suite splits. Three open forward reviews — site project, reusable
  extension, core patch — name the working context and the user's intent and
  nothing else, and only these are recorded. Everything that names a task shape
  becomes a targeted contract case: still readable, still printable, never
  forward evidence. One case is one file, and an environment is a kind of
  working directory rather than someone's checkout.

## Wrong if

- Two consecutive review runs produce findings too diffuse to tie back to a
  requirement or a feedback — then the broad prompt measures the model's taste
  rather than this server. Or the contract cases quietly stop being read because
  nothing schedules them, and the routing they hold rots while the three reviews
  stay green.

## Confirmed on 2026-08-02

The findings tie back, read as a pass over the two files in `scenarios/runs/`
and over the run each of them replaced. `REVIEW-01` run 3 declared translations
clean with `source-language="de"` on screen; run 4 reports it as `M-6` with the
rule quoted from `knowledge/architecture-hints/general.json`, and the absent
`README` and `Documentation/` that three runs walked past is `R-PRJ-006`.
`REVIEW-02` run 4 answered the open feedback that no extension review reaches
the console and settled `D-EVI-003`; run 5 produced four feedbacks, all archived
the same day in `419b622`, `5e76417`, `25100e7` and `cc15e3a`. Thirteen
requirements and five decisions name one of the two reviews as what they were
written from. A minority ties back to nothing, and it is the same shape in both:
`REVIEW-01`'s deployment cluster `H-1` to `M-4`, `REVIEW-02`'s findings 3 and 4
— correct, anchored to lines, and this server's in no part. So the measure is
per run rather than per finding. Each of these four runs produced at least one
finding a stated rule made it find, and at least one this repository could file.

## Confirmed on 2026-08-02

The contract cases are scheduled, by the strongest thing available. All 36 name
live tests in their **Held by** line, `composer ci` runs those tests on every
commit, and `ScenariosTest::everyContractCaseNamesWhatHoldsIt` fails when a
named test is renamed away. What nothing schedules is the reading by hand that
seven of them fall back on where **Held by** says `not guarded` — `CORE-04`,
`CORE-06`, `EXT-01`, `SITE-01`, `SITE-08`, `SKILL-07` and `SKILL-09`. Two of
those carry weight already: `D-EVI-002` rests on `SKILL-07` read by hand, and
`SKILL-09` says in as many words that it is what measures the rest. That residue
is the todo behind this reading, not a correction to this entry.

## Since then

The residue is scheduled and read once, and the list in it was two entries out.
`SITE-08` is held rather than not guarded since the same day — its brief no
longer calls a backend-only task Fluid and TypoScript work — and `EXT-03` was
never in it, though it says outright that nothing makes a session pass
`workflow="project"`. What the seven have in common is not the shape the count
suggested: `CORE-04` and `CORE-06` are waiting on knowledge and on a decision,
carried by `R-KNW-001` and by `R-AUD-004` being open, and the session-recurring
backlog todo already brings those round. The other five are waiting on what a
session does, which no test here reaches, so the reading is what they get:
`todo/recurring/read-the-contract-cases-no-test-can-hold.md`, every 14 days,
starting from `SKILL-09` because it says it measures the rest.

The first reading was done on 2026-08-02. Four stand: the conformance
checklist's sink gate is as `SKILL-09` describes it down to the opt-out being on
the path rather than at the end, the backend-module skill still stops its
workflow and activates the documentation one before editing, the upgrade skill
still carries its five steps in order, and a project-scope brief still names
`workflow="project"` for the commit. `SITE-01` does not: the boundary it asks
for is stated nowhere — neither `covers` nor `doesNotCover` names the site
configuration file or the language setup, and the nearest entry is about servers
and deployment. That is queued as `todo/490`.

Rejected for now: holding each proxy to a digest of the file it stands in, the
way `catalog:check` holds a demo. It fires exactly when the proxied text moves
rather than on a clock, which is the better trigger, and it is a build rather
than a cadence — the reading has to happen once either way before there is a
digest worth recording.
