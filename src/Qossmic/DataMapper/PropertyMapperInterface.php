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

namespace Qossmic\RichModelForms\DataMapper;

/**
 * @author Christian Flothmann <christian.flothmann@qossmic.com>
 */
interface PropertyMapperInterface
{
    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function readPropertyValue($data);

    /**
     * @param mixed $data
     * @param mixed $value
     */
    public function writePropertyValue($data, $value): void;
}
