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

use Qossmic\RichModelForms\Tests\Fixtures\Model\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', AddressType::class, [
                'read_property_path' => $options['address_read_property_path'],
                'write_property_path' => $options['address_write_property_path'],
            ])
            ->add('trackingNumber', TextType::class, [
                'read_property_path' => $options['tracking_number_read_property_path'],
                'write_property_path' => $options['tracking_number_write_property_path'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Order::class);
        $resolver->setDefault('address_read_property_path', 'getShippingAddress');
        $resolver->setDefault('address_write_property_path', 'ship');
        $resolver->setDefault('tracking_number_read_property_path', 'getTrackingNumber');
        $resolver->setDefault('tracking_number_write_property_path', 'ship');
    }
}
