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

namespace SensioLabs\RichModelForms\ExceptionHandling;

trigger_deprecation('sensiolabs-de/rich-model-forms-bundle', '0.8', sprintf('The "%s\ChainExceptionHandler" class is deprecated. Use "%s" instead.', __NAMESPACE__, \Qossmic\RichModelForms\ExceptionHandling\ChainExceptionHandler::class));

class_alias(\Qossmic\RichModelForms\ExceptionHandling\ChainExceptionHandler::class, __NAMESPACE__.'\ChainExceptionHandler');

if (false) {
    class ChainExceptionHandler extends \Qossmic\RichModelForms\ExceptionHandling\ChainExceptionHandler
    {
    }
}
