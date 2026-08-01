---
date: 2026-08-01T00:39:38+00:00
category: wrong-answer
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3extensiontesting
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session, missed item: the assistant attempted to write a PHP...

## Observation

Debrief of the TYPO3 14 testimonials session, missed item: the assistant attempted to write a PHP reflection script into the webroot (/var/www/html/check_record.php) and execute it inside the container to introspect the Record class; the user rejected that action. Writing and executing arbitrary PHP scripts in the live webroot to debug core internals is a boundary violation that was not recorded.

## Query

writing and executing a PHP script in the live webroot to introspect core classes

## Suggestion

Provide the safe way to introspect installed classes (e.g. a functional test, a CLI command, or reading the source) and explicitly forbid executing debug scripts in the live webroot.
