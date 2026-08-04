---
id: D-SCO-010
date: 2026-08-04
status: open
---

# D-SCO-010 — All three `typo3` namespaces are kept, and the scheme is raised in the RFC's comment period

**This server keeps the `typo3_` tool prefix, the `typo3://core` resource scheme
and the `typo3/cms-mcp` package name, and raises the scheme with the RFC's
authors rather than renaming ahead of them.**

The draft RFC on an official MCP interface contract for TYPO3 reserves `typo3.`
for the mandatory part of the contract and requires the content model to be
served as a resource under a uniform scheme it does not name. Two of the three
namespaces here are outside that reservation; the third is inside the sentence
that names no scheme.

Nothing in `decisions/` had ever said why the prefix is `typo3_`. So this entry
records a choice that was never made rather than one being defended, which is
the difference between a decision and a rationalisation.

## Evidence

- The draft was read whole on 2026-08-04 at
  <https://gist.github.com/dkd-dobberkau/1f87ba4051fc85efbb9475d96babf1d5>. It
  carries one revision, created 2026-08-03, and no comments.
- Its **Namespace and Tool Naming** section reserves one prefix and puts a dot
  in it: "The prefix `typo3.` is reserved for the mandatory part of the
  contract. Extensions register their tools under their own prefix." None of the
  26 tool names here contains a dot. They are `typo3_<subject>_<verb>`, which
  `ToolNamingTest` holds against
  [`Registry`](../../src/Tool/Registry.php).
- Its **TCA as a Resource** section says "The content model MUST be served as a
  resource under a uniform scheme and MUST NOT be decomposed into a large number
  of generated tools". The draft names no scheme, there or anywhere else in it.
  `typo3://` is therefore not taken by the document — it is what the document
  leaves to be allocated, and it is the scheme a contract about TYPO3 reaches
  for first.
- `typo3://core` is this server's index and `typo3://core/{id}` is each
  knowledge document, in [`ResourceHandler`](../../src/Sdk/ResourceHandler.php)
  and registered by [`Factory`](../../src/Server/Factory.php). It is not an
  internal detail: every prose answer prints the URI of the document it matched
  ([`Result\Prose`](../../src/Result/Prose.php)), the shared record shape
  declares it ([`Result\Schema`](../../src/Result/Schema.php)), and
  [`RuleLookup`](../../src/Tool/RuleLookup.php) and
  [`ServerScope`](../../src/Tool/ServerScope.php) tell the caller to read it.
- The draft names two implementations, hauptsacheNet/typo3-mcp-server and
  marekskopal/typo3-mcp-server. It does not name this package. Both of those
  publish under a vendor of their own — `hn/typo3-mcp-server` and
  `marekskopal/typo3-mcp-server` on Packagist, read 2026-08-04.
- `typo3/cms-mcp` is declared in `composer.json` and published nowhere.
  Packagist's search answers `{"results":[],"total":0}` for it and
  `repo.packagist.org/p2/typo3/cms-mcp.json` answers 404, both read 2026-08-04.
- Packagist protects a vendor once a package has been published under it, and
  says publishing under an existing one requires being a maintainer of at least
  one package already in it. `typo3/cms-core` lists the maintainers `typo3`,
  `bmack` and `ohader`, read 2026-08-04. Nobody here is one of them.
- The draft's **Process History** table dates a single row, "Draft created", to
  2026-08-03. The row for the Public Comment Period carries no date, and the
  comments section reads "To be added during the Public Comment Period." So
  whether the period is open is not readable from the document, against what
  `todo/waiting/2026-08-03-150000` claimed.

## Decided

- All three namespaces are kept, unchanged: the 26 `typo3_` tool names, the
  `typo3://core` scheme, and `typo3/cms-mcp` in `composer.json`.
- The tool prefix stays because the reservation is `typo3.` and an underscore
  name does not literally collide with it. That is the first reason this
  repository has ever written down for the prefix, and it is a reading of
  somebody else's draft rather than the reason the prefix was chosen — nobody
  knows what that was.
- The scheme is raised with the RFC's authors in its comment period rather than
  renamed ahead of a decision. Before a scheme is allocated this is a sentence
  in somebody else's draft; after it is allocated it is a rename of a URI that
  four classes print and two tool descriptions send the caller to. The comment
  is drafted at
  [documentation/interface-contract/comment-on-the-uniform-scheme.md](../../documentation/interface-contract/comment-on-the-uniform-scheme.md)
  and filing it is a todo of its own, because a comment nobody files settles
  nothing.
- Whether this package may publish as `typo3/cms-mcp` is **unresolved**. The
  vendor belongs to the TYPO3 Association, Packagist would refuse the
  submission, and the RFC does not decide it. What is decided here is only that
  the name stays in `composer.json` while nothing is published, because a name
  that resolves nothing costs nothing. It is asked in the same comment.
- Rejected: renaming the scheme now, to `typo3-cms-mcp://` or similar. It pays
  the whole cost of a rename against a scheme nobody has allocated, and it gives
  the contract's authors one fewer reason to name the scheme at all.
- Rejected: renaming the tool prefix. The reservation does not reach it, and a
  tool name is what clients installed months ago call — the outward name
  `AGENTS.md` says wins where two spellings compete.

## Assumed

- The comment period is read by people who can change the draft, and a third
  implementation already occupying the scheme is an argument they weigh rather
  than a fact they note. Nothing here can make that true.
- The dot in `typo3.` is deliberate and the contract's own tool names will carry
  it. The draft states the prefix once and never gives the separator a rule, so
  this reading rests on one sentence.
- A URI scheme this server hands to a client is not something the client
  resolves against any registry, so `typo3://core` and a contract's `typo3://`
  can coexist in one client until something has to tell them apart.

## Wrong if

- The contract allocates `typo3://` to the content model. Then a conformant
  client that reads `typo3://core` gets a knowledge index where it expects a
  schema, which is ambiguity rather than a clean failure, and the fix is a
  rename of the scheme in `ResourceHandler`, `Factory`, `Result\Prose`,
  `Result\Schema` and the two tool descriptions that name it.
- The comment is filed and the scheme is fixed without it being answered. The
  signal is the Final Comment Period opening with a scheme in the contract and
  no reply on this point; the fix is then to rename before v1.0 rather than
  after it, and to stop treating the comment as the thing that protects the
  scheme.
- The reservation changes from `typo3.` to one that catches `typo3_` — a rule
  about the string `typo3` whatever the separator, or a conformance suite that
  rejects a non-contract tool whose name begins with it. Then all 26 names go,
  and the only reason this entry gives for the prefix goes with them.
- Publishing under the `typo3` vendor is refused, or the Association asks for
  the name back. Then `composer.json` carries a name this package may not use,
  and the rename is cheap only for as long as nothing has installed it.

## Covered by

- `ToolNamingTest::everyToolIsNamedSubjectThenVerb`
- `StdioServerTest::theKnowledgeIndexIsServedWithTheScope`
