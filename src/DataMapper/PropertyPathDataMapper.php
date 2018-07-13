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

namespace SensioLabs\RichModelForms\DataMapper;

use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormError;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
final class PropertyPathDataMapper implements DataMapperInterface
{
    private $dataMapper;
    private $propertyAccessor;

    public function __construct(DataMapperInterface $dataMapper, PropertyAccessorInterface $propertyAccessor)
    {
        $this->dataMapper = $dataMapper;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function mapDataToForms($data, $forms): void
    {
        $isDataEmpty = null === $data || [] === $data;

        if (!$isDataEmpty && !is_array($data) && !is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or null');
        }

        $formsToBeMapped = [];

        foreach ($forms as $form) {
            $readPropertyPath = $form->getConfig()->getOption('read_property_path');

            if (!$isDataEmpty && null !== $readPropertyPath && $form->getConfig()->getMapped()) {
                $form->setData($this->propertyAccessor->getValue($data, $readPropertyPath));
            } elseif (null !== $readPropertyPath) {
                $form->setData($form->getConfig()->getData());
            } else {
                $formsToBeMapped[] = $form;
            }
        }

        $this->dataMapper->mapDataToForms($data, $formsToBeMapped);
    }

    public function mapFormsToData($forms, &$data): void
    {
        if (null === $data) {
            return;
        }

        if (!is_array($data) && !is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or null');
        }

        $formsToBeMapped = [];

        foreach ($forms as $form) {
            $config = $form->getConfig();

            if (null === $writePropertyPath = $config->getOption('write_property_path')) {
                $formsToBeMapped[] = $form;
                continue;
            }

            $readPropertyPath = $config->getOption('read_property_path');

            // write-back is disabled if the form is not synchronized (transformation failed),
            // if the form was not submitted and if the form is disabled (modification not allowed)
            if (!$config->getMapped() || !$form->isSubmitted() || !$form->isSynchronized() || $form->isDisabled()) {
                $formsToBeMapped[] = $form;
                continue;
            }

            if (is_object($data) && $config->getByReference() && $form->getData() === $this->propertyAccessor->getValue($data, $readPropertyPath) && !$writePropertyPath instanceof \Closure) {
                continue;
            }

            if ($writePropertyPath instanceof \Closure) {
                if (null !== $writePropertyPath = $writePropertyPath($form->getData())) {
                    // The property accessor expects the method to accept exactly one argument for write access. Since
                    // our write option here is chosen based on the submitted value we do not need to (and explicitly do
                    // not want to) pass and value we use the property accessor's read operation which will call the
                    // method without any argument.
                    $this->propertyAccessor->getValue($data, $writePropertyPath);
                } else {
                    $form->addError(new FormError($config->getOption('invalid_message') ?? 'This value is not valid.', null, $config->getOption('invalid_message_parameters') ?? []));
                }
            } else {
                $this->propertyAccessor->setValue($data, $writePropertyPath, $form->getData());
            }
        }

        $this->dataMapper->mapFormsToData($formsToBeMapped, $data);
    }
}
