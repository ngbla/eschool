<?php

namespace App\Models;

use PDO;

date_default_timezone_set("Africa/Abidjan");
/**
 * Example user model
 *
 * PHP version 7.0
 */
class Eleve extends \Core\Model
{

    
    public static function getUnivInfos() {

        $db = static::getDB();

        $sql=' SELECT * FROM infosuniv';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;        
    }
    /**
     * Get all the users as an associative array
     *
     * @return array
     */
    //Get Stage By Eleve
    public static function getStageByEleve($id_eleve)
    {
        $db = static::getDB();
        //$sql=' SELECT * FROM stage_etudiant WHERE fk_idetudiant = '.$id_eleve;
        $sql='SELECT stage_etudiant.*,tmp_pers.nom_prenom,tmp_eleve.matricule,tmp_grpe.* ,tmp_prof.nom_prenom AS prof_nomprenom,tmp_prof.prof_tel FROM  stage_etudiant,(SELECT id_anscol_annee_scolaire FROM annee_scolaire ORDER by annee_libelle DESC )tmp_anee,(SELECT nom_prenom,id_type FROM personne WHERE type_pers = 1 AND etat_pers = 1 AND id_type='.$id_eleve.')tmp_pers, (SELECT matricule,id_eleve_eleve FROM eleve)tmp_eleve , (SELECT groupe.groupe_id, groupe.groupe_libelle,niveau.libelle_niveau,classe.libelle AS lib_classe FROM groupe,niveau,classe  WHERE niveau.id_niveau = groupe.fk_idniveau AND classe.id_classe_classe = groupe.groupe_classe)tmp_grpe,(SELECT nom_prenom,id_type,contact AS prof_tel FROM personne WHERE type_pers = 2 )tmp_prof
        WHERE stage_etudiant.fk_idanneeScol =tmp_anee.id_anscol_annee_scolaire AND stage_etudiant.fk_idetudiant=tmp_pers.id_type AND tmp_eleve.id_eleve_eleve = stage_etudiant.fk_idetudiant AND tmp_grpe.groupe_id = stage_etudiant.fk_idgroupe AND tmp_prof.id_type = stage_etudiant.fk_idprof_directEtud AND stage_etudiant.etat_stage = 1 ';
        //print_r( $sql);
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    } 
    public static function get_all_stgEtudiant($id_stage)
    {
        $db = static::getDB();
        //$sql=' SELECT * FROM stage_etudiant WHERE fk_idetudiant = '.$id_eleve;
        $sql='SELECT* FROM(SELECT stage_etudiant.*,tmp_pers.nom_prenom,tmp_eleve.matricule,tmp_grpe.* ,tmp_prof.nom_prenom AS prof_nomprenom,tmp_prof.prof_tel   FROM    stage_etudiant,(SELECT id_anscol_annee_scolaire FROM annee_scolaire ORDER by annee_libelle DESC )tmp_anee,(SELECT nom_prenom,id_type FROM personne WHERE type_pers = 1 AND etat_pers = 1 AND id_type= 2)tmp_pers, (SELECT matricule,id_eleve_eleve FROM eleve)tmp_eleve , (SELECT groupe.groupe_id, groupe.groupe_libelle,niveau.libelle_niveau,classe.libelle AS lib_classe FROM groupe,niveau,classe  WHERE niveau.id_niveau = groupe.fk_idniveau AND classe.id_classe_classe = groupe.groupe_classe)tmp_grpe,(SELECT nom_prenom,id_type,contact AS prof_tel FROM personne WHERE type_pers = 2 )tmp_prof  WHERE stage_etudiant.fk_idanneeScol =tmp_anee.id_anscol_annee_scolaire AND stage_etudiant.fk_idetudiant=tmp_pers.id_type AND tmp_eleve.id_eleve_eleve = stage_etudiant.fk_idetudiant AND tmp_grpe.groupe_id = stage_etudiant.fk_idgroupe AND tmp_prof.id_type = stage_etudiant.fk_idprof_directEtud)tmp_stage_f WHERE tmp_stage_f.id_stage_etudiant = '.$id_stage;
        //var_dump($login,$pass,$sql);
        //print_r( $sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    } 
    //rapport_stage
    //id_rapport_stage	fk_id_stage	  fk_id_eleve		fk_type   date_rapport  	rapport	date_creation
    public static function set_stagerapport($fk_id_stage, $fk_id_eleve,$fk_type,$date_rapport,$rapport ){
        $db = static::getDB();

        $sql=' SELECT * FROM rapport_stage WHERE fk_id_stage= '.$fk_id_stage.' AND fk_id_eleve= '.$fk_id_eleve.' AND fk_type= '.$fk_type.' AND date_rapport= "'.$date_rapport.'" AND rapport= "'.$rapport.'" LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {


            $data = [
                'fk_id_stage' => $fk_id_stage,
                'fk_id_eleve' => $fk_id_eleve,
                'fk_type' => $fk_type,
                'date_rapport' => $date_rapport,
                'rapport' => $rapport
            ];
            $sql=' INSERT INTO rapport_stage (fk_id_stage, fk_id_eleve, fk_type, date_rapport, rapport) VALUES ( :fk_id_stage, :fk_id_eleve,:fk_type, :date_rapport, :rapport);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);

            return 1;
    

        }
        else {          

           return 0 ;
        }


       
    }

