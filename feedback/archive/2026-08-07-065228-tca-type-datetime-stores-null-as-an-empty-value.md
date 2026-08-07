---
date: 2026-08-07T06:52:28+00:00
category: missing-knowledge
status: closed
closed: 2026-08-07
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_task_guide, typo3_documentation_lookup, typo3_changelog_lookup
directory: /home/benji/projects/typo3-cms
---

# TCA type=datetime stores null as an empty value and no lookup here says so

## Observation

Task: verify Forge issue 109572 ("Extbase Repository Query cannot query for is null") against a 15.0.0-dev core checkout, then write the patch for it.

The whole issue turned on a fact no lookup on this server carries. A TCA type=datetime column that is not nullable never holds SQL NULL: it stores 0 for an integer column, or the "0000-00-00 00:00:00" literal for a native one. DateTimeFactory::fromDatabase() maps that value back to null, so a mapped Extbase object reports the property as null while the row is not NULL in the database. I had to establish this by reading core source across many turns: typo3/sysext/core/Classes/Domain/DateTimeFactory.php:99-116, typo3/sysext/core/Classes/Schema/Field/DateTimeFieldType.php:33-39 (nullable defaults to false for an integer column and true for a dbType column), QueryHelper::transformDateTimeToDatabaseValue(), and typo3/sysext/extbase/Classes/Persistence/Generic/Backend.php:559 where insertObject() omits a null property from the INSERT so the schema default decides what is stored.

Not having it cost a wrong verdict that reached the user. I first reported the non-nullable case as correct behaviour and filed the issue as "not reproducible as written", telling the user the reporter was probably right about the symptom but wrong about everything else. The user pushed back with "i am still not getting why this is a problem" and only then did I find the actual inconsistency: the object says null, the query cannot find the row. The defect was real and I had dismissed it.

## Query

typo3_task_guide(task: "Verify a bug report claiming an Extbase repository query cannot filter for IS NULL / IS NOT NULL on a nullable date field", paths: ["typo3/sysext/extbase/Classes/Persistence/Generic/Storage/Typo3DbQueryParser.php", "typo3/sysext/extbase/Classes/Persistence/Generic/Query.php", "typo3/sysext/extbase/Classes/Persistence/QueryInterface.php"], changeType: "audit", targetVersion: "15"); typo3_changelog_lookup(query: "Extbase query null"); typo3_changelog_lookup(query: "extbase null"); typo3_documentation_lookup(queries: ["Extbase query equals null", "nullable datetime TCA"], targetVersion: "13.4")

## Suggestion

A hint under the Extbase persistence subsystem stating, in these terms: a TCA type=datetime column that is not nullable never holds SQL NULL; the empty value is 0 for an integer column and the "0000-00-00..." literal for a native one; DateTimeFactory maps it back to null on read, so a property reading null does not mean the column is NULL; "nullable" defaults to false for an integer column and true for a dbType column. Naming the four implementing places (DateTimeFactory::fromDatabase, DateTimeFieldType::isNullable, QueryHelper::transformDateTimeToDatabaseValue, Backend::insertObject/updateObject) would have replaced the entire reading and prevented a wrong verdict being handed to the user.
