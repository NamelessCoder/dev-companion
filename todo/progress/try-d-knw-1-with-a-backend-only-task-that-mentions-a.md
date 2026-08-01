# Try `D-KNW-1` with a backend-only task that mentions a content element

**Serves:** decisions/
**Branch:** todo/try-d-knw-1-with-a-backend-only-task-that-mentions-a
**Claimed:** 2026-08-01

Sitepackage work is answered from the General category, and the named failure is
a backend-only task coming back with the sitepackage layout because it mentioned
a content element. That is one call: ask `typo3_task_guide` with exactly that
task text and read what comes back. What would hold it is a contract case in
`scenarios/contracts/` for that task shape, plus the assertion in the guide
tests; the other half — General growing until every answer is made of it — is a
size the category can be measured for in the same pass.
