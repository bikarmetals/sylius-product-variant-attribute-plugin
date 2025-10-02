<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Twig\Component;

use Sylius\Bundle\AdminBundle\Twig\Component\ProductVariant\FormComponent as SyliusFormComponent;
use Sylius\Bundle\UiBundle\Twig\Component\LiveCollectionTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeValueInterface;

#[AsLiveComponent]
class ProductVariantFormComponent extends SyliusFormComponent
{
    use ComponentToolsTrait;
    use LiveCollectionTrait;
    use TemplatePropTrait;

    public const ATTRIBUTE_REMOVED_EVENT = 'sylius_admin:product_variant:form:attributed_deleted';

    public const AUTOCOMPLETE_CLEAR_REQUESTED_EVENT = 'sylius_admin.product_variant_attribute_autocomplete.clear_requested';

    /** @var array<string> */
    #[LiveProp(writable: true, hydrateWith: 'hydrateAttributesToBeAdded', dehydrateWith: 'dehydrateAttributesToBeAdded')]
    #[ExposeInTemplate(name: 'attributes_to_be_added')]
    public array $attributesToBeAdded = [];

    /**
     * @param RepositoryInterface<ProductVariantInterface> $productVariantRepository
     * @param ProductVariantFactoryInterface<ProductVariantInterface> $productVariantFactory
     * @param ProductRepositoryInterface<ProductInterface> $productRepository
     */
    public function __construct(
        RepositoryInterface $productVariantRepository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        ProductVariantFactoryInterface $productVariantFactory,
        ProductRepositoryInterface $productRepository,
        protected readonly RepositoryInterface $productVariantAttributeRepository,
    ) {
        parent::__construct($productVariantRepository, $formFactory, $resourceClass, $formClass, $productVariantFactory, $productRepository);
    }

    /**
     * @return array<string, array<string, FormView>>
     */
    #[ExposeInTemplate(name: 'mapped_product_attributes')]
    public function getMappedProductVariantAttributes(): array
    {
        $mappedAttributes = [];

        $attributes = $this->getFormView()->children['attributes'];

        foreach ($attributes->children as $attribute) {
            /** @var ProductVariantAttributeValueInterface */
            $productVariantAttributeValue = $attribute->vars['value'];

            $mappedAttributes[$productVariantAttributeValue->getAttribute()->getCode()][$productVariantAttributeValue->getLocaleCode()] = $attribute;
        }

        return $mappedAttributes;
    }

    #[LiveAction]
    public function applyToAll(#[LiveArg] string $attributeCode, #[LiveArg] string $localeCode): void
    {
        $matchingAttributes = array_filter(
            $this->formValues['attributes'],
            fn (array $value) => $value['attribute'] === $attributeCode && $value['localeCode'] === $localeCode,
        );
        $currentValue = array_pop($matchingAttributes)['value'];

        $this->formValues['attributes'] = array_map(
            fn (array $value) => $value['attribute'] === $attributeCode
                ? ['attribute' => $attributeCode, 'localeCode' => $value['localeCode'], 'value' => $currentValue]
                : $value,
            $this->formValues['attributes'],
        );
    }

    #[LiveAction]
    public function removeAttribute(#[LiveArg] string $attributeCode): void
    {
        $this->formValues['attributes'] = array_filter(
            $this->formValues['attributes'],
            fn (array $value) => $value['attribute'] !== $attributeCode,
        );
        $this->dispatchBrowserEvent(self::ATTRIBUTE_REMOVED_EVENT, ['attributeCode' => $attributeCode]);
    }

    #[LiveAction]
    public function addAttributes(): void
    {
        foreach ($this->attributesToBeAdded as $attributeCode) {
            $productVariantAttribute = $this->productVariantAttributeRepository->findOneBy(['code' => $attributeCode]);

            if (!$productVariantAttribute->isTranslatable()) {
                $this->formValues['attributes'][] = [
                    'attribute' => $attributeCode,
                    'localeCode' => null,
                    'value' => '',
                ];

                continue;
            }

            foreach ($this->formValues['translations'] as $localesCode => $translation) {
                $this->formValues['attributes'][] = [
                    'attribute' => $attributeCode,
                    'localeCode' => $localesCode,
                    'value' => '',
                ];
            }
        }

        $this->dispatchBrowserEvent(self::AUTOCOMPLETE_CLEAR_REQUESTED_EVENT);
    }

    /**
     * @return array<string>
     */
    public function hydrateAttributesToBeAdded(string $value): array
    {
        if ('' === $value) {
            return [];
        }

        return explode(',', $value);
    }

    /**
     * @param array<string> $value
     */
    public function dehydrateAttributesToBeAdded(array $value): string
    {
        return implode(',', $value);
    }

    protected function getDataModelValue(): string
    {
        return 'norender|*';
    }
}
