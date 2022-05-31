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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GrossPriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (null === $options['factory']) {
            throw new InvalidConfigurationException(sprintf('The %s requires a configured value for the "factory" option.', self::class));
        }

        $builder
            ->add('amount', IntegerType::class)
            ->add($options['tax_rate_field_name'], ChoiceType::class, [
                'choices' => [
                    '7%' => 7,
                    '19%' => 19,
                ],
            ] + $options['tax_rate_field_options'])
        ;

        if ($options['extra_field']) {
            $builder->add('extra_field', TextType::class, [
                'mapped' => $options['map_extra_field'],
            ]);
        }

        if ($options['include_button']) {
            $builder->add('submit', SubmitType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', null);
        $resolver->setDefault('extra_field', false);
        $resolver->setDefault('include_button', false);
        $resolver->setDefault('map_extra_field', false);
        $resolver->setDefault('tax_rate_field_name', 'taxRate');
        $resolver->setDefault('tax_rate_field_options', []);
    }
}
