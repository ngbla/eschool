<?php
namespace App\Controllers;

require_once('../App/Models/User.php');
require_once('../App/Models/Log.php');
require_once('../App/Models/Admin.php');
require_once('../App/Models/Prof.php');
require_once('../App/Models/User_Img.php');
require_once('../App/Models/Model_public.php');

use \Core\View;
use App\Config;
use App\Models\Log as modeldb;
use App\Models\User ;
use App\Models\Admin;
use App\Models\Prof;
use App\Models\User_Img;
use App\Models\Model_public;
use App\Models\Upload_files;


/*
* SQL recuperer une bd 
SELECT * FROM INFORMATION_SCHEMA.TABLES
*/
/**
 * Home controller
 *
 * PHP version 7.0
 */
class Crt_prof extends \Core\Controller
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
       
        $get_all_user_notif = Model_public::get_all_user_notif($tabdata_user->type_pers,$tabdata_user->id_type);

        $get_user_annee = Model_public::get_user_annee($tabdata_user->id_pers_personne);
 

        $tabdatas = [
            'user_allnotif_defil'  =>  $get_all_user_notif,
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

    public function accueilAction(){

        $_SESSION['page'] ="prof_accueil" ;

        $tabdatas = Crt_prof::infosuser();
        $tabdata_user = (object)$tabdatas ;
        //var_dump($tabdatas);
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        $fct_exec="|| ";
        $tabdatas['notif_file'] = [];
        $id_notif_tab = [];

        $fct_exec = "||";
        //var_dump( $tabdatas);
        //var_dump($_SERVER);

        //var_dump($_GET,$_POST);exit();
        $get_user_conex = Model_public::get_user_conex($id_pers , $fk_iduniv);
        //var_dump($get_user_conex);exit;
        
        $verifprof = sha1(date("d/m/Y"));
        if (isset( $_GET['verif'] ) && $_GET['verif']==$verifprof &&  $get_user_conex==0) {
            $get_user_conex = 1;
        }

        if ( $get_user_conex == 0 ) {
            $table="connexion";
            $tb_infos=[];
            $tb_conditions=[];
            $tb_conditions["conex_id_personne"]=$id_pers;
            $tb_conditions["fk_iduniv"]=$fk_iduniv;
            $tb_infos["conex_etat"]=0;
            Model_public::set_updateSQL_ALL_by($table , $tb_infos , $tb_conditions);
            if (!empty($_COOKIE['login'])) {
                setcookie('login', "", time() - 3600);
                setcookie('pass', "", time() - 3600);
                setcookie('type', "", time() - 3600);
                setcookie('id_univ', "", time() - 3600);
                setcookie('user', "", time() - 3600);
                setcookie('avatar', "", time() - 3600);
                setcookie(session_id(), "", time() - 3600);
            }
            session_destroy();
            session_write_close();
            View::renderTemplate('Home/index.html',$tabdatas);
        }
        else{

            //********************************** */
            
            if (isset($_POST['btn_send_photo'] )) {
                //var_dump($_POST );//exit();
                $tabdatas['sendImg_etat']=User_Img::sendImg();
                $tabdatas['lien_photo']= $tabdatas['liens'].$_SESSION['user']['id_pers_personne'].'/'.$_SESSION['user']['id_pers_personne'].'.jpg';
            }

            $tabdatas['get_userNotifs']=User::get_userNotifs($tabdata_user->id_type, 2);
            if ( $tabdatas['get_userNotifs'] == 0) {
                unset($tabdatas['get_userNotifs']);
            }
            else {
                foreach ($tabdatas['get_userNotifs'] as $key => $value) {
                $id_notif = $value['id_notif'];
                //var_dump($id_notif);

                $upFolder = dirname(__DIR__, 2)."/"."notifications"."/".$value['id_notif'];
                //var_dump( $upFolder);
                $id_notif_tab=array( $id_notif => User_Img::get_dossierContenu( $upFolder,$tabdatas['liens'], $id_notif ));
                array_push($tabdatas['notif_file'],$id_notif_tab);
                }
            }
            //var_dump($tabdatas['notif_file'][0]);
            //var_dump($tabdatas['liens']);


            $_POST=NULL;
            $_GET=NULL;
            unset($_POST);
            unset($_GET);
            $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
            $tabdatas['nbreAllGrpe']=Prof::getNbreProfGrpe($tabdata_user->id_type);
            $tabdatas['nbreAllMatiere']=Prof::getNbreProfMatriere($tabdata_user->id_type);
            $tabdatas['allEmploiTps']=Prof::getProfEmploiTpsBy($tabdata_user->id_type);
            $tabdatas['allGrpEval']=Prof::getProfGrpEvalBy($tabdata_user->id_type);
            
            /*:::::::DEBUT Enregistrement des logs::::::::::*/
            $info = "Crt_prof ::: etudsuiviAction => " . $fct_exec;
            $log_user ="Espace suivie Etudiant (liste et saisie absence)";
            modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
            //:::::::::::::LOGS::::::::::::::::::
            /*:::::::Fin Enregistrement des logs::::::::::*/
            //var_dump( $tabdatas['allGrpEval']);exit;
            View::renderTemplate('Accueil/prof/'.$_SESSION['page'].'.html',$tabdatas);
            //********************************** */
        }

    }

    public function evaluationAction(){

        $tabdatas = Crt_prof::infosuser();
        $tabdata_user = (object)$tabdatas ;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        $fct_exec="|| ";
        $_SESSION['page'] = 'prof_evaluation';
        $tabdatas['menu']="Gesion des Classes";
        $tabdatas['sousmenu']="Evaluations";
        $id_prof=intval($tabdatas['id_type']);
        
        $tabdatas['tab']=1;

        //var_dump($_POST);
        //var_dump($_POST);
        $tabdatas['allProf_classe']=Prof::getProfGrpe_NoDoublons($tabdata_user->id_type);

        if (isset($_POST['btn_envoi_eval']) && $_POST['btn_envoi_eval'] != "") {
            $id_profeval = intval(htmlspecialchars($_POST['btn_envoi_eval']));
            $tabdatas['update_profeval']=Prof::update_profeval($tabdata_user->id_type, $id_profeval, 1);
            $tabdatas['toast_notif']['etat'] = $tabdatas['update_profeval'];
            $tabdatas['toast_notif']['infos'] = "Activation d'Evaluation ";
        }
        
        if ( isset($_POST['prof_eval_btnsubmit']) &&  isset($_POST['prof_eval_mat']) &&   isset($_POST['prof_eval_groupe']) &&    $_POST['prof_eval_groupe']!=0 && $_POST['prof_eval_mat']!=0  && $_POST['prof_eval_libelle']!="" ) {


            $prof_eval_date =  (htmlspecialchars($_POST['prof_eval_date']));
            //echo $prof_eval_date. "<br>";
            $prof_eval_date =  strtotime($prof_eval_date);
            //echo $prof_eval_date. "<br>";
            $prof_eval_date =  date("d/m/Y",  $prof_eval_date) ;
            //var_dump($_POST);exit;

            $id_grpe = intval(htmlspecialchars($_POST['prof_eval_groupe']));
            $id_mat = intval(htmlspecialchars($_POST['prof_eval_mat']));
            $id_periode = intval(htmlspecialchars($_POST['prof_eval_periode']));

            $id_session = intval(htmlspecialchars($_POST['prof_eval_session']));
            $eval_lib= htmlspecialchars($_POST['prof_eval_libelle']);
            $eval_desc= htmlspecialchars($_POST['prof_eval_desc']);
            $eval_type= htmlspecialchars($_POST['prof_eval_select_type']);

            $eval_date= $prof_eval_date;
            $eval_hDebut= htmlspecialchars($_POST['prof_eval_hDebut']);
            $eval_hFin= htmlspecialchars($_POST['prof_eval_hFin']);
            $coef = floatval(htmlspecialchars($_POST['coef']));
            $notation = intval(htmlspecialchars($_POST['notation']));

            $tabdatas['prof_addevaluation']=Prof::setProfGrpEvalNEWS($id_grpe ,$id_mat ,$eval_lib ,$eval_desc,$id_session,$eval_type, $eval_date,$eval_hDebut ,$eval_hFin, $id_periode ,$coef,$notation);

            if (isset($_POST['prof_eval_groupe_multiple'])) {
                $prof_eval_groupe_multiple= $_POST['prof_eval_groupe_multiple'];
            }
            else {$prof_eval_groupe_multiple=[]; }
            
           
            //var_dump($prof_eval_groupe_multiple);
            if (!empty($prof_eval_groupe_multiple)) {
                foreach ($prof_eval_groupe_multiple as $key => $value) {
                    $id_grpe = intval(htmlspecialchars($value));
                    $tabdatas['prof_addevaluation'] = Prof::setProfGrpEvalNEWS($id_grpe ,$id_mat ,$eval_lib ,$eval_desc,$id_session,$eval_type, $eval_date,$eval_hDebut ,$eval_hFin, $id_periode ,$coef,$notation);
                }
            }

            if (  $tabdatas['prof_addevaluation']== 'ajouter') {
                $tabdatas['toast_notif']['etat'] = "success";
                $tabdatas['toast_notif']['infos'] = "Evaluation : ".$eval_lib." Creer";
            }
            else {
                $tabdatas['toast_notif']['etat'] = "danger";
                $tabdatas['toast_notif']['infos'] = "Erreur lors de la création de l'évaluation :".$eval_lib;
            }

        }

        if (isset($_POST['btn_edit_eval']) && $_POST['btn_edit_eval'] != "") {
            $id_eval_seclect = intval($_POST['btn_edit_eval']);
            $tabdatas['get_Eval_by']=Prof::get_Eval_by($id_eval_seclect);

            $tabdatas['tab']=1;
            //var_dump($_POST);var_dump($tabdatas['get_Eval_by']);//exit;
        }

        if (isset($_POST['prof_eval_btnmodif']) &&  isset($_POST['prof_eval_periode']) &&  isset($_POST['prof_eval_mat'])  &&   isset($_POST['prof_eval_groupe']) &&  $_POST['prof_eval_groupe']!=0 && $_POST['prof_eval_mat']!=0  && $_POST['prof_eval_libelle']!="" ) {
            //exit();
            $id_eval_maj = intval($_POST['prof_eval_btnmodif']);
          
      
            $table= 'prof_eval';
            $tb_infos=[];
            $tb_conditions=[];
            $tb_infos['fk_idpartAneeScol']= $_POST['prof_eval_periode'];
            $tb_infos['id_groupe']= $_POST['prof_eval_groupe'];
            $tb_infos['id_mat']= $_POST['prof_eval_mat'];
            $tb_infos['eval_libelle']= $_POST['prof_eval_libelle'];
            $tb_infos['eval_desc']= $_POST['prof_eval_desc'];
            $tb_infos['eval_session']= $_POST['prof_eval_session'];
            $tb_infos['eval_type']= $_POST['prof_eval_select_type'];
            $tb_conditions['prof_eval_id']= $id_eval_maj;
            //var_dump($_POST);exit;
            $tabdatas['maj_prof_eval']=Model_public::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
             
            $tb_infos=[];
            $tb_conditions=[];
            $table= 'prof_eval_emploitps';
            $tb_infos['eval_date']= $_POST['prof_eval_date'];
            $tb_infos['eval_hDebut']= $_POST['prof_eval_hDebut'];
            $tb_infos['eval_hFin']= $_POST['prof_eval_hFin'];
            $tb_infos['coef']= intval(htmlspecialchars($_POST['coef']));
            $tb_infos['notation']= intval(htmlspecialchars($_POST['notation']));
            
            $tb_conditions['eval_id']= $id_eval_maj;
            $tabdatas['maj_prof_eval_emploitps']=Model_public::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);

            if (intval($tabdatas['maj_prof_eval_emploitps']*$tabdatas['maj_prof_eval'])==1) {
                $tabdatas['toast_notif']['etat'] = "success";
                $tabdatas['toast_notif']['infos'] = "Modification éffectue";
            }
            else {
                $tabdatas['toast_notif']['etat'] = "danger";
                $tabdatas['toast_notif']['infos'] = "Erreur lors de la Modification";
            }

            
            //var_dump($_POST,$tabdatas['maj_prof_eval']);//exit;
        }

        $tabdatas['allProf_eval']=Prof::getProfGrpEval_enAttente($id_prof);
        
        $tabdatas['allSession']=Prof::getSession();
         
        //var_dump($tabdatas['allProf_eval']);
        $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_prof ::: etudsuiviAction => " . $fct_exec;
		$log_user ="Espace suivie Etudiant (liste et saisie absence)";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/
        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);

        View::renderTemplate('Accueil/prof/'.$_SESSION['page'].'.html',$tabdatas);

    }
    public function saisi_noteAction(){

        $tabdatas = Crt_prof::infosuser();
        $tabdata_user = (object)$tabdatas ;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        $fct_exec="|| ";
        $tabdatas['menu']="Gesion des Classes";
        $tabdatas['sousmenu']="Evaluations";
        //var_dump($_POST);
        $tabdatas['allProf_classe']=Prof::getProfGrpe_NoDoublons($tabdata_user->id_type);
        if (isset($_POST['btn_sup_eval'])) {
            $tab_info =explode('_',$_POST['btn_sup_eval']);
            //var_dump($tab_info);
            if ($tab_info[0]=='sup') {
                $tabdatas['allProf_classe']=Prof::del_evalBy($tab_info[1]);
            }
        }
        /*
            if ( isset($_POST['prof_eval_btnsubmit']) && $_POST['prof_eval_groupe']!=0 && $_POST['prof_eval_mat']!=0  && $_POST['prof_eval_libelle']!="" ) {
                //var_dump($_POST);
                $id_grpe = intval(htmlspecialchars($_POST['prof_eval_groupe']));
                $id_mat = intval(htmlspecialchars($_POST['prof_eval_mat']));
                $id_session = intval(htmlspecialchars($_POST['prof_eval_session']));
                $eval_lib= htmlspecialchars($_POST['prof_eval_libelle']);
                $eval_desc= htmlspecialchars($_POST['prof_eval_desc']);

                $tabdatas['prof_addevaluation']=Prof::setProfGrpEval($id_grpe ,$id_mat ,$eval_lib ,$eval_desc,$id_session);

            }
        */
        $tabdatas['allProf_eval']=Prof::getProfGrpEval();
        $tabdatas['allSession']=Prof::getSession();
        
        //var_dump($tabdatas['allProf_eval']);
        
        $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_prof ::: etudsuiviAction => " . $fct_exec;
		$log_user ="Espace suivie Etudiant (liste et saisie absence)";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/
        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);
        View::renderTemplate('Accueil/prof/'.$_SESSION['page'].'.html',$tabdatas);

    }
    
    public function evaluationInfoAction(){

        $tabdatas = Crt_prof::infosuser();
        $tabdata_user = (object)$tabdatas ;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $fct_exec="|| ";
        $id_pers = $tabdata_user->id_pers_personne;
        $type = explode("_", htmlspecialchars($_POST['btn_voir_eval']));
        $id_eval = intval($type[1]);
        //var_dump("id_eval = ",$id_eval);//exit();
        //var_dump($tabdatas);


        $tabdatas['menu']="Gesion des Classes";
        $tabdatas['sousmenu']="Evaluations > Information & Notes";

        $tabdatas['prof_eval_date']=Prof::getProfGrpEvalWithDateBy($id_eval);
        $tabdatas['prof_eval_salle']=Prof::getEvalSalle($id_eval);

        

        $prof_eval_grpEleve_tmp=Prof::getAllEleveByGroup($id_eval);
        //var_dump($prof_eval_grpEleve_tmp);
        //unset($tabdatas['prof_eval_grpEleve']);
        $tabdatas['prof_eval_grpEleve']=[];

       
        //var_dump($tabdatas['prof_eval_date']);
        //var_dump($tabdatas['prof_eval_date']);
        //$tabdatas['prof_eval_grpEleve'] = [];

        foreach ( $prof_eval_grpEleve_tmp as $key => $value) {

            //$prof_eval_grpEleve_tmp[$key].id_eleve_eleve;
            //var_dump( $prof_eval_grpEleve_tmp[$key]['id_eleve_eleve'] );
            $reps = Prof::setGetInitEleveEvalNote(intval( $prof_eval_grpEleve_tmp[$key]['id_eleve_eleve'] ), $id_eval);
            //var_dump("reps", $reps );
            array_push($tabdatas['prof_eval_grpEleve'],$reps);

        }
        //var_dump($tabdatas['prof_eval_grpEleve']);


 
        $dossier = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['SCRIPT_NAME' ],2).'/BanqueDefichiers/evaluations/'.$id_eval.'/'. $tabdatas['id_pers_personne'].'/';
        if(is_dir($dossier)){
            $liens = $tabdatas['base_liens'].'BanqueDefichiers/evaluations/'.$id_eval.'/'.$tabdatas['id_pers_personne'].'/';
            $files_get = Upload_files::get_dossierContenu($dossier, $liens);
            $tabdatas['files_get'] = $files_get;
        }
    
            
        $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_prof ::: etudsuiviAction => " . $fct_exec;
		$log_user ="Espace suivie Etudiant (liste et saisie absence)";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/
        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);
        View::renderTemplate('Accueil/prof/prof_eval_info.html',$tabdatas);

    }
    public function prof_eval_eleveuploadAction(){

        $tabdatas = Crt_prof::infosuser();
        $tabdata_user = (object)$tabdatas ;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
         $fct_exec="|| ";
        //var_dump($_GET);
        if (isset($_GET['eval']) && isset($_GET['id_eleve']) ) {
            $id_eval=intval($_GET['eval']);
            $id_eleve=intval($_GET['id_eleve']);
            $tabdatas['get_elevBy']=Model_public::get_elevBy($id_eleve);
            //var_dump($tabdatas['get_elevBy']);

            $tabdatas['get_Eval_by']=Model_public::get_eval_infosBy($id_eval);

            if (!empty($tabdatas['get_Eval_by']) && !empty($tabdatas['get_elevBy'])) {
                $tabdatas['get_Eval_by']=$tabdatas['get_Eval_by'][0];
                $tabdatas['get_elevBy']=$tabdatas['get_elevBy'][0];

                 $dossier = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['SCRIPT_NAME' ],2).'/BanqueDefichiers/evaluations/'.$id_eval.'/'.$tabdatas['get_elevBy']['id_pers_personne'].'/';

                if(is_dir($dossier)){
                    $liens = $tabdatas['base_liens'].'BanqueDefichiers/evaluations/'.$id_eval.'/'.$tabdatas['get_elevBy']['id_pers_personne'].'/';
                    $files_get = Upload_files::get_dossierContenu($dossier, $liens);
                    // var_dump($files_get );
                    $tabdatas['files_get'] = $files_get;
                }
            }

        }
 

        $tabdatas['menu']="Gesion des Classes";
        $tabdatas['sousmenu']="Evaluations > Fichiers étudiant";

    
        
    
 
    
            
        $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_prof ::: etudsuiviAction => " . $fct_exec;
		$log_user ="Espace suivie Etudiant (liste et saisie absence)";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/
        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);
        View::renderTemplate('Accueil/prof/prof_eval_eleveupload.html',$tabdatas);

    }

    public function prof_eval_enligneAction(){

        $tabdatas = Crt_prof::infosuser();
        $tabdata_user = (object)$tabdatas ;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        $fct_exec="|| ";

        $table="reponse_eval_etat"; 
        $tb_conditions=[];
        $tb_conditions["fk_ideval"]=intval(htmlspecialchars($_GET['eval']));
        $tb_conditions["fk_idelev"]=intval(htmlspecialchars($_GET['id_eleve']));
        $etat_copie=Model_public::get_selectSQL_ALL_by($table, $tb_conditions);
        //var_dump($etat_copie);

        if (empty($etat_copie) || $etat_copie==0 || $etat_copie =='') {
            $tabdatas['etat_copie']='non';

            $table="questions"; 
            $tb_conditions=[];
            $tb_conditions["id_eval"]=intval(htmlspecialchars($_GET['eval']));
            $etat_questions=Model_public::get_selectSQL_ALL_by($table, $tb_conditions);
            //var_dump($etat_questions);

            foreach ($etat_questions as $key => $value) {
                $etat_questions[$key]["question"] = htmlspecialchars_decode($value["question"]);
            }

            $tabdatas['etat_questions']=$etat_questions;
        }
        else {
            $tabdatas['etat_copie']=$etat_copie;

            $table="questions"; 
            $tb_conditions=[];
            $tb_conditions["id_eval"]=intval(htmlspecialchars($_GET['eval']));
            $etat_questions=Model_public::get_selectSQL_ALL_by($table, $tb_conditions);
            //var_dump($etat_questions);

         
            $table="reponse_quiz"; 

            foreach ($etat_questions as $key => $value) {
                $etat_questions[$key]["question"] = htmlspecialchars_decode($value["question"]);
                            
                $tb_conditions=[];
                $tb_conditions["id_eval"]=intval(htmlspecialchars($_GET['eval']));
                $tb_conditions["id_eleve"]=intval(htmlspecialchars($_GET['id_eleve']));
                $tb_conditions["id_question"]=intval($value['question_number']);
                $etat_reponses=Model_public::get_selectSQL_ALL_by($table, $tb_conditions);  
                //var_dump($etat_reponses); 
                $etat_questions[$key]["reps"] = [];
                foreach ($etat_reponses as $cle => $reps) {
                    array_push($etat_questions[$key]["reps"], array($reps["dateajout"],htmlspecialchars_decode($reps["choice"])));
                }
            }
            $tabdatas['etat_questions']=$etat_questions;

        }

       
        //var_dump($tabdatas['etat_questions']);


        if (isset($_GET['eval']) && isset($_GET['id_eleve']) ) {
            $id_eval=intval($_GET['eval']);
            $id_eleve=intval($_GET['id_eleve']);
            $tabdatas['get_elevBy']=Model_public::get_elevBy($id_eleve);
            //var_dump($tabdatas['get_elevBy']);

            $tabdatas['get_Eval_by']=Model_public::get_eval_infosBy($id_eval);

            if (!empty($tabdatas['get_Eval_by']) && !empty($tabdatas['get_elevBy'])) {
                $tabdatas['get_Eval_by']=$tabdatas['get_Eval_by'][0];
                $tabdatas['get_elevBy']=$tabdatas['get_elevBy'][0];

                 $dossier = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['SCRIPT_NAME' ],2).'/BanqueDefichiers/evaluations/'.$id_eval.'/'.$tabdatas['get_elevBy']['id_pers_personne'].'/';

                if(is_dir($dossier)){
                    $liens = $tabdatas['base_liens'].'BanqueDefichiers/evaluations/'.$id_eval.'/'.$tabdatas['get_elevBy']['id_pers_personne'].'/';
                    $files_get = Upload_files::get_dossierContenu($dossier, $liens);
                    // var_dump($files_get );
                    $tabdatas['files_get'] = $files_get;
                }
            }

        }


 
        $tabdatas['menu']="Gesion des Classes";
        $tabdatas['sousmenu']="Evaluations > Copie étudiant";

        $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);

        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_prof ::: prof_eval_enligneAction => " . $fct_exec;
		$log_user =$tabdatas['menu'].' > '.$tabdatas['sousmenu'];
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/
        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);
        View::renderTemplate('Accueil/prof/'.$_SESSION['page'].'.html',$tabdatas);

    }
    
    public function moyAction(){

        
        $tabdatas = Crt_prof::infosuser();
        $tabdata_user = (object)$tabdatas ;
                
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;   
        $id_prof = $tabdata_user->id_type;   
        $fct_exec = "||";

        $tabdatas['menu']="Gestion des Classes";
        $tabdatas['sousmenu']="Moyennes";


        if ( isset($_POST['input_eleve_matmoy'])  ) {

            $variable = $_POST['input_eleve_matmoy'];

            foreach ($variable as $key => $value) {

                $infomoyset = explode("_",  $value );
                $moy_id_groupe = $infomoyset[0] ;
                $moy_id_eleve_eleve = $infomoyset[1] ;
                $moy_id_matiere_matiere = $infomoyset[2] ;
                $moy_id_prof = $infomoyset[3] ;
                $moy_id_session_session = $infomoyset[4] ;
                $moy_moyenne = $infomoyset[5] ;
                $fk_part_annee = $infomoyset[6] ;

                $tabdatas['setEleve_annee_moyBY']= Prof::setEleve_annee_moyBY($moy_id_groupe, $moy_id_eleve_eleve , $moy_id_matiere_matiere, $moy_id_prof, $moy_id_session_session, $moy_moyenne, $fk_part_annee);

                //var_dump($tabdatas['setEleve_annee_moyBY']);
            }

            
        }

        //var_dump($tabdata );
        //exit();
        if ( isset($_POST['btn_voir_classe_notes']) && $_POST['btn_voir_classe_notes'] != "" ) {
            
            //var_dump($_POST);
            $tabdatas['btn_voir_classe_notes']=htmlspecialchars($_POST['btn_voir_classe_notes']);

            $type = explode("_", htmlspecialchars($_POST['btn_voir_classe_notes']));
            $id_groupe = intval($type[0]) ;
            $id_mat = intval($type[1]);
            $tabdatas['get_mat_grp_name']= Prof::get_mat_grp_name($id_groupe, $id_mat);

            if ( isset($_POST['profmoy_select_sess']) && isset($_POST['profmoy_select_periode'])) {
                $id_session = intval(htmlspecialchars($_POST['profmoy_select_sess']));
                $id_periode = intval(htmlspecialchars($_POST['profmoy_select_periode']));
                $table='annee_partie';
                $tb_conditions=[];
                $tb_conditions['id_annee_partie']=$id_periode;
                $tabdatas['info_annee_partie']= Model_public::get_selectSQL_ALL_by($table, $tb_conditions);
                //var_dump($tabdatas['info_annee_partie']);

                $tabdatas['profmoy_select_sess']=htmlspecialchars($_POST['profmoy_select_sess']);
                $tabdatas['profmoy_select_periode']=htmlspecialchars($_POST['profmoy_select_periode']);

                $tabdatas['getAll_grpEvalBymat']= Prof::get_grpEvalBy($id_prof,$id_groupe, $id_mat, $id_session, $id_periode);
                //var_dump( $tabdatas['getAll_grpEvalBymat'] );

                if (!empty($tabdatas['getAll_grpEvalBymat'])) {
                    $etatmoy_grp = (($tabdatas['getAll_grpEvalBymat'][0])['id_groupe']);
                    $etatmoy_mat = (($tabdatas['getAll_grpEvalBymat'][0])['id_mat']);
                    $etatmoy_prof = (($tabdatas['getAll_grpEvalBymat'][0])['id_prof']);
                    $etatmoy_sess = (($tabdatas['getAll_grpEvalBymat'][0])['id_session_session']);
                    $tabdatas['getetat_envoimoy']= Prof::get_moy_etat_envoi($etatmoy_grp, $etatmoy_mat, $etatmoy_prof, $etatmoy_sess, $id_periode);
                }
                $tabdatas['get_notes_prof_eval_grpBY']=Prof::get_grpe_notesBy($id_groupe , $id_mat, $id_session, $id_prof, $id_periode);

            }




            $tabdatas['btn_voir_classe_notes']= htmlspecialchars($_POST['btn_voir_classe_notes']);
            $tabdatas['getAll_elvDSgrp']= Prof::getAll_elvDSgrp($id_groupe);
            $tabdatas['getSession']= Prof::getSession();
            $tabdatas['getmat_periode']= Prof::get_mat_anneePart($id_groupe, $id_mat);
            
            //SELECT etat_moy FROM moyenne WHERE id_groupe = 1 AND id_matiere = 28 and id_prof = 2 AND id_session = 1 GROUP BY id_groupe

            $_SESSION['page'] = "prof_moy_notes";
            $tabdatas['sousmenu']="Moyennes > Classes";

            //var_dump("get_notes_prof_eval_grpBY" ,$tabdatas['get_notes_prof_eval_grpBY']);
            //var_dump("getAll_elvDSgrp", $tabdatas['getAll_elvDSgrp']);
            //var_dump("getAll_grpEvalBymat", $tabdatas['getAll_grpEvalBymat']);
            //var_dump("getetat_envoimoy", $tabdatas['getetat_envoimoy']);
            //var_dump("getSession", $tabdatas['getSession']);
            
        }
        else {

            $_POST=NULL;
            $_GET=NULL;
            unset($_POST);
            unset($_GET);
        }
   
        $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
        $tabdatas['getAllGroupeANDmat']=Prof::getAllGroupeANDmat(intval($tabdata_user->id_type));

        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $fct_exec = $fct_exec.$tabdatas['menu'].' -> '.$tabdatas['sousmenu'].' ||';
        $info = "Crt_prof ::: moyAction => " . $fct_exec;
		$log_user =" Page Vérification et envoie des moyennes ";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/
        


        //var_dump( $tabdatas['getAllGroupeANDmat']);//exit;
        View::renderTemplate('Accueil/prof/'.$_SESSION['page'].'.html',$tabdatas);
        

    }

    public function etudsuiviAction(){

        $tabdatas = Crt_prof::infosuser();
        $tabdata_user = (object)$tabdatas ;

        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;

        $fct_exec = "|| ";

        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);

        
        $tabdatas['menu']="Information Etudiants";
        $tabdatas['sousmenu']="Suivi étudiants";
        $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);

        //var_dump( $tabdatas['allGrpEval']);exit;
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_prof ::: etudsuiviAction => " . $fct_exec;
		$log_user ="Espace suivie Etudiant (liste et saisie absence)";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/

        View::renderTemplate('Accueil/prof/'.$_SESSION['page'].'.html',$tabdatas);
        

    }

    
    public function group_infoAction(){

        $tabdatas = Crt_prof::infosuser();
        $tabdata_user = (object)$tabdatas ;
        //var_dump($tabdata_user);
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        $id_prof = $tabdata_user->id_type;
        $fct_exec="|| ";

        $tabdatas['menu']="Information Etudiants";
        $tabdatas['sousmenu']="Suivi étudiants";
        //var_dump($_POST);//exit();

        if (isset($_POST['btn_afficheMoy']) && isset($_POST['suivi_classe']) && $_POST['suivi_classe'] !='' ) {

            $id_groupe = intval( htmlspecialchars($_POST['suivi_classe']) );
            //$tabdatas['suivi_mat'] = intval( htmlspecialchars($_POST['suivi_mat']) );
            $tabdatas['suivi_classe'] = intval( htmlspecialchars($_POST['suivi_classe']) );
            $table= "groupe";
            $tb_conditions=[]; 
            $tb_conditions['groupe_id'] =$tabdatas['suivi_classe'];
            $tabdatas['info_groupe'] = Model_public::get_selectSQL_ALL_by($table, $tb_conditions);

            $tabdatas['btn_afficheMoy'] = htmlspecialchars($_POST['btn_afficheMoy']) ;

            $tabdatas['getAll_elvDSgrp'] = Prof::getAll_elvDSgrp($id_groupe);
            $fct_exec= $fct_exec."Affichage liste de classe  :  Prof::getAll_elvDSgrp(".$id_groupe.");|| ";

            $getEmploiTpsBy = Admin::get_GroupEmploiDutpsBy($id_groupe);
           
            foreach ($getEmploiTpsBy as $key => $value) {
                if (intval($value['emploitps_id_prof']) == $id_prof) {
                    $tabdatas["getEmploiTpsBy"][$key]=$value;
                }
            }

            //var_dump($tabdatas['getEmploiTpsBy']);
            $fct_exec= $fct_exec."Recuperatio liste emploi du temps classe  :  Prof::get_GroupEmploiDutpsBy(".$id_groupe.");|| ";
            $tabdatas['sousmenu']="Suivi étudiants > Liste étudiants";
            $_SESSION['page'] = 'prof_list_etud';

            //var_dump($tabdatas['getAll_elvDSgrp']);

            if ( isset($_POST['optradio']) && isset($_POST['motif_absence'])) {

                //var_dump($_POST);exit;

                $ideleve_absence = intval( htmlspecialchars($_POST['ideleve_absence']) );
                $emploi_absence = ( htmlspecialchars($_POST['emploitps']) );

                $table="absence_eleve";
                $tb_infos=[];
                $tb_conditions=[];
                $tb_conditions["fk_emploitps"]= $emploi_absence;
                $tb_conditions["fk_id_eleve"]=$ideleve_absence;
                $tb_infos= $tb_conditions;
                $tb_infos["abs_motifs"]=htmlspecialchars($_POST['motif_absence']);
                $tb_infos["abs_justif"]= intval( htmlspecialchars($_POST['optradio']) );
                $tb_infos["fk_createur"]=$id_pers;

                $tabdatas['set_absence_eleves'] = Model_public::set_insertSQL($table,$tb_infos, $tb_conditions);
                $fct_exec =  $fct_exec."Ajout Absence Eleve : id_eleve=".$ideleve_absence." & Emploi TPS=".$emploi_absence." & Motif=".$tb_infos["abs_motifs"]." & Createur=".$id_pers." & Justification=".$tb_infos["abs_justif"]." &||";
                //var_dump($tabdatas['set_absence_eleves']);

            }

            //var_dump($tabdatas['getAll_elvDSgrp']);//exit;

        }

        if ( isset($_POST['btn_info_eleve']) ) {
            //var_dump($_POST);exit;
            $btn_info_eleve=explode("_",$_POST['btn_info_eleve']);
            $id_eleve = intval($btn_info_eleve[0]);
            $matricule = $btn_info_eleve[1];
            $nom_prenom = $btn_info_eleve[2];
            $suivi_mat = intval($btn_info_eleve[3]);
            $suivi_classe = intval($btn_info_eleve[4]);
            //{{ eleve.id_eleve_eleve }}_{{ eleve.matricule }}_{{ eleve.nom_prenom }}_{{ suivi_mat }}_{{ suivi_classe }}
            $tabdatas['get_elevBy']=Model_public::get_elevBy($id_eleve);
            //var_dump($tabdatas['get_elevBy']);

            $table= "eleve";
            $tb_conditions=[]; 
            $tb_conditions['id_eleve_eleve'] = $id_eleve;
            $tabdatas['info_eleve'] = Model_public::get_selectSQL_ALL_by($table, $tb_conditions);
            //var_dump($tabdatas['info_eleve']);


            $tabdatas["get_all_absence_elev"] = Admin::get_all_absence_elev($id_eleve,$suivi_classe);
			$fct_exec="Admin::get_all_absence_elev(".$id_eleve.",".$suivi_classe.")";
            //var_dump($tabdatas['get_all_absence_elev']);

            $_SESSION['page']="prof_eleve_infos";
        }

        $tabdatas['getProfGroupe'] = Prof::getProfGroupe($tabdata_user->id_type);
        $tabdatas['getProfMat'] = Prof::getProfMat($tabdata_user->id_type);

        unset($_POST);
        unset($_GET);
        $_POST=NULL;
        $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
        $_GET=NULL;
        //var_dump( $tabdatas['getProfMat']);//exit;
        //var_dump($tabdatas);
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_prof ::: group_infoAction => " . $fct_exec;
		$log_user ="Espace suivie Etudiant (liste et saisie absence)";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/
        //var_dump($_SESSION['page']);
        View::renderTemplate('Accueil/prof/'.$_SESSION['page'].'.html',$tabdatas);
        

    }

    public function prof_etudStageAction(){

        $tabdatas = Crt_prof::infosuser();
        $tabdata_user = (object)$tabdatas ;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        $fct_exec="|| ";
        $tabdatas['menu']="Information Etudiants";
        $tabdatas['sousmenu']="Stage étudiants";

        $tabdatas['panel']="home";


        if (isset($_POST['btn_afficheMoy']) && $_POST['suivi_classe'] !='' && $_POST['suivi_mat'] !='' ) {

            $id_groupe = intval( htmlspecialchars($_POST['suivi_classe']) );
            $tabdatas['suivi_mat'] = intval( htmlspecialchars($_POST['suivi_mat']) );
            $tabdatas['suivi_classe'] = intval( htmlspecialchars($_POST['suivi_classe']) );
            $tabdatas['btn_afficheMoy'] = htmlspecialchars($_POST['btn_afficheMoy']) ;

            $tabdatas['getAll_elvDSgrp'] = Prof::getAll_elvDSgrp($id_groupe);
            
            $tabdatas['sousmenu']="Suivi étudiants > Liste étudiants";

            $_SESSION['page'] = 'prof_list_etud';

            //var_dump($_POST);

            if ( isset($_POST['date_absence']) ) {
                
                $ideleve_absence = intval( htmlspecialchars($_POST['ideleve_absence']) );
                $date_absence = ( htmlspecialchars($_POST['date_absence']) );
                $hdebut_absence = ( htmlspecialchars($_POST['hdebut_absence']) );
                $hfin_absence = ( htmlspecialchars($_POST['hfin_absence']) );
                $motif_absence = ( htmlspecialchars($_POST['motif_absence']) );
                $tabdatas['set_absence_eleves'] = Prof::set_absence_eleves($ideleve_absence , $tabdatas['suivi_mat'] ,$id_groupe ,$hdebut_absence ,$hfin_absence ,$date_absence, $motif_absence );
            }

            //1var_dump($tabdatas['getAll_elvDSgrp']);//exit;

        }
        if(isset($_POST['btn_etud_listStage']) && isset($_POST['etudiant_stgeinfo']) ){
            //var_dump($_POST);
            $explode_tab = explode("_", htmlspecialchars($_POST['etudiant_stgeinfo']));
            $id_etudiant = intval($explode_tab[0]);
            $groupe_id= intval($explode_tab[1]);
            //$id_anneescol = intval(htmlspecialchars($_POST['anneeScolaire_stginfo']));
            $id_anneescol = intval(Model_public::getGroupe_idanneeBy($groupe_id));
            //var_dump( $explode_tab, $id_anneescol );exit;

            $tabdatas['get_all_stgEtudiant']=Admin::get_all_stgEtudiant($id_etudiant,$id_anneescol);
            $tabdatas['panel']="fichestage";
            //var_dump($tabdatas['get_all_stgEtudiant']);
        }


        $tabdatas['getProfGroupe'] = Prof::getProfGroupe($tabdata_user->id_type);
        $tabdatas['getProfMat'] = Prof::getProfMat($tabdata_user->id_type);


        $tabdatas['getStageByProf'] = Prof::getStageByProf($tabdata_user->id_pers_personne);
        //var_dump( $tabdatas['getStageByProf']); //exit();

        $tabdatas['getAllElev']=Admin::getAllElev();
        $tabdatas['getAnneeScolaire']=Admin::getAnneeScolaireUniq();


        //var_dump($tabdatas['getStageByProf']);
        //var_dump($tabdatas['getAllElev']);
        unset($_POST);
        unset($_GET);
        $_POST=NULL;
        $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
        $_GET=NULL;
        //var_dump( $tabdatas['getProfMat']);//exit;
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_prof ::: etudsuiviAction => " . $fct_exec;
		$log_user ="Espace suivie Etudiant (liste et saisie absence)";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/
        View::renderTemplate('Accueil/prof/'.$_SESSION['page'].'.html',$tabdatas);
        

    }







}
