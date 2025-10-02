<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Umanit\SyliusProductVariantAttributePlugin\Form\Type\ProductVariantAttributeChoiceType;

// TODO: Check if this controller can be deleted
class ProductVariantAttributeController extends ResourceController
{
    public function renderAttributesAction(Request $request): Response
    {
        /** @var string */
        $template = $request->attributes->get('template');

        $form = $this->get('form.factory')->create(ProductVariantAttributeChoiceType::class, null, [
            'multiple' => true,
        ])
        ;

        return $this->render($template, ['form' => $form->createView()]);
    }

    public function renderAttributeValueFormsAction(Request $request): Response
    {
        /** @var string */
        $template = $request->attributes->get('template');

        $form = $this->get('form.factory')->create(ProductVariantAttributeChoiceType::class, null, [
            'multiple' => true,
        ])
        ;
        $form->handleRequest($request);

        /** @var ?AttributeInterface[] */
        $attributes = $form->getData();
        if (null === $attributes) {
            throw new BadRequestHttpException();
        }

        /** @var string[] */
        $localeCodes = $this->get('sylius.translation_locale_provider')->getDefinedLocalesCodes();

        $forms = [];
        foreach ($attributes as $attribute) {
            $forms[$attribute->getCode()] = $this->getAttributeFormsInAllLocales($attribute, $localeCodes);
        }

        return $this->render($template, [
            'forms' => $forms,
            'count' => $request->query->get('count'),
            'metadata' => $this->metadata,
        ]);
    }
}
