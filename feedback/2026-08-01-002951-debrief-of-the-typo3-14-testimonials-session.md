---
date: 2026-08-01T00:29:51+00:00
category: missing-knowledge
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3documentationlookup, typo3extensiontesting, typo3contentelementdevelopment
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session. The user had to correct and steer the assistant rep...

## Observation

Debrief of the TYPO3 14 testimonials session. The user had to correct and steer the assistant repeatedly, and every correction was a point the server should have answered up front. Concrete corrections the user made: (1) the Fluid f:if template needed an explicit <f:then> branch — the user said 'you added the else but not the then', 'did you use the f:then? your condition seems wrong please check the IF viewhelper documentation', 'list just frap the link viewhelper in <f:then>' — the external-URL company link rendered empty until this was fixed; (2) for the backend preview the user corrected my wrong assumption that data comes from TypoScript: 'the data for the backendpreview is inserted, its not comming from typoscript, please check the documentation'; (3) the user pushed twice toward the Record API documentation: 'do you not think there is any documentation how the data is processed?' and 'please check the record api'; (4) the user stopped a reflection-script rabbit hole with 'you do not need this, for the backend preview'; (5) on tests, the user had earlier prompted 'do you use the correct request type in the tests?' and 'is your dataset correct?'; (6) the session ended with 'ok lets stop here, please note you just do not have the knowledge how to do it'. Each of these was an answer the knowledge base should have supplied without the user's intervention.

## Query

Fluid f:if f:then f:else structure; backend content preview data source (record, not TypoScript); Record API field access; functional test request type and dataset priming

## Suggestion

Cover, at the depth a preview/test task needs: (a) that f:if with an f:else requires an explicit f:then and that typolink output inside a conditional branch is swallowed otherwise, (b) that v14 backend preview templates receive the record, not TypoScript-derived data, (c) the Record API field-access mechanism, (d) that functional tests boot an empty primed state per run so fixtures are the only data contract.
