---
description: >-
  What the TYPO3 backend styleguide is, where it lives, how it is installed where it is not shipped, and what its examples do and do not state.
whenToUse: >-
  Before writing backend markup or borrowing a core backend class or icon into a package. It names what the styleguide settles and what it does not, so a demo is not read as a contract for the parts it happens to include.
hints:
  - css-styleguide-demos
  - backend-ui
---

# Using the Backend Styleguide

The styleguide is the core's own demonstration of its backend components, and it
is the boundary of what a package may use: a component it lists is public and
one it does not list is not to be used or suggested. Nothing else in the core
states that, so the listing is the statement.

## Where the Styleguide Lives

**Since:** 13

It is a system extension, below `typo3/sysext/styleguide/`, and every
installation has it on disk whether or not it is activated. Activating it puts a
**Styleguide** module in the backend under the developer tools, and the
component demos are one page per component.

## Installing the Styleguide Where the Core Does Not Ship It

**Until:** 12

It is a separate package, `typo3/cms-styleguide`, required as a development
dependency. An installation that never required it does not have it at all, and
neither its module nor its templates can be read there.

## Reading It Without the Module

The templates are
`typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/`, and
the list of components is `$allowedActions` in
`typo3/sysext/styleguide/Classes/Controller/ComponentsController.php`. Both are
committed, so a checkout of a branch answers what that branch demonstrates
without anything being installed or run — which is how a question about a major
the installation does not have is settled.

## What an Example States, and What It Does Not

**An example is complete, and completeness is what it costs.** A table demo
carries a caption, a head, a foot and a control column; a card demo carries an
image, a header, a body and a footer. None of that says which parts make the
component and which the author added to show it off, and there is no minimal
form beside the full one to subtract from.

So a demo answers what a full usage looks like and never what is required.
Reading it the other way produces markup that carries everything, which works
and is not what the component is.

## What a Template Writes Is Not What the Demo Shows

A demo renders through ViewHelpers and web components as often as it writes
markup. The avatar demo is `<be:avatar>` and spells no avatar class at all, and
the status indicators are laid out in the styleguide's own `indicators-grid`,
which is page furniture rather than part of any component.

So the class names in a template are neither all of what the component uses nor
only that, and a class read out of one is a guess until something places it.

## What Places a Class

`typo3_component_lookup` answers where a class sits — around the component, on
it, or inside it — for each major, read from the core's compiled stylesheet.
That is the part a demo cannot be subtracted into, and it is what makes
borrowing a class safe. A class the answer does not place is one the stylesheet
says nothing about, which is not a licence to attach it anywhere.

A package declaring more than one major asks that question for each of them. The
installation supplies one, and the styleguide in it demonstrates that one.
