---
id: R-DIS-003
status: held
---

# R-DIS-003 — The console is looked for where the installation declares it

**The console is looked for at the `config.bin-dir` the installation declares,
before the Composer defaults, an absolute declaration below the root included.**

Absolute is a spelling of the same directory, and the invocation needs it
relative to the root. One outside the root has no relative form, so it is named
in the reason together with `TYPO3_MCP_CONSOLE` rather than passed over in
silence.

## From

`.build/bin/typo3` existing, working, and never being probed (2026-07-29); the
same directory declared absolutely being dropped again (2026-08-01).

## Held by

- `Typo3CliTest::aConsoleInTheDeclaredBinDirectoryIsFound`
- `Typo3CliTest::anAbsoluteBinDirectoryBelowTheRootIsTheSameDirectory`
- `Typo3CliTest::aBinDirectoryOutsideTheRootIsNamedRatherThanPassedOver`
