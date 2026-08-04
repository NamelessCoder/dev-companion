# Work out how `typo3/tailor` is installed and set up for an extension

**Serves:** R-SKL-009
**Priority:** high

`typo3-extension-release` is unpublished as of 2026-08-04 and this card is the
work that decides what replaces it. The maintainer's reading is that the skill
was never defined or tested: it was not clear what it is supposed to do at all —
help carry a release out, describe the procedure so the person does it, or set
`typo3/tailor` up so that releasing is possible in the first place. That
question is answered, and the answer is the third. By this repository's own
vocabulary — a tool answers a question, a skill orders a task
([judging.md](../../documentation/feedback/judging.md)) — the third is a
different task from the other two, so it is a different skill rather than a
section of one. **It applies to extensions only**, not to projects and not to
sitepackages.

So the step is: establish how `typo3/tailor` is installed and configured for an
extension, and what a skill ordering that task would look like. **Do not write
an account of Tailor from what you remember.** It is not vendored here — no
`composer.json` and no `composer.lock` in this checkout requires it, checked on
2026-08-04, and the only mentions anywhere are an archived feedback, one hint in
`knowledge/hints/extension.json` about `ext_emconf.php`, and the assertion in
`SkillTest::aReleaseVerifiesTheArtifactAgainstEachRegistrysOwnExclusions` that
keeps the package name out of a published body. Its real commands and the order
they run in are part of the work and come from its own documentation and release
notes, which is what
[working-a-todo.md](../../documentation/feedback/working-a-todo.md) prescribes
for a tool this repository does not own. Getting that wrong here is how a guess
ends up in a file no release of this server corrects.

What the answer has to distribute is the range the maintainer named: simple
tagging, generating a changelog, adjusting version numbers, through to
publishing — via `typo3/tailor`, git tags, and Composer/Packagist. That is the
material rather than the question. Setup is what this card owns; whichever of
those steps setup does not reach is what the next question is about, and it is
not answered here.

The unpublication is done and needs no repeating: `typo3-extension-release` is
out of `Installer::SKILLS`, which is also the list the resource surface reads,
so it is neither installed nor served —
`ResourceSurfaceTest::onlyAPublishedSkillIsOffered` holds the second half. The
file stays in `skills/`, the state `typo3-development-installation` has been in
since 2026-08-03.

**The copies already in other people's projects.** `Installer::publishSkills()`
removes a skill that has left `Installer::SKILLS`: it diffs the list against
`skills` in the project's own `.typo3-cms-mcp/state.json` and deletes the
directory of each name that is no longer published, which
`InstallerTest::codexUpdateRemovesSkillsTrackedByThePreviousCentralState` holds.
So a project that runs `bin/typo3-cms-mcp install` or `update` again loses its
copy, and no change to installer behaviour is needed for the ordinary case. Two
cases are left and neither is fixed by anything here: a project that never runs
either command keeps the copy forever, and one whose `state.json` is missing or
predates the skills record has nothing to diff against, because `readState()`
answers an absent file with an empty list. Nothing reports either, and the copy
is a workflow nobody defined. Whether that is worth a change to the installer is
the maintainer's, and it needs a decision of its own before anybody writes one.

What is also left for the maintainer, priced here rather than decided: this
withdrew the only release workflow the server had, and `R-SKL-009` — held, and
earned by a measured run in `E-EXT` that made forty-one `Bash` calls, activated
no skill and called no tool — now describes a property that reaches no session.
Its tests still pass, because they read `skills/` rather than what is published.
The priority above is `high` for that reason: the need was measured once and is
unserved again. Lower it if the hole is acceptable while the setup question is
worked, and say so in the file rather than in a session.
