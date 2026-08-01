---
date: 2026-07-29T10:03:14+00:00
category: bug
status: closed
closed: 2026-07-29
commit: e26c21f
subject: "Say when a console is reachable but not ready"
tool: typo3_server_scope
---

# The scope reports the console as reachable via host PHP 8.3 and presents installation-backed tool...

## Observation

The scope reports the console as reachable via host PHP 8.3 and presents installation-backed tools as usable, although DDEV is stopped and the configured database host db is unresolved on the host. configuration:show and --version happen to work, but DB-dependent maintenance commands such as upgrade:list fail with php_network_getaddresses for db. This makes reachable=true too broad and potentially misleading for maintenance readiness.

## Query

Discover this Composer TYPO3 installation while its DDEV project is stopped

## Suggestion

Distinguish bootstrap-only console availability from database-backed runtime availability. When a .ddev project exists and is stopped, report DDEV stopped and expose per-capability readiness; probe a harmless DB-dependent command or state that only DB-independent console tools are currently reachable.
