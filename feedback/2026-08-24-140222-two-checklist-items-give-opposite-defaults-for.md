---
date: 2026-08-24T14:02:22+00:00
category: wrong-answer
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_hint_lookup
directory: /home/benji/projects/ext-usercentrics
---

# Two checklist items give opposite defaults for config/system/additional.php and I picked the wron...

## Observation

Task: DDEV development installation for an extension, TYPO3 14.3.6, database in DDEV's own db container.

typo3_task_guide (changeType=operations) returned two checklist items about the same file that point in opposite directions:

1. "Leave config/system/additional.php to DDEV where DDEV writes it. While the #ddev-generated marker line is in that file it is rewritten by every start that finds an installed TYPO3 ... Where the installation does not use DDEV's database container, on SQLite or with omit_containers: [db], that generated mysqli connection overrides what typo3 setup wrote into settings.php: take the file over by removing the marker line and leaving its database section out."

2. "A clone is where that detection finds nothing ... the ddev start that necessarily precedes composer install writes no additional.php, and every request answers exception 1396795884 for the trusted hosts pattern while typo3 setup reports success. Start the environment again once the dependencies are installed — a post-start hook that installs them does not close it, because the detection runs before the hooks — or commit a project-owned additional.php that is there from the first request."

Item 1's default is: leave it generated, take it over only when not on DDEV's database. My installation was on DDEV's database, so item 1 said leave it. Item 2 offers "commit a project-owned additional.php" as a way to get a single-command start, with no condition attached.

I took item 2, wrote a project-owned additional.php with all four sections, set disable_settings_management: true, and un-ignored that one path in .gitignore. It worked — one ddev start brought a fresh clone up.

The user rejected it: "du hast ddev configs committed", then "also auch config / system gehört da nicht rein", then "schaue im bootstrap_package wie es da configuriert ist". bootstrap_package tracks .ddev/config.yaml, ignores config/ entirely, has no hooks at all, and lets DDEV generate additional.php — item 1's position exactly. I reverted to it.

So the server held the right answer and I read past it, because item 2 reads as an equally-weighted alternative rather than as the exception. The prediction in item 2 was otherwise perfect: my first ddev start on a clone-like state produced HTTP 500 on every URL with an empty log, and I recognised it immediately as 1396795884 because of that sentence.

## Query

typo3_task_guide task="Set up a DDEV development installation for a TYPO3 extension so its site set can be verified in a real frontend, and add functional tests that need a database", changeType=operations, paths=[".ddev/config.yaml","composer.json","Configuration/Sets/Usercentrics/config.yaml","Tests/Functional"], targetVersion=14. Then typo3_hint_lookup id=project-configuration-files.

## Suggestion

Make the two items one item with an order of preference, and say what the cost of the second route is rather than presenting it as symmetrical: committing a project-owned additional.php puts installation state into the repository, which is wrong for a distributed extension and is what a reviewer will reject, so it belongs to project repositories that deploy from the checkout and not to extension repositories. For an extension, name the accepted shape outright — .ddev/config.yaml declares the container, config/ is ignored whole, the install is documented rather than hooked, and a fresh clone needs ddev start, composer install, ddev restart in that order. That is what our own reference repository does and it is what I was corrected to.
