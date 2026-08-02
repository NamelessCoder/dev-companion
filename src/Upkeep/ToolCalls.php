<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

/**
 * Every tool driven once on a hit and once on a miss, as arguments.
 *
 * Two things read this and they need the same table. `ToolContractTest` drives
 * it to hold every answer to the schema its tool declares, on both paths, and
 * `bin/cli tools:record` drives it to write down what a filled answer looks
 * like — which is the half no schema shows. A second table would drift from the
 * first, and the recording would then illustrate calls nothing validates.
 *
 * It lives in `Upkeep` rather than in `tests/` because a command may not depend
 * on a test class, and because that is what this is: neither the server nor its
 * knowledge, but the apparatus this repository is kept in order by.
 *
 * Two tools are deliberately absent. `typo3_feedback_record` writes, and a
 * table driven by two callers has to be safe to drive; `typo3_feedback_list`
 * answers with prose somebody else wrote, so a truncated title in a feedback
 * — `typo3_t…` is in one of them — reads to the name check as a tool that does
 * not exist.
 */
final class ToolCalls
{
    /**
     * The calls, keyed by what each one is an example of.
     *
     * An installation-backed tool answers from whatever the caller is standing
     * in: nothing in a test run, so the entry exercises the unsupported path
     * there, and the packages of a core checkout when `tools:record` is pointed
     * at one. Both are worth seeing and neither is named in the key, because
     * the key describes the call rather than the answer.
     *
     * @return array<string, array{0: string, 1: array<string, mixed>}>
     */
    public static function all(): array
    {
        return [
            'scope' => ['typo3_server_scope', []],
            'rules: hit' => ['typo3_rule_lookup', ['query' => 'deprecation']],
            'rules: miss' => ['typo3_rule_lookup', ['query' => 'quantum entanglement pineapple']],
            'scripts: hit' => ['typo3_script_lookup', ['task' => 'functional tests']],
            'scripts: miss' => ['typo3_script_lookup', ['task' => 'quantum entanglement pineapple']],
            'brief: with area' => ['typo3_task_guide', [
                'task' => 'Deprecate a public method',
                'area' => 'typo3/sysext/core/Classes/Utility/GeneralUtility.php',
                'changeType' => 'cleanup',
            ]],
            'brief: task only' => ['typo3_task_guide', ['task' => 'Add a badge to the list module']],
            'brief: paths of two kinds' => ['typo3_task_guide', [
                'task' => 'Fix the query that reads the events',
                'paths' => [
                    'packages/acme_events/Classes/Domain/Repository/EventRepository.php',
                    'typo3/sysext/core/Classes/Database/Query/QueryBuilder.php',
                ],
                'changeType' => 'bugfix',
            ]],
            'runTests: all' => ['typo3_test_run_guide', []],
            'runTests: hit' => ['typo3_test_run_guide', ['query' => 'phpstan']],
            'runTests: miss' => ['typo3_test_run_guide', ['query' => 'quantumflux']],
            'runTests: narrowed by paths' => ['typo3_test_run_guide', [
                'query' => 'what do I have to run',
                'paths' => ['Build/Sources/Sass/component/_card.scss'],
            ]],
            'architecture: path' => ['typo3_architecture_lookup', ['paths' => ['typo3/sysext/core/Classes/DataHandling/DataHandler.php']]],
            'architecture: topic' => ['typo3_architecture_lookup', ['task' => 'sass build']],
            'architecture: miss' => ['typo3_architecture_lookup', ['task' => 'quantumflux']],
            'documentation: unsupported version' => ['typo3_documentation_lookup', [
                'queries' => ['page title event'],
                'targetVersion' => '999',
            ]],
            'components: list' => ['typo3_component_lookup', []],
            'components: hit' => ['typo3_component_lookup', ['query' => 'badge']],
            'components: miss' => ['typo3_component_lookup', ['query' => 'quantumflux']],
            'system extensions: hit' => ['typo3_system_extension_lookup', ['query' => 'impexp']],
            'system extensions: miss' => ['typo3_system_extension_lookup', ['query' => 'typo3/cms-content-blocks']],
            'system extensions: everything' => ['typo3_system_extension_lookup', []],
            'references' => ['typo3_reference_list', []],
            'domain: EXT reference' => ['typo3_translation_domain_lookup', [
                'path' => 'EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf',
            ]],
            'domain: checkout path' => ['typo3_translation_domain_lookup', [
                'path' => 'typo3/sysext/core/Resources/Private/Language/locallang.xlf',
            ]],
            'domain: on an older target' => ['typo3_translation_domain_lookup', [
                'path' => 'EXT:backend/Resources/Private/Language/locallang_alt_doc.xlf',
                'targetVersion' => '13.4',
            ]],
            'domain: miss' => ['typo3_translation_domain_lookup', ['path' => 'somewhere/else.xlf']],
            'labels: hit' => ['typo3_label_lookup', ['query' => 'save']],
            'labels: miss' => ['typo3_label_lookup', ['query' => 'quantumflux']],
            'icons: hit' => ['typo3_icon_lookup', ['query' => 'actions-open']],
            'icons: everything' => ['typo3_icon_lookup', []],
            'modules' => ['typo3_backend_module_lookup', []],
            'namespaces' => ['typo3_fluid_namespace_list', []],
            'configuration' => ['typo3_configuration_lookup', ['path' => 'SYS/fluid']],
            'schema: one table' => ['typo3_schema_lookup', ['table' => 'tt_content']],
            'schema: every table' => ['typo3_schema_lookup', []],
            'changelog: hit' => ['typo3_changelog_lookup', ['query' => 'ext_tables.php']],
            'changelog: miss' => ['typo3_changelog_lookup', ['query' => 'quantumflux']],
            'project' => ['typo3_project_scope', []],
            'extension' => ['typo3_extension_scope', ['extension' => 'backend']],
            'catalog scope' => ['typo3_catalog_scope', []],
            'commit: from parts' => ['typo3_commit_message_guide', [
                'changeType' => 'BUGFIX',
                'summary' => 'Show hidden records in the import preview',
                'issue' => '106123',
            ]],
            'commit: from a message' => ['typo3_commit_message_guide', [
                'message' => "[TASK] Do a thing\n\nBody.\n\nResolves: #1\nReleases: main",
            ]],
        ];
    }
}
