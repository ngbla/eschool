<?php

namespace App\Controllers;

require_once('../App/Models/User.php');
require_once('../App/Models/Log.php');
//require_once('../App/Models/Admin.php');
require_once('../App/Models/Parent.php');
require_once('../App/Models/User_Img.php');


use \Core\View;
use App\Config;
use App\Models\Log as modeldb;
use App\Models\User ;
//use App\Models\Admin;
use App\Models\Eleve;
use App\Models\Parents;
use App\Models\User_Img;
use App\Models\Model_public;



/**

 * Home controller

 *

 * PHP version 7.0

 */

class Crt_parent extends \Core\Controller{

    /**

     * Show the index page

     *

     * @return void

    */
    public function infosuser(){

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

    public function accueilAction() {
        $tabdatas = Crt_parent::infosuser();
        $tabdata_user = (object)$tabdatas ;
        $tabdatas['notif_file'] = [];
        $id_notif_tab = [];
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        
        $fct_exec = "||";
        //var_dump( $tabdatas);exit;
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

            //********************************** */
            if (isset($_POST['btn_send_photo'] )) {
                //var_dump($_POST );//exit();
                $tabdatas['sendImg_etat']=User_Img::sendImg();
                $tabdatas['lien_photo']= $tabdatas['liens'].$_SESSION['user']['id_pers_personne'].'/'.$_SESSION['user']['id_pers_personne'].'.jpg';
            }

            $tabdatas['get_userNotifs']=User::get_userNotifs($tabdata_user->id_type, 3);
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

            $table ='parent' ;
            $tb_conditions =[] ;
            $tb_conditions['id_parent_parent'] =intval($tabdatas['id_type']);
            $_parent_info =  Model_public::get_selectSQL_ALL_by($table, $tb_conditions);
            //var_dump($_parent_info);

            if (!empty($_parent_info) && $_parent_info!=0) {
                $_parent_info=$_parent_info[0];
                $tble_mat_enfs = explode(';',$_parent_info['matricule']);
                //var_dump($tble_mat_enfs);
                foreach ($tble_mat_enfs as $key => $value) {
                    $table ='eleve' ;
                    $tb_conditions =[] ;
                    $tb_conditions['matricule'] =$value;
                    $_eleve_info =  Model_public::get_selectSQL_ALL_by($table, $tb_conditions);

                    if (isset($_eleve_info) && !empty($_eleve_info) && $_eleve_info!=0) {
                        $_eleve_info= $_eleve_info[0];
                        //var_dump($_eleve_info['id_eleve_eleve']);

                        $tb_infos = [];
                        $table ='parent_enfants' ;
                        $tb_conditions =[] ;
                        $tb_conditions['id_parent'] =intval($tabdatas['id_type']);
                        $tb_conditions['id_enfant'] =intval($_eleve_info['id_eleve_eleve']);

                        $tb_infos['id_enfant'] =intval($_eleve_info['id_eleve_eleve']);
                        $tb_infos['id_parent'] =intval($tabdatas['id_type']);
                        $tb_infos['etat_parent_enfant'] =1;
                        $_eleve_ajout =  Model_public::set_insertSQL($table,$tb_infos, $tb_conditions);
                        //var_dump($_eleve_ajout);
                        
                    }
                    
                    
                }

            }
            


            $id_parent = intval($tabdatas['id_type']);
            $tabdatas['get_allEnfantBy']=Parents::get_allEnfantBy($id_parent);
            //var_dump($tabdatas['get_allEnfantBy']);

            $_POST=NULL;
            $_GET=NULL;
            unset($_POST);
            unset($_GET);

            $tabdatas['univInfos']=Model_public::get_univInfo_By($tabdata_user->fk_iduniv);

            View::renderTemplate('Accueil/parent/'.$_SESSION['page'].'.html',$tabdatas);

            //********************************** */
        }

    }

