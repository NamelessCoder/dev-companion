---
date: 2026-07-29T09:45:27+00:00
category: bug
status: closed
closed: 2026-07-29
commit: be89c67
subject: "Stop remembering that there was nothing to read"
tool: typo3_icon_lookuptypo3_label_lookuptypo3_fluid_namespace_listtypo3_configuration_lookuptypo3_backend_module_lookup
---

# Instance::$resolved memoizes the discovery result for the lifetime of the stdio process, and the ...

## Observation

Instance::$resolved memoizes the discovery result for the lifetime of the stdio process, and the stdio process lives as long as the agent session. If no installation existed when the first installation-answered tool was called, every later call in that session returns "no installation" even after one exists. I hit this for real and it cost me most of the session. The server started with an empty project — composer install had not run — and the first typo3_icon_lookup cached null. I then started DDEV and installed dependencies until bin/typo3 answered "TYPO3 CMS 13.4.33 (Application Context: Development)", and typo3_icon_lookup, typo3_label_lookup, typo3_fluid_namespace_list and typo3_configuration_lookup all still reported no installation. I concluded the discovery was broken. It is not: driving the same binary as a fresh process from the same directory answered correctly and well — "9 icon identifier(s) in /home/benji/projects/site-events match \"delete\"" with actions-delete scored top on both a name part and a concept match. So the capability works and the cache hides it, with no way to invalidate short of restarting the MCP client, which a caller has no reason to suspect. Note also that discovery uses composer/installed.json rather than a binary path, which is the robust choice — this project sets config.bin-dir to bin, so its console is bin/typo3 and a vendor/bin/typo3 probe would have missed a perfectly good installation. Worth keeping as is.

## Query

typo3_icon_lookup{query:"delete"} in a session that began before composer install had populated the project

## Suggestion

Do not cache the negative result. Caching a successful resolution for the process lifetime is fine and worth keeping; when locate() returns null, re-attempt on the next call, or at least re-attempt when the marker it keys on — composer/installed.json below the declared vendor directory — has appeared or changed mtime since the failed attempt. A bare stat per call is cheap next to running a console command. It would also help if typo3_server_scope reported the resolution as observed now rather than as cached, and named the directory the search started from, so a caller who suspects the environment changed has one call that tells them the truth.
