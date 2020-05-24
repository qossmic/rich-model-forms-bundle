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

namespace SensioLabs\RichModelForms\Tests\Unit\Extension;

use PHPUnit\Framework\TestCase;
use SensioLabs\RichModelForms\DataMapper\DataMapper;
use SensioLabs\RichModelForms\ExceptionHandling\FormExceptionHandler;
use SensioLabs\RichModelForms\Extension\RichModelFormsTypeExtension;
use SensioLabs\RichModelForms\Tests\ExceptionHandlerRegistryTrait;
use SensioLabs\RichModelForms\Tests\Fixtures\Model\GrossPrice;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
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
        $exceptionHandlerRegistry = $this->createExceptionHandlerRegistry();
        $this->extension = new RichModelFormsTypeExtension(PropertyAccess::createPropertyAccessor(), $exceptionHandlerRegistry, new FormExceptionHandler($exceptionHandlerRegistry));
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
        $resolvedOptions = $this->configureOptions()->resolve([]);

        $this->assertArrayHasKey('read_property_path', $resolvedOptions);
        $this->assertArrayHasKey('write_property_path', $resolvedOptions);
        $this->assertNull($resolvedOptions['read_property_path']);
        $this->assertNull($resolvedOptions['write_property_path']);
    }

    public function testReadPropertyPathAndWritePropertyPathCanBeConfigured(): void
    {
        $resolvedOptions = $this->configureOptions()->resolve([
            'read_property_path' => 'foo',
            'write_property_path' => 'bar',
        ]);

        $this->assertArrayHasKey('read_property_path', $resolvedOptions);
        $this->assertArrayHasKey('write_property_path', $resolvedOptions);
        $this->assertSame('foo', $resolvedOptions['read_property_path']);
        $this->assertSame('bar', $resolvedOptions['write_property_path']);
    }

    public function testErrorHandlerMustReferenceExistingStrategies(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->configureOptions()->resolve([
            'exception_handling_strategy' => 'unknown',
        ]);
    }

    public function testSingleErrorHandlerCanBeConfigured(): void
    {
        $resolvedOptions = $this->configureOptions()->resolve([
            'exception_handling_strategy' => 'type_error',
        ]);

        $this->assertSame(['type_error'], $resolvedOptions['exception_handling_strategy']);
    }

    /**
     * @group legacy
     */
    public function testExpectedExceptionAndExceptionHandlingStrategyCannotBeUsedAtTheSameTime(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->configureOptions()->resolve([
            'expected_exception' => [\InvalidArgumentException::class, \LogicException::class],
            'exception_handling_strategy' => 'type_error',
        ]);
    }

    public function testHandleExceptionAndExceptionHandlingStrategyCannotBeUsedAtTheSameTime(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->configureOptions()->resolve([
            'handle_exception' => [\InvalidArgumentException::class, \LogicException::class],
            'exception_handling_strategy' => 'type_error',
        ]);
    }

    /**
     * @group legacy
     */
    public function testExpectedExceptionAndHandleExceptionCannotBeUsedAtTheSameTime(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->configureOptions()->resolve([
            'expected_exception' => [\InvalidArgumentException::class, \LogicException::class],
            'handle_exception' => [\InvalidArgumentException::class, \LogicException::class],
        ]);
    }

    /**
     * @group legacy
     */
    public function testHandleExceptionValueDefaultsToExpectedExceptionValue(): void
    {
        $resolvedOptions = $this->configureOptions()->resolve([
            'expected_exception' => [\InvalidArgumentException::class, \LogicException::class],
        ]);

        $this->assertSame([\InvalidArgumentException::class, \LogicException::class], $resolvedOptions['handle_exception']);
    }

    public function testDefaultExceptionHandlingStrategyWhenExpectedExceptionIsNotConfigured(): void
    {
        $resolvedOptions = $this->configureOptions()->resolve();

        $this->assertSame(['type_error', 'fallback'], $resolvedOptions['exception_handling_strategy']);
    }

    public function testItExtendsTheBaseFormType(): void
    {
        $this->assertSame(FormType::class, $this->extension->getExtendedType());
    }

    public function testFactoryStringsMustReferenceExistingClasses(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->configureOptions()->resolve([
            'factory' => __NAMESPACE__.'\\NotExistent',
        ]);
    }

    public function testFactoryArraysMustBeCallables(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->configureOptions()->resolve([
            'factory' => [GrossPrice::class, 'createGrossPrice'],
        ]);
    }

    public function testImmutableObjectsNeedFactories(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->configureOptions()->resolve([
            'immutable' => true,
        ]);
    }

    public function testImmutableObjectsDoNotWorkWithDataClasses(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->configureOptions()->resolve([
            'data_class' => GrossPrice::class,
            'factory' => GrossPrice::class,
            'immutable' => true,
        ]);
    }

    private function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $this->extension->buildForm($formBuilder, $this->configureOptions()->resolve($options));
    }

    private function configureOptions()
    {
        $resolver = new OptionsResolver();
        (new FormType())->configureOptions($resolver);
        $this->extension->configureOptions($resolver);

        return $resolver;
    }
}
