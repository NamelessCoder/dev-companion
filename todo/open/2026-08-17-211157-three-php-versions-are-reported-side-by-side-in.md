# the supported PHP range for a target version is readable only where an installation already exists

**Serves:** feedback/2026-08-17-211157-three-php-versions-are-reported-side-by-side-in.md
**Priority:** low

Judge the half of this feedback that is left rather than fix what it reports.
The other half landed on 2026-08-18 as `R-PRJ-010`, and this one is the sentence
that follows it: the PHP a project will run is chosen while writing
`.ddev/config.yaml`, before anything is installed, and every tool that could
state a supported range answers from an installation. So the number is set at
the one moment nothing here can be asked about it, and it stops looking like a
decision as soon as the container is built.

Settle first whether this is a knowledge question or a tool one — what PHP a
TYPO3 major supports is a fact per major and could be a statement in
`knowledge/`, which needs no installation and is what `D-KNW-055` says the major
does not carry — and whether `typo3_server_scope` or the task skills are where a
caller who has no project yet would meet it. `documentation/records/judging.rst`
is the ladder; what the feedback actually says is in the file it serves.
