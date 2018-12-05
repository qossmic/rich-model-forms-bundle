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

namespace SensioLabs\RichModelForms\Tests\Fixtures\Form;

use SensioLabs\RichModelForms\DataMapper\PropertyMapper;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
class CancellationDateMapper implements PropertyMapper
{
    public function readPropertyValue($model)
    {
        return $model->cancelledFrom();
    }

    public function writePropertyValue($model, $value): void
    {
        $model->cancelFrom($value);
    }
}
