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

use SensioLabs\RichModelForms\Tests\Fixtures\Model\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $nameOptions = [
            'read_property_path' => 'getName',
            'write_property_path' => 'rename',
        ];

        if ($options['legacy_exception_handling_option']) {
            $nameOptions['expected_exception'] = $options['expected_name_exception'];
        } else {
            $nameOptions['handle_exception'] = $options['expected_name_exception'];
        }

        $builder
            ->add('name', TextType::class, $nameOptions)
            ->add('parent', ChoiceType::class, [
                'choices' => $options['categories'],
                'read_property_path' => function (Category $category): ?Category {
                    if ($category->hasParent()) {
                        return $category->getParent();
                    }

                    return null;
                },
                'write_property_path' => 'getName',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('categories', []);
        $resolver->setDefault('expected_name_exception', null);
        $resolver->setDefault('legacy_exception_handling_option', false);
    }
}
