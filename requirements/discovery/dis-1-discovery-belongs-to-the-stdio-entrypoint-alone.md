---
id: R-DIS-1
status: held
---

# R-DIS-1 — Discovery belongs to the stdio entrypoint alone

**The installation is never derived from `getcwd()` on its own; only the stdio
entrypoint enables discovery, because an HTTP endpoint has no such relationship
to its callers.**

That is one call, in `Server\Entrypoint`, which `bin/typo3-cms-mcp` runs and
nothing else does. It used to stand in the binary itself; moving it into the
class the binary delegates to changed where the line is, not how many there are.

**Held by:**
`InstanceTest::withoutAnEntrypointHandingInADirectoryThereIsNoInstance`
