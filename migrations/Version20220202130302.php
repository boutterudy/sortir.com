<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220202130302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
            CREATE PROCEDURE updateOutingsStatus()
            BEGIN
                /* Set variables values */
                SELECT id INTO @enCreation FROM status WHERE LOWER(libelle) LIKE \'%en création%\';
                SELECT id INTO @ouverte FROM status WHERE LOWER(libelle) LIKE \'%ouverte%\';
                SELECT id INTO @cloturee FROM status WHERE LOWER(libelle) LIKE \'%clôturée%\';
                SELECT id INTO @enCours FROM status WHERE LOWER(libelle) LIKE \'%en cours%\';
                SELECT id INTO @terminee FROM status WHERE LOWER(libelle) LIKE \'%terminée%\';
                SELECT id INTO @annulee FROM status WHERE LOWER(libelle) LIKE \'%annulée%\';
                SELECT id INTO @archivee FROM status WHERE LOWER(libelle) LIKE \'%archivée%\';
            
                /* 	Get outings with states:	en création, ouvertes,
                clôturées, en cours, terminées and	annulées */
                 CREATE TEMPORARY TABLE outings SELECT outing.id as outing_id,
                        COUNT(*) as outing_nb_users,
                        max_users as outing_max_users,
                        limit_subscription_date as outing_limit_subscription_date,
                        start_at as outing_start_at_date,
                        DATE_ADD(start_at, INTERVAL duration MINUTE) as outing_end_date,
                        status.libelle as outing_status,
                        status.id as outing_status_id
                FROM outing_user
                INNER JOIN outing ON outing_user.outing_id = outing.id
                INNER JOIN status ON status.id = outing.status_id
                WHERE
                    LOWER(status.libelle) LIKE \'%en création%\' OR
                    LOWER(status.libelle) LIKE \'%ouverte%\' OR
                    LOWER(status.libelle) LIKE \'%clôturée%\' OR
                    LOWER(status.libelle) LIKE \'%en cours%\' OR
                    LOWER(status.libelle) LIKE \'%terminée%\' OR
                    LOWER(status.libelle) LIKE \'%annulée%\'
                GROUP BY outing_id;
            
                /* 	If outing_nb_users = outing_max_users or date > outing_limit_subscription_date
                    Except outings with states: en création, en cours, terminée and annulée
                    
                    Update state to clôturée */
                UPDATE outings SET outing_status_id = @cloturee WHERE outing_status_id != @enCreation AND outing_status_id != @enCours AND outing_status_id != @terminee AND outing_status_id != @annulee AND (outing_nb_users = outing_max_users OR CURRENT_TIMESTAMP > outing_limit_subscription_date);
            
                /* 	If outing_nb_users < outing_max_users and date <= outing_limit_subscription_date
                    Except outings with states: en création, en cours, terminée and annulée
                    
                    Update state to ouverte */
                UPDATE outings SET outing_status_id = @ouverte WHERE outing_status_id != @enCreation AND outing_status_id != @enCours AND outing_status_id != @terminee AND outing_status_id != @annulee AND outing_nb_users < outing_max_users AND CURRENT_TIMESTAMP <= outing_limit_subscription_date;
                
                /* 	If date >= outing_start_at_date and date < outing_end_date
                    Except outings with states: en création and annulée
                    
                    Update state to en cours */
                UPDATE outings SET outing_status_id = @enCours WHERE outing_status_id != @enCreation AND outing_status_id != @annulee AND CURRENT_TIMESTAMP >= outing_start_at_date AND CURRENT_TIMESTAMP < outing_end_date;
            
                /*	If date > outing_end_date
                    Except outings with states: en création and annulée
            
                    Update state to terminée */
                UPDATE outings SET outing_status_id = @terminee WHERE outing_status_id != @enCreation AND outing_status_id != @annulee AND CURRENT_TIMESTAMP > outing_end_date;
            
                /* 	If date > 1 month after outing_start_at_date
                    Only outings with state: annulée
            
                    Update state to archivée */
                UPDATE outings SET outing_status_id = @archivee WHERE outing_status_id = @annulee AND CURRENT_TIMESTAMP > DATE_ADD(outing_start_at_date, INTERVAL 1 MONTH);
            
                /*	If date > outing_end_date
                    Except outings with states: en création and annulée
            
                    Update state to archivée */
                UPDATE outings SET outing_status_id = @archivee WHERE outing_status_id != @enCreation AND outing_status_id != @annulee AND CURRENT_TIMESTAMP > DATE_ADD(outing_end_date, INTERVAL 1 MONTH);
            
                /* Update in database */
                UPDATE outing
                INNER JOIN outings ON outing.id = outings.outing_id
                SET outing.status_id = outings.outing_status_id;
                DROP TEMPORARY TABLE outings;
            
            END;
        ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP PROCEDURE IF EXISTS updateOutingsStatus;');
    }
}