    public static function get_stagerapport($id_eleve)
    {
        $db = static::getDB();
        $sql='SELECT personne.nom_prenom,personne.contact,personne.email,rapport_stage.* FROM rapport_stage,personne WHERE rapport_stage.fk_id_eleve = personne.id_type AND fk_type = personne.type_pers AND rapport_stage.fk_id_eleve='.$id_eleve;
        //print_r( $sql);
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    } 
   
   
    //Note Eleve
    public static function get_eleves_allinfos($id_eleve) {

        $db = static::getDB();

        $sql=' SELECT tmp_elvgrpan.*,tmp_classe.class_lib FROM (SELECT tmp_ok_elvgpr.*,tmp_anee.annee_libelle ,tmp_anee.annee_date_debut ,tmp_anee.annee_date_fin  
        FROM (SELECT tmp_elv_gpe.*,tmp_libgrp.groupe_annee ,tmp_libgrp.groupe_classe ,tmp_libgrp.groupe_libelle 
              FROM (SELECT tmp_elv.*,tmp_grp.elv_ds_grpe_groupe FROM (SELECT *  FROM eleve WHERE id_eleve_eleve = '.$id_eleve.')tmp_elv 
                    INNER JOIN (SELECT * FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = '.$id_eleve.' AND elv_ds_grpe_etat = 1)tmp_grp 
                    ON tmp_elv.id_eleve_eleve = tmp_grp.elv_ds_grpe_idelev)tmp_elv_gpe INNER JOIN (SELECT groupe_id, groupe_annee, groupe_classe, groupe_libelle  FROM groupe )tmp_libgrp ON tmp_elv_gpe.elv_ds_grpe_groupe  = tmp_libgrp.groupe_id)tmp_ok_elvgpr 
                    INNER JOIN (SELECT id_anscol_annee_scolaire,annee_libelle,annee_date_debut,annee_date_fin FROM annee_scolaire )tmp_anee 
                    ON tmp_ok_elvgpr.groupe_annee = tmp_anee.id_anscol_annee_scolaire)tmp_elvgrpan 
                    INNER JOIN
                    (SELECT id_classe_classe,libelle AS class_lib FROM classe )tmp_classe 
                    ON tmp_elvgrpan.groupe_classe = tmp_classe.id_classe_classe';
        //var_dump($login,$pass,$sql);
        //exit();
        //print_r( $sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);exit;
        return $result;        
    }
    //All Groupe Eleve
    public static function get_eleves_allGrpe($id_eleve) {

        $db = static::getDB();

        $sql='SELECT groupe_id,groupe_libelle FROM groupe , eleve_estds_groupe WHERE groupe.groupe_id = eleve_estds_groupe.elv_ds_grpe_groupe AND eleve_estds_groupe.elv_ds_grpe_idelev='.$id_eleve;
        //var_dump($login,$pass,$sql);
        //exit();
        //print_r( $sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);exit;
        return $result;        
    }
    
    public static function get_eleves_grpEmploiTps($id_grp_eleve) {

        $db = static::getDB();

        $sql='SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM groupe_emploitps WHERE emploitps_etat = 1 AND emploitps_groupe_id='.$id_grp_eleve.')tmp_tps INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp ON tmp_tps.emploitps_groupe_id = tmp_grp.groupe_id)tmp_1 INNER JOIN (SELECT id_matiere_matiere,code,libele as lib_matiere FROM matiere)tmp_mat ON tmp_1.emploitps_id_mat = tmp_mat.id_matiere_matiere)tmp_2 INNER JOIN (SELECT id_salle, libelle as lib_salle,Code_salle FROM salle )tmp_salle on tmp_2.emploitps_salle = tmp_salle.id_salle)tmp_empl_tps_ok INNER JOIN (SELECT id_annee_partie,libele_partie FROM annee_partie)tmp_partanne ON tmp_empl_tps_ok.emploitps_periode =tmp_partanne.id_annee_partie';

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;        
    }
    
    public static function get_eleves_grpEval($id_grp_eleve) {

        $db = static::getDB();

        $sql='SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM prof_eval WHERE eval_etat= 2 )tmp_eval INNER JOIN (SELECT * FROM prof_eval_emploitps )tmp_evaltps ON tmp_eval.prof_eval_id = tmp_evaltps.eval_id)tmp_1 INNER JOIN (SELECT id_salle,libelle FROM salle)tmp_salle ON tmp_1.eval_salle_id = tmp_salle.id_salle)tmp_2 INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe)tmp_grp ON tmp_2.id_groupe = tmp_grp.groupe_id)tmp_3 INNER JOIN (SELECT id_type,nom_prenom FROM personne WHERE type_pers = 2)tmp_pers ON tmp_3.id_prof = tmp_pers.id_type)tmp_eval_progok WHERE tmp_eval_progok.id_groupe='.$id_grp_eleve;

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;        
    }


    public static function getAllProcoursByEleve($id_eleve)
    {
        /*
        SELECT 
                    emploitps_date AS DateCours,
                    emploitps_h_debut AS HeureDebut,
                    emploitps_h_debut AS HeureFin, 
                    salle.libelle AS SalleCours, 
                    matiere.libele AS MatiereCours,
                    personne.nom AS NomProf 
                FROM 
                    matiere, salle, groupe_emploitps, groupe, prof, personne
                WHERE 
                    groupe_emploitps.emploitps_salle = salle.id_salle AND
                    groupe_emploitps.emploitps_id_mat = matiere.id_matiere_matiere AND
                    groupe_emploitps.emploitps_id_prof = prof.id_prof_prof AND
                    eleve_estds_groupe.elv_ds_grpe_groupe = groupe.id_groupe AND
                    groupe_emploitps.emploitps_groupe_id = groupe.id_groupe AND 
                    NomProf = (SELECT nom_prenomm FROM personne WHERE prof.id_prof_prof = personne.id_pers_personne AND personne.type_pers = 2)
                    etat_pers = 1 AND type_pers = 2 AND id_type =
        */
        //var_dump($id_eleve);exit();
        $db = static::getDB();
        $sql='
            SELECT 
                *,
                emploitps_date AS DateCours,
                emploitps_h_debut AS HeureDebut,
                emploitps_h_debut AS HeureFin, 
                lib_salle AS SalleCours, 
                lib_matiere AS MatiereCours,
                nom_prenom AS ProfCours 
            FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM(SELECT * FROM (SELECT * FROM groupe_emploitps)tmp_tps INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp ON tmp_tps.emploitps_groupe_id = tmp_grp.groupe_id)tmp_1 INNER JOIN (SELECT id_matiere_matiere,code,libele as lib_matiere FROM  matiere)tmp_mat on tmp_1.emploitps_id_mat = tmp_mat.id_matiere_matiere)tmp_2 INNER JOIN (SELECT id_salle, libelle as lib_salle,Code_salle FROM salle )tmp_salle on tmp_2.emploitps_salle = tmp_salle.id_salle)tmp_4 INNER JOIN (SELECT nom_prenom,id_type FROM personne WHERE type_pers = 2)tmp_prof on tmp_4.emploitps_id_prof = tmp_prof.id_type)tmp_5 INNER JOIN (SELECT  id_annee_partie,libele_partie FROM annee_partie)tmp_partanne on tmp_5.emploitps_periode =tmp_partanne.id_annee_partie)tmp_f WHERE groupe_id IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_etat = 1 AND elv_ds_grpe_idelev ='.$id_eleve.')';
        //var_dump($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result; 
        //var_dump($result);
        //exit();      
    }
    public static function getAllEvaluationByEleve($id_eleve)
    {
        $db = static::getDB();
        $sql='
            SELECT   * 
            FROM(SELECT * FROM(SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM prof_eval WHERE eval_etat=2)tmp_eval INNER JOIN (SELECT * FROM prof_eval_emploitps )tmp_evaltps ON tmp_eval.prof_eval_id = tmp_evaltps.eval_id)tmp_1 INNER JOIN (SELECT id_salle,libelle FROM salle)tmp_salle ON tmp_1.eval_salle_id = tmp_salle.id_salle)tmp_2 INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe)tmp_grp ON tmp_2.id_groupe = tmp_grp.groupe_id)tmp_3 INNER JOIN (SELECT id_type,nom_prenom FROM personne WHERE type_pers = 2)tmp_pers on tmp_3.id_prof = tmp_pers.id_type) tmp_f WHERE id_groupe  IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_etat = 1 AND elv_ds_grpe_idelev ='.$id_eleve.')';
        //var_dump($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result; 
        //var_dump($result, $id_eleve);
        //exit();      
    }
    public static function setAnneeScolaire()
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $cree_anne_scol = htmlspecialchars($_POST["cree_anne_scol"]);
        $cree_anne_scol_dateDebut = htmlspecialchars($_POST["cree_anne_scol_dateDebut"]);
        $cree_anne_scol_dateFin = htmlspecialchars($_POST["cree_anne_scol_dateFin"]);
        $cree_anne_scol_part = (int)( htmlspecialchars($_POST["cree_anne_scol_part"]) );
        /* $cree_anne_scol_Part1 = htmlspecialchars($_POST["cree_anne_scol_Part1"]);
        $cree_anne_scol_Part1_dateDebut = htmlspecialchars($_POST["cree_anne_scol_Part1_dateDebut"]);
        $cree_anne_scol_Part1_dateDebut = htmlspecialchars($_POST["cree_anne_scol_Part2_dateFin"]);*/
        
        $sql=' SELECT * FROM annee_scolaire WHERE annee_libelle= "'.$cree_anne_scol.'" AND etat_annee = 1 LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {

            $data = [
                'annee_libelle' => $cree_anne_scol,
                'annee_date_debut' => $cree_anne_scol_dateDebut,
                'annee_date_fin' => $cree_anne_scol_dateFin
            ];
            //var_dump($db);
                    
            $sql=' INSERT INTO annee_scolaire (annee_libelle, annee_date_debut, annee_date_fin) VALUES ( :annee_libelle, :annee_date_debut, :annee_date_fin);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ( $result == TRUE) {

                for ($i=0; $i < $cree_anne_scol_part; $i++) { 
                    $data = [
                        'libele_partie' => htmlspecialchars($_POST["cree_anne_scol_Part".($i+1)]),
                        'partie_dateDebut' => htmlspecialchars($_POST["cree_anne_scol_Part".($i+1)."_dateDebut"]),
                        'partie_dateFin' =>  htmlspecialchars($_POST["cree_anne_scol_Part".($i+1)."_dateFin"]),
                        'id_anneeScolaire' => $lastid
                    ];
                    $sql=' INSERT INTO annee_partie (libele_partie, partie_dateDebut, partie_dateFin, id_anneeScolaire) VALUES ( :libele_partie, :partie_dateDebut, :partie_dateFin, :id_anneeScolaire);';
                    $stmt= $db->prepare($sql);
                    $result = $stmt->execute($data);
                }
                unset($_POST);
                return 1;
            } else {

                return -1;   
    
            }



        }
        else {          

            return 0;


        }
    }

    public static function getUniqEleve($id_eleve)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT nom_prenom,sexe,email,contact,id_eleve_eleve FROM personne INNER JOIN eleve ON personne.id_type = eleve.id_eleve_eleve WHERE etat_pers = 1 AND type_pers = 2 AND id_type ='.$id_eleve;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }
    public static function getEleveMat($id_eleve){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM(SELECT id_mat FROM eleve_matiere WHERE id_eleve = '.$id_eleve.' AND etat_eleve_mat = 1 )tmp_eleve INNER JOIN (SELECT id_matiere_matiere,code,libele FROM matiere WHERE etat =1)tmp_mat ON tmp_eleve.id_mat= tmp_mat.id_matiere_matiere';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }

    public static function setEleveMat($id_matiere, $id_eleve){
        $db = static::getDB();

        $sql=' SELECT * FROM eleve_matiere WHERE id_eleve= "'.$id_eleve.'" AND id_mat= "'.$id_matiere.'"  LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {

            $data = [
                'id_eleve' => $id_eleve,
                'id_mat' => $id_matiere
            ];
            //var_dump($db);
                    
            $sql=' INSERT INTO eleve_matiere (id_eleve, id_mat) VALUES ( :id_eleve, :id_mat);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ( $result == TRUE) { return 'ajouter';} else {return 'erreur';   }
        }
        else {          

            $sql=' UPDATE eleve_matiere SET etat_eleve_mat = 1 WHERE id_eleve ='.$id_eleve.' AND id_mat = '.$id_matiere.' ;';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            if ( $result == TRUE) { return 'ajouter';} else {return 'erreur';   }
        }


       
    }

    public static function setSupEleveMat($id_matiere, $id_eleve){
        $db = static::getDB();

        $sql=' SELECT * FROM eleve_matiere WHERE id_eleve= "'.$id_eleve.'" AND id_mat= "'.$id_matiere.'"  LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {

            return 'inconnu'; 
        }
        else {          

            $sql=' UPDATE eleve_matiere SET etat_eleve_mat = 0 WHERE id_eleve ='.$id_eleve.' AND id_mat = '.$id_matiere.' ;';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            if ( $result == TRUE) { return 'supprimer';} else {return 'erreur';   }
        }


       
    }
 

    public static function getEleveMatriere($id_eleve){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM (SELECT id_mat FROM eleve_matiere WHERE id_eleve = '.$id_eleve.' AND etat_eleve_mat = 1)tmp_elevemat INNER JOIN (SELECT id_matiere_matiere,code,libele FROM matiere)tmp_mat ON tmp_elevemat.id_mat = tmp_mat.id_matiere_matiere';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }
    public static function getEleve_grpeMatParent($id_eleve){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql='SELECT * FROM (SELECT tmp_matcoef.*,tmp_mat.mat_code ,tmp_mat.mat_lib FROM ( SELECT * FROM groupe_matiere_coef WHERE matiere_parent_id_tmp = 0)tmp_matcoef INNER JOIN (SELECT id_matiere_matiere, CODE AS mat_code ,libele AS mat_lib FROM matiere WHERE id_matiere_matiere ) tmp_mat ON tmp_matcoef.matiere_id_tmp = tmp_mat.id_matiere_matiere)tmp_matparent WHERE groupe_id_tmp IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = '.$id_eleve.' AND elv_ds_grpe_etat = 1)';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }
    public static function getEleve_grpeMatFils($id_eleve, $id_matparent){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql='SELECT * FROM (SELECT tmp_matcoef.*,tmp_mat.mat_code ,tmp_mat.mat_lib FROM ( SELECT * FROM groupe_matiere_coef WHERE matiere_parent_id_tmp <> 0)tmp_matcoef INNER JOIN (SELECT id_matiere_matiere, CODE AS mat_code ,libele AS mat_lib FROM matiere WHERE id_matiere_matiere ) tmp_mat ON tmp_matcoef.matiere_id_tmp = tmp_mat.id_matiere_matiere)tmp_matparent WHERE groupe_id_tmp IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = '.$id_eleve.' AND elv_ds_grpe_etat = 1 AND tmp_matparent.matiere_parent_id_tmp = '.$id_matparent.' )';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }


   
    public static function setEleveGrpEval($id_grpe ,$id_mat ,$eval_lib ,$eval_desc){
        //eleve_eval_id 	id_eleve 	id_groupe 	id_mat 	eval_libelle 	eval_desc 	eval_etat 
        $id_eleve = intval($_SESSION['user']['id_type']);
        // var_dump($_POST,$_SESSION);
        //var_dump($id_eleve);exit;
        $db = static::getDB();
        //id_eleve 	id_groupe 	etat_eleve_classe 
        $sql=' SELECT * FROM eleve_eval WHERE id_eleve= "'.$id_eleve.'" AND id_groupe= "'.$id_grpe.'" AND id_mat= "'.$id_mat.'" AND eval_libelle= "'.$eval_lib.'"  LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {

            $data = [
                'id_eleve' => $id_eleve,
                'id_groupe' => $id_grpe,
                'id_mat' => $id_mat,
                'eval_libelle' => $eval_lib,
                'eval_desc' => $eval_desc
            ];
            //var_dump($db);
                    
            $sql=' INSERT INTO eleve_eval (id_eleve, id_groupe, id_mat, eval_libelle, eval_desc) VALUES ( :id_eleve, :id_groupe, :id_mat, :eval_libelle, :eval_desc);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ( $result == TRUE) { 
                //eleve_eval_emploiTps_id 	eval_id 	eval_date 	eval_hDebut 	eval_hFin 	etat_evalTps 
                $data = [
                    'eval_id' => $lastid
                ];
                //var_dump($db);
                $sql=' INSERT INTO eleve_eval_emploitps (eval_id) VALUES ( :eval_id);';
                $stmt= $db->prepare($sql);
                $result = $stmt->execute($data);
                //$lastid =  $db->lastInsertId();
                return 'ajouter';
            } 
            else {return 'erreur';   }
        }
        else {          

            $sql=' UPDATE eleve_eval SET eval_etat = 1 WHERE id_eleve ='.$id_eleve.' AND id_groupe = '.$id_grpe.' AND id_mat = '.$id_mat.' AND eval_libelle = "'.$eval_lib.'" ;';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            if ( $result == TRUE) { return 'ajouter';} else {return 'erreur';   }
        }
    }
    public static function getEleveGrpEval(){
        $id_eleve = intval($_SESSION['user']['id_type']);
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM(SELECT * FROM(SELECT id_groupe,eleve_eval_id,id_mat,eval_libelle,eval_desc FROM eleve_eval WHERE id_eleve = '. $id_eleve .' AND eval_etat = 1) tmp_evaleleve INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp ON tmp_evaleleve.id_groupe =tmp_grp.groupe_id)tmp_eleveeval INNER JOIN (SELECT id_matiere_matiere,code,libele AS matierelib FROM matiere)tmp_mat ON tmp_eleveeval.id_mat = tmp_mat.id_matiere_matiere ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;   
    }
    public static function getEleveGrpEvalWithDate(){
        $id_eleve = intval($_SESSION['user']['id_type']);
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM  (SELECT * FROM(SELECT * FROM(SELECT id_groupe,eleve_eval_id,id_mat,eval_libelle,eval_desc FROM eleve_eval WHERE id_eleve = '.$id_eleve.' AND eval_etat = 1) tmp_evaleleve INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp ON tmp_evaleleve.id_groupe =tmp_grp.groupe_id)tmp_eleveeval INNER JOIN (SELECT id_matiere_matiere,code,libele AS matierelib FROM matiere)tmp_mat ON tmp_eleveeval.id_mat = tmp_mat.id_matiere_matiere  )tmp_eleveeval INNER JOIN (SELECT * FROM eleve_eval_emploitps ORDER BY eleve_eval_emploiTps_id ASC)tmp_evaltps ON tmp_eleveeval.eleve_eval_id = tmp_evaltps.eval_id';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;   
    }
    public static function getEleveGrpEvalWithDateBy($eval_id){
        $id_eleve = intval($_SESSION['user']['id_type']);
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM  (SELECT * FROM(SELECT * FROM(SELECT id_groupe,eleve_eval_id,id_mat,eval_libelle,date_creation_eval,eval_desc FROM prof_eval WHERE id_eleve = '.$id_eleve.' AND eleve_eval_id = '.$eval_id.' AND eval_etat = 1) tmp_evaleleve INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp ON tmp_evaleleve.id_groupe =tmp_grp.groupe_id)tmp_eleveeval INNER JOIN (SELECT id_matiere_matiere,code,libele AS matierelib FROM matiere)tmp_mat ON tmp_eleveeval.id_mat = tmp_mat.id_matiere_matiere  )tmp_eleveeval INNER JOIN (SELECT * FROM eleve_eval_emploitps ORDER BY eleve_eval_emploiTps_id ASC)tmp_evaltps ON tmp_eleveeval.eleve_eval_id = tmp_evaltps.eval_id';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;   
    }

    public static function getEvalSalle($id_eval){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM(SELECT eval_salle_id FROM eleve_eval_emploitps WHERE eval_id = '.$id_eval.')tmp_idsalle INNER JOIN (SELECT * FROM salle)tmp_salle ON tmp_idsalle.eval_salle_id = tmp_salle.id_salle';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }

    public static function getAllEleveByGroup($id_eval){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM(SELECT * FROM(SELECT * FROM(SELECT id_type,nom_prenom,sexe,email,contact FROM personne WHERE type_pers = 1 AND etat_pers = 1)tmp_elev INNER JOIN (SELECT elv_ds_grpe_idelev,elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_etat =1)tmp_grpele ON tmp_elev.id_type = tmp_grpele.elv_ds_grpe_idelev)tmp_a INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1)tmp_b ON tmp_a.id_type = tmp_b.id_eleve_eleve)tmp_aa INNER JOIN (SELECT id_groupe,groupe_libelle FROM(SELECT * FROM groupe)tmp_grp INNER JOIN (SELECT id_groupe FROM eleve_eval WHERE eval_etat=1 AND eleve_eval_id = '.$id_eval.')tmp_grpeval ON tmp_grp.groupe_id = tmp_grpeval.id_groupe) tmp_bb on tmp_aa.elv_ds_grpe_groupe = tmp_bb.id_groupe';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }

    public static function setGetInitEleveEvalNote($id_eleve, $id_eval){
        $db = static::getDB();
        $etatsql = 0;
        //id_notes_notes 	id_eleve_eleve 	id_evaluation 	note 
        $sql=' SELECT * FROM notes WHERE id_eleve_eleve = '.$id_eleve.' AND id_evaluation = '.$id_eval.' LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result,$id_eleve, $id_eval);exit;
        if ( empty($result) || $result==0) {

            $data = [
                'id_eleve_eleve' => $id_eleve,
                'id_evaluation' => $id_eval
            ];
            //var_dump($db);
                    
            $sql=' INSERT INTO notes (id_eleve_eleve, id_evaluation) VALUES ( :id_eleve_eleve, :id_evaluation)';
            //var_dump($sql,$data);exit;
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            //$lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ( $result == TRUE) { $etatsql=1;} 
            else {$etatsql=0; }
        }

        $sql=' SELECT * FROM(SELECT * FROM(SELECT * FROM(SELECT * FROM(SELECT id_type,nom_prenom,sexe,email,contact FROM personne WHERE type_pers = 1 AND etat_pers = 1)tmp_elev INNER JOIN (SELECT elv_ds_grpe_idelev,elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_etat =1)tmp_grpele ON tmp_elev.id_type = tmp_grpele.elv_ds_grpe_idelev)tmp_a INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1)tmp_b ON tmp_a.id_type = tmp_b.id_eleve_eleve)tmp_aa INNER JOIN (SELECT id_groupe,groupe_libelle,eleve_eval_id FROM(SELECT * FROM groupe)tmp_grp INNER JOIN (SELECT id_groupe,eleve_eval_id FROM eleve_eval WHERE eval_etat=1 AND eleve_eval_id = '.$id_eval.')tmp_grpeval ON tmp_grp.groupe_id = tmp_grpeval.id_groupe) tmp_bb on tmp_aa.elv_ds_grpe_groupe = tmp_bb.id_groupe)tmp_finaltab INNER JOIN (SELECT * FROM notes WHERE id_eleve_eleve = '.$id_eleve.' )tmp_notes ON tmp_finaltab.eleve_eval_id = tmp_notes.id_evaluation';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;        
    }

    public static function getAllEleveNoteBy($id_eleve){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql='SELECT * FROM(
                SELECT note,fk_idpartAneeScol,libele_partie,eval_libelle, coef, notation, eval_date, tmp_sess.Libelle_session,eval_id , id_mat, id_groupe, id_prof FROM (
                        SELECT * FROM(
                            SELECT tmp_evalTps.*,annee_partie.libele_partie FROM(
                                SELECT * FROM prof_eval 
                                INNER JOIN prof_eval_emploitps 
                                ON prof_eval.prof_eval_id = prof_eval_emploitps.eval_id
                            )tmp_evalTps, annee_partie
                            WHERE  id_groupe IN 
                                (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = '.$id_eleve.' AND elv_ds_grpe_etat = 1) 
                            AND annee_partie.id_annee_partie=tmp_evalTps.fk_idpartAneeScol
                        ) tmp_f_a 
                        INNER JOIN 
                            (SELECT id_evaluation,note FROM notes WHERE id_eleve_eleve = '.$id_eleve.' AND etat_note = 1 )tmp_f_b 
                        ON tmp_f_a.prof_eval_id = tmp_f_b.id_evaluation)tmp_finalok 
                
                INNER JOIN 
                    (SELECT id_session_session, Libelle_session FROM annee_session)tmp_sess 
                ON tmp_finalok.eval_session = tmp_sess.id_session_session)tmp_final_b 
            INNER JOIN (SELECT code AS code_mat,libele AS lib_mat,id_matiere_matiere FROM matiere)tmp_mat 
            ON tmp_final_b.id_mat =tmp_mat.id_matiere_matiere';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }

    public static function getAllEleve_Mat_MoyenneBy($id_eleve){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql='SELECT * FROM (SELECT * FROM (SELECT * FROM moyenne WHERE  id_eleve = '.$id_eleve.' AND id_groupe IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = '.$id_eleve.' AND elv_ds_grpe_etat = 1))tmp_matmoy INNER JOIN (SELECT id_session_session,Libelle_session FROM annee_session)tmp_session ON tmp_matmoy.id_session =  tmp_session.id_session_session )tmp_f_a INNER JOIN (SELECT matiere_id_tmp, coeficient_tmp,libele_partie,id_annee_partie,mat_cod	,mat_lib FROM (SELECT * FROM(SELECT matiere_id_tmp,coeficient_tmp,groupe_id_tmp,tmp_partannee.libele_partie,tmp_partannee.id_annee_partie FROM groupe_matiere_coef INNER JOIN (SELECT id_annee_partie, libele_partie FROM annee_partie)tmp_partannee ON groupe_matiere_coef.part_annee_id_tmp = tmp_partannee.id_annee_partie)tmp_a WHERE  tmp_a.groupe_id_tmp IN(SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = '.$id_eleve.' AND elv_ds_grpe_etat = 1) )tmp_mat_per INNER JOIN (SELECT id_matiere_matiere,code AS mat_cod, libele AS mat_lib FROM matiere)tmp_mat ON  tmp_mat_per.matiere_id_tmp = tmp_mat.id_matiere_matiere)tmp_f_b ON tmp_f_a.id_matiere = tmp_f_b.matiere_id_tmp';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }

    
    public static function get_elevGrp_anneePart($id_eleve){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        $sql='SELECT * FROM (SELECT * FROM annee_scolaire INNER JOIN annee_partie ON annee_scolaire.id_anscol_annee_scolaire =annee_partie.id_anneeScolaire ORDER BY annee_scolaire.id_anscol_annee_scolaire)tmp_annee_part WHERE id_anneeScolaire IN (SELECT groupe_annee FROM groupe WHERE groupe_id IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = '.$id_eleve.' AND elv_ds_grpe_etat = 1) )';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }

    public static function getAllEleve_UniqMaxMat_MoyenneBy($id_eleve, $id_partannee){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql='CREATE TEMPORARY TABLE tmp_tempory_table SELECT * FROM (SELECT id_groupe,id_eleve,id_matiere,id_prof,id_session,moyenne,etat_moy,id_session_session,Libelle_session,matiere_id_tmp,coeficient_tmp, libele_partie,id_annee_partie, mat_cod, mat_lib  FROM(SELECT * FROM (SELECT * FROM (SELECT * FROM moyenne WHERE etat_moy=1 AND id_eleve = '.$id_eleve.' AND id_groupe IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = '.$id_eleve.' AND elv_ds_grpe_etat = 1))tmp_matmoy INNER JOIN (SELECT id_session_session,Libelle_session FROM annee_session)tmp_session ON tmp_matmoy.id_session =  tmp_session.id_session_session )tmp_f_a INNER JOIN (SELECT matiere_id_tmp, coeficient_tmp,libele_partie,id_annee_partie,mat_cod,mat_lib FROM (SELECT * FROM(SELECT matiere_id_tmp,coeficient_tmp,groupe_id_tmp,tmp_partannee.libele_partie,tmp_partannee.id_annee_partie FROM groupe_matiere_coef INNER JOIN (SELECT id_annee_partie, libele_partie FROM annee_partie)tmp_partannee ON groupe_matiere_coef.part_annee_id_tmp = tmp_partannee.id_annee_partie)tmp_a WHERE  tmp_a.groupe_id_tmp IN(SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = '.$id_eleve.' AND elv_ds_grpe_etat = 1) )tmp_mat_per INNER JOIN (SELECT id_matiere_matiere,code AS mat_cod, libele AS mat_lib FROM matiere)tmp_mat ON  tmp_mat_per.matiere_id_tmp = tmp_mat.id_matiere_matiere)tmp_f_b ON tmp_f_a.id_matiere = tmp_f_b.matiere_id_tmp  ORDER BY  mat_cod ASC)tmp_moy_ok_final)tmp_moy_ok_final_a WHERE tmp_moy_ok_final_a.id_annee_partie = '.$id_partannee.';';
        //print_r($sql);
        //exit();
        $stmt = $db->query($sql);
        //$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        

        $sql='SELECT * FROM ( SELECT *, RANK() OVER (PARTITION BY matiere_id_tmp ORDER BY moyenne DESC) dest_rank FROM tmp_tempory_table )tmp_tble where dest_rank = 1;';
        //print_r($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        return $result;       
    }
    
    //Absences

    

    /** TABLE : absences
    * id_absences 	id_eleve 	id_matiere 	id_groupe 	abs_debut 	abs_fin 	abs_date 	abs_motifs 	etat_abs 
    */
    public static function get_eleve_abs($id_eleve){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        $sql='SELECT a.*, m.code,m.libele AS mat_lib,g.groupe_libelle  FROM absences a, matiere m, groupe g   WHERE a.etat_abs =1 AND a.id_matiere = m.id_matiere_matiere AND a.id_groupe IN (SELECT elv.elv_ds_grpe_groupe FROM eleve_estds_groupe elv WHERE elv_ds_grpe_etat = 1 AND elv_ds_grpe_idelev = '.$id_eleve.') AND a.id_eleve = '.$id_eleve.' AND g.groupe_id = a.id_groupe';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }
    
        

    
}
