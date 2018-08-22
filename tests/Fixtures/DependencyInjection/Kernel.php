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

use SensioLabs\RichModelForms\RichModelFormsBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new RichModelFormsBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->setParameter('kernel.secret', __FILE__);
            $container->setAlias('test.sensiolabs.rich_model_forms.exception_handler.registry', new Alias('sensiolabs.rich_model_forms.exception_handler.registry', true));
        });
    }

    public function getCacheDir(): string
    {
        return sprintf('%s/RichModelForms/%d/cache', sys_get_temp_dir(), self::VERSION_ID);
    }

    public function getLogDir(): string
    {
        return sprintf('%s/RichModelForms/%d/log', sys_get_temp_dir(), self::VERSION_ID);
    }
}
