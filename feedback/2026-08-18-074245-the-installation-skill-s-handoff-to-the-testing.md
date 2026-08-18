---
date: 2026-08-18T07:42:45+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-development-installation, typo3-extension-testing, typo3-extension-upgrade, typo3_task_guide
directory: /home/benji/projects/blog
---

# The installation skill's handoff to the testing and upgrade skills sits in closing prose and stay...

## Observation

Task chain in one session on t3g/blog: diagnose a frontend 404 on a DDEV TYPO3 14.3.6 installation, then fix a v14 IncompleteRecordException in a ViewHelper, then add functional tests for it, then prove it on both declared majors, then change the extension's setup service.

typo3-development-installation was the right skill for the opening, and typo3_task_guide with changeType operations named it, which matched. It carried the first half well: read what the repository declares, boot rather than repair, read the failure from what the installation wrote down rather than from the rendered error page. That last instruction saved me a wasted fetch — I went to var/log/typo3_*.log instead of parsing the 404 body, and that is where the exception with its class, code and stack was.

The session then left that skill's scope entirely and I did not notice. The skill does say so, in its closing "Where this stops": tests and static checks are typo3-extension-testing, with the crossing described in both directions. I read that section when the skill loaded, at which point I had a 404 and no tests in sight. Forty minutes and several user turns later, when the user asked for unit and functional tests and for proof on 13.4 and 14.3, I extended Tests/Functional/... and built a second core installation without activating typo3-extension-testing or typo3-extension-upgrade, both of which exist and whose descriptions fit what I was doing almost word for word ("extend ... PHPUnit unit and functional tests" and "proving every version it claims").

A handoff written at the end of a skill is read once, before the work that triggers it exists. That is the failure mode worth reporting: not a missing skill, and not a wrong description — two correct skills that stayed shut because nothing re-raised them at the moment the task changed shape.

## Query

Skill typo3-development-installation activated with args "Blog-Setup wurde ausgeführt, Frontend liefert weiterhin 404". Later user turns in the same session: "zudem hast du keine unit / functional tests dafür angelegt" and "wir haben support für 13.4 und 14.3 deklariert auch dein bugfix von gerade muss also in beiden versionen laufen".

## Suggestion

Make the crossing an event rather than a closing paragraph. Where a skill hands work to another, state the trigger in the imperative at the point the work is likely to arrive — "the moment this task grows a test, stop and activate typo3-extension-testing" — rather than in a section about scope. typo3_task_guide could reinforce it: it is cheap to call again mid-session, but nothing in the skill told me to re-run it when the task changed from operations to test, so I did not.
