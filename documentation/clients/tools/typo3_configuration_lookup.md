# `typo3_configuration_lookup`

Read an effective TYPO3_CONF_VARS value from the installation you are working in
— the value as it is at runtime after every extension has had its say, not the
shipped default. Use it for configuration whose assembled shape matters, such as
SYS/formEngine/formDataGroup, SYS/caching/cacheConfigurations, or SYS/fluid.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

## Takes

```yaml
# Slash-separated path into TYPO3_CONF_VARS, for example "SYS/fluid" or
# "SYS/formEngine/formDataGroup".
path: string
```

## Answers with

```yaml
# The TYPO3_CONF_VARS path that was read.
path: string
# Whether the installation has a value at that path. Present only where one was
# asked: false is a statement about an installation, and where there was none to
# ask, unsupported stands in place of this answer.
found: boolean  # optional
# The effective runtime value, of whatever shape the configuration has.
value: object  # optional
# One of: installation, packages. installation: its assembled runtime state
# answered. packages: read from the files the installed packages ship, because
# the console could not be asked — overrides applied at runtime are not
# reflected.
answeredBy: string  # optional
unsupported:  # optional
  # One of: no-installation, misconfigured, installation-not-answering.
  # no-installation: nothing to ask from here, and searched says where it
  # looked. misconfigured: an installation was named and could not be used, so
  # nothing was searched for. installation-not-answering: one was found and its
  # console did not answer — a stopped container or a database with no schema,
  # which is a state that ends without reinstalling anything.
  cause: string
  # What stopped it, in the words the attempt produced.
  reason: string
  # What the reason means where the message alone does not say it — a console
  # that starts and then fails on a missing table has a database without a
  # schema, not a broken installation. Empty where nothing beyond the reason is
  # known.
  diagnosis: string  # optional
  # Every directory the discovery walked, in order. "Nothing was found" and "the
  # server was started somewhere else" wear one sentence, and only this tells
  # them apart. Empty where discovery never ran.
  searched: [string]
  # What was set and could not be used. Null where nothing was set.
  misconfiguration: string or null  # optional
  settings:
    # Environment variable that names the installation root.
    root: string
    # Environment variable that names the console command.
    console: string
```

The answer carries exactly one of these sets of fields: `path`, `found`,
`answeredBy` — or `path`, `unsupported`.

## Answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against core-checkout, TYPO3
14.3.6-dev, the 14.3 core checkout below .checkouts/, whose console could not be
reached: <installation> has no TYPO3 console — none of bin/typo3,
vendor/bin/typo3 exists. Answered against composer-project, TYPO3 14.3.5, the
E-SITE this repository makes below .environments/, whose console answers. The
tools that declare `answeredBy` carry an answer from each, under a heading
naming which; every other answer is from the first alone, because nothing in it
would differ. Nothing checks what is below this heading; everything above it is
derived from the class that answers the call, and `bin/cli tools:check` holds
it.

### configuration

Called with:

```json
{
    "path": "SYS/fluid"
}
```

#### From the 14.3 core checkout below .checkouts/, whose console could not be reached

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

#### From the E-SITE this repository makes below .environments/, whose console answers

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
