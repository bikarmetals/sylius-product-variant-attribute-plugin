<?php

// Copy of Sylius\Bundle\AdminBundle\Form\Type\ProductVariantType

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Form\Type;

use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType as BaseProductVariantType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductVariantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('attributes', CollectionType::class, [
                'entry_type' => ProductVariantAttributeValueType::class,
                'required' => false,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_product_variant';
    }

    public function getParent(): string
    {
        return BaseProductVariantType::class;
    }
}
