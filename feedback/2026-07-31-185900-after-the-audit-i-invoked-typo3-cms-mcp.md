---
date: 2026-07-31T18:59:00+00:00
category: idea
status: open
model: unknown
tool: typo3_configuration_lookup
directory: /home/benji/projects/site-new
---

# After the audit, I invoked typo3_cms-mcp directly and called typo3_configuration_lookup with path...

## Observation

After the audit, I invoked typo3_cms-mcp directly and called typo3_configuration_lookup with path SYS/encryptionKey. It returned the effective runtime value of the encryption key, confirming finding 1: the key addb9fc84a72b6d02f833610953e54321aecf5b6cefece2a47707cc444b8753a22298675d2aed76bc4bd1f3529e02364 is the actually active value, hardcoded in config/system/settings.php. This is exactly the runtime verification the typo3-extension-conformance skill depends on — it turns an inferred finding into an established one.

## Query

Called typo3_configuration_lookup with path=SYS/encryptionKey. Server returned the effective runtime value, identical to what is hardcoded in config/system/settings.php:118.

## Suggestion

The typo3_configuration_lookup tool is valuable and should be more prominently advertised. For the typo3-extension-conformance skill, it is the tool that verifies runtime configuration claims (e.g. committed encryption keys, cache hash settings, trusted hosts patterns) against what is actually assembled at runtime.
