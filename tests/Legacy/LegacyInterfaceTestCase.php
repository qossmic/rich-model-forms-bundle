<?php

/*
 * This file is part of the RichModelFormsBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@qossmic.com>
 * (c) Christopher Hertel <mail@christopher-hertel.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Qossmic\RichModelForms\Tests\Legacy;

use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;

abstract class LegacyInterfaceTestCase extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @group legacy
     */
    public function testInterfaceIsDeprecated(): void
    {
        $testedInterfaceName = preg_replace('/^Qossmic\\\\RichModelForms\\\\Tests\\\\Legacy(.+)Test/', 'SensioLabs\\RichModelForms$1', static::class);
        $replacementInterfaceName = str_replace('SensioLabs', 'Qossmic', $testedInterfaceName);

        $this->expectDeprecation(sprintf('Since sensiolabs-de/rich-model-forms-bundle 0.8: The "%s" is deprecated. Use "%s" instead.', $testedInterfaceName, $replacementInterfaceName));

        $object = $this->createMock($testedInterfaceName);

        self::assertInstanceOf($testedInterfaceName, $object);
        self::assertInstanceOf($replacementInterfaceName, $object);
    }
}
