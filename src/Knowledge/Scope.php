<?php

declare(strict_types=1);

namespace TYPO3\DevCompanion\Knowledge;

use TYPO3\DevCompanion\Installation\Instance;

/**
 * Which of the three kinds of work a path, or a statement about one, belongs to.
 *
 * One vocabulary for the whole server. It used to be four that nobody could
 * line up: `binding` on the hints, `provenance` on the covered topics,
 * `audience` on the answers, and an `outsideCore` boolean beside it — the first
 * three overlapping on `core` and disagreeing everywhere else, the fourth
 * saying only what the work is not.
 *
 * `Any` and `Uncertain` are the two that belong to one side each. A statement
 * can hold wherever TYPO3 is written; a path cannot, because a path is one
 * piece of work. A path can be one nothing placed, which R-AUD-002 asks to be
 * said rather than decided silently; a statement nobody could place is a
 * statement nobody should have written.
 */
enum Scope: string
{
    /** The TYPO3 core repository: its contribution process, scripts and sysext code. */
    case Core = 'core';

    /** The site repository around an installation, outside any package in it. */
    case Project = 'project';

    /** A package: a sitepackage, a project's own extension, or a third-party one. */
    case Extension = 'extension';

    /** Holds wherever TYPO3 is written. Statements only. */
    case Any = 'any';

    /** Nothing in the call placed the work. Paths only. */
    case Uncertain = 'uncertain';

    /**
     * Paths and phrases that place a task in an extension.
     *
     * typo3conf/ext and vendor are where an installation's extensions live,
     * packages and extensions are where a project keeps its own. Every phrase
     * here names an extension: a sitepackage is one, and so is the package a
     * project wrote for itself.
     *
     * @var array<int, string>
     */
    private const EXTENSION_WORK = [
        'packages/', 'typo3conf/ext/', 'vendor/', 'extensions/',
        'project extension', 'site package', 'sitepackage', 'custom extension',
        'third-party extension', 'own extension',
    ];

    /**
     * Paths that belong to the site repository itself rather than to any
     * package inside it.
     *
     * What a project has and an extension does not: the site configuration, the
     * document root, the writable directories, the container setup. A path here
     * is project work even where the session has no installation to read.
     *
     * @var array<int, string>
     */
    private const PROJECT_WORK = [
        'config/sites/', 'config/system/', 'config/services.yaml',
        'public/', 'var/', '.ddev/',
    ];

    /**
     * Paths and phrases that place a task inside core contribution.
     *
     * Positive evidence, and deliberately narrow: it decides whether the core's
     * own contribution process may be stated as applying, and the absence of an
     * extension or project marker is not evidence of anything.
     *
     * @var array<int, string>
     */
    private const CORE_WORK = [
        'typo3/sysext/', 'gerrit', 'change-id', 'review.typo3.org', 'forge.typo3.org',
        'typo3 core', 'core patch', 'core contribution',
        // The work that ends before a patch names the core as a tracker and a
        // checkout rather than as a patch. "Triage an old open core bug report"
        // carried none of the three above, so a triage in a checkout
        // `typo3_project_describe` had just called `core-checkout` was answered
        // with the extension side of every intent it matched (`D-SKL-023`).
        'core issue', 'core bug', 'core checkout', 'core backlog', 'core tracker',
        // The tracker as people name it: "Forge 15984", not the host. The
        // trailing space is what keeps "forget" and "forgetting" out, which are
        // the only other forms of the word this repository's own corpus
        // carries — measured on 2026-08-08 across knowledge/, skills/,
        // scenarios/ and src/.
        'forge ',
    ];

    /**
     * Directories an extension is laid out in. A path that begins with one of
     * them is inside a package, and no core file is ever named that way from
     * the core root: there everything is below typo3/sysext/<key>/ or Build/.
     *
     * @var array<int, string>
     */
    private const EXTENSION_LAYOUT = [
        'classes/', 'configuration/', 'resources/',
        'ext_localconf.php', 'ext_tables.php', 'ext_tables.sql', 'ext_emconf.php',
    ];

