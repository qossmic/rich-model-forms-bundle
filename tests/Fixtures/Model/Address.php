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

namespace OpenSC\RichModelForms\Tests\Fixtures\Model;

class Address
{
    private $street;
    private $zipcode;
    private $city;

    public function __construct(string $street, string $zipcode, string $city)
    {
        $this->street = $street;
        $this->zipcode = $zipcode;
        $this->city = $city;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function getCity(): string
    {
        return $this->city;
    }
}
