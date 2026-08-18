# What a failing installation's exception is read from

**Serves:** feedback/2026-08-17-212702-an-agent-meeting-http-500-scrapes-the-exception.md
**Priority:** normal

Establish what an agent whose site answers HTTP 500 can read the exception from,
before a word of the hint is written: the default `LOG.writerConfiguration` and
the `var/log/typo3_<hash>.log` name `FileWriter` derives, the codes
`AbstractExceptionHandler` refuses to log and how that list differs across
`.checkouts/12.4`, `13.4`, `14.3` and `main`, and what decides whether the page
shows the message at all. Then write it as a hint of its own in
`knowledge/hints/project.json`, curated with the multi-word phrases a caller
arrives with rather than with the subject it is filed under, and route the
skill's proving step and its boot section to it. `D-KNW-092` records the
boundary and why this is a hint of its own rather than a statement inside
`installation-boot`, and `D-ANS-084` is what a phrase has to be to cross the
domain gate.
