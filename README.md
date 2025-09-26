<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

# UmanIT

## Sylius Product Variant Attribute Plugin

Add attributes on your products variants.

## Install

Install the plugin with composer

`$ composer require umanit/sylius-product-variant-attribute-plugin`

Register the plugin to your `config/bundles.php`

```php
<?php

return [
    // ...
    Umanit\SyliusProductVariantAttributePlugin\UmanitSyliusProductVariantAttributePlugin::class => ['all' => true],
];
```

Import the configuration file, for example in `config/packages/umanit_sylius_product_variant_attribute_plugin.yaml`

```yaml
imports:
    - { resource: '@UmanitSyliusProductVariantAttributePlugin/Resources/config/config.yaml' }
```

Import the routing file, for example in `config/routes/sylius_admin.yaml`

```yaml
umanit_sylius_product_variant_attribute_plugin:
    resource: '@UmanitSyliusProductVariantAttributePlugin/Resources/config/admin_routing.yaml'
    prefix: /admin
```

Update your `ProductVariant` entity by implementing the `ProductVariantInterface` and using the `ProductVariantTrait`

```php
<?php

declare(strict_types=1);

namespace App\Entity\Product;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantInterface;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_product_variant")
 */
class ProductVariant extends BaseProductVariant implements ProductVariantInterface
{
    use ProductVariantTrait {
        __construct as attributesConstruct;
    }

    public function __construct()
    {
        parent::__construct();

        $this->attributesConstruct();
    }

    protected function createTranslation(): ProductVariantTranslationInterface
    {
        return new ProductVariantTranslation();
    }
}
```

Finally, don't forget to update your database!

```bash
php bin/console doctrine:migrations:migrate
```

## Usage

Like in product edition, variants now has an `Attributes` tab in which you can add attributes. The operation and
possibilities are the same as with the existing attributes.

The variant attributes list differ from the existing one used for products. A new entry is added to the `Catalog` menu
in order to manage this new list.

By default, the existing entry `Attributes` is renamed to `Products attributes`. You can change this behaviour by
defining the following configuration:

```yaml
umanit_sylius_product_variant_attribute_plugin:
    rename_product_attribute_menu_entry: false
```

## Development
1. Install [DDEV](https://ddev.com/)
2. Run `ddev start` to launch the project
3. Setup the project. Run `ddev ssh` to enter the container and run the following actions:
```bash
/var/www/html $ php bin/create_node_symlink.php
/var/www/html $ composer install
/var/www/html $ cd tests/Application
# You must create an admin user with email "admin@example.com" and password "qwerty"
/var/www/html/tests/Application $ bin/console sylius:install --fixture-suite=default
/var/www/html/tests/Application $ bin/console assets:install
/var/www/html/tests/Application $ yarn install
/var/www/html/tests/Application $ yarn build
```
4. Exit the container and run the tests:
```bash
$ ddev composer test # Run phpspec tests
$ export DISPLAY=:0  # Important before the E2E tests
$ xhost +            # Important before the E2E tests
$ ddev cypress-run   # Run Cypress E2E tests
```
