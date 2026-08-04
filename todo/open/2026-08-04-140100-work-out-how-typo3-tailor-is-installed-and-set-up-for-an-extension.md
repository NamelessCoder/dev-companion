# Work out how `typo3/tailor` is installed and set up for an extension

**Serves:** R-SKL-009
**Priority:** high

Extension release is a subject this server does not serve, and it is intended to
return. What such a task is at all was the question: help carry a release out,
describe the procedure so the person does it, or set `typo3/tailor` up so that
releasing is possible in the first place. That question is answered, and the
answer is the third. By this repository's own vocabulary — a tool answers a
question, a skill orders a task
([judging.md](../../documentation/feedback/judging.md)) — the third is a
different task from the other two, so it is a different skill rather than a
section of one. **It applies to extensions only**, not to projects and not to
sitepackages.

So the step is: establish how `typo3/tailor` is installed and configured for an
extension, and what a skill ordering that task would look like. **Do not write
an account of Tailor from what you remember.** It is not vendored here — no
`composer.json` and no `composer.lock` in this checkout requires it, checked on
2026-08-04, and the only mention anywhere is one hint in
`knowledge/hints/extension.json` saying that Tailor and the TER read
`ext_emconf.php`. One thing about it was measured rather than recalled and is
worth starting from: `tailor create-artefact` filters by its own
`conf/ExcludeFromPackaging.php` and never reads `.gitattributes`, so it keeps
files a `git archive` of the same commit drops for an `export-ignore` attribute
— which is how one commit hands two registries different file sets, and it is
the only property of the tool this repository has established. The rest of its
commands and the order they run in are part of the work and come from its own
documentation and release notes, which is what
[working-a-todo.md](../../documentation/feedback/working-a-todo.md) prescribes
for a tool this repository does not own. Getting that wrong here is how a guess
ends up in a file no release of this server corrects.

What the answer has to distribute is the range the maintainer named: simple
tagging, generating a changelog, adjusting version numbers, through to
publishing — via `typo3/tailor`, git tags, and Composer/Packagist. That is the
material rather than the question. Setup is what this card owns; whichever of
those steps setup does not reach is what the next question is about, and it is
not answered here.

`R-SKL-009` is what this serves and is where the reading starts: what a release
answer is about, and the four things a workflow around it still has to settle.
The priority above is `high` because the need is measured and unserved — a run
in `E-EXT` made forty-one `Bash` calls, activated no skill and called no tool.
Lower it if the hole is acceptable while the setup question is worked, and say
so in the file rather than in a session.

**The copies of a skill already in other people's projects.**
`Installer::publishSkills()` removes a skill that has left `Installer::SKILLS`:
it diffs the list against `skills` in the project's own
`.typo3-cms-mcp/state.json` and deletes the directory of each name the list no
longer carries, which
`InstallerTest::codexUpdateRemovesSkillsTrackedByThePreviousCentralState` holds.
So a project that runs `bin/typo3-cms-mcp install` or `update` again is brought
into line, and the ordinary case needs no change to the installer. Two cases are
left and neither is fixed by anything here: a project that runs neither command
keeps its copy forever, and one whose `state.json` is missing or predates the
skills record has nothing to diff against, because `readState()` answers an
absent file with an empty list. Nothing reports either. Whether that is worth a
change to the installer is the maintainer's, and it needs a decision of its own
before anybody writes one.
