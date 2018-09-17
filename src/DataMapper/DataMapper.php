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

use SensioLabs\RichModelForms\ExceptionHandling\FormExceptionHandler;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
final class DataMapper implements DataMapperInterface
{
    private $dataMapper;
    private $propertyAccessor;
    private $formExceptionHandler;

    public function __construct(DataMapperInterface $dataMapper, PropertyAccessorInterface $propertyAccessor, FormExceptionHandler $formExceptionHandler)
    {
        $this->dataMapper = $dataMapper;
        $this->propertyAccessor = $propertyAccessor;
        $this->formExceptionHandler = $formExceptionHandler;
    }

    public function mapDataToForms($data, $forms): void
    {
        $isDataEmpty = null === $data || [] === $data;

        if (!$isDataEmpty && !\is_array($data) && !\is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or null');
        }

        $formsToBeMapped = [];

        foreach ($forms as $form) {
            $readPropertyPath = $form->getConfig()->getOption('read_property_path');

            if (!$isDataEmpty && $readPropertyPath instanceof \Closure && $form->getConfig()->getMapped()) {
                $form->setData($readPropertyPath($data));
            } elseif (!$isDataEmpty && null !== $readPropertyPath && $form->getConfig()->getMapped()) {
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

        if (!\is_array($data) && !\is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or null');
        }

        foreach ($forms as $form) {
            $forwardToWrappedDataMapper = false;
            $config = $form->getConfig();

            $readPropertyPath = $config->getOption('read_property_path');

            if (null === $writePropertyPath = $config->getOption('write_property_path')) {
                $forwardToWrappedDataMapper = true;
            } elseif (!$config->getMapped() || !$form->isSubmitted() || !$form->isSynchronized() || $form->isDisabled()) {
                // write-back is disabled if the form is not synchronized (transformation failed),
                // if the form was not submitted and if the form is disabled (modification not allowed)
                $forwardToWrappedDataMapper = true;
            } elseif (\is_object($data) && $config->getByReference() && $form->getData() === ($readPropertyPath instanceof \Closure ? $readPropertyPath($data) : $this->propertyAccessor->getValue($data, $readPropertyPath)) && !$writePropertyPath instanceof \Closure) {
                $forwardToWrappedDataMapper = true;
            }

            try {
                if ($forwardToWrappedDataMapper) {
                    $this->dataMapper->mapFormsToData([$form], $data);
                } elseif ($writePropertyPath instanceof \Closure) {
                    $writePropertyPath($data, $form->getData());
                } else {
                    $this->propertyAccessor->setValue($data, $writePropertyPath, $form->getData());
                }
            } catch (\Throwable $e) {
                $this->formExceptionHandler->handleException($form, $data, $e);
            }
        }
    }
}
