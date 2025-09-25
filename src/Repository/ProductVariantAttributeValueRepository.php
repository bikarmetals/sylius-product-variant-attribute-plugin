<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;

class ProductVariantAttributeValueRepository extends EntityRepository implements ProductVariantAttributeValueRepositoryInterface
{
    public function findByJsonChoiceKey(string $choiceKey): array
    {
        /** @var ProductAttributeValueInterface[] */
        return $this
            ->createQueryBuilder('o')
            ->andWhere('o.json LIKE :key')
            ->setParameter('key', '%"' . $choiceKey . '"%')
            ->getQuery()
            ->getResult()
        ;
    }
}
