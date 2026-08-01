<?php

declare(strict_types=1);

namespace Typo3CmsMcp\Upkeep;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Typo3CmsMcp\Upkeep\Command\BacklogList;
use Typo3CmsMcp\Upkeep\Command\CatalogCheck;
use Typo3CmsMcp\Upkeep\Command\CatalogPaths;
use Typo3CmsMcp\Upkeep\Command\CheckoutStatus;
use Typo3CmsMcp\Upkeep\Command\CheckoutUpdate;
use Typo3CmsMcp\Upkeep\Command\DecisionCheck;
use Typo3CmsMcp\Upkeep\Command\DecisionIndex;
use Typo3CmsMcp\Upkeep\Command\DecisionList;
use Typo3CmsMcp\Upkeep\Command\FeedbackArchive;
use Typo3CmsMcp\Upkeep\Command\FeedbackList;
use Typo3CmsMcp\Upkeep\Command\FeedbackNext;
use Typo3CmsMcp\Upkeep\Command\HintCoverage;
use Typo3CmsMcp\Upkeep\Command\HintProbe;
use Typo3CmsMcp\Upkeep\Command\ProseCheck;
use Typo3CmsMcp\Upkeep\Command\RepositoryCheck;
use Typo3CmsMcp\Upkeep\Command\RequirementCheck;
use Typo3CmsMcp\Upkeep\Command\RequirementIndex;
use Typo3CmsMcp\Upkeep\Command\RequirementList;
use Typo3CmsMcp\Upkeep\Command\ScenarioCheck;
use Typo3CmsMcp\Upkeep\Command\ScenarioContract;
use Typo3CmsMcp\Upkeep\Command\ScenarioRecord;
use Typo3CmsMcp\Upkeep\Command\ScenarioShow;
use Typo3CmsMcp\Upkeep\Command\TodoCheck;
use Typo3CmsMcp\Upkeep\Command\TodoList;
use Typo3CmsMcp\Upkeep\Command\TodoNext;
use Typo3CmsMcp\Upkeep\Command\TodoWaiting;

/**
 * Everything this repository is kept in order by, as one console application.
 *
 * What this replaces is six scripts below bin/, each with a usage line of its
 * own. Nothing said what the set of them was: a command existed for whoever had
 * listed the directory, and the only overview was a block in AGENTS.md that no
 * code read and nothing held to the truth. The list below is that set, and it is
 * the only place a command is switched on — everything a caller is shown, from
 * `bin/cli list` down to the arguments of one command, the console reads off the
 * classes registered here.
 *
 * The console is `symfony/console`, and it is a dev dependency: `bin/cli` is the
 * upkeep of this checkout and Composer exports it as no `bin`, so what it needs
 * is not what an installation of this package needs.
 *
 * `bin/typo3-cms-mcp` is deliberately not here. That one is the product — the
 * client launches it, Composer exports it as a `bin`, and it has no business
 * carrying the upkeep of the repository it happens to live in.
 */
final class Cli
{
    /**
     * The sentence at the head of the command list. It is the application's
     * name because that is where the console prints it, and there is no second
     * place to say what this command is for.
     */
    private const ABOUT = 'The upkeep of this repository. bin/typo3-cms-mcp is the server itself.';

    private static ?Application $application = null;

    /**
     * Every command there is, on the application `bin/cli` runs and that a
     * todo's own `Run:` lines are dispatched through.
     */
    public static function application(): Application
    {
        if (self::$application instanceof Application) {
            return self::$application;
        }

        $application = new Application(self::ABOUT);
        // The exit code belongs to the caller: `bin/cli` hands it to exit(),
        // and `todo:next` reads it to decide whether a todo still has work.
        $application->setAutoExit(false);

        $application->addCommand(new RequirementList());
        $application->addCommand(new RequirementCheck());
        $application->addCommand(new RequirementIndex());
        $application->addCommand(new DecisionList());
        $application->addCommand(new DecisionCheck());
        $application->addCommand(new DecisionIndex());
        $application->addCommand(new ScenarioShow());
        $application->addCommand(new ScenarioContract());
        $application->addCommand(new ScenarioRecord());
        $application->addCommand(new ScenarioCheck());
        $application->addCommand(new TodoNext());
        $application->addCommand(new TodoList());
        $application->addCommand(new TodoWaiting());
        $application->addCommand(new TodoCheck());
        $application->addCommand(new ProseCheck());
        $application->addCommand(new FeedbackNext());
        $application->addCommand(new FeedbackList());
        $application->addCommand(new FeedbackArchive());
        $application->addCommand(new BacklogList());
        $application->addCommand(new HintProbe());
        $application->addCommand(new HintCoverage());
        $application->addCommand(new CatalogCheck());
        $application->addCommand(new CatalogPaths());
        $application->addCommand(new CheckoutUpdate());
        $application->addCommand(new CheckoutStatus());
        $application->addCommand(new RepositoryCheck());

        return self::$application = $application;
    }

    /**
     * Whether `bin/cli …` names something this command can do, for the todos,
     * whose `Run:` lines are commands nobody runs until the day they are due.
     */
    public static function knows(string $command): bool
    {
        $words = explode(' ', $command);
        if (array_shift($words) !== 'bin/cli') {
            return true;
        }

        return $words !== [] && self::application()->has($words[0]);
    }

    /**
     * Where a command writes what went wrong, which is not where it writes its
     * answer. A check prints its problems here and its count on stdout, so the
     * count survives a pipe that the problems are read out of.
     */
    public static function errors(OutputInterface $output): OutputInterface
    {
        return $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
    }
}
