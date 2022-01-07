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

namespace Qossmic\RichModelForms\DataTransformer;

use Qossmic\RichModelForms\ExceptionHandling\ExceptionHandlerRegistry;
use Qossmic\RichModelForms\ExceptionHandling\ExceptionToErrorMapperTrait;
use Qossmic\RichModelForms\Instantiator\ViewDataInstantiator;
use Symfony\Component\Form\ButtonBuilder;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Christian Flothmann <christian.flothmann@qossmic.com>
 */
class ValueObjectTransformer implements DataTransformerInterface
{
    use ExceptionToErrorMapperTrait;

    private $propertyAccessor;
    private $form;

    public function __construct(ExceptionHandlerRegistry $exceptionHandlerRegistry, PropertyAccessorInterface $propertyAccessor, FormBuilderInterface $form)
    {
        $this->exceptionHandlerRegistry = $exceptionHandlerRegistry;
        $this->propertyAccessor = $propertyAccessor;
        $this->form = $form;
    }

    /**
     * @param mixed $value
     *
     * @return string|array<string,string>|null
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if ($this->form->getCompound()) {
            $viewData = [];

            /** @var string $name */
            foreach ($this->form as $name => $child) {
                if ($child instanceof ButtonBuilder) {
                    continue;
                }

                if (!$child->getOption('mapped')) {
                    continue;
                }

                $viewData[$name] = $this->getPropertyValue($child, $value);
            }

            return $viewData;
        }

        return $this->getPropertyValue($this->form, $value);
    }

    /**
     * @return object
     */
    public function reverseTransform($value)
    {
        try {
            return (new ViewDataInstantiator($this->form, $value))->instantiateObject();
        } catch (\Throwable $e) {
            $error = $this->mapExceptionToError($this->form, $value, $e);

            if (null !== $error) {
                throw new TransformationFailedException(strtr($error->getMessageTemplate(), $error->getParameters()), 0, $e);
            }

            throw $e;
        }
    }

    /**
     * @param object $object
     *
     * @return string
     */
    private function getPropertyValue(FormBuilderInterface $form, $object)
    {
        if (null !== $form->getPropertyPath()) {
            return $this->propertyAccessor->getValue($object, $form->getPropertyPath());
        }

        $readPropertyPath = $form->getFormConfig()->getOption('read_property_path') ?? $form->getName();

        if ($readPropertyPath instanceof \Closure) {
            return $readPropertyPath($object);
        }

        return $this->propertyAccessor->getValue($object, $readPropertyPath);
    }
}
