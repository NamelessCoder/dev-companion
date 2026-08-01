<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Result;

use Typo3CmsMcp\Scope;

/**
 * The record shapes several tools answer with, and the builders they are
 * written in.
 *
 * A tool declares its own output schema; what lives here is what more than one
 * of them says — a knowledge match, an architecture hint, a catalog entry, the
 * reason an installation could not be asked. Schemas stay open (no
 * additionalProperties: false) so a new field is an addition rather than a
 * break, and only fields that are always present are required.
 */
final class Schema
{
    /**
     * Why an installation-backed answer is unanswered rather than empty.
     *
     * Present exactly when answeredBy is "nothing". The text said this all
     * along; a client that renders structuredContent and drops the text block
     * saw an empty result and nothing else.
     *
     * @return array<string, mixed>
     */
    public static function unavailable(): array
    {
        return self::object([
            'reason' => self::string('What stopped the installation from being asked.'),
            'diagnosis' => self::string('What the reason means where the message alone does not say it — a console that starts and then fails on a missing table has a database without a schema, not a broken installation. Empty where nothing beyond the reason is known.'),
            'settings' => self::object([
                'root' => self::string('Environment variable that names the installation root.'),
                'console' => self::string('Environment variable that names the console command.'),
            ], ['root', 'console']),
        ], ['reason']);
    }

    /**
     * Every answer that comes from the installation says so, because an empty
     * result and an unanswerable question are not the same thing.
     *
     * @return array<string, mixed>
     */
    public static function answeredBy(): array
    {
        return [
            'type' => 'string',
            'enum' => ['installation', 'packages', 'nothing'],
            'description' => 'installation: its assembled runtime state answered. packages: read from the files '
                . 'the installed packages ship, because the console could not be asked — overrides applied at '
                . 'runtime are not reflected. nothing: the installation could not be asked at all, so an empty '
                . 'result is unanswered rather than a miss.',
        ];
    }

    /**
     * Who an answer is for, as the three values that decide it.
     *
     * The third is not a hedge but the case the other two cannot state: signals
     * that disagree with nothing left to resolve them. An answer that picked a
     * side there would be right half the time and say so never.
     *
     * @return array<string, mixed>
     */
    public static function audience(string $description = ''): array
    {
        return [
            'type' => 'string',
            'enum' => [Scope::AUDIENCE_CORE, Scope::AUDIENCE_OUTSIDE, Scope::AUDIENCE_UNCERTAIN],
            'description' => $description === ''
                ? 'Who this answer is for: work on the TYPO3 core itself, work outside it — a project or '
                    . 'third-party extension, where the core\'s own process has no counterpart — or undecided, '
                    . 'which means nothing in the call placed it and what came back is the core\'s own.'
                : $description,
        ];
    }

    /**
     * The same decision per path, because a call is not a path: two files of
     * different audience in one call are two questions.
     *
     * @return array<string, mixed>
     */
    public static function audiences(string $description): array
    {
        return self::listOf(self::object([
            'path' => self::string(),
            'audience' => self::audience(),
        ], ['path', 'audience']), $description);
    }

    /** @return array<string, mixed> */
    public static function knowledgeLookup(): array
    {
        return self::object([
            'query' => self::string(),
            'resource' => self::nullableString('The exact XLF resource the result was restricted to. Null means the caller did not yet provide the usage context.'),
            'matchCount' => self::integer(),
            'matches' => self::listOf(self::knowledgeMatch()),
            'documents' => self::listOf(self::object([
                'id' => self::string(),
                'title' => self::string(),
                'topics' => self::listOf(self::string()),
            ], ['id', 'title', 'topics']), 'Documents in the knowledge base with the topics they cover. Returned when nothing matched.'),
            'elsewhere' => self::listOf(self::string(), 'Documents outside the searched ones that do match the query.'),
            'alsoInHints' => self::listOf(self::object([
                'id' => self::string(),
                'title' => self::string(),
            ], ['id', 'title']), 'Architecture hints matching the same query. They are a second corpus, searched by typo3_architecture_lookup, which takes one of these ids.'),
        ], ['query', 'matchCount', 'matches']);
    }

    /** @return array<string, mixed> */
    public static function knowledgeMatch(): array
    {
        return self::object([
            'documentId' => self::string(),
            'title' => self::string('Title of the knowledge document.'),
            'uri' => self::string('typo3://core resource holding the full document.'),
            'heading' => self::string('Heading of the matched section.'),
            'body' => self::string('The section as written, formatting included.'),
            'coverage' => ['type' => 'number', 'description' => 'Share of the query terms the section covers, 0 to 1.'],
            'score' => self::integer('Weighted match score; headings weigh more than body text.'),
            'truncated' => ['type' => 'boolean', 'description' => 'Whether the body was cut; read the resource for the rest.'],
        ], ['documentId', 'title', 'uri', 'heading', 'body', 'coverage', 'score', 'truncated']);
    }

    /**
     * Who is obliged by something, where that is not everyone.
     *
     * @return array<string, mixed>
     */
    public static function binding(string $subject): array
    {
        return [
            'type' => ['string', 'null'],
            'enum' => ['core', null],
            'description' => sprintf(
                'Who %s obliges. "core" means it is a condition of a patch to the TYPO3 core and a convention '
                . 'anywhere else — the backend\'s own design system, the changelog artifact, the paths of the mono '
                . 'repository. Null, the ordinary case, means it holds wherever TYPO3 is written: an API that '
                . 'throws throws in a sitepackage too.',
                $subject,
            ),
        ];
    }

