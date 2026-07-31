---
id: R-DIS-1
status: held
---

# R-DIS-1 — Discovery belongs to the stdio entrypoint alone

**The installation is never derived from `getcwd()` on its own; only
`bin/typo3-cms-mcp` enables discovery, because an HTTP endpoint has no such
relationship to its callers.**

**Held by:**
`InstanceTest::withoutAnEntrypointHandingInADirectoryThereIsNoInstance`
