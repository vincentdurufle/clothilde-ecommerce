<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210714183001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image ADD collaboration_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FEF1544CE FOREIGN KEY (collaboration_id) REFERENCES collaboration (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_C53D045FEF1544CE ON image (collaboration_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F126F525E');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FEF1544CE');
        $this->addSql('DROP INDEX IDX_C53D045FEF1544CE ON image');
        $this->addSql('ALTER TABLE image DROP collaboration_id');
    }
}