    /**
     * The other half of that sentence, and only the half that holds. From the
     * core root everything not below typo3/sysext/<key>/ is below Build/, but
     * Build/ is not the core's alone — an extension that compiles anything
     * ships one too. What no other repository has is what the core keeps in it:
     * Build/Scripts/, which is runTests.sh and its neighbours, and
     * Build/Sources/, the backend's Sass and TypeScript sources.
     *
     * @var array<int, string>
     */
    private const CORE_LAYOUT = ['build/scripts/', 'build/sources/'];

    /**
     * Things that exist in the core repository and nowhere else. A line of
     * advice naming one of them cannot be followed outside it.
     *
     * @var array<int, string>
     */
    private const CORE_ONLY_ARTIFACTS = [
        'typo3/sysext/', 'build/scripts/', 'runtests.sh', 'gerrit', 'change-id',
        'refs/for/', 'forge.typo3.org', 'core checkout', 'core branch', 'core root',
        'core team',
    ];

    /**
     * What every tool says first once it has recognised work outside the core.
     *
     * One sentence in one place, because three tools now say it and a caller
     * that learns to recognise it in one answer has to find it unchanged in the
     * next. Each tool appends what follows from it for its own payload.
     */
    public const OUTSIDE_CORE_NOTICE = 'This reads as work outside the TYPO3 core — a project or third-party '
        . 'extension. That is covered here, as far as the core\'s own conventions reach into it; what belongs to '
        . 'the core repository alone (the changelog, the Gerrit workflow, the runTests.sh suites) has no '
        . 'counterpart there and is left out of the answer rather than handed over.';

    /**
     * What every tool says when nothing in the call places the work at all.
     *
     * Not a hedge: the answer below is still the core's own, because there is
     * no second body of it to hand over. What is new is that the caller is told
     * which question was never answered, and how to answer it.
     */
    public const UNCERTAIN_NOTICE = 'Nothing here says which repository this work is in — no path with a '
        . 'shape of its own, none the installation knows as a package, and no installation to read. What follows is the '
        . 'core\'s own, so name a path or the repository if it is not, because several of these steps exist '
        . 'nowhere else.';

    /**
     * The scopes a path can be classified as, in the order a call's paths are
     * grouped: core work first, then what nothing placed, then the project and
     * the extensions in it.
     *
     * @return array<int, self>
     */
    public static function ofPaths(): array
    {
        return [self::Core, self::Uncertain, self::Project, self::Extension];
    }

    /**
     * The scopes a statement in the knowledge base can declare itself for.
     *
     * @return array<int, self>
     */
    public static function ofKnowledge(): array
    {
        return [self::Core, self::Project, self::Extension, self::Any];
    }

    /** Whether this is one of the two the core's own process does not reach. */
    public function isOutsideTheCore(): bool
    {
        return $this === self::Project || $this === self::Extension;
    }

