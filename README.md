[![M2 Coding Standard](https://github.com/FriendsOfMagento/module-annotations/actions/workflows/coding-standard.yml/badge.svg?branch=main)](https://github.com/FriendsOfMagento/module-annotations/actions/workflows/coding-standard.yml)
[![M2 Mess Detector](https://github.com/FriendsOfMagento/module-annotations/actions/workflows/mess-detector.yml/badge.svg?branch=main)](https://github.com/FriendsOfMagento/module-annotations/actions/workflows/mess-detector.yml)
[![M2 PHPStan](https://github.com/FriendsOfMagento/module-annotations/actions/workflows/phpstan.yml/badge.svg?branch=main)](https://github.com/FriendsOfMagento/module-annotations/actions/workflows/phpstan.yml)
# Annotations for Magento 2

During the development of extensions, we often encounter a situation where some [plugin](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/plugins.html) or [observer](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/events-and-observers.html) should be executed only on certain Magento versions and Magento editions. To solve this problem, we usually inject `\Magento\Framework\App\ProductMetadataInterface` and then use additional checks for versions and editions.

**Annotations for Magento 2** will help solve this problem and provide flexible configuration for conditions combination.

## Requirements

- PHP >= 7.1
- Magento Any Edition >= 2.3.0

## How to install

### Install via composer (recommended)

Run the following command in Magento 2 root folder:

```
composer require fom/module-annotations
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## Developer Guide

In Magento 2, all [plugins](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/plugins.html) and [observers](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/events-and-observers.html) using `\Magento\Framework\App\ProductMetadataInterface` to check the current version and edition still continue to be instantiated, executed, and use memory and processor. **Annotations for Magento 2** module uses an optimized approach, plugins and observers that should not be executed for the current version and edition will be disabled at the stage of building the configuration of plugins and observers. This means that checking whether the plugin or the observer should be executed will be performed **ONLY** once, at the stage of cache building, which will slightly reduce stack traces, and this in turn should have a positive impact on performance.

[Attributes](https://www.php.net/manual/en/language.attributes.php) (for PHP >= 8.0) or [Doctrine Annotations](https://www.doctrine-project.org/projects/annotations.html) (for PHP >= 7.1) are used to configure [plugins](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/plugins.html) and [observers](https://devdocs.magento.com/guides/v2.4/extension-dev-guide/events-and-observers.html).

**Annotations for Magento 2** provides two attribute classes:
- [`Fom\Annotations\Attribute\Platform`](Attribute/Platform.php)
- [`Fom\Annotations\Attribute\Operator`](Attribute/Operator.php)

### [`Fom\Annotations\Attribute\Platform`](Attribute/Platform.php)
The attribute is used to describe the edition, version, and comparison operator for the Magento. This attribute is repeatable, and can be added to the class several times.

Parameters:
- `edition` - `string`, contains the Magento edition. Acceptable values:
  - `'any'` - corresponds to the constant [`Platform::EDITION_ANY`](Attribute/Platform.php#L21)
  - `'Community'` - corresponds to the constant [`Platform::EDITION_COMMUNITY`](Attribute/Platform.php#L22)
  - `'Commerce'` - corresponds to the constant [`Platform::EDITION_COMMERCE`](Attribute/Platform.php#L23)
  - `'B2B'` - corresponds to the constant [`Platform::EDITION_B2B`](Attribute/Platform.php#L24)
  
  If an empty value is passed, the check will be performed as for the value [`Platform::EDITION_ANY`](Attribute/Platform.php#L21).


- `version` - `string`, any correct version that will be passed to the [version_compare()](https://www.php.net/manual/en/function.version-compare.php) function.

- `comparison` - `string`, any valid comparison operator accepted by the [version_compare()](https://www.php.net/manual/en/function.version-compare.php) function:
  - `'<'` - corresponds to the constant [`Platform::COMPARISON_LESS_THAN`](Attribute/Platform.php#L29)
  - `'<='` - corresponds to the constant [`Platform::COMPARISON_LESS_THAN_OR_EQUAL`](Attribute/Platform.php#L30)
  - `'>'` - corresponds to the constant [`Platform::COMPARISON_GREATER_THAN`](Attribute/Platform.php#L31)
  - `'>='` - corresponds to the constant [`Platform::COMPARISON_GREATER_THAN_OR_EQUAL`](Attribute/Platform.php#L32)
  - `'='` - corresponds to the constant [`Platform::COMPARISON_EQUAL`](Attribute/Platform.php#L33)
  - `'!='` - corresponds to the constant [`Platform::COMPARISON_NOT_EQUAL`](Attribute/Platform.php#L34)

  If the value was not passed, the [`Platform::COMPARISON_GREATER_THAN_OR_EQUAL`](Attribute/Platform.php#L32) will be used by default.

### [`Fom\Annotations\Attribute\Operator`](Attribute/Operator.php)

The attribute is used if the [`Platform`](Attribute/Platform.php) attribute has been added to the class several times, it is used to summarize the results obtained from [`Platform`](Attribute/Platform.php) list. This attribute is optional and can be applied to a class only 1 time.

Parameters:
- `operator` - `string`, used to sum the result as logical `AND` and logical `OR`.
  - `'and'` - corresponds to the constant [`Operator::OPERATOR_AND`](Attribute/Operator.php#L17)
  - `'or'` - corresponds to the constant [`Operator::OPERATOR_OR`](Attribute/Operator.php#L18)

  By default, [`Operator::OPERATOR_AND`](Attribute/Operator.php#L17) is used.

## Examples

In the examples we will demonstrate observers, but the exact same declaration is used for plugins.

### Observer with PHP8 Attributes

Let's disable observer for Any Magento Edition with versions lower than 2.4.4.

```php
<?php

declare(strict_types=1);

namespace Vendor\Module\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

#[
    \Fom\Annotations\Attribute\Platform(
        edition: \Fom\Annotations\Attribute\Platform::EDITION_ANY,
        version: '2.4.4',
        comparison: \Fom\Annotations\Attribute\Platform::COMPARISON_LESS_THAN
    ),
    \Fom\Annotations\Attribute\Operator(operator: \Fom\Annotations\Attribute\Operator::OPERATOR_AND)
]
class SomeKindOfActionObserver implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        // ...
    }
}
```

We can also import our attributes to use a short class name.

```php
<?php

declare(strict_types=1);

namespace Vendor\Module\Observer;

use Fom\Annotations\Attribute\Operator;
use Fom\Annotations\Attribute\Platform;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

#[
    Platform(
        edition: Platform::EDITION_ANY,
        version: '2.4.4',
        comparison: Platform::COMPARISON_LESS_THAN
    ),
    Operator(operator: Operator::OPERATOR_AND)
]
class SomeKindOfActionObserver implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        // ...
    }
}
```

### Observer with Doctrine Annotations (PHP < 8.0).

Let's do the same, but with a docblock section to allow use for older PHP versions.

```php
<?php

