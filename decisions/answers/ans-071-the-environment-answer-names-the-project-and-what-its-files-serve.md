---
id: D-ANS-071
date: 2026-08-10
status: open
---

# D-ANS-071 — The environment answer names the project and what its files serve

**`typo3_project_describe` carries the DDEV project name and the hostnames its
files declare, and says where the running half comes from.**

The answer reported that a DDEV environment exists and nothing that lets a
caller reach the site it describes.

## Evidence

- `feedback/2026-08-10-101723`. Four shell round trips for the project name, the
  primary URL and the router's address, and one wrong attempt in between —
  `host.docker.internal` against the bound port, refused. The session read the
  omission correctly: running ports are not in `.ddev/config.yaml`.
- The name is. `ddev config --project-name` is "normally the same as the last
  part of directory name" and `--project-tld` defaults to `ddev.site`, both out
  of DDEV's own help; the config file's comments state that an
  `additional_hostnames` entry is served under the same top-level domain and an
  `additional_fqdns` entry as written.
- Read against the DDEV project running on this machine, the answer now names
  `typo3-cms` and `typo3-cms.ddev.site`, which is the `primary_url` that project
  reports.

## Decided

- Two fields, `project` and `hostnames`, required on every path — null and empty
  where the environment is the one `TYPO3_DEV_COMPANION_CONSOLE` names, which
  declares neither.
- The top-level domain is the project's own or the default, and a global DDEV
  configuration that sets another one is not read. This answer is the project's
  files (`R-PRJ-001`), and a machine file would make it depend on where it runs.
  A project that sets its own `project_tld` states it in the same files and is
  answered from them.
- The answer says what it is not: bound ports and the router's address on the
  container network come from `ddev describe -j` and `docker inspect`. Reaching
  for them here would mean asking a running project, which is what `R-DIS-006`
  forbids — a stopped project has to read exactly like a running one.
- The sentence is its own line rather than part of "where the commands run". A
  repository that declares no commands has no such line at all, and what the
  environment serves is not a fact about commands.
- What to do with the hostname is not repeated here. Reaching the site from a
  container is
  [`D-KNW-069`](../knowledge/knw-069-a-browser-in-a-container-reaches-a-ddev-site-on-the-routers-own-network.md)
  and the document it decided.

## Assumed

- That a project whose config states no `name` is served under its directory
  name. It is what DDEV's own help says the flag defaults to, and no project
  without the key was created to watch it.

## Wrong if

- A caller acts on a hostname the running router does not answer on — a global
  `project_tld`, a project renamed since. The fields say they are what the
  configuration declares; a report of somebody being misled by them anyway means
  the marking is not carrying it.
