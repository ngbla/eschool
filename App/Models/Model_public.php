<?php

namespace App\Models;

use PDO;

date_default_timezone_set("Africa/Abidjan");
/**
 * Example user model
 *
 * PHP version 7.0
 */
class Model_public extends \Core\Model
{

    public static function get_parent_enfant( $id_parent){

        $db = static::getDB();

        $sql='SELECT * FROM parent_enfants WHERE id_parent ='.$id_parent;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) {
            $result=0;
        }

        return $result;        
    }

    public static function get_enfant_idParent( $id_enfant){

        $db = static::getDB();

        $sql='SELECT id_parent FROM parent_enfants WHERE  etat_parent_enfant > 0 AND id_enfant='.$id_enfant;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;        
    }

    public static function get_elevBymatricule($matricule){

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql='  SELECT * FROM (SELECT pers.id_pers_personne,pers.nom_prenom,pers.date_naiss,pers.lieu_naiss,pers.sexe,pers.email,pers.contact,elev.matricule,elev.id_eleve_eleve FROM (SELECT * FROM personne WHERE etat_pers = 1 AND type_pers = 1)pers INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1) elev  ON pers.id_type = elev.id_eleve_eleve) tmp_all_elv WHERE tmp_all_elv.matricule = "'.$matricule.'" ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;        
    }

    public static function get_AllParentsBy($id_parent){

        $db = static::getDB();

        $sql=' SELECT * FROM parent INNER JOIN (SELECT id_pers_personne, id_type, nom_prenom, date_naiss, lieu_naiss, sexe, email, contact FROM personne WHERE type_pers = 3 AND etat_pers = 1)tmp_per ON parent.id_parent_parent = tmp_per.id_type WHERE parent.id_parent_parent = '.$id_parent;

        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;        
    }

    public static function get_elevBy($id_eleve){

        $db = static::getDB();

        $sql='SELECT * FROM (SELECT pers.id_pers_personne,pers.nom_prenom,pers.date_naiss,pers.lieu_naiss,pers.sexe,pers.email,pers.contact,elev.matricule,elev.id_eleve_eleve FROM (SELECT * FROM personne WHERE etat_pers = 1 AND type_pers = 1)pers INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1) elev  ON pers.id_type = elev.id_eleve_eleve) tmp_fina_ok_elev WHERE tmp_fina_ok_elev.id_eleve_eleve ='.$id_eleve;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) {
            $result = 0;
        }
        return $result;        
    }

    public static function getAllElevByGrp($id_grp){
        
        $id_grp=intval($id_grp);
        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql=' SELECT * FROM (SELECT pers.id_pers_personne,pers.nom_prenom,pers.date_naiss,pers.lieu_naiss,pers.sexe,pers.email,pers.contact,elev.matricule,elev.id_eleve_eleve FROM (SELECT * FROM personne WHERE etat_pers = 1 AND type_pers = 1)pers INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1) elev  ON pers.id_type = elev.id_eleve_eleve)infoelev INNER JOIN (SELECT * FROM eleve_estds_groupe WHERE elv_ds_grpe_groupe = '.$id_grp.')elevgrpbyid ON infoelev.id_eleve_eleve = elevgrpbyid.elv_ds_grpe_idelev ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;        
    }

    public static function getAllElevNotGrp($id_annee){
        $id_annee=intval($id_annee);
        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql=' SELECT * FROM(SELECT pers.id_pers_personne,pers.nom_prenom,pers.sexe,pers.email,pers.contact,elev.matricule,elev.id_eleve_eleve FROM (SELECT * FROM personne WHERE etat_pers = 1 AND type_pers = 1)pers INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1) elev  ON pers.id_type = elev.id_eleve_eleve)tbelev WHERE tbelev.id_eleve_eleve NOT IN (SELECT eleve_estds_groupe.elv_ds_grpe_idelev as id_eleve_eleve FROM (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_annee = '.$id_annee.')grpanee INNER JOIN eleve_estds_groupe ON grpanee.groupe_id = eleve_estds_groupe.elv_ds_grpe_groupe) ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;        
    }
    
    //::::: SQL :::::: GROUPE


    public static function getGroupeBy($annee_id){

        $db = static::getDB();
        //var_dump($_POST);exit();
        if (isset($_POST["annee_id"])) {
            $annee_id = intval(htmlspecialchars($_POST["annee_id"]));
        }
    
        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql=' SELECT * FROM groupe WHERE groupe_annee='.$annee_id;
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();
        return $result;
       
    }

    public static function getGroupe_idanneeBy($groupe_id){

        $db = static::getDB();
        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql='SELECT groupe_annee FROM groupe WHERE groupe_id ='.$groupe_id;
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();
        return $result;
       
    }
    public static function getAllGroupe(){

        $db = static::getDB();
        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql=' SELECT * FROM (SELECT * FROM (SELECT * FROM groupe WHERE groupe_etat = 1)grp INNER JOIN (SELECT id_anscol_annee_scolaire,annee_libelle FROM annee_scolaire WHERE etat_annee = 1)anne ON grp.groupe_annee = anne.id_anscol_annee_scolaire)grpann INNER JOIN (SELECT id_classe_classe,libelle FROM classe WHERE etat = 1)clas on grpann.groupe_classe = clas.id_classe_classe ';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();
        return $result;
       
    }
    public static function get_allFilliere(){

        $db = static::getDB();
        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql=' SELECT id_classe_classe,libelle FROM classe WHERE etat = 1 ';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();
        return $result;
       
    }

    public static function get_allGroupe_active($id_filli){

        $db = static::getDB();
        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql=' SELECT groupe_id,groupe_libelle FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM groupe WHERE groupe_etat = 1)grp INNER JOIN (SELECT id_anscol_annee_scolaire,annee_libelle FROM annee_scolaire WHERE etat_annee = 1)anne ON grp.groupe_annee = anne.id_anscol_annee_scolaire)grpann INNER JOIN (SELECT id_classe_classe,libelle FROM classe WHERE etat = 1)clas on grpann.groupe_classe = clas.id_classe_classe)tmp_ok_grpby WHERE id_classe_classe = '.$id_filli;
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();
        return $result;
       
    }


    //::::: SQL :::::: MATIERE
    public static function get_MatiereBy($idmatiere){
        $db = static::getDB();
        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql=' SELECT * FROM matiere WHERE id_matiere_matiere = '.intval($idmatiere);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();
        if (empty($result)) {
            return 0;
        }
        else {
            return $result;
        }
        

    }

     //::::: SQL :::::: PERSONNES
    public static function get_Pers_infos($idtype,$type){
        $db = static::getDB();
        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql=' SELECT id_pers_personne, fk_iduniv, nom_prenom, date_naiss, lieu_naiss, sexe, email, contact, type_pers, id_type FROM personne WHERE type_pers = '.intval($type).' AND id_type = '.intval($idtype);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();
        if (empty($result)) {
            return 0;
        }
        else {
            return $result;
        }
        

    }
  
    /* * 
    *TABLE  : notification
    * NOTIFICATIONS
    *	id_notif notif_titre	notif_desc 	notif_debut 	notif_fin 	notif_methode	createur_notif notif_etat 	 
    * TABLE :  notif_user
    * usernotif_iduser 	usernotif_id 	usernotif_typeuser 	usernotif_etat 
    */

    public static function set_notifications( $notif_titre,$notif_desc ,$notif_debut,$notif_fin ,$notif_methode, $createur_notif ){
        $db = static::getDB();

        //unset($_POST);
        $sql=' SELECT * FROM notification WHERE notif_titre= "'.$notif_titre.'" AND notif_desc = "'.$notif_desc.'" AND notif_debut = "'.$notif_debut.'" AND notif_fin = "'.$notif_fin.'" AND notif_methode = "'.$notif_methode.'" AND createur_notif = "'.$createur_notif.'" LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        //exit();
        if ( empty($result) || $result==0) {

            $data = [
                'notif_titre' => $notif_titre,
                'notif_desc' => $notif_desc,
                'notif_debut' => $notif_debut,
                'notif_fin' => $notif_fin,
                'notif_methode' => $notif_methode,
                'createur_notif' => $createur_notif
            ];
            //var_dump($db);
            $sql=' INSERT INTO notification (notif_titre, notif_desc, notif_debut, notif_fin, notif_methode, createur_notif) VALUES ( :notif_titre, :notif_desc, :notif_debut, :notif_fin, :notif_methode, :createur_notif);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ( $result == TRUE) { 
                
                return $lastid ; 
            
            } 
            else {return -1;  }

        }
        else { return $result[0]['id_notif'];}
       
    }

    public static function set_usersNotif($usernotif_iduser, $usernotif_id, $usernotif_typeuser){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
       
        $sql=' SELECT * FROM notif_user WHERE usernotif_iduser= "'.$usernotif_iduser.'" AND usernotif_id = "'.$usernotif_id.'"  AND usernotif_typeuser = "'.$usernotif_typeuser.'"  LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);
        //exit();
        if ( empty($result) || $result==0) {

            $data = [
                'usernotif_iduser' => $usernotif_iduser,
                'usernotif_id' => $usernotif_id,
                'usernotif_typeuser' => $usernotif_typeuser
            ];
            //var_dump($db);
            $sql=' INSERT INTO notif_user (usernotif_iduser, usernotif_id, usernotif_typeuser) VALUES ( :usernotif_iduser, :usernotif_id, :usernotif_typeuser);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            //var_dump($lastid);
            if ( $result == TRUE) { return 1;} 
            else { return -1;    }
        }
        else {  return 0;}
       
    }

    public static function get_allNotifs(){

        $db = static::getDB();
        $sql='SELECT * FROM(SELECT * FROM notification INNER JOIN (SELECT id_type,nom_prenom,contact FROM personne WHERE type_pers  = 4)tmp_pers ON notification.createur_notif = tmp_pers.id_type)tmp_f WHERE tmp_f.notif_etat > 0';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {
            return 0;
        }
        else {
            return $result;        
        }

    }

    public static function set_allNotifs_etat($id_notif, $etat_notif){

        $db = static::getDB();
        $sql=' SELECT * FROM notification WHERE id_notif = '.$id_notif.'  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {
            return "setupdatenotif_erreur";
        }
        else {      
            $data = [
                'id_notif' => $id_notif,
                'notif_etat' => $etat_notif
            ];
           
            $sql='UPDATE notification SET notif_etat = :notif_etat WHERE id_notif =:id_notif ;';
            //var_dump($sql);
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            //$lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ( $result == TRUE) {unset($_POST);return "setupdatenotif_update";} 
            else {return "setupdatenotif_erreur"; }

        }
        
    }
    


    public static function get_liste_niveau($id_univ){

        $sql='SELECT * FROM niveau WHERE fk_id_univ = '.$id_univ.' ORDER BY niveau.libelle_niveau ASC';
        //print_r($sql);

        $result = Model_public::sql_query_get($sql);
        if ($result != 0 && !empty($result) ) {
            //vide
            //vide
            return  $result;
        }
        else {
            //vide
            return 0 ;

        }
        //print_r($sql);

    }

    
    public static function get_liste_filieres($id_univ){

        $sql='SELECT * FROM infosuniv,departement,classe WHERE infosuniv.id_univ = departement.fk_iduniv AND departement.id_depat = classe.id_departement AND infosuniv.id_univ = '.$id_univ.' ORDER BY departement.lib_depat ASC';
        //print_r($sql);

        $result = Model_public::sql_query_get($sql);
        if ($result != 0 && !empty($result) ) {
            //vide
            //vide
            return  $result;
        }
        else {
            //vide
            return 0 ;

        }
        //print_r($sql);

    }
    public static function get_uniq_filieresBy($lib_niveau,$id_univ){

        $sql='SELECT * FROM classe,departement WHERE classe.id_departement = departement.id_depat AND classe.libelle ="'.$lib_niveau.'"   AND departement.fk_iduniv ='.$id_univ.' LIMIT 1';
        //print_r($sql);

        $result = Model_public::sql_query_get($sql);
        if ($result != 0 && !empty($result) ) {
            //vide
            //vide
            return  $result;
        }
        else {
            //vide
            return 0 ;

        }
        //print_r($sql);

    }
    public static function get_uniq_niveauBy($lib_niveau,$id_univ){

        $sql='SELECT * FROM niveau  WHERE fk_id_univ = '.$id_univ.' AND niveau.libelle_niveau = "'.$lib_niveau.'" LIMIT 1';
        //print_r($sql);

        $result = Model_public::sql_query_get($sql);
        if ($result != 0 && !empty($result) ) {
            //vide
            //vide
            return  $result;
        }
        else {
            //vide
            return 0 ;

        }
        //print_r($sql);

    }
    public static function get_univInfo_By($id_univ){

        $sql='SELECT * FROM infosuniv WHERE id_univ = '.$id_univ;
        //print_r($sql);

        $result = Model_public::sql_query_get($sql);
        if ($result != 0 && !empty($result) ) {
            //vide
            //vide
            return  $result;
        }
        else {
            //vide
            return 0 ;

        }
        //print_r($sql);

    }

    public static function get_adminRole_By($id_admin){

        $sql='SELECT roles.* FROM admintab,roles WHERE admintab.id_role = roles.id_role AND admintab.id_admin_admin = '.$id_admin.' LIMIT 1';
        //print_r($sql);

        $result = Model_public::sql_query_get($sql);
        if ($result != 0 && !empty($result) ) {
            //vide
            //vide
            return  $result;
        }
        else {
            //vide
            return 0 ;

        }
        //print_r($sql);

    }
    public static function get_all_univ(){

        $sql='SELECT * FROM infosuniv';
        //print_r($sql);

        $result = Model_public::sql_query_get($sql);
        if ($result != 0 && !empty($result) ) {
            //vide
            //vide
            return  $result;
        }
        else {
            //vide
            return 0 ;

        }
        //print_r($sql);

    }


    public static function get_all_user_notif($type,$id_type){

        $sql="SELECT * FROM
            (SELECT * FROM notification,notif_user WHERE notification.id_notif=notif_user.usernotif_id AND notification.notif_methode LIKE '%notif%' AND notif_user.usernotif_typeuser=".$type." AND notif_user.usernotif_iduser=".$id_type."  AND notification.notif_debut <= Now()  AND Now() <= notification.notif_fin )tmp_notif
            INNER JOIN ( SELECT personne.nom_prenom, personne.id_pers_personne FROM personne)tmp_pers
            ON tmp_notif.createur_notif =tmp_pers.id_pers_personne";
        //print_r($sql);

        $result = Model_public::sql_query_get($sql);
        if ($result != 0 && !empty($result) ) { return  $result; }
        else { return 0 ; }
        //print_r($sql);

    }

    public static function get_eval_infosBy($id_eval){

        $sql="SELECT matiere.libele,matiere.code AS mat_code ,personne.nom_prenom,personne.id_pers_personne AS id_pers_prof, tmp1.* FROM
        (SELECT groupe.groupe_libelle,tmp_eval.* FROM
        (SELECT * FROM prof_eval,prof_eval_emploitps WHERE prof_eval.prof_eval_id=".$id_eval." AND prof_eval.eval_etat=2 AND prof_eval.prof_eval_id=prof_eval_emploitps.eval_id)tmp_eval,groupe 
        WHERE tmp_eval.id_groupe=groupe.groupe_id)tmp1,matiere,personne WHERE tmp1.id_mat = matiere.id_matiere_matiere AND tmp1.id_prof = personne.id_type AND personne.type_pers =2";
        //print_r($sql);

        $result = Model_public::sql_query_get($sql);
        if ($result != 0 && !empty($result) ) { return  $result; }
        else { return 0 ; }
        //print_r($sql);

    }
                 
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ Fonction global µµµµµµµµµµµµµµµµµµµ emploi du temps µµµµµµµµµµµµµµµµµµµµ
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ
    //	id_horaires	debut_h	fin_h	infos_h	
    public static function set_insertSQL($table,$tb_infos, $tb_conditions){
        $db = static::getDB();

        //var_dump($table,$tb_infos, $tb_conditions);//exit;
        $nbr_tb_conditions = count($tb_conditions);
        $var_i = 1 ;
        $nbr_tb_infos = count($tb_infos);

        if ($nbr_tb_conditions>0) { 
            
            $conditions=' WHERE  '; 
                
            foreach ($tb_conditions as $key => $value) {
                
                if ($var_i == $nbr_tb_conditions ) { $conditions= $conditions.' '.$key.' = "'.$value.'"' ; }
                else { $conditions= $conditions.' '.$key.' = "'.$value.'" AND ' ; }
                $var_i = $var_i +1;
            }
        
        }else { $conditions='   '; }

        $sql = ' SELECT * FROM '.$table.'  '.$conditions.' LIMIT 1';
        //var_dump($sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $champ1 = '';
        $champ2 = '';

        if ((empty($result) || $result == 0) && ($nbr_tb_infos > 0) ) {

            foreach ($tb_infos as $key => $value) {
                $data[$key] = $value;
                if ($champ1 == '') { $champ1= $key.' ' ;  }
                else {  $champ1= $champ1.' ,'.$key.' ' ; }

                if ($champ2 == '') { $champ2 = ' :'.$key.' ' ; }
                else {  $champ2= $champ2.' , :'.$key.' ' ; }
                
            }
            
            //var_dump($db);

            $sql = ' INSERT INTO '.$table.'  ('.$champ1.') VALUES ( '.$champ2.');';
            //var_dump($sql);exit;
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) { return 1; } 
            else { return -1;  }
        } 
        else { return 0; }


    }
    public static function get_selectSQL_ALL_by($table, $tb_conditions)
    {
        //var_dump($tb_conditions);exit();
        $nbr_tb_conditions = count($tb_conditions);
        $var_i = 1 ;
        if ($nbr_tb_conditions>0) { 
            $conditions=' WHERE  '; 
            foreach ($tb_conditions as $key => $value) {
                
                if ($var_i == $nbr_tb_conditions ) { $conditions= $conditions.' '.$key.' = "'.$value.'"' ; }
                else { $conditions= $conditions.' '.$key.' = "'.$value.'" AND ' ; }
                $var_i = $var_i +1;
            }
        }else { $conditions='   '; }

        $sql = ' SELECT * FROM '.$table.'  '.$conditions.' ;';
        //print_r($sql);echo "\n";
        $result = Model_public::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }

    public static function set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions)
    {

        $db = static::getDB();

        //var_dump($table,$tb_infos, $tb_conditions);//exit;
        $nbr_tb_conditions = count($tb_conditions);
        $var_i = 1 ;
        $nbr_tb_infos = count($tb_infos);

        if ($nbr_tb_conditions>0) { 
            
            $conditions=' WHERE  '; 
                
            foreach ($tb_conditions as $key => $value) {
                
                if ($var_i == $nbr_tb_conditions ) { $conditions= $conditions.' '.$key.' = "'.$value.'"' ; }
                else { $conditions= $conditions.' '.$key.' = "'.$value.'" AND ' ; }
                $var_i = $var_i +1;
            }
        
        }else { $conditions='   '; }

        $sql = ' SELECT * FROM '.$table.'  '.$conditions.' LIMIT 1';
        //var_dump($sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data ='';

        if (empty($result) || $result == 0 || $nbr_tb_infos == 0) {
            return 0;
        } else {
            //var_dump($db);
            //. $table . '="' . $valeur . '"
            foreach ($tb_infos as $key => $value) {

                
                if ($data == '') { $data= $key.' = "'.$value.'"  ';  }
                else {  $data= $data.' ,'.$key.' = "'.$value.'"  '; }
                
            }

            $sql = 'UPDATE '.$table.' SET   '.$data.'  '.$conditions.';';
            //print_r($sql);
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            return 1;
        }
    }


    public static function set_deleteSQL_ALL_by($table,$tb_conditions)
    {

        $db = static::getDB();

        //var_dump($table,$tb_infos, $tb_conditions);//exit;
        $nbr_tb_conditions = count($tb_conditions);
        $var_i = 1 ;

        if ($nbr_tb_conditions>0) { 
            
            $conditions=' WHERE  '; 
                
            foreach ($tb_conditions as $key => $value) {
                
                if ($var_i == $nbr_tb_conditions ) { $conditions= $conditions.' '.$key.' = "'.$value.'"' ; }
                else { $conditions= $conditions.' '.$key.' = "'.$value.'" AND ' ; }
                $var_i = $var_i +1;
            }
        
        }else { $conditions='   '; }

        $sql = ' SELECT * FROM '.$table.'  '.$conditions.' LIMIT 1';
        //var_dump($sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0 || $nbr_tb_conditions == 0) {
            return 0;
        } else {

            $sql = 'DELETE FROM '.$table.'  '.$conditions.';';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            return 1;
        }
    }

    
    //Fucntion permettant d'executer les sql pour recuperer des valeur (prend le sql en paramettre et retourne le resultat);
    public static function sql_query_get($sql){

        $db = static::getDB();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        unset($db);
        unset($stmt);
        unset($sql);

        if ( empty($result) || $result==0) {
            return 0;
        }
        else {
            return $result;        
        }



    }
    

    //Fucntion DELETE

    public static function delete_courresource($id_cour_resourc){

        $db = static::getDB();
        //groupe_matiere_coef_id 	matiere_id_tmp 	coeficient_tmp 	matiere_parent_id_tmp 	groupe_id_tmp 	part_annee_id_tmp 
        $sql=' SELECT * FROM docsvideo_de_cours WHERE id_docsvideocour= '.$id_cour_resourc.'  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();

        if ( empty($result) || $result==0) { return 0; }
        else {          
            $sql='DELETE FROM docsvideo_de_cours WHERE id_docsvideocour = '.$id_cour_resourc;
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            return 1;
        }


    }

    public static function delete_courpartie($id_cour_partie){

        $db = static::getDB();
        //groupe_matiere_coef_id 	matiere_id_tmp 	coeficient_tmp 	matiere_parent_id_tmp 	groupe_id_tmp 	part_annee_id_tmp 
        $sql=' SELECT * FROM cours_plan WHERE id_cours_plan= '.$id_cour_partie.'  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();

        if ( empty($result) || $result==0) { return 0; }
        else {          
            $sql='DELETE FROM cours_plan WHERE id_cours_plan = '.$id_cour_partie;
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            return 1;
        }


    }

    public static function delete_coursection($id_cour_partie){

        $db = static::getDB();
        //groupe_matiere_coef_id 	matiere_id_tmp 	coeficient_tmp 	matiere_parent_id_tmp 	groupe_id_tmp 	part_annee_id_tmp 
        $sql=' SELECT * FROM cours_plan WHERE id_cours_plan= '.$id_cour_partie.'  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();

        if ( empty($result) || $result==0) { return 0; }
        else {          
            $sql='DELETE FROM cours_plan WHERE id_cours_plan = '.$id_cour_partie;
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();

            $sql='DELETE FROM cours_plan WHERE id_parent_plan = '.$id_cour_partie;
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            return 1;
        }


    }


    
    public static function change_user($id_type,$type){

        $info=Model_public::get_Pers_infos($id_type,$type);
        $verif = sha1(date("d/m/Y"));
        //var_dump($verif);exit;

        if(!empty($info)){
            $info = $info[0];
            //var_dump($info);exit();


            $tabdata = [
                'id_pers_personne'  => intval($info['id_pers_personne']),
                'fk_iduniv'  => intval($info['fk_iduniv']),
                //'lib_user_annee'  => $info["annee_libelle"],
                //'lib_user_id_annee'  => intval($info["id_anscol_annee_scolaire"]),
                
                'nom_prenom' => $info['nom_prenom'],
                'date_naiss' => $info['date_naiss'],
                'lieu_naiss' => $info['lieu_naiss'],
                'sexe' => $info['sexe'],
                'email' => $info['email'],
                'contact' => $info['contact'],
                'type_pers' => intval($info['type_pers']),
                'id_type' => intval($info['id_type'])
            ];
            $_SESSION["user"]=$tabdata;
            $_POST=NULL;
            $_GET=NULL;
            unset($_POST);
            unset($_GET);
            $typ_user = intval($info['type_pers']);
            //var_dump($dbresult->type_pers,$typ_user);exit();
           
           
            
            switch ($typ_user) {
                case 1:
                    $_SESSION['p'] = "eleve_accueil";
                    $_GET['p']="eleve_accueil";
                    header('Refresh: 1;URL=index.php?p=eleve_accueil&verif='.$verif);
                    exit;
                    //var_dump("admin type",$typ_user);
                    //View::renderTemplate('Accueil/admin/admin_accueil.html',$tabdata);
                break;
                case 3:
                    $_SESSION['p'] = "parent_accueil";
                    $_GET['p']="parent_accueil";
                    header('Refresh: 1;URL=index.php?p=parent_accueil&verif='.$verif);
                    exit;
                    //var_dump("admin type",$typ_user);
                    //View::renderTemplate('Accueil/admin/admin_accueil.html',$tabdata);
                break;
                case 2:
                    $_SESSION['p'] = "prof_accueil";
                    $_GET['p']="prof_accueil";
                    header('Refresh: 1;URL=index.php?p=prof_accueil&verif='.$verif);
                    exit;
                    //var_dump('prof type',$typ_user);
                    //View::renderTemplate('Accueil/prof/prof_accueil.html',$tabdata);
                break;
                case 4:
                    $_SESSION['p'] = "accueil";
                    $_GET['p']="accueil";
                    header('Refresh: 1;URL=index.php?p=accueil&verif='.$verif);
                    exit;
                    //var_dump("admin type",$typ_user);
                    //View::renderTemplate('Accueil/admin/admin_accueil.html',$tabdata);
                break;
                
                default:
                    return 0;
                break;
            }

        }
    }
    public static function get_user_annee($id_pers){

        $sql="SELECT personne.nom_prenom,personne.fk_idanneescol,annee_scolaire.* FROM personne INNER JOIN annee_scolaire ON annee_scolaire.id_anscol_annee_scolaire=personne.fk_idanneescol WHERE personne.id_pers_personne=".$id_pers."  LIMIT 1";
        //print_r($sql);

        $result = Model_public::sql_query_get($sql);
        if ($result != 0 && !empty($result) ) { return  $result; }
        else { return 0 ; }
        //print_r($sql);

    }
    
    public static function get_user_conex($id_pers,$id_univ){

        $sql="SELECT * FROM connexion WHERE fk_iduniv=".$id_univ." AND conex_id_personne=".$id_pers." AND DATEDIFF( CURRENT_TIMESTAMP , connexion.conex_date_heure) > -1 AND 7 > DATEDIFF( CURRENT_TIMESTAMP , connexion.conex_date_heure) AND connexion.conex_message= 'réussite' LIMIT 1";
        //print_r($sql);
        $result = Model_public::sql_query_get($sql);
        if ($result != 0 && !empty($result) ) { return  $result; }
        else { return 0 ; }
        //print_r($sql);

    }    //Fucntion Historique de connexion
    
    public static function user_connexion($id_personne,$id_univ,$message,$etat_conex){
        $ip_user = $_SERVER['REMOTE_ADDR'];
        //$ip_user = '160.155.112.37';

        if ($_SERVER['HTTP_HOST'] == 'localhost.eschool') {
            $ip_info ='offline';
            $conex_continent_pays= 'localhost';
            $conex_coordonne = 'lat: local | log : local';
        }
        else {
            $ip_info = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=". $ip_user));
            //$ip_info = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp"));
            $conex_continent_pays= $ip_info->geoplugin_continentCode.'-'.$ip_info->geoplugin_continentName;
            $conex_continent_pays = $conex_continent_pays.' || '.$ip_info->geoplugin_countryCode.'-'.$ip_info->geoplugin_countryName;
            $conex_coordonne = 'lat: '.$ip_info->geoplugin_latitude.'| log : '.$ip_info->geoplugin_longitude.' | ARadius :'.$ip_info->geoplugin_locationAccuracyRadius;
        }
   
        $datetime = date("Y-m-d H:i:s");
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        // conex_id	   conex_id_personne	fk_iduniv	conex_ip	conex_date_heure	conex_navigateur	conex_continent_pays	conex_coordonne	conex_etat conex_message  	

        $db = static::getDB();

        $sql_reset='UPDATE connexion SET conex_etat = 0 WHERE connexion.conex_id_personne = '.$id_personne.' AND connexion.fk_iduniv ='.$id_univ;
        $stmt_reset= $db->prepare($sql_reset);
        $result_reset = $stmt_reset->execute();

        $data = [
            'conex_id_personne' => $id_personne,
            'fk_iduniv' => $id_univ,
            'conex_ip' => $ip_user,
            'conex_date_heure' => $datetime,
            'conex_navigateur' => $useragent,
            'conex_continent_pays' => $conex_continent_pays,
            'conex_coordonne' => $conex_coordonne,
            'conex_etat' => $etat_conex,
            'conex_message' => $message
        ];
        $sql=' INSERT INTO connexion  (conex_id_personne, fk_iduniv, conex_ip, conex_date_heure, conex_navigateur, conex_continent_pays, conex_coordonne, conex_etat, conex_message) VALUES ( :conex_id_personne, :fk_iduniv, :conex_ip, :conex_date_heure, :conex_navigateur, :conex_continent_pays, :conex_coordonne, :conex_etat, :conex_message);';
        $stmt= $db->prepare($sql);
        $result = $stmt->execute($data);
        $lastid =  $db->lastInsertId();
    }

}