<?php

/**
 * 
 */
namespace App\Controllers\Elearning;

use \Core\View;
use App\Config;
use App\Models\Prof;
use App\Models\Admin;
use App\Models\Upload_files;
use App\Models\Elearning\Prof_elearn as Prof_elearn;

use App\Models\Model_public;

/*
* SQL récupérer une bd 
SELECT * FROM INFORMATION_SCHEMA.TABLES
*/
class Crt_E_prof extends \Core\Controller
{

    public static function infosuser(){
    

        if (Config::ENVI == 'local') {
            $protocole=Config::PROTOC_LOCAL;
        } else {
            $protocole=Config::PROTOC_LIGNE;
        }

        $base_liens =  $protocole.'://'.($_SERVER['HTTP_HOST']).'/';
        $liens =  $protocole.'://'.($_SERVER['HTTP_HOST']).'/files'.'/';

        $tabdata_user = (object)$_SESSION['user'] ;
        $infos_univ =(object)(Model_public::get_univInfo_By($tabdata_user->fk_iduniv)[0]);

        $get_user_annee = Model_public::get_user_annee($tabdata_user->id_pers_personne);


        $tabdatas = [

            'fk_iduniv'  =>  $infos_univ->id_univ,
            'non_univ'  =>  $infos_univ->non_univ,
            'contact_univ'  =>  $infos_univ->contact_univ,
            'email_univ'  =>  $infos_univ->email_univ,
            'ville_univ'  =>  $infos_univ->ville_univ,
            'initiale_univ'  =>  $infos_univ->initiale_univ,

            'lib_user_annee'  => $get_user_annee[0]["annee_libelle"],
            'lib_user_id_annee'  => intval($get_user_annee[0]["id_anscol_annee_scolaire"]),

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
            'liens' =>  $liens 
        ];

        $filename = '../files/'.$tabdata_user->id_pers_personne.'/'.$tabdata_user->id_pers_personne.'.jpg';

        if (file_exists($filename)) {
            $tabdatas['lien_photo']= $tabdatas['liens'].$tabdata_user->id_pers_personne.'/'.$tabdata_user->id_pers_personne.'.jpg';
        } else {
            unset($tabdatas['lien_photo'] );
        }

        return $tabdatas;

    }

    public function elearning_profAction(){
        //var_dump( 'http://'.($_SERVER['HTTP_HOST']).dirname($_SERVER['SCRIPT_NAME'], 2).'/files'.'/' );
        $tabdatas['univInfos']=Admin::getUnivInfos();
        $tabdatas = Crt_E_prof::infosuser();
        $tabdata_user = (object)$tabdatas ;

        $tabdatas['get_matEnseigners']=Prof_elearn::get_proMatEnseigner($tabdata_user->id_type);
       
        foreach ($tabdatas['get_matEnseigners'] as $key => $value) {
            $fichier =  '../BanqueDefichiers/matiere/'.intval($value['id_matiere_matiere']).'.jpg';
            if( file_exists ( $fichier)){
                ($tabdatas['get_matEnseigners'][ $key])['liens']="/BanqueDefichiers/matiere/".intval($value['id_matiere_matiere']).'.jpg';
            }
        }
        //var_dump( $tabdatas['get_matEnseigners']);

        /**
            * INFORMATIONS A RENVOYER A LA PAGE
        */
        
        $tabdatas['toast_notif']['etat'] = "success";
        $tabdatas['toast_notif']['infos'] = "Acces a mes cours";

        $tabdatas['menu']="Elearning";
        $tabdatas['sousmenu']="Mes Cours";
        
        if ( isset($_GET['action']) && $_GET['action'] == "getcoursdocs" && $_GET['id_prof'] != "" && $_GET['id_matiere'] !="" ) {

            $id_pers_prof = intval($tabdata_user->id_type);
            $id_matiere = intval(htmlspecialchars($_GET['id_matiere']));
            $matiere_select= htmlspecialchars($_GET['matiere_select']);
            $tabdatas['mat_select'] = $id_matiere;
            $tabdatas['profpers_select'] = $id_pers_prof;
            $tabdatas['matiere_select'] = $matiere_select;

            $dossier = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['SCRIPT_NAME' ],2).'/BanqueDefichiers/COURS/'.$id_pers_prof.'/'.$id_matiere.'/';

            //var_dump($dossier);
            $tabdatas['get_profmatiere_video'] = Prof_elearn::get_profmatiere_video($id_matiere, $id_pers_prof);
            //var_dump($tabdatas['get_profmatiere_video']);

            if(is_dir($dossier)){

                $liens = $tabdatas['base_liens'].'BanqueDefichiers/COURS/'.$id_pers_prof.'/'.$id_matiere.'/';
                $files_get = Upload_files::get_dossierContenu($dossier, $liens);
                // var_dump($files_get );
                $tabdatas['files_get'] = $files_get;


            }
            else{

            }
            //exit;

            //BanqueDefichiers/COURS/IDPROF/IDMATIERE



        } 

