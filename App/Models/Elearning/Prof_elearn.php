<?php

namespace App\Models\Elearning;

use PDO;

date_default_timezone_set("Africa/Abidjan");


class Prof_elearn extends \Core\Model{
    
    public static function get_proMatEnseigner($id_prof){

        $db = static::getDB();

        $sql=' SELECT * FROM(SELECT id_mat FROM prof_matiere WHERE id_prof = '.$id_prof.' AND etat_prof_mat = 1 )tmp_prof INNER JOIN (SELECT id_matiere_matiere,code,libele FROM matiere WHERE etat =1)tmp_mat ON tmp_prof.id_mat= tmp_mat.id_matiere_matiere';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }
    /** docsvideo_de_cours
    *OLD ***id_videocour	video_libelle id_matiere	id_pers_prof	lien_video	date_video	etat_video
    ************************************************************************************************** 
    * id_docsvideocour courplan_id id_matiere id_pers_prof type  video_libelle lien_docsvideo date_video dispo_datedebut dispo_datefin etat_docsvideo
    */
    public static function set_profmatiere_video($id_matiere, $id_persprof, $lienvideo, $video_libelle , $datedebut_dispo , $datefin_dispo , $courplan_id){

        $db = static::getDB();

        $sql=' SELECT * FROM docsvideo_de_cours WHERE courplan_id= "'.$courplan_id.'" AND id_matiere= "'.$id_matiere.'" AND id_pers_prof= "'.$id_persprof.'" AND type= "v" AND lien_docsvideo= "'.$lienvideo.'"  LIMIT 1';
        //var_dump($login,$pass,$sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();

        if ( empty($result) || $result==0) {
            
            $data = [
                'courplan_id' => $courplan_id,
                'id_matiere' => $id_matiere,
                'id_pers_prof' => $id_persprof,
                'type' => 'v',
                'video_libelle' => $video_libelle,
                'lien_docsvideo' => $lienvideo,
                'dispo_datedebut' => $datedebut_dispo,
                'dispo_datefin' => $datefin_dispo
            ];
            //var_dump($db);
            
            $sql=' INSERT INTO docsvideo_de_cours (courplan_id, id_matiere, id_pers_prof , type, video_libelle , lien_docsvideo , dispo_datedebut, dispo_datefin) VALUES ( :courplan_id, :id_matiere, :id_pers_prof , :type , :video_libelle, :lien_docsvideo , :dispo_datedebut, :dispo_datefin);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            if ( $result == TRUE) { return "success"; } 
            else { return "erreur";   }
        }
        else { return "erreur";   }

        //var_dump(__NAMESPACE__ );
    }

    public static function get_matiere_docsvideo($id_matiere, $id_persprof){

        $db = static::getDB();

        $sql=' SELECT * FROM docsvideo_de_cours WHERE id_matiere='.$id_matiere.' AND id_pers_prof='.$id_persprof .' AND etat_docsvideo=1 ORDER BY type ASC';

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result; 

        //var_dump(__NAMESPACE__ );
    }

    public static function get_profmatiere_video($id_matiere, $id_persprof){

        $db = static::getDB();

        $sql=' SELECT * FROM video_de_cours WHERE id_matiere='.$id_matiere.' AND id_pers_prof='.$id_persprof;

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result; 

        //var_dump(__NAMESPACE__ );
    }

    public static function get_prof_prochainmat($id_prof, $date_jour ){

        $db = static::getDB();
 
        $sql= " SELECT * FROM (SELECT * FROM (SELECT code as code_mat,libele as lib_mat , id_matiere_matiere FROM matiere) tmp_mat INNER JOIN (SELECT * FROM groupe_emploitps WHERE emploitps_id_prof = '$id_prof' AND emploitps_etat = 1 AND emploitps_date >= CURRENT_DATE()-1 ORDER BY emploitps_date ASC  LIMIT 10)tmp_prochCM  ON tmp_mat.id_matiere_matiere = tmp_prochCM.emploitps_id_mat) tmp_emploitps2 ,

        (SELECT * FROM (SELECT g.groupe_id, g.groupe_libelle, g.groupe_annee, c.id_classe_classe, c.libelle AS filiere_lib FROM groupe g,classe c WHERE g.groupe_classe = c.id_classe_classe) tmp_grp1 INNER JOIN (SELECT ans.id_anscol_annee_scolaire, ans.annee_libelle, ansp.id_annee_partie ,ansp.libele_partie FROM annee_scolaire ans , annee_partie ansp WHERE ans.id_anscol_annee_scolaire = ansp.id_anneeScolaire) tmp_an1 ON tmp_grp1.groupe_annee = tmp_an1.id_anscol_annee_scolaire) tmp_grpan2  

        WHERE tmp_emploitps2.emploitps_groupe_id = tmp_grpan2.groupe_id   AND tmp_emploitps2.emploitps_anneescolaire = tmp_grpan2.id_anscol_annee_scolaire   AND tmp_emploitps2.emploitps_periode = tmp_grpan2.id_annee_partie";
        
        //var_dump($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result; 

        //var_dump(__NAMESPACE__ );
    }

