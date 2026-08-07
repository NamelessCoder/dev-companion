---
date: 2026-08-07T13:24:26+00:00
category: wrong-answer
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# Extbase persistence paths return FAL storage hints while the persistence hints are withheld

## Observation

Task: review a local TYPO3 core commit changing Extbase null/empty date handling in Typo3DbQueryParser, Backend, ColumnMap and DataMapper.

The call returned three hint groups: datahandler-basics, system-extension-boundaries and fal-storages-drivers. The third is about FAL — StorageRepository::findByUid(), DriverInterface, storage uid 0 and the System Resource API. Nothing in it touches Extbase persistence, and none of the three paths I passed is under Resources/ or anywhere near FAL.

The two hints that were on topic were both present in availableHints and not returned: persistence-reading ("Reading Records, and What Is Hidden From the Query") and extbase-domain-mapping ("Models, Repositories and the Table Behind Them"). The first in particular is exactly the subject of the diff under review — how a query finds or fails to find rows.

An earlier typo3_task_guide call with the same paths had listed fal-storages-drivers too, there under omittedHints, so the same match is being made in both places.

The likely mechanism: the word "storage" in my task text and in the path segment Persistence/Generic/Storage/ matched the FAL sense of the word. This is the one call in the prescribed order whose job is to give me the subsystem conventions before I form a view of the code, and it gave me conventions for a different subsystem. It cost me nothing directly, because I fell back to reading the four classes and their callers, but the safeguard did not fire.

## Query

typo3_hint_lookup task="Extbase persistence, query parser, data mapper, column map, writing and reading records" paths=["typo3/sysext/extbase/Classes/Persistence/Generic/Storage/Typo3DbQueryParser.php","typo3/sysext/extbase/Classes/Persistence/Generic/Mapper/ColumnMap.php","typo3/sysext/extbase/Classes/Persistence/Generic/Backend.php"] limit=8

## Suggestion

Weight the path segments above the free-text task, or disambiguate "storage" by context: a path under typo3/sysext/extbase/Classes/Persistence/ is the Extbase persistence layer whatever the word after Generic/ is, and should reach persistence-reading and extbase-domain-mapping before anything FAL. A cheap regression check: typo3_hint_lookup with any path under typo3/sysext/extbase/Classes/Persistence/ should not return fal-storages-drivers, and should return persistence-reading.
