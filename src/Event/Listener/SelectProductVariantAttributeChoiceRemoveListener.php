<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Event\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeInterface;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeValueInterface;
use Umanit\SyliusProductVariantAttributePlugin\Repository\ProductVariantAttributeValueRepositoryInterface;

final class SelectProductVariantAttributeChoiceRemoveListener
{
    /** @var class-string */
    private $productVariantAttributeValueClass;

    /**
     * @param class-string $productVariantAttributeValueClass
     */
    public function __construct(string $productVariantAttributeValueClass)
    {
        $this->productVariantAttributeValueClass = $productVariantAttributeValueClass;
    }

    public function postUpdate(LifecycleEventArgs $event): void
    {
        $productAttributeVariant = $event->getObject();

        if (!$productAttributeVariant instanceof ProductVariantAttributeInterface) {
            return;
        }

        if ($productAttributeVariant->getType() !== SelectAttributeType::TYPE) {
            return;
        }

        $objectManager = $event->getObjectManager();

        $unitOfWork = $objectManager->getUnitOfWork();
        $changeSet = $unitOfWork->getEntityChangeSet($productAttributeVariant);

        $oldChoices = $changeSet['configuration'][0]['choices'] ?? [];
        $newChoices = $changeSet['configuration'][1]['choices'] ?? [];

        $removedChoices = array_diff_key($oldChoices, $newChoices);

        if (!empty($removedChoices)) {
            $this->removeValues($objectManager, array_keys($removedChoices));
        }
    }

    /**
     * @param array|string[] $choiceKeys
     */
    public function removeValues(ObjectManager $entityManager, array $choiceKeys): void
    {
        /** @var ProductVariantAttributeValueRepositoryInterface $productVariantAttributeValueRepository */
        $productVariantAttributeValueRepository = $entityManager->getRepository($this->productVariantAttributeValueClass);

        foreach ($choiceKeys as $choiceKey) {
            $productVariantAttributeValues = $productVariantAttributeValueRepository->findByJsonChoiceKey($choiceKey);

            /** @var ProductVariantAttributeValueInterface $productVariantAttributeValue */
            foreach ($productVariantAttributeValues as $productVariantAttributeValue) {
                $newValue = array_diff($productVariantAttributeValue->getValue(), [$choiceKey]);

                if (!empty($newValue)) {
                    $productVariantAttributeValue->setValue($newValue);

                    continue;
                }

                $entityManager->remove($productVariantAttributeValue);
            }
        }

        $entityManager->flush();
    }
}
