<?php

namespace App\Controllers\Elearning;

use \Core\View;
use App\Config;
use App\Models\Eleve;
use App\Models\Elearning\Prof_elearn as Prof_elearn;

use App\Models\Elearning\Eleve_elearn as Eleve_elearn;
use App\Controllers\Crt_eleve;

use App\Models\Model_public;
use App\Models\Prof;
use App\Models\Upload_files;
use App\Models\Log as modeldb;

/**

 * Home controller

 *

 * PHP version 7.0

 */

class Crt_E_eleve extends \Core\Controller
{

    /**
     *      ELEARNING
     */
    public static function infosuser()
    {

        
        if ($_SERVER['HTTP_HOST'] == 'localhost.eschool') {
            $protocole=Config::PROTOC_LOCAL;
        } else {
            $protocole=Config::PROTOC_LIGNE;
        }

        $base_liens =  $protocole.'://'.($_SERVER['HTTP_HOST']).'/';
        $liens =  $protocole.'://'.($_SERVER['HTTP_HOST']).'/files'.'/';

        $tabdata_user = (object)$_SESSION['user'] ;
        $infos_univ =(object)(Model_public::get_univInfo_By($tabdata_user->fk_iduniv)[0]);
        $get_all_user_notif = Model_public::get_all_user_notif($tabdata_user->type_pers,$tabdata_user->id_type);

        //var_dump($infos_univ);
        
        $tabdatas = [
            'user_allnotif_defil'  =>  $get_all_user_notif,
            'fk_iduniv'  =>  $infos_univ->id_univ,
            'non_univ'  =>  $infos_univ->non_univ,
            'contact_univ'  =>  $infos_univ->contact_univ,
            'email_univ'  =>  $infos_univ->email_univ,
            'ville_univ'  =>  $infos_univ->ville_univ,
            'initiale_univ'  =>  $infos_univ->initiale_univ,

            'id_pers_personne'  =>  $tabdata_user->id_pers_personne,
            'nom_prenom' =>  $tabdata_user->nom_prenom,
            'date_naiss' => $tabdata_user->date_naiss,
            'lieu_naiss' => $tabdata_user->lieu_naiss,
            'sexe' =>  $tabdata_user->sexe,
            'email' =>  $tabdata_user->email,
            'contact' =>  $tabdata_user->contact,
            'type_pers' =>  $tabdata_user->type_pers,
            'id_type' =>  $tabdata_user->id_type,
            'base_liens' =>  $base_liens, 
            'liens' =>  $liens,

        ];

        $eleveconect_infos= Eleve::get_eleves_allinfos($tabdata_user->id_type);


        if (empty( $eleveconect_infos)) {   
            $tabdatas =  $tabdatas + [

                'matricule' => 'aucun',
                'Niveau' => 'aucun',
    
                'groupe_libelle' => 'aucun',
                'class_lib' => 'aucun',
    
                'annee_libelle' => 'aucun',
                'annee_date_debut' => 'aucun',
                'annee_date_fin' => 'aucun',
    
                'groupe_annee_id' => 0,
                'groupe_classe_id' => 0,
                'groupe_grpe_id' => 0
            ];
         }
        else {
            $tabdata_eleve_infosecole = (object)($eleveconect_infos)[0];

            $tabdatas =  $tabdatas + [

                'matricule' => $tabdata_eleve_infosecole->matricule,
                'Niveau' => $tabdata_eleve_infosecole->Niveau,
    
                'groupe_libelle' => $tabdata_eleve_infosecole->groupe_libelle,
                'class_lib' => $tabdata_eleve_infosecole->class_lib,
    
                'annee_libelle' => $tabdata_eleve_infosecole->annee_libelle,
                'annee_date_debut' => $tabdata_eleve_infosecole->annee_date_debut,
                'annee_date_fin' => $tabdata_eleve_infosecole->annee_date_fin,
    
                'groupe_annee_id' => $tabdata_eleve_infosecole->groupe_annee,
                'groupe_classe_id' => $tabdata_eleve_infosecole->groupe_classe,
                'groupe_grpe_id' => $tabdata_eleve_infosecole->elv_ds_grpe_groupe
                
    
            ];
        }


        $filename = '../files/'.$tabdata_user->id_pers_personne.'/'.$tabdata_user->id_pers_personne.'.jpg';

        if (file_exists($filename)) {
            $tabdatas['lien_photo']= $tabdatas['liens'].$tabdata_user->id_pers_personne.'/'.$tabdata_user->id_pers_personne.'.jpg';
        } else {
            unset($tabdatas['lien_photo'] );
        }

        return $tabdatas;


    }



