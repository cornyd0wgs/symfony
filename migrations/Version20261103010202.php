<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261103010202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(20) NOT NULL, createdat DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_BA388B7A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE cart_item (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, cart_id INT NOT NULL, variant_id INT DEFAULT NULL, INDEX IDX_F0FE25271AD5CDBF (cart_id), INDEX IDX_F0FE25273B69A9AF (variant_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(150) NOT NULL, description LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, order_number VARCHAR(40) NOT NULL, status VARCHAR(30) NOT NULL, subtotal NUMERIC(12, 2) NOT NULL, total_amount NUMERIC(12, 2) NOT NULL, shipping_recipient VARCHAR(150) NOT NULL, shpping_phone VARCHAR(150) DEFAULT NULL, shipping_address VARCHAR(255) NOT NULL, shippingfee NUMERIC(12, 2) NOT NULL, shipping_city VARCHAR(120) NOT NULL, shipping_province VARCHAR(120) NOT NULL, shipping_postal_code VARCHAR(20) DEFAULT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, product_name VARCHAR(180) NOT NULL, sku VARCHAR(100) NOT NULL, unitprice NUMERIC(12, 2) NOT NULL, quantity INT NOT NULL, linetotal NUMERIC(12, 2) NOT NULL, order_ref_id INT NOT NULL, variant_id INT DEFAULT NULL, INDEX IDX_52EA1F09E238517C (order_ref_id), INDEX IDX_52EA1F093B69A9AF (variant_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(180) NOT NULL, slug VARCHAR(200) NOT NULL, sku VARCHAR(80) NOT NULL, description LONGTEXT DEFAULT NULL, baseprice NUMERIC(12, 2) DEFAULT NULL, saleprice NUMERIC(12, 2) DEFAULT NULL, status VARCHAR(20) NOT NULL, isfeatured TINYINT NOT NULL, category_id INT DEFAULT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE product_variant (id INT AUTO_INCREMENT NOT NULL, variantname VARCHAR(120) DEFAULT NULL, size VARCHAR(50) DEFAULT NULL, color VARCHAR(255) DEFAULT NULL, sku VARCHAR(100) NOT NULL, priceadjustment NUMERIC(12, 2) NOT NULL, stockquantity INT NOT NULL, status VARCHAR(20) NOT NULL, product_id INT NOT NULL, INDEX IDX_209AA41D4584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, rating SMALLINT NOT NULL, comment LONGTEXT NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, product_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_794381C64584665A (product_id), INDEX IDX_794381C6A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(100) NOT NULL, lastname VARCHAR(100) NOT NULL, phone VARCHAR(30) DEFAULT NULL, status VARCHAR(20) NOT NULL, createdat DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25271AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE25273B69A9AF FOREIGN KEY (variant_id) REFERENCES product_variant (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09E238517C FOREIGN KEY (order_ref_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F093B69A9AF FOREIGN KEY (variant_id) REFERENCES product_variant (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product_variant ADD CONSTRAINT FK_209AA41D4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C64584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A76ED395');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25271AD5CDBF');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE25273B69A9AF');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F09E238517C');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F093B69A9AF');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product_variant DROP FOREIGN KEY FK_209AA41D4584665A');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C64584665A');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE cart_item');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_variant');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
