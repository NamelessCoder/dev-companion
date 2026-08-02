# What `typo3_documentation_lookup` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks this page;
[tools.md](../tools.md) is where the current shape of an answer is, and
[readme.md](readme.md) is what the recording as a whole is of.

## documentation: search

Called with:

```json
{
    "queries": [
        "page title event",
        "page title provider"
    ],
    "targetVersion": "14.3",
    "limit": 3
}
```

Text:

```
Official TYPO3 documentation for 14.3.
Source: https://docs.typo3.org

## Page title API
typo3/reference-coreapi · 14.3 · https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Seo/PageTitleApi.html
In order to keep setting the page titles in control, you can use the page title API. The API uses page title providers to define the page title based on page record and the content on the page. Based on the priority of the providers, the \TYPO3\CMS\Core\PageTitle\PageTitleProviderManager will check the providers if a title is given by the provider. Besides the providers shipped by the Core, you can add own providers. An integrator can define the priority of the providers for his project. New in version 14.0

## ModifyPageLayoutOnLoginProviderSelectionEvent
typo3/reference-coreapi · 14.3 · https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Events/Events/Backend/ModifyPageLayoutOnLoginProviderSelectionEvent.html
The PSR-14 event \TYPO3\CMS\Backend\LoginProvider\Event\ModifyPageLayoutOnLoginProviderSelectionEvent allows to modify variables for the view depending on a special login provider set in the controller. Note Currently, we do not have an example for this event. If you can provide a useful one, please open an issue with your code snippets or a pull request. Allows to modify variables for the view depending on a special login provider set in the controller.

## ModuleProvider
typo3/reference-coreapi · 14.3 · https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Backend/BackendModules/ModuleProviderAPI.html
The ModuleProvider API allows extension authors to work with the registered modules. This API is the central point to retrieve modules, since it automatically performs necessary access checks and prepares specific structures, for example for the use in menus. This is the central point to retrieve modules from the ModuleRegistry, while performing the necessary access checks, which ModuleRegistry does not deal with. Simple wrapper for the registry, which just checks if a module is registered. Does NOT perform any access checks.
```

Data:

```json
{
    "mode": "search",
    "status": "answered",
    "targetVersion": "14.3",
    "source": "https://docs.typo3.org",
    "queries": [
        "page title event",
        "page title provider"
    ],
    "results": [
        {
            "title": "Page title API",
            "url": "https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Seo/PageTitleApi.html",
            "document": "typo3/reference-coreapi",
            "documentTitle": "TYPO3 Explained",
            "documentVersion": "14.3",
            "section": "Page title API",
            "excerpt": "In order to keep setting the page titles in control, you can use the page title API. The API uses page title providers to define the page title based on page record and the content on the page. Based on the priority of the providers, the \\TYPO3\\CMS\\Core\\PageTitle\\PageTitleProviderManager will check the providers if a title is given by the provider. Besides the providers shipped by the Core, you can add own providers. An integrator can define the priority of the providers for his project. New in version 14.0",
            "content": ""
        },
        {
            "title": "ModifyPageLayoutOnLoginProviderSelectionEvent",
            "url": "https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Events/Events/Backend/ModifyPageLayoutOnLoginProviderSelectionEvent.html",
            "document": "typo3/reference-coreapi",
            "documentTitle": "TYPO3 Explained",
            "documentVersion": "14.3",
            "section": "ModifyPageLayoutOnLoginProviderSelectionEvent",
            "excerpt": "The PSR-14 event \\TYPO3\\CMS\\Backend\\LoginProvider\\Event\\ModifyPageLayoutOnLoginProviderSelectionEvent allows to modify variables for the view depending on a special login provider set in the controller. Note Currently, we do not have an example for this event. If you can provide a useful one, please open an issue with your code snippets or a pull request. Allows to modify variables for the view depending on a special login provider set in the controller.",
            "content": ""
        },
        {
            "title": "ModuleProvider",
            "url": "https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Backend/BackendModules/ModuleProviderAPI.html",
            "document": "typo3/reference-coreapi",
            "documentTitle": "TYPO3 Explained",
            "documentVersion": "14.3",
            "section": "ModuleProvider",
            "excerpt": "The ModuleProvider API allows extension authors to work with the registered modules. This API is the central point to retrieve modules, since it automatically performs necessary access checks and prepares specific structures, for example for the use in menus. This is the central point to retrieve modules from the ModuleRegistry, while performing the necessary access checks, which ModuleRegistry does not deal with. Simple wrapper for the registry, which just checks if a module is registered. Does NOT perform any access checks.",
            "content": ""
        }
    ],
    "unavailable": null
}
```

