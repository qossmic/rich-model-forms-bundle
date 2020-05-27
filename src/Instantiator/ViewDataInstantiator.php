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

use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
class ViewDataInstantiator extends ObjectInstantiator
{
    private $form;
    private $viewData;
    private $formNameForArgument;

    /**
     * @param array<string,mixed> $viewData
     */
    public function __construct(FormBuilderInterface $form, $viewData)
    {
        parent::__construct($form->getFormConfig()->getOption('factory'));

        $this->form = $form;
        $this->viewData = $viewData;
        $this->formNameForArgument = [];

        foreach ($form as $name => $child) {
            $this->formNameForArgument[$child->getOption('factory_argument') ?? $child->getName()] = $child->getName();
        }
    }

    protected function isCompoundForm(): bool
    {
        return $this->form->getFormConfig()->getCompound();
    }

    protected function getData()
    {
        return $this->viewData;
    }

    protected function getArgumentData(string $argument)
    {
        return $this->viewData[$this->formNameForArgument[$argument]] ?? null;
    }
}
