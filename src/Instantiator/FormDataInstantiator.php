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

namespace SensioLabs\RichModelForms\Instantiator;

use Symfony\Component\Form\FormInterface;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
class FormDataInstantiator extends ObjectInstantiator
{
    private $form;

    /**
     * @param string|\Closure|callable $factory
     */
    public function __construct($factory, FormInterface $form)
    {
        parent::__construct($factory);

        $this->form = $form;
    }

    protected function isCompoundForm(): bool
    {
        return $this->form->getConfig()->getCompound();
    }

    protected function getData()
    {
        if ($this->isCompoundForm()) {
            $data = [];

            foreach ($this->form as $childForm) {
                $data[$childForm->getConfig()->getName()] = $childForm->getData();
            }

            return $data;
        }

        return $this->form->getData();
    }

    protected function getArgumentData(string $argument)
    {
        if (!$this->form->has($argument)) {
            foreach ($this->form as $childForm) {
                $factoryArgument = $childForm->getConfig()->getOption('factory_argument');
                if ((string) $factoryArgument === $argument) {
                    return $childForm->getData();
                }
            }
        }

        return $this->form->get($argument)->getData();
    }
}
