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

use SensioLabs\RichModelForms\DataMapper\DataMapper;
use SensioLabs\RichModelForms\DataMapper\ExceptionHandler\ExceptionHandlerRegistry;
use SensioLabs\RichModelForms\DataTransformer\ValueObjectTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
final class RichModelFormsTypeExtension extends AbstractTypeExtension
{
    private $propertyAccessor;
    private $exceptionHandlerRegistry;
    private $translator;
    private $translationDomain;

    public function __construct(PropertyAccessorInterface $propertyAccessor, ExceptionHandlerRegistry $exceptionHandlerRegistry, TranslatorInterface $translator = null, string $translationDomain = null)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->exceptionHandlerRegistry = $exceptionHandlerRegistry;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (null !== $options['factory']) {
            $builder->addViewTransformer(new ValueObjectTransformer($this->propertyAccessor, $builder));
        }

        if (null === $dataMapper = $builder->getDataMapper()) {
            return;
        }

        $builder->setDataMapper(new DataMapper($dataMapper, $this->propertyAccessor, $this->exceptionHandlerRegistry, $this->translator, $this->translationDomain));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('read_property_path', null);
        $resolver->setAllowedTypes('read_property_path', ['string', 'null']);

        $resolver->setDefault('write_property_path', null);
        $resolver->setAllowedTypes('write_property_path', ['string', 'null', \Closure::class]);

        $resolver->setNormalizer('read_property_path', function (Options $options, ?string $value) {
            if (null !== $value && null === $options['write_property_path']) {
                throw new InvalidConfigurationException('Cannot use "read_property_path" without "write_property_path".');
            }

            if (null !== $options['write_property_path'] && null === $value) {
                throw new InvalidConfigurationException('Cannot use "write_property_path" without "read_property_path".');
            }

            return $value;
        });

        $resolver->setDefault('exception_handling_strategy', ['type_error', 'fallback']);

        $resolver->setNormalizer('exception_handling_strategy', function (Options $options, $value) {
            $value = (array) $value;

            foreach ($value as $strategy) {
                if (!$this->exceptionHandlerRegistry->has($strategy)) {
                    throw new InvalidConfigurationException(sprintf('The "%s" error handling strategy is not registered.', $strategy));
                }
            }

            return $value;
        });

        $resolver->setDefault('factory', null);
        $resolver->setAllowedTypes('factory', ['string', 'array', 'null', \Closure::class]);

        $resolver->setNormalizer('factory', function (Options $options, $value) {
            if (\is_string($value) && !class_exists($value)) {
                throw new InvalidConfigurationException(sprintf('The configured value for the "factory" option is not a valid class name ("%s" given).', $value));
            }

            if (\is_array($value) && !\is_callable($value)) {
                throw new InvalidConfigurationException(sprintf('An array used for the "factory" option must be a valid callable.', $value));
            }

            return $value;
        });
    }

    public function getExtendedType(): string
    {
        return FormType::class;
    }
}
