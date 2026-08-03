---
date: 2026-08-01T00:30:03+00:00
category: missing-knowledge
status: closed
closed: 2026-08-02
model: opencode/deepseek-v4-flash-free
tool: typo3-extension-testing
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session: the assistant repeatedly did not understand the fun...

## Observation

Debrief of the TYPO3 14 testimonials session: the assistant repeatedly did not understand the functional test data model. It missed that every test run boots an empty database and that fixture CSV records are the only data present, so it kept trying to fetch or verify data that was never primed, and had to be prompted with 'do you use the correct request type in the tests?' and 'is your dataset correct?'. It also resorted to direct database inserts on the live database instead of using the DataHandler/import APIs, and lost track of which records were manually modified. The priming requirement and the request-type handling are the two concepts the task guidance should have stated up front.

## Query

functional test empty database per run, fixture priming, request type, DataHandler for record operations

## Suggestion

In the test task guide, state explicitly: each functional test run starts from an empty primed database, fixtures in the CSV are the sole data contract, and record operations in tests should go through the DataHandler. Also warn that manual live-database edits are not reproducible and are outside the API.
