---
date: 2026-07-29T10:51:30+00:00
category: tool-gap
status: open
directory: /home/benji/projects/site-new
---

# Overall verdict from using this server as the only reference while building a deployable TYPO3 v1...

## Observation

Overall verdict from using this server as the only reference while building a deployable TYPO3 v14.3 business site from an empty directory.

What worked, and worked well:
- typo3_server_scope is honest and precise. It said up front that project work is out of scope, and every later surprise was consistent with it. That is rarer than it sounds.
- The Site Sets hints are the best content in the catalog for this kind of work. They transferred to a project sitepackage essentially unchanged and the 'settings.definitions.yaml, not constants.typoscript' steer was exactly right.
- typo3_fluid_namespace_list and typo3_configuration_lookup answered correctly from the installation I had just created, including the project extension. The installation-backed half is genuinely useful and worked with zero setup once a TYPO3 installation existed in the working directory.

Where the shape of the server does not fit the shape of the work:
The catalog is organised around 'what must a patch satisfy to be merged'. Site building asks 'how do I do X with this API'. Those retrieve differently. Asking about DataHandler for creating pages from a CLI command returned the review criteria (needs functional tests, preserve workspace and localization behaviour, permission checks are security-sensitive) — all true, none of it what the question was about. There is no 'usage' axis, only a 'review' axis.

The suggestion is not to grow a project-development knowledge base; the scope statement is a deliberate and defensible boundary. It is that several topics already in the catalog are core mechanics that project work depends on, and they are currently written only from the review angle. Frontend Fluid rendering, content area identifiers, DataHandler ordering, the composer asset publishing step and the Initialisation/ import mechanism are all TYPO3 core behaviour. Documenting them from the usage angle would stay inside the stated scope and would have changed most of the decisions in this build.

One process note: the server's tools were not registered in the Claude Code session despite a valid .mcp.json in the working directory, so every call in this evaluation went through a hand-written stdio JSON-RPC wrapper. That is very likely a client-side approval issue rather than a server defect, and it is recorded only so the evaluation is reproducible.
