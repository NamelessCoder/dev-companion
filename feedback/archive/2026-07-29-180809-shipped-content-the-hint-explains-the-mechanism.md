---
date: 2026-07-29T18:08:09+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: 1654822
subject: "[FEATURE] Answer what happens to shipped content after the file exists"
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# SHIPPED CONTENT: THE HINT EXPLAINS THE MECHANISM AND STOPS BEFORE THE LIFECYCLE. Four things I ne...

## Observation

SHIPPED CONTENT: THE HINT EXPLAINS THE MECHANISM AND STOPS BEFORE THE LIFECYCLE. Four things I needed and did not have, in the order they bit.

Context for the value of this: I regenerated Initialisation/data.xml three times in one session and never once imported it. Not out of carelessness — the installation had already imported, so there was nothing I could do locally that would have shown me a result. I verified the artifact by reading the XML and checking that a softref pointed where I expected. That is reasoning, not verification, and the hint gave me no reason to think anything more was available.

1. The hint states the trap and does not draw the consequence. "A changed data file does not reach an installation that already ran it" is exactly right and is the most important sentence in it. What follows from it is not said: the person maintaining that file can never see the effect of their own change on their own installation. The artifact is write-only in practice. So the sentence needs a second half — verify it somewhere it can be imported: a functional test that imports it into a clean instance, or a scratch database.

2. How to re-trigger it at all. The registry entry that guards the import is namespace "extensionDataImport", key "<extension_key>:Initialisation/dataImported" — I found that by querying sys_registry, and knowing the key is what makes extension:setup usable a second time. The hint names the namespace but not the key, and the key is the operative part.

3. The uid problem is stated without its remedy. "The site configuration import remaps the root page id and nothing else" is in the hint and is correct. What to DO about it is not. My project ships errorContentSource: 't3://page?uid=2' in Initialisation/Site/main/config.yaml and will point at a stranger's page 2 on any import into a populated database. The remedy belongs next to the fact: keep uids out of the shipped site configuration, and where that is impossible, treat the shipped config as first-install-only and say so.

4. The rule about which references survive — this is the one I would most want. A reference is carried through the import only where TCA declares it as a relation or produces a soft reference. I verified two by hand in the export: a TCA type=link field came out as a typolink softref with recordRef pages:14, and a type=group to pages inside a FlexForm came out under flexFormRels/db_relations. A page uid in a plain input or number field would ship as a stale integer and nothing would warn. I changed a FlexForm field from type=link to type=group for an unrelated reason — the URI builder wanted a plain uid — and got correct remapping as a lucky side effect. That should not be luck.

One honest gap I could not close and therefore cannot report as fact: how FAL references inside exported records behave. My demo products have no images, so the export contains no sys_file_reference rows and I never exercised it. Whether impexp:export pulls the files in, and whether --save-files-outside-export-file matters for an artifact that is meant to be committed, I do not know — and a sitepackage shipping demo content with images is the normal case, not the exception.</observation>
<parameter name="suggestion">Extend sitepackage-initial-content past the point where the file exists: how to re-trigger the import (the registry key, not only the namespace), that the artifact has to be verified somewhere other than the installation it was exported from — a functional test importing it is the cheap form and the test hints already exist to point at — the rule that only TCA relations and soft references are remapped so uids in plain fields ship stale, and the remedy for uids in the shipped site configuration. If FAL behaviour can be established, that too: it is the first thing a real sitepackage hits after the first demo image.

## Query

typo3_architecture_lookup id=sitepackage-initial-content — the hint covers the mechanism well, including the parts I fed back last round. This is about what happens after the artifact exists.
