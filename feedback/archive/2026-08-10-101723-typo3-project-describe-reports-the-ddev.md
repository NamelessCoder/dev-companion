---
date: 2026-08-10T10:17:23+00:00
category: idea
status: closed
closed: 2026-08-10
model: claude-opus-5[1m]
tool: typo3_project_describe
directory: /home/benji/projects/typo3-cms
---

# typo3_project_describe reports the DDEV environment but not how to reach the running site

## Observation

Task: review and rework a core patch, then drive the developer's running installation in a browser to verify it.

typo3_project_describe told me the repository configures DDEV: {"via":"ddev","php":"8.5","source":".ddev/config.yaml","entered":false,"hooks":[],"providers":[]}. That is the right fact and it is where I learned the environment exists. What it does not carry is anything that lets you reach the site it describes: no project name, no primary url, no bound host ports, no container names, no docker network.

I had to get all of it elsewhere, from the host: ddev describe -j for the project name, primary_url and the 127.0.0.1 http/https ports; docker network ls and docker inspect for the ddev_default network and the ddev-router address. Four shell round trips before the first request could be made, and one wrong attempt in between (host.docker.internal against the bound port, refused, because the router binds 127.0.0.1 only).

The answer already declares itself read "from files only, no console and no database, so it answers on a fresh clone", which explains the omission — running ports genuinely are not in .ddev/config.yaml. But the project name is, and the hostname derived from it is predictable, and both would have been enough to start.

## Query

typo3_project_describe in a core checkout that has a running DDEV project

## Suggestion

Add what the files do carry: the DDEV project name and the hostname it implies (<name>.ddev.site plus additional_hostnames), and the container name prefix ddev-<name>-web, marked as what the configuration declares rather than what is running. Optionally a note that live ports and the router address come from ddev describe -j and docker inspect, so a caller that needs them knows where to go instead of guessing.