        $date_jour = date("Y-m-d");
        $get_prof_prochainmat = Prof_elearn::get_prof_prochainmat($tabdata_user->id_type, $date_jour );

        foreach ($get_prof_prochainmat as $key => $value) {

            $get_nbre_courplan_nif =intval( Prof_elearn::get_nbre_courplan_nif(intval($value['id_matiere_matiere']), intval($value['groupe_id']), $tabdata_user->id_type));
            //var_dump('get_nbre_courplan_nif <pre>',$get_nbre_courplan_nif,'</pre>' );
            $get_nbre_courplan =intval( Prof_elearn::get_nbre_courplan(intval($value['id_matiere_matiere']), $tabdata_user->id_type));
            //var_dump('get_nbre_courplan <pre>',$get_nbre_courplan,'</pre>' );
            if ($get_nbre_courplan == 0) {
                ($get_prof_prochainmat[$key])["progression"]=0;
            }
            else {
                ($get_prof_prochainmat[$key])["progression"]=( $get_nbre_courplan_nif * 100)/$get_nbre_courplan ;
            }
        }
        $tabdatas['get_prof_prochainmat'] = $get_prof_prochainmat ; 
        foreach ($tabdatas['get_prof_prochainmat'] as $key => $value) {
            $fichier =  '../BanqueDefichiers/matiere/'.intval($value['id_matiere_matiere']).'.jpg';
            if( file_exists ( $fichier)){
                ($tabdatas['get_prof_prochainmat'][ $key])['liens']="/BanqueDefichiers/matiere/".intval($value['id_matiere_matiere']).'.jpg';
            }
        }
        
       
        //var_dump($tabdatas['get_prof_prochainmat']);
        $tabdatas['getAllGroupeANDmat']=Prof::getAllGroupeANDmat($tabdata_user->id_type);
        
        foreach ($tabdatas['getAllGroupeANDmat'] as $key => $value) {
            //var_dump($value);
            $nbre_courplan_nif = Prof_elearn::get_nbre_courplan_nif($value['id_mat'], $value['groupe_id'], $value['id_prof']);
            //var_dump('nbre_courplan_nif' ,$nbre_courplan_nif);
            $nbre_courplan = Prof_elearn::get_nbre_courplan($value['id_mat'], $value['id_prof']);
            //var_dump('get_nbre_courplan' ,$get_nbre_courplan);
            if ($nbre_courplan==0) {
                $pourcentage = 0;
            }
            else {
                $pourcentage = $nbre_courplan_nif*100/$nbre_courplan;
            }
            ($tabdatas['getAllGroupeANDmat'][$key])['pourcentage']=$pourcentage;
        }
        //var_dump($tabdatas['getAllGroupeANDmat']);

