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

namespace SensioLabs\RichModelForms\DataMapper;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
interface PropertyMapperInterface
{
    public function readPropertyValue($data);

    public function writePropertyValue($data, $value): void;
}
