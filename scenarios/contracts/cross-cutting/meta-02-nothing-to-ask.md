# META-02 — Nothing to ask

**Environment:** `E-NONE`, then `E-STOPPED` · **Contract:** `held`
— `R-DIS-006` … `R-DIS-009`, `R-ANS-001`, `R-ANS-005`
**Held by:**
`Typo3CliTest::aDdevProjectThatIsNotRunningIsReportedRatherThanStarted`,
`Typo3CliTest::aMissingConsoleNamesEveryPathThatWasProbed`,
`InstanceTest::anInstallationThatAppearsDuringTheSessionIsFound`,
`Typo3CliTest::aStoppedProjectThisMachineCanRunIsAskedAgainAfterItStarts`,
`Typo3CliTest::aStoppedProjectIsAskedAgainAfterItStarts`,
`LabelSearchTest::aConsoleThatCannotRunIsStillUnanswered`. Not guarded: that a
stopped project returns the running project's own icons and labels is a property
of an environment, and no test has one — it was measured instead, in
`.environments/e-site-14.3` on 2026-08-04, and the reading is on `D-ANS-005`.

> Which icons can I use for a "publish" action, and is there already a label for
> "Publish page"?

Run it twice: once in a directory with no TYPO3 anywhere above it, and once in a
site project whose DDEV is stopped. The prompt stays the same; only the
environment changes.

The two halves are not two grades of the same failure, and the criteria below
say which is which. With nothing above the directory there is no installation to
ask, and the answer has to say so. With the project stopped there is one, and on
every covered line this machine can reach its console — so the prompt is
answered, and the state lives in `typo3_server_scope` rather than in the answer.

**What has to come out of it**

- With nothing above the directory, neither lookup answers "there are no such
  icons" or "there is no such label". Both say the installation could not be
  asked, and why.
- With the project stopped, the prompt is answered rather than refused: the
  console reached through an interpreter of this machine returns the icons and
  labels the running project returns, and says it read them from the
  installation. Two states that a lookup cannot tell apart may not be reported
  as though it could.
- Where discovery failed, the answer names where it looked.
- Nothing is started as a side effect, in either environment. The stopped
  project is reported with the command that would start it, by
  `typo3_server_scope` — which is where that state lives, and the only answer
  that carries it.
- The way out is named where there is nothing to ask: the two environment
  variables that end the guessing.
- After the user starts DDEV and asks `typo3_server_scope` again in the same
  session, it answers differently — the console is reached through DDEV on the
  runtime the project declares, and the caveat is gone. A negative is not
  remembered (`R-DIS-009`).

**How it fails**

- An empty result shaped like a complete one, and the agent concluding the
  extension registers nothing.
- DDEV started by the agent because a lookup wanted an answer.
- The session having to be restarted after the installation became reachable.
