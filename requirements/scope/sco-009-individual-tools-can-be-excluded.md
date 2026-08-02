---
id: R-SCO-009
status: held
restsOn: [D-AUD-004]
---

# R-SCO-009 — Individual tools can be excluded

**A caller can exclude individual tools with `TYPO3_MCP_EXCLUDE_TOOLS`.**

The scope answer names the resulting omissions, so a shorter tool list carries
its reason.

## From

The two fixed profiles forcing a caller that wants all but one tool to pay for
all of them (2026-07-30). The profiles were removed on 2026-08-02 under
[`D-AUD-004`](../../decisions/audience/aud-004-every-client-is-offered-every-tool-and-the-answer-obliges.md)
and this is what is left of them: the subtraction the caller declares.

## Held by

- `ExcludedToolsTest::onlyTheCallerShortensTheList` and
- `ExcludedToolsTest::theScopeNamesWhatTheCallerExcluded`
