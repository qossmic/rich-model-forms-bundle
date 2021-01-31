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

namespace Qossmic\RichModelForms\Tests\Fixtures\Form;

use Qossmic\RichModelForms\DataMapper\PropertyMapperInterface;

/**
 * @author Christian Flothmann <christian.flothmann@qossmic.com>
 */
class CancellationDateMapper implements PropertyMapperInterface
{
    public function readPropertyValue($data)
    {
        return $data->cancelledFrom();
    }

    public function writePropertyValue($data, $value): void
    {
        $data->cancelFrom($value);
    }
}
