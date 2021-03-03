<?php

namespace App\Models;
 
use PDO;
  
date_default_timezone_set("Africa/Abidjan");
/**
 * Example user model
 *
 * PHP version 7.0
*/
class Comptable extends \Core\Model
{

    public static function get_entreeBy($id_univ)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = "SELECT SUM(montant) AS Entree FROM operation WHERE  type_op = 1 AND univ = ".$id_univ;
        //print_r($sql);
        $result = Comptable::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result[0]['Entree'];
        } else {
            return 0;
        }
    }  

    public static function get_sortieBy($id_univ)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = "SELECT SUM(montant) AS Sortie FROM operation WHERE  type_op = 2 AND univ = ".$id_univ;
        //print_r($sql);
        $result = Comptable::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result[0]['Sortie'];
        } else {
            return 0;
        }
    } 

    public static function get_allVersementsBy($id_univ)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = "SELECT *,eleve.id_eleve_eleve AS id_eleve, personne.id_type AS IdPers, personne.id_pers_personne AS IdPersV,eleve.id_eleve_eleve AS id_eleve, ligneversement.dateajout AS DatePay , ligneversement.id AS IdVers FROM personne, eleve, ligneversement WHERE personne.type_pers = 1 AND personne.id_type = eleve.id_eleve_eleve AND ligneversement.id_eleve = eleve.id_eleve_eleve AND personne.fk_iduniv = ".$id_univ." ORDER BY ligneversement.id DESC";
        //print_r($sql);
        $result = Comptable::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    } 

    public static function get_elevegrpeBy($id_niv,$id_classe,$id_eleve)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = "SELECT * FROM eleve_estds_groupe,groupe WHERE elv_ds_grpe_etat=1 AND eleve_estds_groupe.elv_ds_grpe_groupe =groupe.groupe_id AND groupe.fk_idniveau=".$id_niv." AND groupe.groupe_classe=".$id_classe." AND eleve_estds_groupe.elv_ds_grpe_idelev=".$id_eleve;
        //print_r($sql);
        $result = Comptable::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }  


    public static function get_univ_scolarite($id_univ)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM frais_scolarite,classe,niveau WHERE frais_scolarite.classe=classe.id_classe_classe AND frais_scolarite.niveau=niveau.id_niveau AND frais_scolarite.univ='.$id_univ.' ORDER BY classe.libelle ASC';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    } 

    public static function get_univtype_scolarite($id_univ)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM (SELECT addtypefrais.*,classe.libelle AS lib_class,niveau.libelle_niveau FROM addtypefrais,classe,niveau WHERE addtypefrais.classe=classe.id_classe_classe AND addtypefrais.niveau=niveau.id_niveau AND addtypefrais.univ='.$id_univ.' ORDER BY classe.libelle ASC) tmp_typ_frais LEFT JOIN typefrais ON tmp_typ_frais.typefrais =typefrais.id';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }           
    //Fucntion permettant d'executer les sql pour recuperer des valeur (prend le sql en paramettre et retourne le resultat);
    public static function sql_query_get($sql)
    {

        $db = static::getDB();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result;
        }
    }
}
