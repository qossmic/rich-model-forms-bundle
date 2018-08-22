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
use SensioLabs\RichModelForms\Tests\ExceptionHandlerRegistryTrait;
use SensioLabs\RichModelForms\Tests\Fixtures\Model\GrossPrice;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class RichModelFormsTypeExtensionTest extends TestCase
{
    use ExceptionHandlerRegistryTrait;

    private $extension;

    protected function setUp(): void
    {
        $this->extension = new RichModelFormsTypeExtension(PropertyAccess::createPropertyAccessor(), $this->createExceptionHandlerRegistry());
    }

    public function testNoDataMapperWillBeSetIfNoneWasConfigured(): void
    {
        $formBuilder = (new FormFactoryBuilder())->getFormFactory()->createBuilder(FormType::class, null, ['compound' => false]);
        $this->buildForm($formBuilder, []);

        $this->assertNull($formBuilder->getDataMapper());
    }

    public function testPreConfiguredDataMappersWillBeReplaced(): void
    {
        $formBuilder = (new FormFactoryBuilder())->getFormFactory()->createBuilder();
        $this->buildForm($formBuilder, []);

        $this->assertInstanceOf(DataMapper::class, $formBuilder->getDataMapper());
    }

    public function testReadPropertyPathAndWritePropertyPathAreBothNullByDefault(): void
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
    public function testReadPropertyPathCannotBeConfiguredWithoutWritePropertyPath(): void
    {
        $resolver = new OptionsResolver();
        $this->extension->configureOptions($resolver);
        $resolver->resolve(['read_property_path' => 'foo']);
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidConfigurationException
     */
    public function testWritePropertyPathCannotBeConfiguredWithoutReadPropertyPath(): void
    {
        $resolver = new OptionsResolver();
        $this->extension->configureOptions($resolver);
        $resolver->resolve(['write_property_path' => 'foo']);
    }

    public function testReadPropertyPathAndWritePropertyPathCanBeConfigured(): void
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

    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidConfigurationException
     */
    public function testErrorHandlerMustReferenceExistingStrategies(): void
    {
        $resolver = new OptionsResolver();
        $this->extension->configureOptions($resolver);
        $resolver->resolve([
            'exception_handling_strategy' => 'unknown',
        ]);
    }

    public function testSingleErrorHandlerCanBeConfigured(): void
    {
        $resolver = new OptionsResolver();
        $this->extension->configureOptions($resolver);
        $resolvedOptions = $resolver->resolve([
            'exception_handling_strategy' => 'type_error',
        ]);

        $this->assertSame(['type_error'], $resolvedOptions['exception_handling_strategy']);
    }

    public function testItExtendsTheBaseFormType(): void
    {
        $this->assertSame(FormType::class, $this->extension->getExtendedType());
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidConfigurationException
     */
    public function testFactoryStringsMustReferenceExistingClasses(): void
    {
        $resolver = new OptionsResolver();
        $this->extension->configureOptions($resolver);
        $resolver->resolve([
            'factory' => __NAMESPACE__.'\\NotExistent',
        ]);
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidConfigurationException
     */
    public function testFactoryArraysMustBeCallables(): void
    {
        $resolver = new OptionsResolver();
        $this->extension->configureOptions($resolver);
        $resolver->resolve([
            'factory' => [GrossPrice::class, 'createGrossPrice'],
        ]);
    }

    private function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $resolver = new OptionsResolver();
        $this->extension->configureOptions($resolver);

        $this->extension->buildForm($formBuilder, $resolver->resolve($options));
    }
}
