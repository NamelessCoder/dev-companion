---
date: 2026-07-29T10:51:10+00:00
category: missing-knowledge
status: open
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# Three concrete things bit me while building a TYPO3 v14.3 site that no lookup warns about. All th...

## Observation

Three concrete things bit me while building a TYPO3 v14.3 site that no lookup warns about. All three are core mechanics, not project trivia, and each cost a failed run.

1. Extension assets are published at COMPOSER time, not runtime.
typo3/cms-composer-installers symlinks each package's Resources/Public into public/_assets/<hash>/ during composer install. I added Resources/Public/Css/printworks.css to an already-installed sitepackage and the frontend died with a hard 500:
  #1758098512 CanNotResolvePublicResourceException — Resolved resource "PKG:bk2k/printworks-sitepackage:Resources/Public/Css/printworks.css" is not a public resource
The fix is re-running composer install, which is not something the exception text suggests. Anyone adding a first CSS file to a sitepackage hits this immediately.

2. DataHandler puts every new record at the TOP of its page.
A datamap written in reading order comes out reversed: my navigation rendered Kontakt, Ueber uns, Materialien, Leistungen. Same for content elements within a colPos. The workaround of chaining negative pids ('-NEW_previous') is supported — DataHandler resolves NEW placeholders in pid including the negative form — but it breaks the moment one element of the chain is an existing uid rather than a NEW record. The 'DataHandler and Persistence' hints say nothing about ordering.

3. impexp:import resolves relative paths against the PUBLIC path, not the project root.
  vendor/bin/typo3 impexp:import extensions/my_ext/Initialisation/data.xml 0
  -> File not found: /var/www/html/public/extensions/my_ext/Initialisation/data.xml
The EXT: notation works and is what should be documented:
  vendor/bin/typo3 impexp:import EXT:my_ext/Initialisation/data.xml 0
The error message names the resolved path, which is the only reason this was quick to diagnose, but the command's own help says nothing about the base.

Separately, and offered only as context rather than as a request: the environment traps in this kind of work are as costly as the TYPO3 ones. DDEV generates a config/system/.gitignore that excludes additional.php, so environment overrides silently stay out of the repository unless you notice the nested .gitignore and take ownership of it. I mention it because it sits exactly where a 'site work' mode of this server would have to reach.
