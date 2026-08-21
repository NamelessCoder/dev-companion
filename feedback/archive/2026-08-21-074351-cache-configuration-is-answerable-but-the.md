---
date: 2026-08-21T07:43:51+00:00
category: idea
status: closed
closed: 2026-08-21
model: claude-opus-5[1m]
tool: typo3_configuration_lookup
---

# cache configuration is answerable but the CacheManager that registered it is not

## Observation

typo3_configuration_lookup reads SYS/caching/cacheConfigurations effective, which is the configured array after every extension has had its say. I assumed while comparing that this covered cache inspection, and it does not.

What is missing is the CacheManager. Per cache: whether it is registered as well as configured, whether it has been initialized, and which frontend class it actually got rather than the one the configuration names. The identifier set is a union — a cache registered by code and absent from the array is invisible to the configuration path, and one configured that never registers looks fine there.

The gap between configured and registered is the diagnostic, and it is the same argument typo3_backend_module_lookup already rests on: the resolved tree says what the registration file cannot. A configuration read is the registration file of caching.

## Query

typo3_configuration_lookup with path=SYS/caching/cacheConfigurations — answers the configured array, which is the whole of what is available.

## Suggestion

Where the caching configuration is asked for, answer from the CacheManager rather than from the array alone: per identifier, configured and registered and initialized, the frontend class in effect, the groups, and the backend options. Reading a cache entry is not wanted and flushing is outside what this server does — this is the registry read, not the contents and not the operation.
