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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangeProductStockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stock', IntegerType::class)
            ->setDataMapper(new class() implements DataMapperInterface {
                public function mapDataToForms($data, $forms): void
                {
                    foreach ($forms as $form) {
                        if ('stock' === $form->getConfig()->getName()) {
                            $form->setData($data->currentStock());
                        }
                    }
                }

                public function mapFormsToData($forms, &$data): void
                {
                    foreach ($forms as $form) {
                        if ('stock' === $form->getConfig()->getName()) {
                            $data->allocateStock($form->getData());
                        }
                    }
                }
            })
        ;
    }
}
