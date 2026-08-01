# Widen the `D-SCO-6` guard to the surfaces it does not read

**Serves:** decisions/

The claim that project work is out of scope kept coming back, and the guard
catches it in one place while the entry names three it does not read: a tool
description, the readme, a hint. Extend that guard across all three — the same
assertion, more surfaces — and the "Wrong if" is answered by a test instead of
by whoever notices the sentence next. If the claim then reappears in a surface
no test can reach, the flag rename the entry proposes (`outsideCore` →
`coreRepositoryOnly`) is the next step, and it is a todo of its own.