declare(strict_types=1);

namespace Vendor\Module\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @\Fom\Annotations\Attribute\Platform(
 *     edition = \Fom\Annotations\Attribute\Platform::EDITION_ANY,
 *     version = "2.4.4",
 *     comparison = \Fom\Annotations\Attribute\Platform::COMPARISON_LESS_THAN
 * )
 * @\Fom\Annotations\Attribute\Operator(operator = \Fom\Annotations\Attribute\Operator::OPERATOR_AND)
 */
class SomeKindOfActionObserver implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        // ...
    }
}
```

We can still import our attributes to use a short class name.

```php
<?php

declare(strict_types=1);

namespace Vendor\Module\Observer;

use Fom\Annotations\Attribute\Operator;
use Fom\Annotations\Attribute\Platform;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @Platform(
 *     edition = Platform::EDITION_ANY,
 *     version = "2.4.4",
 *     comparison = Platform::COMPARISON_LESS_THAN
 * )
 * @Operator(operator = Operator::OPERATOR_AND)
 */
class SomeKindOfActionObserver implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        // ...
    }
}
```

### Observer with a combined declaration, PHP 8 Attribute and Doctrine annotations, allowing this module to be used with different PHP versions.

We have to use the one-line PHP8 Attribute notation. In this case, our PHP8 Attribute will be interpreted as a comment for older PHP versions, since it starts with `#`.