    /* TABLE : cours_plan
    * id_cours_plan	//id_plan_prof//id_mat_plan	//id_parent_plan//plan_titre//plan_description//plan_position//plan_position_num//etat_plan
    */

    public static function set_cour_plan($id_plan_prof, $id_mat_plan, $id_parent_plan, $plan_titre , $plan_description , $plan_position, $plan_position_num){

        $db = static::getDB();

        $sql=' SELECT * FROM cours_plan WHERE id_plan_prof="'.$id_plan_prof.'" AND id_mat_plan="'.$id_mat_plan.'" AND id_parent_plan="'.$id_parent_plan.'" AND plan_titre="'.$plan_titre.'"  AND plan_description="'.$plan_description.'"   AND plan_position="'.$plan_position.'"   LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();

        if ( empty($result) || $result==0) {
            
            $data = [
                'id_plan_prof' => $id_plan_prof,
                'id_mat_plan' => $id_mat_plan,
                'id_parent_plan' => $id_parent_plan,
                'plan_titre' => $plan_titre,
                'plan_description' => $plan_description,
                'plan_position' => $plan_position,
                'plan_position_num' => $plan_position_num
                
            ];
            //var_dump($db);
            $sql=' INSERT INTO cours_plan (id_plan_prof, id_mat_plan, id_parent_plan, plan_titre , plan_description , plan_position, plan_position_num) VALUES (:id_plan_prof, :id_mat_plan, :id_parent_plan, :plan_titre , :plan_description , :plan_position , :plan_position_num);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            if ( $result == TRUE) { return "success"; } 
            else { return "erreur";   }
        }
        else { return "erreur";   }

        //var_dump(__NAMESPACE__ );
    }

    public static function get_cour_section($id_prof, $id_mat_plan ){

        $db = static::getDB();

        $sql= "SELECT * FROM cours_plan WHERE id_parent_plan = 0 AND id_mat_plan = $id_mat_plan AND id_plan_prof = $id_prof AND plan_position = 'SECTION' AND etat_plan = 1 ORDER BY plan_position_num ASC";
        
        //var_dump($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result; 

        //var_dump(__NAMESPACE__ );
    }

    public static function get_cour_partie($id_prof, $id_mat_plan ){

        $db = static::getDB();

        $sql= "SELECT * FROM cours_plan WHERE id_parent_plan <> 0 AND id_mat_plan = $id_mat_plan AND id_plan_prof = $id_prof AND plan_position = 'PARTIE' AND etat_plan = 1 ORDER BY plan_position_num ASC";
        
        //var_dump($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result; 

        //var_dump(__NAMESPACE__ );
    }

    public static function get_cour_partieBy($id_prof, $id_mat_plan ,$id_planmatid){

        $db = static::getDB();

        $sql= "SELECT * FROM cours_plan WHERE id_parent_plan = $id_planmatid AND id_mat_plan = $id_mat_plan AND id_plan_prof = $id_prof AND plan_position = 'PARTIE' AND etat_plan = 1 ORDER BY plan_position_num ASC";
        
        //var_dump($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result; 

        //var_dump(__NAMESPACE__ );
    }

    public static function get_courplan_lastnum($id_prof, $id_mat_plan ,$cour_partie){

        $db = static::getDB();

        $sql= "SELECT MAX(plan_position_num) AS lastnum FROM cours_plan WHERE id_plan_prof =$id_prof  AND id_mat_plan = $id_mat_plan AND etat_plan = 1 AND plan_position = '$cour_partie'";
        
        //var_dump($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result[0]['lastnum']);exit;
        return $result[0]['lastnum']; 

        //var_dump(__NAMESPACE__ );
    }
    