    /**
     * The scope of one path: which kind of work an answer about it is for.
     *
     * The conventions here are the core's own, and several of them — the
     * changelog, the Gerrit workflow, the runTests.sh suites — do not exist
     * outside it. Answering a project-extension question with a core patch
     * checklist is worse than saying so.
     *
     * The evidence is structural where structure exists, because wording is
     * the weakest of the signals: "bootstrap_package" says everything about
     * which repository this is and matches none of the phrases below. The
     * order the signals are read in is R-SCO-001's, and what comes first is what
     * the path says about itself — a call is not a path, and two of them in one
     * session are two questions.
     *
     * @param string $path one path, or '' where the call named none and only
     *                     what was said about it can decide
     */
    public static function of(string $path, string $text = ''): self
    {
        // The "./" a caller writes for "here", and nothing else: trimming the
        // two characters as a class ate the dot of a dotfile, so no path could
        // ever reach the `.ddev/` entry below (`D-SCO-012`).
        $lowered = (string) preg_replace('#^(\./|/)+#', '', mb_strtolower(str_replace('\\', '/', $path)));
        $prose = mb_strtolower($text);

        // What this path says about itself. Nothing said about the call as a
        // whole moves it, which is what keeps two paths of one call apart:
        // folded into one string, the second path is answered for by the first.
        if ($lowered !== '') {
            if (str_contains($lowered, 'typo3/sysext/')) {
                return self::Core;
            }
            foreach (self::EXTENSION_WORK as $marker) {
                if (str_contains($lowered, $marker)) {
                    return self::Extension;
                }
            }
            // After the extension containers, not before: a sitepackage below
            // packages/ has a Configuration/ of its own, and the site
            // configuration this looks for is the one the project keeps outside
            // any package.
            foreach (self::PROJECT_WORK as $marker) {
                if (str_starts_with($lowered, $marker)) {
                    return self::Project;
                }
            }
        }

        // The same markers where the caller only wrote them down. A sysext path
        // is the only one strong enough to end the question outright. The prose
        // ones are not, and cannot be: "not TYPO3 core, a composer package under
        // vendor bk2k" names the core in order to rule it out, and reads to a
        // substring search exactly like claiming it.
        if (str_contains($prose, 'typo3/sysext/')) {
            return self::Core;
        }
        foreach (self::EXTENSION_WORK as $marker) {
            if (str_contains($prose, $marker)) {
                return self::Extension;
            }
        }

        // What the path itself is still worth once no marker has decided: what
        // the installation knows it as, then its shape. Both are read off the
        // path alone, so neither says anything where the call named none.
        $systemExtension = null;
        if ($lowered !== '') {
            // A path that is the key of an installed package which is not a
            // system extension. The installation is asked because only it
            // knows: the same key is a system extension in one and a vendor
            // package in the next. A path it has no package for says nothing,
            // which is most of them — a file inside a package was placed by the
            // markers above.
            $systemExtension = Instance::isSystemExtension($lowered);
            // The key the repository the session stands in declares for itself
            // answers the same question from its own manifest, which is the one
            // source there is before `composer install` has run (`D-SCO-012`).
            if ($systemExtension === false || $lowered === Instance::startedInPackage()) {
                return self::Extension;
            }

            // Which directories a file sits in says which repository it was
            // laid out for.
            foreach (self::EXTENSION_LAYOUT as $prefix) {
                if (str_starts_with($lowered, $prefix) && !self::isTheCoreCheckout()) {
                    return self::Extension;
                }
            }
            foreach (self::CORE_LAYOUT as $prefix) {
                if (str_starts_with($lowered, $prefix) && self::couldBeTheCore()) {
                    return self::Core;
                }
            }
        }

        // Naming a system extension by its key, or naming the contribution
        // workflow, is evidence in the other direction. Both beat the weakest
        // signal there is — which installation the session happens to sit in —
        // and neither beats a marker above, because those describe the work
        // while these only accompany it.
        if ($systemExtension === true || self::isCoreWork([$path], $text)) {
            return self::Core;
        }

        // That signal, and where there is no installation either, nothing in
        // the call has placed the work at all. That is not the core by default:
        // it is the case R-AUD-002 names, and the answer says so.
        return match (Instance::startedIn()) {
            // The project rather than an extension inside it: nothing named a
            // package, and the repository the session sits in is the site.
            Instance::KIND_COMPOSER_PROJECT => self::Project,
            Instance::KIND_CORE_CHECKOUT => self::Core,
            Instance::KIND_EXTENSION_REPOSITORY => self::Extension,
            default => self::Uncertain,
        };
    }

    /**
     * Whether the repository this session sits in can be the core at all.
     *
     * Build/Scripts/ and Build/Sources/ are the core's own from the core root,
     * and from an extension's root they are that extension's build setup. What
     * decides is the manifest rather than the directory name: the core declares
     * "type": "typo3-cms-core" in the composer.json at its root, which is what
     * `Instance` reads a checkout's kind from, and a repository declaring
     * anything else has already said it is not the core.
     *
     * The repository the session sits in, rather than the installation named
     * for reading — the two are the same until `TYPO3_DEV_COMPANION_ROOT` says otherwise.
     *
     * Where nothing places the session at all, the shape is left standing — a
     * `Build/Sources/` path is then the only evidence there is. A repository
     * whose root manifest declares an extension is placed, and there that
     * directory is the extension's own build setup.
     */
    private static function couldBeTheCore(): bool
    {
        $kind = Instance::startedIn();

        return $kind === null || $kind === Instance::KIND_CORE_CHECKOUT;
    }

