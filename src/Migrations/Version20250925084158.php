<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Migrations;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250925084158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create product variant attributes tables';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->platform instanceof MySqlPlatform, 'The platform is not MySQL/MariaDB');
        $this->addSql('CREATE TABLE umanit_sylius_product_variant_attribute (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, storage_type VARCHAR(255) NOT NULL, configuration LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, position INT NOT NULL, translatable TINYINT(1) DEFAULT \'1\' NOT NULL, UNIQUE INDEX UNIQ_E1FBCFD277153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE umanit_sylius_product_variant_attribute_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_ADE366922C2AC5D3 (translatable_id), UNIQUE INDEX umanit_sylius_product_variant_attribute_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE umanit_sylius_product_variant_attribute_value (id INT AUTO_INCREMENT NOT NULL, product_variant_id INT NOT NULL, attribute_id INT NOT NULL, locale_code VARCHAR(255) DEFAULT NULL, text_value LONGTEXT DEFAULT NULL, boolean_value TINYINT(1) DEFAULT NULL, integer_value INT DEFAULT NULL, float_value DOUBLE PRECISION DEFAULT NULL, datetime_value DATETIME DEFAULT NULL, date_value DATE DEFAULT NULL, json_value LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_5ABB5DB7A80EF684 (product_variant_id), INDEX IDX_5ABB5DB7B6E62EFA (attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE umanit_sylius_product_variant_attribute_translation ADD CONSTRAINT FK_ADE366922C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES umanit_sylius_product_variant_attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE umanit_sylius_product_variant_attribute_value ADD CONSTRAINT FK_5ABB5DB7A80EF684 FOREIGN KEY (product_variant_id) REFERENCES sylius_product_variant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE umanit_sylius_product_variant_attribute_value ADD CONSTRAINT FK_5ABB5DB7B6E62EFA FOREIGN KEY (attribute_id) REFERENCES umanit_sylius_product_variant_attribute (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(!$this->platform instanceof MySqlPlatform, 'The platform is not MySQL/MariaDB');
        $this->addSql('ALTER TABLE umanit_sylius_product_variant_attribute_translation DROP FOREIGN KEY FK_ADE366922C2AC5D3');
        $this->addSql('ALTER TABLE umanit_sylius_product_variant_attribute_value DROP FOREIGN KEY FK_5ABB5DB7B6E62EFA');
        $this->addSql('DROP TABLE umanit_sylius_product_variant_attribute');
        $this->addSql('DROP TABLE umanit_sylius_product_variant_attribute_translation');
        $this->addSql('DROP TABLE umanit_sylius_product_variant_attribute_value');
    }
}