```php
<?php

declare(strict_types=1);

namespace Vendor\Module\Observer;

use Fom\Annotations\Attribute\Operator;
use Fom\Annotations\Attribute\Platform;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @Platform(
 *     edition = Platform::EDITION_ANY,
 *     version = "2.4.4",
 *     comparison = Platform::COMPARISON_LESS_THAN
 * )
 * @Operator(operator = Operator::OPERATOR_AND)
 */
#[Platform(edition: Platform::EDITION_ANY, version: '2.4.4', comparison: Platform::COMPARISON_LESS_THAN), Operator(operator: Operator::OPERATOR_AND)]
class SomeKindOfActionObserver implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        // ...
    }
}
```

### Example without using the [`Operator`](Attribute/Operator.php) attribute

Since [`Operator`](Attribute/Operator.php) is equal to [`Operator::OPERATOR_AND`](Attribute/Operator.php#L17) by default, we can omit it in the following cases:

#### We use only one platform condition

```php
<?php

declare(strict_types=1);

namespace Vendor\Module\Observer;

use Fom\Annotations\Attribute\Operator;
use Fom\Annotations\Attribute\Platform;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

#[
    Platform(
        edition: Platform::EDITION_ANY,
        version: '2.4.4',
        comparison: Platform::COMPARISON_LESS_THAN
    )
]
class SomeKindOfActionObserver implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        // ...
    }
}
```

#### We use several platform conditions, but all their conditions must be satisfied at the same time

```php
<?php

declare(strict_types=1);

namespace Vendor\Module\Observer;

use Fom\Annotations\Attribute\Platform;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

#[
    Platform(
        edition: Platform::EDITION_COMMERCE,
        version: '2.4.4',
        comparison: Platform::COMPARISON_LESS_THAN
    ),
    Platform(
        edition: Platform::EDITION_B2B,
        version: '2.4.4',
        comparison: Platform::COMPARISON_LESS_THAN
    )
]
class SomeKindOfActionObserver implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        // ...
    }
}
```

### Example of using [`Operator::OPERATOR_AND`](Attribute/Operator.php#L18)

In rare cases, we may need to use specific conditions when a plugin or an observer must work on different versions and editions at the same time, in this case we can describe several necessary platform conditions and summarize them using [`Operator::OPERATOR_AND`](Attribute/Operator.php#L18). In this case, if at least one of the conditions is met, our observer or plugin will be executed.

In the example below, the observer will be executed on `Magento Community Edition` `=` `2.4.3` or on `Magento Commerce Edition` `=` `2.4.2`, on any other versions or editions, this observer will be disabled.

```php
<?php

declare(strict_types=1);

namespace Vendor\Module\Observer;

use Fom\Annotations\Attribute\Operator;
use Fom\Annotations\Attribute\Platform;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

#[
    Platform(
        edition: Platform::EDITION_COMMUNITY,
        version: '2.4.3',
        comparison: Platform::COMPARISON_EQUAL
    ),
    Platform(
        edition: Platform::EDITION_COMMERCE,
        version: '2.4.2',
        comparison: Platform::COMPARISON_LESS_THAN
    ),
    Operator(operator: Operator::OPERATOR_OR)
]
class SomeKindOfActionObserver implements ObserverInterface
{
    public function execute(Observer $observer): void
    {
        // ...
    }
}
```

## Additional Tools

- [PHP Annotations](https://plugins.jetbrains.com/plugin/7320-php-annotations) - Extends PhpStorm to support annotations in DocBlocks.

## Copyright

Copyright © FriendsOfMagento