<?php

namespace App\Models\Elearning;

use PDO;

date_default_timezone_set("Africa/Abidjan");
/**
 * Example user model
 *
 * PHP version 7.0
 */
class Eleve_elearn extends \Core\Model
{
    public static function getUnivInfos()
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM infosuniv';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
 
    public static function get_eleves_grpEmploiTps($id_grp_eleve)
    {

        $db = static::getDB();

        $sql = 'SELECT * FROM 
        (SELECT * FROM 
            (SELECT * FROM 
                (SELECT * FROM 
                    (SELECT * FROM groupe_emploitps WHERE emploitps_etat = 1 AND emploitps_groupe_id=' . $id_grp_eleve . ')tmp_tps 
                    INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp 
                    ON tmp_tps.emploitps_groupe_id = tmp_grp.groupe_id)tmp_1 
                INNER JOIN (SELECT id_matiere_matiere,code,libele as lib_matiere FROM matiere)tmp_mat 
                ON tmp_1.emploitps_id_mat = tmp_mat.id_matiere_matiere)tmp_2 
            INNER JOIN (SELECT id_salle, libelle as lib_salle,Code_salle FROM salle )tmp_salle 
            on tmp_2.emploitps_salle = tmp_salle.id_salle)tmp_empl_tps_ok 
        INNER JOIN (SELECT id_annee_partie,libele_partie FROM annee_partie)tmp_partanne 
        ON tmp_empl_tps_ok.emploitps_periode =tmp_partanne.id_annee_partie  
        WHERE emploitps_date>=NOW()-1
        ORDER BY tmp_empl_tps_ok.emploitps_date ASC LIMIT 6';

        //print_r($sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_Liste_groupe_ProfMat($groupe_id)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = 'SELECT prof_classe.* ,prof_infos.* ,mat_infos.mat_code,mat_infos.matiere_lib ,tmp_gp.* FROM (SELECT * FROM prof_classe WHERE id_groupe = ' . $groupe_id . ' AND etat_prof_classe = 1)prof_classe, (SELECT id_type, email AS prof_email, nom_prenom AS prof_nom ,contact AS prof_contact ,id_pers_personne AS prof_idpers FROM personne WHERE type_pers = 2)prof_infos, (SELECT code AS mat_code, libele AS matiere_lib, id_matiere_matiere FROM matiere WHERE id_matiere_matiere <> 0)mat_infos, (SELECT groupe.* , annee_scolaire.annee_libelle , classe.libelle AS lib_filliere FROM groupe, annee_scolaire,classe WHERE groupe_id = ' . $groupe_id . ' AND groupe.groupe_annee = annee_scolaire.id_anscol_annee_scolaire AND classe.id_classe_classe = groupe.groupe_classe)tmp_gp WHERE prof_classe.id_prof = prof_infos.id_type AND prof_classe.id_mat = mat_infos.id_matiere_matiere AND tmp_gp.groupe_id = prof_classe.id_groupe';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_grpe_matProf_allinfos($groupe_id)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        // RETIRER DE LIGNE 76 : (SELECT * FROM groupe_matiere_coef WHERE groupe_id_tmp = ' . $groupe_id . ' AND matiere_id_tmp NOT IN ( SELECT matiere_parent_id_tmp FROM groupe_matiere_coef WHERE groupe_id_tmp =	' . $groupe_id . ' AND matiere_parent_id_tmp <> 0))tmp_grp_matcoef

        $sql = 'SELECT * FROM 
            (SELECT * FROM groupe_matiere_coef WHERE groupe_id_tmp = ' . $groupe_id . ' )tmp_grp_matcoef
            INNER JOIN 
            (SELECT prof_classe.* ,prof_infos.* ,mat_infos.mat_code,mat_infos.matiere_lib ,tmp_gp.* FROM 
                (SELECT * FROM prof_classe WHERE id_groupe = ' . $groupe_id . ' AND etat_prof_classe = 1)prof_classe, (SELECT id_type, email AS prof_email, nom_prenom AS prof_nom ,contact AS prof_contact ,id_pers_personne AS prof_idpers FROM personne WHERE type_pers = 2)prof_infos, 
                (SELECT code AS mat_code, libele AS matiere_lib, id_matiere_matiere FROM matiere WHERE id_matiere_matiere <> 0)mat_infos, 
                (SELECT groupe.* , annee_scolaire.annee_libelle , classe.libelle AS lib_filliere FROM groupe, annee_scolaire,classe WHERE groupe_id = ' . $groupe_id . ' AND groupe.groupe_annee = annee_scolaire.id_anscol_annee_scolaire AND classe.id_classe_classe = groupe.groupe_classe)tmp_gp 
                WHERE prof_classe.id_prof = prof_infos.id_type AND prof_classe.id_mat = mat_infos.id_matiere_matiere AND tmp_gp.groupe_id = prof_classe.id_groupe)tmp_grp_profmat
            ON tmp_grp_matcoef.matiere_id_tmp = tmp_grp_profmat.id_mat AND tmp_grp_matcoef.groupe_id_tmp = tmp_grp_profmat.id_groupe GROUP BY tmp_grp_profmat.id_mat';
        //print_r($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }



    public static function getEleve_grpeMatParent($id_eleve)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = 'SELECT * FROM ( SELECT * FROM (SELECT tmp_matcoef.*,tmp_mat.mat_code ,tmp_mat.mat_lib FROM ( SELECT * FROM groupe_matiere_coef WHERE matiere_parent_id_tmp = 0)tmp_matcoef INNER JOIN (SELECT id_matiere_matiere, CODE AS mat_code ,libele AS mat_lib FROM matiere WHERE id_matiere_matiere ) tmp_mat ON tmp_matcoef.matiere_id_tmp = tmp_mat.id_matiere_matiere)tmp_matparent WHERE groupe_id_tmp IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = ' . $id_eleve . ' AND elv_ds_grpe_etat = 1) )tmp_ff1 
        INNER JOIN (SELECT prof_classe.*, tmp_prof.nom_prenom,tmp_prof.contact ,tmp_grp.groupe_libelle FROM prof_classe, (SELECT id_type, nom_prenom , contact FROM personne WHERE type_pers = 2) tmp_prof, (SELECT groupe_id,groupe_libelle FROM groupe)tmp_grp WHERE prof_classe.id_prof = tmp_prof.id_type AND prof_classe.id_groupe = tmp_grp.groupe_id)tmp_grp_infos 
        ON  tmp_ff1.groupe_id_tmp = tmp_grp_infos.id_groupe AND tmp_ff1.matiere_id_tmp = tmp_grp_infos.id_mat';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function getEleve_grpeMatFils($id_eleve, $id_matparent)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = 'SELECT * FROM (SELECT * FROM (SELECT tmp_matcoef.*,tmp_mat.mat_code ,tmp_mat.mat_lib FROM ( SELECT * FROM groupe_matiere_coef WHERE matiere_parent_id_tmp <> 0)tmp_matcoef INNER JOIN (SELECT id_matiere_matiere, CODE AS mat_code ,libele AS mat_lib FROM matiere WHERE id_matiere_matiere ) tmp_mat ON tmp_matcoef.matiere_id_tmp = tmp_mat.id_matiere_matiere)tmp_matparent WHERE groupe_id_tmp IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = ' . $id_eleve . ' AND elv_ds_grpe_etat = 1 AND tmp_matparent.matiere_parent_id_tmp = ' . $id_matparent . ' ) )tmp_ff1 
        INNER JOIN (SELECT prof_classe.*, tmp_prof.nom_prenom,tmp_prof.contact ,tmp_grp.groupe_libelle FROM prof_classe, (SELECT id_type, nom_prenom , contact FROM personne WHERE type_pers = 2) tmp_prof, (SELECT groupe_id,groupe_libelle FROM groupe)tmp_grp WHERE prof_classe.id_prof = tmp_prof.id_type AND prof_classe.id_groupe = tmp_grp.groupe_id)tmp_grp_infos 
        ON  tmp_ff1.groupe_id_tmp = tmp_grp_infos.id_groupe AND tmp_ff1.matiere_id_tmp = tmp_grp_infos.id_mat';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getEleve_profname($id_prof)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = 'SELECT nom_prenom,contact FROM personne WHERE type_pers = 2 AND id_type = ' . $id_prof;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    /************************ */

