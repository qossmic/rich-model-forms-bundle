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

namespace Qossmic\RichModelForms\Tests\Fixtures\Form;

use Qossmic\RichModelForms\Tests\Fixtures\Model\Price;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeMismatchPriceChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $price = $builder->create('price', IntegerType::class, [
                'handle_exception' => $options['expected_price_exception'],
            ])
            ->addViewTransformer(new CallbackTransformer(
                function ($value) {
                    if (!$value instanceof Price) {
                        return null;
                    }

                    return $value->amount();
                },
                function ($value) {
                    return $value;
                }
            ), true);

        $builder->add($price);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('expected_price_exception', null);
    }
}
