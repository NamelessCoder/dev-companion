# META-02 — Nothing to ask

**Environment:** `E-NONE`, then `E-STOPPED` · **Contract:** `held`
— `R-DIS-006` … `R-DIS-009`, `R-ANS-001`, `R-ANS-005`
**Held by:**
`Typo3CliTest::aDdevProjectThatIsNotRunningIsReportedRatherThanStarted`,
`Typo3CliTest::aMissingConsoleNamesEveryPathThatWasProbed`,
`InstanceTest::anInstallationThatAppearsDuringTheSessionIsFound`,
`Typo3CliTest::aStoppedProjectNoInterpreterHereCanRunIsAskedAgainAfterItStarts`,
`LabelSearchTest::aConsoleThatCannotRunIsStillUnanswered`

> Which icons can I use for a "publish" action, and is there already a label for
> "Publish page"?

Run it twice: once in a directory with no TYPO3 anywhere above it, and once in a
site project whose DDEV is stopped. The prompt stays the same; only the
environment changes.

**What has to come out of it**

- Neither run answers "there are no such icons" or "there is no such label".
  Both say that the installation could not be asked, and why.
- The two reasons are distinguishable: nothing found versus found but not
  running.
- Where discovery failed, the answer names where it looked.
- The stopped project is reported with the command that would start it, and
  nothing is started as a side effect.
- The way out is named: the two environment variables that end the guessing.
- After the user starts DDEV and asks again in the same session, the answer
  changes — a negative is not remembered (`R-DIS-009`).

**How it fails**

- An empty result shaped like a complete one, and the agent concluding the
  extension registers nothing.
- DDEV started by the agent because a lookup wanted an answer.
- The session having to be restarted after the installation became reachable.