    /**
     * table : suivi_active_courplan
     * sa_courplan_id	
     * sa_groupe_id	
     * sa_etatvue_groupe (0-vue grpe Desactiver //1-groupe vue activer)	
     * sa_progression (0-non effectuer//1-terminer)
     */

    public static function set_suivi_active_courplan($sa_courplan_id, $sa_groupe_id, $rqtetype, $rqtetype_valeur ){

        $db = static::getDB();

        $sql=' SELECT * FROM suivi_active_courplan WHERE sa_courplan_id= "'.$sa_courplan_id.'" AND sa_groupe_id= "'.$sa_groupe_id.'" LIMIT 1';
        //var_dump($login,$pass,$sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();
        $etatrqte = 0;

        if ( empty($result) || $result==0) {
            
            $data = [
                'sa_courplan_id' => $sa_courplan_id,
                'sa_groupe_id' => $sa_groupe_id
            ];
            //var_dump($db);
            
            $sql=' INSERT INTO suivi_active_courplan (sa_courplan_id, sa_groupe_id) VALUES ( :sa_courplan_id, :sa_groupe_id);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            //$lastid =  $db->lastInsertId();
            if ( $result == TRUE) {$etatrqte = 1; } 
            else {  $etatrqte = 0; }
        }
        else { $etatrqte = 1; }

		//1-maj sa_etatvue_groupe  et 2- maj sa_progression
        if($etatrqte == 1){
            switch ($rqtetype) {
                case 1:
                    $sql=' UPDATE suivi_active_courplan SET sa_etatvue_groupe ='.$rqtetype_valeur.' WHERE sa_courplan_id ='.$sa_courplan_id.' AND sa_groupe_id = '.$sa_groupe_id.' ;';
                    $stmt= $db->prepare($sql);
                    $result = $stmt->execute();
                break;
                case 2:
                    $sql=' UPDATE suivi_active_courplan SET sa_progression ='.$rqtetype_valeur.' WHERE sa_courplan_id ='.$sa_courplan_id.' AND sa_groupe_id = '.$sa_groupe_id.' ;';
                    $stmt= $db->prepare($sql);
                    $result = $stmt->execute();
                break;
                
                default:
                break;
            }
            return 'success';
        }
        else {
            return 'danger';
        }

        //var_dump(__NAMESPACE__ );
    }

    public static function get_suivi_active_courplan($sa_courplan_id, $sa_groupe_id){
        $db = static::getDB();
        $sql=' SELECT * FROM suivi_active_courplan WHERE sa_courplan_id= "'.$sa_courplan_id.'" AND sa_groupe_id= "'.$sa_groupe_id.'" LIMIT 1';
        //var_dump($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result; 

        //var_dump(__NAMESPACE__ );
    }
    

    public static function get_nbre_courplan_nif($sa_mat_id, $sa_groupe_id, $sa_prof_id){
        $db = static::getDB();
        $sql='SELECT SUM(suivi_active_courplan.sa_progression) AS nbr_nif FROM cours_plan ,suivi_active_courplan WHERE cours_plan.id_cours_plan = suivi_active_courplan.sa_courplan_id AND cours_plan.id_plan_prof = '.$sa_prof_id.' AND cours_plan.etat_plan = 1 AND cours_plan.id_mat_plan = '.$sa_mat_id.' AND suivi_active_courplan.sa_groupe_id = '.$sa_groupe_id;
        //var_dump($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($result[0]["nbr_nif"])) {
            //var_dump('sql <pre>', $result[0]["nbr_nif"], ' </pre>');
            $rep= $result[0]["nbr_nif"];
        }
        else {
            //var_dump('sql  <pre>', 0, ' </pre>');
            $rep = 0;
        }

        return $rep; 

        //var_dump(__NAMESPACE__ );
    }
    
    public static function get_nbre_courplan($sa_mat_id, $sa_prof_id){
        $db = static::getDB();
        $sql='SELECT COUNT(id_cours_plan) AS nbre_plan FROM cours_plan WHERE id_plan_prof = '.$sa_prof_id.' AND id_mat_plan = '.$sa_mat_id.' AND etat_plan = 1';
        //var_dump($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($result[0]["nbre_plan"])) {
            //var_dump('sql  <pre>', $result[0]["nbre_plan"], ' </pre>');
            $rep= $result[0]["nbre_plan"];
        }
        else {
            //var_dump('sql  <pre>', 0, ' </pre>');
            $rep= 0;
        }

        return $rep; 

         
        //var_dump(__NAMESPACE__ );
    }

    
    
    
}
