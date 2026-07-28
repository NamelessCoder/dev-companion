---
date: 2026-07-28T15:11:46+00:00
category: missing-knowledge
status: open
tool: typo3_architecture_lookuptypo3_task_guide
---

# Representative high-impact core areas are not covered by architecture hints. Cache, FAL/resource ...

## Observation

Representative high-impact core areas are not covered by architecture hints. Cache, FAL/resource storage, authentication/authorization and permissions all return only the generic System Extension Boundaries hint. A Symfony console command lookup additionally receives HTTP Routing/Middleware advice merely because its class name contains RequestHandler, even though PSR-7/PSR-15 guidance is irrelevant to console commands. The PHP architecture catalog currently has only six sections: extension boundaries, DI, events, TCA/FormEngine, DataHandler/persistence and HTTP routing.

## Query

Architecture lookups for CacheManager.php, ResourceFactory.php, CommandRequestHandler.php and BackendUserAuthentication.php

## Suggestion

Add focused architecture topics for caching, FAL/resource/storage, authentication/authorization/security, Symfony Console/CLI, mail, configuration/site sets and Extbase where appropriate. Tighten routing matching so generic Route/Controller/RequestHandler substrings do not classify Console paths as HTTP request handling; use path-qualified patterns such as /Http/, /Middleware/ and /Routing/.
