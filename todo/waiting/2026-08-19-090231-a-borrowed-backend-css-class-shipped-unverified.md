# Carry a second derived range for the class list

**Serves:** feedback/2026-08-19-090231-a-borrowed-backend-css-class-shipped-unverified.md
**Priority:** normal
**Waiting on:** whether the second range stays on the class list or goes down to
    one class each. The maintainer's answer named the class list, and derived on
    2026-08-21 against 12.4, 13.4, 14.3 and main it does not reach the caller
    this came from: `table-fit` is written in `_table.scss` on 12.4, and
    `table-striped`, `table-hover`, `table-sm` and `table-selected` are not, so
    the table class list binds at v13 and a v12 caller is still told nothing.
    Four of the 26 entries reach further back than their entry does — card and
    pagination to every covered version, panel and table to v13 — so a v13
    caller asking about `table-fit` is answered today and a v12 caller is not.

A range per class would answer the v12 case. It costs 120 class-to-version
    pairs across 21 entries in `components.json`, written by hand and re-derived
    by hand on every core release, because `bin/cli catalog:check` reports a
    binding rather than writing one. That is the trade to decide, and it is the
    finer split `D-CAT-001` rejected once for the entry — for a reason that does
    not carry here, since a class-shaped answer hands over one class and one
    range rather than four ranges in one paste.

    The recommendation is to leave it on the class list until a second feedback
    asks about a class below its list's binding. The mechanism is built either
    way, and going finer later changes the recorded field and the derivation
    while the answer shape, the decision and the documentation stand.

The class list carries its own range and the lookup answers from it, on this
branch:

- `classesSince` on each entry in `knowledge/catalog/components.json`, derived
  by `bin/cli catalog:check` from what the entry's own range already reads, one
  fewer name.
- `coveredClasses` in `typo3_component_lookup`: the classes the query named
  outright, on a target version their entry is withheld for. It carries the
  name, the range and the Sass file the core writes it in, in a block of its
  own, and no markup and no custom property — which is what says a covered class
  is not a covered component.
- `D-CAT-006` records the shape, the measurement above and what would show it
  wrong. `D-CAT-001`'s **Since then** now names it and says it did not reach
  v12.

What this todo does not touch, and what the feedback still holds open: the entry
point. The session's own account is that the task announced itself as npm
dependency maintenance and no instruction attached to a task reached it, which
is `D-SKL-067`'s subject rather than the catalog's.
