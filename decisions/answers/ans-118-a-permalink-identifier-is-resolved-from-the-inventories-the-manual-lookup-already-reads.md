---
id: D-ANS-118
title: A permalink identifier is resolved from the inventories the manual lookup already reads
date: 2026-08-27
status: open
coveredBy: []
---

# D-ANS-118 — A permalink identifier is resolved from the inventories the manual lookup already reads

**A docs.typo3.org permalink identifier is validated, resolved to its page and
recovered from an old URL out of the Sphinx inventory this server already
fetches.**

A session replacing hardcoded documentation URLs in a core checkout spent most
of it on one question — does this identifier resolve, and what replaces this URL
— and answered it with about sixty `curl` round trips against docs.typo3.org.

## Evidence

- The knowledge half was answered the same day and the other half was not.
  `documentation-links`
  ([`D-KNW-128`](../knowledge/knw-128-building-a-link-into-the-official-documentation-is-a-subject-this-server-owns.md))
  states how such a link is built — the identifier, the appended branch, the
  ViewHelper, `getDocsLink`. Nothing in it says which identifier, whether one
  resolves, or what replaces an old URL.
- `bin/cli hints:probe "docs.typo3.org permalink identifier"` ranks
  `documentation-links` first. So delivery, routing and wording are not the
  lever: the entry that exists arrives, and the half the session needed has no
  entry to deliver.
- `typo3_documentation_lookup` cannot be asked it. Its index is page titles and
  section paths, and its results carry a URL, a document and a section — never
  an identifier. The session read that from the description and went to `curl`,
  which was the correct reading.
- The artefact is already fetched. `Manual\Documentation` reads each manual's
  `objects.inv` per branch and holds it against the host's `ETag` (`D-ANS-065`),
  so a second read of a current inventory costs one round trip and no payload.
- `Documentation::pages()` reads only the `std:doc` role, because what it
  searches is a table of contents (`R-DOC-001`). The identifiers are in the
  roles it skips.
- **The inventory is the identifier index.** TYPO3 Explained at `main` lists
  12093 names, and `extension-scanner` is among them as `std:label` pointing at
  `ExtensionArchitecture/HowTo/UpdateExtensions/ExtensionScanner.html#extension-scanner`
  — the exact target `/permalink/t3coreapi:extension-scanner` answers 307 with.
  So resolving an identifier, validating one and reversing a URL back to one are
  three readings of a table already in hand, not three network questions.
- The TCA reference at `main` lists 2919 names across five roles, of which 1669
  are `std:label` and 748 `std:confval`. `t3tca:columns-onchange` and
  `t3tca:confval-columns-onchange` both answer 307 with the same anchor, so the
  `confval` name and its generated label are two spellings of one target and the
  inventory is what tells them apart.
- Both system-extension spellings resolve to one target:
  `typo3/cms-felogin:typo3-frontend-login` and
  `typo3-cms-felogin:typo3-frontend-login` each answer 307 with
  `c/typo3/cms-felogin/main/en-us/Index.html#start`.
- **An unknown version falls back to `main` without saying so.**
  `t3tca:columns-onchange@99.9` answers 307 with the `main` page.
  `t3tca:confval-input-search-case` is 404 at `main` and 307 at `@13.4`. So a
  caller that trusts the status code is told an identifier resolves for a
  release whose manual it never reached, which is what `R-DOC-001` forbids this
  server to pass on.
- Guessing the identifier fails per manual and no rule covers it.
  `typo3-cms-lowlevel:start` is 404 while `typo3-cms-lowlevel:typo3-low-level`
  is 307; `typo3-cms-felogin:start` is 307 today, where the feedback recorded it
  as 404 on 2026-08-25. The reporting session's own conclusion — that guessing
  costs more than it saves — is what both readings support.
- The shortcode is not derivable from the manual path. `t3tsref` is
  `reference-typoscript`, `t3contribute` is `guide-contributionworkflow`, and
  `t3start` is `tutorial-getting-started`. All three answer 307, and all three
  name manuals this server does not search.

## Decided

- **Step 1b, taken on.** The answer is available here and there is no shape that
  delivers it: a tool is missing, and the verb is `lookup` — a query goes in,
  matching entries come out, and finding nothing is a legitimate answer.
- Proposed: typo3_permalink_lookup, written here plainly and nowhere in the
  queue, because a tool name is a claim about today wherever a caller could read
  it as one — `D-DOC-040`.
- **Not closed on the spot.** It adds a class under `src/Tool/` and a declared
  schema, which the ladder puts on the reviewed side however well established
  the mechanism is.
- The answer is read from the inventory rather than from `/permalink/`. One
  inventory already held answers every identifier of that manual, where the
  status-code route is one request each and reports a fallback as a hit.
- The reverse direction is the same table read the other way, so mapping an old
  URL to the identifier that replaces it is in scope rather than a second tool.
- Both spellings and both of a `confval`'s names are answered as equivalent, and
  the answer says which one the inventory generates, because that is the review
  question the session had to invent a rule for.
- The version is the caller's and is never silently widened. An identifier asked
  for a covered release is looked up in that release's inventory, and the
  `@<branch>` fallback is reported rather than passed on.
- **Priority `normal`, set by the round trips rather than by the count of
  sessions.** One session reported it, which is why it is not `high`; it is the
  largest round-trip cost in the corpus and the artefact is already fetched,
  which is why it is not `low`
  ([`D-FBK-027`](../feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)).
- What waits for the todo is which manuals the tool answers for. This server
  searches four and the shortcodes reach further, so the map from shortcode to
  manual is a maintained list whose extent nobody here has established.

## Assumed

- That every manual on the host publishes its inventory at the same path and in
  the same format. It was read for TYPO3 Explained and the TCA reference, both
  under `/m/`, and the system-extension manuals under `/c/` were reached only
  through `/permalink/`.
- That the inventory names and the identifiers the permalink route accepts are
  one set. Every name checked in both directions agreed, and no manual was read
  exhaustively against the route.
- That an extension manual is out of scope. The identifier space is open-ended
  once `/c/` is included, and the reporting session's own examples were core and
  system-extension manuals.

## Wrong if

- The permalink route stops resolving inventory names, or starts accepting names
  no inventory lists. Then the identifier space is not the inventory and the
  tool answers from the wrong table.
- A session asks it for an extension manual and is told the shortcode is unknown
  for something that resolves. Then the maintained map is the boundary in the
  wrong place, and the tool should derive the manual rather than list it.
- The shortcode map goes stale without anything failing. Nothing on this host
  publishes the list, so a manual renamed or added is a 404 the next session
  reports as a gap.
- A caller uses it to validate a link and still lands on `main` documentation
  for an LTS. Then the version fallback was reported and not believed, and the
  answer has to withhold the hit rather than qualify it.
- It answers what `typo3_documentation_lookup` already answers. The two read one
  artefact, and a caller asked to choose between them by name is a boundary
  stated in the wrong place.
