---
date: 2026-08-01T00:39:29+00:00
category: tool-gap
status: closed
closed: 2026-08-03
model: opencode/deepseek-v4-flash-free
tool: typo3-extension-testing
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session, missed item: functional-test database sprawl and lo...

## Observation

Debrief of the TYPO3 14 testimonials session, missed item: functional-test database sprawl and loss of track. Each functional test class creates its own per-class database (printworks_ft + sha1 prefix); over the session many such databases accumulated, and the assistant lost track of which records had been manually modified across the live DB versus test DBs. The user's corrective feedback included 'created tons of databases, lost track what manually were modified instead of using api'.

## Query

functional test per-class databases, tracking which records were manually modified

## Suggestion

Explain the functional-test per-class database model and how to track/manage the test databases; and state that manual DB modifications should be recorded or avoided in favor of DataHandler/fixtures so state stays reproducible.