## documentation: page

Called with:

```json
{
    "page": "https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Seo/PageTitleApi.html",
    "targetVersion": "14.3"
}
```

Text:

````
Official TYPO3 documentation for 14.3.
Source: https://docs.typo3.org

## Page title API
typo3/reference-coreapi · 14.3 · https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Seo/PageTitleApi.html

# Page title API

In order to keep setting the page titles in control, you can use the page title API. The API uses page title providers to define the page title based on page record and the content on the page.

Based on the priority of the providers, the \TYPO3\CMS\Core\PageTitle\PageTitleProviderManager will check the providers if a title is given by the provider.

Besides the providers shipped by the Core, you can add own providers. An integrator can define the priority of the providers for his project.

New in version 14.0

The page title can also be set via the Page.title ViewHelper <f:page.title>.

See also

The page title is further influenced by Properties of 'config' and websiteTitle.

Table of contents

- List of page title providers shipped by the Core SeoTitlePageTitleProvider RecordTitleProvider RecordPageTitleProvider

- Create your own page title provider Example: Set the page title from your extension's controller Example: Use values from the site configuration in the page title

- Define the priority of PageTitleProviders

## List of page title providers shipped by the Core

The TYPO3 Core ships the following page title providers by default, listed from highest to lowest priority.

### SeoTitlePageTitleProvider

System extension typo3/cms-seo ships the \TYPO3\CMS\Seo\PageTitle\SeoTitlePageTitleProvider . It is only available if the extension is installed. It has the identifier seo.

When an editor has set a value for the SEO title in the page properties of the page, this provider will provide that title.

If you have not installed the SEO system extension, the field and provider are not available.

### RecordTitleProvider

New in version 14.0

The fallback provider with the lowest priority is the \TYPO3\CMS\Core\PageTitle\RecordTitleProvider . It has the identifier recordTitle.

This provider can be used by third-party extensions to set the page title.

```
<?php

declare(strict_types=1);

namespace MyVendor\MyExtension\Controller;

use MyVendor\MyExtension\Domain\Model\Item;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\PageTitle\RecordTitleProvider;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class ItemController extends ActionController
{
    public function __construct(
        private readonly RecordTitleProvider $recordTitleProvider,
    ) {}

    public function showAction(Item $item): ResponseInterface
    {
        $this->recordTitleProvider->setTitle($item->getTitle());
        $this->view->assign('item', $item);
        return $this->htmlResponse();
    }
}
```

### RecordPageTitleProvider

The fallback provider with the lowest priority is the \TYPO3\CMS\Core\PageTitle\RecordPageTitleProvider . It has the identifier record.

When no other title is set by a provider, this provider will return the title of the page as defined in the page properties.

## Create your own page title provider

Extension developers may want to have an own provider for page titles. For example, if you have an extension with records and a detail view, the title of the page record will not be the correct title. To make sure to display the correct page title, you have to create your own page title provider. It is quite easy to create one.

New in version 14.0

In many use cases, the provider RecordTitleProvider can be used instead of writing a custom page title provider.

### Example: Set the page title from your extension's controller

First, create a PHP class in your extension that implements the \TYPO3\CMS\Core\PageTitle\PageTitleProviderInterface , for example by extending \TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider . Within this method you can create your own logic to define the correct title.

```
<?php

declare(strict_types=1);

namespace MyVendor\MySitepackage\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;

final class MyOwnPageTitleProvider extends AbstractPageTitleProvider
{
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
```

Usage example in an Extbase controller:

```
<?php

use MyVendor\MySitepackage\PageTitle\MyOwnPageTitleProvider;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class SomeController extends ActionController
{
    public function __construct(
        private readonly MyOwnPageTitleProvider $titleProvider,
    ) {}

    public function someAction(): ResponseInterface
    {
        $this->titleProvider->setTitle('Title from controller action');
        // do something
        return $this->htmlResponse();
    }
}
```

Configure the new page title provider in your TypoScript setup:

```
config {
  pageTitleProviders {
    sitepackage {
      provider = MyVendor\MySitepackage\PageTitle\MyOwnPageTitleProvider
      before = record
    }
  }
}
```

### Example: Use values from the site configuration in the page title

If you want to use data from the site configuration, for example the site title, you can implement a page title provider as follows:

