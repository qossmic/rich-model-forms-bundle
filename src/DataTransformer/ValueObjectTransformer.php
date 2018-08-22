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

namespace SensioLabs\RichModelForms\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
class ValueObjectTransformer implements DataTransformerInterface
{
    private $propertyAccessor;
    private $form;

    public function __construct(PropertyAccessorInterface $propertyAccessor, FormBuilderInterface $form)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->form = $form;
    }

    public function transform($value)
    {
        if ($this->form->getCompound()) {
            $viewData = [];

            foreach ($this->form as $name => $child) {
                $viewData[$name] = $this->getPropertyValue($child, $value);
            }

            return $viewData;
        }

        return $this->getPropertyValue($this->form, $value);
    }

    public function reverseTransform($value)
    {
        if (null === $factory = $this->form->getFormConfig()->getOption('factory')) {
            return $value;
        }

        if ($factory instanceof \Closure) {
            return $factory($value);
        }

        if (\is_string($factory)) {
            $factoryMethod = (new \ReflectionClass($factory))->getConstructor();
            $factoryMethodAsString = $factory.'::__construct';
        } elseif (\is_array($factory)) {
            $class = \is_object($factory[0]) ? \get_class($factory[0]) : $factory[0];
            $factoryMethod = (new \ReflectionMethod($class, $factory[1]));
            $factoryMethodAsString = $class.'::'.$factory[1];
        }

        if (!$factoryMethod->isPublic()) {
            throw new TransformationFailedException(sprintf('The factory method %s() is not public.', (string) $factoryMethodAsString));
        }

        $arguments = [];

        if (\is_array($value)) {
            foreach ($factoryMethod->getParameters() as $parameter) {
                $arguments[] = $value[$parameter->getName()] ?? null;
            }
        } else {
            $arguments[] = $value;
        }

        if (\is_string($factory)) {
            return new $factory(...$arguments);
        }

        if (\is_array($factory)) {
            return $factory(...$arguments);
        }
    }

    private function getPropertyValue(FormBuilderInterface $form, $object)
    {
        if (null !== $form->getPropertyPath()) {
            return $this->propertyAccessor->getValue($object, $form->getPropertyPath());
        }

        return $this->propertyAccessor->getValue($object, new PropertyPath($form->getName()));
    }
}
