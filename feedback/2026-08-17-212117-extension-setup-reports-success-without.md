---
date: 2026-08-17T21:21:17+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/site-demo
---

# extension:setup reports success without creating tables from TCA added since the last cache flush

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. Two new child tables declared purely in TCA, with no ext_tables.sql — which is correct on v14 and which the extension-schema-sql hint and typo3_schema_lookup between them confirmed.

I wrote Configuration/TCA/tx_sitepackage_teaser_card.php and tx_sitepackage_accordion_item.php, then ran `typo3 extension:setup`. It answered "[OK] Extension(s) ... successfully set up." for the full list including my two. typo3_schema_lookup then described tx_sitepackage_teaser_card in full — 23 derived columns, exactly as expected. Everything said the tables were there.

They were not. The seeding script died on "An exception occurred while executing a query: Table 'db.tx_sitepackage_teaser_card' doesn't exist". `cache:flush` followed by a second `extension:setup` created both tables, and the same command then reported the same success message it had reported the first time.

So the schema step ran against a TCA that did not yet include my files, and reported success anyway. Two round trips to diagnose, which is cheap — but the diagnosis was only cheap because the failure was loud. The dangerous shape is the near miss: had I not seeded content immediately, I would have gone on believing the setup had succeeded, and the missing tables would have surfaced later.

Worth separating two things the session taught me here, because they look alike and only one is documented. typo3_schema_lookup's own description is honest that it "describes what TYPO3 would create, never what the database currently has" — I read that and still took its answer as evidence the tables existed, which is my error. What is not documented anywhere is the other half: that `typo3 extension:setup`, whose help text is "Set up extensions and perform database migrations", performs those migrations from a cached TCA and reports success regardless.

I also note, without having established it, that this may be specific to running the command twice in one session against a warm cache rather than to a fresh install — the clean-clone rebuilds later in the session created both tables in one pass with no flush needed.

## Query

Add a new Configuration/TCA/tx_myext_thing.php to an installed extension, then run `typo3 extension:setup` without flushing caches, on TYPO3 14.3.6

## Suggestion

Say it beside the schema knowledge, where somebody adding a table will be looking: after adding or changing a TCA file on a running installation, flush caches before `extension:setup`, because the migration runs against the cached TCA and the command reports success either way. The check is worth naming too, since success is not evidence — ask the database, not the command, and note that typo3_schema_lookup will not settle it because it answers what TYPO3 would create rather than what exists. extension-schema-sql is the natural home, as it is the hint a caller reads while deciding they need no ext_tables.sql, which is exactly the moment before this bites.
