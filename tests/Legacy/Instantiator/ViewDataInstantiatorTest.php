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

namespace Qossmic\RichModelForms\Tests\Legacy\Instantiator;

use Qossmic\RichModelForms\Tests\Legacy\LegacyClassTestCase;
use SensioLabs\RichModelForms\Instantiator\ViewDataInstantiator;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormConfigInterface;

class ViewDataInstantiatorTest extends LegacyClassTestCase
{
    protected function createInstance(): object
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder->expects($this->any())
            ->method('getFormConfig')
            ->willReturn($this->createMock(FormConfigInterface::class))
        ;

        return new ViewDataInstantiator($formBuilder, []);
    }
}
