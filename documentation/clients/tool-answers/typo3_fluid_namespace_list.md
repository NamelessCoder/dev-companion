# What `typo3_fluid_namespace_list` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against core-checkout, TYPO3
14.3.6-dev, the 14.3 core checkout below .checkouts/, whose console could not
be reached: <installation> has no TYPO3 console — none of bin/typo3,
vendor/bin/typo3 exists. Answered against composer-project, TYPO3 14.3.5, the
E-SITE this repository makes below .environments/, whose console answers. The
tools that declare `answeredBy` carry an answer from each, under a heading
naming which; every other answer is from the first alone, because nothing in it
would differ. Nothing checks this page; [tools.md](../tools.md) is where the
current shape of an answer is, and [readme.md](readme.md) is what the recording
as a whole is of.

## namespaces

Called with:

```json
{}
```

### From the 14.3 core checkout below .checkouts/, whose console could not be reached

Text:

```
3 globally registered Fluid namespace(s):
- core: TYPO3\CMS\Core\ViewHelpers
- f: TYPO3\CMS\Adminpanel\ViewHelpers\Fluid, TYPO3Fluid\Fluid\ViewHelpers, TYPO3\CMS\Fluid\ViewHelpers
- formvh: TYPO3\CMS\Form\ViewHelpers

These prefixes work in any template without being declared. Every other namespace is declared in the template itself — xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers" on the root element, together with data-namespace-typo3-fluid="true" so the declaration is stripped from the output.

Read from the Configuration/Fluid/Namespaces.php of the installed packages: the console could not be asked (<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists). That is what the packages declare, not what the container assembled from them.
```

Data:

```json
{
    "matchCount": 3,
    "namespaces": [
        {
            "prefix": "core",
            "phpNamespaces": [
                "TYPO3\\CMS\\Core\\ViewHelpers"
            ]
        },
        {
            "prefix": "f",
            "phpNamespaces": [
                "TYPO3\\CMS\\Adminpanel\\ViewHelpers\\Fluid",
                "TYPO3Fluid\\Fluid\\ViewHelpers",
                "TYPO3\\CMS\\Fluid\\ViewHelpers"
            ]
        },
        {
            "prefix": "formvh",
            "phpNamespaces": [
                "TYPO3\\CMS\\Form\\ViewHelpers"
            ]
        }
    ],
    "answeredBy": "packages"
}
```

### From the E-SITE this repository makes below .environments/, whose console answers

Text:

```
3 globally registered Fluid namespace(s):
- core: TYPO3\CMS\Core\ViewHelpers
- f: TYPO3Fluid\Fluid\ViewHelpers, TYPO3\CMS\Fluid\ViewHelpers
- formvh: TYPO3\CMS\Form\ViewHelpers

These prefixes work in any template without being declared. Every other namespace is declared in the template itself — xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers" on the root element, together with data-namespace-typo3-fluid="true" so the declaration is stripped from the output.
```

Data:

```json
{
    "matchCount": 3,
    "namespaces": [
        {
            "prefix": "core",
            "phpNamespaces": [
                "TYPO3\\CMS\\Core\\ViewHelpers"
            ]
        },
        {
            "prefix": "f",
            "phpNamespaces": [
                "TYPO3Fluid\\Fluid\\ViewHelpers",
                "TYPO3\\CMS\\Fluid\\ViewHelpers"
            ]
        },
        {
            "prefix": "formvh",
            "phpNamespaces": [
                "TYPO3\\CMS\\Form\\ViewHelpers"
            ]
        }
    ],
    "answeredBy": "installation"
}
```
