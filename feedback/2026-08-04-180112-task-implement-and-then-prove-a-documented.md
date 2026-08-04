---
date: 2026-08-04T18:01:12+00:00
category: tool-gap
status: open
model: claude-opus-5
tool: typo3_configuration_lookup
directory: /home/benji/projects/site-new
---

# Task: implement and then prove a documented environment-variable contract in config/system/additi...

## Observation

Task: implement and then prove a documented environment-variable contract in config/system/additional.php — TYPO3_DB_*, TYPO3_ENCRYPTION_KEY, TYPO3_TRUSTED_HOSTS, TYPO3_MAIL_DSN, plus a DDEV-only block.

typo3_configuration_lookup is the obvious tool for "what is this configuration value really, after every extension has had its say", and I called it zero times. I used `vendor/bin/typo3 configuration:show` for six different paths instead.

The reason is a capability boundary rather than a defect: the tool reads the installation as it currently is. What I had to prove was conditional — the same path resolving to different values under different environments. Concretely:
- SYS/displayErrors is 1 under DDEV and must fall back to -1 with IS_DDEV_PROJECT unset, which is the whole point of having moved it out of the committed settings.php.
- SYS/trustedHostsPattern had to be shown resolving through four layers in order: settings.php, the DDEV block, .env, .env.local, and a real environment variable beating all of them.
- DB/Connections/Default had to be shown picking up TYPO3_DB_HOST and TYPO3_DB_PORT.

None of that is expressible as "read path X from the installation". Each run needed its own environment. So the tool was correct and simply could not answer the question I had, and I reached for the console.

Worth recording because from the outside this session looks like one that ignored typo3_configuration_lookup entirely. It did not; the question was the wrong shape.

## Query

Not called for this. I used the CLI instead: `typo3 configuration:show SYS/trustedHostsPattern`, `SYS/displayErrors`, `SYS/devIPmask`, `BE/debug`, `DB/Connections/Default`, each run under a different environment (with and without env vars set, and with IS_DDEV_PROJECT unset).

## Suggestion

Either accept optional environment overrides on typo3_configuration_lookup — an `env` map applied to the process that reads the value, which would make "prove this variable reaches this path" a single call — or say plainly in the description that it reads the installation as it stands and that conditional resolution belongs to the console. The second costs nothing and stops a session guessing. The first would be genuinely useful for auditing any additional.php contract, which is a recurring shape.
