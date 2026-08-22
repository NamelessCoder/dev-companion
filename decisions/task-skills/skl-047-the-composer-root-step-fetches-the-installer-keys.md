---
id: D-SKL-047
title: 'The Composer root step fetches the installer keys'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::anInstallationIsBuiltInDependencyOrderAndHandsOverOnceItAnswers
---

# D-SKL-047 — The Composer root step fetches the installer keys

**Step 1 of `typo3-development-installation` routes to `typo3_hint_lookup` with
`id=extension-repository-installation` for the `extra` block, rather than to the
manual and to a package that is not installed yet.**

The step names two sources and neither answers at the moment it is read: the
indexed manual defers the keys, and the installed installer package is what the
manifest being written is there to install.

## Evidence

- `feedback/2026-08-17-212152` is a v14 demo build that wrote the root
  `composer.json`, `.ddev/config.yaml` and `.gitignore` in its first fifteen
  minutes, before anything was installed, and traces two of a reviewer's ten
  findings and one filed gap to those minutes. It asked
  `typo3_documentation_lookup` twice, read no page with `page`, and reports that
  neither answer changed a decision.
- **The step's second source cannot exist when the step runs.** Step 1 says to
  "read the installed installer package where the documentation is thinner than
  the question", and the package that installs TYPO3 beneath the repository is
  what the manifest under construction has to pull in. Nothing is installed
  until step 3.
- **The answer is in the corpus.** `extension-repository-installation` states
  `extra.typo3/cms.web-dir` and where the installer writes `index.php`,
  `config.vendor-dir` and `config.bin-dir` with the console path they produce,
  the message `app-dir` and `root-dir` are accepted and then ignored with, the
  reset that follows a `web-dir` outside the Composer root, and the
  `typo3/cms-cli` constraint that cannot resolve and names `typo3/cms-core` as
  the conflict.
- **It is reachable and routed from nowhere.**
  `bin/cli hints:probe "extra block the TYPO3 Composer installer reads to install TYPO3 beneath the package"`
  returns that hint alone at `text only(405)` on 2026-08-18, and a search of
  `skills/` for its id returns nothing on the same day. Steps 2 and 5 of the
  same file name `php-versions`, `project-configuration-files` and
  `project-build-and-scripts`.
- **The step keeps a paraphrase where the id would go.** Its "three properties
  of that step survive any version" are three statements of that hint: a layout
  key that warns rather than errors is `app-dir`'s message, the package the core
  requires itself is `typo3/cms-cli`, and the empty extension directory below
  the document root is the hint's closing statement. The skill opens by
  forbidding retained keys and package names, and this is the one step that
  retains both.
- **The other two facts the report names have landed.** `D-KNW-086` put the
  interpreter into `php-versions` and into step 2; `D-KNW-088` put what a
  Composer installation generates onto `project-build-and-scripts` and
  `public-assets`, which step 5 already names.
- **Convergence is not what carries this.** `bin/cli feedback:list` reports 13
  open on 2026-08-18, all from one directory and one debrief, so the evidence is
  the checkout rather than a second session.

## Decided

- **Step 3 of the ladder, routing.** The right answer exists, is reachable by
  the query the moment produces, and the step that asks the question points
  somewhere else.
- **Queued rather than closed on the spot.** A skill is installed into somebody
  else's project, so its contract is reviewed rather than improvised, and
  `documentation/records/judging.rst` puts it on the todo side of that line.
- **The feedback is trimmed to this half.** The interpreter and the generated
  paths are answered, and the report says the second was filed as a card of its
  own.
- **Rejected: making the `no-installation` answer say what can still be asked.**
  `Result\Unsupported` is one class for every tool that reads an installation,
  so a route written there is attached to every unanswerable answer rather than
  to the moment before an install. `D-ANS-083` took the one pointer that answer
  carried back out, and `D-ANS-061` decided on three sessions that naming a tool
  in an answer is not the lever. What owns the moment is the workflow step,
  which is where `R-KNW-072` put the interpreter and where this puts the keys.
- **Not a new hint, and not a document.** What was missing is a route to one
  statement, not a statement and not an order of steps.
- **Priority `normal`.** One session reported it, so not `high`; the file it
  informs is written once and never revised, so not `low`.

## Assumed

- That the hint's `extension` scope is the shape this step has. The step makes
  the package's own manifest the Composer root, which is that hint's subject,
  and a project whose root is not a package is the other branch of the skill.
- That the paraphrase can come out whole. Each of the three properties is read
  here as a restatement of a statement in the hint, from the two texts and not
  from a run that fetched both.
- That what the session asked `typo3_documentation_lookup` is what its report
  says. Nothing here reproduces those two calls.

## Wrong if

- The routing lands and a session still writes the `extra` block from memory.
  Then the id was reachable and unread, which is delivery rather than routing,
  and the answer belongs in the step's own prose.
- The hint turns out to answer for an extension repository and not for the
  sitepackage-plus-distribution shape the report was building, so the step is
  routed to a statement that is true beside the question. Then a second hint is
  owed and this is step 1a.
- A session follows the id and finds the manual carried something the hint does
  not. Then the step's first source was doing work and only its second was
  circular.

## Since then

The routing landed and the manual half came out with the circular half, because
the third **Wrong if** was measured rather than assumed. Asked at 14.3 on
2026-08-18, `typo3_documentation_lookup` reproduces what the report describes:
three queries about the installer's `extra` block return six page titles and the
best carries 37% of the query's weight. The one page whose title does cover the
question, *Installing TYPO3 with Composer* at 78%, teaches
`composer create-project typo3/cms-base-distribution` and contains none of
`web-dir`, `vendor-dir`, `bin-dir`, `app-dir` or `typo3/cms-cli` — it documents
the project shape, not the package that is its own Composer root. Asking for the
keys by name returns nothing at all, because that index is page titles and
section paths and no page is titled after one. So the manual was not the working
half of the sentence, and the third **Wrong if** does not hold.

Two things were found beside the step and left where they were. The manual's
*composer.json* page carries `extra.typo3/cms.extension-key`, `.version` and
`.Package.providesPackages`, which this hint does not — those are the manifest's
extension metadata rather than the installation's layout, and
`extension-manifest` already owns them for a caller who asks about them. And the
plugin allowance the step also names is Composer's own configuration, which
nothing here covers, so the step now says so instead of pointing at a source
that would not answer it.
