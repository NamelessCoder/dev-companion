---
date: 2026-07-29T09:45:17+00:00
category: bug
status: closed
closed: 2026-07-29
commit: 8152bf6
subject: "Never say a configuration path is absent without asking"
tool: typo3_icon_lookuptypo3_label_lookuptypo3_fluid_namespace_listtypo3_configuration_lookuptypo3_backend_module_lookup
---

# CORRECTION to my earlier note on this topic — please read the two together, the earlier one sta...

## Observation

CORRECTION to my earlier note on this topic — please read the two together, the earlier one states the problem wrongly. I claimed the server does not say that the installation was unreachable. It does. Driving the binary directly over stdio from a directory with no installation, content[0].text reads: "The installation could not be asked, so this is unanswered rather than empty: no TYPO3 installation was found from the directory this server was started in. typo3_server_scope reports the installation and its console." That is exactly the promised behaviour and it is well worded. What I saw as a caller was only structuredContent, because my client surfaces the structured payload and drops the text block. So the real defect is narrower and different: the warning lives exclusively in the human-readable content, while structuredContent carries the opposite message. {"matchCount":0,"icons":[],"answeredBy":"nothing"} and especially {"path":"SYS/fluid","found":false,"value":null} are indistinguishable from a genuine miss, and found:false is a positive assertion that the path is absent. Any client that prefers structuredContent — which is the point of declaring one, and the direction MCP clients are heading — receives only the misleading half of the answer. The good prose is invisible precisely where machine consumption happens.

## Query

Correction to the earlier note "When no TYPO3 installation is reachable, the installation-answered tools return a normal-looking empty result instead of saying so"

## Suggestion

Keep the prose exactly as it is and make structuredContent carry the same meaning, so the two halves cannot disagree. Setting isError:true would be the bluntest fix. Failing that, drop the fields that imply a real lookup happened rather than emitting falsy values for them: no matchCount, no found, no value when no installation was consulted, replaced by an unambiguous marker such as {"unanswered":"no-installation","message":"...same text as the content block..."}. At minimum found must never be false unless a console actually answered. Renaming answeredBy:"nothing" to something that cannot be read as "no source had it", for example answeredBy:"no-installation-available", would also help clients that do inspect it.
