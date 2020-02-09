<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200121142125 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE collection_content (id INT AUTO_INCREMENT NOT NULL, cards_id INT DEFAULT NULL, from_collection_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_AAE94278DC555177 (cards_id), INDEX IDX_AAE94278181D576A (from_collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE collections (id INT AUTO_INCREMENT NOT NULL, users_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_D325D3EE67B3B43D (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, auteur_id INT NOT NULL, posted_on_card_id INT DEFAULT NULL, posted_on_user_id INT DEFAULT NULL, posted_at DATETIME NOT NULL, content LONGTEXT NOT NULL, flagged TINYINT(1) DEFAULT \'0\' NOT NULL, validated TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_5F9E962A60BB6FE6 (auteur_id), INDEX IDX_5F9E962AC237EFDF (posted_on_card_id), INDEX IDX_5F9E962A2F95A66A (posted_on_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, registered_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wishlist_content (id INT AUTO_INCREMENT NOT NULL, cards_id INT DEFAULT NULL, from_wishlist_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_61C9E6D3DC555177 (cards_id), INDEX IDX_61C9E6D365C165A8 (from_wishlist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wishlists (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_4A4C2E1B67B3B43D (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE collection_content ADD CONSTRAINT FK_AAE94278DC555177 FOREIGN KEY (cards_id) REFERENCES cards (card_id)');
        $this->addSql('ALTER TABLE collection_content ADD CONSTRAINT FK_AAE94278181D576A FOREIGN KEY (from_collection_id) REFERENCES collections (id)');
        $this->addSql('ALTER TABLE collections ADD CONSTRAINT FK_D325D3EE67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AC237EFDF FOREIGN KEY (posted_on_card_id) REFERENCES cards (card_id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A2F95A66A FOREIGN KEY (posted_on_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE wishlist_content ADD CONSTRAINT FK_61C9E6D3DC555177 FOREIGN KEY (cards_id) REFERENCES cards (card_id)');
        $this->addSql('ALTER TABLE wishlist_content ADD CONSTRAINT FK_61C9E6D365C165A8 FOREIGN KEY (from_wishlist_id) REFERENCES wishlists (id)');
        $this->addSql('ALTER TABLE wishlists ADD CONSTRAINT FK_4A4C2E1B67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cards CHANGE card_color card_color INT DEFAULT NULL, CHANGE card_type card_type INT DEFAULT NULL, CHANGE card_set card_set INT DEFAULT NULL, CHANGE card_rarity card_rarity INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sets CHANGE block block INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE collection_content DROP FOREIGN KEY FK_AAE94278181D576A');
        $this->addSql('ALTER TABLE collections DROP FOREIGN KEY FK_D325D3EE67B3B43D');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A60BB6FE6');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A2F95A66A');
        $this->addSql('ALTER TABLE wishlists DROP FOREIGN KEY FK_4A4C2E1B67B3B43D');
        $this->addSql('ALTER TABLE wishlist_content DROP FOREIGN KEY FK_61C9E6D365C165A8');
        $this->addSql('DROP TABLE collection_content');
        $this->addSql('DROP TABLE collections');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE wishlist_content');
        $this->addSql('DROP TABLE wishlists');
        $this->addSql('ALTER TABLE cards CHANGE card_color card_color INT NOT NULL, CHANGE card_rarity card_rarity INT NOT NULL, CHANGE card_set card_set INT NOT NULL, CHANGE card_type card_type INT NOT NULL');
        $this->addSql('ALTER TABLE sets CHANGE block block INT NOT NULL');
    }
}
