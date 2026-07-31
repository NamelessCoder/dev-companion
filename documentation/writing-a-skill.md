# Writing a task skill

What the published skills are is in [AGENTS.md](../AGENTS.md), and the order
every one of them starts in is [skills/base.md](../skills/base.md), which is one
file rather than a paragraph each. This page is the other half: how a skill is
**written**, and what holds each rule to that.

The rules are narrow for one reason. A skill is not a document this repository
keeps — it is a copy the installer writes into somebody else's project, where
nothing reports that it has fallen behind the server it came from and no release
of this server corrects it. Whatever it states, it states permanently, in a
context that is paid for by the token, about an installation it cannot see.

## Before there is a skill

A domain earns one when a scenario or a recorded session shows that the tools
and skills that exist **fail to carry the task** — not when the subject is large
enough to look like it deserves one. Release, static analysis, performance and
security have each looked like a skill at some point; what settles it is a case
in [scenarios/](../scenarios/readme.md) or a run in
[scenarios/runs/](../scenarios/runs/) where the existing workflow was reached for
and came up short.

What such a run shows is almost always smaller than the domain: an order nobody
keeps, a step that only runs when a finding happens to walk into it, a boundary
two skills both believe they own. The skill is written around that and around
nothing else. "Less is more" is not a preference here — it is an instruction
every session in another project loads before it does anything.

## The rules, and what holds each one

| Rule | Held by |
| --- | --- |
| It is filed under the name it calls itself, with a description a client can route on | `SkillTest::everySkillIsPublishedUnderTheNameItCallsItself` |
| It starts from the base before it reaches for anything of its own | `SkillTest::everySkillStartsFromTheBaseBeforeItsOwnEvidence` |
| It keeps no second copy of what a tool owns | `SkillTest::noSkillKeepsASecondCopyOfWhatAToolOwns` |
| It routes through the owners of its own facts, in the order it needs them | `SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder` |
| Every reference is one hop away and loaded on demand | `SkillTest::everyReferenceIsOneHopAwayAndLoadedOnDemand` |
| A skill that judges keeps its checklist beside it | `SkillTest::judgmentHeavySkillsKeepTheirChecklistBesideThem` |
| It says what it owns | `SkillTest::everySkillStatesWhatItOwns` |

Those seven run over the skills directory rather than over a list, so a skill
added later is held to them without anybody registering it anywhere — which is
the point, because the list is the thing a new skill is written without ever
seeing. `SkillTest::theAuthoringContractIsWrittenDownAndNamesWhatHoldsIt` holds
this table and that set to each other in both directions: a rule here with no
test behind it, or a directory-wide assertion nobody wrote down, fails.

**The name and the description.** The directory name, the `name:` in the front
matter and the name every other skill calls it by are one string. The
`description` is the only part of a skill read before it is chosen, so it is
written in the words a user brings — the request, the symptom, the files being
touched — and never in this server's tool names.
[`D-AUD-3`](../decisions/audience/aud-3-the-instructions-carry-the-entry-point.md)
is what a wrong one costs: a review prompt whose every criterion the conformance
skill's body would have met did not activate it, and all thirty-five calls of
that session went through Bash.

**Starting from the base.** The skill links `references/base.md` and then states
what it *adds* to it. It never restates a step the base already fixes: five
hand-written copies of one order is what the base replaced, and the copy that
drifts is always the one already published into somebody's project.

**Nothing a tool owns.** No TYPO3 version number, no API signature, no
dependency constraint, no backend markup, and no command the checkout has not
been asked for. A version in a permanently loaded instruction is the one fact
that cannot be re-asked when the installation turns out to be a different one,
and no answer built on it ever says where it came from. The same holds for a
package name: it is one word in a published file that no release of this server
can correct, so it is written where a task reads it once — a reference — rather
than where every task carries it.

**Routing, in order.** What a skill adds to the base is a short list of tools in
the order it needs them, recorded in the `ROUTING_SKILLS` map of `SkillTest`.
The four calls the base already fixes are deliberately absent from that list.

**References.** Anything a task reads once — a checklist, a rubric, one layer's
implementation guide — is a file below `references/`, named in `SKILL.md`
together with when to read it. One hop: a reference that loads another reference
is a body whose size the skill no longer decides.

**Judgment keeps a checklist; construction does not.** A skill that assesses
something needs a rubric and surfaces beside it. A skill that builds something
needs registries, and registries are tools.

**What it owns, and where it stops.** Every skill says `This skill owns …`, and
where its work runs into another skill's, the crossing is explicit: name the
verified stopping point, stop before editing the other owner's files, activate
that owner, carry across only the scope and verified behaviour it needs
([`R-SKL-3`](../requirements/task-skills/skl-3-crossing-into-another-skills-work-is-an-explicit-transition.md)).

## Publishing it

A skill exists for its readers once it is in `Installer::SKILLS`. Add it there,
add it to the lists in `tests/Smoke/InstallerTest.php` that read the published
directory back, and the installer copies `skills/base.md` into the new
directory as `references/base.md` — one copy per skill, because each of them
lands in another project alone and a link out of its own directory would resolve
here and nowhere it is actually read.

Then run the installer in the checkout that plays the environment the skill is
for, before any run that is meant to measure it. The published skills are a copy
and nothing reports that they are older than the server;
[todo.md](../todo.md) says which checkout plays which environment on this
machine and how the installer is reached there.

## What none of this holds

That a session **does** what a skill says. Every rule above is read off the file,
which makes it a proxy: the wording is present and a reorganisation can leave it
present while the behaviour goes.
[`D-EVI-2`](../decisions/evidence/evi-2-a-skill-crossing-is-read-rather-than-run.md)
accepts that proxy for the skill crossing and says why no forward run will
replace it. Everywhere else, what measures the behaviour is a case in
[scenarios/contracts/](../scenarios/contracts/readme.md) or an open review in
[scenarios/forward/](../scenarios/forward/readme.md) — and a forward run grades
the answer a session produced, never the file it came out of, which is why the
authoring contract is the half of a skill that has to be written down instead.
