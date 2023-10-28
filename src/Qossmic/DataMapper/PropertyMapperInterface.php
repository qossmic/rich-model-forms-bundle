<?php

/*
 * This file is part of the RichModelFormsBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@qossmic.com>
 * (c) Christopher Hertel <mail@christopher-hertel.de>
 * (c) QOSSMIC GmbH <info@qossmic.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Qossmic\RichModelForms\DataMapper;

/**
 * @author Christian Flothmann <christian.flothmann@qossmic.com>
 */
interface PropertyMapperInterface
{
    public function readPropertyValue(mixed $data): mixed;

    public function writePropertyValue(mixed $data, mixed $value): void;
}