```
<?php

declare(strict_types=1);

namespace MyVendor\MySitepackage\PageTitle;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\PageTitle\PageTitleProviderInterface;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Frontend\Page\PageInformation;

#[Autoconfigure(public: true)]
final readonly class WebsiteTitleProvider implements PageTitleProviderInterface
{
    private ServerRequestInterface $request;

    public function __construct(
        private SiteFinder $siteFinder,
    ) {}

    public function getTitle(): string
    {
        $site = $this->siteFinder->getSiteByPageId($this->getPageInformation()->getId());
        $titles = [
            $this->getPageInformation()->getPageRecord()['title'] ?? '',
            $site->getAttribute('websiteTitle'),
        ];

        return implode(' - ', $titles);
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    private function getPageInformation(): PageInformation
    {
        $pageInformation = $this->request->getAttribute('frontend.page.information');
        if (!$pageInformation instanceof PageInformation) {
            throw new \Exception('Current frontend page information not available', 1730098625);
        }
        return $pageInformation;
    }
}
```

The class must be set to public, because we inject the class SiteFinder as dependency.

Then flush the cache in System > Maintenance > Flush TYPO3 and PHP Cache.

Configure the new page title provider to be used in your TypoScript setup:

```
config {
  pageTitleProviders {
    sitepackage {
      provider = MyVendor\MySitepackage\PageTitle\WebsiteTitleProvider
      before = record
      after = seo
    }
  }
}
```

The registered page title providers are called after each other in the configured order. The first provider that returns a non-empty value is used, the providers later in the order are ignored.

Therefore our custom provider should be loaded before record, the default provider which always returns a value. If the system extension typo3/cms-seo is loaded the default SEO Title has a particular format, you can change this by loading your custom provider before seo.

## Define the priority of PageTitleProviders

The priority of the providers is set by the TypoScript property config.pageTitleProviders. This way an integrator is able to set the priorities for their project and can even have conditions in place.

By default, the Core has the following setup:

```
config.pageTitleProviders {
  record.provider = TYPO3\CMS\Core\PageTitle\RecordPageTitleProvider
  recordTitle {
    provider = TYPO3\CMS\Core\PageTitle\RecordTitleProvider
    before = record
  }
}
```

The sorting of the providers is based on the before and after parameters. If you want a provider to be handled before a specific other provider, just set that provider in the before , do the same with after .

For example, if you want the RecordTitleProvider to take priority over the SeoTitlePageTitleProvider you can change the order via TypoScript:

```
config.pageTitleProviders {
  recordTitle {
    before = seo
  }
}
```

First the SeoTitlePageTitleProvider (because it will be handled before record ) and, if this providers did not provide a title, the RecordPageTitleProvider will be checked.

You can override these settings within your own installation. You can add as many providers as you want. Be aware that if a provider returns a non-empty value, all provider with a lower priority will not be checked.
````

Data:

