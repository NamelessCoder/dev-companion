---
date: 2026-08-18T07:06:11+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_documentation_lookup
directory: /home/benji/projects/blog
---

# The setup command's options were read from the installed console rather than from the manual the skill routes to

## Observation

Task: boot the local DDEV development installation of the t3g/blog extension
repository. This reports what I never put to the server, since it sees the calls
made and nothing else.

typo3_documentation_lookup — never called, and here the substitution was right.
The skill's step 3 says to ask it for the setup command's options at the version
installed. I ran `ddev exec typo3 setup --help` instead, on the reasoning that
the installed binary is the authority for the exact option set at 14.3.6 and
costs one round trip. It held: --help gave me the option names, the defaults
already matching DDEV (host db, dbname db, username db), and one thing I doubt
the manual carries — that --distribution was rendered as "[disabled] Requires
typo3/cms-impexp to be installed" in this installation. Reporting it because the
skill routes to the lookup and the container answered better; the routing may be
worth softening where an installation is already reachable.

*Trimmed on 2026-08-18 (`D-SKL-057`).* Three halves of this report are carried
elsewhere. The typo3_extension_describe finding is corrected and taken further by
`feedback/2026-08-18-071500`, which recounts the cost as five checkout calls
rather than two and adds the seeding question behind it. The
typo3_changelog_lookup half — base.md's "name the step you did not reach" stated
twice and complied with neither, and a deprecation found at the end of a boot
with nothing inviting the question — is `D-SKL-049`, whose second **Wrong if**
it fires, with `D-SKL-048` carrying the probe that says the hint index is not
the route. The typo3_configuration_lookup half reported a routing that was never
needed because DDEV's own database container agreed with what setup wrote, and
named the two cases where it would have been; that is the skill's *The
environment's settings against the installation's own* section confirmed from
practice, and nothing follows from it.

## Query

Tools whose schemas I loaded via ToolSearch select: typo3_project_describe,
typo3_extension_describe, typo3_task_guide, typo3_hint_lookup,
typo3_configuration_lookup. Tools actually called: typo3_project_describe (x2),
typo3_hint_lookup (id=installation-boot), typo3_task_guide
(changeType=operations). Task: booting the DDEV installation of
github.com/TYPO3GmbH/blog.

## Suggestion

Consider softening the routing to typo3_documentation_lookup for the setup
command's options where an installation is already reachable: the installed
console is the authority for the exact option set, and it is the only source
that can say which options this installation's packages have disabled.