    /**
     * Whether the session is standing in the core monorepo.
     *
     * The mirror of the gate above, for the other layout: `Classes/`,
     * `Configuration/` and `Resources/` are what a package is laid out as, and
     * from the core root nothing is named that way — `typo3/sysext/<key>/` or
     * `Build/` comes first. So inside a core checkout such a path is one a
     * contributor typed from the system extension directory they were standing
     * in, and reading it as somebody's extension is the back half of
     * `D-SCO-005`'s first **Wrong if**.
     *
     * Where the session sits in no installation the shape stands, for the same
     * reason it does there: it is then the only evidence in the call.
     */
    private static function isTheCoreCheckout(): bool
    {
        return Instance::startedIn() === Instance::KIND_CORE_CHECKOUT;
    }

    /**
     * The scope of every path a call named, in the order it named them.
     *
     * @param array<int, string> $paths
     * @return array<int, array{path: string, scope: self}>
     */
    public static function ofEach(array $paths, string $text = ''): array
    {
        return array_values(array_map(
            static fn(string $path): array => ['path' => $path, 'scope' => self::of($path, $text)],
            $paths,
        ));
    }

    /**
     * The paths of a call that share one scope.
     *
     * @param array<int, array{path: string, scope: self}> $scopes
     * @return array<int, string>
     */
    public static function pathsOf(array $scopes, self ...$of): array
    {
        return array_values(array_map(
            static fn(array $entry): string => $entry['path'],
            array_filter($scopes, static fn(array $entry): bool => in_array($entry['scope'], $of, true)),
        ));
    }

    /**
     * A call's paths as the questions they actually are: one group per scope,
     * core work first, then what nothing placed, then the project and the
     * extensions in it. A call that named no path is one group all the same —
     * what was said about it is then the whole of the evidence.
     *
     * @param array<int, string> $paths
     * @param array<int, array{path: string, scope: self}> $scopes
     * @return array<int, array{scope: self, paths: array<int, string>}>
     */
    public static function groups(array $paths, array $scopes, string $text): array
    {
        if ($paths === []) {
            return [['scope' => self::of('', $text), 'paths' => []]];
        }

        $groups = [];
        foreach (self::ofPaths() as $scope) {
            $of = self::pathsOf($scopes, $scope);
            if ($of !== []) {
                $groups[] = ['scope' => $scope, 'paths' => $of];
            }
        }

        return $groups;
    }

    /**
     * What every tool says once a call names paths of more than one scope.
     *
     * The paths are named, because the whole point of splitting is that the
     * caller can tell which half of the answer is about which of its files.
     *
     * @param array<int, string> $paths
     */
    public static function outsideCoreAmong(array $paths): string
    {
        return sprintf(
            'Of the paths given, %s %s outside the TYPO3 core — a project or third-party extension. What follows '
            . 'is split accordingly: what only the core repository has is left out of the half that is about %s.',
            implode(' and ', $paths),
            count($paths) === 1 ? 'is' : 'are',
            count($paths) === 1 ? 'it' : 'them',
        );
    }

    /**
     * Whether a single step, check or checklist item is core-only.
     *
     * The distinction the notice draws in prose has to be drawn in the payload
     * too, and it is drawn per line rather than per section: a checklist mixes
     * "reproduce the bug with a failing test" — true anywhere — with "add a
     * changelog file under typo3/sysext/", which is a path that does not exist
     * in the repository the caller is in.
     */
    public static function isCoreOnly(string $text): bool
    {
        $haystack = mb_strtolower($text);
        foreach (self::CORE_ONLY_ARTIFACTS as $artifact) {
            if (str_contains($haystack, $artifact)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether anything in the task actually says this is core work.
     *
     * A scope of `core` can be the last signal speaking — the checkout the
     * session sits in — and most tasks say nothing either way: "review the TCA
     * of an extension" is one of them. Where an answer would state the core's
     * own process as applying, that is not enough, so this asks for the
     * evidence rather than for the absence of the opposite.
     *
     * @param array<int, string> $paths
     */
    public static function isCoreWork(array $paths, string $text = ''): bool
    {
        $haystack = mb_strtolower(implode(' ', $paths) . ' ' . $text);
        foreach (self::CORE_WORK as $marker) {
            if (str_contains($haystack, $marker)) {
                return true;
            }
        }

        return false;
    }
}
