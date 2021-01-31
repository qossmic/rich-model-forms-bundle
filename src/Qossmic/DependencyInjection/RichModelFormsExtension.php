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

namespace Qossmic\RichModelForms\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\AliasDeprecatedPublicServicesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Christian Flothmann <christian.flothmann@qossmic.com>
 */
class RichModelFormsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        foreach ($container->getDefinitions() as $id => $definition) {
            if (0 !== strpos($id, 'qossmic.')) {
                continue;
            }

            $aliasId = str_replace('qossmic.', 'sensiolabs.', $id);
            $alias = new Alias($id);

            if (class_exists(AliasDeprecatedPublicServicesPass::class)) {
                /* @phpstan-ignore-next-line */
                $alias->setDeprecated('sensiolabs/rich-model-forms-bundle', '0.8', sprintf('The "%%alias_id%% alias is deprecated, use "%s" instead.', $id));
            } else {
                /* @phpstan-ignore-next-line */
                $alias->setDeprecated(true, sprintf('Since sensiolabs/rich-model-forms-bundle 0.8: The "%%alias_id%% alias is deprecated, use "%s" instead.', $id));
            }

            $container->setAlias($aliasId, $alias);
        }
    }
}
