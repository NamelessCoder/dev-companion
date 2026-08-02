---
date: 2026-08-02T14:45:11+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_server_scope
directory: /home/benji/projects/typo3-cms
---

# Task: evaluate Forge #105403, find related issues, find existing patches, then write a fix.

## Observation

Task: evaluate Forge #105403, find related issues, find existing patches, then write a fix.

Three of the four things I was asked for lived entirely outside this server, and the whole first phase of the session was spent on tooling I had to improvise:

- WebFetch against https://forge.typo3.org/issues/105403 returned HTTP 403. Forge sits behind Anubis bot protection which challenges browser-like user agents; a curl with the default curl/x user agent passes, and a curl carrying a Chrome user agent gets the challenge page with HTTP 200 and an HTML body. So the naive fix (set a browser UA) is exactly wrong, and I burned two round trips discovering that.
- Issue data then came from the Redmine JSON API: /issues/<id>.json?include=journals,relations,changesets, which carries status, tracker, fixed_version, the custom field "TYPO3 Version", relations, and the journal notes that contained the two core maintainers' verdicts — the decisive evidence for judging the issue.
- Similar issues came from /search.json?q=<terms>&issues=1&limit=15.
- Existing patches came from the Gerrit REST API at https://review.typo3.org/changes/?q=message:105403&o=CURRENT_REVISION, whose response needs the first five bytes of XSSI protection stripped before it parses as JSON. It returned zero changes, which was itself the answer to "has anyone already fixed this".

None of this is TYPO3 knowledge in the sense this server curates, and I am not arguing it belongs in the catalogue. But "look at this Forge issue and fix it" is a very common shape of core work, and every session that gets it will rediscover the Anubis user-agent inversion and the Gerrit XSSI prefix from scratch.

## Query

Task text: "evaluate Forge issue https://forge.typo3.org/issues/105403, check whether it is valid, find similar issues, find patches that already fixed it, then create a patch". No server tool covers Forge or Gerrit; resolved with curl against forge.typo3.org/issues/105403.json?include=journals,relations,changesets and review.typo3.org/changes/?q=message:105403

## Suggestion

Either add a thin lookup for the two services — a Forge issue fetch returning subject, status, tracker, target version, relations and journal notes, and a Gerrit search by issue number returning change number, branch and status — or, much cheaper, state the access recipe in typo3_server_scope under what the server deliberately does not cover: that forge.typo3.org serves JSON at /issues/<id>.json and /search.json but is behind bot protection that rejects browser user agents while accepting curl's default, and that review.typo3.org/changes/?q=message:<issue> answers "is there already a patch" once the XSSI prefix is stripped. Naming where the answer lives is a legitimate answer for something out of scope, and it would have saved the most wasted round trips of this session.
