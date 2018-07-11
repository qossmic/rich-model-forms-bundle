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

namespace SensioLabs\RichModelForms\Extension;

use SensioLabs\RichModelForms\DataMapper\PropertyPathDataMapper;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
final class RichModelFormsTypeExtension extends AbstractTypeExtension
{
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (null === $dataMapper = $builder->getDataMapper()) {
            return;
        }

        $builder->setDataMapper(new PropertyPathDataMapper($dataMapper, $this->propertyAccessor));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('read_property_path', null);
        $resolver->setAllowedTypes('read_property_path', ['string', 'null']);

        $resolver->setDefault('write_property_path', null);
        $resolver->setAllowedTypes('write_property_path', ['string', 'null']);

        $resolver->setNormalizer('read_property_path', function (Options $options, ?string $value) {
            if (null !== $value && null === $options['write_property_path']) {
                throw new InvalidConfigurationException('Cannot use "read_property_path" without "write_property_path".');
            }

            if (null !== $options['write_property_path'] && null === $value) {
                throw new InvalidConfigurationException('Cannot use "write_property_path" without "read_property_path".');
            }

            return $value;
        });
    }

    public function getExtendedType(): string
    {
        return FormType::class;
    }
}
