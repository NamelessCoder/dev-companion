---
date: 2026-07-29T09:34:33+00:00
category: bug
status: open
tool: typo3_icon_lookup
---

# Typo3Cli::consoleBinary() probes only vendor/bin/typo3 and bin/typo3. This repository sets config...

## Observation

Typo3Cli::consoleBinary() probes only vendor/bin/typo3 and bin/typo3. This repository sets config.bin-dir=.build/bin and config.vendor-dir=.build/vendor with typo3/cms.web-dir=.build/public — the layout the TYPO3 extension testing setup produces, used by a large share of public TYPO3 extensions. The console exists and works (`ddev exec .build/bin/typo3 --version` → TYPO3 CMS 14.3.0, PHP 8.5.3), DDEV was running, but discovery finds nothing, so icon, label, backend module, Fluid namespace and configuration lookup are all dead in this repository. The viaDdev path is implemented and would have worked; it never gets a binary to run. Host PHP is 8.3 here while the project requires >= 8.4, so viaPhp cannot substitute either.</observation>
<parameter name="suggestion">Read config.bin-dir from the root composer.json in consoleBinary() and probe <bin-dir>/typo3 before falling back to vendor/bin/typo3 and bin/typo3. Composer's default is vendor/bin, so honouring the declared value is strictly a superset of today's behaviour and costs one json_decode that requiredPhpVersion() already performs on the same file.</parameter>
</invoke>

## Query

typo3_icon_lookup {"query":"bootstrappackage"} in /home/benji/projects/bootstrap_package with a running DDEV project
