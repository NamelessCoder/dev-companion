---
date: 2026-07-29T10:50:31+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: 9dec0e3
subject: "Write down how a page is rendered, not only how a module is"
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# The Fluid architecture hints are entirely backend-facing and miss the frontend page rendering pat...

## Observation

The Fluid architecture hints are entirely backend-facing and miss the frontend page rendering path, which is the mechanism every TYPO3 v13/v14 site is built on.

What comes back: .fluid.html vs .html, globally registered namespaces, xmlns:be plus data-namespace-typo3-fluid, 'a backend module resolves templates through the templateRootPaths of its view factory', escaping, and pointers to typo3_component_lookup / typo3_icon_lookup. All of it is about backend modules.

What is missing, verified against typo3/cms-frontend v14.3 while building a site:
- PAGEVIEW is the frontend counterpart. page.10 = PAGEVIEW with paths.10 = EXT:<ext>/Resources/Private/.
- The path derivation is not obvious and is not documented anywhere the lookup points to: PageViewContentObject appends 'Pages/' to each configured path for templateRootPaths, 'Partials/' for partialRootPaths and 'Layouts/' for layoutRootPaths. So one 'paths' entry produces three roots.
- The template NAME is not configurable. It is the page layout identifier with the pagets__ prefix stripped, via PageLayoutResolver::getLayoutIdentifierForPageWithoutPrefix(), defaulting to 'default'. So backend_layout = pagets__landingpage renders Pages/Landingpage.fluid.html and nothing else will.
- Content reaches the template as a ContentAreaCollection under the variable name from 'contentAs', default 'content', addressed as {content.<identifier>} and rendered with <f:render.contentArea contentArea="{content.main}"/>. ContentAreaViewHelper exists in v14.3.
- Site set settings arrive as {settings.<path>}; 'site', 'language' and 'page' are reserved variable names and PAGEVIEW throws 1711748615 if a variables. entry uses one.
- In contrast to FLUIDTEMPLATE, PAGEVIEW deliberately does not support custom layoutRootPaths/partialRootPaths or templateName./template./file. resolving.

I had to read PageViewContentObject.php, PageLayoutResolver.php and ContentAreaCollection.php in the vendor tree to get any of this, and cross-check against sysext/theme_camino on main for a worked example. A 'Fluid: frontend page rendering' section would be the highest-value addition for anyone building a site.

## Query

task="Fluid templates frontend"
