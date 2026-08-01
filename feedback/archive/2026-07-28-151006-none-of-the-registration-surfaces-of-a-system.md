---
date: 2026-07-28T15:10:06+00:00
category: missing-knowledge
status: closed
closed: 2026-07-28
commit: da72dff
subject: "Cover the registration surfaces of a system extension"
tool: typo3_architecture_hint
---

# None of the registration surfaces of a system extension is covered. I passed five concrete regist...

## Observation

None of the registration surfaces of a system extension is covered. I passed five concrete registration paths — backend module definitions, the TCA schema API, a console command, ext_tables.sql and JavaScriptModules.php — and got back Backend UI and Web Components, System Extension Boundaries, DataHandler, TCA/FormEngine and "CSS Accessibility, Contrast, and States". Not one hint addressed any of the five, and one of ten slots again went to CSS for a request containing no stylesheet. These are declarative files whose exact shape cannot be guessed and where being wrong fails silently or at boot: Configuration/Backend/Modules.php returns an array keyed by module identifier with parent, access, path, iconIdentifier, labels (a translation domain, e.g. "filelist.module"), aliases and a routes map pointing at Controller::method; console commands are registered with the Symfony #[AsCommand('cache:flushtags', '...')] attribute on the class rather than through Services.yaml tags; Configuration/JavaScriptModules.php declares the import map and dependencies for backend JavaScript; ext_tables.sql carries only the delta the schema analyzer needs; and Classes/Schema (223 file touches in the last 18 months) is the newer TCA schema API that a patch touching TCA is increasingly expected to go through. Backend controllers are the top non-test PHP bucket in core history (1181 touches under Classes/Controller), and a new backend module means touching Modules.php — so this is a frequently needed answer, not an exotic one.

## Query

paths=["typo3/sysext/backend/Configuration/Backend/Modules.php","typo3/sysext/core/Classes/Schema/TcaSchemaFactory.php","typo3/sysext/core/Classes/Command/DumpAutoloadCommand.php","typo3/sysext/backend/ext_tables.sql","typo3/sysext/backend/Configuration/JavaScriptModules.php"], limit=10

## Suggestion

Add an architecture section on extension registration surfaces, matched on Configuration/Backend/**, Configuration/JavaScriptModules.php, Configuration/Icons.php, ext_tables.sql, ext_localconf.php and Classes/Command/**, documenting the array shape of Modules.php with its keys, the #[AsCommand] attribute for console commands, the JavaScriptModules.php import map, and what does and does not belong in ext_tables.sql. Add a separate section for Classes/Schema and the TCA schema API. A useful cross-check for the catalog build: the labels key in Modules.php is a translation domain, so it depends on the domain-derivation knowledge reported missing earlier.
