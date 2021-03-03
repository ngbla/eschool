<?php

namespace App\Models;

use PDO;

date_default_timezone_set("Africa/Abidjan");
/*
* SQL recuperer une bd 
SELECT * FROM INFORMATION_SCHEMA.TABLES
*/
/**
 * Example user model
 *
 * PHP version 7.0
 */
class Prof extends \Core\Model
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
    /**
     * Get all the users as an associative array
     *
     * @return array
     */

    public static function setAnneeScolaire(){
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

    public static function getUniqProf($id_prof){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT id_pers_personne AS prof_id_pers,nom_prenom,sexe,email,contact,id_prof_prof FROM personne INNER JOIN prof ON personne.id_type = prof.id_prof_prof WHERE etat_pers = 1 AND type_pers = 2 AND id_type ='.$id_prof;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }
    public static function getStageByProf($prof_idpers)
    {
        $db = static::getDB();
        //$sql=' SELECT * FROM stage_etudiant WHERE fk_idetudiant = '.$id_eleve;
        $sql='SELECT * FROM (SELECT stage_etudiant.*,tmp_pers.nom_prenom,tmp_eleve.matricule,tmp_grpe.* ,tmp_prof.id_pers_personne AS prof_idpers ,tmp_prof.nom_prenom AS prof_nomprenom,tmp_prof.prof_tel  FROM 
        stage_etudiant,(SELECT id_anscol_annee_scolaire FROM annee_scolaire ORDER by annee_libelle DESC)tmp_anee,(SELECT nom_prenom,id_type FROM personne WHERE type_pers = 1 AND etat_pers = 1)tmp_pers, (SELECT matricule,id_eleve_eleve FROM eleve)tmp_eleve , (SELECT groupe.groupe_id, groupe.groupe_libelle,niveau.libelle_niveau,classe.libelle AS lib_classe FROM groupe,niveau,classe  WHERE niveau.id_niveau = groupe.fk_idniveau AND classe.id_classe_classe = groupe.groupe_classe)tmp_grpe,(SELECT  id_pers_personne,nom_prenom,id_type,contact AS prof_tel FROM personne WHERE type_pers = 2 )tmp_prof
        WHERE stage_etudiant.fk_idanneeScol =tmp_anee.id_anscol_annee_scolaire AND stage_etudiant.fk_idetudiant=tmp_pers.id_type AND tmp_eleve.id_eleve_eleve = stage_etudiant.fk_idetudiant AND tmp_grpe.groupe_id = stage_etudiant.fk_idgroupe AND tmp_prof.id_type = stage_etudiant.fk_idprof_directEtud)tmp_fok WHERE  tmp_fok.etat_stage = 1 AND tmp_fok.prof_idpers = '.$prof_idpers;
        //print_r($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    } 
    public static function getProfMat($id_prof){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM(SELECT id_mat FROM prof_matiere WHERE id_prof = '.$id_prof.' AND etat_prof_mat = 1 )tmp_prof INNER JOIN (SELECT id_matiere_matiere,code,libele FROM matiere WHERE etat =1)tmp_mat ON tmp_prof.id_mat= tmp_mat.id_matiere_matiere';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }

    public static function setProfMat($id_matiere, $id_prof){
        $db = static::getDB();

        $sql=' SELECT * FROM prof_matiere WHERE id_prof= "'.$id_prof.'" AND id_mat= "'.$id_matiere.'"  LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {

            $data = [
                'id_prof' => $id_prof,
                'id_mat' => $id_matiere
            ];
            //var_dump($db);
                    
            $sql=' INSERT INTO prof_matiere (id_prof, id_mat) VALUES ( :id_prof, :id_mat);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ( $result == TRUE) { return 'ajouter';} else {return 'erreur';   }
        }
        else {          

            $sql=' UPDATE prof_matiere SET etat_prof_mat = 1 WHERE id_prof ='.$id_prof.' AND id_mat = '.$id_matiere.' ;';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            if ( $result == TRUE) { return 'ajouter';} else {return 'erreur';   }
        }


       
    }

    public static function setSupProfMat($id_matiere, $id_prof){
        $db = static::getDB();

        $sql=' SELECT * FROM prof_matiere WHERE id_prof= "'.$id_prof.'" AND id_mat= "'.$id_matiere.'"  LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {

            return 'inconnu'; 
        }
        else {          

            $sql=' UPDATE prof_matiere SET etat_prof_mat = 0 WHERE id_prof ='.$id_prof.' AND id_mat = '.$id_matiere.' ;';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            if ( $result == TRUE) { return 'supprimer';} else {return 'erreur';   }
        }


       
    }

    public static function getProfGroupe($id_prof){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM (SELECT * FROM(SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp INNER JOIN (SELECT id_prof,id_groupe,id_mat FROM prof_classe WHERE id_prof='.$id_prof.' AND etat_prof_classe = 1 )tmp_prof ON tmp_grp.groupe_id =tmp_prof.id_groupe)tmp_fa INNER JOIN (SELECT id_matiere_matiere,code,libele as libmat FROM matiere)tmp_mat ON tmp_fa.id_mat = tmp_mat.id_matiere_matiere ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }

    public static function setProfGroupe($id_groupe, $id_prof, $id_mat){
        $db = static::getDB();
        //id_prof 	id_groupe 	etat_prof_classe 
        //id_prof	id_groupe	id_mat	etat_prof_classe
        $sql=' SELECT * FROM prof_classe WHERE id_prof= "'.$id_prof.'" AND id_groupe= "'.$id_groupe.'" AND id_mat= "'.$id_mat.'"  LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {

            $data = [
                'id_prof' => $id_prof,
                'id_groupe' => $id_groupe,
                'id_mat' => $id_mat
            ];
            //var_dump($db);
                    
            $sql=' INSERT INTO prof_classe (id_prof, id_groupe , id_mat) VALUES ( :id_prof, :id_groupe , :id_mat);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ( $result == TRUE) { return 'ajouter';} else {return 'erreur';   }
        }
        else {          

            $sql=' UPDATE prof_classe SET etat_prof_classe = 1 WHERE id_prof ='.$id_prof.' AND id_groupe = '.$id_groupe.' ;';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            if ( $result == TRUE) { return 'ajouter';} else {return 'erreur';   }
        }


       
    }

    public static function setSupProfGroupe($id_groupe, $id_prof, $id_mat){
        $db = static::getDB();

        $sql=' SELECT * FROM prof_classe WHERE id_prof= "'.$id_prof.'" AND id_groupe= "'.$id_groupe.'"  AND id_mat= "'.$id_mat.'" LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {

            return 'inconnu'; 
        }
        else {          

            //$sql=' UPDATE prof_classe SET etat_prof_classe = 0 WHERE id_prof ='.$id_prof.' AND id_groupe = '.$id_groupe.' ;';
            $sql=' DELETE FROM prof_classe WHERE prof_classe.id_prof ='.$id_prof.' AND prof_classe.id_groupe = '.$id_groupe.' AND prof_classe.id_mat = '.$id_mat.' ;';
            
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            //
            //$lastid =  $db->lastInsertId();
            if ( $result == TRUE) { return 'supprimer';} else {return 'erreur';   }
        }


       
    }

    public static function getNbreProfGrpe($id_prof){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT COUNT(id_groupe) as nbre_groupe FROM (SELECT * FROM prof_classe WHERE etat_prof_classe = 1 AND  id_prof ='.$id_prof.' GROUP BY id_groupe)tmp_grp';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }
    public static function getNbreProfMatriere($id_prof){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT COUNT(id_mat) as nbre_matiere FROM prof_matiere WHERE etat_prof_mat = 1 AND  id_prof ='.$id_prof;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }

    public static function getProfGrpe($id_prof){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM(SELECT * FROM prof_classe WHERE etat_prof_classe = 1 AND id_prof='.$id_prof.' )tmp_prof_grp INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1 )tmp_grp ON tmp_prof_grp.id_groupe = tmp_grp.groupe_id';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }
    public static function getProfGrpe_NoDoublons($id_prof){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM(SELECT * FROM prof_classe WHERE etat_prof_classe = 1 AND id_prof='.$id_prof.' )tmp_prof_grp INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1 )tmp_grp ON tmp_prof_grp.id_groupe = tmp_grp.groupe_id GROUP BY groupe_libelle';
        //print_r($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }
    
    public static function getAllGroupeANDmat($id_prof){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT *FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM prof_classe WHERE id_prof = '.$id_prof.')tmp_pclase INNER JOIN (SELECT groupe_id,groupe_annee, groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp ON tmp_pclase.id_groupe = tmp_grp.groupe_id) tmp_1 INNER JOIN (SELECT id_matiere_matiere, code ,libele as mat_lib FROM matiere)tmp_mat ON tmp_1.id_mat = tmp_mat.id_matiere_matiere)tmp_final INNER JOIN (SELECT id_anscol_annee_scolaire,annee_libelle FROM annee_scolaire WHERE etat_annee = 1)tmp_anneeScol ON tmp_final.groupe_annee = tmp_anneeScol.id_anscol_annee_scolaire ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }
    

    public static function getProfMatriere($id_prof){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM (SELECT id_mat FROM prof_matiere WHERE id_prof = '.$id_prof.' AND etat_prof_mat = 1)tmp_profmat INNER JOIN (SELECT id_matiere_matiere,code,libele FROM matiere)tmp_mat ON tmp_profmat.id_mat = tmp_mat.id_matiere_matiere';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }

    public static function getProfMatriereBy($id_prof, $id_groupe){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql='SELECT * FROM (SELECT * FROM (SELECT id_mat FROM prof_matiere WHERE id_prof = '.$id_prof.' AND etat_prof_mat = 1)tmp_profmat INNER JOIN (SELECT id_matiere_matiere,code,libele FROM matiere)tmp_mat ON tmp_profmat.id_mat = tmp_mat.id_matiere_matiere)tmp_okprof WHERE tmp_okprof.id_matiere_matiere IN (SELECT id_matiere FROM (SELECT * FROM classe_matiere WHERE etat = 1)tmp_clasemat INNER JOIN (SELECT * FROM groupe WHERE groupe_id = '.$id_groupe.')tmpgpe ON tmp_clasemat.id_classe = tmpgpe.groupe_classe) ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }

   
    public static function setProfGrpEval($id_grpe ,$id_mat ,$eval_lib ,$eval_desc, $id_session ,$eval_type, $eval_date,$eval_hDebut ,$eval_hFin, $id_periode ){
        //prof_eval_id 	id_prof 	id_groupe 	id_mat 	eval_libelle 	eval_desc 	eval_etat 
        //prof_eval_id	id_prof	  id_groupe	  id_mat 	eval_libelle	eval_desc	date_creation_eval	eval_session eval_type	eval_etat
        $id_prof = intval($_SESSION['user']['id_type']);
        // var_dump($_POST,$_SESSION);
        //var_dump($id_prof);exit;
        $db = static::getDB();
        //id_prof 	id_groupe 	etat_prof_classe 
        $sql=' SELECT * FROM prof_eval WHERE id_prof= "'.$id_prof.'" AND fk_idpartAneeScol= "'.$id_periode.'" AND id_groupe= "'.$id_grpe.'" AND id_mat= "'.$id_mat.'" AND eval_libelle= "'.$eval_lib.'" AND eval_type= "'.$eval_type.'" LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {

            $data = [
                'fk_idpartAneeScol' => $id_periode,
                'id_prof' => $id_prof,
                'id_groupe' => $id_grpe,
                'id_mat' => $id_mat,
                'eval_libelle' => $eval_lib,
                'eval_desc' => $eval_desc,
                'eval_session' => $id_session,
                'eval_type' => $eval_type
            ];
            //var_dump($db);
                    
            $sql=' INSERT INTO prof_eval (fk_idpartAneeScol, id_prof, id_groupe, id_mat, eval_libelle, eval_desc, eval_session, eval_type) VALUES (:fk_idpartAneeScol,  :id_prof, :id_groupe, :id_mat, :eval_libelle, :eval_desc, :eval_session, :eval_type);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ( $result == TRUE) { 
                //prof_eval_emploiTps_id 	eval_id 	eval_date 	eval_hDebut 	eval_hFin 	etat_evalTps 
                //	  coef   notation
                
                $data = [
                    'eval_id' => $lastid,
                    'eval_date' => $eval_date,
                    'eval_hDebut' => $eval_hDebut,
                    'eval_hFin' => $eval_hFin
                ];
                //var_dump($db);
                $sql=' INSERT INTO prof_eval_emploitps (eval_id, eval_date, eval_hDebut, eval_hFin) VALUES ( :eval_id, :eval_date, :eval_hDebut, :eval_hFin);';
                $stmt= $db->prepare($sql);
                $result = $stmt->execute($data);
                //$lastid =  $db->lastInsertId();
                return 'ajouter';
            } 
            else {return 'erreur';   }
        }
        else {          

            $sql=' UPDATE prof_eval SET eval_etat = 1 WHERE id_prof ='.$id_prof.' AND id_groupe = '.$id_grpe.' AND id_mat = '.$id_mat.' AND eval_libelle = "'.$eval_lib.'" AND eval_type = "'.$eval_type.'" ;';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            if ( $result == TRUE) { return 'ajouter';} else {return 'erreur';   }
        }
    }

    public static function setProfGrpEvalNEWS($id_grpe ,$id_mat ,$eval_lib ,$eval_desc, $id_session ,$eval_type, $eval_date,$eval_hDebut ,$eval_hFin, $id_periode,$coef,$notation ){
                        
        //prof_eval_id 	id_prof 	id_groupe 	id_mat 	eval_libelle 	eval_desc 	eval_etat 
        //prof_eval_id	id_prof	  id_groupe	  id_mat 	eval_libelle	eval_desc	date_creation_eval	eval_session eval_type	eval_etat
        $id_prof = intval($_SESSION['user']['id_type']);
        // var_dump($_POST,$_SESSION);
        //var_dump($id_prof);exit;
        $db = static::getDB();
        //id_prof 	id_groupe 	etat_prof_classe 
        $sql=' SELECT * FROM prof_eval WHERE id_prof= "'.$id_prof.'" AND fk_idpartAneeScol= "'.$id_periode.'" AND id_groupe= "'.$id_grpe.'" AND id_mat= "'.$id_mat.'" AND eval_libelle= "'.$eval_lib.'" AND eval_type= "'.$eval_type.'" LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {

            $data = [
                'fk_idpartAneeScol' => $id_periode,
                'id_prof' => $id_prof,
                'id_groupe' => $id_grpe,
                'id_mat' => $id_mat,
                'eval_libelle' => $eval_lib,
                'eval_desc' => $eval_desc,
                'eval_session' => $id_session,
                'eval_type' => $eval_type
            ];
            //var_dump($db);
                    
            $sql=' INSERT INTO prof_eval (fk_idpartAneeScol, id_prof, id_groupe, id_mat, eval_libelle, eval_desc, eval_session, eval_type) VALUES (:fk_idpartAneeScol,  :id_prof, :id_groupe, :id_mat, :eval_libelle, :eval_desc, :eval_session, :eval_type);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ( $result == TRUE) { 
                //prof_eval_emploiTps_id 	eval_id 	eval_date 	eval_hDebut 	eval_hFin 	etat_evalTps 
                //	  coef   notation
                
                $data = [
                    'eval_id' => $lastid,
                    'eval_date' => $eval_date,
                    'eval_hDebut' => $eval_hDebut,
                    'eval_hFin' => $eval_hFin,
                    'coef' => $coef,
                    'notation' => $notation,
                ];
                //var_dump($db);
                $sql=' INSERT INTO prof_eval_emploitps (eval_id, eval_date, eval_hDebut, eval_hFin, coef, notation) VALUES ( :eval_id, :eval_date, :eval_hDebut, :eval_hFin, :coef, :notation);';
                $stmt= $db->prepare($sql);
                $result = $stmt->execute($data);
                //$lastid =  $db->lastInsertId();
                return 'ajouter';
            } 
            else {return 'erreur';   }
        }
        else {          

            $sql=' UPDATE prof_eval SET eval_etat = 3 WHERE id_prof ='.$id_prof.' AND id_groupe = '.$id_grpe.' AND id_mat = '.$id_mat.' AND eval_libelle = "'.$eval_lib.'" AND eval_type = "'.$eval_type.'" ;';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            if ( $result == TRUE) { return 'ajouter';} else {return 'erreur';   }
        }
    }

    public static function update_profeval($id_prof ,$id_profeval ,$etatval ){
        //prof_eval_id 	id_prof 	id_groupe 	id_mat 	eval_libelle 	eval_desc 	eval_etat 
        //prof_eval_id	id_prof	  id_groupe	  id_mat 	eval_libelle	eval_desc	date_creation_eval	eval_session eval_type	eval_etat
        // var_dump($_POST,$_SESSION);
        //var_dump($id_prof);exit;
        $db = static::getDB();
        //id_prof 	id_groupe 	etat_prof_classe 
        //UPDATE `prof_eval` SET `eval_etat` = '1' WHERE `prof_eval`.`prof_eval_id` = 7;
        $sql=' SELECT * FROM prof_eval WHERE prof_eval_id= '.$id_profeval.'  LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( !empty($result) && $result!=0) {
            $sql=' UPDATE prof_eval SET eval_etat ='.$etatval.' WHERE id_prof ='.$id_prof.' AND prof_eval_id = '.$id_profeval.' ;';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            //var_dump($lastid);
            if ( $result == TRUE) { return 'success'; } 
            else {return 'warning';   }
        }
        else {   return 'danger';  }
    }
    public static function getProfGrpEval(){
        $id_prof = intval($_SESSION['user']['id_type']);
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql='SELECT tmp_f_pe.*,annee_partie.libele_partie FROM( SELECT* FROM(SELECT * FROM(SELECT * FROM(SELECT date_creation_eval, eval_type, id_groupe,prof_eval_id,id_mat,eval_libelle,eval_desc,eval_session,fk_idpartAneeScol FROM prof_eval WHERE id_prof = '.$id_prof.' AND eval_etat != 3) tmp_evalprof INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp ON tmp_evalprof.id_groupe =tmp_grp.groupe_id)tmp_profeval INNER JOIN (SELECT id_matiere_matiere,code,libele AS matierelib FROM matiere)tmp_mat ON tmp_profeval.id_mat = tmp_mat.id_matiere_matiere)tmp_final INNER JOIN (SELECT id_session_session,Libelle_session FROM annee_session)tmp_session ON tmp_final.eval_session = tmp_session.id_session_session)tmp_f_pe,annee_partie WHERE tmp_f_pe.fk_idpartAneeScol=annee_partie.id_annee_partie ';
        
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);exit();
        return $result;   
    }
    public static function getProfGrpEval_enAttente($id_prof){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM 
            (SELECT * FROM
                (SELECT * FROM
                    (SELECT * FROM
                        (SELECT id_groupe,prof_eval_id,id_mat,eval_libelle,eval_desc,eval_session,eval_type,fk_idpartAneeScol,annee_partie.libele_partie FROM prof_eval,annee_partie WHERE id_prof = '.$id_prof.' AND eval_etat = 3 AND prof_eval.fk_idpartAneeScol = annee_partie.id_annee_partie) tmp_evalprof 
                    INNER JOIN 
                        (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp 
                    ON tmp_evalprof.id_groupe =tmp_grp.groupe_id)tmp_profeval 
                INNER JOIN 
                    (SELECT id_matiere_matiere,code,libele AS matierelib FROM matiere)tmp_mat 
                ON tmp_profeval.id_mat = tmp_mat.id_matiere_matiere)tmp_final 
            INNER JOIN 
            (SELECT id_session_session,Libelle_session FROM annee_session)tmp_session 
            ON 
            tmp_final.eval_session = tmp_session.id_session_session )tmp_n_evalf
        INNER JOIN 
        (SELECT eval_id,eval_date,eval_hDebut,eval_hFin,coef FROM prof_eval_emploitps)tmp_n_tpsf
        ON tmp_n_evalf.prof_eval_id = tmp_n_tpsf.eval_id ';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);exit();
        return $result;   
    }
    public static function getProfGrpEvalWithDate(){
        $id_prof = intval($_SESSION['user']['id_type']);
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM  (SELECT * FROM(SELECT * FROM(SELECT id_groupe,prof_eval_id,id_mat,eval_libelle,eval_desc FROM prof_eval WHERE id_prof = '.$id_prof.' AND eval_etat = 1) tmp_evalprof INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp ON tmp_evalprof.id_groupe =tmp_grp.groupe_id)tmp_profeval INNER JOIN (SELECT id_matiere_matiere,code,libele AS matierelib FROM matiere)tmp_mat ON tmp_profeval.id_mat = tmp_mat.id_matiere_matiere  )tmp_profeval INNER JOIN (SELECT * FROM prof_eval_emploitps ORDER BY prof_eval_emploiTps_id ASC)tmp_evaltps ON tmp_profeval.prof_eval_id = tmp_evaltps.eval_id';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;   
    }
    public static function getProfGrpEvalWithDateBy($eval_id){
        $id_prof = intval($_SESSION['user']['id_type']);
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM (SELECT * FROM  (SELECT * FROM(SELECT * FROM(SELECT id_groupe,prof_eval_id,id_mat,eval_libelle,date_creation_eval,eval_desc, eval_session,eval_type FROM prof_eval WHERE id_prof = '.$id_prof.' AND prof_eval_id = '.$eval_id.' ) tmp_evalprof INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp ON tmp_evalprof.id_groupe =tmp_grp.groupe_id)tmp_profeval INNER JOIN (SELECT id_matiere_matiere,code,libele AS matierelib FROM matiere)tmp_mat ON tmp_profeval.id_mat = tmp_mat.id_matiere_matiere  )tmp_profeval INNER JOIN (SELECT * FROM prof_eval_emploitps ORDER BY prof_eval_emploiTps_id ASC)tmp_evaltps ON tmp_profeval.prof_eval_id = tmp_evaltps.eval_id) tmp_final INNER JOIN (SELECT id_session_session,Libelle_session FROM annee_session)tmp_session ON tmp_final.eval_session = tmp_session.id_session_session';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;   
    }
    public static function get_Eval_by($eval_id){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        //$sql='SELECT * FROM prof_eval,prof_eval_emploitps WHERE prof_eval.prof_eval_id = prof_eval_emploitps.eval_id AND prof_eval.prof_eval_id='.$eval_id;
        $sql=' SELECT * FROM (SELECT * FROM  (SELECT * FROM(SELECT * FROM(SELECT id_groupe,prof_eval_id,id_mat,eval_libelle,date_creation_eval,eval_desc, eval_session,eval_type,fk_idpartAneeScol FROM prof_eval WHERE  prof_eval_id = '.$eval_id.' ) tmp_evalprof INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe )tmp_grp ON tmp_evalprof.id_groupe =tmp_grp.groupe_id)tmp_profeval INNER JOIN (SELECT id_matiere_matiere,code,libele AS matierelib FROM matiere)tmp_mat ON tmp_profeval.id_mat = tmp_mat.id_matiere_matiere  )tmp_profeval INNER JOIN (SELECT * FROM prof_eval_emploitps ORDER BY prof_eval_emploiTps_id ASC)tmp_evaltps ON tmp_profeval.prof_eval_id = tmp_evaltps.eval_id) tmp_final INNER JOIN (SELECT id_session_session,Libelle_session FROM annee_session)tmp_session ON tmp_final.eval_session = tmp_session.id_session_session';
        
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);exit();
        return $result;   
    }

    public static function getEvalSalle($id_eval){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM(SELECT eval_salle_id FROM prof_eval_emploitps WHERE eval_id = '.$id_eval.')tmp_idsalle INNER JOIN (SELECT * FROM salle)tmp_salle ON tmp_idsalle.eval_salle_id = tmp_salle.id_salle';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }
    //ETAT ELEVE COPIE


    
    /**
     * Recupe eval notes
     * 
    */
    //inutilise
    //:::::::::::DEBUT MOYENNE SQL ::::::::::::::::
    
    public static function get_notes_prof_eval_grpBY($id_groupe , $id_mat , $id_session){
        $id_prof = intval($_SESSION['user']['id_type']);
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql='SELECT * FROM(SELECT * FROM(SELECT * FROM (SELECT * FROM(SELECT id_prof,id_mat,id_groupe,id_eleve_eleve,id_evaluation,eval_libelle,eval_session,note FROM(SELECT id_notes_notes,id_eleve_eleve,id_evaluation,note FROM notes)tmp_note INNER JOIN (SELECT id_prof,id_mat,prof_eval_id,eval_libelle,id_groupe,eval_session FROM prof_eval)tmp_eval ON tmp_note.id_evaluation = tmp_eval.prof_eval_id)tmp_a1 WHERE id_groupe ='.$id_groupe.') tmp_note_1 INNER JOIN (SELECT * FROM(SELECT tmp_elevgrp.nom_prenom ,tmp_elevgrp.sexe,tmp_elevgrp.email ,tmp_elevgrp.contact,tmp_elevgrp.matricule,tmp_elevgrp.id_eleve_eleve AS id_eleve,tmp_elevgrp.elv_ds_grpe_groupe, tmp_elevgrp.elv_ds_grpe_etat FROM (SELECT * FROM (SELECT pers.id_pers_personne,pers.nom_prenom,pers.sexe,pers.email,pers.contact,elev.matricule,elev.id_eleve_eleve FROM (SELECT * FROM personne WHERE etat_pers = 1 AND type_pers = 1)pers INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1) elev ON pers.id_type = elev.id_eleve_eleve)infoelev INNER JOIN (SELECT * FROM eleve_estds_groupe WHERE elv_ds_grpe_groupe = '.$id_groupe.')elevgrpbyid ON infoelev.id_eleve_eleve = elevgrpbyid.elv_ds_grpe_idelev)tmp_elevgrp)tmp_el_1 WHERE tmp_el_1.elv_ds_grpe_groupe = '.$id_groupe.')tmp_elevf ON tmp_note_1.id_eleve_eleve = tmp_elevf.id_eleve)tmp_ok_final WHERE id_prof = '.$id_prof.' AND id_mat = '.$id_mat.' AND id_groupe = '.$id_groupe.' AND eval_session = '.$id_session.' ORDER BY tmp_ok_final.id_eleve ASC) tmp_ok_final INNER JOIN (SELECT eval_id,coef,notation FROM prof_eval_emploitps)tmp_matcoef ON tmp_ok_final.id_evaluation = tmp_matcoef.eval_id ORDER BY tmp_ok_final.id_eleve ASC' ;
        
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);exit();
        return $result;   
    }
    public static function get_grpe_notesBy($id_groupe , $id_mat , $id_session, $id_prof, $id_periode){
        $db = static::getDB();
        
        $sql='SELECT * FROM
        (SELECT * FROM
            (SELECT * FROM 
                (SELECT * FROM
                    (SELECT id_prof,id_mat,id_groupe,id_eleve_eleve,id_evaluation,eval_libelle,eval_session,note FROM
                        (SELECT id_notes_notes,id_eleve_eleve,id_evaluation,note FROM notes)tmp_note 
                    INNER JOIN  
                        (SELECT id_prof,id_mat,prof_eval_id,eval_libelle,id_groupe,eval_session FROM prof_eval WHERE fk_idpartAneeScol='.$id_periode.')tmp_eval 
                    ON tmp_note.id_evaluation = tmp_eval.prof_eval_id)tmp_a1 
                    WHERE id_groupe ='.$id_groupe.') tmp_note_1 
                INNER JOIN 
                    (SELECT * FROM
                        (SELECT tmp_elevgrp.nom_prenom ,tmp_elevgrp.sexe,tmp_elevgrp.email ,tmp_elevgrp.contact,tmp_elevgrp.matricule,tmp_elevgrp.id_eleve_eleve AS id_eleve,tmp_elevgrp.elv_ds_grpe_groupe, tmp_elevgrp.elv_ds_grpe_etat FROM 
                            (SELECT * FROM 
                                (SELECT pers.id_pers_personne,pers.nom_prenom,pers.sexe,pers.email,pers.contact,elev.matricule,elev.id_eleve_eleve FROM 
                                    (SELECT * FROM personne WHERE etat_pers = 1 AND type_pers = 1)pers 
                                INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1) elev 
                                ON pers.id_type = elev.id_eleve_eleve)infoelev 
                            INNER JOIN (SELECT * FROM eleve_estds_groupe WHERE elv_ds_grpe_groupe = '.$id_groupe.')elevgrpbyid 
                            ON infoelev.id_eleve_eleve = elevgrpbyid.elv_ds_grpe_idelev)tmp_elevgrp
                        )tmp_el_1 
                    WHERE tmp_el_1.elv_ds_grpe_groupe = '.$id_groupe.')tmp_elevf 
                ON tmp_note_1.id_eleve_eleve = tmp_elevf.id_eleve)tmp_ok_final 
                WHERE id_prof = '.$id_prof.' AND id_mat = '.$id_mat.' AND id_groupe = '.$id_groupe.' AND eval_session = '.$id_session.' ORDER BY tmp_ok_final.id_eleve ASC) tmp_ok_final 
            INNER JOIN 
            (SELECT eval_id,coef,notation FROM prof_eval_emploitps)tmp_matcoef 
            ON tmp_ok_final.id_evaluation = tmp_matcoef.eval_id ORDER BY tmp_ok_final.id_eleve ASC' ;
    
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);exit();
        return $result;   
    }
    public static function getAll_elvDSgrp($id_groupe){
        $id_prof = intval($_SESSION['user']['id_type']);
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' 
            SELECT id_pers_personne,nom_prenom ,date_naiss, lieu_naiss, sexe,email ,contact,matricule,id_eleve_eleve,elv_ds_grpe_groupe, elv_ds_grpe_etat,nationnalite,statut_affecter ,satut_brourse FROM (
                SELECT * FROM (
                    SELECT pers.id_pers_personne,pers.nom_prenom,pers.date_naiss, pers.lieu_naiss,pers.sexe,pers.email,pers.contact,elev.matricule,elev.id_eleve_eleve ,elev.nationnalite,elev.statut_affecter ,elev.satut_brourse 
                    FROM ( SELECT * FROM personne WHERE etat_pers = 1 AND type_pers = 1)pers 
                    INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1) elev 
                    ON pers.id_type = elev.id_eleve_eleve)infoelev 
                INNER JOIN (SELECT * FROM eleve_estds_groupe WHERE elv_ds_grpe_groupe = '.$id_groupe.')elevgrpbyid 
                ON infoelev.id_eleve_eleve = elevgrpbyid.elv_ds_grpe_idelev)tmp_elevgrp 
            ORDER BY tmp_elevgrp.nom_prenom ASC' ;
        
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);exit();
        return $result;   
    }
    
    public static function getAll_grpEvalBymat($id_groupe, $îd_mat, $id_session){
        $id_prof = intval($_SESSION['user']['id_type']);
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql='SELECT * FROM (SELECT * FROM(SELECT * FROM (SELECT * FROM(SELECT* FROM(SELECT eval_id,id_prof, id_groupe, id_mat, eval_libelle,eval_session,coef, notation, eval_date FROM ( SELECT prof_eval_id, id_prof, id_groupe, id_mat, eval_libelle,eval_session FROM prof_eval) tmp_profeval INNER JOIN ( SELECT eval_id, coef, notation, eval_date FROM prof_eval_emploitps)tmp_evalprog ON tmp_profeval.prof_eval_id = tmp_evalprog.eval_id) tmp_eval_m_p WHERE id_prof = '. $id_prof .' AND id_groupe = '. $id_groupe .' AND id_mat = '. $îd_mat .')tmp_x1 INNER JOIN (SELECT id_matiere_matiere,libele AS lib_mat,code AS code_mat FROM matiere)tmp_xmat ON tmp_x1.id_mat = tmp_xmat.id_matiere_matiere)tmp_x3 INNER JOIN (SELECT groupe_id AS idgrp ,groupe_libelle FROM groupe) tmp_xgrp ON tmp_x3.id_groupe = tmp_xgrp.idgrp) tmp_xfil INNER JOIN (SELECT id_session_session,Libelle_session FROM annee_session)tmp_xsess ON  tmp_xfil.eval_session =tmp_xsess.id_session_session) tmp_ffinal WHERE eval_session ='. $id_session  ;
        
        //print_r($sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);exit();
        return $result;   
    }

    public static function get_grpEvalBy($id_prof,$id_groupe, $îd_mat, $id_session, $id_periode){
        $db = static::getDB();
        
        $sql='SELECT * FROM 
        (SELECT * FROM
            (SELECT * FROM 
                (SELECT * FROM
                    (SELECT * FROM
                        (SELECT eval_id,id_prof, id_groupe, id_mat, eval_libelle,eval_session,coef, notation, eval_date,fk_idpartAneeScol FROM 
                            ( SELECT prof_eval_id, id_prof, id_groupe, id_mat, eval_libelle,eval_session,fk_idpartAneeScol FROM prof_eval WHERE prof_eval.fk_idpartAneeScol = '. $id_periode .') tmp_profeval 
                        INNER JOIN ( SELECT eval_id, coef, notation, eval_date FROM prof_eval_emploitps)tmp_evalprog 
                        ON tmp_profeval.prof_eval_id = tmp_evalprog.eval_id) tmp_eval_m_p 
                    WHERE id_prof = '. $id_prof .' AND id_groupe = '. $id_groupe .' AND id_mat = '. $îd_mat .')tmp_x1 
                INNER JOIN (SELECT id_matiere_matiere,libele AS lib_mat,code AS code_mat FROM matiere)tmp_xmat 
                ON tmp_x1.id_mat = tmp_xmat.id_matiere_matiere)tmp_x3 
            INNER JOIN (SELECT groupe_id AS idgrp ,groupe_libelle FROM groupe) tmp_xgrp 
            ON tmp_x3.id_groupe = tmp_xgrp.idgrp) tmp_xfil 
        INNER JOIN (SELECT id_session_session,Libelle_session FROM annee_session)tmp_xsess 
        ON  tmp_xfil.eval_session =tmp_xsess.id_session_session) tmp_ffinal WHERE eval_session ='. $id_session  ;
        //print_r($sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);exit();
        return $result;   
    }

    public static function get_mat_anneePart($id_groupe, $îd_mat){
        $db = static::getDB();
        
        $sql='SELECT groupe_matiere_coef.part_annee_id_tmp,annee_partie.libele_partie FROM groupe_matiere_coef,annee_partie WHERE groupe_matiere_coef.groupe_id_tmp='.$id_groupe.' AND groupe_matiere_coef.matiere_id_tmp='.$îd_mat.' AND annee_partie.id_annee_partie=groupe_matiere_coef.part_annee_id_tmp'  ;
        //print_r($sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);exit();
        return $result;   
    }
    public static function get_mat_grp_name($id_groupe, $îd_mat){
        $db = static::getDB();
        
        $sql='SELECT groupe.groupe_libelle,matiere.code,matiere.libele AS lib_mat FROM groupe,matiere WHERE groupe.groupe_id='.$id_groupe.' AND matiere.id_matiere_matiere='.$îd_mat;
        //print_r($sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);exit();
        return $result;   
    }
    //:::::::::::DEBUT MOYENNE  SQL ::::::::::::::::
    // FIN ::::::::: fRecupe eval notes :::::::::::::::::


    public static function getAllEleveByGroup($id_eval){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM(SELECT * FROM(SELECT * FROM(SELECT id_pers_personne,id_type,nom_prenom,sexe,email,contact FROM personne WHERE type_pers = 1 AND etat_pers = 1)tmp_elev INNER JOIN (SELECT elv_ds_grpe_idelev,elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_etat =1)tmp_grpele ON tmp_elev.id_type = tmp_grpele.elv_ds_grpe_idelev)tmp_a INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1)tmp_b ON tmp_a.id_type = tmp_b.id_eleve_eleve)tmp_aa INNER JOIN (SELECT id_groupe,groupe_libelle FROM(SELECT * FROM groupe)tmp_grp INNER JOIN (SELECT id_groupe FROM prof_eval WHERE  prof_eval_id = '.$id_eval.')tmp_grpeval ON tmp_grp.groupe_id = tmp_grpeval.id_groupe) tmp_bb on tmp_aa.elv_ds_grpe_groupe = tmp_bb.id_groupe';
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

        $sql=' SELECT * FROM(
                SELECT * FROM(
                    SELECT * FROM(
                        SELECT * FROM(
                            SELECT id_pers_personne,id_type,nom_prenom,sexe,email,contact FROM personne WHERE type_pers = 1 AND etat_pers = 1)tmp_elev 
                            INNER JOIN 
                            (SELECT elv_ds_grpe_idelev,elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_etat =1)tmp_grpele 
                            ON tmp_elev.id_type = tmp_grpele.elv_ds_grpe_idelev)tmp_a 
                        INNER JOIN 
                        (SELECT * FROM eleve WHERE eleve_etat = 1 AND id_eleve_eleve = '.$id_eleve.')tmp_b 
                        ON tmp_a.id_type = tmp_b.id_eleve_eleve)tmp_aa 
                    INNER JOIN 
                        (SELECT id_groupe,groupe_libelle,prof_eval_id FROM(SELECT * FROM groupe)tmp_grp 
                        INNER JOIN 
                        (SELECT id_groupe,prof_eval_id FROM prof_eval WHERE  prof_eval_id = '.$id_eval.')tmp_grpeval 
                        ON tmp_grp.groupe_id = tmp_grpeval.id_groupe) tmp_bb 
                    ON tmp_aa.elv_ds_grpe_groupe = tmp_bb.id_groupe)tmp_finaltab 
                INNER JOIN 
                (SELECT * FROM notes WHERE id_eleve_eleve = '.$id_eleve.' )tmp_notes 
                ON tmp_finaltab.prof_eval_id = tmp_notes.id_evaluation';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;        
    }

    public static function setProfEval_elevnote($id_eleve_eleve, $eval_id, $fk_ipers, $note_val){
        $db = static::getDB();
        $etatsql = 0;
        //id_notes_notes 	id_eleve_eleve 	id_evaluation 	note 
        $sql=' SELECT * FROM notes WHERE id_eleve_eleve = '.$id_eleve_eleve.' AND id_evaluation = '.$eval_id.' LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result,$id_eleve, $id_eval);exit;
        if ( empty($result) || $result==0) {
            return 'majnote_vide';          
        }
        else {
            $sql=' UPDATE notes SET note = '.$note_val.',fk_ipers = '.$fk_ipers.', etat_note = 1 WHERE id_eleve_eleve ='.$id_eleve_eleve.' AND id_evaluation = '.$eval_id.' ;';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            if ( $result == TRUE) { return 'majnote_ok';} else {return 'majnote_erreur';   }

        }

      
    }

    public static function getProfEmploiTpsBy($id_prof){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM(SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM(SELECT * FROM (SELECT * FROM groupe_emploitps WHERE emploitps_etat = 1)tmp_tps INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp ON tmp_tps.emploitps_groupe_id = tmp_grp.groupe_id)tmp_1 INNER JOIN (SELECT id_matiere_matiere,code,libele as lib_matiere FROM matiere)tmp_mat on tmp_1.emploitps_id_mat = tmp_mat.id_matiere_matiere)tmp_2 INNER JOIN (SELECT id_salle, libelle as lib_salle,Code_salle FROM salle )tmp_salle on tmp_2.emploitps_salle = tmp_salle.id_salle)tmp_4 INNER JOIN (SELECT nom_prenom,id_type FROM personne WHERE type_pers = 2)tmp_prof on tmp_4.emploitps_id_prof = tmp_prof.id_type)tmp_5 INNER JOIN (SELECT id_annee_partie,libele_partie FROM annee_partie)tmp_partanne on tmp_5.emploitps_periode =tmp_partanne.id_annee_partie)tmp_tps WHERE emploitps_id_prof =  '.$id_prof;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;       
    }

    public static function getProfGrpEvalBy($id_prof){
        //var_dump("id_prof",$id_prof);//exit();
        $db = static::getDB();
        
        $sql=' SELECT * FROM (SELECT * FROM(SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM prof_eval WHERE eval_etat= 2 )tmp_eval INNER JOIN (SELECT * FROM prof_eval_emploitps )tmp_evaltps ON tmp_eval.prof_eval_id = tmp_evaltps.eval_id)tmp_1 INNER JOIN (SELECT id_salle,libelle FROM salle)tmp_salle ON tmp_1.eval_salle_id = tmp_salle.id_salle)tmp_2 INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe)tmp_grp ON tmp_2.id_groupe = tmp_grp.groupe_id)tmp_3 INNER JOIN (SELECT id_type,nom_prenom FROM personne WHERE type_pers = 2)tmp_pers on tmp_3.id_prof = tmp_pers.id_type) tmp_final WHERE id_prof = '.$id_prof;
        //var_dump($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump("result",$result);//exit();
        return $result;       
    }

    public static function get_anneepartie_ByGrpe($id_groupe){
        //var_dump("id_prof",$id_prof);//exit();
        $db = static::getDB();
        
        $sql='SELECT annee_partie.libele_partie,annee_partie.id_annee_partie FROM 
        (SELECT groupe.groupe_annee FROM groupe WHERE groupe.groupe_id='.$id_groupe.')tmp_grp,
        annee_partie WHERE tmp_grp.groupe_annee=annee_partie.id_anneeScolaire';
        //var_dump($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty( $result)) {
            $result =0;
        }
        //var_dump("result",$result);//exit();
        return $result;       
    }
    public static function getSession(){
        $db = static::getDB();

        //Prof::getAllBDtable();

        $sql=' SELECT * FROM annee_session WHERE etat_session = 1 ';
        
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);//exit();
        return $result;       
    }

    //Prof::getAllBDtable();
    public static function getAllBDtable(){
        $db = static::getDB();

        $query = $db->prepare('show tables');
        $query->execute();
        
        while($rows = $query->fetch(PDO::FETCH_ASSOC)){
            var_dump($rows);echo "</br>";
            foreach ($rows as $key => $value) {
                //var_dump("key :",$key, "value : ",$value);
                $querys = $db->prepare('SHOW COLUMNS from '.$value);
                $querys->execute();
                while($cols = $querys->fetch(PDO::FETCH_ASSOC)){
                    echo "</br>";var_dump($cols);echo "</br>";
                }
                echo "</br>**************************************</br>";
            }
            //$querys = $db->prepare('SHOW COLUMNS from '.$rows[0]);
            //$querys->execute();
            //echo "</br></br></br>";
        }
        exit();
        
     
    }

    
    
        
    //INSERT INTO `prof_matiere` (`id_prof`, `id_mat`, `etat_prof_mat`) VALUES ('1', '1', '1'); 
    //MOYENNE SET
    public static function setEleve_annee_moyBY($moy_id_groupe, $moy_id_eleve_eleve , $moy_id_matiere_matiere, $moy_id_prof, $moy_id_session_session, $moy_moyenne, $fk_part_annee){
        $db = static::getDB();
        //id_groupe 	id_eleve 	id_matiere 	id_prof 	id_session 	moyenne 	etat_moy 0-active admin//1-activer eleve// 2-desactiver 
        $sql=' SELECT * FROM moyenne WHERE id_groupe= "'.$moy_id_groupe.'" AND id_eleve= "'.$moy_id_eleve_eleve.'" AND id_matiere= "'.$moy_id_matiere_matiere.'" AND id_prof= "'.$moy_id_prof.'" AND id_session= "'.$moy_id_session_session.'"  AND fk_part_annee= "'.$fk_part_annee.'" LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {

            $data = [
                'id_groupe' => $moy_id_groupe,
                'id_eleve' => $moy_id_eleve_eleve,
                'id_matiere' => $moy_id_matiere_matiere,
                'id_prof' => $moy_id_prof,
                'id_session' => $moy_id_session_session,
                'moyenne' => $moy_moyenne,
                'fk_part_annee' => $fk_part_annee,
                
            ];
            //var_dump($db);
                    
            $sql=' INSERT INTO moyenne (id_groupe, id_eleve , id_matiere, id_prof, id_session , moyenne , fk_part_annee) VALUES ( :id_groupe, :id_eleve , :id_matiere, :id_prof , :id_session, :moyenne , :fk_part_annee);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ( $result == TRUE) { return 'moy_ajouter';} else {return 'moy_erreur';   }
        }
        else {          

            $sql=' UPDATE moyenne SET moyenne = '.$moy_moyenne.' WHERE id_groupe= "'.$moy_id_groupe.'" AND id_eleve= "'.$moy_id_eleve_eleve.'" AND id_matiere= "'.$moy_id_matiere_matiere.'" AND id_prof= "'.$moy_id_prof.'" AND id_session= "'.$moy_id_session_session.'" AND fk_part_annee= "'.$fk_part_annee.'";';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            if ( $result == TRUE) { return 'moy_update';} else {return 'moy_update';   }
        }


       
    }

    
    public static function getetat_envoimoy($etatmoy_grp, $etatmoy_mat, $etatmoy_prof, $etatmoy_sess){

        $db = static::getDB();
        $sql='SELECT MAX(etat_moy) AS etat_moy FROM moyenne WHERE id_groupe = '.$etatmoy_grp.' AND id_matiere = '.$etatmoy_mat.' and id_prof = '.$etatmoy_prof.' AND id_session = '.$etatmoy_sess.' GROUP BY id_groupe';
        //print_r($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result[0]['etat_moy']);exit;
        if ( empty($result) ) {

            return 2;
        }
        else {       
            
            return  intval($result[0]['etat_moy']);

        }

        
     
    }
    public static function get_moy_etat_envoi($etatmoy_grp, $etatmoy_mat, $etatmoy_prof, $etatmoy_sess, $etatmoy_periode){

        $db = static::getDB();
        $sql='SELECT MAX(etat_moy) AS etat_moy FROM moyenne WHERE id_groupe = '.$etatmoy_grp.' AND id_matiere = '.$etatmoy_mat.' and id_prof = '.$etatmoy_prof.' AND id_session = '.$etatmoy_sess.' AND fk_part_annee = '.$etatmoy_periode.' GROUP BY id_groupe';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ( empty($result) ) {  return 2;  }
        else { return  intval($result[0]['etat_moy']);  }
    }  
    /** TABLE : absences
    * id_absences 	id_eleve 	id_matiere 	id_groupe 	abs_debut 	abs_fin 	abs_date 	abs_motifs 	etat_abs 
    */

    public static function set_absence_eleves($id_eleve , $id_matiere ,$id_groupe ,$abs_debut  ,$abs_fin  ,$abs_date,$abs_motifs){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql=' SELECT * FROM absences WHERE id_eleve= "'.$id_eleve.'" AND id_matiere= "'.$id_matiere.'" AND id_groupe= "'.$id_groupe.'"  AND abs_debut= "'.$abs_debut.'" AND abs_fin= "'.$abs_fin.'" AND abs_date= "'.$abs_date.'" LIMIT 1';
        //var_dump($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {

            $data = [
                'id_eleve' => $id_eleve,
                'id_matiere' => $id_matiere,
                'id_groupe' => $id_groupe,
                'abs_debut' => $abs_debut,
                'abs_fin' => $abs_fin,
                'abs_date' => $abs_date,
                'abs_motifs' => $abs_motifs
            ];
            //var_dump($db);
                    
            $sql=' INSERT INTO absences (id_eleve, id_matiere, id_groupe, abs_debut, abs_fin, abs_date, abs_motifs) VALUES ( :id_eleve, :id_matiere, :id_groupe, :abs_debut, :abs_fin, :abs_date, :abs_motifs);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ( $result == TRUE) {return 1;} 
            else {return -1;   }

        }
        else {  return 0;}


       
    }

    public static function set_presence_eleves($id_eleve , $id_matiere ,$id_groupe ,$abs_debut  ,$abs_fin  ,$abs_date,$abs_motifs){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        $sql=' SELECT * FROM absences WHERE id_eleve= "'.$id_eleve.'" AND id_matiere= "'.$id_matiere.'" AND id_groupe= "'.$id_groupe.'"  AND abs_debut= "'.$abs_debut.'" AND abs_fin= "'.$abs_fin.'" AND abs_date= "'.$abs_date.'" LIMIT 1';
        //var_dump($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);
        //exit();
        if ( empty($result) || $result==0 ) { return 0; }
        else {             
            
            //var_dump($db);
            $sql=' DELETE FROM absences WHERE id_eleve= "'.$id_eleve.'" AND id_matiere= "'.$id_matiere.'" AND id_groupe= "'.$id_groupe.'"  AND abs_debut= "'.$abs_debut.'" AND abs_fin= "'.$abs_fin.'" AND abs_date= "'.$abs_date.'";';
            $result = $db->exec($sql);
            //var_dump($lastid);
            if ( !empty($result) || $result!=0) {return 1;} 
            else {return -1;   }




        }
       
    }


    public static function del_evalBy($id_eval ){
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        $sql=' SELECT * FROM prof_eval WHERE prof_eval_id= '.$id_eval.'  LIMIT 1;';
        //var_dump($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);
        //exit();
        if ( empty($result) || $result==0 ) { return 0; }
        else {    

            $sql=' DELETE FROM prof_eval WHERE prof_eval_id= '.$id_eval.';';
            $result = $db->exec($sql);
            //var_dump($lastid);
            if ( !empty($result) || $result!=0) {
                $sql=' SELECT * FROM prof_eval_emploitps WHERE eval_id= '.$id_eval.'  LIMIT 1;';
                $stmt = $db->query($sql);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ( empty($result) || $result==0 ) { return 0; }
                else {  
                    $sql=' DELETE FROM prof_eval_emploitps WHERE eval_id= '.$id_eval.';';
                    $result = $db->exec($sql);
                    if ( !empty($result) || $result!=0) { return 1; } 
                    else {return -1;   }
                }
                
            } 
            else {return -1;   }
        }
    }

    
}