```json
{
    "mode": "page",
    "status": "answered",
    "targetVersion": "14.3",
    "source": "https://docs.typo3.org",
    "queries": [],
    "results": [
        {
            "title": "Page title API",
            "url": "https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/Seo/PageTitleApi.html",
            "document": "typo3/reference-coreapi",
            "documentTitle": "TYPO3 Explained",
            "documentVersion": "14.3",
            "section": "Page title API",
            "excerpt": "# Page title API\n\nIn order to keep setting the page titles in control, you can use the page title API. The API uses page title providers to define the page title based on page record and the content on the page.\n\nBased on the priority of the providers, the \\TYPO3\\CMS\\Core\\PageTitle\\PageTitleProviderManager will check the providers if a title is given by the provider.\n\nBesides the providers shipped by the Core, you can add own providers. An integrator can define the priority of the providers for his project.\n\nNew in version 14.0\n\nThe page title can also be set via the Page.title ViewHelper <f:page.title>.\n\nSee also\n\nThe page title is further influenced by Properties of 'config' and websiteTit",
            "content": "# Page title API\n\nIn order to keep setting the page titles in control, you can use the page title API. The API uses page title providers to define the page title based on page record and the content on the page.\n\nBased on the priority of the providers, the \\TYPO3\\CMS\\Core\\PageTitle\\PageTitleProviderManager will check the providers if a title is given by the provider.\n\nBesides the providers shipped by the Core, you can add own providers. An integrator can define the priority of the providers for his project.\n\nNew in version 14.0\n\nThe page title can also be set via the Page.title ViewHelper <f:page.title>.\n\nSee also\n\nThe page title is further influenced by Properties of 'config' and websiteTitle.\n\nTable of contents\n\n- List of page title providers shipped by the Core SeoTitlePageTitleProvider RecordTitleProvider RecordPageTitleProvider\n\n- Create your own page title provider Example: Set the page title from your extension's controller Example: Use values from the site configuration in the page title\n\n- Define the priority of PageTitleProviders\n\n## List of page title providers shipped by the Core\n\nThe TYPO3 Core ships the following page title providers by default, listed from highest to lowest priority.\n\n### SeoTitlePageTitleProvider\n\nSystem extension typo3/cms-seo ships the \\TYPO3\\CMS\\Seo\\PageTitle\\SeoTitlePageTitleProvider . It is only available if the extension is installed. It has the identifier seo.\n\nWhen an editor has set a value for the SEO title in the page properties of the page, this provider will provide that title.\n\nIf you have not installed the SEO system extension, the field and provider are not available.\n\n### RecordTitleProvider\n\nNew in version 14.0\n\nThe fallback provider with the lowest priority is the \\TYPO3\\CMS\\Core\\PageTitle\\RecordTitleProvider . It has the identifier recordTitle.\n\nThis provider can be used by third-party extensions to set the page title.\n\n```\n<?php\n\ndeclare(strict_types=1);\n\nnamespace MyVendor\\MyExtension\\Controller;\n\nuse MyVendor\\MyExtension\\Domain\\Model\\Item;\nuse Psr\\Http\\Message\\ResponseInterface;\nuse TYPO3\\CMS\\Core\\PageTitle\\RecordTitleProvider;\nuse TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ActionController;\n\nfinal class ItemController extends ActionController\n{\n    public function __construct(\n        private readonly RecordTitleProvider $recordTitleProvider,\n    ) {}\n\n    public function showAction(Item $item): ResponseInterface\n    {\n        $this->recordTitleProvider->setTitle($item->getTitle());\n        $this->view->assign('item', $item);\n        return $this->htmlResponse();\n    }\n}\n```\n\n### RecordPageTitleProvider\n\nThe fallback provider with the lowest priority is the \\TYPO3\\CMS\\Core\\PageTitle\\RecordPageTitleProvider . It has the identifier record.\n\nWhen no other title is set by a provider, this provider will return the title of the page as defined in the page properties.\n\n## Create your own page title provider\n\nExtension developers may want to have an own provider for page titles. For example, if you have an extension with records and a detail view, the title of the page record will not be the correct title. To make sure to display the correct page title, you have to create your own page title provider. It is quite easy to create one.\n\nNew in version 14.0\n\nIn many use cases, the provider RecordTitleProvider can be used instead of writing a custom page title provider.\n\n### Example: Set the page title from your extension's controller\n\nFirst, create a PHP class in your extension that implements the \\TYPO3\\CMS\\Core\\PageTitle\\PageTitleProviderInterface , for example by extending \\TYPO3\\CMS\\Core\\PageTitle\\AbstractPageTitleProvider . Within this method you can create your own logic to define the correct title.\n\n```\n<?php\n\ndeclare(strict_types=1);\n\nnamespace MyVendor\\MySitepackage\\PageTitle;\n\nuse TYPO3\\CMS\\Core\\PageTitle\\AbstractPageTitleProvider;\n\nfinal class MyOwnPageTitleProvider extends AbstractPageTitleProvider\n{\n    public function setTitle(string $title): void\n    {\n        $this->title = $title;\n    }\n}\n```\n\nUsage example in an Extbase controller:\n\n```\n<?php\n\nuse MyVendor\\MySitepackage\\PageTitle\\MyOwnPageTitleProvider;\nuse Psr\\Http\\Message\\ResponseInterface;\nuse TYPO3\\CMS\\Extbase\\Mvc\\Controller\\ActionController;\n\nfinal class SomeController extends ActionController\n{\n    public function __construct(\n        private readonly MyOwnPageTitleProvider $titleProvider,\n    ) {}\n\n    public function someAction(): ResponseInterface\n    {\n        $this->titleProvider->setTitle('Title from controller action');\n        // do something\n        return $this->htmlResponse();\n    }\n}\n```\n\nConfigure the new page title provider in your TypoScript setup:\n\n```\nconfig {\n  pageTitleProviders {\n    sitepackage {\n      provider = MyVendor\\MySitepackage\\PageTitle\\MyOwnPageTitleProvider\n      before = record\n    }\n  }\n}\n```\n\n### Example: Use values from the site configuration in the page title\n\nIf you want to use data from the site configuration, for example the site title, you can implement a page title provider as follows:\n\n```\n<?php\n\ndeclare(strict_types=1);\n\nnamespace MyVendor\\MySitepackage\\PageTitle;\n\nuse Psr\\Http\\Message\\ServerRequestInterface;\nuse Symfony\\Component\\DependencyInjection\\Attribute\\Autoconfigure;\nuse TYPO3\\CMS\\Core\\PageTitle\\PageTitleProviderInterface;\nuse TYPO3\\CMS\\Core\\Site\\SiteFinder;\nuse TYPO3\\CMS\\Frontend\\Page\\PageInformation;\n\n#[Autoconfigure(public: true)]\nfinal readonly class WebsiteTitleProvider implements PageTitleProviderInterface\n{\n    private ServerRequestInterface $request;\n\n    public function __construct(\n        private SiteFinder $siteFinder,\n    ) {}\n\n    public function getTitle(): string\n    {\n        $site = $this->siteFinder->getSiteByPageId($this->getPageInformation()->getId());\n        $titles = [\n            $this->getPageInformation()->getPageRecord()['title'] ?? '',\n            $site->getAttribute('websiteTitle'),\n        ];\n\n        return implode(' - ', $titles);\n    }\n\n    public function setRequest(ServerRequestInterface $request): void\n    {\n        $this->request = $request;\n    }\n\n    private function getPageInformation(): PageInformation\n    {\n        $pageInformation = $this->request->getAttribute('frontend.page.information');\n        if (!$pageInformation instanceof PageInformation) {\n            throw new \\Exception('Current frontend page information not available', 1730098625);\n        }\n        return $pageInformation;\n    }\n}\n```\n\nThe class must be set to public, because we inject the class SiteFinder as dependency.\n\nThen flush the cache in System > Maintenance > Flush TYPO3 and PHP Cache.\n\nConfigure the new page title provider to be used in your TypoScript setup:\n\n```\nconfig {\n  pageTitleProviders {\n    sitepackage {\n      provider = MyVendor\\MySitepackage\\PageTitle\\WebsiteTitleProvider\n      before = record\n      after = seo\n    }\n  }\n}\n```\n\nThe registered page title providers are called after each other in the configured order. The first provider that returns a non-empty value is used, the providers later in the order are ignored.\n\nTherefore our custom provider should be loaded before record, the default provider which always returns a value. If the system extension typo3/cms-seo is loaded the default SEO Title has a particular format, you can change this by loading your custom provider before seo.\n\n## Define the priority of PageTitleProviders\n\nThe priority of the providers is set by the TypoScript property config.pageTitleProviders. This way an integrator is able to set the priorities for their project and can even have conditions in place.\n\nBy default, the Core has the following setup:\n\n```\nconfig.pageTitleProviders {\n  record.provider = TYPO3\\CMS\\Core\\PageTitle\\RecordPageTitleProvider\n  recordTitle {\n    provider = TYPO3\\CMS\\Core\\PageTitle\\RecordTitleProvider\n    before = record\n  }\n}\n```\n\nThe sorting of the providers is based on the before and after parameters. If you want a provider to be handled before a specific other provider, just set that provider in the before , do the same with after .\n\nFor example, if you want the RecordTitleProvider to take priority over the SeoTitlePageTitleProvider you can change the order via TypoScript:\n\n```\nconfig.pageTitleProviders {\n  recordTitle {\n    before = seo\n  }\n}\n```\n\nFirst the SeoTitlePageTitleProvider (because it will be handled before record ) and, if this providers did not provide a title, the RecordPageTitleProvider will be checked.\n\nYou can override these settings within your own installation. You can add as many providers as you want. Be aware that if a provider returns a non-empty value, all provider with a lower priority will not be checked."
        }
    ],
    "unavailable": null
}
```

## documentation: unsupported version

Called with:

```json
{
    "queries": [
        "page title event"
    ],
    "targetVersion": "999"
}
```

Text:

```
Official TYPO3 documentation for 999.
Source: https://docs.typo3.org
Could not answer: TYPO3 999 is outside the covered versions: 12.4, 13.4, 14.3, main.
```

Data:

```json
{
    "mode": "search",
    "status": "unavailable",
    "targetVersion": "999",
    "source": "https://docs.typo3.org",
    "queries": [
        "page title event"
    ],
    "results": [],
    "unavailable": {
        "cause": "version-not-covered",
        "reason": "TYPO3 999 is outside the covered versions: 12.4, 13.4, 14.3, main."
    }
}
```