    /** @return array<string, mixed> */
    public static function architectureHintRecord(): array
    {
        return self::object([
            'id' => self::string(),
            'title' => self::string(),
            'category' => self::string('PHP, TypeScript, JavaScript, CSS, or General.'),
            'binding' => self::binding('the whole hint'),
            'hints' => self::listOf(self::object([
                'text' => self::string('The statement itself. It reads the same on every version it holds for; the range is beside it, never inside it.'),
                'since' => ['type' => ['integer', 'null'], 'description' => 'First TYPO3 major this holds on. Null means as far back as this knowledge base reaches.'],
                'until' => ['type' => ['integer', 'null'], 'description' => 'Last TYPO3 major this holds on. Null means it still holds.'],
                'versions' => self::string('The same range as a sentence, empty when the statement is bound to nothing.'),
                'binding' => self::binding('this statement'),
            ], ['text', 'since', 'until', 'versions', 'binding'])),
            'checks' => self::listOf(self::string(), 'Commands relevant to this hint.'),
        ], ['id', 'title', 'category', 'binding', 'hints', 'checks']);
    }

    /** @return array<string, mixed> */
    public static function testSuiteRecord(): array
    {
        return self::object([
            'suite' => self::string(),
            'command' => self::string('Full command, run from the core root.'),
            'targeted' => self::nullableString('Narrowed form for iterating on a single file or test.'),
            'description' => self::string(),
            'whenToUse' => self::string(),
            'domains' => self::listOf(self::string()),
            'versions' => self::nullableString('The TYPO3 majors whose runTests.sh has this suite, where that is not all of them. Null means every covered version.'),
        ], ['suite', 'command', 'targeted', 'versions']);
    }

    /**
     * The majors a catalog entry was verified on — the same since/until the
     * architecture hints carry, so a client reads one model rather than two.
     *
     * @return array<string, mixed>
     */
    public static function verifiedOn(): array
    {
        return [
            'since' => ['type' => ['integer', 'null'], 'description' => 'The TYPO3 major this entry starts holding at, or null when it holds on every covered version.'],
            'until' => ['type' => ['integer', 'null'], 'description' => 'The TYPO3 major it stops holding after, or null when nothing has replaced it.'],
            'verifiedOn' => self::string('The same range as a sentence, empty when the entry holds on every covered version.'),
        ];
    }

    /** @return array<string, mixed> */
    public static function withheldComponents(): array
    {
        return self::listOf(self::object([
            'name' => self::string(),
            'title' => self::string(),
            'sassPaths' => self::listOf(self::string(), 'What to verify the entry against on the target version.'),
            'demoPath' => self::nullableString(),
        ] + self::verifiedOn(), ['name', 'title', 'verifiedOn']), 'Components this catalog has but was never verified on the target version. Left out of components rather than handed over — an empty answer here means "not verified where you are", not "does not exist".');
    }

    /** @return array<string, mixed> */
    public static function catalogProvenance(): array
    {
        return self::object([
            'repository' => self::string(),
            'branch' => self::string(),
            'version' => self::string('TYPO3 version of the snapshot.'),
            'commit' => self::string('Core revision the catalogs were taken from.'),
            'verifiedAt' => self::string(),
            'installedVersion' => self::nullableString('TYPO3 version of the installation this server was started in, where there is one. Null means there was nothing to compare the snapshot with.'),
            'skew' => self::nullableString('Set when that installation and the snapshot are different TYPO3 majors, and what to do about it. Null when they agree or nothing is known.'),
        ], ['branch', 'version', 'commit', 'verifiedAt'], 'The core revision behind catalog answers, and how it relates to the installation being read. A miss means "not in this snapshot".');
    }

    /**
     * @param array<string, mixed> $properties
     * @param array<int, string> $required
     * @return array<string, mixed>
     */
    public static function object(array $properties, array $required = [], string $description = ''): array
    {
        $schema = ['type' => 'object'];
        if ($properties !== []) {
            $schema['properties'] = $properties;
        }
        if ($required !== []) {
            $schema['required'] = $required;
        }
        if ($description !== '') {
            $schema['description'] = $description;
        }

        return $schema;
    }

    /**
     * @param array<string, mixed> $items
     * @return array<string, mixed>
     */
    public static function listOf(array $items, string $description = ''): array
    {
        $schema = ['type' => 'array', 'items' => $items];
        if ($description !== '') {
            $schema['description'] = $description;
        }

        return $schema;
    }

    /** @return array<string, mixed> */
    public static function string(string $description = ''): array
    {
        return $description === '' ? ['type' => 'string'] : ['type' => 'string', 'description' => $description];
    }

    /** @return array<string, mixed> */
    public static function nullableString(string $description = ''): array
    {
        $schema = ['type' => ['string', 'null']];
        if ($description !== '') {
            $schema['description'] = $description;
        }

        return $schema;
    }

    /** @return array<string, mixed> */
    public static function integer(string $description = ''): array
    {
        return $description === '' ? ['type' => 'integer'] : ['type' => 'integer', 'description' => $description];
    }
}
