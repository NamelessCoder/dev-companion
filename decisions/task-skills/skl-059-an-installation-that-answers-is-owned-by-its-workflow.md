---
id: D-SKL-059
title: 'An installation that answers is owned by its workflow'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::anInstallationIsBuiltInDependencyOrder
---

# D-SKL-059 — An installation that answers is owned by its workflow

**An installation that is up — running it and repairing what it answers — is
owned by `typo3-development-installation` rather than by a skill of its own.**

The guide names the domain in an intent, routes it to that skill, and the skill
gives half of it away in its closing sentence.

## Evidence

- **The re-run reproduces the substance and not the number.**
  `TaskGuide::answer()` was called from this worktree on 2026-08-18 with the
  feedback's query rebuilt —
  `task="Boot the local DDEV development installation for the blog extension repository"`,
  `changeType="operations"`, which is the quoted call minus the ellipsis nobody
  can restore. It answers `installation-operations` strong, `installation-setup`
  weak rather than the second strong match the report quotes, and one skill:
  `typo3-development-installation`.
- **Two intents naming one skill is the construction rather than a miss.**
  `knowledge/task-intents.json` declares
  `"skill": "typo3-development-installation"` on `installation-setup` and on
  `installation-operations`, and `TaskIntents::skills()` answers with the set.
  What the report read as one intent having no skill is both intents pointing at
  the same file.
- **The skill claims the domain in its description and disclaims it in its
  closing.** The description offers to "boot and repair the one the repository
  declares … and a site that will not come up"; **Where this stops** ends "it
  does not own what runs against the installation once it answers". What runs
  between those two sentences is whether the site answers at all.
- **The second arrival is a different task shape.** `feedback/2026-08-18-074606`
  is a frontend 404 on an installation that existed, booted and served both
  sides: "typo3-extension-conformance audits code,
  typo3-development-installation creates and boots, and the space between them —
  an installation that is up and misconfigured — has no skill." `D-SKL-056` read
  it as "a second task shape out of that directory reaching the same edge from
  the other end" and left its card standing for this question.
- **The answers are here and nothing orders them.** `bin/cli hints:probe` on the
  verbs this feedback lists — flushing caches after a code change, resetting the
  backend password, importing a colleague's dump — returns `installation-boot`,
  `page-cache-flushing` and `caching` on 2026-08-18. `installation-boot` and
  `installation-exception-output` are the two the skill already routes to, both
  from its create-and-boot half.
- **A fourteenth description has 25 characters to fit into.** The twelve
  published descriptions cost 3575 of the 3600
  `SkillTest::everyDescriptionIsWrittenToTheBudgetTheyShare` allows, counted
  from `Installer::skills()` on 2026-08-18. The thirteenth,
  `typo3-distribution-content`, is drafted and its publishing card sits in
  `todo/waiting/` with the room undecided.
- **Half of what the feedback lists has no order to write down.** Resetting a
  password, adding an editor user and flushing a cache after a code change are
  single commands that `installation-boot` and `page-cache-flushing` already
  carry. What has a working order is the diagnosis — what the installation
  answers, which site matched the request, what it wrote down — and taking over
  one somebody else built.

## Decided

- **Step 1b, and taken on.** The answers are available here and nothing says in
  which order to ask for them, which is the half of that rung a workflow fills.
- **A section in the skill that exists rather than a fourteenth skill.** The
  discriminant a second one would need is whether the site answers at all, and
  nobody types that: `074606`'s user reported a served 404 as "das frontend
  zeigt immer noch 404", which reads as a site that will not come up and is a
  site that answers. A skill chosen on that line is chosen wrong at the moment
  it decides anything. The budget above is the second reason and the weaker one.
- **What the section also avoids is named as a consequence rather than as a
  reason.** A new skill buys a baseline run and a review before publication and
  an edit stays on the author's word (`D-SKL-035`), so the cheap answer and the
  right one coincide here — which is the coincidence `D-SKL-052`'s fourth
  **Wrong if** watches for, and it is written down rather than used.
- **Where the boundary runs.** Inside: what the installation answers and which
  site matched, the log that is empty because nothing was thrown, the site
  configuration of a running installation, and taking over one built by somebody
  else — the dump, the schema behind it, the password nobody has. Outside,
  unchanged: hosting, deployment and backups, and the major upgrade, which
  `installation-upgrade` already carries a checklist for and which is a project
  undertaking rather than a verb of the installation somebody develops in.
- **`074606` is folded into this card and its own is deleted.** It asks whether
  the domain has an owner, which is the question decided above, and two cards
  would each carry half of one gap (`R-FBK-014`). `D-SKL-056` kept it so that a
  rewrite would not hide the question; this card answers it. Its **Prove it**
  half rides along rather than staying behind: an empty log separating a
  deliberate status code from an uncaught exception is the first step of the
  diagnosis the section owns.
- **The hand-off the feedback asks for mostly stops being one.** It wants an
  explicit hand-over at the end of `typo3-development-installation` into the
  operational verbs and into `typo3-extension-conformance` for a first-boot
  deprecation log. Once the running half is that skill's own, only the
  conformance crossing is a hand-over, and what form a crossing takes is
  `feedback/2026-08-18-074245`'s card.
- **The 1a facts keep their own cards.** `074200` — which of two colliding site
  bases wins — and `074545` — the site the core auto-creates for a new root page
  — are what this section would route to, and each is judged on its own card.
- **Priority `normal`, set by arrival rather than by weight.** Two task shapes
  in one directory reached the domain, which is not `low`. One repository, one
  session series and two finished tasks are not `high`.
- **Both feedback stay open behind the card**, which is what `D-FBK-017` asks of
  a judgement that turns a feedback into work.

## Assumed

- That the two reports are two sessions rather than one filing twice. Both are
  `/home/benji/projects/blog` half an hour apart, and
  `feedback/2026-08-18-071603` describes the first as a boot session debriefed
  at its end.
- That the discriminant is unusable rather than merely awkward. One report says
  a user called a served 404 a frontend that does not come up.
- That the diagnosis has an order at all. It is read off `074606`'s account of
  its own session and off no run made here.

## Wrong if

- The reading finds the running half is the hints the probe already returns plus
  a routing line. Then it belongs on the `installation-operations` intent, and
  this entry grew a workflow for a checklist.
- The section lands and a session whose installation answers still finds nothing
  past "the site answered". Then the domain wanted a file of its own, and a
  budget decided a boundary the work should have decided.
- `typo3-development-installation` stops being read whole once it carries both
  halves. That is `D-SKL-052`'s first **Wrong if** arriving in the file this
  entry grows.
- The listing turns out not to bind — a client with more room, or a trim that
  frees a description — and the domain is then split cleanly in two. This entry
  would have read a wall as a boundary.

## Since then

The section was written on 2026-08-18, as **The installation that already
answers**, and the fork above it names the shape that enters there — an
installation that is up is neither created nor booted. The description was not
touched: it already offered to boot and repair, which is what this entry read as
the skill claiming the domain, and the 25 characters the listing has left would
not have paid for more. Two things the section does not carry, because nothing
here answers them yet: what a request that matched a site and then answered
not-found means, which is
`todo/open/2026-08-18-133000-say-what-a-not-found-means-when-a-site-was-matched.md`,
and what form the crossing into `typo3-extension-conformance` takes beyond
naming it, which is `feedback/2026-08-18-074245`'s card. The second **Wrong if**
is what a session arriving with a running installation will answer, and nothing
has yet.
