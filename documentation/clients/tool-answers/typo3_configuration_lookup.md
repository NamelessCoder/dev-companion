# What `typo3_configuration_lookup` answered

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

## configuration

Called with:

```json
{
    "path": "SYS/fluid"
}
```

### From the 14.3 core checkout below .checkouts/, whose console could not be reached

Text:

```
This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists.
typo3_server_scope reports the installation and its console.
```

Data:

```json
{
    "path": "SYS/fluid",
    "unsupported": {
        "cause": "installation-not-answering",
        "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists",
        "diagnosis": "",
        "searched": [
            "<installation>"
        ],
        "misconfiguration": null,
        "settings": {
            "root": "TYPO3_MCP_ROOT",
            "console": "TYPO3_MCP_CONSOLE"
        }
    }
}
```

### From the E-SITE this repository makes below .environments/, whose console answers

Text:

````
Effective value of TYPO3_CONF_VARS/SYS/fluid in this installation:

```json
{
    "interceptors": [],
    "preProcessors": [
        "TYPO3Fluid\\Fluid\\Core\\Parser\\TemplateProcessor\\EscapingModifierTemplateProcessor",
        "TYPO3Fluid\\Fluid\\Core\\Parser\\TemplateProcessor\\PassthroughSourceModifierTemplateProcessor",
        "TYPO3Fluid\\Fluid\\Core\\Parser\\TemplateProcessor\\NamespaceDetectionTemplateProcessor",
        "TYPO3Fluid\\Fluid\\Core\\Parser\\TemplateProcessor\\RemoveCommentsTemplateProcessor"
    ],
    "expressionNodeTypes": [
        "TYPO3Fluid\\Fluid\\Core\\Parser\\SyntaxTree\\Expression\\CastingExpressionNode",
        "TYPO3Fluid\\Fluid\\Core\\Parser\\SyntaxTree\\Expression\\MathExpressionNode",
        "TYPO3Fluid\\Fluid\\Core\\Parser\\SyntaxTree\\Expression\\TernaryExpressionNode"
    ],
    "namespaces": []
}
```

This is the assembled runtime value, not the default the core ships.
````

Data:

```json
{
    "path": "SYS/fluid",
    "found": true,
    "value": {
        "interceptors": [],
        "preProcessors": [
            "TYPO3Fluid\\Fluid\\Core\\Parser\\TemplateProcessor\\EscapingModifierTemplateProcessor",
            "TYPO3Fluid\\Fluid\\Core\\Parser\\TemplateProcessor\\PassthroughSourceModifierTemplateProcessor",
            "TYPO3Fluid\\Fluid\\Core\\Parser\\TemplateProcessor\\NamespaceDetectionTemplateProcessor",
            "TYPO3Fluid\\Fluid\\Core\\Parser\\TemplateProcessor\\RemoveCommentsTemplateProcessor"
        ],
        "expressionNodeTypes": [
            "TYPO3Fluid\\Fluid\\Core\\Parser\\SyntaxTree\\Expression\\CastingExpressionNode",
            "TYPO3Fluid\\Fluid\\Core\\Parser\\SyntaxTree\\Expression\\MathExpressionNode",
            "TYPO3Fluid\\Fluid\\Core\\Parser\\SyntaxTree\\Expression\\TernaryExpressionNode"
        ],
        "namespaces": []
    },
    "answeredBy": "installation"
}
```
