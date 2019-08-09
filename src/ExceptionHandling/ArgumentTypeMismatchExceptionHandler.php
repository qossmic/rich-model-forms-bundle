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

namespace SensioLabs\RichModelForms\ExceptionHandling;

use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
final class ArgumentTypeMismatchExceptionHandler implements ExceptionHandlerInterface
{
    private $translator;
    private $translationDomain;

    public function __construct(TranslatorInterface $translator = null, string $translationDomain = null)
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    public function getError(FormConfigInterface $formConfig, $data, \Throwable $e): ?FormError
    {
        if ($e instanceof \TypeError) {
            // we are not interested in type errors that are not related to argument type mismatches
            if (0 !== strpos($e->getMessage(), 'Argument ')) {
                return null;
            }

            // code for extracting the expected type borrowed from the error handling in the Symfony PropertyAccess component
            if (false !== $pos = strpos($e->getMessage(), 'must be of the type ')) {
                $pos += 20;
            } elseif (false !== $pos = strpos($e->getMessage(), 'must be an instance of ')) {
                $pos += 23;
            } else {
                $pos = strpos($e->getMessage(), 'must implement interface ') + 25;
            }

            $parameters = [
                '{{ type }}' => substr($e->getMessage(), $pos, strpos($e->getMessage(), ',', $pos) - $pos),
            ];

            return $this->buildFormError($e, $parameters);
        }

        // type errors that are triggered when the property accessor performs the write call are wrapped in an
        // InvalidArgumentException by the PropertyAccess component
        if ($e instanceof InvalidArgumentException) {
            return $this->buildFormError($e, [
                '{{ type }}' => substr($e->getMessage(), 27, strpos($e->getMessage(), '",') - 27),
            ]);
        }

        return null;
    }

    private function buildFormError(\Throwable $e, array $parameters): FormError
    {
        $messageTemplate = 'This value should be of type {{ type }}.';

        if (null !== $this->translator) {
            $message = $this->translator->trans($messageTemplate, $parameters, $this->translationDomain);
        } else {
            $message = strtr($messageTemplate, $parameters);
        }

        return new FormError($message, $messageTemplate, $parameters, null, $e);
    }
}
