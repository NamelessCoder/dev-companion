---
id: D-ANS-053
date: 2026-08-04
status: open
---

# D-ANS-053 — A rejected call names the argument that was not understood

**An argument this server does not have is answered by name, so a caller that
spelled one wrong is corrected in the rejection rather than in a second round
trip.**

`typo3_documentation_lookup` is the only lookup here whose search argument is
`queries`; five others spell it `query`. A session guessed from the five, and
the rejection named neither the guess nor the near miss.

## Evidence

- `feedback/2026-08-04-175819`: the call carried `query` and `targetVersion` and
  came back "Missing required properties: `queries`.; Missing required
  properties: `page`." The unknown property was ignored, so both `oneOf`
  branches failed and the message described a call the session had not made.
- `typo3_changelog_lookup`, `typo3_icon_lookup`, `typo3_label_lookup`,
  `typo3_component_lookup` and `typo3_backend_module_lookup` take `query`;
  `typo3_hint_lookup` takes `task`. The plural is not a slip — it takes several
  alternatives at once — but it is one word against five.
- `DocumentationLookup::inputSchema()` declares no `additionalProperties`, so an
  argument nothing here knows is silently dropped before the tool runs. That is
  the whole surface, not one tool.
- The cost is paid twice in a client where these tools arrive schema-deferred: a
  name is guessed or a schema is fetched before any call, so a wrong guess buys
  a failed call and a fetch. The reporting session counted both.

## Decided

- The judgement is **step 4**, wording, on the message rather than on the
  description. `D-ANS-012` put the alternative into the two descriptions and
  that held: this session composed a search correctly and named it wrong.
- Two candidates, and the todo decides between them after reading what the SDK
  does: declare `additionalProperties: false` so the validator rejects an
  unknown argument by name, or accept `query` as a singular alias folded into
  `queries`.
- The alias is the weaker one and is written down as such. "One thing, one word"
  is the rule this repository holds names to, and a second spelling that works
  is what makes a third one arrive.
- Not closed on the spot: both candidates move a declared input schema.

## Assumed

- That the SDK's validator reports a property it refuses by name. Nothing here
  has run that; it is the todo's first step, and if it does not, the alias is
  what is left.
- That no client sends arguments of its own alongside a call. A wrapper adding
  metadata would start failing on the day `additionalProperties` is declared.

## Wrong if

- The unknown argument is rejected by name and a session still guesses the
  spelling before reading it. Then the answer is one vocabulary across the
  lookups, which is a rename and a breaking one.
- A client is found that sends its own extra properties. Then the strict schema
  is a break for a message, and the alias is the answer.

`{"query": "…", "targetVersion": "14.3"}` is answered today with "Missing
required properties: `queries`.; Missing required properties: `page`." — the two
`oneOf` branches, neither of which the caller was asking about. With
`additionalProperties` declared on this tool's input schema the same call comes
back as "Additional object properties are not allowed: [\"query\"]", so the
SDK's validator does name a property it refuses, and the assumption above holds.

It is declared on this tool and on no other. A lookup whose search argument is
simply `required` already names the missing one, so what the keyword buys
elsewhere is the risk of refusing a client that sends a property of its own —
and nothing here has evidence about such a client either way. The alias stays
unbuilt for the reason this entry gave: a second spelling that works is what
makes a third arrive.

`StdioServerTest::aCallNamingAnArgumentTheToolDoesNotHaveIsRejectedByThatName`
holds it, beside the case `D-ANS-012` left.

## Since then

On 2026-08-04, both candidates were measured against this checkout over stdio
and the first one was taken.