    /**
     * Get all the users as an associative array
     *
     * @return array
     */
    //Note Eleve

    public static function get_eleves_grpEval($id_grp_eleve)
    {

        $db = static::getDB();

        $sql = 'SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM prof_eval WHERE eval_etat= 2 )tmp_eval INNER JOIN (SELECT * FROM prof_eval_emploitps )tmp_evaltps ON tmp_eval.prof_eval_id = tmp_evaltps.eval_id)tmp_1 INNER JOIN (SELECT id_salle,libelle FROM salle)tmp_salle ON tmp_1.eval_salle_id = tmp_salle.id_salle)tmp_2 INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe)tmp_grp ON tmp_2.id_groupe = tmp_grp.groupe_id)tmp_3 INNER JOIN (SELECT id_type,nom_prenom FROM personne WHERE type_pers = 2)tmp_pers ON tmp_3.id_prof = tmp_pers.id_type)tmp_eval_progok WHERE tmp_eval_progok.id_groupe=' . $id_grp_eleve;

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
        $sql = '
            SELECT 
                *,
                emploitps_date AS DateCours,
                emploitps_h_debut AS HeureDebut,
                emploitps_h_debut AS HeureFin, 
                lib_salle AS SalleCours, 
                lib_matiere AS MatiereCours,
                nom_prenom AS ProfCours 
            FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM(SELECT * FROM (SELECT * FROM groupe_emploitps)tmp_tps INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp ON tmp_tps.emploitps_groupe_id = tmp_grp.groupe_id)tmp_1 INNER JOIN (SELECT id_matiere_matiere,code,libele as lib_matiere FROM  matiere)tmp_mat on tmp_1.emploitps_id_mat = tmp_mat.id_matiere_matiere)tmp_2 INNER JOIN (SELECT id_salle, libelle as lib_salle,Code_salle FROM salle )tmp_salle on tmp_2.emploitps_salle = tmp_salle.id_salle)tmp_4 INNER JOIN (SELECT nom_prenom,id_type FROM personne WHERE type_pers = 2)tmp_prof on tmp_4.emploitps_id_prof = tmp_prof.id_type)tmp_5 INNER JOIN (SELECT  id_annee_partie,libele_partie FROM annee_partie)tmp_partanne on tmp_5.emploitps_periode =tmp_partanne.id_annee_partie)tmp_f WHERE groupe_id IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_etat = 1 AND elv_ds_grpe_idelev =' . $id_eleve . ')';
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
        $sql = '
            SELECT 
                * 
            FROM(SELECT * FROM(SELECT * FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM prof_eval WHERE eval_etat=2)tmp_eval INNER JOIN (SELECT * FROM prof_eval_emploitps )tmp_evaltps ON tmp_eval.prof_eval_id = tmp_evaltps.eval_id)tmp_1 INNER JOIN (SELECT id_salle,libelle FROM salle)tmp_salle ON tmp_1.eval_salle_id = tmp_salle.id_salle)tmp_2 INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe)tmp_grp ON tmp_2.id_groupe = tmp_grp.groupe_id)tmp_3 INNER JOIN (SELECT id_type,nom_prenom FROM personne WHERE type_pers = 2)tmp_pers on tmp_3.id_prof = tmp_pers.id_type) tmp_f WHERE id_groupe  IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_etat = 1 AND elv_ds_grpe_idelev =' . $id_eleve . ')';
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
        $cree_anne_scol_part = (int)(htmlspecialchars($_POST["cree_anne_scol_part"]));
        /* $cree_anne_scol_Part1 = htmlspecialchars($_POST["cree_anne_scol_Part1"]);
        $cree_anne_scol_Part1_dateDebut = htmlspecialchars($_POST["cree_anne_scol_Part1_dateDebut"]);
        $cree_anne_scol_Part1_dateDebut = htmlspecialchars($_POST["cree_anne_scol_Part2_dateFin"]);*/



        $sql = ' SELECT * FROM annee_scolaire WHERE annee_libelle= "' . $cree_anne_scol . '" AND etat_annee = 1 LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {

            $data = [
                'annee_libelle' => $cree_anne_scol,
                'annee_date_debut' => $cree_anne_scol_dateDebut,
                'annee_date_fin' => $cree_anne_scol_dateFin
            ];
            //var_dump($db);

            $sql = ' INSERT INTO annee_scolaire (annee_libelle, annee_date_debut, annee_date_fin) VALUES ( :annee_libelle, :annee_date_debut, :annee_date_fin);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {

                for ($i = 0; $i < $cree_anne_scol_part; $i++) {
                    $data = [
                        'libele_partie' => htmlspecialchars($_POST["cree_anne_scol_Part" . ($i + 1)]),
                        'partie_dateDebut' => htmlspecialchars($_POST["cree_anne_scol_Part" . ($i + 1) . "_dateDebut"]),
                        'partie_dateFin' =>  htmlspecialchars($_POST["cree_anne_scol_Part" . ($i + 1) . "_dateFin"]),
                        'id_anneeScolaire' => $lastid
                    ];
                    $sql = ' INSERT INTO annee_partie (libele_partie, partie_dateDebut, partie_dateFin, id_anneeScolaire) VALUES ( :libele_partie, :partie_dateDebut, :partie_dateFin, :id_anneeScolaire);';
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute($data);
                }
                unset($_POST);
                return 1;
            } else {

                return -1;
            }
        } else {

            return 0;
        }
    }

    public static function getUniqEleve($id_eleve)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = ' SELECT nom_prenom,sexe,email,contact,id_eleve_eleve FROM personne INNER JOIN eleve ON personne.id_type = eleve.id_eleve_eleve WHERE etat_pers = 1 AND type_pers = 2 AND id_type =' . $id_eleve;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }




    /**
     * Recupere les evaluations à venir
     */

    public static function get_groupe_alleval($id_groupe)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = 'SELECT * FROM(SELECT * FROM(SELECT * FROM prof_eval_emploitps WHERE etat_evalTps = 1)tmp_profprog INNER JOIN (SELECT tmp_evalprof_1.*,tmp_mat.codemat,tmp_mat.matlib, tmp_prof.nom_prenom ,tmp_prof.contact , tmp_grp.groupe_libelle FROM (SELECT * FROM prof_eval WHERE eval_etat = 2 AND id_groupe = ' . $id_groupe . ') tmp_evalprof_1, (SELECT id_matiere_matiere,code AS codemat,libele AS matlib FROM matiere WHERE id_matiere_matiere <> 0) tmp_mat , (SELECT id_type, nom_prenom,contact FROM personne WHERE type_pers = 2)tmp_prof , (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_id = ' . $id_groupe . ') tmp_grp WHERE tmp_evalprof_1.id_mat = tmp_mat.id_matiere_matiere AND tmp_prof.id_type = tmp_evalprof_1.id_prof AND tmp_grp.groupe_id = tmp_evalprof_1.id_groupe)tmp_profeval ON tmp_profprog.eval_id = tmp_profeval.prof_eval_id ORDER BY eval_date ASC) tmp_final INNER JOIN (SELECT id_salle ,libelle AS sallelib FROM salle)tmp_salle ON tmp_final.eval_salle_id =tmp_salle.id_salle';
        //var_dump($login,$pass,$sql);
        //exit();
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }





















    /***********ZIE******??????????? */
    public static function getEleveMat($id_eleve)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = ' SELECT * FROM(SELECT id_mat FROM eleve_matiere WHERE id_eleve = ' . $id_eleve . ' AND etat_eleve_mat = 1 )tmp_eleve INNER JOIN (SELECT id_matiere_matiere,code,libele FROM matiere WHERE etat =1)tmp_mat ON tmp_eleve.id_mat= tmp_mat.id_matiere_matiere';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    //get Matirere activer
    public static function getEleveMatriere($id_eleve)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = ' SELECT * FROM(SELECT id_mat FROM eleve_matiere WHERE id_eleve = ' . $id_eleve . ' AND etat_eleve_mat = 1)tmp_elevemat INNER JOIN (SELECT id_matiere_matiere,code,libele FROM matiere)tmp_mat ON tmp_elevemat.id_mat = tmp_mat.id_matiere_matiere';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function setEleveMat($id_matiere, $id_eleve)
    {
        $db = static::getDB();

        $sql = ' SELECT * FROM eleve_matiere WHERE id_eleve= "' . $id_eleve . '" AND id_mat= "' . $id_matiere . '"  LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {

            $data = [
                'id_eleve' => $id_eleve,
                'id_mat' => $id_matiere
            ];
            //var_dump($db);

            $sql = ' INSERT INTO eleve_matiere (id_eleve, id_mat) VALUES ( :id_eleve, :id_mat);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                return 'ajouter';
            } else {
                return 'erreur';
            }
        } else {

            $sql = ' UPDATE eleve_matiere SET etat_eleve_mat = 1 WHERE id_eleve =' . $id_eleve . ' AND id_mat = ' . $id_matiere . ' ;';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            if ($result == TRUE) {
                return 'ajouter';
            } else {
                return 'erreur';
            }
        }
    }

    public static function setSupEleveMat($id_matiere, $id_eleve)
    {
        $db = static::getDB();

        $sql = ' SELECT * FROM eleve_matiere WHERE id_eleve= "' . $id_eleve . '" AND id_mat= "' . $id_matiere . '"  LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {

            return 'inconnu';
        } else {

            $sql = ' UPDATE eleve_matiere SET etat_eleve_mat = 0 WHERE id_eleve =' . $id_eleve . ' AND id_mat = ' . $id_matiere . ' ;';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            if ($result == TRUE) {
                return 'supprimer';
            } else {
                return 'erreur';
            }
        }
    }
    /*****************??????????? */







    public static function setEleveGrpEval($id_grpe, $id_mat, $eval_lib, $eval_desc)
    {
        //eleve_eval_id 	id_eleve 	id_groupe 	id_mat 	eval_libelle 	eval_desc 	eval_etat 
        $id_eleve = intval($_SESSION['user']['id_type']);
        // var_dump($_POST,$_SESSION);
        //var_dump($id_eleve);exit;
        $db = static::getDB();
        //id_eleve 	id_groupe 	etat_eleve_classe 
        $sql = ' SELECT * FROM eleve_eval WHERE id_eleve= "' . $id_eleve . '" AND id_groupe= "' . $id_grpe . '" AND id_mat= "' . $id_mat . '" AND eval_libelle= "' . $eval_lib . '"  LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {

            $data = [
                'id_eleve' => $id_eleve,
                'id_groupe' => $id_grpe,
                'id_mat' => $id_mat,
                'eval_libelle' => $eval_lib,
                'eval_desc' => $eval_desc
            ];
            //var_dump($db);

            $sql = ' INSERT INTO eleve_eval (id_eleve, id_groupe, id_mat, eval_libelle, eval_desc) VALUES ( :id_eleve, :id_groupe, :id_mat, :eval_libelle, :eval_desc);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                //eleve_eval_emploiTps_id 	eval_id 	eval_date 	eval_hDebut 	eval_hFin 	etat_evalTps 
                $data = [
                    'eval_id' => $lastid
                ];
                //var_dump($db);
                $sql = ' INSERT INTO eleve_eval_emploitps (eval_id) VALUES ( :eval_id);';
                $stmt = $db->prepare($sql);
                $result = $stmt->execute($data);
                //$lastid =  $db->lastInsertId();
                return 'ajouter';
            } else {
                return 'erreur';
            }
        } else {

            $sql = ' UPDATE eleve_eval SET eval_etat = 1 WHERE id_eleve =' . $id_eleve . ' AND id_groupe = ' . $id_grpe . ' AND id_mat = ' . $id_mat . ' AND eval_libelle = "' . $eval_lib . '" ;';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            if ($result == TRUE) {
                return 'ajouter';
            } else {
                return 'erreur';
            }
        }
    }
    public static function getEleveGrpEval()
    {
        $id_eleve = intval($_SESSION['user']['id_type']);
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = ' SELECT * FROM(SELECT * FROM(SELECT id_groupe,eleve_eval_id,id_mat,eval_libelle,eval_desc FROM eleve_eval WHERE id_eleve = ' . $id_eleve . ' AND eval_etat = 1) tmp_evaleleve INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp ON tmp_evaleleve.id_groupe =tmp_grp.groupe_id)tmp_eleveeval INNER JOIN (SELECT id_matiere_matiere,code,libele AS matierelib FROM matiere)tmp_mat ON tmp_eleveeval.id_mat = tmp_mat.id_matiere_matiere ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function getEleveGrpEvalWithDate()
    {
        $id_eleve = intval($_SESSION['user']['id_type']);
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = ' SELECT * FROM  (SELECT * FROM(SELECT * FROM(SELECT id_groupe,eleve_eval_id,id_mat,eval_libelle,eval_desc FROM eleve_eval WHERE id_eleve = ' . $id_eleve . ' AND eval_etat = 1) tmp_evaleleve INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp ON tmp_evaleleve.id_groupe =tmp_grp.groupe_id)tmp_eleveeval INNER JOIN (SELECT id_matiere_matiere,code,libele AS matierelib FROM matiere)tmp_mat ON tmp_eleveeval.id_mat = tmp_mat.id_matiere_matiere  )tmp_eleveeval INNER JOIN (SELECT * FROM eleve_eval_emploitps ORDER BY eleve_eval_emploiTps_id ASC)tmp_evaltps ON tmp_eleveeval.eleve_eval_id = tmp_evaltps.eval_id';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function getEleveGrpEvalWithDateBy($eval_id)
    {
        $id_eleve = intval($_SESSION['user']['id_type']);
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = ' SELECT * FROM  (SELECT * FROM(SELECT * FROM(SELECT id_groupe,eleve_eval_id,id_mat,eval_libelle,date_creation_eval,eval_desc FROM eleve_eval WHERE id_eleve = ' . $id_eleve . ' AND eleve_eval_id = ' . $eval_id . ' AND eval_etat = 1) tmp_evaleleve INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_etat = 1)tmp_grp ON tmp_evaleleve.id_groupe =tmp_grp.groupe_id)tmp_eleveeval INNER JOIN (SELECT id_matiere_matiere,code,libele AS matierelib FROM matiere)tmp_mat ON tmp_eleveeval.id_mat = tmp_mat.id_matiere_matiere  )tmp_eleveeval INNER JOIN (SELECT * FROM eleve_eval_emploitps ORDER BY eleve_eval_emploiTps_id ASC)tmp_evaltps ON tmp_eleveeval.eleve_eval_id = tmp_evaltps.eval_id';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getEvalSalle($id_eval)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = ' SELECT * FROM(SELECT eval_salle_id FROM eleve_eval_emploitps WHERE eval_id = ' . $id_eval . ')tmp_idsalle INNER JOIN (SELECT * FROM salle)tmp_salle ON tmp_idsalle.eval_salle_id = tmp_salle.id_salle';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getAllEleveByGroup($id_eval)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = ' SELECT * FROM(SELECT * FROM(SELECT * FROM(SELECT id_type,nom_prenom,sexe,email,contact FROM personne WHERE type_pers = 1 AND etat_pers = 1)tmp_elev INNER JOIN (SELECT elv_ds_grpe_idelev,elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_etat =1)tmp_grpele ON tmp_elev.id_type = tmp_grpele.elv_ds_grpe_idelev)tmp_a INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1)tmp_b ON tmp_a.id_type = tmp_b.id_eleve_eleve)tmp_aa INNER JOIN (SELECT id_groupe,groupe_libelle FROM(SELECT * FROM groupe)tmp_grp INNER JOIN (SELECT id_groupe FROM eleve_eval WHERE eval_etat=1 AND eleve_eval_id = ' . $id_eval . ')tmp_grpeval ON tmp_grp.groupe_id = tmp_grpeval.id_groupe) tmp_bb on tmp_aa.elv_ds_grpe_groupe = tmp_bb.id_groupe';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function setGetInitEleveEvalNote($id_eleve, $id_eval)
    {
        $db = static::getDB();
        $etatsql = 0;
        //id_notes_notes 	id_eleve_eleve 	id_evaluation 	note 
        $sql = ' SELECT * FROM notes WHERE id_eleve_eleve = ' . $id_eleve . ' AND id_evaluation = ' . $id_eval . ' LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result,$id_eleve, $id_eval);exit;
        if (empty($result) || $result == 0) {

            $data = [
                'id_eleve_eleve' => $id_eleve,
                'id_evaluation' => $id_eval
            ];
            //var_dump($db);

            $sql = ' INSERT INTO notes (id_eleve_eleve, id_evaluation) VALUES ( :id_eleve_eleve, :id_evaluation)';
            //var_dump($sql,$data);exit;
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            //$lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                $etatsql = 1;
            } else {
                $etatsql = 0;
            }
        }

        $sql = ' SELECT * FROM(SELECT * FROM(SELECT * FROM(SELECT * FROM(SELECT id_type,nom_prenom,sexe,email,contact FROM personne WHERE type_pers = 1 AND etat_pers = 1)tmp_elev INNER JOIN (SELECT elv_ds_grpe_idelev,elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_etat =1)tmp_grpele ON tmp_elev.id_type = tmp_grpele.elv_ds_grpe_idelev)tmp_a INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1)tmp_b ON tmp_a.id_type = tmp_b.id_eleve_eleve)tmp_aa INNER JOIN (SELECT id_groupe,groupe_libelle,eleve_eval_id FROM(SELECT * FROM groupe)tmp_grp INNER JOIN (SELECT id_groupe,eleve_eval_id FROM eleve_eval WHERE eval_etat=1 AND eleve_eval_id = ' . $id_eval . ')tmp_grpeval ON tmp_grp.groupe_id = tmp_grpeval.id_groupe) tmp_bb on tmp_aa.elv_ds_grpe_groupe = tmp_bb.id_groupe)tmp_finaltab INNER JOIN (SELECT * FROM notes WHERE id_eleve_eleve = ' . $id_eleve . ' )tmp_notes ON tmp_finaltab.eleve_eval_id = tmp_notes.id_evaluation';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getAllEleveNoteBy($id_eleve)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = 'SELECT * FROM(SELECT note,eval_libelle, coef, notation, eval_date, tmp_sess.Libelle_session,eval_id , id_mat, id_groupe, id_prof FROM (SELECT * FROM(SELECT * FROM(SELECT * FROM prof_eval INNER JOIN prof_eval_emploitps ON prof_eval.prof_eval_id = prof_eval_emploitps.eval_id)tmp_evalTps WHERE eval_etat = 2 AND id_groupe IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = ' . $id_eleve . ' AND elv_ds_grpe_etat = 1) ) tmp_f_a INNER JOIN (SELECT id_evaluation,note FROM notes WHERE id_eleve_eleve = ' . $id_eleve . ' AND etat_note = 1 )tmp_f_b ON tmp_f_a.prof_eval_id = tmp_f_b.id_evaluation)tmp_finalok INNER JOIN (SELECT id_session_session, Libelle_session FROM annee_session)tmp_sess ON tmp_finalok.eval_session = tmp_sess.id_session_session)tmp_final_b INNER JOIN (SELECT code AS code_mat,libele AS lib_mat,id_matiere_matiere FROM matiere)tmp_mat ON tmp_final_b.id_mat =tmp_mat.id_matiere_matiere';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getAllEleve_Mat_MoyenneBy($id_eleve)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = 'SELECT * FROM (SELECT * FROM (SELECT * FROM moyenne WHERE etat_moy=1 AND id_eleve = ' . $id_eleve . ' AND id_groupe IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = ' . $id_eleve . ' AND elv_ds_grpe_etat = 1))tmp_matmoy INNER JOIN (SELECT id_session_session,Libelle_session FROM annee_session)tmp_session ON tmp_matmoy.id_session =  tmp_session.id_session_session )tmp_f_a INNER JOIN (SELECT matiere_id_tmp, coeficient_tmp,libele_partie,id_annee_partie,mat_cod	,mat_lib FROM (SELECT * FROM(SELECT matiere_id_tmp,coeficient_tmp,groupe_id_tmp,tmp_partannee.libele_partie,tmp_partannee.id_annee_partie FROM groupe_matiere_coef INNER JOIN (SELECT id_annee_partie, libele_partie FROM annee_partie)tmp_partannee ON groupe_matiere_coef.part_annee_id_tmp = tmp_partannee.id_annee_partie)tmp_a WHERE  tmp_a.groupe_id_tmp IN(SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = ' . $id_eleve . ' AND elv_ds_grpe_etat = 1) )tmp_mat_per INNER JOIN (SELECT id_matiere_matiere,code AS mat_cod, libele AS mat_lib FROM matiere)tmp_mat ON  tmp_mat_per.matiere_id_tmp = tmp_mat.id_matiere_matiere)tmp_f_b ON tmp_f_a.id_matiere = tmp_f_b.matiere_id_tmp';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public static function get_elevGrp_anneePart($id_eleve)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        $sql = 'SELECT * FROM (SELECT * FROM annee_scolaire INNER JOIN annee_partie ON annee_scolaire.id_anscol_annee_scolaire =annee_partie.id_anneeScolaire ORDER BY annee_scolaire.id_anscol_annee_scolaire)tmp_annee_part WHERE id_anneeScolaire IN (SELECT groupe_annee FROM groupe WHERE groupe_id IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = ' . $id_eleve . ' AND elv_ds_grpe_etat = 1) )';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getAllEleve_UniqMaxMat_MoyenneBy($id_eleve, $id_partannee)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = 'CREATE TEMPORARY TABLE tmp_tempory_table SELECT * FROM (SELECT id_groupe,id_eleve,id_matiere,id_prof,id_session,moyenne,etat_moy,id_session_session,Libelle_session,matiere_id_tmp,coeficient_tmp, libele_partie,id_annee_partie, mat_cod, mat_lib  FROM(SELECT * FROM (SELECT * FROM (SELECT * FROM moyenne WHERE etat_moy=1 AND id_eleve = ' . $id_eleve . ' AND id_groupe IN (SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = ' . $id_eleve . ' AND elv_ds_grpe_etat = 1))tmp_matmoy INNER JOIN (SELECT id_session_session,Libelle_session FROM annee_session)tmp_session ON tmp_matmoy.id_session =  tmp_session.id_session_session )tmp_f_a INNER JOIN (SELECT matiere_id_tmp, coeficient_tmp,libele_partie,id_annee_partie,mat_cod,mat_lib FROM (SELECT * FROM(SELECT matiere_id_tmp,coeficient_tmp,groupe_id_tmp,tmp_partannee.libele_partie,tmp_partannee.id_annee_partie FROM groupe_matiere_coef INNER JOIN (SELECT id_annee_partie, libele_partie FROM annee_partie)tmp_partannee ON groupe_matiere_coef.part_annee_id_tmp = tmp_partannee.id_annee_partie)tmp_a WHERE  tmp_a.groupe_id_tmp IN(SELECT elv_ds_grpe_groupe FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = ' . $id_eleve . ' AND elv_ds_grpe_etat = 1) )tmp_mat_per INNER JOIN (SELECT id_matiere_matiere,code AS mat_cod, libele AS mat_lib FROM matiere)tmp_mat ON  tmp_mat_per.matiere_id_tmp = tmp_mat.id_matiere_matiere)tmp_f_b ON tmp_f_a.id_matiere = tmp_f_b.matiere_id_tmp  ORDER BY  mat_cod ASC)tmp_moy_ok_final)tmp_moy_ok_final_a WHERE tmp_moy_ok_final_a.id_annee_partie = ' . $id_partannee . ';';
        //print_r($sql);
        //exit();
        $stmt = $db->query($sql);
        //$result = $stmt->fetchAll(PDO::FETCH_ASSOC);



        $sql = 'SELECT * FROM ( SELECT *, RANK() OVER (PARTITION BY matiere_id_tmp ORDER BY moyenne DESC) dest_rank FROM tmp_tempory_table )tmp_tble where dest_rank = 1;';
        //print_r($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        return $result;
    }
}
