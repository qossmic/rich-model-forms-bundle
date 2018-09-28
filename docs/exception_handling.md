Catching Model Exceptions
=========================

To ensure that your model is in a valid state at all times you need to forbid interactions with your model that would
transform it into an invalid state. The exceptions being thrown to achieve that will by default not be caught by the
Form component meaning that any invalid user input leads to "internal server error" responses.

You can use the `expected_exception` option to indicate which exceptions can be triggered when a particular property
is modified. Exceptions that can be thrown inside the constructor must be specified for the whole form:

```php
class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'read_property_path' => 'getName',
                'write_property_path' => 'rename',
                'expected_exception' => ProductException::class,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return $category->getName();
                },
                'read_property_path' => 'getCategory',
                'write_property_path' => 'moveToCategory',
            ])
            ->add('price', PriceType::class, [
                'read_property_path' => 'getPrice',
                'write_property_path' => 'costs',
                'expected_exception' => PriceException::class,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('factory', Product::class);

        // catches exceptions thrown in the constructor
        $resolver->setDefault('expected_exception', [ProductException::class, PriceException::class]);
    }
}
```

Additionally, the bundle will catch all [TypeError instances](http://www.php.net/manual/en/class.typeerror.php) that are
caused by passing invalid types when the submitted data is mapped to your model.
