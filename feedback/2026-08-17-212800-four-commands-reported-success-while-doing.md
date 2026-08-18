---
date: 2026-08-17T21:28:00+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/site-demo
---

# four commands reported success while doing nothing in one session, so a procedural hint owes the ...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. The user asked whether the hints help or whether I need a different format. They help — 12 of 21 were decisive, and the whole knowledge base delivered a complete project build in 12,101 tokens of payload. The prose form is right and I would defend it: what made me act was consistently a fact with its consequence attached in the same sentence. sitepackage-templates saying a shared layout root "renders the site header and footer inside every single content element — on a page that still comes back without an error" is the single most useful sentence I read all session, and it is useful precisely because it is prose carrying a failure mode, not a table.

What the format is missing is not a replacement. It is one more line per procedural hint: how you know it worked.

Four times in this session a command reported success and had done nothing, or nothing I wanted:

- typo3 impexp:export answered "[OK] Exporting to ... succeeded" having shipped no image bytes at all, because --include-related=sys_file was absent. The hint said images come along on their own.
- The same command answered OK against a path I had not given it, silently ignoring the EXT: filename the same hint recommends.
- typo3 extension:setup answered "successfully set up" for my two new tables without creating them, having migrated from a cached TCA.
- DataHandler::process_datamap() left errorLog empty and substNEWwithIDs fully populated while discarding every inline relation, because my NEW placeholders contained underscores.

In this domain a success message is not evidence, and that is a property of the domain worth encoding rather than a run of bad luck. Each of those cost between two and twelve round trips, and together they are most of the nine debugging cycles in the session.

The check is usually one line and I had to invent all four. For the export it is: a correct artifact has a files_fal section beside header and records, and its .files directory is not empty — that single sentence would have caught the hint's own incorrect claim before I shipped a distribution with no images. For extension:setup it is: ask the database, not the command. For the datamap it is: the parent's relation column holds the child count, so read it back.

One pairing already does this well and is worth naming as the model: extension-schema-sql says ext_tables.sql carries only the delta, and typo3_schema_lookup answers what TYPO3 derives — so the claim and the way to verify it are both available, and its own description is scrupulous that it describes what would be created rather than what exists. That is the shape. It just is not applied to the procedural hints, which are exactly the ones where an unverified success is expensive.

## Query

typo3_hint_lookup on any hint that prescribes a console command — then run the procedure it describes and compare what the command reports against what actually happened.

## Suggestion

Give every hint that tells a caller to run something a closing line naming what a correct result looks like, distinct from what the command prints. Not a new format — one sentence inside the existing prose, in the same voice as the consequences the best hints already carry. Where a lookup on this server can perform the check, name it there — schema_lookup for the derived columns is the existing example. The general rule this session earned: where a TYPO3 command can report success without having acted, the hint that recommends it owes the reader the discriminator.

## What is left of this

Trimmed on 2026-08-18. The four sightings are answered, each by its own card:
`D-KNW-080` corrected both impexp sentences and `impexp-artifact` now closes on
the files_fal check this feedback proposed, `D-KNW-081` answered the `NEW`
placeholder and `datahandler-relations` states what the parent's counter holds,
`D-KNW-089` answered the warm TCA cache and `extension-schema-sql` now says its
success is not evidence. `datahandler-seeding` and `installation-boot` are
decided against as homes, in `D-KNW-093` and in `D-KNW-089`.

What is left is the general rule and the corpus it has not been applied to. Two
hints carry a discriminator and eleven console commands are prescribed
elsewhere; `D-KNW-093` is the judgement and the card serving this feedback is
the sweep.