        $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);

        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);
        
        View::renderTemplate('Elearning/Prof/'.$_SESSION['page'].'.html',$tabdatas);
        

    }

    public function elearning_prof_uploadAction(){
        //var_dump( 'http://'.($_SERVER['HTTP_HOST']).dirname($_SERVER['SCRIPT_NAME'], 2).'/files'.'/' );

        $tabdatas = Crt_E_prof::infosuser();
        $tabdata_user = (object)$tabdatas ;

        $tabdatas['get_matEnseigners']=Prof_elearn::get_proMatEnseigner($tabdata_user->id_type);
        //var_dump( $tabdatas['get_matEnseigners']);

        /**
            * INFORMATIONS A RENVOYER A LA PAGE
        */
        
        $tabdatas['toast_notif']['etat'] = "success";
        $tabdatas['toast_notif']['infos'] = "Acces a mes cours";

        $tabdatas['menu']="Elearning";
        $tabdatas['sousmenu']="Mes Cours";
        $tabdatas['univInfos']=Admin::getUnivInfos();
        if ( isset($_GET['action']) && $_GET['action'] == "getcoursdocs" && $_GET['id_prof'] != "" && $_GET['id_matiere'] !="" ) {

            $id_pers_prof = intval($tabdata_user->id_type);
            $id_matiere = intval(htmlspecialchars($_GET['id_matiere']));
            $matiere_select= htmlspecialchars($_GET['matiere_select']);
            $tabdatas['mat_select'] = $id_matiere;
            $tabdatas['profpers_select'] = $id_pers_prof;
            $tabdatas['matiere_select'] = $matiere_select;

            $dossier = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['SCRIPT_NAME' ],2).'/BanqueDefichiers/COURS/'.$id_pers_prof.'/'.$id_matiere.'/';

            //var_dump($dossier);
            $tabdatas['get_profmatiere_video'] = Prof_elearn::get_profmatiere_video($id_matiere, $id_pers_prof);
            //var_dump($tabdatas['get_profmatiere_video']);

            if(is_dir($dossier)){

                $liens = $tabdatas['base_liens'].'BanqueDefichiers/COURS/'.$id_pers_prof.'/'.$id_matiere.'/';
                $files_get = Upload_files::get_dossierContenu($dossier, $liens);
                // var_dump($files_get );
                $tabdatas['files_get'] = $files_get;


            }
            else{

            }
            //exit;

            //BanqueDefichiers/COURS/IDPROF/IDMATIERE



        } 

        $date_jour = date("Y-m-d");
        $tabdatas['get_prof_prochainmat'] = Prof_elearn::get_prof_prochainmat($tabdata_user->id_type, $date_jour );

        //var_dump($tabdatas['get_prof_prochainmat'] );

        //var_dump($tabdatas);
        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);
        
        View::renderTemplate('Elearning/Prof/'.$_SESSION['page'].'.html',$tabdatas);
        

    }

    public function elearning_prof_coursinfosAction(){
        //var_dump( 'http://'.($_SERVER['HTTP_HOST']).dirname($_SERVER['SCRIPT_NAME'], 2).'/files'.'/' );
        //var_dump($_POST);
        $tabdatas = Crt_E_prof::infosuser();
        $tabdata_user = (object)$tabdatas ;
        $id_pers_prof = intval($tabdata_user->id_type);
        //var_dump( $tabdatas);
        $tabdatas['menu']="Elearning";
        $tabdatas['univInfos']=Admin::getUnivInfos();

        if ( isset($_GET['action']) && isset($_GET['id_matiere']) && isset($_GET['matiere_select']) && $_GET['action'] == "getcoursdocs"  && $_GET['id_matiere'] !="" && $_GET['id_matiere'] !=0 ) {

            $id_matiere = intval(htmlspecialchars($_GET['id_matiere']));
            $matiere_select= htmlspecialchars($_GET['matiere_select']);

            if ( isset($_GET['groupeid']) && isset($_GET['groupelibelle']) ) {
                $tabdatas['groupeid'] = intval(htmlspecialchars($_GET['groupeid']));
                $tabdatas['groupelibelle'] = (htmlspecialchars($_GET['groupelibelle']));
                $tabdatas['liste_grpeeleves'] = Prof::getAll_elvDSgrp($tabdatas['groupeid']);
                //var_dump($tabdatas['liste_grpeeleves']);
            }

            
            if ( isset($_GET['datejr']) && isset($_GET['datedebut']) && isset($_GET['datefin']) ) {
                $tabdatas['datejr'] = (htmlspecialchars($_GET['datejr']));
                $tabdatas['datedebut'] = (htmlspecialchars($_GET['datedebut']));
                $tabdatas['datefin'] = (htmlspecialchars($_GET['datefin']));
            }


            if ( isset($_POST['btn_creer_plan']) ) {


                //var_dump($_POST);
                //var_dump($_GET);
                $input_m_titre = (htmlspecialchars($_POST['input_m_titre']));
                $input_m_position = (htmlspecialchars($_POST['input_m_position']));
                
                //var_dump($input_m_position);
                $input_m_idparen = intval(htmlspecialchars($_POST['input_m_parentitre']));
                if ( $_POST['textarea_m_desc'] ==  '<p>Saisissez la description ici.</p>' ) {
                    $textarea_m_desc = "";
                }
                else {
                    $textarea_m_desc = (htmlspecialchars($_POST['textarea_m_desc']));
    
                }

                $plan_position_num = Prof_elearn::get_courplan_lastnum($id_pers_prof, $id_matiere ,$input_m_position);

                //var_dump($plan_position_num);exit;

                $plan_position_num= intval( $plan_position_num ) + 1;
    
                $tabdatas['set_cour_plan'] = Prof_elearn::set_cour_plan($id_pers_prof, $id_matiere, $input_m_idparen, $input_m_titre , $textarea_m_desc , $input_m_position, $plan_position_num);

                //var_dump( $tabdatas['set_cour_plan']  );
                if ($tabdatas['set_cour_plan']  = 'success') {
                    $tabdatas['toast_notif']['etat'] = "success";
                    $tabdatas['toast_notif']['infos'] = "Création de la ".$input_m_position."  ".$input_m_titre." éffectué avec succes";
                }
                else {
                    $tabdatas['toast_notif']['etat'] = "danger";
                    $tabdatas['toast_notif']['infos'] = "Erreur dans la Création de la ".$input_m_position."  ".$input_m_titre;
                }

    
            }

            if (isset($_POST['input_mod_titre']) && isset($_POST['mod_idcourplan']) && isset($_POST['textarea_mod_desc'])) {
                
                $input_mod_titre= htmlspecialchars($_POST['input_mod_titre']);
                $mod_idcourplan=intval(htmlspecialchars($_POST['mod_idcourplan']));
                $textarea_mod_desc=htmlspecialchars($_POST['textarea_mod_desc']);
                $table="cours_plan";
                $tb_infos=[];
                $tb_conditions=[];
                $tb_conditions["id_cours_plan"]=$mod_idcourplan;
                $tb_infos["plan_titre"]=$input_mod_titre;
                $tb_infos["plan_description"]=$textarea_mod_desc;
                Model_public::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
                
                $tabdatas['toast_notif']['etat'] = "success";
                $tabdatas['toast_notif']['infos'] = "Modification du cour éffectuée";

            }

            $tabdatas['get_cour_section'] = Prof_elearn::get_cour_section($id_pers_prof, $id_matiere );
            //var_dump( $tabdatas['get_cour_section'] );
            $tabdatas['get_cour_partie'] = Prof_elearn::get_cour_partie($id_pers_prof, $id_matiere );

            foreach ($tabdatas['get_cour_section'] as $key => $value) {

                ( $tabdatas['get_cour_section'][ $key])['plan_description'] = html_entity_decode( $value['plan_description']);
            
                $sa_courplan_id = intval( ($tabdatas['get_cour_section'][ $key])['id_cours_plan'] );

                if ( isset($_GET['groupeid']) && $_GET['groupeid']!=0 && $_GET['groupeid'] !="" ) {
                    $get_suivi_active_courplan = Prof_elearn::get_suivi_active_courplan($sa_courplan_id,$tabdatas['groupeid']);
                    //var_dump('cour plan '.$sa_courplan_id,$get_suivi_active_courplan );
                    if (empty($get_suivi_active_courplan)) {
                        ( $tabdatas['get_cour_section'][ $key])['vue_groupe'] =0;
                        ( $tabdatas['get_cour_section'][ $key])['etat_progression'] =0;
                    }
                    else{
                        ( $tabdatas['get_cour_section'][ $key])['vue_groupe'] =intval($get_suivi_active_courplan[0]["sa_etatvue_groupe"]);
                        ( $tabdatas['get_cour_section'][ $key])['etat_progression'] =intval($get_suivi_active_courplan[0]["sa_progression"]);
                    }
                }
                
            }

            foreach ($tabdatas['get_cour_partie'] as $key => $value) {

                ( $tabdatas['get_cour_partie'][ $key])['plan_description'] = html_entity_decode( $value['plan_description']);

                $sa_courplan_id = intval( ($tabdatas['get_cour_partie'][ $key])['id_cours_plan'] );

                if ( isset($_GET['groupeid']) && $_GET['groupeid']!=0 && $_GET['groupeid'] !="" ) {
                    $get_suivi_active_courplan = Prof_elearn::get_suivi_active_courplan($sa_courplan_id,$tabdatas['groupeid']);
                    //var_dump('cour plan '.$sa_courplan_id,$get_suivi_active_courplan );
                    if (empty($get_suivi_active_courplan)) {
                        ( $tabdatas['get_cour_partie'][ $key])['vue_groupe'] =0;
                        ( $tabdatas['get_cour_partie'][ $key])['etat_progression'] =0;
                    }
                    else{
                        ( $tabdatas['get_cour_partie'][ $key])['vue_groupe'] =intval($get_suivi_active_courplan[0]["sa_etatvue_groupe"]);
                        ( $tabdatas['get_cour_partie'][ $key])['etat_progression'] =intval($get_suivi_active_courplan[0]["sa_progression"]);
                    }
                }
            }
            //exit;
            //var_dump( $tabdatas['get_cour_section'] );

            //var_dump( $tabdatas['get_cour_partie'] );
            $tabdatas['get_matiere_docsvideo'] = Prof_elearn::get_matiere_docsvideo($id_matiere, $id_pers_prof);
            $tabdatas['liens_profmat_docs'] = $tabdatas['base_liens'].'BanqueDefichiers/COURS/'.$id_pers_prof.'/'.$id_matiere.'/';
            //var_dump($tabdatas['get_matiere_docsvideo']);
            //var_dump($tabdatas['liens_profmat_docs']);

        }
        else {header('Refresh: 1;URL=index.php?p=elearning_prof');}

        //$tabdatas['get_matEnseigners']=Prof_elearn::get_proMatEnseigner($id_pers_prof);
        //var_dump( $tabdatas['get_matEnseigners']);
        //**  INFORMATIONS A RENVOYER A LA PAGE *
        $tabdatas['sousmenu']="Mes Cours > Suivi cours > ".$matiere_select;
        $tabdatas['mat_select'] = $id_matiere;
        $tabdatas['profpers_select'] = $id_pers_prof;
        $tabdatas['matiere_select'] = $matiere_select;
        $tabdatas['idtype_prof'] = intval($tabdata_user->id_pers_personne);
        //$tabdatas['test_html'] = '<strong>Bonjour</strong> elvis eudes ';
        
        if (isset($_POST['visio_conf_jitsi']) && ($_POST['visio_conf_jitsi']=="jitsi")) {
            $_SESSION['page']= "elearning_prof_jitsi" ;
            //var_dump($_GET);
            //var_dump($tabdatas['matiere_select']);
            //var_dump($tabdatas);
        }

        //var_dump($tabdatas['get_prof_prochainmat'] );

        //var_dump($tabdatas);
        $_POST=null;
        $_GET=null;
        unset($_POST);
        unset($_GET);
        //echo '/<pre>';
            
        View::renderTemplate('Elearning/Prof/'.$_SESSION['page'].'.html', $tabdatas);
        

    }


    public function elearning_classeforumAction(){
        $tabdatas = Crt_E_prof::infosuser();
        $tabdata_user = (object)$tabdatas ;
        //var_dump($tabdata_user->base_liens."public/classe_chat");exit;
        //View::renderTemplate("../../../public/classe_chat");

        header('Refresh: 1;URL='.$tabdata_user->base_liens.'public/classe_chat');
        exit;
    }

}
