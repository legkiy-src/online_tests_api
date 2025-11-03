<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251103191445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE answer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE attempt_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE attempt_answer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE question_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE subject_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(<<<'SQL'
            CREATE TABLE answer (
              id INT NOT NULL,
              question_id INT NOT NULL,
              answer_text TEXT NOT NULL,
              is_correct BOOLEAN NOT NULL,
              order_index INT DEFAULT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_DADD4A251E27F6BF ON answer (question_id)');
        $this->addSql('COMMENT ON COLUMN answer.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN answer.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE attempt (
              id INT NOT NULL,
              subject_id INT DEFAULT NULL,
              user_id INT DEFAULT NULL,
              score INT DEFAULT NULL,
              max_score INT DEFAULT NULL,
              status VARCHAR(255) NOT NULL,
              time_spent INT NOT NULL,
              started_at DATE NOT NULL,
              completed_at DATE NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_18EC026623EDC87 ON attempt (subject_id)');
        $this->addSql('CREATE INDEX IDX_18EC0266A76ED395 ON attempt (user_id)');
        $this->addSql('COMMENT ON COLUMN attempt.started_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN attempt.completed_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE attempt_answer (
              id INT NOT NULL,
              attempt_id INT NOT NULL,
              question_id INT NOT NULL,
              answer_id INT DEFAULT NULL,
              answer_text TEXT NOT NULL,
              is_correct BOOLEAN NOT NULL,
              answered_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              points_awarded INT DEFAULT NULL,
              teacher_comment TEXT DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_FEC920DCB191BE6B ON attempt_answer (attempt_id)');
        $this->addSql('CREATE INDEX IDX_FEC920DC1E27F6BF ON attempt_answer (question_id)');
        $this->addSql('CREATE INDEX IDX_FEC920DCAA334807 ON attempt_answer (answer_id)');
        $this->addSql('COMMENT ON COLUMN attempt_answer.answered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE question (
              id INT NOT NULL,
              subject_id INT DEFAULT NULL,
              question_text TEXT NOT NULL,
              type VARCHAR(255) NOT NULL,
              points INT NOT NULL,
              order_index INT NOT NULL,
              created_at DATE NOT NULL,
              updated_at DATE NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_B6F7494E23EDC87 ON question (subject_id)');
        $this->addSql('COMMENT ON COLUMN question.created_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN question.updated_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE subject (
              id INT NOT NULL,
              teacher_id INT DEFAULT NULL,
              name VARCHAR(255) NOT NULL,
              description TEXT DEFAULT NULL,
              created_at DATE NOT NULL,
              updated_at DATE NOT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_FBCE3E7A41807E1D ON subject (teacher_id)');
        $this->addSql('COMMENT ON COLUMN subject.created_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN subject.updated_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (
              id INT NOT NULL,
              email VARCHAR(180) NOT NULL,
              password VARCHAR(255) NOT NULL,
              first_name VARCHAR(50) NOT NULL,
              last_name VARCHAR(50) NOT NULL,
              patronymic VARCHAR(50) DEFAULT NULL,
              role VARCHAR(255) NOT NULL,
              status VARCHAR(255) NOT NULL,
              created_at DATE NOT NULL,
              updated_at DATE NOT NULL,
              last_login_at DATE DEFAULT NULL,
              PRIMARY KEY(id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".updated_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".last_login_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql(<<<'SQL'
            ALTER TABLE
              answer
            ADD
              CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              attempt
            ADD
              CONSTRAINT FK_18EC026623EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              attempt
            ADD
              CONSTRAINT FK_18EC0266A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              attempt_answer
            ADD
              CONSTRAINT FK_FEC920DCB191BE6B FOREIGN KEY (attempt_id) REFERENCES attempt (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              attempt_answer
            ADD
              CONSTRAINT FK_FEC920DC1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              attempt_answer
            ADD
              CONSTRAINT FK_FEC920DCAA334807 FOREIGN KEY (answer_id) REFERENCES answer (id) ON DELETE
            SET
              NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              question
            ADD
              CONSTRAINT FK_B6F7494E23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              subject
            ADD
              CONSTRAINT FK_FBCE3E7A41807E1D FOREIGN KEY (teacher_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE answer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE attempt_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE attempt_answer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE question_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE subject_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE answer DROP CONSTRAINT FK_DADD4A251E27F6BF');
        $this->addSql('ALTER TABLE attempt DROP CONSTRAINT FK_18EC026623EDC87');
        $this->addSql('ALTER TABLE attempt DROP CONSTRAINT FK_18EC0266A76ED395');
        $this->addSql('ALTER TABLE attempt_answer DROP CONSTRAINT FK_FEC920DCB191BE6B');
        $this->addSql('ALTER TABLE attempt_answer DROP CONSTRAINT FK_FEC920DC1E27F6BF');
        $this->addSql('ALTER TABLE attempt_answer DROP CONSTRAINT FK_FEC920DCAA334807');
        $this->addSql('ALTER TABLE question DROP CONSTRAINT FK_B6F7494E23EDC87');
        $this->addSql('ALTER TABLE subject DROP CONSTRAINT FK_FBCE3E7A41807E1D');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE attempt');
        $this->addSql('DROP TABLE attempt_answer');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE "user"');
    }
}
