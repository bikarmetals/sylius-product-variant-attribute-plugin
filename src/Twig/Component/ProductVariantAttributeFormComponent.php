<?php
declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Twig\Component;

use Sylius\Bundle\UiBundle\Twig\Component\LiveCollectionTrait;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeInterface;

// TODO: Check if this component can be deleted as it's a copy of sylius_admin:product_attribute:form or extend it from the base one
#[AsLiveComponent]
class ProductVariantAttributeFormComponent
{
    use LiveCollectionTrait;
    use TemplatePropTrait;

    /** @use ResourceFormComponentTrait<ProductAttributeInterface> */
    use ResourceFormComponentTrait {
        initialize as public __construct;
    }

    #[LiveProp(fieldName: 'type')]
    public ?string $type = null;

    protected function createResource(): ProductVariantAttributeInterface
    {
        return new $this->resourceClass();
    }

    protected function instantiateForm(): FormInterface
    {
        $this->resource->setType($this->type);

        return $this->formFactory->create($this->formClass, $this->resource);
    }
}
