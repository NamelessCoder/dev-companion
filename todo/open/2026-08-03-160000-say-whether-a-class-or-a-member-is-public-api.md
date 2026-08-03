# Say whether a class or a member is public API

**Serves:** feedback/2026-08-01-115713-while-reviewing-patch-7175fcaf7fe-i-had-to-read.md
**Priority:** normal

Whether a removed method was public API or `@internal` is what decides whether a
removal is breaking, and a patch review reaching that question today reads the
class and its history by hand — `D-KNW-036`'s sibling case, reported while
reviewing 7175fcaf7fe, where `GifBuilder` is public API and the removed
`getTemporaryImageWithText()` is not. `D-FBK-037` decided the capability is
worth building; this is what is left to settle before it is.

The mechanism is already here: `PhpArray`, `Extension` and `FluidNamespaces`
read shipped PHP with `token_get_all` without executing it, and a class-level
and member-level `@internal` is a docblock in the same token stream. What is not
settled is where the answer comes from, and that decides the tool rather than
follows from it.

The report asks for the answer **on each covered TYPO3 version**, which no
caller's checkout can give: it has one. Reading the installation answers for the
version in front of the caller and reports `unavailable` without one, which
`D-FBK-027` names as the shape that buys the caller nothing. Reading
`.checkouts/` is not on offer either — those are this repository's own and ship
with nothing. So decide between the installation alone, a bundled catalog per
covered major built the way the component catalog is, and the two together with
the answer saying which one spoke. Establish first what share of the question is
actually version-dependent: a class's `@internal` rarely moves, and if it does
not, the per-version half is a smaller promise than the report assumes.

Whichever wins, the answer says what it read and what it could not: an absent
docblock is not the same as public API, and a tool that conflates them turns a
missing annotation into a green light for a breaking change.