    public function etudAction() {

        $tabdatas = Crt_parent::infosuser();
        $tabdata_user = (object)$tabdatas ;

        $_SESSION['page'] ="parent_etud" ;

        //var_dump($_POST);
        //var_dump($_GET);//exit();
        $id_parent = intval($tabdatas['id_type']);
        $tabdatas['get_allEnfantBy']=Parents::get_allEnfantBy($id_parent);
        //var_dump( $tabdatas['get_allEnfantBy']);

        if (isset($_GET['id'])) {

            $id_eleve = intval($_GET['id']);
            $tabdatas['parent_enfant'] =$id_eleve;
            foreach ( $tabdatas['get_allEnfantBy'] as $key => $value) {
                //var_dump($value);
                if ( intval($value['id_eleve_eleve']) == $id_eleve) {
                    $id_eleve_pers = $value['id_pers_personne'];
                }
            }
            $tabdatas['lien_photo_enfant']= $tabdatas['liens'].$id_eleve_pers.'/'.$id_eleve_pers.'.jpg';

            $tabdata_eleve_infosecole = Eleve::get_eleves_allinfos($id_eleve);
            //var_dump($tabdata_eleve_infosecole);

            if (!empty($tabdata_eleve_infosecole) && $tabdata_eleve_infosecole !=0) {
                 $tabdata_eleve_infosecole =(object)($tabdata_eleve_infosecole)[0];
            

                $tabdatas['matricule'] =$tabdata_eleve_infosecole->matricule;
                $tabdatas['Niveau'] =$tabdata_eleve_infosecole->Niveau;

                $tabdatas['groupe_libelle'] =$tabdata_eleve_infosecole->groupe_libelle;
                $tabdatas['class_lib'] =$tabdata_eleve_infosecole->class_lib;

                $tabdatas['annee_libelle'] =$tabdata_eleve_infosecole->annee_libelle;
                $tabdatas['annee_date_debut'] =$tabdata_eleve_infosecole->annee_date_debut;
                $tabdatas['annee_date_fin'] =$tabdata_eleve_infosecole->annee_date_fin;

                $tabdatas['groupe_annee_id'] =$tabdata_eleve_infosecole->groupe_annee;
                $tabdatas['groupe_classe_id'] =$tabdata_eleve_infosecole->groupe_classe;
                $tabdatas['groupe_grpe_id'] =$tabdata_eleve_infosecole->elv_ds_grpe_groupe;
            }
            //ABSENCES
            $tabdatas['get_eleve_abs']=Eleve::get_eleve_abs($id_eleve);

            //NOTES
                $tabdatas['getAllEleveNoteBy']=Eleve::getAllEleveNoteBy($id_eleve );

            //MOYENNES
                $tabdatas['getAllEleve_Mat_MoyenneBy']=Eleve::getAllEleve_Mat_MoyenneBy($id_eleve );

            //PV
            if ( isset($_POST['semmestre_id']) ) {
                $id_partannee = intval (htmlspecialchars( $_POST['semmestre_id'] ));
    
                $tabdatas["semmestre_id"]=  $id_partannee;
                //var_dump('semmestre_id',$tabdatas["semmestre_id"]);
    
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
            
            $tabdatas['getEleve_grpeMatParent']=Eleve::getEleve_grpeMatParent($id_eleve);
    
            foreach ($tabdatas['getEleve_grpeMatParent']  as $key => $value) {
                //var_dump($key);
                //print_r($value);
                //var_dump('matiere_id_tmp', ($tabdatas['getEleve_grpeMatParent'][$key])['matiere_id_tmp'] );
                $id_matparent = intval( ($tabdatas['getEleve_grpeMatParent'][$key])['matiere_id_tmp'] );
                $tabdatas['getEleve_grpeMatFils']=Eleve::getEleve_grpeMatFils($id_eleve, $id_matparent);
                ($tabdatas['getEleve_grpeMatParent'][$key])['matiere_fils'] = $tabdatas['getEleve_grpeMatFils'];

            }
            //var_dump($tabdatas['getEleve_grpeMatParent'] );
            $tabdatas['get_elevGrp_anneePart']=Eleve::get_elevGrp_anneePart($id_eleve);
            

    
    
        
        
        
        }

        $tabdatas['menu']="Suivi";
        $tabdatas['sousmenu']="Informations sur l'enfant";

        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);

        
        $tabdatas['univInfos']=User::getUnivInfos();

        View::renderTemplate('Accueil/parent/'.$_SESSION['page'].'.html',$tabdatas);

    }



