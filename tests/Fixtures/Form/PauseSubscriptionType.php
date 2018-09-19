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

use SensioLabs\RichModelForms\Tests\Fixtures\Model\Subscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PauseSubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('state', ChoiceType::class, [
                'choices' => [
                    'active' => true,
                    'paused' => false,
                ],
                'read_property_path' => 'isSuspended',
                'write_property_path' => function (Subscription $subscription, $submittedData): void {
                    if (true === $submittedData) {
                        $subscription->reactivate();
                    } elseif (false === $submittedData) {
                        $subscription->suspend();
                    } else {
                        throw new \LogicException('This value is not valid.');
                    }
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Subscription::class);
    }
}
