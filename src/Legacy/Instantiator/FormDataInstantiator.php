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

namespace SensioLabs\RichModelForms\Instantiator;

trigger_deprecation('sensiolabs-de/rich-model-forms-bundle', '0.8', sprintf('The "%s\FormDataInstantiator" class is deprecated. Use "%s" instead.', __NAMESPACE__, \Qossmic\RichModelForms\Instantiator\FormDataInstantiator::class));

class_alias(\Qossmic\RichModelForms\Instantiator\FormDataInstantiator::class, __NAMESPACE__.'\FormDataInstantiator');

if (false) {
    class FormDataInstantiator extends \Qossmic\RichModelForms\Instantiator\FormDataInstantiator
    {
    }
}
