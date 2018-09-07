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

namespace SensioLabs\RichModelForms\Tests\Fixtures\DependencyInjection;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PublicTestAliasPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $testAliases = [];

        foreach ($container->getDefinitions() as $id => $definition) {
            if (0 === strpos($id, 'sensiolabs.rich_model_forms.')) {
                $container->setAlias('test.'.$id, new Alias($id, true));
                $testAliases['test.'.$id] = $definition->getClass();
            }
        }

        $container->setParameter('sensiolabs.rich_model_forms.test_service_aliases', $testAliases);
    }
}
