# Comment on RFC-XXXX: name the scheme, as the contract names the prefix

Drafted 2026-08-04, not yet filed. It is written to be pasted into the comment
period of
[RFC-XXXX, An Official MCP Interface Contract for TYPO3](https://gist.github.com/dkd-dobberkau/1f87ba4051fc85efbb9475d96babf1d5),
read there in its revision of 2026-08-03. What it rests on is
[`D-SCO-010`](../../decisions/scope/sco-010-all-three-typo3-namespaces-are-kept-and-the-scheme-is-raised.md);
who files it and when is a card in [todo/](../../todo/readme.md).

The draft's Process History dates only "Draft created", and its comments section
reads "To be added during the Public Comment Period", so whether the period is
open has to be established before this is sent. Everything below the line is the
comment itself.

---

## To the authors of RFC-XXXX

The draft is precise where namespaces are cheapest to get wrong and silent where
they are most expensive. **Namespace and Tool Naming** reserves `typo3.` for the
mandatory part and sends extensions to their own prefix, which resolves tool
naming in two sentences. **TCA as a Resource** then requires the content model
to be "served as a resource under a uniform scheme" and never says which scheme.

A tool name is local to one server's catalogue. A URI scheme is not: it is the
one namespace in MCP that two servers in the same client share whether they
meant to or not. So the namespace the draft leaves unnamed is the one that
actually collides.

### What already occupies `typo3://`

We maintain a local, read-only MCP server that guides coding agents through
TYPO3 work — version-bound knowledge about the core, plus facts read out of the
project the agent is in. It is not one of the two implementations the draft
names, and it is not a candidate for the mandatory set: it has no write path, no
backend identity, and it never touches a record.

It has served resources under `typo3://` since before this draft existed:

- `typo3://core` — an index resource: what the server covers, how it routes a
  question, and a listing of the documents below it.
- `typo3://core/<id>` — one markdown document per entry in its knowledge base.

That scheme is not an implementation detail we could quietly change. Every prose
answer the server returns carries the `typo3://core/<id>` of the document it
matched, its output schemas declare the field, and two tool descriptions
instruct the calling model to read the resource for the full text. The URI is in
the model's context, in the client's resource list, and in the answers users
have already read.

### Why this is the contract's problem and not only ours

If the contract's uniform scheme turns out to be `typo3://`, a conformant client
cannot tell a TCA resource from something else by the URI alone. It reads
`typo3://core` expecting a machine-readable content model and gets a
documentation index. That is worse than a clean failure, because nothing errors
— the model simply receives the wrong kind of document and proceeds.

The contract can be made immune to that in one sentence, and it already contains
the template for it. Applying the rule the draft gives tool names to schemes as
well would read something like:

> The scheme `typo3://` is reserved for the mandatory part of the contract.
> Implementations serving anything beyond it serve it under a scheme of their
> own.

That is unambiguous, it costs the draft one paragraph, and it tells every
implementer — us included — exactly what has to be renamed and by when. We are
not asking to keep the scheme. We are asking that the contract say who has it.

If reserving the whole scheme is too broad, the alternative that also works is
to make the mandatory resources distinguishable within it — a fixed authority
segment such as `typo3://tca/...`, stated in the contract rather than left to
each implementation. What does not work is leaving "a uniform scheme" undefined
through v1.0, because by then several servers will have picked one.

### This is adjacent to an Open Question you already have

Your Open Questions ask how the contract relates to public, read-only discovery
surfaces — llms.txt style signals — and note that shared terminology would be
desirable. A read-only server that describes an installation and its framework
to a model is the same shape of thing, one process boundary closer. Whatever the
answer to that question is, it needs the scheme settled first: both surfaces
describe TYPO3 to machines, and only one of them can hold `typo3://` by default.

### One question about the tool prefix

We read `typo3.` as reserving names that literally begin with `typo3.`, dot
included, and therefore as not reaching a tool named `typo3_page_read`. On that
reading the 26 tools we ship under a `typo3_` prefix are unaffected. If that is
not the intent — if the reservation is meant to cover the string `typo3` under
any separator, or if the conformance suite would fail a non-contract tool whose
name begins with it — please say so in the revision. It is a rename either way;
it is a cheap one now and an expensive one after the marker exists.

### One question for the Association rather than for the RFC

Our package declares itself as `typo3/cms-mcp` in its `composer.json` and is
published nowhere. The `typo3` vendor on Packagist belongs to the Association,
and we make no claim on it. We raise it here only because this RFC is the first
document to draw a line around what may present itself as TYPO3's MCP surface:
if the Association intends a policy on the vendor for MCP packages, we would
rather learn it from the contract than from a rejected submission.

### What we would do with an answer

Name the scheme and we rename ours to match, on our own schedule, before the
contract reaches v1.0. Leave it unnamed and we keep `typo3://core`, because
renaming against a scheme nobody has allocated buys the ecosystem nothing.
