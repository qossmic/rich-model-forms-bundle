<?php

/*
 * This file is part of the RichModelFormsBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@sensiolabs.de>
 * (c) Christopher Hertel <christopher.hertel@sensiolabs.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace spec\SensioLabs\RichModelForms;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class RichModelFormsBundleSpec extends ObjectBehavior
{
    public function it_is_a_bundle()
    {
        $this->shouldHaveType(BundleInterface::class);
    }
}
