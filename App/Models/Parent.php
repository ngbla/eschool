<?php

namespace App\Models;

use PDO;

date_default_timezone_set("Africa/Abidjan");
/**
 * Example user model
 *
 * PHP version 7.0
*/
class Parents extends \Core\Model
{
 
    
    public static function getUnivInfos(){

        $db = static::getDB();

        $sql=' SELECT * FROM infosuniv';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;        
    }

    
    public static function get_allEnfantBy($id_parent){

        $db = static::getDB();

        $sql='SELECT * FROM(SELECT pers.id_pers_personne,pers.nom_prenom,pers.date_naiss,pers.lieu_naiss,pers.sexe,pers.email,pers.contact,elev.matricule,elev.id_eleve_eleve FROM (SELECT * FROM personne WHERE  type_pers = 1)pers INNER JOIN (SELECT * FROM eleve ) elev  ON pers.id_type = elev.id_eleve_eleve) tmp_final_elev WHERE tmp_final_elev.id_eleve_eleve IN (SELECT id_enfant FROM parent_enfants WHERE etat_parent_enfant = 1 AND id_parent = '.$id_parent.')';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;        
    }

    

 
    

    


}
