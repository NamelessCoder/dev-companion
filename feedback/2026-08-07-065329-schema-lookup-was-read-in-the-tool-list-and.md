---
date: 2026-08-07T06:53:29+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_schema_lookup
directory: /home/benji/projects/typo3-cms
---

# schema_lookup was read in the tool list and passed over because its name promised another answer

## Observation

Task: verify Forge 109572 and then prove the fix across every storage variant of a TCA type=datetime column, on three databases.

For a large part of the session the live question was a DDL one: what SQL column does TCA type=datetime produce with and without dbType, is it nullable, and what default does it carry. typo3_schema_lookup sat in my deferred tool list the whole time and I never fetched its schema. From the name alone I assumed it was about the TCA schema of the installation's tables — what fields a table has — rather than about the SQL column a TCA type generates and its nullability.

So I answered it two other ways instead: by reading typo3/sysext/core/Classes/Schema/Field/DateTimeFieldType.php, and by building a fixture table with six datetime column variants and measuring what actually got created and stored on sqlite, mariadb and postgres. That measurement is how I discovered that a native column with an explicit nullable=false cannot be inserted at all by Extbase on any of the three.

I never tested the assumption, so I cannot say whether the tool would have answered. That is precisely the finding: from its name and description I did not expect it to, and a name is what a client installed months ago still calls it by.

## Query

not called. The questions taken elsewhere: "does TCA type=datetime with dbType=datetime generate a nullable column", "what default does a non-nullable type=datetime column carry", "is a native DATE column with nullable=false writable" — answered by reading DateTimeFieldType and DefaultTcaSchema behaviour, and by creating tx_blogexample_domain_model_datetimestorage with six variants and inspecting the result per DBMS.

## Suggestion

If typo3_schema_lookup does answer what column a TCA type generates, whether it is nullable and what default it carries, say so in the description in those words — that is the question a patch author actually holds, and it is not the question "schema lookup" suggests. If it does not answer it, that is a genuine tool gap worth naming: nothing on this server covers the DDL side of a TCA type, and it took me a purpose-built fixture table across three databases to get it.
