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

namespace Qossmic\RichModelForms\Instantiator;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Christian Flothmann <christian.flothmann@qossmic.com>
 */
class ViewDataInstantiator extends ObjectInstantiator
{
    private FormBuilderInterface $form;
    /** @var array<string,mixed>|bool|int|string */
    private $viewData;
    /** @var array<string,string> */
    private array $formNameForArgument;

    /**
     * @param array<string,mixed>|bool|int|string $viewData
     */
    public function __construct(FormBuilderInterface $form, $viewData)
    {
        parent::__construct($form->getFormConfig()->getOption('factory'));

        $this->form = $form;
        $this->viewData = $viewData;
        $this->formNameForArgument = [];

        foreach ($form as $child) {
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
        if (!\is_array($this->viewData)) {
            return null;
        }

        return $this->viewData[$this->formNameForArgument[$argument]] ?? null;
    }
}