    public function elearning_mescoursAction()
    {

        $tabdatas = Crt_E_eleve::infosuser();
        $tabdata_user = (object)$tabdatas;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        $id_eleve = $tabdata_user->id_type;
        $groupe_libelle = $tabdata_user->groupe_libelle;
        $groupe_grpe_id = $tabdata_user->groupe_grpe_id;
        $fct_exec="|| ";

        $tabdatas['eleve_all_matiere'] = [];
        //echo '<code><pre>';
        //var_dump($tabdata_user);

        //$date_jour = date("Y-m-d");
        $get_eleve_prochainmat = Eleve_elearn::get_eleves_grpEmploiTps($groupe_grpe_id);
        $fct_exec=$fct_exec."Eleve_elearn::get_eleves_grpEmploiTps(".$groupe_grpe_id.") ||";

        foreach ($get_eleve_prochainmat as $key => $value) {

            $get_nbre_courplan_nif = intval(Prof_elearn::get_nbre_courplan_nif(intval($value['emploitps_id_mat']), intval($tabdata_user->groupe_grpe_id), intval($value['emploitps_id_prof'])));
            //var_dump('get_nbre_courplan_nif <pre>',$get_nbre_courplan_nif,'</pre>' );
            $get_nbre_courplan = intval(Prof_elearn::get_nbre_courplan(intval($value['emploitps_id_mat']), intval($value['emploitps_id_prof'])));
            //var_dump('get_nbre_courplan <pre>',$get_nbre_courplan,'</pre>' );
            if ($get_nbre_courplan == 0) {
                ($get_eleve_prochainmat[$key])["progression"] = 0;
            } else {
                ($get_eleve_prochainmat[$key])["progression"] = ($get_nbre_courplan_nif * 100) / $get_nbre_courplan;
            }
            $fichier =  '../BanqueDefichiers/matiere/' . intval($value['emploitps_id_mat']) . '.jpg';
            if (file_exists($fichier)) {
                ($get_eleve_prochainmat[$key])['liens'] = "/BanqueDefichiers/matiere/" . intval($value['emploitps_id_mat']) . '.jpg';
            }
        }

        $tabdatas['get_eleve_prochainmat'] = $get_eleve_prochainmat;
        //var_dump($tabdatas['get_eleve_prochainmat']);

        //$getEleve_grpeMatParent =  Eleve_elearn::getEleve_grpeMatParent($tabdata_user->id_type);
        $tabdatas['eleve_all_matiere'] =  Eleve_elearn::get_grpe_matProf_allinfos($groupe_grpe_id);
        //var_dump($tabdatas['eleve_all_matiere']);
        $fct_exec=$fct_exec."Eleve_elearn::get_grpe_matProf_allinfos(".$groupe_grpe_id.") ||";
        foreach ($tabdatas['eleve_all_matiere'] as $key => $value) {
            $fichier =  '../BanqueDefichiers/matiere/' . intval($value['matiere_id_tmp']) . '.jpg';
            if (file_exists($fichier)) {
                ($tabdatas['eleve_all_matiere'][$key])['liens'] = "/BanqueDefichiers/matiere/" . intval($value['matiere_id_tmp']) . '.jpg';
            }
        }


        $tabdatas['univInfos'] = Model_public::get_univInfo_By($tabdata_user->fk_iduniv);


        $tabdatas['menu'] = "Elearning";
        $tabdatas['sousmenu'] = "Elearning > Mes Cours";

        $tabdatas['toast_notif']['etat'] = "success";
        $tabdatas['toast_notif']['infos'] = "Acces a mes cours";


        $_POST = NULL;
        $_GET = NULL;
        unset($_POST);
        unset($_GET);
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_E_eleve ::: elearning_mescoursAction => " . $fct_exec;
		$log_user =$tabdatas['menu']." -- ".$tabdatas['sousmenu'];
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/
        //echo '</pre></code>';

        View::renderTemplate('Elearning/Eleve/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function  elearning_eleve_coursinfosAction()
    {
        //var_dump( 'http://'.($_SERVER['HTTP_HOST']).dirname($_SERVER['SCRIPT_NAME'], 2).'/files'.'/' );
        //echo '<code><pre>';
        //var_dump($_GET);exit;

        $tabdatas = Crt_E_eleve::infosuser();
        $tabdata_user = (object)$tabdatas;
        $id_eleve = intval($tabdata_user->id_type);
        //var_dump($tabdatas );
        //echo '<pre>';
        //var_dump($_SERVER['HTTP_HOST'],$_SERVER['SCRIPT_NAME'],dirname($_SERVER['SCRIPT_NAME'], 1));
        //var_dump($_GET);
        //echo '/<pre>';
        //$tabdatas['profpers_name'] 
        $tabdatas['menu'] = "Elearning";
        $tabdatas['univInfos'] = Model_public::get_univInfo_By($tabdata_user->fk_iduniv);

        if (isset($_GET['action']) && isset($_GET['id_matiere']) && isset($_GET['matiere_select']) && $_GET['action'] == "getcoursdocs"  && $_GET['id_matiere'] != "" && $_GET['id_matiere'] != 0) {

            $id_matiere = intval(htmlspecialchars($_GET['id_matiere']));
            $matiere_select = htmlspecialchars($_GET['matiere_select']);

            $profpers_select = intval(htmlspecialchars($_GET['profpers_select']));

            $infos_prof = Eleve_elearn::getEleve_profname($profpers_select);
            $tabdatas['profpers_name'] =  $infos_prof[0]['nom_prenom'];
            $tabdatas['profpers_tel'] =  $infos_prof[0]['contact'];
            $tabdatas['eleve_nom'] =  $tabdata_user->nom_prenom;
            $tabdatas['eleve_idpers'] =  $tabdata_user->id_pers_personne;

            //var_dump( '<pre>',$infos_prof,'</pre>');


            if (isset($_GET['groupelibelle']) && isset($_GET['profpers_name'])) {
                $tabdatas['groupelibelle'] = (htmlspecialchars($_GET['groupelibelle']));
                $tabdatas['profpers_name'] = (htmlspecialchars($_GET['profpers_name']));
                $tabdatas['profpers_tel'] = (htmlspecialchars($_GET['profpers_tel']));
                $tabdatas['id_eleve'] =  $id_eleve;
            }

            if (isset($_GET['groupeid'])) {
                $tabdatas['groupeid_eleve'] = intval(htmlspecialchars($_GET['groupeid']));
            }
            if (isset($_GET['etat_online'])) {
                if ($_GET['etat_online'] == 'online') {
                    $tabdatas['etat_online'] = 'online';
                }
            }

            $tabdatas['get_cour_section'] = Prof_elearn::get_cour_section($profpers_select, $id_matiere);
            $tabdatas['get_cour_partie'] = Prof_elearn::get_cour_partie($profpers_select, $id_matiere);

            foreach ($tabdatas['get_cour_section'] as $key => $value) {
                ($tabdatas['get_cour_section'][$key])['plan_description'] = html_entity_decode($value['plan_description']);

                $sa_courplan_id = intval(($tabdatas['get_cour_section'][$key])['id_cours_plan']);
                //var_dump($_GET);
                if (isset($_GET['groupeid']) && $_GET['groupeid'] != 0 && $_GET['groupeid'] != "") {
                    $get_suivi_active_courplan = Prof_elearn::get_suivi_active_courplan($sa_courplan_id, $tabdatas['groupeid_eleve']);
                    //var_dump('cour plan '.$sa_courplan_id,$get_suivi_active_courplan );
                    if (empty($get_suivi_active_courplan)) {
                        ($tabdatas['get_cour_section'][$key])['vue_groupe'] = 0;
                        ($tabdatas['get_cour_section'][$key])['etat_progression'] = 0;
                    } else {
                        ($tabdatas['get_cour_section'][$key])['vue_groupe'] = intval($get_suivi_active_courplan[0]["sa_etatvue_groupe"]);
                        ($tabdatas['get_cour_section'][$key])['etat_progression'] = intval($get_suivi_active_courplan[0]["sa_progression"]);
                    }
                }
            }

            foreach ($tabdatas['get_cour_partie'] as $key => $value) {
                ($tabdatas['get_cour_partie'][$key])['plan_description'] = html_entity_decode($value['plan_description']);

                $sa_courplan_id = intval(($tabdatas['get_cour_partie'][$key])['id_cours_plan']);

                if (isset($_GET['groupeid']) && $_GET['groupeid'] != 0 && $_GET['groupeid'] != "") {
                    $get_suivi_active_courplan = Prof_elearn::get_suivi_active_courplan($sa_courplan_id, $tabdatas['groupeid_eleve']);
                    //var_dump('cour plan '.$sa_courplan_id,$get_suivi_active_courplan );
                    if (empty($get_suivi_active_courplan)) {
                        ($tabdatas['get_cour_partie'][$key])['vue_groupe'] = 0;
                        ($tabdatas['get_cour_partie'][$key])['etat_progression'] = 0;
                    } else {
                        ($tabdatas['get_cour_partie'][$key])['vue_groupe'] = intval($get_suivi_active_courplan[0]["sa_etatvue_groupe"]);
                        ($tabdatas['get_cour_partie'][$key])['etat_progression'] = intval($get_suivi_active_courplan[0]["sa_progression"]);
                    }
                }
            }
            //exit;
            //var_dump('get_cour_section', $tabdatas['get_cour_section']);

            //var_dump( $tabdatas['get_cour_partie'] );
            $tabdatas['get_matiere_docsvideo'] = Prof_elearn::get_matiere_docsvideo($id_matiere, $profpers_select);
            $tabdatas['liens_profmat_docs'] = $tabdatas['base_liens'] . 'BanqueDefichiers/COURS/' . $profpers_select . '/' . $id_matiere . '/';
            //var_dump($tabdatas['get_matiere_docsvideo']);

        } else {
            header('Refresh: 1;URL=index.php?p=elearning_mescours');
        }


        //$tabdatas['get_matEnseigners']=Prof_elearn::get_proMatEnseigner($id_pers_prof);
        //var_dump( $tabdatas['get_matEnseigners']);

        /**  INFORMATIONS A RENVOYER A LA PAGE */



        $tabdatas['sousmenu'] = "Mes Cours > Suivi cours > " . $matiere_select;
        $tabdatas['mat_select'] = $id_matiere;
        $tabdatas['profpers_select'] = $profpers_select;
        $tabdatas['id_eleve'] = $id_eleve;
        $tabdatas['matiere_select'] = $matiere_select;
        //var_dump($_POST);
        
        if (isset($_POST['visio_conf_jitsi']) && ($_POST['visio_conf_jitsi']=="jitsi")) {
            $_SESSION['page']= "elearning_eleve_jitsi" ;
            //var_dump($_GET);
            //var_dump($tabdatas['matiere_select']);
            //var_dump($tabdatas);
        }

        //$tabdatas['test_html'] = '<strong>Bonjour</strong> elvis eudes ';

        //var_dump($tabdatas['get_prof_prochainmat'] );

        //var_dump($tabdatas);
        $_POST = NULL;
        $_GET = NULL;
        unset($_POST);
        unset($_GET);
        //echo '</pre></code>';

        View::renderTemplate('Elearning/Eleve/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function elearning_eleve_evalAction()
    {
        echo '<pre>';
        $tabdatas = Crt_E_eleve::infosuser();
        $tabdata_user = (object)$tabdatas;
        //var_dump( $tabdata_user );exit;
        $tabdatas['menu'] = "Elearning";
        $tabdatas['sousmenu'] = "Evaluations";

        $tabdatas['get_groupe_alleval'] = Eleve_elearn::get_groupe_alleval($tabdata_user->groupe_grpe_id);
        //var_dump($tabdatas['get_groupe_alleval'] );
        $tabdatas['univInfos'] = Model_public::get_univInfo_By($tabdata_user->fk_iduniv);

        $_POST = NULL;
        $_GET = NULL;
        unset($_POST);
        unset($_GET);
        echo '</pre>';
        View::renderTemplate('Elearning/Eleve/' . $_SESSION['page'] . '.html', $tabdatas);
    }
    /**
     *      ELEARNING
     */



    public function elearning_eleveAction()
    {

        $_SESSION['page'] = "elearning_eleve";
        $tabdatas = Crt_E_eleve::infosuser();
        $tabdata_user = (object)$tabdatas;

        //var_dump($_SERVER['HTTP_HOST']);

        $tabdatas['menu'] = "Elearning";
        $tabdatas['sousmenu'] = "Elearning > Cours et activités";



        $_POST = NULL;
        $_GET = NULL;
        unset($_POST);
        unset($_GET);

        View::renderTemplate('Elearning/Eleve/' . $_SESSION['page'] . '.html', $tabdatas);
    }


    public function elearning_start_evaluationAction()
    {

        $_SESSION['page'] = "elearning_start_evaluation";
        $tabdatas = Crt_E_eleve::infosuser();
        $tabdata_user = (object)$tabdatas;

        //var_dump($_SERVER['HTTP_HOST']);

        $tabdatas['menu'] = "Elearning";
        $tabdatas['sousmenu'] = "Elearning > Evaluations";



        $_POST = NULL;
        $_GET = NULL;
        unset($_POST);
        unset($_GET);

        View::renderTemplate('Elearning/Eleve/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function elearning_eleve_uploadAction()
    {

        $_SESSION['page'] = "elearning_eleve_upload";
        $tabdatas = Crt_E_eleve::infosuser();
        $tabdata_user = (object)$tabdatas;

        $tabdatas['notif_file'] = [];
        $id_notif_tab = [];

        $id_etud_personne = $tabdata_user->id_pers_personne;

        //var_dump($_GET);
        
        if (isset($_GET['eval'])) {
            $_SESSION['id_eval'] = intval($_GET['eval']);
            //var_dump($_SESSION['id_eval']);
        }
        if (isset($_SESSION['id_eval'])) {

            $tabdatas['get_Eval_by']=Model_public::get_eval_infosBy($_SESSION['id_eval']);
             

            if (!empty($tabdatas['get_Eval_by'])) {
                $tabdatas['get_Eval_by']=$tabdatas['get_Eval_by'][0];
                //var_dump($tabdatas['get_Eval_by']);
                $id_prof = intval($tabdatas['get_Eval_by']['id_prof']);
                //var_dump($id_prof);
                $tabdatas['get_Pers_infos']=(Model_public::get_Pers_infos($id_prof,2))[0];
                //var_dump($tabdatas['get_Pers_infos']);
                $id_prof_pers = intval($tabdatas['get_Pers_infos']['id_pers_personne']);

                $dossier = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['SCRIPT_NAME' ],2).'/BanqueDefichiers/evaluations/'.$_SESSION['id_eval'].'/'.$id_prof_pers.'/';
                
                if(is_dir($dossier)){
                    $liens = $tabdatas['base_liens'].'BanqueDefichiers/evaluations/'.$_SESSION['id_eval'].'/'.$id_prof_pers.'/';
                    $filesprof_get = Upload_files::get_dossierContenu($dossier, $liens);
                    //var_dump($filesprof_get );
                    $tabdatas['filesprof_get'] = $filesprof_get;
                }

            }



            $dossier = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['SCRIPT_NAME' ],2).'/BanqueDefichiers/evaluations/'.$_SESSION['id_eval'].'/'.$id_etud_personne.'/';
            if(is_dir($dossier)){
                $liens = $tabdatas['base_liens'].'BanqueDefichiers/evaluations/'.$_SESSION['id_eval'].'/'.$id_etud_personne.'/';
                $files_get = Upload_files::get_dossierContenu($dossier, $liens);
                // var_dump($files_get );
                $tabdatas['files_get'] = $files_get;
            }
  
            
        }

 
        $tabdatas['menu'] = "Elearning";
        $tabdatas['sousmenu'] = "Elearning > Import de fichiers";
        $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);



        $_POST = NULL;
        $_GET = NULL;
        unset($_POST);
        unset($_GET);

        View::renderTemplate('Elearning/Eleve/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    
}
