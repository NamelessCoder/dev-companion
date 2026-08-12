# Answer the navigation component and the routes per module

**Serves:** feedback/2026-08-11-055242-never-called-typo3-backend-module-lookup-while.md
**Priority:** normal

Judged as `D-ANS-077`: step 1b, the shape is missing. The tool hands back what
`debug:backend:modules` exports, which carries neither the resolved
`navigationComponent` nor any route beyond the module's own path, and that
command does not exist below v14 at all. The entry carries the boundary; this is
the reading it left open.

Settle which source carries which field, against `.checkouts/` on all four
covered lines:

- what the container answers for a module. `ModuleInterface` exposes
  `getNavigationComponent()`, `getDefaultRouteOptions()`, `getAccess()` and
  `getAliases()`, and `src/Installation/probe.php` is where a topic for it goes
  — but `DebugBackendModulesCommand` builds its own `ModuleRegistry` from
  `backend.modules` and the `ModuleFactory` rather than autowiring one, so the
  probe has to do the same and that is what has to be tried first;
- whether the console is still needed beside it. The core says `packageName` and
  `labels` cannot be had from the registry API
  (`DebugBackendModulesCommand.php:100`), and the labels the CSV carries are
  translated through `LanguageService`;
- where the routes come from. `debug:backend:routes --json` lists every route
  with its options and its type, and is v14 and up like the other command, so
  the container is the only source that answers on 12.4 and 13.4.

Two fields at least: the resolved navigation component, and the routes with
their identifiers. `<module>.<name>` and `<module>.<ControllerAlias>_<action>`
are the two sub-route shapes `backend-routing-internals` already states, so the
answer and the hint have to agree.

The version hole is the same work. Today the tool answers
`unsupported: installation-not-answering` with the console's "command is not
defined" on 12.4 and 13.4, while its description promises unconditionally —
`D-ANS-077` says the answer names the lines it holds on instead.

`bin/cli environment:create E-SITE <version>` is what this is verified in, and
the fixture at `src/Upkeep/Fixture.php:555` answers `debug:backend:modules`
today, so it moves with whatever source the reading picks.
