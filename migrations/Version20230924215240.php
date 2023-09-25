<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230924215240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE object_props (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', fields_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', objects_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', value LONGTEXT NOT NULL, INDEX IDX_DF5DFE4A2C5439AE (fields_id), INDEX IDX_DF5DFE4A4BEE6933 (objects_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE object_props ADD CONSTRAINT FK_DF5DFE4A2C5439AE FOREIGN KEY (fields_id) REFERENCES fields (id)');
        $this->addSql('ALTER TABLE object_props ADD CONSTRAINT FK_DF5DFE4A4BEE6933 FOREIGN KEY (objects_id) REFERENCES objects (id)');
        $this->addSql('ALTER TABLE objects_fields DROP FOREIGN KEY FK_2941F3B22C5439AE');
        $this->addSql('ALTER TABLE objects_fields DROP FOREIGN KEY FK_2941F3B24BEE6933');
        $this->addSql('DROP TABLE objects_fields');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE objects_fields (objects_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', fields_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_2941F3B22C5439AE (fields_id), INDEX IDX_2941F3B24BEE6933 (objects_id), PRIMARY KEY(objects_id, fields_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE objects_fields ADD CONSTRAINT FK_2941F3B22C5439AE FOREIGN KEY (fields_id) REFERENCES fields (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objects_fields ADD CONSTRAINT FK_2941F3B24BEE6933 FOREIGN KEY (objects_id) REFERENCES objects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE object_props DROP FOREIGN KEY FK_DF5DFE4A2C5439AE');
        $this->addSql('ALTER TABLE object_props DROP FOREIGN KEY FK_DF5DFE4A4BEE6933');
        $this->addSql('DROP TABLE object_props');
    }
}
