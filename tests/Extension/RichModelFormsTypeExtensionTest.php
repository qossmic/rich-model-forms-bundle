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

namespace SensioLabs\RichModelForms\Tests\Extension;

use PHPUnit\Framework\TestCase;
use SensioLabs\RichModelForms\DataMapper\DataMapper;
use SensioLabs\RichModelForms\Extension\RichModelFormsTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class RichModelFormsTypeExtensionTest extends TestCase
{
    private $extension;

    protected function setUp()
    {
        $this->extension = new RichModelFormsTypeExtension(PropertyAccess::createPropertyAccessor());
    }

    public function testNoDataMapperWillBeSetIfNoneWasConfigured()
    {
        $formBuilder = (new FormFactoryBuilder())->getFormFactory()->createBuilder(FormType::class, null, ['compound' => false]);
        $this->extension->buildForm($formBuilder, []);

        $this->assertNull($formBuilder->getDataMapper());
    }

    public function testPreConfiguredDataMappersWillBeReplaced()
    {
        $formBuilder = (new FormFactoryBuilder())->getFormFactory()->createBuilder();
        $this->extension->buildForm($formBuilder, []);

        $this->assertInstanceOf(DataMapper::class, $formBuilder->getDataMapper());
    }

    public function testReadPropertyPathAndWritePropertyPathAreBothNullByDefault()
    {
        $resolver = new OptionsResolver();
        $this->extension->configureOptions($resolver);
        $resolvedOptions = $resolver->resolve([]);

        $this->assertArrayHasKey('read_property_path', $resolvedOptions);
        $this->assertArrayHasKey('write_property_path', $resolvedOptions);
        $this->assertNull($resolvedOptions['read_property_path']);
        $this->assertNull($resolvedOptions['write_property_path']);
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidConfigurationException
     */
    public function testReadPropertyPathCannotBeConfiguredWithoutWritePropertyPath()
    {
        $resolver = new OptionsResolver();
        $this->extension->configureOptions($resolver);
        $resolver->resolve(['read_property_path' => 'foo']);
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidConfigurationException
     */
    public function testWritePropertyPathCannotBeConfiguredWithoutReadPropertyPath()
    {
        $resolver = new OptionsResolver();
        $this->extension->configureOptions($resolver);
        $resolver->resolve(['write_property_path' => 'foo']);
    }

    public function testReadPropertyPathAndWritePropertyPathCanBeConfigured()
    {
        $resolver = new OptionsResolver();
        $this->extension->configureOptions($resolver);
        $resolvedOptions = $resolver->resolve([
            'read_property_path' => 'foo',
            'write_property_path' => 'bar',
        ]);

        $this->assertArrayHasKey('read_property_path', $resolvedOptions);
        $this->assertArrayHasKey('write_property_path', $resolvedOptions);
        $this->assertSame('foo', $resolvedOptions['read_property_path']);
        $this->assertSame('bar', $resolvedOptions['write_property_path']);
    }

    public function testItExtendsTheBaseFormType()
    {
        $this->assertSame(FormType::class, $this->extension->getExtendedType());
    }
}
