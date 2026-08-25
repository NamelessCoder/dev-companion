---
id: D-KNW-119
title: 'The corpus tells apart the failures one usage synopsis presents alike'
date: 2026-08-25
status: confirmed
coveredBy: []
---

# D-KNW-119 — The corpus tells apart the failures one usage synopsis presents alike

**A `typo3` command prints its usage synopsis and exits 255 for any uncaught
exception, so the corpus says what that surface hides.**

[`D-KNW-094`](knw-094-how-a-variable-reaches-a-console-command-is-a-subject-this-server-owns.md)
owns how a variable reaches the command inside the container, and its reading
measured the forms its own feedback named. The session judged here met a form
nobody had asked about, and it reported three failures rather than one because
all three came back looking like an argument error.

## Evidence

- The miss reproduces on the server as it is now.
  `bin/cli hints:probe "ddev exec multi-line command backslash continuation"`
  returns `installation-setup` and `project-configuration-files`, and the run of
  `ddev exec` statements in `installation-setup` names the join, `--raw`, a
  parenthesis, a pipe and a value with a space. None of them names a command
  written across several lines, and neither `synopsis` nor `secure enough`
  appears anywhere below `knowledge/` or `skills/`.
- The password half holds and is read from the checkout.
  `SetupCommand::getAdminUserPassword()` in `.checkouts/14.3` throws a
  `\RuntimeException` carrying `Administrator password not secure enough!` and
  the code `1669747614` wherever
  `SetupDatabaseService::getBackendUserPasswordValidationErrors()` returns
  anything.
- The surface belongs to Symfony rather than to TYPO3.
  `Application::renderThrowable()` writes the running command's synopsis for
  every throwable, and `Application::run()` replaces an exception code above 255
  with 255. That is symfony/console v7.4, the line `.checkouts/14.3` pins at
  v7.4.13 and this repository has installed at v7.4.15. A TYPO3 exception code
  is nine digits, so every uncaught one exits 255 and prints the synopsis.
- The empty flow mapping holds and is read from the checkout.
  `SiteWriter::createNewBasicSite()` writes `errorHandling`, `routes` and
  `dependencies` as empty arrays, and `SiteWriter::write()` dumps without the
  `Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE` its sibling `writeSettings()` passes.
  `Yaml::dump(['dependencies' => []], 99, 2)` on the symfony/yaml installed here
  answers `dependencies: {  }`, with the two spaces the feedback quoted.
  `SetupService` fills `dependencies` only where `typo3/fluid-styled-content` is
  active, which an extension repository's installation commonly is not.
- What this run cannot read is the multi-line invocation. It started no
  container, and this checkout carries no `.environments/`.

## Decided

- Step 1a, on the subject `D-KNW-094` owns, and one card for all three reports.
  They arrived as one output, which is why the session could place none of them,
  and a caller who meets one of the statements is meeting the other two in the
  same breath.
- Queued rather than closed on the spot. The multi-line form is a measurement
  nothing here can stand in for, and the two halves that were read were read on
  one checkout while `installation-setup` binds its statements by TYPO3 major.
- The measurement is an `E-SITE` made here rather than the reporting session's
  project, which is `D-EVI-004`.
- The feedback's account is the observation and not the statement. That the
  command failed with the synopsis is what the session saw; what `ddev exec`
  does with a newline is what the reading has to establish.
- `normal` rather than `low`. The output names the arguments and is caused by
  none of them, so the caller's next move is rewriting a command that was
  already right.
- `coveredBy: []`. What would show this wrong is what a statement says and what
  a later session does with it, and no test in this repository reads either.

## Assumed

- That the multi-line command lost its variable assignments rather than failing
  in the caller's own shell. The report establishes the output and not the
  cause.
- That DDEV v1.25.1 is what the caller runs. `D-KNW-094` carries the same
  assumption and what it costs.
- That the password and the flow-mapping halves hold on 12.4 and 13.4 too. Only
  `.checkouts/14.3` was read here.

## Wrong if

- The measurement finds that a multi-line command survives the join. Then what
  failed was the quoting on the caller's side, and the statement belongs
  wherever this server tells a caller how to write the line rather than beside
  `ddev exec`.
- A caller still reads the synopsis as an argument error once the statements
  land. Then the gap was the wording of what is already there, and the fix is a
  rewrite rather than three statements.
- The two read halves turn out to differ across the covered majors far enough
  that one entry cannot hold them. Then they are separate statements and this
  entry's one card was the wrong shape.

## Confirmed on 2026-08-25

The statement was measured rather than read. On an `E-SITE` of 14.3.6, made here
on mariadb, `--admin-user-password=password` answered with the four-line policy
error, the `setup` synopsis and exit status 255.

The multi-line half came out the other way round. A backslash continuation
inside the quoted string survives the join and the command runs: DDEV wraps the
whole argument in double quotes and escapes the ones inside it, the newline
reaches the container's bash as a newline, and a backslash immediately before it
is a line continuation there. Five lines of assignments, one of them a value
with a space, all reached the command. What breaks is a line that ends without a
backslash, and every assignment above that break is then spent on a command of
its own. A backslash with a space after it is the same break, which bash reports
as a space that is not a command.

So the first **Wrong if** fired and its consequence did not follow. The loss
happens in the container's bash reading the joined line, which is the subject
the `ddev exec` statements already own, so the new statement went beside them.
What the reporting session met was a broken continuation rather than a form
`ddev exec` refuses, and the statement says which of the two a caller is looking
at.

The password half gained what only a run could say. `selectAndImportDatabase()`
stands above `getAdminUserPassword()` in `execute()` on 12.4, 13.4 and 14.3
alike, so the schema is written before the password is judged. Measured against
an empty database: nought tables before the rejected run, forty-two after it.
The next attempt is then refused by the table check that `--force` does not
clear, which makes the insecure password a one-way failure on a server database.

Both read halves hold on the other two majors.
`getBackendUserPasswordValidationErrors()` is identical in the three checkouts
and the `default` policy asks each of them for the same five things. `write()`
dumps without `Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE` in all three, and
symfony/yaml renders `{  }` at v6.4.26 as at v7.4.15. `symfony/console` prints
the running command's synopsis and replaces an exit code above 255 at v6.4.27 —
which is what 12.4 pins — as at v7.4.15.

`dependencies` is the half that moved and it is bound `since: 14`. The
installation made here carries `typo3/fluid-styled-content`, which is the one
package `createSite()` reads, so the key came out as a list of two sets. The
feedback's empty mapping is the case where that package is absent, and
`errorHandling` and `routes` are the empty mappings on every covered major.

What the run also turned up is outside this entry and unsettled.
`bin/cli environment:create E-SITE 14.3 mariadb` stopped at its own `setup` step
on forty-two tables, in a directory and under a project name that had never
existed here. Which of the steps before it installed TYPO3 was not isolated, and
a database volume left behind by a name somebody unlisted rather than deleted
would produce the same thing. That is `E-SITE`'s build rather than the corpus.
