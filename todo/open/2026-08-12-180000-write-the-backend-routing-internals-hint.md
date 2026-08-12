# Write the backend routing internals hint

**Serves:** feedback/2026-08-11-055220-no-hint-covers-backend-routing-internals-how-a.md
**Priority:** normal

Judged as `D-KNW-070`: step 1a, the knowledge is missing, and it is built as a
hint of its own beside `backend-modules` rather than inside it. The entry
carries the boundary and what the probe returned; this is the reading it left
open.

Establish each of the four against `.checkouts/` on all four covered lines, and
bind what does not hold on all of them:

- a module route carries the module in its `module` option and a route declared
  in `Configuration/Backend/Routes.php` does not, which is what `ModuleResolver`
  reads first;
- the sub-route identifier is `<module>.<name>` for a declarative module and
  `<module>.<ControllerAlias>_<action>` for an Extbase one, generated per
  action;
- `navigationComponent` is inherited from the parent module unless overridden,
  so a module's own registration does not say whether it is page-tree navigated;
- `TYPO3\CMS\Backend\Routing\Route` and `TYPO3\CMS\Core\Routing\Route` take
  different constructor arguments, so an identifier passed where `$defaults`
  belongs makes `getOption('_identifier')` return null with no error.

The last one goes to `core-tests` as well. It is what let two functional tests
pass while never reaching the branch they are named after, and a test file is
where the silence costs most.

`appliesTo` decides whether it arrives: the call that missed was path-scoped on
`backend/Classes/Breadcrumb/`, so the paths are the routing half of this card
and `bin/cli hints:probe` with the feedback's own task is what says it works.
