---
date: 2026-07-29T09:41:47+00:00
category: bug
status: open
tool: typo3_icon_lookuptypo3_label_lookuptypo3_fluid_namespace_listtypo3_configuration_lookuptypo3_backend_module_lookup
---

# When no TYPO3 installation is reachable, the installation-answered tools return a normal-looking ...

## Observation

When no TYPO3 installation is reachable, the installation-answered tools return a normal-looking empty result instead of saying so. typo3_icon_lookup{query:"delete"} — an identifier present in literally every installation — returned {"matchCount":0,"exactMatch":false,"icons":[],"answeredBy":"nothing"}. typo3_fluid_namespace_list returned {"namespaces":[],"matchCount":0}. typo3_configuration_lookup is the worst case: it returned {"found":false,"value":null}, which positively asserts that the path does not exist when in truth nothing was consulted. The only signal is the enum value answeredBy:"nothing", which is easy to overlook next to a populated-looking result shape, and "nothing" reads like "no source had it" rather than "no source was available". typo3_server_scope promises the opposite behaviour: "Unavailable when no installation is reachable, and then said so rather than answered empty." A consuming agent that acts on these answers will conclude that an icon identifier is unregistered and invent a new one, or that a label does not exist and invent a new key — the exact failure the tools exist to prevent. This state is not exotic for a site project: a fresh checkout before composer install, a stopped DDEV container, CI, or an install that aborts on missing credentials for a private package all produce it. In this project composer install aborted on a 404 for a private repository, leaving a partial vendor/ and no console at all.

## Query

typo3_icon_lookup{query:"delete"} and typo3_configuration_lookup{path:"SYS/formEngine/formDataGroup"} in a project without an installed vendor/ directory

## Suggestion

Make the unavailable case structurally distinct from the empty case rather than a field on an otherwise identical payload. Drop matchCount/found/icons entirely when no installation was reached and return an explicit error-shaped object, for example {"error":"no-installation","message":"No TYPO3 installation was reachable; this answer is not evidence that the identifier does not exist.","looked":["vendor/bin/typo3","typo3/sysext/*/Configuration/Icons.php"],"remedy":"Run composer install and start the environment, then retry."}. Never emit found:false from typo3_configuration_lookup unless a console actually answered. Additionally report installation reachability as a first-class field in typo3_server_scope so an agent can check it once up front instead of inferring it from a miss.
