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

namespace SensioLabs\RichModelForms\Tests\DataMapper;

use PHPUnit\Framework\TestCase;
use SensioLabs\RichModelForms\Extension\RichModelFormsTypeExtension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class PropertyPathDataMapperTest extends TestCase
{
    public function testDataIsNotMappedToFormIfTheFormIsNotMapped()
    {
        $form = $this->createForm();
        $form->setData(new Foo());

        $this->assertSame('bar not mapped data', $form['barNotMapped']->getData());
        $this->assertSame('baz not mapped data', $form['bazNotMapped']->getData());
    }

    public function testTheDataOptionIsMappedToTheFormIfTheDataToBeMappedIsNull()
    {
        $form = $this->createForm();
        $form->setData(null);

        $this->assertSame('bar_data', $form['bar']->getData());
        $this->assertSame('baz_data', $form['baz']->getData());
    }

    public function testTheDataOptionIsMappedToTheFormIfTheDataToBeMappedIsTheEmptyArray()
    {
        $form = $this->createForm([
            'data_class' => null,
        ]);
        $form->setData([]);

        $this->assertSame('bar_data', $form['bar']->getData());
        $this->assertSame('baz_data', $form['baz']->getData());
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testMappingScalarDataToFormsIsRejected()
    {
        $form = $this->createForm([
            'data_class' => null,
        ]);
        $form->setData('foo');
    }

    public function testDataForFieldsWithoutTheReadPropertyPathOptionAreStillMappedUsingTheDecoratedDataMapper()
    {
        $form = $this->createForm();
        $form->setData(new Foo('foo'));

        $this->assertSame('foo', $form['bar']->getData());
    }

    public function testDataToBeMappedIsReadUsingTheReadPropertyPathOption()
    {
        $form = $this->createForm();
        $form->setData(new Foo(null, 'foo'));

        $this->assertSame('foo', $form['baz']->getData());
    }

    public function testSubmittedDataForFieldsWithoutTheWritePropertyPathOptionAreStillMappedUsingTheDecoratedDataMapper()
    {
        $form = $this->createForm();
        $data = new Foo();
        $form->setData($data);
        $form->submit([
            'bar' => 'submitted bar',
        ]);

        $this->assertSame('submitted bar', $data->getBar());
    }

    public function testSubmittedDataForFieldsWithTheWritePropertyPathOptionAreAreMapped()
    {
        $form = $this->createForm();
        $data = new Foo();
        $form->setData($data);
        $form->submit([
            'baz' => 'submitted baz',
        ]);

        $this->assertSame('submitted baz', $data->bazGetter());
    }

    public function testSubmittingNonMappedTypesDoesNotChangeTheUnderlyingData()
    {
        $form = $this->createForm();
        $data = new Foo();
        $form->setData($data);
        $form->submit([
            'barNotMapped' => 'submitted bar',
            'bazNotMapped' => 'submitted baz',
        ]);

        $this->assertSame('bar not mapped', $data->getBarNotMapped());
        $this->assertSame('baz not mapped', $data->bazNotMappedGetter());
    }

    private function createForm(array $options = []): FormInterface
    {
        $formFactory = (new FormFactoryBuilder())
            ->addTypeExtension(new RichModelFormsTypeExtension(PropertyAccess::createPropertyAccessor()))
            ->getFormFactory();

        return $formFactory->create(FooType::class, null, $options);
    }
}

class Foo
{
    private $bar;
    private $baz;
    private $barNotMapped = 'bar not mapped';
    private $bazNotMapped = 'baz not mapped';

    public function __construct(?string $bar = null, ?string $baz = null)
    {
        $this->bar = $bar;
        $this->baz = $baz;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function setBar($bar): void
    {
        $this->bar = $bar;
    }

    public function bazGetter()
    {
        return $this->baz;
    }

    public function bazSetter($baz): void
    {
        $this->baz = $baz;
    }

    public function getBarNotMapped()
    {
        return $this->barNotMapped;
    }

    public function setBarNotMapped($barNotMapped): void
    {
        $this->barNotMapped = $barNotMapped;
    }

    public function bazNotMappedGetter()
    {
        return $this->bazNotMapped;
    }

    public function bazNotMappedSetter($bazNotMapped): void
    {
        $this->bazNotMapped = $bazNotMapped;
    }
}

class FooType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('bar')
            ->add('baz', null, [
                'read_property_path' => 'bazGetter',
                'write_property_path' => 'bazSetter',
            ])
            ->add('barNotMapped', null, [
                'mapped' => false,
            ])
            ->add('bazNotMapped', null, [
                'mapped' => false,
                'read_property_path' => 'bazNotMappedGetter',
                'write_property_path' => 'bazNotMappedSetter',
            ])
        ;
        $builder->get('bar')->setData('bar_data');
        $builder->get('baz')->setData('baz_data');
        $builder->get('barNotMapped')->setData('bar not mapped data');
        $builder->get('bazNotMapped')->setData('baz not mapped data');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Foo::class);
    }
}
