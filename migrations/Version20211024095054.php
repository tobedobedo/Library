<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211024095054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33167402924');
        $this->addSql('DROP INDEX IDX_CBE5A33167402924 ON book');
        $this->addSql('ALTER TABLE book ADD pub_house_id INT DEFAULT NULL, DROP publishing_house_id');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331A68679BC FOREIGN KEY (pub_house_id) REFERENCES publishing_house (id)');
        $this->addSql('CREATE INDEX IDX_CBE5A331A68679BC ON book (pub_house_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331A68679BC');
        $this->addSql('DROP INDEX IDX_CBE5A331A68679BC ON book');
        $this->addSql('ALTER TABLE book ADD publishing_house_id INT NOT NULL, DROP pub_house_id');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33167402924 FOREIGN KEY (publishing_house_id) REFERENCES publishing_house (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_CBE5A33167402924 ON book (publishing_house_id)');
    }
}
