# Write the Fluid object access hint

**Serves:** feedback/2026-08-13-215637-fluid-resolves-get-is-has-methods-before-a.md
**Priority:** normal

Judged as `D-KNW-075`: step 1a, the knowledge is missing. The resolution order
is established in that entry's evidence and read on all four covered lines, so
this card is the symptom, the binding and the placement.

- Establish what `<f:for each="{obj.items}">` does on `.checkouts/12.4` and
  `13.4` when the value is a boolean. The message the feedback quotes — "The
  argument "each" was registered with type "array", but is of type "boolean"" —
  comes from the strict argument processor `fluid-viewhelpers` already states
  `since: 14`; `ForViewHelper` on 13.4 takes its `foreach` over whatever
  arrived. Bind the statement to what each side actually does.
- State the order: an array key or `ArrayAccess` offset, then `has()`/`get()`
  where the subject is a PSR-11 container, then `getFoo()`, `isFoo()`,
  `hasFoo()`, and the public property `foo` last. The container branch is
  `since: 13`; the rest holds unbound.
- State the two consequences, which is what the statement is for.
  `{obj.hasItems}` never reaches `hasItems()` — Fluid looks for `getHasItems`,
  `isHasItems`, `hasHasItems` and the property. And a `hasItems()` shadows a
  public `$items`, so `{obj.items}` is the boolean and the array is unreachable.
- Name the core's own convention as the worked example:
  `SlidingWindowPagination::getHasMorePages()`, read in a template as
  `{pagination.hasMorePages}`. The template is
  `redirects/Resources/Private/Partials/Pagination.fluid.html` in
  `.checkouts/main`.
- Place it. `fluid-conditions-and-arrays` is where `fluid-templates` routes an
  expression that goes wrong, and its title is about conditions, escaping and
  array literals; a hint of its own beside it is the alternative.
  `preview-record-variable` is not touched — it is an instance of this order,
  not a second statement of it.
- `appliesTo` decides whether it arrives. The query that missed was written in
  the mechanism's words and matched nothing, so the symptom belongs in there
  too, and `bin/cli hints:probe` with the feedback's own query is what says it
  works.

The feedback is archived by the commit that lands the hint.
