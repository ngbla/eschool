<?php

namespace App\Controllers;

require_once('../App/Models/User.php');
require_once('../App/Models/Log.php');
//require_once('../App/Models/Admin.php');
require_once('../App/Models/Eleve.php');
require_once('../App/Models/User_Img.php');
require_once('../App/Models/Model_public.php');

use \Core\View;
use App\Config;
use App\Models\Log as modeldb;
use App\Models\User ;
use App\Models\Admin;
use App\Models\Eleve;
use App\Models\User_Img;
use App\Models\Model_public;
use App\Models\Upload_files;

/**

 * Home controller

 *

 * PHP version 7.0

 */

class Crt_eleve extends \Core\Controller{

    public static function infosuser(){

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
        //var_dump( );


        $filename = '../files/'.$tabdata_user->id_pers_personne.'/'.$tabdata_user->id_pers_personne.'.jpg';

        if (file_exists($filename)) {
            $tabdatas['lien_photo']= $tabdatas['liens'].$tabdata_user->id_pers_personne.'/'.$tabdata_user->id_pers_personne.'.jpg';
        } else {
            unset($tabdatas['lien_photo'] );
        }

        return $tabdatas;


    }

    public function accueilAction(){

        $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;
        $tabdatas['notif_file'] = [];
        $id_notif_tab = [];

        //var_dump($tabdata_user);
        $id_etud_personne = $tabdata_user->id_pers_personne;
        $id_pers = $tabdata_user->id_pers_personne;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        
        $fct_exec = "||";
        //var_dump( $tabdatas);
        //var_dump($_SERVER);
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

            //************************ */
            $_SESSION['page'] ="eleve_accueil" ;

            if ( isset($_POST['btn_send_fichier']) && isset($_FILES['fichier_scanner']) && $_FILES['fichier_scanner']['error']==0) {
                //var_dump($_POST,$_FILES['fichier_scanner']);exit;
                
                $tmp_file_img_error = $_FILES['fichier_scanner']['error'];
                $tmp_file_type =$_FILES['fichier_scanner']['type'];
                $tmp_file_img_tmp_name = $_FILES['fichier_scanner']['tmp_name'];
                $tmp_file_img_size = $_FILES['fichier_scanner']['size'];
                $tmp_file_img_name = $_FILES['fichier_scanner']['name'];
                User::uploadFiles($id_etud_personne, $tmp_file_img_error, $tmp_file_type, $tmp_file_img_tmp_name, $tmp_file_img_size, $tmp_file_img_name);
            }

            if (isset($_POST['btn_send_photo'] )) {

                //var_dump($_POST );//exit();
                $tabdatas['sendImg_etat']=User_Img::sendImg();
                $tabdatas['lien_photo']= $tabdatas['liens'].$_SESSION['user']['id_pers_personne'].'/'.$_SESSION['user']['id_pers_personne'].'.jpg';

            }

            $tabdatas['get_userNotifs']=User::get_userNotifs($tabdata_user->id_type, 1);
            if ( $tabdatas['get_userNotifs'] == 0) {
                unset($tabdatas['get_userNotifs']);
            }
            else {
                //var_dump($tabdatas['get_userNotifs']);
                foreach ($tabdatas['get_userNotifs'] as $key => $value) {
                    $id_notif = $value['id_notif'];
                    //var_dump($id_notif);
    
                    $upFolder = dirname(__DIR__, 2)."/"."notifications"."/".$value['id_notif'];
                    //var_dump( $upFolder);
                    $id_notif_tab=array( $id_notif => User_Img::get_dossierContenu( $upFolder,$tabdatas['liens'], $id_notif ));
                    array_push($tabdatas['notif_file'],$id_notif_tab);
                }
            }

            $id_grp_eleve = intval($tabdatas['groupe_grpe_id']);
            $tabdatas['allEmploiTps']=Eleve::get_eleves_grpEmploiTps($id_grp_eleve);
            $tabdatas['allGrpEval']=Eleve::get_eleves_grpEval($id_grp_eleve);

            $dossier = dirname(getcwd(), 1) . '/files' . '/' . $id_etud_personne . '/' . 'dossier/';
            $liens =   $tabdatas['base_liens'] . 'files' . '/' . $id_etud_personne . '/' . 'dossier/';
            $get_dossierContenu = Upload_files::get_dossierContenu($dossier, $liens);

            if ($get_dossierContenu != 0) {
                $tabdatas['get_dossierContenu'] = $get_dossierContenu;
            }
    
            $_POST=NULL;
            $_GET=NULL;
            unset($_POST);
            unset($_GET);
            //$tabdatas['univInfos']=User::getUnivInfos();
            $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
            //var_dump( $tabdatas['univInfos']);
            //var_dump($tabdatas);
            View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

            //************************ */
        }

    }

    public function noteAction(){

        $_SESSION['page'] ="note_eleve" ;
        $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;

        //var_dump($tabdata_user);exit;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        $id_eleve = $tabdata_user->id_type;
        $groupe_libelle = $tabdata_user->groupe_libelle;
        $groupe_grpe_id = $tabdata_user->groupe_grpe_id;
        $fct_exec="|| ";

        $tabdatas['menu']="Vie Scolaire";
        $tabdatas['sousmenu']="Notes";
        //var_dump( $tabdata_user->id_type );
        //exit();
        $tabdatas['getAllEleveNoteBy']=Eleve::getAllEleveNoteBy($tabdata_user->id_type);
        $fct_exec="Eleve::getAllEleveNoteBy(".$tabdata_user->id_type.")";
        //var_dump('getAllEleveNoteBy',$tabdatas['getAllEleveNoteBy']);
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_eleve ::: noteAction => " . $fct_exec;
		$log_user ="Vie scolaire -- Notes";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/

        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);

        //$tabdatas['univInfos']=User::getUnivInfos();
        $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function pvAction(){

        //var_dump($_POST);
        $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;
        //var_dump( $tabdatas );exit;
        //exit();

        $_SESSION['page'] ="pv_eleve" ;

        if ( isset($_POST['semmestre_id']) ) {
            $id_partannee = intval (htmlspecialchars( $_POST['semmestre_id'] ));

            $tabdatas["semmestre_id"]=  $id_partannee;
            //var_dump('semmestre_id',$tabdatas["semmestre_id"]);

            $id_eleve = intval ($tabdata_user->id_type);
            $getAllEleve_UniqMaxMat_MoyenneBy=Eleve::getAllEleve_UniqMaxMat_MoyenneBy($id_eleve, $id_partannee);
            //var_dump('getAllEleve_UniqMaxMat_MoyenneBy ',  $getAllEleve_UniqMaxMat_MoyenneBy );

            foreach ($getAllEleve_UniqMaxMat_MoyenneBy as $key => $value) {
                $cle = intval($value["id_matiere"]);
                //var_dump($cle);
                $tabdatas["moyennes"]["$cle"] = [$value["moyenne"] ,$value["Libelle_session"],$value["libele_partie"],$value["id_annee_partie"]  ]; 
            }

            //var_dump("moyennes",$tabdatas["moyennes"]);
            //var_dump($tabdatas);

        }
        

        $tabdatas['getEleve_grpeMatParent']=Eleve::getEleve_grpeMatParent($tabdata_user->id_type);

        foreach ($tabdatas['getEleve_grpeMatParent']  as $key => $value) {
            //var_dump($key);
            //print_r($value);
            //var_dump('matiere_id_tmp', ($tabdatas['getEleve_grpeMatParent'][$key])['matiere_id_tmp'] );
            $id_matparent = intval( ($tabdatas['getEleve_grpeMatParent'][$key])['matiere_id_tmp'] );
            $tabdatas['getEleve_grpeMatFils']=Eleve::getEleve_grpeMatFils($tabdata_user->id_type, $id_matparent);
            ($tabdatas['getEleve_grpeMatParent'][$key])['matiere_fils'] = $tabdatas['getEleve_grpeMatFils'];
            
        }
        //var_dump($tabdatas['getEleve_grpeMatParent'] );
        $tabdatas['get_elevGrp_anneePart']=Eleve::get_elevGrp_anneePart($tabdata_user->id_type);
        
        //var_dump( $tabdatas );
        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);
        //$tabdatas['univInfos']=User::getUnivInfos();
                $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
        $tabdatas['menu']="Vie scolaire ";
        $tabdatas['sousmenu']=" PV";

        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function absencesAction() {

        $_SESSION['page'] ="absences_eleve" ;
        $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;
        //var_dump($tabdata_user);exit;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        $id_eleve = $tabdata_user->id_type;
        $groupe_libelle = $tabdata_user->groupe_libelle;
        $groupe_grpe_id = $tabdata_user->groupe_grpe_id;
        
        $fct_exec="|| ";
  
        //var_dump($_POST);
        $tabdatas["get_eleve_abs"] = Admin::get_all_absence_elev($id_eleve,$groupe_grpe_id);
        $fct_exec="Admin::get_eleve_abs(".$id_eleve.",".$groupe_grpe_id.")";
        //var_dump($tabdatas['get_all_absence_elev']);
        //$tabdatas['get_eleve_abs']=Eleve::get_eleve_abs($tabdata_user->id_type);
        //var_dump($tabdatas['get_eleve_abs']);  

        $tabdatas['menu']="Vie scolaire ";
        $tabdatas['sousmenu']=" Absences";

        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_eleve ::: absencesAction => " . $fct_exec;
		$log_user ="Vie scolaire -- Absences";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/

        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);
        //$tabdatas['univInfos']=User::getUnivInfos();
        $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function convocationAction() {


        $_SESSION['page'] ="convocation_eleve" ;

        $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;


        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        //$tabdatas['univInfos']=User::getUnivInfos();
                $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);

        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function informationAction(){

        $_SESSION['page'] ="information_eleve" ;

        $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;

        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        //$tabdatas['univInfos']=User::getUnivInfos();
                $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);

        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function agendaAction() {

        $_SESSION['page'] ="agenda_eleve" ;

        $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;

        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        //$tabdatas['univInfos']=User::getUnivInfos();
                $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);

        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function fichierAction() {

        $_SESSION['page'] ="fichier_perso" ;

        $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;

        //exit();

        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        //$tabdatas['univInfos']=User::getUnivInfos();
                $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);

        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function moyAction() {

        $_SESSION['page'] ="moy_eleve" ;

        $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;
        //var_dump($tabdata_user);exit;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        $id_eleve = $tabdata_user->id_type;
        $groupe_libelle = $tabdata_user->groupe_libelle;
        $groupe_grpe_id = $tabdata_user->groupe_grpe_id;
        $fct_exec="|| ";

        //$tabdatas['getAllEleve_Mat_MoyenneBy']=Eleve::getAllEleve_Mat_MoyenneBy($tabdata_user->id_type);
        //var_dump('getAllEleve_Mat_MoyenneBy',$tabdatas['getAllEleve_Mat_MoyenneBy']);
        //$fct_exec="Eleve::getAllEleve_Mat_MoyenneBy(".$tabdata_user->id_type.")";

        $tabdatas['getAllEleve_Mat_MoyenneBy'] = Admin::get_eleve_all_moy($id_eleve,$tabdata_user->groupe_annee_id,$groupe_grpe_id);
        $fct_exec=$fct_exec."Admin::get_eleve_all_moy(".$id_eleve.",".$tabdata_user->groupe_annee_id.",".$groupe_grpe_id.") ||";
        //var_dump('getAllEleve_Mat_MoyenneBy',$tabdatas['getAllEleve_Mat_MoyenneBy']);

        $tabdatas['menu']="Vie Scolaire";
        $tabdatas['sousmenu']="Moyenne";

        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);

        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_eleve ::: moyAction => " . $fct_exec;
		$log_user =$tabdatas['menu']." -- ".$tabdatas['sousmenu'];
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/

        //$tabdatas['univInfos']=User::getUnivInfos();
        $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function procoursAction() {

        $_SESSION['page'] ="pro_cours_eleve" ;

               $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;
        //$tabdatas['univInfos']=User::getUnivInfos();
                $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
        $tabdatas['AllProcoursByEleve']=Eleve::getAllProcoursByEleve($tabdata_user->id_type);
        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);
        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function evaluationAction() {

        //var_dump($_SESSION);

        //var_dump($_SESSION["page"]);

        //exit();

        $_SESSION['page'] ="evaluation_eleve" ;

        $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;

        //var_dump($tabdata );

        //exit();

        //$tabdatas['univInfos']=User::getUnivInfos();
                $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
        $tabdatas['AllEvaluationByEleve']=Eleve::getAllEvaluationByEleve($tabdata_user->id_type);

        

        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        //$tabdatas['nbreAllGrpe']=Eleve::getNbreEleveGrpe($tabdata_user->id_type);

        //$tabdatas['nbreAllMatiere']=Eleve::getNbreEleveMatriere($tabdata_user->id_type);

        //var_dump( $tabdatas['nbreAllGrpe'], $tabdatas['nbreAllMatiere']);//exit;

        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function notifAction(){



               $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;

        $tabdatas['menu']="notification";

        $tabdatas['sousmenu']="";


        $tabdatas['allEleve_classe']=Eleve::getEleveGrpe($tabdata_user->id_type);



        if ( isset($_POST['eleve_eval_btnsubmit']) && $_POST['eleve_eval_groupe']!=0 && $_POST['eleve_eval_mat']!=0  && $_POST['eleve_eval_libelle']!="" ) {

           //var_dump($_POST);



            $id_grpe = intval(htmlspecialchars($_POST['eleve_eval_groupe']));

            $id_mat = intval(htmlspecialchars($_POST['eleve_eval_mat']));

            $eval_lib= htmlspecialchars($_POST['eleve_eval_libelle']);

            $eval_desc= htmlspecialchars($_POST['eleve_eval_desc']);



            $tabdatas['eleve_addevaluation']=Eleve::setEleveGrpEval($id_grpe ,$id_mat ,$eval_lib ,$eval_desc);



        }



        $tabdatas['allEleve_eval']=Eleve::getEleveGrpEval();

        //var_dump($tabdatas['allEleve_eval']);



        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);



    }

    public function evaluationInfoAction(){

        $type = explode("_", htmlspecialchars($_POST['btn_voir_eval']));

        $id_eval = intval($type[1]);

               $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;

        $tabdatas['menu']="Gesion des Classes";

        $tabdatas['sousmenu']="Evaluations > Information & Notes";

        $tabdatas['eleve_eval_date']=Eleve::getEleveGrpEvalWithDateBy($id_eval);

        $tabdatas['eleve_eval_salle']=Eleve::getEvalSalle($id_eval);

        $eleve_eval_grpEleve_tmp=Eleve::getAllEleveByGroup($id_eval);

        $tabdatas['eleve_eval_grpEleve'] = [];

        foreach ( $eleve_eval_grpEleve_tmp as $key => $value) {

            //var_dump("key =",$key);

            foreach ($value as $keyfils => $valuefils) {

                if ($keyfils == 'id_eleve_eleve') {

                    //var_dump($keyfils,$valuefils);

                    //$tabdatas['eleve_eval_grpEleve']=Eleve::setGetInitEleveEvalNote(intval($valuefils), $id_eval);

                    $reps = Eleve::setGetInitEleveEvalNote(intval($valuefils), $id_eval);

                    array_push($tabdatas['eleve_eval_grpEleve'],$reps);

                }

            }

        }

        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        View::renderTemplate('Accueil/eleve/eleve_eval_info.html',$tabdatas);

    }



    public function stagesAction()
    {
        $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;
        $tabdatas['menu']="Mes Stages";
        //$tabdatas['sousmenu']="Moyennes";
        //var_dump( $tabdata_user->id_type );
        //exit();
        //var_dump($_POST);//exit;
        //var_dump($_SESSION['page'] );
        //var_dump($_GET['p']);getStageByEleve
        $tabdatas['getAllEleveNoteBy']=Eleve::getAllEleveNoteBy($tabdata_user->id_type);
        $tabdatas['get_stage_etudiant']=Eleve::getStageByEleve($tabdata_user->id_type);
        //var_dump('get_stage_etudiant',$tabdatas['get_stage_etudiant']); //exit();
        //Type personne (1 eleve)
        $fk_type = 1;

        $panel = 'home';
        $tabdatas['get_stagerapport'] = [];

        if ( isset($_POST['anneeScolaire'])  && isset($_POST['classe'])  && isset($_POST['direct_ecole'])  && $_POST['anneeScolaire']!="" && $_POST['classe']!="" && $_POST['direct_ecole']!="") {

            $panel = 'ajoutstage';
            $theme_stage = htmlspecialchars($_POST['theme_stage']);
            $fk_idetudiant= intval($tabdata_user->id_type);
            $fk_idgroupe= intval(htmlspecialchars($_POST['classe']));
            $fk_idprof_directEtud= intval(htmlspecialchars($_POST['direct_ecole']));
            $fk_idanneeScol= intval(htmlspecialchars($_POST['anneeScolaire']));
            $nom_entreprise= htmlspecialchars($_POST['entreprise']);
            $ville_entreprise= htmlspecialchars($_POST['ville_entreprise']);
            $loca_entreprise= htmlspecialchars($_POST['loca_entreprise']);
            $tel_entreprise= htmlspecialchars($_POST['tel_entreprise']);
            $email_entreprise= htmlspecialchars($_POST['mail_entreprise']);
            $maitre_stage= htmlspecialchars($_POST['maitre_stage']);
            $tel_maitre_stage= htmlspecialchars($_POST['tel_maitre_stage']);
            $poste_maitre_stage= htmlspecialchars($_POST['job_maitre_stage']);
            $date_debut= htmlspecialchars($_POST['date_debut']);
            $date_fin= htmlspecialchars($_POST['date_fin']);
            


            $tabdatas['stage_etudiant']=Admin::stage_etudiant($theme_stage, $fk_idetudiant, $fk_idgroupe,$fk_idprof_directEtud, $fk_idanneeScol,$nom_entreprise,$ville_entreprise, $loca_entreprise, $tel_entreprise, $email_entreprise, $maitre_stage, $tel_maitre_stage, $poste_maitre_stage, $date_debut, $date_fin);

            //var_dump('stage_etudiant',$tabdatas['stage_etudiant']);


        }
        

        if (isset($_POST['btn_etud_listStage']) && isset($_POST['etudiant_stgeinfo']) ) {
            $id_stage = intval(htmlspecialchars($_POST['etudiant_stgeinfo']));
            $tabdatas['get_all_stgEtudiant']=Eleve::get_all_stgEtudiant($id_stage);
            $panel = 'fichestage';
            $get_stagerapport=Eleve::get_stagerapport($tabdata_user->id_type);
            foreach ($get_stagerapport as $key => $value) {
                $value_stage= intval($value['fk_id_stage']);
                //var_dump($value_stage,'=',$id_stage );
                if($value_stage ==$id_stage ){
                    //var_dump( $get_stagerapport[$key]);
                    $tabdatas['get_stagerapport'][]= $value;
                }
                
            }
        }


        if (isset($_POST['btn_etud_rapportStage'])) {

            //var_dump($_POST);//exit;
            $panel = 'rapportstage';

            $fk_id_stage =intval( htmlspecialchars($_POST['etudiant_stgeinfo']));
            $date_rapport = htmlspecialchars($_POST['date_rapport']);
            $rapport = htmlspecialchars($_POST['rapport']);
            $fk_id_eleve = $tabdata_user->id_type;
            $tabdatas['set_stagerapport'] = Eleve::set_stagerapport($fk_id_stage, $fk_id_eleve,$fk_type,$date_rapport,$rapport );
            //var_dump('set_stagerapport',$tabdatas['set_stagerapport']);//exit;

        }
        $tabdatas['getAnneeScolaire']=Admin::getAnneeScolaireUniq();
        $tabdatas['get_eleves_allGrpe']=Eleve::get_eleves_allGrpe($tabdata_user->id_type);
        //var_dump('get_eleves_allGrpe',$tabdatas['get_eleves_allGrpe']); //exit();

        $tabdatas['panel']= $panel;
        $tabdatas['getAllProf']=Admin::getProf();
        
       //var_dump($tabdatas['get_stagerapport']);
        //var_dump($tabdatas['get_stagerapport']);//exit;

        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);

        //$tabdatas['univInfos']=User::getUnivInfos();
                $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);
        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);


    }

    

    /**
    *      ELEARNING
    */
    public function elearning_eleveAction(){

        $tabdatas = Crt_eleve::infosuser();
        $tabdata_user = (object)$tabdatas ;

        $tabdatas['menu']="Gesion des Classes";
        $tabdatas['sousmenu']="Evaluations > Information & Notes";



        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);

        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);


    }



}

