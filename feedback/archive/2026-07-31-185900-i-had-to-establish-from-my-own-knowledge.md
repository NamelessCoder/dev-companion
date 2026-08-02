---
date: 2026-07-31T18:59:00+00:00
category: missing-knowledge
status: closed
closed: 2026-08-02
model: unknown
tool: typo3_documentation_lookup, typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# I had to establish from my own knowledge whether TYPO3 core reads environment variables like TYPO...

## Observation

I had to establish from my own knowledge whether TYPO3 core reads environment variables like TYPO3_ENCRYPTION_KEY or TYPO3_DB_HOST automatically, since the project readme documents these but no code reads them. I could not verify this against the knowledge server because typo3_documentation_lookup was broken (see separate bug note), and typo3_architecture_lookup returned No architecture hint matched for TYPO3 encryption key configuration environment variable. The answer I relied on — that TYPO3 core does NOT read TYPO3_ENCRYPTION_KEY from the environment and that a project must wire it up in additional.php — was correct, but it was inferred, not server-verified.

## Query

Searched for whether TYPO3 core reads TYPO3_ENCRYPTION_KEY from environment variables. typo3_documentation_lookup was unusable (missing page parameter). typo3_architecture_lookup with id=configuration-reach returned general config ownership hints but nothing about env-var reading.

## Suggestion

Add a knowledge-base entry or architecture hint about environment variable handling in TYPO3 configuration: which env vars core reads automatically (TYPO3_CONTEXT, TYPO3_SITE_BASE in site config), which require project code to wire them up, and the recommended patterns (additional.php, %env() syntax in site config). This is a common deployment question.
