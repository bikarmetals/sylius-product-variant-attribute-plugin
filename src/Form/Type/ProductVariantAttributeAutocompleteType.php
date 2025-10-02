<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Form\Type;

use Sylius\Bundle\AdminBundle\Form\Type\TranslatableAutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;

#[AsEntityAutocompleteField(
    alias: 'sylius_admin_product_variant_attribute',
    route: 'sylius_admin_entity_autocomplete',
)]
final class ProductVariantAttributeAutocompleteType extends AbstractType
{
    public function __construct(
        private readonly string $productVariantAttributeClass,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => $this->productVariantAttributeClass,
            'choice_name' => 'name',
            'choice_value' => 'code',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_product_variant_attribute_autocomplete';
    }

    public function getParent(): string
    {
        return TranslatableAutocompleteType::class;
    }
}
