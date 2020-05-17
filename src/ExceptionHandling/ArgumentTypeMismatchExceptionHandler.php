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
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
final class ArgumentTypeMismatchExceptionHandler implements ExceptionHandlerInterface
{
    public function getError(FormConfigInterface $formConfig, $data, \Throwable $e): ?Error
    {
        if ($e instanceof \TypeError) {
            if (0 === strpos($e->getMessage(), 'Argument ')) {
                // code for extracting the expected type borrowed from the error handling in the Symfony PropertyAccess component
                if (false !== $pos = strpos($e->getMessage(), 'must be of the type ')) {
                    $pos += 20;
                } elseif (false !== $pos = strpos($e->getMessage(), 'must be an instance of ')) {
                    $pos += 23;
                } else {
                    $pos = strpos($e->getMessage(), 'must implement interface ') + 25;
                }

                return new Error($e, 'This value should be of type {{ type }}.', [
                    '{{ type }}' => substr($e->getMessage(), $pos, strpos($e->getMessage(), ',', $pos) - $pos),
                ]);
            }

            if (0 === strpos($e->getMessage(), 'Typed property ')) {
                $pos = strpos($e->getMessage(), 'must be ') + 8;

                return new Error($e, 'This value should be of type {{ type }}.', [
                    '{{ type }}' => substr($e->getMessage(), $pos, strpos($e->getMessage(), ',', $pos) - $pos),
                ]);
            }

            // we are not interested in type errors that are not related to argument type nor property type (PHP 7.4+) mismatches
            return null;
        }

        // type errors that are triggered when the property accessor performs the write call are wrapped in an
        // InvalidArgumentException by the PropertyAccess component
        if ($e instanceof InvalidArgumentException) {
            return new Error($e, 'This value should be of type {{ type }}.', [
                '{{ type }}' => substr($e->getMessage(), 27, strpos($e->getMessage(), '",') - 27),
            ]);
        }

        return null;
    }
}
