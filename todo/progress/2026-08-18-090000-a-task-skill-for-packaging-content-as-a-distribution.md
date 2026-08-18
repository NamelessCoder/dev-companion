# A task skill for packaging content as a distribution

**Serves:** feedback/2026-08-17-212538-no-skill-owns-the-project-as-a-deliverable-or.md
**Priority:** normal
**Branch:** todo/a-task-skill-for-packaging-content-as-a-distribution
**Claimed:** 2026-08-18

Read the producing side of a distribution artifact before a line of the skill is
written, because nothing here has established it: make an installation with
`bin/cli environment:create`, seed content into it with DataHandler, export it
with the command `impexp-artifact` prescribes, place the artifact and its files
directory in a package, ship the site configuration through
`Initialisation/Site/`, and import the package into a second clean install to
see what arrives. `D-SKL-050` records the boundary that reading is bounded by
and why this is one skill rather than the two the feedback asks for;
`documentation/contributing/writing-a-skill.rst` is what the file is then
written under, including the baseline run `D-SKL-035` buys and the intent
`D-SKL-013` requires in the publishing commit.
