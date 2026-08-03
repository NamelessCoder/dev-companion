# Say where a one-off script may not be written, and reach the session about to write one

**Serves:** feedback/2026-08-01-003938-attempted-to-write-a-php-reflection-script-into.md
**Priority:** low

Step 2 of the ladder, on the evidence in
[`D-KNW-026`](../../decisions/knowledge/knw-026-where-a-one-off-script-may-not-be-written-is-a-gap-this-server-owns.md):
`project-repository-layout` says a one-off script goes into `Build/` and names
`var/` as the place it may not go, because that directory is ignored. It never
names the document root, which is where this session wrote its debug file.
Establish first where the document root of a Composer TYPO3 installation is,
from `extra.typo3/cms.web-dir` in `composer.json` and the default the installer
applies, and what DDEV's TYPO3 project type serves from `/var/www/html` — from
DDEV's own documentation, because that is the claim the entry's first **Wrong
if** turns on. Then extend that clause in `knowledge/hints/project.json` so it
names the document root and says what makes it the wrong place: a file there is
reachable over HTTP and outlives the run that wrote it. Add the words to the
hint's `appliesTo` that a session about to write such a file would use —
`debug script`, `one-off script`, `scratch script`, `document root`, `webroot` —
and measure the change with `bin/cli hints:probe` on the two phrasings in the
entry that reach nothing today. Then write the requirement for what has to keep
holding. `feedback/2026-08-01-003933` is being judged on another branch for the
positive half, reading the installed class rather than experimenting on it; read
what it landed and put the two sentences in one place rather than in two.
