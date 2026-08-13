---
description: >-
  How a throwaway functional test puts one snippet through the frontend, and how the HTML it produced is read at all.
whenToUse: >-
  When a change to TypoScript defaults, to ext_localconf TypoScript or to anything below lib.parseFunc has to be judged and the diff does not say what it renders. Asserting a response whose expected value is already known is the frontend request hint instead.
hints:
  - core-tests
  - extension-test-frontend-request
---

# Proving What a Rendering Change Renders

A TypoScript diff does not say what it renders, and where no test covers the
constellation nothing else in the checkout says it either. What settles it is a
throwaway functional test that renders one snippet and prints what came out.

This page is the harness. What `lib.parseFunc_RTE` does to a snippet is what the
probe is for, and nothing here states it.

## The Probe

The file goes below `typo3/sysext/frontend/Tests/Functional/Rendering/`, because
a file the runner does not collect proves nothing. One page row, one site
configuration and one `sys_template` row are the whole fixture.

```php
<?php
declare(strict_types=1);

namespace TYPO3\CMS\Frontend\Tests\Functional\Rendering;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Tests\Functional\SiteHandling\SiteBasedTestTrait;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class RenderingProbeTest extends FunctionalTestCase
{
    use SiteBasedTestTrait;

    protected const LANGUAGE_PRESETS = [
        'EN' => ['id' => 0, 'title' => 'English', 'locale' => 'en_US.UTF8'],
    ];

    #[Test]
    public function probe(): void
    {
        $connectionPool = $this->get(ConnectionPool::class);
        $connectionPool->getConnectionForTable('pages')->insert(
            'pages',
            ['uid' => 1, 'pid' => 0, 'title' => 'Root', 'slug' => '/', 'doktype' => 1, 'perms_everybody' => 15]
        );
        $this->writeSiteConfiguration(
            'test',
            $this->buildSiteConfiguration(1, '/'),
            [$this->buildDefaultLanguageConfiguration('EN', '/en/')]
        );
        $connectionPool->getConnectionForTable('sys_template')->insert(
            'sys_template',
            [
                'pid' => 1,
                'root' => 1,
                'clear' => 3,
                'config' => <<<EOT
page = PAGE
page.10 = TEXT
page.10.value (
<figure class="table">
<figcaption>A caption</figcaption>
</figure>
)
page.10.parseFunc =< lib.parseFunc_RTE
EOT,
            ]
        );
        $response = $this->executeFrontendSubRequest((new InternalRequest())->withPageId(1));
        echo "\n===PROBE===\n" . (string)$response->getBody() . "\n===END===\n";
        self::assertTrue(true);
    }
}
```

`TEXT` is the cObj that renders a snippet handed to it: it takes `value` and
runs the rest of its configuration as `stdWrap`, so `parseFunc` beside `value`
is what puts the snippet through the RTE setup. A `FLUIDTEMPLATE` around an
`<f:format.html>` reaches the same code through two more layers, and a snippet
written as entities there never arrives at `parseFunc` at all.

## Putting the Snippet Into TypoScript

Markup that spans lines needs the multi-line form, `value ( … )`. A single-line
assignment ends at the newline, and the lines after it are read as TypoScript.

Three spellings differ by one space, and two of them are not an assignment:

- `value = <figure …>` assigns the markup as text. A leading `<` in a value is
  text, whatever it looks like.
- `value =<figure …>` is the reference operator. What follows it is read as an
  identifier and stops at the first space, so the line becomes a reference to
  `figure`. Nothing fails: the page renders `< figure` where the markup should
  be.
- `value < lib.foo` is the copy operator.

In a `parseFunc` position both `parseFunc =< lib.parseFunc_RTE` and
`parseFunc = < lib.parseFunc_RTE` work, and they resolve at different times —
the first when the TypoScript is parsed, the second through
`ContentObjectRenderer::mergeTSRef()` while the page renders. The core's own
TypoScript writes the second.

## Reading What It Rendered

A functional test that passes prints nothing, and the rendered HTML is the
unknown the probe exists to see.

`echo` from the test body is what prints it. The core's functional PHPUnit
configuration does not set `beStrictAboutOutputDuringTests`, so output is
neither swallowed nor turned into a risky test, and it appears whether the probe
passes or fails.

Asserting a sentinel — `self::assertSame('PROBE', $body)` — prints the whole
body as the failure diff. It is the fallback where something does swallow
output, and it costs a red run whose failure means nothing.

## Where lib.parseFunc_RTE Comes From

**Since:** 13

`typo3/sysext/frontend/ext_localconf.php` registers `lib.parseFunc` and
`lib.parseFunc_RTE`, so both are in every functional test. The probe needs no
extension and no static template.

## Where lib.parseFunc_RTE Comes From

**Until:** 12

`lib.parseFunc_RTE` lives in `fluid_styled_content`'s static TypoScript and in
nothing the frontend loads by itself. Without it the request dies with
`LogicException: Invoked ContentObjectRenderer::parseFunc without any configuration`,
code `1641989097`.

Two things bring it in and both are needed:
`protected array $coreExtensionsToLoad = ['fluid_styled_content'];` on the test
class, and
`'include_static_file' => 'EXT:fluid_styled_content/Configuration/TypoScript/'`
in the `sys_template` row. Either one alone leaves the same exception.

## Running It

```bash
CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- typo3/sysext/frontend/Tests/Functional/Rendering/RenderingProbeTest.php
```

sqlite is the default and the fastest, which is what makes several rounds
affordable; `typo3_script_lookup` has the rest of the options. Name the file
after `--`, because a probe is run again after every change to it.

## Removing the Probe

Delete the file when the question is answered, and confirm with `git status`
that the checkout is clean. What the probe established goes into the review or
the issue; the probe itself asserts nothing and is evidence of nothing once it
is committed.
