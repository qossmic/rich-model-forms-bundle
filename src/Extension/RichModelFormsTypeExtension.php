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
use SensioLabs\RichModelForms\DataMapper\ExceptionHandler\ArgumentTypeMismatchExceptionHandler;
use SensioLabs\RichModelForms\DataMapper\ExceptionHandler\ChainExceptionHandler;
use SensioLabs\RichModelForms\DataMapper\ExceptionHandler\FallbackExceptionHandler;
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
    private $translator;
    private $translationDomain;

    public function __construct(PropertyAccessorInterface $propertyAccessor, TranslatorInterface $translator = null, string $translationDomain = null)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (null === $dataMapper = $builder->getDataMapper()) {
            return;
        }

        $exceptionHandler = new ChainExceptionHandler([
            new ArgumentTypeMismatchExceptionHandler($this->translator, $this->translationDomain),
            new FallbackExceptionHandler($this->translator, $this->translationDomain),
        ]);

        $builder->setDataMapper(new DataMapper($dataMapper, $this->propertyAccessor, $exceptionHandler, $this->translator, $this->translationDomain));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('read_property_path', null);
        $resolver->setAllowedTypes('read_property_path', ['string', 'null']);

        $resolver->setDefault('write_property_path', null);
        $resolver->setAllowedTypes('write_property_path', ['string', 'null', 'Closure']);

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
