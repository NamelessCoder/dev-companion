<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Result;

use Typo3CmsMcp\Installation\Project;
use Typo3CmsMcp\Knowledge\Versions;

/**
 * Which TYPO3 versions an answer was selected for, and why.
 *
 * The task guide and the architecture lookup both filter by version and both
 * have to say what that filtering cost, so the sentence is written once.
 */
final class VersionScope
{
    /**
     * The interesting case is the one this said nothing about for a long time:
     * a repository declaring `^13.4 || ^14.3` gets both majors, and a caller
     * that does not know this reads a statement labelled for one of them as the
     * current shape and the other as drift. It is the difference between the
     * two that the code is built around — the file kept for the older major,
     * the interface not replaced yet — so the sentence names it as a constraint
     * rather than leaving it to be discovered.
     *
     * @param array<int, int> $targets
     */
    public static function line(array $targets): string
    {
        if ($targets === []) {
            return 'No target TYPO3 version was stated and none was found to read, so every statement comes back '
                . 'with the versions it holds for. Pass targetVersion to have the ones that do not apply left out.';
        }

        $constraint = Project::coreConstraint();
        $declared = self::severalDeclared();

        // The narrowing is invisible from inside the answer, and that is how a
        // widened default gets switched off by a caller being careful: a
        // session reads the installed version out of typo3_project_scope,
        // states it because it looks like the accurate thing to do, and gets
        // back exactly the answer this filtering was changed to stop giving.
        // So the one case where the two disagree says so.
        if (count($targets) === 1) {
            if ($declared === []) {
                return sprintf('Answered for TYPO3 v%d: statements that do not hold there are left out.', $targets[0]);
            }

            return sprintf(
                'Answered for TYPO3 v%d alone, because targetVersion stated it. This repository declares '
                . 'typo3/cms-core as %s, so one codebase serves %s here, and every statement that holds only on '
                . '%s is missing from this answer — on a repository like this one those are not somebody else\'s '
                . 'rules, they are the constraint this code lives under. Leave targetVersion out to be answered '
                . 'for all of them at once.',
                $targets[0],
                $constraint === null ? 'a range' : '"' . $constraint . '"',
                self::majorList($declared),
                self::majorList(array_values(array_diff($declared, $targets))),
            );
        }

        return sprintf(
            'Answered for TYPO3 %s at once, because this repository declares typo3/cms-core as %s and one codebase '
            . 'serves all of them. A statement is kept when it holds on any of them, and the range beside it says '
            . 'which — where two statements about the same subject differ, that difference is the constraint this '
            . 'code lives under rather than something to clean up. Pass targetVersion to answer for one of them.',
            self::majorList($targets),
            $constraint === null ? 'a range' : '"' . $constraint . '"',
        );
    }

    /**
     * The majors this repository declares, where it declares more than one.
     *
     * Empty is the ordinary case and covers both shapes that behave alike: a
     * repository built for a single major, and one whose constraint nothing
     * here can read. Neither has an answer that could have been wider.
     *
     * @return array<int, int>
     */
    public static function severalDeclared(): array
    {
        $declared = Versions::declared(Project::coreConstraint());

        return count($declared) > 1 ? $declared : [];
    }

    /** @param array<int, int> $majors */
    private static function majorList(array $majors): string
    {
        $labels = array_map(static fn(int $major): string => 'v' . $major, $majors);
        $last = array_pop($labels);

        return $labels === [] ? $last : implode(', ', $labels) . ' and ' . $last;
    }
}
