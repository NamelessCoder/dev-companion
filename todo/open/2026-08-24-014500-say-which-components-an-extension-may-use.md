# Say which components an extension may use

**Serves:** D-CAT-004
**Priority:** high

Take the styleguide as the public API boundary and record it per component:
**what the styleguide lists is public API**, said by the maintainer on
2026-08-24. That replaces the criterion `D-CAT-004` selects the index on — a
partial below `Build/Sources/Sass/component/`, a custom element — which is
structural and admits what the backend keeps to itself.

## What it settles, and what it leaves

Settled: a component the styleguide lists may be used by an extension, and one
it does not list may not. The selection criterion follows from that rather than
from the Sass directory, and the 30 template files on 14.3 are the list against
26 entries here.

Left open, and it decides the work:

- **Whether listing a component makes all of its classes public**, or only the
  ones an example shows. Either is derivable now that the loop variables are
  read from the controller, and which one is meant is the maintainer's.
- **12.4 has no styleguide in the core.** It became a system extension with 13.4
  and was an installable package before, so the boundary is either read from an
  installed package there or is not answerable on that major.
- Whether the boundary binds by version. A component can be opened up or closed
  off between majors, and the rendered listing per major is what would say.

## The listing is a committed file, not a rendered page

`typo3/sysext/styleguide/Classes/Controller/ComponentsController.php` holds
`$allowedActions`, which is the styleguide's own component listing — 31 entries
on 14.3, in one file, per branch. The variants each demo shows are assigned in
the same file, as PHP arrays: the badges view assigns `primary`, `secondary`,
`info`, `success`, `warning`, `danger`, `notice` and `default`, which is what
`<span class="badge badge-{variant}">` becomes.

So the boundary is read the way the positions are, out of the checkouts, and no
instance is needed. An earlier reading of this said the loops made rendering
unavoidable; the loops are fed from a file.

What still has no source is 12.4, where the styleguide is not in the core.

## What it costs to be wrong

Calling something public that the core changes at will is how an extension
breaks on a minor release. It is worse than silence, because silence sends the
reader to the Sass and a marking does not.