    public function moyAction() {


        $_SESSION['page'] ="parent_moy" ;
        $tabdatas = Crt_parent::infosuser();
        $tabdata_user = (object)$tabdatas ;

        //var_dump($tabdatas );

        //exit();

        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        $tabdatas['univInfos']=User::getUnivInfos();

        View::renderTemplate('Accueil/parent/'.$_SESSION['page'].'.html',$tabdatas);

    }



    public function noteAction() {

        $tabdatas = Crt_parent::infosuser();
        $tabdata_user = (object)$tabdatas ;

        $_SESSION['page'] ="note_eleve" ;

 
        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        $tabdatas['univInfos']=User::getUnivInfos();

        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function pvAction()  {

        $tabdatas = Crt_parent::infosuser();
        $tabdata_user = (object)$tabdatas ;

        $_SESSION['page'] ="pv_eleve" ;

        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        $tabdatas['univInfos']=User::getUnivInfos();


        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function absencesAction() {
 
        $_SESSION['page'] ="absences_eleve" ;
        $tabdatas = Crt_parent::infosuser();
        $tabdata_user = (object)$tabdatas ;

        //var_dump($tabdata );

        //exit();

        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        $tabdatas['univInfos']=User::getUnivInfos();

        var_dump( $tabdatas['nbreAllGrpe'], $tabdatas['nbreAllMatiere']);//exit;

        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function convocationAction() {

        $_SESSION['page'] ="absences_eleve" ;
        $tabdatas = Crt_parent::infosuser();
        $tabdata_user = (object)$tabdatas ;

        $_SESSION['page'] ="convocation_eleve" ;
 
        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        $tabdatas['univInfos']=User::getUnivInfos();
 
        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function informationAction() {

        $_SESSION['page'] ="absences_eleve" ;
        $tabdatas = Crt_parent::infosuser();
        $tabdata_user = (object)$tabdatas ;

        $_SESSION['page'] ="information_eleve" ;
 
        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        $tabdatas['univInfos']=User::getUnivInfos();

  
        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function agendaAction()  {

        $_SESSION['page'] ="absences_eleve" ;
        $tabdatas = Crt_parent::infosuser();

        $_SESSION['page'] ="agenda_eleve" ;
 

        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        $tabdatas['univInfos']=User::getUnivInfos();
 

        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function fichierAction()  {
        $_SESSION['page'] ="absences_eleve" ;
        $tabdatas = Crt_parent::infosuser();
        $tabdata_user = (object)$tabdatas ;

        $_SESSION['page'] ="fichier_perso" ;
 

        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        $tabdatas['univInfos']=User::getUnivInfos();
 

        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }

    public function notifAction(){
        $_SESSION['page'] ="absences_eleve" ;
        $tabdatas = Crt_parent::infosuser();
        $tabdata_user = (object)$tabdatas ;


        $tabdatas['menu']="notification";

        $tabdatas['sousmenu']="";

        $tabdatas['allEleve_classe']=Eleve::getEleveGrpe($tabdata_user->id_type);

        if ( isset($_POST['eleve_eval_btnsubmit']) && $_POST['eleve_eval_groupe']!=0 && $_POST['eleve_eval_mat']!=0  && $_POST['eleve_eval_libelle']!="" ) {


            $id_grpe = intval(htmlspecialchars($_POST['eleve_eval_groupe']));

            $id_mat = intval(htmlspecialchars($_POST['eleve_eval_mat']));

            $eval_lib= htmlspecialchars($_POST['eleve_eval_libelle']);

            $eval_desc= htmlspecialchars($_POST['eleve_eval_desc']);



            $tabdatas['eleve_addevaluation']=Eleve::setEleveGrpEval($id_grpe ,$id_mat ,$eval_lib ,$eval_desc);



        }

        $tabdatas['allEleve_eval']=Eleve::getEleveGrpEval();

        $_POST=NULL;

        $_GET=NULL;

        unset($_POST);

        unset($_GET);

        View::renderTemplate('Accueil/eleve/'.$_SESSION['page'].'.html',$tabdatas);

    }


    public function evaluationInfoAction(){
 
        $tabdatas = Crt_parent::infosuser();
        $tabdata_user = (object)$tabdatas ;

 
        $type = explode("_", htmlspecialchars($_POST['btn_voir_eval']));

        $id_eval = intval($type[1]);
 

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

}

