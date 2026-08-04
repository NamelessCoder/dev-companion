# Report the PHP floor the required core declares, beside the project's own

**Serves:** feedback/2026-08-04-055638-task-add-a-code-style-fixer-to-a-typo3-14-3.md
**Priority:** normal

Step 1b, and the reading is done —
[`D-KNW-055`](../../decisions/knowledge/knw-055-the-first-check-a-standalone-extension-repository-gets-is-a-gap-this-server-owns.md)
carries it: `^8.1` on 12.4, `^8.2` on 13.4 and 14.3, `^8.5` on `main`, in the
mono repo and in the installed `typo3/cms-core` alike, so the floor a package
may declare is not derivable from the major and the environment's interpreter is
the wrong number to take it from. Add the field to `Project::describe()` beside
`phpConstraint` and `coreConstraint`: `require.php` out of the core package's
own `composer.json`, at the directory `Instance::packages()['core']` already
resolves to read `Classes/Information/Typo3Version.php` from, so it is one
further read on a path the answer has. Declare it in
`ProjectScope::outputSchema()` as what the installed core requires rather than
what the project declares or what the environment runs, put it in the opening
line beside the declared constraint where the first call of a workflow is read,
and hold it in `ProjectTest` for the two shapes that matter: a project declaring
no `php` constraint at all, and one declaring a floor above the core's.
