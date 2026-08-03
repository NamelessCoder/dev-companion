# Writing a task skill

What the published skills are is in [AGENTS.md](../../AGENTS.md), and the order
every one of them starts in is [skills/base.md](../../skills/base.md), which is
one file rather than a paragraph each. This page is the other half: how a skill
is **written**, and what holds each rule to that.

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
in [scenarios/](../../scenarios/readme.md) or a run in
[scenarios/runs/](../../scenarios/runs/) where the existing workflow was reached
for and came up short.

What such a run shows is almost always smaller than the domain: an order nobody
keeps, a step that only runs when a finding happens to walk into it, a boundary
two skills both believe they own. The skill is written around that and around
nothing else. "Less is more" is not a preference here — it is an instruction
every session in another project loads before it does anything.

## Before it is written

A skill is written against the current state of the practice, never from recall.
What that costs is one session's reading; what recall costs is a file that
states, permanently and in somebody else's project, a practice that was current
when its author last happened to see it. Before the first line:

- Ask this server what it already answers about the domain, with the tools the
  skill will route to. `typo3_documentation_lookup` for the official
  documentation at the versions in play, `typo3_hint_lookup` for the
  conventions, `typo3_changelog_lookup` for what moved. An author who has not
  called a tool is routing to an answer shape they are guessing at, and a
  surface the server already covers does not need a paragraph in a skill.
- Read the current official documentation of the domain itself, and where the
  task runs through tools this server does not own — a packaging tool, a
  registry, a CI runner, a test harness — read theirs. Which tools exist, and
  what each one does by default, is exactly the fact that moves after the file
  is published.
- Read what the failing run actually did, call by call, rather than what its
  report concluded. The gap the skill is written around is in the calls.

None of that research goes into the skill; the rule below still holds. It
decides what the skill **asks**: which surfaces exist at all, which of them a
tool already owns, and where the practice moves fast enough that only an
instruction to check survives being written down. Written from recall, a skill
invents surfaces that do not exist and misses the one that decides the case.

## The draft is read by somebody before it is published

A skill is not finished when its tests pass. Those hold its shape — the name,
the base, the references, that it keeps no second copy of what a tool owns — and
no assertion here can say whether the workflow it describes is the one a
maintainer actually runs, whether its order matches how the work really goes, or
whether the step that decides the outcome is in it at all. The person who asked
for the skill can say all three, and is the only one who can.

So the draft is shown before it is published: `SKILL.md` and every reference,
whole, not summarised. And feedback is **asked for by name** — does this match
how the task is really done, which step is missing, which one is wrong, what
does it claim that is not true here. "Does this look good?" gets agreement, not
review. What comes back is worked in before the skill reaches
`Installer::SKILLS`, because the copy in somebody else's project is not
corrected by the next release of this server.

## The rules, and what holds each one

- It is filed under the name it calls itself, with a description a client can
  route on — `SkillTest::everySkillIsPublishedUnderTheNameItCallsItself`
- It starts from the base before it reaches for anything of its own —
  `SkillTest::everySkillStartsFromTheBaseBeforeItsOwnEvidence`
- It keeps no second copy of what a tool owns —
  `SkillTest::noSkillKeepsASecondCopyOfWhatAToolOwns`
- It routes through the owners of its own facts, in the order it needs them —
  `SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder`
- Every reference is one hop away and loaded on demand —
  `SkillTest::everyReferenceIsOneHopAwayAndLoadedOnDemand`
- A skill that judges keeps its checklist beside it —
  `SkillTest::judgmentHeavySkillsKeepTheirChecklistBesideThem`
- It says what it owns — `SkillTest::everySkillStatesWhatItOwns`

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
[`D-AUD-003`](../../decisions/audience/aud-003-the-instructions-carry-the-entry-point.md)
is what a wrong one costs: a review prompt whose every criterion the conformance
skill's body would have met did not activate it, and all thirty-five calls of
that session went through Bash.

It names **every side of the domain the skill owns**, and a skill that owns two
sides of one thing says so in the opening line rather than in the ninth item of
a list. A domain named by one of its halves — "frontend content elements" for a
skill that also owns the backend preview of the same element — sends the other
half to whichever skill happens to carry a word of it, and the body that covers
it is never loaded. That is the same entry's second sighting, one task later,
and `SkillTest::aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement` holds the
pair it was measured on. Which sides a skill owns is not readable off the file,
so nothing holds this over the directory: it is a question the author answers
against `This skill owns …` and the crossings in the body, and a crossing that
names one side while the description names both is the file disagreeing with
itself in somebody else's project.

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
([`R-SKL-003`](../../requirements/task-skills/skl-003-crossing-into-another-skills-work-is-an-explicit-transition.md)).

## Publishing it

A skill exists for its readers once it is in `Installer::SKILLS`, and nothing
this server answers with may name one before that: `knowledge/task-intents.json`
routes a recognized task to the skill that owns it
([`D-SKL-013`](../../decisions/task-skills/skl-013-the-guide-names-the-skill-that-owns-the-task.md)),
and `SkillTest::everySkillNamedInKnowledgeIsPublished` holds every name there to
that list. A route into a skill nobody has installed is worse than none, because
the caller cannot tell the two apart. Add it there, add it to the lists in
`tests/Smoke/InstallerTest.php` that read the published directory back, and the
installer copies `skills/base.md` into the new directory as `references/base.md`
— one copy per skill, because each of them lands in another project alone and a
link out of its own directory would resolve here and nowhere it is actually
read.

Then run the installer in the checkout that plays the environment the skill is
for, before any run that is meant to measure it. The published skills are a copy
and nothing reports that they are older than the server;
[todo/reference/](../../todo/reference/) says which checkout plays which
environment on this machine and how the installer is reached there.

## What none of this holds

Three of the steps above are the author's and nothing reads them off a file:
that a domain earned a skill at all, that the practice was researched before it
was written, and that the draft was shown and asked about. Each leaves the same
trace as its absence — a skill written from recall is shaped exactly like one
written from the documentation, and it is wrong in places no assertion knows to
look. They are written down because that is all that can be done for them, and
because the author who skips them is usually the one who has not read this page.

And that a session **does** what a skill says. Every rule in the table is read
off the file, which makes it a proxy: the wording is present and a
reorganisation can leave it present while the behaviour goes.
[`D-EVI-002`](../../decisions/evidence/evi-002-a-skill-crossing-is-read-rather-than-run.md)
accepts that proxy for the skill crossing and says why no forward run will
replace it. Everywhere else, what measures the behaviour is a case in
[scenarios/contracts/](../../scenarios/contracts/readme.md) or an open review in
[scenarios/forward/](../../scenarios/forward/readme.md) — and a forward run
grades the answer a session produced, never the file it came out of, which is
why the authoring contract is the half of a skill that has to be written down
instead.
