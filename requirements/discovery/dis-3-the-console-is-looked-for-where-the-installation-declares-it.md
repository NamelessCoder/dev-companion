---
id: R-DIS-3
status: held
---

# R-DIS-3 — The console is looked for where the installation declares it

**The console is looked for at the `config.bin-dir` the installation declares,
before the Composer defaults.**

**From:** `.build/bin/typo3` existing, working, and never being probed
(2026-07-29).

**Held by:** `Typo3CliTest::aConsoleInTheDeclaredBinDirectoryIsFound`
