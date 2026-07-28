---
date: 2026-07-28T15:00:45+00:00
category: missing-knowledge
status: open
tool: typo3_icon_lookup
---

# The server validates icon identifiers but never says how to render one, which is the other half o...

## Observation

The server validates icon identifiers but never says how to render one, which is the other half of the job. typo3_architecture_hint for exactly that task returned a single CSS hint about layout stability and nothing about the APIs. The only place any usage appears is an incidental example in the button component. So an agent gets a correct identifier and then has to guess the call, and the guesses have known wrong answers: in PHP the Icon::SIZE_SMALL constants were replaced by the IconSize enum (typo3/sysext/core/Classes/Imaging/IconSize.php, cases DEFAULT SMALL MEDIUM LARGE MEGA OVERLAY), so IconFactory::getIcon('actions-close', IconSize::SMALL)->render() is current and the constant form is not. In Fluid it is <core:icon identifier="actions-delete" size="small" /> with the core namespace. In TypeScript there are two paths that are easy to confuse: the Icons module imported as Icons from '@typo3/backend/icons' for programmatic retrieval, and the <typo3-backend-icon identifier="..." size="small"> custom element from @typo3/backend/element/icon-element for markup. None of this is retrievable from the server.

## Query

typo3_architecture_hint task="render a core icon in a Fluid template, in PHP and in a TypeScript backend module"

## Suggestion

Add a knowledge section on icon usage, reachable from typo3_architecture_hint and ideally attached to every typo3_icon_lookup result as a short usage block: the Fluid ViewHelper with its arguments (identifier, size, state, overlay, alternativeMarkupIdentifier), the PHP IconFactory with the IconSize enum and the explicit note that Icon::SIZE_* constants are gone, and the two TypeScript entry points with their import paths. Add a second section for adding a new icon: T3Icons come from the npm package and are not hand-added to a sysext, whereas an extension-specific icon is registered in Configuration/Icons.php with SvgIconProvider or BitmapIconProvider and a source path.
