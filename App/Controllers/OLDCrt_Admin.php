<?php

namespace App\Controllers;

require_once('../App/Models/User.php');
require_once('../App/Models/Log.php');
require_once('../App/Models/Admin.php');
require_once('../App/Models/Prof.php');
require_once('../App/Models/User_Img.php');
require_once('../App/Models/Upload_files.php');
require_once('../App/Models/Model_public.php');

use \Core\View;
use App\Config;
use App\Models\Log as modeldb;
use App\Models\User as modelUser;
use App\Models\Admin;
use App\Models\Eleve;
use App\Models\Prof;
use App\Models\User_Img;
use App\Models\Upload_files;
use App\Models\Model_public;
use App\Models\User;
use App\Models\Comptable;
   
 
/**
 * Home controller
 *
 * PHP version 7.0
 */
class Crt_Admin extends \Core\Controller
{
    public static function infosuser()
    {

        //var_dump($_SESSION['user']);
        $tabdata_user = (object)$_SESSION['user'];
        $infos_univ = (object)(Model_public::get_univInfo_By($tabdata_user->fk_iduniv)[0]);
        $get_adminRole_By = (object)(Model_public::get_adminRole_By($tabdata_user->id_type)[0]);
        $get_all_user_notif = Model_public::get_all_user_notif($tabdata_user->type_pers,$tabdata_user->id_type);

        $get_user_annee = Model_public::get_user_annee($tabdata_user->id_pers_personne);

        //var_dump( $get_user_annee );

        //var_dump( $get_all_user_notif);


        if (Config::ENVI == 'local') {
            $protocole = Config::PROTOC_LOCAL;
        } else {
            $protocole = Config::PROTOC_LIGNE;
        }

        $base_liens =  $protocole . '://' . ($_SERVER['HTTP_HOST']) . '/';
        $liens =  $protocole . '://' . ($_SERVER['HTTP_HOST']) . '/files' . '/';


        $tabdatas = [

            'id_pers_personne'  =>  $tabdata_user->id_pers_personne,
            'user_allnotif_defil'  =>  $get_all_user_notif,

            'id_role'  =>  $get_adminRole_By->id_role,
            'lib_role'  =>  $get_adminRole_By->lib_role,

            'lib_user_annee'  => $get_user_annee[0]["annee_libelle"],
            'lib_user_id_annee'  => intval($get_user_annee[0]["id_anscol_annee_scolaire"]),

            'fk_iduniv'  =>  $infos_univ->id_univ,
            'non_univ'  =>  $infos_univ->non_univ,
            'contact_univ'  =>  $infos_univ->contact_univ,
            'email_univ'  =>  $infos_univ->email_univ,
            'ville_univ'  =>  $infos_univ->ville_univ,
            'initiale_univ'  =>  $infos_univ->initiale_univ,

            'nom_prenom' =>  $tabdata_user->nom_prenom,
            'date_naiss' => $tabdata_user->date_naiss,
            'lieu_naiss' => $tabdata_user->lieu_naiss,
            'sexe' =>  $tabdata_user->sexe,
            'email' =>  $tabdata_user->email,
            'contact' =>  $tabdata_user->contact,
            'type_pers' =>  $tabdata_user->type_pers,
            'id_type' =>  $tabdata_user->id_type,
            'liens' =>  $liens,
            'base_liens' =>  $base_liens,
            'server_host' => $_SERVER['HTTP_HOST']
        ];

        $filename = '../files/' . $tabdata_user->id_pers_personne . '/' . $tabdata_user->id_pers_personne . '.jpg';

        if (file_exists($filename)) {
            $tabdatas['lien_photo'] = $liens . $tabdata_user->id_pers_personne . '/' . $tabdata_user->id_pers_personne . '.jpg';
        } else {
            $tabdatas['lien_photo'] = '/public/assets/img/m.png ';
        }
        //var_dump($tabdatas);

        return $tabdatas;
    }

    public function accueilAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        //var_dump( $tabdatas);
        //var_dump($_POST);
        if (isset($_POST['change_annee']) && isset($_POST['id_annee_admin'])) {
            $id_annee_admin = intval(htmlspecialchars($_POST['id_annee_admin']));
            $table="personne";
            $tb_infos=[]; 
            $tb_conditions=[]; 
            $tb_conditions['id_pers_personne']=$tabdata_user->id_pers_personne;
            $tb_infos['fk_idanneescol']=$id_annee_admin;
            $tabdatas['change_annee'] = Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);

            $tabdatas = Crt_Admin::infosuser();
            $tabdata_user = (object)$tabdatas;
        }

        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        $fct_exec = "||";
        //var_dump( $tabdatas);


        if (isset($_GET) && isset($_GET['action']) && isset($_GET['cible'])) {
            $action_shs = sha1('update_univ');
            //echo $action_shs;
            if ($_GET['action'] == $action_shs && $tabdata_user->id_role == 1) {
                $id_tmp_univ = intval(htmlspecialchars($_GET['cible']));
                $tabdatas['set_update_adminUnivBy'] = Admin::set_update_adminUnivBy($id_tmp_univ, $tabdata_user->id_type);
                //var_dump($_GET);exit();
                $fct_exec = $fct_exec . 'set_update_adminUnivBy || ';

                //Recuperation des infos apres update


                unset($_SESSION["user"]['fk_iduniv']);
                $_SESSION["user"]['fk_iduniv'] = $id_tmp_univ;
                //var_dump($_SESSION["user"]['fk_iduniv']);

                unset($tabdatas);
                $tabdatas = Crt_Admin::infosuser();
                $tabdata_user = (object)$tabdatas;
            }
        }

        $tabdatas['notif_file'] = [];
        $id_notif_tab = [];

        //$tabdatas['result_effectif_all'] =Admin::getAllEffectifs();
        $tabdatas['result_effectif_all'] = Admin::get_AllEffectifsBy($tabdata_user->fk_iduniv);
        $tabdatas['get_all_univ'] = Model_public::get_all_univ();
        //var_dump($tabdatas['get_all_univ']);
        //var_dump($tabdatas['result_effectif_all'] );

        if (isset($_POST['btn_send_photo'])) {

            //var_dump($_POST );//exit();
            $tabdatas['sendImg_etat'] = User_Img::sendImg();
            $tabdatas['lien_photo'] = $tabdatas['liens'] . $_SESSION['user']['id_pers_personne'] . '/' . $_SESSION['user']['id_pers_personne'] . '.jpg';
            $fct_exec = $fct_exec . '|| sendImg() & liens=' . $tabdatas['lien_photo'] . ' || ';
        }
        //$tabdatas['get_userNotifs']=modelUser::get_userNotifs($tabdata_user->id_type, 4);
        $tabdatas['get_userNotifs'] = modelUser::get_userNotifsBy($tabdata_user->id_type, 4, $tabdata_user->fk_iduniv);

        if ($tabdatas['get_userNotifs'] == 0) {
            unset($tabdatas['get_userNotifs']);
        } else {

            foreach ($tabdatas['get_userNotifs'] as $key => $value) {
                $id_notif = $value['id_notif'];

                $upFolder = dirname(__DIR__, 2) . "/" . "notifications" . "/" . $value['id_notif'];
                //var_dump( $upFolder);
                $id_notif_tab = User_Img::get_dossierContenu($upFolder, $tabdatas['liens'], $id_notif);
                $fct_exec = $fct_exec . '|| sendImg() & liens=' . $tabdatas['liens'] . ' || ';

                //var_dump( $id_notif_tab);

                if(!empty($id_notif_tab) && $id_notif_tab != NULL){
                    //var_dump( current($id_notif_tab));exit();
                    foreach ($id_notif_tab as $keys => $values) {
                         //var_dump( $keys);
                        ($tabdatas['notif_file'][$id_notif])[$keys]=$values;
                    }                    
                }                    
            }
        }
        //var_dump($tabdatas['notif_file']);
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
 
        $tabdatas['nbre_inscri_attente'] = Admin::get_nbrepers_by(1,4,$tabdata_user->fk_iduniv);
        $tabdatas['nbre_paiement_attente'] = Admin::get_nbrepers_by(1,0,$tabdata_user->fk_iduniv);
        $tabdatas['nbre_active_attente'] = Admin::get_nbrepers_by(1,3,$tabdata_user->fk_iduniv);
        $tabdatas['nbre_etud_inscri'] = Admin::get_nbrepers_by(1,1,$tabdata_user->fk_iduniv);

        $tabdatas['eleve_conect'] = Admin::get_persconect_by(1,$tabdata_user->fk_iduniv);
        $tabdatas['prof_conect'] = Admin::get_persconect_by(2,$tabdata_user->fk_iduniv);
        $tabdatas['parent_conect'] = Admin::get_persconect_by(3,$tabdata_user->fk_iduniv);
        $tabdatas['admin_conect'] = Admin::get_persconect_by(4,$tabdata_user->fk_iduniv);

        //var_dump($tabdatas['eleve_conect']);
        //var_dump($tabdatas['prof_conect']);
        //var_dump($tabdatas['parent_conect']);
        //var_dump($tabdatas['admin_conect']);
        $table="annee_scolaire";
        $tb_conditions=[];
        $tb_conditions["fk_univ"]=$fk_iduniv;
        $tabdatas['all_annee'] = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
        //var_dump($tabdatas['all_annee']);


        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_Admin ::: accueilAction => " . $fct_exec;
		$log_user =" Page Accueil ";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/

        $_POST = NULL;
        $_GET = NULL;
        unset($_POST);
        unset($_GET);
        

        View::renderTemplate('Accueil/admin/admin_accueil.html', $tabdatas);
    }
    public function admin_comptabiliteAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        $fct_exec = "||";
        $tabdatas['menu'] = "Administration";
        $tabdatas['sousmenu'] = "Comptabilité";

        $id_univ = intval($tabdata_user->fk_iduniv);
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($id_univ);

        $tabdatas['entree_t'] = Comptable::get_entreeBy($id_univ);
        $tabdatas['sortie_t'] =Comptable::get_sortieBy($id_univ);
        $all_versem =Comptable::get_allVersementsBy($id_univ);
        foreach ($all_versem as $key => $value) {

            $table='classe';
            $tb_conditions=[];
            $tb_conditions['id_classe_classe']=$value['classe'];
            $classe = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
            if (!empty($classe) && $classe!=0) {
                $all_versem[$key]['classe_lib']=$classe[0]['libelle'];
            }
            else{$all_versem[$key]['classe_lib']="";}
            

            $table='niveau';
            $tb_conditions=[];
            $tb_conditions['id_niveau']=$value['niveau'];
            $niveau = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
            if (!empty($niveau) && $niveau!=0) {
                $all_versem[$key]['niveau_lib']=$niveau[0]['libelle_niveau'];
            }
            else{$all_versem[$key]['niveau_lib']="";}
            

            $get_elevegrpeBy =Comptable::get_elevegrpeBy($value['niveau'],$value['classe'],$value['id_eleve']);
            if (!empty($get_elevegrpeBy) && $get_elevegrpeBy!=0) {
                $all_versem[$key]['groupe_lib']=$get_elevegrpeBy[0]['groupe_libelle'];
            }
            else{$all_versem[$key]['groupe_lib']="";}
            
            //var_dump($classe,$niveau,$get_elevegrpeBy);

        }
        $tabdatas['all_versem']=$all_versem ;
        //var_dump($tabdatas['all_versem'][0]);
        $tabdatas['get_univ_scolarite'] =Comptable::get_univ_scolarite($id_univ);
        $tabdatas['get_univtype_scolarite'] =Comptable::get_univtype_scolarite($id_univ);
       
        //var_dump($tabdatas['get_univtype_scolarite'][0]);



        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_Admin ::: admin_comptabiliteAction => " . $fct_exec;
		$log_user =" Page Comptabilite ";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/

        $_POST = NULL;
        $_GET = NULL;
        unset($_POST);
        unset($_GET);
        

        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }
    public function user_activeAction()
    {

        //var_dump($_SESSION);
        //var_dump($_SESSION["page"]);
        //exit();
        //$resultat = User::setEtatPers(62,8,1,1,0);
        //var_dump($_SESSION["page"]);
        $fct_exec="|| ";

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $id_univ = $tabdata_user->fk_iduniv;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;

        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        //var_dump($tabdatas['univInfos']);
        $tabdatas['toast_notif']['etat'] = "success";
        $tabdatas['toast_notif']['infos'] = "Accès aux comptes en attente d'activation";

        $tabdatas['menu'] = "Administration";
        $tabdatas['sousmenu'] = "Activation de comptes";


        $tabdatas['getRole'] = Admin::getRole();

        $tabdatas['getAnneeScolaire'] = Admin::get_anneeBy($id_univ);
        //var_dump($tabdatas['getAnneeScolaire']);
        //get_anneeBy($fk_univ)
        $tabdatas['getAllGroupe'] = Admin::getAllGroupe($id_univ);
        //var_dump($tabdatas['getAnneeScolaire']);
        //var_dump($tabdatas['getAllGroupe']);
        //$tabdatas['getGroupeBy']=Admin::getGroupeBy($annee_id);

        $allCpteactive = modelUser::getAlluserBy(1, $id_univ);
        $tabdatas['allCpteElev'] = $allCpteactive;
        //var_dump( $tabdatas['allCpteElev']);
        $allCpteactive = modelUser::getAlluserBy(2, $id_univ);
        $tabdatas['allCpteProf'] = $allCpteactive;
        $allCpteactive = modelUser::getAlluserBy(3, $id_univ);
        $tabdatas['allCpteParent'] = $allCpteactive;
        $allCpteactive = modelUser::getAlluserBy(4, $id_univ);
        $tabdatas['allCpteAdmin'] = $allCpteactive;

        //GET all compte by anne
        $tabdatas['get_allactivecompte'] = Admin::get_allactivecompte($tabdata_user->lib_user_id_annee);
        //var_dump( $tabdatas['get_allactivecompte'][0]);
        /* ::: DEBUT :::: FONCTION COPIES ETAT USER TO TABLE etat_by_annee
             
            $table ="personne";
            $tb_conditions=[];
            //$tb_conditions["fk_iduniv"]=$id_univ;
            $tb_conditions["fk_idanneescol"]=$tabdata_user->lib_user_id_annee;
            $tabdatas['get_allcomptes_active'] = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
            $table="etat_by_annee";
            $tb_infos=[];
            $tb_conditions=[];
            //var_dump($tabdatas['get_allcomptes_active']);
            $table_ane="annee_scolaire"; 
            $tb_conditions_ane=[];
            $tb_conditions_ane["fk_univ"]=$fk_iduniv;
            $get_allanne = Admin::get_selectSQL_ALL_by($table_ane, $tb_conditions_ane);

            foreach ($tabdatas['get_allcomptes_active'] as $key => $value) {
                $tb_infos["fk_id_personne"]=$value["id_pers_personne"];
                $tb_infos["etat_pers"]=$value["etat_pers"];

                foreach ($get_allanne as $cle => $anne) {
                    $tb_conditions=[];
                    $tb_infos["fk_idanneescol"]=$anne["id_anscol_annee_scolaire"];    
                    $tb_conditions= $tb_infos;
                    $set_insertSQL = Admin::set_insertSQL($table,$tb_infos, $tb_conditions); 
                }

            }
    ::: FIN :::: FONCTION COPIES ETAT USER TO TABLE etat_by_annee */  
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_Admin ::: user_activeAction => " . $fct_exec;
		$log_user =" Page Gestion des comptes ";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/


        $_POST = NULL;
        $_GET = NULL;
        unset($_POST);
        unset($_GET);


        View::renderTemplate('Accueil/admin/admin_active_user.html', $tabdatas);
    }

    public function creeranneeAction()
    {
        $fct_exec = '||';
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $eschool_univ =  intval($tabdata_user->fk_iduniv);
        //var_dump( $tabdata_user );

        //**CREATION ANNEE :: Utilisateur Connecter /Admin/ et cree_anne_scol_btn
        if (isset($_POST["cree_anne_scol_btn"]) && isset($_SESSION['user']) && ($tabdata_user->type_pers == '4')) {
            $creer_annee = Admin::setAnneeScolaire($eschool_univ);
            $fct_exec = $fct_exec . ' setAnneeScolaire() ||';
            $tabdatas['creation_annee'] = $creer_annee;
            //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire( $eschool_univ);
            $tabdatas['allAnneeScolaire'] = Admin::getAnneeScolaireAllBy($eschool_univ);
        } else {
            //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire( $eschool_univ);
            $tabdatas['allAnneeScolaire'] = Admin::getAnneeScolaireAllBy($eschool_univ);
            //var_dump($tabdatas['allAnneeScolaire']);exit();
            //View::renderTemplate('Accueil/admin/admin_creerannee.html',$tabdatas);
        }

        $tabdatas['menu'] = "Administration";
        $tabdatas['sousmenu'] = "Années Scolaire";


        /*:::::::DEBUT Enregistrement des logs $fct_exec= '||'; ::::::::::*/
        $info = "Crt_Admin ::: creeranneeAction => " . $fct_exec;
        modeldb::set_AllLog($info);
        /*:::::::Fin Enregistrement des logs::::::::::*/

        $_POST = NULL;
        $_GET = NULL;
        unset($_POST);
        unset($_GET);

        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }
    //$tabdatas['menu']="Gestion des Classes";
    public function ceerclasseAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $tabdatas['toast_notif']['etat'] = "success";
        $tabdatas['toast_notif']['infos'] = "Acces à la Gestion des Fillières";
        //var_dump( $tabdatas);exit();
        $eschool_univ =  intval($tabdata_user->fk_iduniv);
        $tabdatas['get_all_departBy'] = Admin::get_all_departBy(intval($tabdata_user->fk_iduniv));



        //**CREATION ANNEE :: Utilisateur Connecter /Admin/ et cree_anne_scol_btn


        if (isset($_POST["cree_classe_btn"]) && isset($_POST["cree_classe_nom"]) &&  isset($_SESSION['user']) && ($_SESSION['user']['type_pers'] == '4' &&  isset($_POST['departement']))) {

            //var_dump($_POST); exit;

            $tabdatas['creation_classe'] = Admin::setclasse();
            if ($tabdatas['creation_classe'] == 1) {
                $tabdatas['toast_notif']['etat'] = "success";
                $tabdatas['toast_notif']['infos'] = "Filière " . $_POST["cree_classe_nom"] . " Créer!";
            } else {
                $tabdatas['toast_notif']['etat'] = "danger";
                $tabdatas['toast_notif']['infos'] = "Erreur  la création de la Fillière " . $_POST["cree_classe_nom"];
            }
        }
        if (isset($_POST["modif_classe_btn"]) && isset($_SESSION['user']) && ($_SESSION['user']['type_pers'] == '4')) {

            if (isset($_POST["select_modfi_classe"]) && isset($_POST["select_modfi_classe"]) != "" && isset($_POST["select_modfi_classe"]) != 0) {
                $tabdatas['modif_classe'] = Admin::setUpdateclasse();
                //var_dump($tabdatas['modif_classe']);
            }
        }


        if (isset($_POST["btn_sup_classe"])) {
            //var_dump($_POST);
            $id_classe = intval(htmlspecialchars($_POST["btn_sup_classe"]));
            //var_dump($id_classe );//exit;
            $tabdatas['sup_classe'] = Admin::setDeleteclasse($id_classe);
            //var_dump($tabdatas['sup_classe']);
            unset($_POST["btn_sup_classe"]);
        }



        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);
        //exit();

        //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();
        //$tabdatas['allmatiere']=Admin::getMatiere();
        $tabdatas['allmatiere'] = Admin::get_AllMatiereBy($eschool_univ);
        //$tabdatas['allclasses']=Admin::getClasses();
        $tabdatas['allclasses'] = Admin::getClassesBy($eschool_univ);

        //var_dump( intval($tabdata_user->fk_iduniv) );
        //var_dump( $tabdatas['allclasses'] );

        $tabdatas['menu'] = "Gestion des Fillières";
        $tabdatas['sousmenu'] = "Ajout et affichage de fillière";
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);


        //var_dump($tabdatas['allclasses']);
        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);


        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }
    //$tabdatas['menu']="Gestion des Classes";
    public function admin_repart_matAction()
    {

        //var_dump($_POST, $_GET);
        //exit;
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        //var_dump( $tabdata_user);
        $tabdatas['toast_notif']['etat'] = "success";
        $tabdatas['toast_notif']['infos'] = "Accès à la Repartition des matières";

        $tabdatas['tab_select'] = 1;

        $eschool_univ =  intval($tabdata_user->fk_iduniv);


        //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaireAllBy($eschool_univ);
        $tabdatas['allAnneeScolaire'] = Admin::get_anneeBy($eschool_univ);
        //var_dump($tabdatas['allAnneeScolaire']);
        //$tabdatas['allniveau']=Admin::getNiveau();
        $tabdatas['allniveau'] = Admin::getNiveauBy($eschool_univ);

        //**CREATION ANNEE :: Utilisateur Connecter /Admin/ et cree_anne_scol_btn
        //var_dump('INFOS',$_POST,$_GET);


        if (isset($_POST["modif_classe_btn"]) && isset($_SESSION['user']) && ($_SESSION['user']['type_pers'] == '4')) {

            if (isset($_POST["select_modfi_classe"]) && isset($_POST["select_modfi_classe"]) != "" && isset($_POST["select_modfi_classe"]) != 0  &&  isset($_POST["select_modfi_niveau"]) && isset($_POST["select_modfi_niveau"]) != "" && isset($_POST["select_modfi_niveau"]) != 0) {

                //$tabdatas['modif_classe'] = Admin::setUpdateclasse();
                $id_nivo = intval(htmlspecialchars($_POST["select_modfi_niveau"]));
                $id_filiere = intval(htmlspecialchars($_POST["select_modfi_classe"]));
                $tabdatas['modif_classe'] = Admin::set_filiereNivo_mat($id_nivo, $id_filiere);
                //var_dump($tabdatas['modif_classe']);
            }
        }

        /*************************************************************** */

        if (isset($_POST['select_repart_classe']) &&  isset($_POST['select_repart_niveau']) && $_POST['select_repart_niveau'] != "" &&  isset($_POST['select_repart_annee']) && $_POST['select_repart_annee'] != "") {

            //id_anscol_annee_scolaire//_//groupe_id//_//id_classe_classe (filliere)
            //$infos_tab = explode("_",$_POST['btn_admin_classe_voirmat']);
            //var_dump( $infos_tab);
            //$anneeid= intval( $infos_tab[0]);
            //var_dump($_POST);

            $tabdatas['tab_select'] = 2;


            $idnivo = intval(htmlspecialchars($_POST['select_repart_niveau']));
            $id_filiere = intval(htmlspecialchars($_POST['select_repart_classe']));
            $anneeid = intval(htmlspecialchars($_POST['select_repart_annee']));

            $tabdatas['select_repart_niveau'] = Admin::get_infosNiveauBy($idnivo, $eschool_univ);
            $tabdatas['select_repart_classe'] = Admin::get_infosClassesBy($id_filiere);

            $tabdatas['part_anneescol'] = Admin::getAnneeScolaireBy($anneeid);
            $tabdatas['get_mat_nivoFilBy'] = Admin::get_mat_nivoFilBy($idnivo, $id_filiere, $eschool_univ);



            if (isset($_POST['attrib_etap2_partannee']) &&  isset($_POST['attrib_etap2_mat']) && isset($_POST['attrib_etap2_mat_coef']) &&  isset($_POST['attrib_etap2_mat_credit']) &&  isset($_POST['attrib_etap2_mats1'])) {

                
                $attrib_etap2_partannee = intval(htmlspecialchars($_POST['attrib_etap2_partannee']));
                $attrib_etap2_mat = intval(htmlspecialchars($_POST['attrib_etap2_mat']));
                $attrib_etap2_mat_coef = intval(htmlspecialchars($_POST['attrib_etap2_mat_coef']));
                $attrib_etap2_mat_credit = intval(htmlspecialchars($_POST['attrib_etap2_mat_credit']));
                $attrib_etap2_mats1_parent = intval(htmlspecialchars($_POST['attrib_etap2_mats1']));

                if (htmlspecialchars($_POST['attrib_etap2_partannee']) == 'all') {
                    //var_dump($_POST);//exit();
                    $table="annee_partie";
                    $tb_conditions=[];
                    $tb_conditions['id_anneeScolaire']=$anneeid;
                    $tabdatas['part_annee_all'] = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
                    //var_dump($tabdatas['part_annee_all']);exit();
                    foreach ($tabdatas['part_annee_all'] as $key => $value) {
                        $tabdatas['set_filiere_matcoef'] = Admin::set_filiere_matcoef($attrib_etap2_mat, $attrib_etap2_mat_coef, $attrib_etap2_mats1_parent, $idnivo, $id_filiere, intval($value['id_annee_partie']), $anneeid, $attrib_etap2_mat_credit);
                    }
                }
                else{
                    //var_dump( $attrib_etap2_mat_coef);exit();
                    $tabdatas['set_filiere_matcoef'] = Admin::set_filiere_matcoef($attrib_etap2_mat, $attrib_etap2_mat_coef, $attrib_etap2_mats1_parent, $idnivo, $id_filiere, $attrib_etap2_partannee, $anneeid, $attrib_etap2_mat_credit);
                    //var_dump('set_filiere_matcoef', $tabdatas['set_filiere_matcoef']);

                }
   

            }


            if (isset($_POST['sup_fili_matcoef']) && $_POST['sup_fili_matcoef'] != "") {

                $id_filmatcoef =  intval(htmlspecialchars($_POST['sup_fili_matcoef']));
                $tabdatas['setDeletegrpMatCoef'] = Admin::setDelete_filMatCoef($id_filmatcoef);

                //var_dump('group_mat_sanscoef',$tabdatas['group_mat_sanscoef']);
            }




            $tabdatas['get_allidGrp_filiereGrpeBy'] = Admin::get_allidGrp_filiereGrpeBy($id_filiere, $idnivo, $anneeid);
            //var_dump('get_allidGrp_filiereGrpeBy', $tabdatas['get_allidGrp_filiereGrpeBy']);

            //exit();

            //var_dump($eschool_univ,$anneeid,$idnivo,$id_filiere);
            $tabdatas['get_filiereNiv_matcoef'] = Admin::get_filiereNiv_matcoef($eschool_univ, $anneeid, $idnivo, $id_filiere);

            $tabdatas['coefcedir']=[];

            if (!empty($tabdatas['get_allidGrp_filiereGrpeBy']) && !empty($tabdatas['get_filiereNiv_matcoef'])) {

                foreach ($tabdatas['get_filiereNiv_matcoef'] as $key => $value) {
                    foreach ($tabdatas['get_allidGrp_filiereGrpeBy'] as $cle => $info) {

                        $tabdatas['setGroupeMat_f'] = Admin::setGroupeMat_f($value['fk_matiere_id_tmp'], $value['coeficient_tmp'], $value['fk_matiere_parent_id_tmp'], $info['groupe_id'], $value['fk_part_annee_id_tmp'], $value['credit_tmp']);
                    }
                }

                foreach ($tabdatas['part_anneescol'] as $cle => $infos) {
                    foreach ($tabdatas['get_filiereNiv_matcoef'] as $key => $value) {
                    if ($infos['id_annee_partie'] == $value['fk_part_annee_id_tmp']) {

                            $id_anneet=intval($infos['id_annee_partie']);
                            if (empty(($tabdatas['coefcedir'][$id_anneet])['val'])) {
                                ($tabdatas['coefcedir'][$id_anneet])['val']=0;
                            }
                            if (empty(($tabdatas['coefcedir'][$id_anneet])['lib_anne'])) {
                                ($tabdatas['coefcedir'][$id_anneet])['lib_anne']='';
                            }

                            if (intval($value['coeficient_tmp']) != 0) {
                                ($tabdatas['coefcedir'][$id_anneet])['lib_anne']=$value['libele_partie'];
                                ($tabdatas['coefcedir'][$id_anneet])['val']=($tabdatas['coefcedir'][$id_anneet])['val'] + intval($value['coeficient_tmp']);

                            }
                            else {
                                ($tabdatas['coefcedir'][$id_anneet])['lib_anne']=$value['libele_partie'];
                                ($tabdatas['coefcedir'][$id_anneet])['val']=($tabdatas['coefcedir'][$id_anneet])['val'] + intval($value['credit_tmp']);
                            }


                            
                        }
                    }
                }

            }


            $tabdatas['get_filNiv_matcoef_WithMP'] = Admin::get_filNiv_matcoef_WithMP($eschool_univ, $anneeid, $idnivo, $id_filiere);

            if (!empty($tabdatas['get_allidGrp_filiereGrpeBy']) && !empty($tabdatas['get_filNiv_matcoef_WithMP'])) {

                foreach ($tabdatas['get_filNiv_matcoef_WithMP'] as $key => $value) {
                    foreach ($tabdatas['get_allidGrp_filiereGrpeBy'] as $cle => $info) {

                        $tabdatas['setGroupeMat_f'] = Admin::setGroupeMat_f($value['fk_matiere_id_tmp'], $value['coeficient_tmp'], $value['fk_matiere_parent_id_tmp'], $info['groupe_id'], $value['fk_part_annee_id_tmp'], $value['credit_tmp']);
                    }
                }


                foreach ($tabdatas['part_anneescol'] as $cle => $infos) {
                    foreach ($tabdatas['get_filNiv_matcoef_WithMP'] as $key => $value) {
                    if ($infos['id_annee_partie'] == $value['fk_part_annee_id_tmp']) {

                            $id_anneet=intval($infos['id_annee_partie']);
                            if (empty(($tabdatas['coefcedir'][$id_anneet])['val'])) {
                                ($tabdatas['coefcedir'][$id_anneet])['val']=0;
                            }
                            if (empty(($tabdatas['coefcedir'][$id_anneet])['lib_anne'])) {
                                ($tabdatas['coefcedir'][$id_anneet])['lib_anne']='';
                            }

                            if (intval($value['coeficient_tmp']) != 0) {
                                ($tabdatas['coefcedir'][$id_anneet])['lib_anne']=$value['libele_partie'];
                                ($tabdatas['coefcedir'][$id_anneet])['val']=($tabdatas['coefcedir'][$id_anneet])['val'] + intval($value['coeficient_tmp']);

                            }
                            else {
                                ($tabdatas['coefcedir'][$id_anneet])['lib_anne']=$value['libele_partie'];
                                ($tabdatas['coefcedir'][$id_anneet])['val']=($tabdatas['coefcedir'][$id_anneet])['val'] + intval($value['credit_tmp']);
                            }


                            
                        }
                    }
                }
            }
            
            //var_dump('get_filiereNiv_matcoef', $tabdatas['get_filiereNiv_matcoef']);
            //var_dump($tabdatas['coefcedir']);
            //var_dump('get_filNiv_matcoef_WithMP', $tabdatas['get_filNiv_matcoef_WithMP']);
        }

        /********************************************************* */
        $tabdatas['get_all_departBy'] = Admin::get_all_departBy($eschool_univ);
        $tabdatas['getNiveauBy'] = Admin::getNiveauBy($eschool_univ);
        //var_dump($tabdatas['getNiveauBy']);
        //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();
        //$tabdatas['allmatiere']=Admin::getMatiere();
        $tabdatas['allmatiere'] = Admin::get_AllMatiereBy($eschool_univ);
        //$tabdatas['allclasses']=Admin::getClasses();
        $tabdatas['allclasses'] = Admin::getClassesBy($eschool_univ);

        //var_dump( intval($tabdata_user->fk_iduniv) );
        //var_dump( $tabdatas['allclasses'] );

        $tabdatas['menu'] = "Repartition des matières";
        $tabdatas['sousmenu'] = "";
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);


        //var_dump($tabdatas['tab_select']);
        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);


        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }
    public function classe_emploiTpsAction()
    {
 
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        //var_dump($tabdata_user);
        $eschool_univ =  intval($tabdata_user->fk_iduniv);


        if (isset($_POST['attribution_emlploiTps_heuredebut'])  && isset($_POST['attribution_emlploiTps_heurefin'])) {

            $prog_anneescol=htmlspecialchars($_POST['prog_anneescol']);        
            $prog_groupe=htmlspecialchars($_POST['prog_groupe']);       
            $prog_periode=htmlspecialchars($_POST['prog_periode']);    
            $tb_periode=explode("_",$prog_periode); 

            $prog_freqce=htmlspecialchars($_POST['prog_freqce']);        
            $prog_datedebut=htmlspecialchars($_POST['prog_datedebut']);         
            $prog_datefin=htmlspecialchars($_POST['prog_datefin']);         
            $attribution_emlploiTps_mat=htmlspecialchars($_POST['attribution_emlploiTps_mat']);  
            $tb_prof_mat=explode("_",$attribution_emlploiTps_mat); 

            $Tps_salle=htmlspecialchars($_POST['attribution_emlploiTps_salle']);        
            $Tps_heuredebut=htmlspecialchars($_POST['attribution_emlploiTps_heuredebut']);       
            $Tps_heurefin=htmlspecialchars($_POST['attribution_emlploiTps_heurefin']);      



            switch ($prog_freqce) {
                case 'Aucune':
                    foreach ($tb_periode as $key => $value) {
                         $prog_periode = intval($value);
                        if ($prog_periode != 0){
                            $ajout_result=Crt_Admin::set_emploiTPs_gpe($prog_datedebut ,$tb_prof_mat[1] ,$Tps_salle ,$Tps_heuredebut ,$Tps_heurefin ,$tb_prof_mat[0],$prog_anneescol ,$prog_periode ,$prog_groupe ,$eschool_univ);
                        }
                    }
                        
                break;
                case 'Quotidienne':
                    //$text=0;
                    //var_dump($_POST,$prog_freqce,$tb_periode);
                    for ($dte=$prog_datedebut; $dte <= $prog_datefin; $dte = date("Y-m-d", strtotime("$dte +1 day"))) { 
                        //var_dump($prog_datedebut,$dte,$prog_datefin);
                        //var_dump('i=',$text);
                        foreach ($tb_periode as $key => $value) {
                            $prog_periode = intval($value);
                            if ($prog_periode != 0){
                                $ajout_result=Crt_Admin::set_emploiTPs_gpe($dte ,$tb_prof_mat[1] ,$Tps_salle ,$Tps_heuredebut ,$Tps_heurefin ,$tb_prof_mat[0],$prog_anneescol ,$prog_periode ,$prog_groupe ,$eschool_univ);
                                //var_dump('ajout_result',$ajout_result);
                            }
                        }
                        //$text++;
                    }
                    //exit();


                break;
                case 'Hebdomadaire':

                    for ($dte=$prog_datedebut; $dte <= $prog_datefin; $dte = date("Y-m-d", strtotime("$dte +7 day"))) { 

                        foreach ($tb_periode as $key => $value) {
                            $prog_periode = intval($value);
                            if ($prog_periode != 0){
                                $ajout_result=Crt_Admin::set_emploiTPs_gpe($dte ,$tb_prof_mat[1] ,$Tps_salle ,$Tps_heuredebut ,$Tps_heurefin ,$tb_prof_mat[0],$prog_anneescol ,$prog_periode ,$prog_groupe ,$eschool_univ);
                            }
                        }

                    }

                break;
                case 'Mensuelle':

                    for ($dte=$prog_datedebut; $dte <= $prog_datefin; $dte = date("Y-m-d", strtotime("$dte +30 day"))) { 

                        foreach ($tb_periode as $key => $value) {
                            $prog_periode = intval($value);
                            if ($prog_periode != 0){
                                $ajout_result=Crt_Admin::set_emploiTPs_gpe($dte ,$tb_prof_mat[1] ,$Tps_salle ,$Tps_heuredebut ,$Tps_heurefin ,$tb_prof_mat[0],$prog_anneescol ,$prog_periode ,$prog_groupe ,$eschool_univ);
                            }
                        }

                    }

                break;
                default:
                    # code...
                break;
            }
            //var_dump($prog_freqce,$ajout_result);//exit();
            //$tabdatas['setEmploiTps'] = Admin::setEmploiTpsByPost($eschool_univ);
        }

            
        $table='horaires';
        $fk_iduniv= intval($tabdata_user->fk_iduniv);
        $tb_cond['fk_iduniv']= $fk_iduniv;
        //var_dump( $tb_cond);//exit;
        $tabdatas['get_allhoraires'] = Admin::get_selectSQL_ALL_by($table,$tb_cond);
        //var_dump( $tabdatas['get_allhoraires']);//exit;

        //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();
        $tabdatas['allSalle'] = Admin::getAll_univSalle($tabdata_user->fk_iduniv);
        //var_dump( $tabdatas['allSalle']);
        $tabdatas['allProf'] = Admin::getProf_Byuniv($tabdata_user->fk_iduniv);
        //var_dump($tabdatas['allProf']);exit();
        //$tabdatas['univInfos']=Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($eschool_univ);
        $tabdatas['allannee'] = Admin::get_anneeBy($eschool_univ);

        $tabdatas['menu'] = "Gestion des Classes";
        $tabdatas['sousmenu'] = "Emploi du temps";

        //var_dump($tabdatas['allannee']);
        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);


        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public static function set_emploiTPs_gpe($emploitps_date ,$emploitps_id_mat ,$emploitps_salle ,$emploitps_h_debut ,$emploitps_h_fin ,$emploitps_id_prof ,$emploitps_anneescolaire ,$emploitps_periode ,$emploitps_groupe_id ,$var_iduniv){

            $tb_conditions=[];
            $tb_infos=[];
            $table='groupe_emploitps';
            $tb_infos['emploitps_date']= $emploitps_date;
            $tb_infos['emploitps_id_mat']= $emploitps_id_mat;
            $tb_infos['emploitps_salle']= $emploitps_salle ;
            $tb_infos['emploitps_h_debut']= $emploitps_h_debut ;
            $tb_infos['emploitps_h_fin']= $emploitps_h_fin ;
            $tb_infos['emploitps_id_prof']= $emploitps_id_prof ;
            $tb_infos['emploitps_anneescolaire']= $emploitps_anneescolaire ;
            $tb_infos['emploitps_periode']= $emploitps_periode ;
            $tb_infos['emploitps_groupe_id']= $emploitps_groupe_id ;
            $tb_infos['fk_iduniv']= $var_iduniv ;

            $tb_conditions= $tb_infos;
            //$tb_conditions['emploitps_id_mat']= $emploitps_id_mat;
            //$tb_conditions['emploitps_salle']= $emploitps_salle ;
            //$tb_conditions['emploitps_h_debut']= $emploitps_h_debut ;
            //$tb_conditions['emploitps_h_fin']= $emploitps_h_fin ;
            //$tb_conditions['emploitps_id_prof']= $emploitps_id_prof ;
            //$tb_conditions['emploitps_anneescolaire']= $emploitps_anneescolaire ;
            //$tb_conditions['emploitps_periode']= $emploitps_periode ;
            //$tb_conditions['fk_iduniv']= $var_iduniv ;

            return Admin::set_insertSQL($table,$tb_infos, $tb_conditions);

    }

    public function eval_autoAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $eschool_univ =  intval($tabdata_user->fk_iduniv);
        //var_dump($_POST);//exit();

        if (isset($_POST['attribution_emlploiTps_heuredebut'])  && isset($_POST['attribution_emlploiTps_heurefin'])) {
            $tabdatas['setEmploiTps'] = Admin::setEmploiTpsByPost($eschool_univ);

            //var_dump($tabdatas['setEmploiTps']);//exit();
        }

        if (isset($_POST['btn_set_evalProg'])  && isset($_POST['evalProg_date'])) {

            //var_dump($_POST);

            $evalProg_date = htmlspecialchars($_POST['evalProg_date']);
            $evalProg_salle = htmlspecialchars($_POST['evalProg_salle']);
            $evalProg_salle_heuredebut = htmlspecialchars($_POST['evalProg_salle_heuredebut']);
            $evalProg_salle_heurefin = htmlspecialchars($_POST['evalProg_salle_heurefin']);
            $evalProg_ideval = intval(htmlspecialchars($_POST['evalProg_ideval']));
            $evalProg_coef = intval(htmlspecialchars($_POST['evalProg_coef']));
            $evalProg_notation = intval(htmlspecialchars($_POST['evalProg_notation']));

            $tabdatas['setevalProg'] = Admin::setEvalProg_tps($evalProg_date, $evalProg_salle, $evalProg_salle_heuredebut, $evalProg_salle_heurefin, $evalProg_ideval, $evalProg_coef, $evalProg_notation);

            //var_dump($tabdatas['setevalProg']);//exit();
        }



        //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();
        $tabdatas['allSalle'] = Admin::getAll_univSalle($tabdata_user->fk_iduniv);
        //var_dump( $tabdatas['allSalle']);
        $tabdatas['allProf'] = Admin::getProf_Byuniv($tabdata_user->fk_iduniv);

        //var_dump($tabdatas['allProf']);exit();
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $tabdatas['allannee'] = Admin::get_anneeBy($tabdata_user->fk_iduniv);

        $tabdatas['menu'] = "Evaluations";
        $tabdatas['sousmenu'] = "Autorisations";

        //var_dump($tabdatas['allannee']);
        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }


    public function eval_moyenneAction()
    {
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $tabdatas['tab']=1;

        $tabdatas['menu'] = 'Examen et Evaluation';
        $tabdatas['sousmenu'] = "Affichages des moyennes";
        $fct_exec="";
        
        //var_dump($tabdatas);
        
        //var_dump($_POST);

        if (isset($_POST['moy_annee']) && isset($_POST['moy_classe']) && isset($_POST['moy_mat'])  && $_POST['moy_annee'] != 0  && $_POST['moy_classe'] != 0  && $_POST['moy_mat'] != 0  && $_POST['moy_annee'] != ""  && $_POST['moy_classe'] != ""  && $_POST['moy_mat'] != "" ) {

            $moy_annee = intval(htmlspecialchars($_POST['moy_annee']));
            $moy_grpe = intval(htmlspecialchars($_POST['moy_classe']));
            $moy_mat = intval(htmlspecialchars($_POST['moy_mat']));


            $tabdatas['sousmenu'] = "Affichages des moyennes > Liste des moyennes";

            $tabdatas['get_info_onMoy'] = Admin::get_allElv_grpmat_coef_by($moy_grpe,$moy_mat,$moy_annee);
            if (empty($tabdatas['get_info_onMoy']) || $tabdatas['get_info_onMoy']==0) {
                $tabdatas['get_info_onMoy'] = [];
            }
            $fct_exec=$fct_exec." Admin::get_allElv_grpmat_coef_by(".$moy_grpe.",".$moy_mat.",".$moy_annee.") ||";
            //var_dump($tabdatas['get_info_onMoy']);

            $table = 'annee_partie';
            $tb_conditions=[];
            $tb_conditions['id_anneeScolaire']=$moy_annee;
            $annee_partie = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
            //var_dump($annee_partie);exit();
            $tb_idelev=[];
            
            foreach ($annee_partie as $key_part => $val_part) {
                $tb_moy=[];
                $tmp_moy=0;
                //Parcours listes  all etudiants
               //var_dump($tabdatas['get_info_onMoy'],COUNT($tabdatas['get_info_onMoy']));exit();

                foreach ($tabdatas['get_info_onMoy'] as $key => $value) {    
                    
                    $test_idelep=intval($value['id_eleve']).'_'.intval($value['fk_part_annee']);
                    //Vérif si moyene eleve deja use et elev part anne =part anne in for
                    if ($value['fk_part_annee'] == $val_part['id_annee_partie'] && !(in_array($test_idelep,$tb_idelev))) {
                        array_push($tb_idelev,$test_idelep);
                        $tmp_moy=floatval($value['moyenne']);
                        //Recup de tout moyenne de elve si meme part annee et moy sup
                        foreach ($tabdatas['get_info_onMoy'] as $k_test => $val_test) {
                            if (  $value['id_eleve'] == $val_test['id_eleve'] && $val_test['fk_part_annee'] == $val_part['id_annee_partie'] && floatval($val_test['moyenne']) > $tmp_moy) {
                                $tmp_moy=floatval($val_test['moyenne']);
                            }
                        }
                        array_push($tb_moy,(float)$tmp_moy);
                        //var_dump('eleve : '.$value['nom_prenom'].' | moyenne: '.$tmp_moy.' | Part anee='.$value['fk_part_annee']);

                        $table = 'moyenne_matf';
                        $tb_conditions=[];
                        $tb_conditions['fk_id_eleve']=intval($value['id_eleve']);
                        $tb_conditions['fk_id_mat']=$moy_mat;
                        $tb_conditions['fk_id_partAnnee']=intval($value['fk_part_annee']);
                        $tb_conditions['fk_id_grpe']=$moy_grpe ;
                        $test_moymat = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
                        //var_dump('test_moymat',$test_moymat);
                        $tb_infos=[];
                        $tb_infos['moy_mat_final']=(float)$tmp_moy;
                        $tb_infos['fk_id_eleve']=intval($value['id_eleve']);
                        $tb_infos['fk_id_mat']=$moy_mat;
                        $tb_infos['fk_id_partAnnee']=intval($value['fk_part_annee']);
                        $tb_infos['fk_id_grpe']=$moy_grpe ;
                        if ($test_moymat==0) {
                            $test_moymat = Admin::set_insertSQL($table,$tb_infos, $tb_conditions);
                            //$fct_exec=$fct_exec.'Ajout Moyenne='.$tb_infos['moy_mat_final'].' De l eleve ='.$tb_conditions['fk_id_eleve'].' classe_mat_Pannee ='.$tb_infos['fk_id_grpe'].'_'.$tb_infos['fk_id_mat'].'_'.$tb_infos['fk_id_partAnnee'].' ||';
                        }
                        else {
                            $test_moymat = Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
                            //$fct_exec=$fct_exec.'Mise à jour Moyenne='.$tb_infos['moy_mat_final'].' De l eleve ='.$tb_conditions['fk_id_eleve'].' classe_mat_Pannee ='.$tb_infos['fk_id_grpe'].'_'.$tb_infos['fk_id_mat'].'_'.$tb_infos['fk_id_partAnnee'].' ||';
                        }
                    }  
                }

                array_multisort($tb_moy,SORT_DESC);
                //var_dump($tb_moy);exit();
                foreach ($tb_moy as $key => $value) {
                    $table = 'moyenne_matf';
                    $tb_conditions=[];
                    $tb_conditions['fk_id_mat']=$moy_mat;
                    $tb_conditions['fk_id_partAnnee']=intval($val_part['id_annee_partie']);
                    $tb_conditions['fk_id_grpe']=$moy_grpe ;
                    $tb_conditions['moy_mat_final']=(float)$value ;
                    $test_moymat = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
                    //var_dump('test_moymat',$test_moymat);exit();
                    if ($test_moymat !=0) {
                        foreach ($test_moymat as $cle => $val) {
                            //var_dump('moyenne ='.(float)$value);
                            $tb_infos=[];
                            $tb_conditions['fk_id_eleve']=intval($val['fk_id_eleve']) ;
                            $tb_infos['rang_mat']=$key+1;
                            $test_moymat = Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
                            //$fct_exec=$fct_exec.'Mise à jour rang='.$tb_infos['rang_mat'].' De l eleve ='.$tb_conditions['fk_id_eleve'].' ||';
                        }
                    }
                }
                $fct_exec=$fct_exec."| Mise à jour Moy, rang matiere= ".$moy_mat." du groupe".$moy_grpe." & periode=".intval($val_part['id_annee_partie']);
            }
            //var_dump($_POST);
            //exit();
           
            $table = 'annee_scolaire';
            $tb_conditions=[];
            $tabdatas['moy_annee'] = intval(htmlspecialchars($_POST['moy_annee']));
            $tb_conditions['id_anscol_annee_scolaire']=$tabdatas['moy_annee'];
            $annee_infos = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
            //var_dump($annee_infos);
            
            $tabdatas['moy_classe'] = intval(htmlspecialchars($_POST['moy_classe']));
            $table = 'groupe';
            $tb_conditions=[];
            $tb_conditions['groupe_id']=$tabdatas['moy_classe'];
            $gpre_infos = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
            //var_dump($gpre_infos);


            $tabdatas['moy_mat'] = intval(htmlspecialchars($_POST['moy_mat']));
            $table = 'matiere';
            $tb_conditions=[];
            $tb_conditions['id_matiere_matiere']=$tabdatas['moy_mat'];
            $matiere_infos = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
            //var_dump($matiere_infos);


            $tabdatas['moy_annee_lib'] = $annee_infos[0]['annee_libelle'];
            $tabdatas['moy_classe_lib'] = $gpre_infos[0]['groupe_libelle'];
            $tabdatas['moy_mat_lib'] =$matiere_infos[0]['libele'];
        
            $tabdatas['moy_annee'] = $moy_annee ;
            $tabdatas['moy_classe'] = $moy_grpe ;
            $tabdatas['moy_mat'] = $moy_mat ;

        }
        elseif (isset($_POST['elev_moy_annee']) && isset($_POST['elev_moy_classe']) && isset($_POST['elev_moy_id']) && isset($_POST['elev_btn_afficheMoy']) ) {
            //var_dump($_POST);exit();
            $elev_moy_annee = intval(htmlspecialchars($_POST['elev_moy_annee']));
            $elev_moy_classe = intval(htmlspecialchars($_POST['elev_moy_classe']));
            $elev_moy_id = intval(htmlspecialchars($_POST['elev_moy_id']));

            $tabdatas['get_eleve_all_moy'] = Admin::get_eleve_all_moy($elev_moy_id,$elev_moy_annee,$elev_moy_classe);
            $fct_exec=$fct_exec."Admin::get_eleve_all_moy(".$elev_moy_id.",".$elev_moy_annee.",".$elev_moy_classe.") ||";

            //var_dump($tabdatas['get_eleve_all_moy']);

            $tabdatas['tab']=2;

            $tabdatas['get_elevBy'] = Admin::get_elevBy($elev_moy_id);
            //var_dump($tabdatas['get_eleve_all_moy']);

            $table = 'annee_scolaire';
            $tb_conditions=[];
            $tb_conditions['id_anscol_annee_scolaire']=$elev_moy_annee;
            $annee_infos = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
            
            $table = 'groupe';
            $tb_conditions=[];
            $tb_conditions['groupe_id']=$elev_moy_classe;
            $gpre_infos = Admin::get_selectSQL_ALL_by($table, $tb_conditions);

            $tabdatas['elve_annee_lib'] = $annee_infos[0]['annee_libelle'];
            $tabdatas['elve_classe_lib'] = $gpre_infos[0]['groupe_libelle'];
        }

        
        //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();
        $tabdatas['allsession'] = Prof::getSession();
        //$fct_exec=$fct_exec. "Prof::getSession() || ";
        $tabdatas['allProf'] = Admin::getProf_Byuniv($tabdata_user->fk_iduniv);
        //$fct_exec=$fct_exec. "Admin::getProf_Byuniv(".$tabdata_user->fk_iduniv.") || ";
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        //$fct_exec=$fct_exec. "Admin::get_UnivInfosBy(".$tabdata_user->fk_iduniv.") || ";
        $tabdatas['allannee'] = Admin::get_anneeBy($tabdata_user->fk_iduniv);
        //$fct_exec=$fct_exec. "Admin::get_anneeBy(".$tabdata_user->fk_iduniv.") || ";

        //:::::::::::::LOGS::::::::::::::::::
        $info = "Crt_Admin ::: Action : eval_moyenneAction => " . $fct_exec;
        $log_user ="Menu => Examen et Evaluation => Moyennes";
        //var_dump($info,$log_user);
        modeldb::set_Ajax_Log($info,$log_user,$tabdata_user->id_pers_personne,$tabdata_user->fk_iduniv);
        //:::::::::::::LOGS::::::::::::::::::


        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }


    public function classe_attrib_filliereAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $fct_exec = "||";
        //var_dump($tabdatas);

        $eschool_univ =  intval($tabdatas['fk_iduniv']);
        $tabdatas['menu'] = "Gestion des Classes";

        //var_dump($_POST, $_GET);
        if (isset($_POST['btn_admin_classe_voirmat'])) {
             $tabdatas['btn_admin_classe_voirmat'] = htmlspecialchars($_POST['btn_admin_classe_voirmat']);
             //var_dump($tabdatas['btn_admin_classe_voirmat']);
        }
        //$tabdatas['allmatiere']=Admin::getMatiere();
        $tabdatas['allmatiere'] = Admin::get_AllMatiereBy($eschool_univ);
        //$tabdatas['allclasses'] = Admin::getClasses();
        $tabdatas['allclasses'] = Admin::getClassesBy($eschool_univ);
        $tabdatas['allAnneeScolaire'] = Admin::get_anneeBy($eschool_univ);
        //var_dump($tabdatas['allAnneeScolaire']);
        //$tabdatas['allniveau']=Admin::getNiveau();
        $tabdatas['allniveau'] = Admin::getNiveauBy($eschool_univ);
        //var_dump($_POST);
        if (isset($_POST['sup_groupe_mat']) && isset($_POST['supMat_groupe_matiere_coef_id']) && $_POST['supMat_groupe_matiere_coef_id'] != "") {

            if (isset($_SESSION['anneeid']) && isset($_SESSION['groupeid']) && isset($_SESSION['classeid'])) {

                $tabdatas['setDeletegrpMatCoef'] = Admin::setDeletegrpMatCoef(intval(htmlspecialchars($_POST['supMat_groupe_matiere_coef_id'])));
                //var_dump( $tabdatas['setDeletegrpMatCoef'] );

                $tabdatas['classe_voirmat'] = Admin::getGrpMatiereNonRepartiByPartie($_SESSION['groupeid'],  $_SESSION['classeid']);
                $tabdatas['group_partAnnee'] = Admin::getPartAnneeByGroup($_SESSION['groupeid']);
                $tabdatas['group_mat_sanscoef'] = Admin::getMatiereSansPartie_SansCoef($_SESSION['groupeid'], $_SESSION['classeid']);
                $tabdatas['group_mat_sousMat'] = Admin::getClasseMatiereBy($_SESSION['classeid']);
                $tabdatas['idgroup'] = $_SESSION['groupeid'];
                $tabdatas['idclasse'] = $_SESSION['classeid'];

                //var_dump('group_mat_sanscoef',$tabdatas['group_mat_sanscoef']);

                $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
                $tabdatas['sousmenu'] = "Attribuer une fillière > Ajout & Liste des matières par période";
                $_SESSION['page']='admin_classe_matBYperios';
            }
        } 

        if (isset($_POST['btn_admin_classe_voirmat']) && $_POST['btn_admin_classe_voirmat'] != "") {

            //id_anscol_annee_scolaire//_//groupe_id//_//id_classe_classe (filliere)
            $infos_tab = explode("_", $_POST['btn_admin_classe_voirmat']);
            //var_dump( $infos_tab);
            $anneeid = intval($infos_tab[0]);
            $groupeid = intval($infos_tab[1]);
            $classeid = intval($infos_tab[2]);
            $groupe_libelle = ($infos_tab[3]);

            $_SESSION['anneeid'] = $anneeid;
            $_SESSION['groupeid'] = $groupeid;
            $_SESSION['classeid'] = $classeid;

            $tabdatas['group_partAnnee'] = Admin::getPartAnneeByGroup($groupeid);

            $tabdatas['classe_voirmat'] = Admin::getGrpMatiereNonRepartiByPartie($groupeid, $classeid);


            $tabdatas['group_mat_sanscoef'] = Admin::getMatiereSansPartie_SansCoef($groupeid, $classeid);
            $tabdatas['group_mat_sousMat'] = Admin::getClasseMatiereBy($classeid);

            //********************************************************************** */

            $tabdatas['get_grp_MatRepartie_By'] = Admin::get_grp_MatRepartie_By($eschool_univ, $groupeid, $anneeid);
            //var_dump($tabdatas['get_grp_MatRepartie_By']);
            $fct_exec = $fct_exec . " get_grp_MatRepartie_By(" . $eschool_univ . "," . $groupeid . "," . $anneeid . ");  || ";
            $tabdatas['get_grp_MatRepartie_WithMP_By'] = Admin::get_grp_MatRepartie_WithMP_By($eschool_univ, $groupeid, $anneeid);
            //var_dump($tabdatas['get_grp_MatRepartie_WithMP_By']);
            $fct_exec = $fct_exec . " get_grp_MatRepartie_WithMP_By(" . $eschool_univ . "," . $groupeid . "," . $anneeid . ");  || ";

            //********************************************************************** */


            $tabdatas['idgroup'] = $groupeid;
            $tabdatas['groupe_libelle'] = $groupe_libelle;
            $tabdatas['idclasse'] = $classeid;

            //var_dump($tabdatas['group_mat_sanscoef']);
            //exit();
            $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
            $tabdatas['sousmenu'] = "Attribuer une fillière > Ajout & Liste des matières par période";


            /*:::::::DEBUT Enregistrement des logs::::::::::*/
            $info = "Crt_Admin ::: classe_attrib_filliereAction => " . $fct_exec;
            modeldb::set_AllLog($info);
            /*:::::::Fin Enregistrement des logs::::::::::*/
            $_SESSION['page']= 'admin_classe_matBYperios';

        } 
        else {
            //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();
            $tabdatas['allGroupe'] = Admin::getAllGroupe($eschool_univ);
            //var_dump($tabdatas['allGroupe']);
            $_POST = NULL;
            $_GET = NULL;
            $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
            $tabdatas['sousmenu'] = "Attribuer une fillière";
            //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);
            
        }

        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);

    }
    public function admin_classe_infosAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        $fct_exec = "||";
        //var_dump($tabdatas);
        $eschool_univ =  intval($tabdatas['fk_iduniv']);
        $tabdatas['menu'] = "Gestion des Classes";
        $tabdatas['sousmenu'] = "Classe informations";

        $table="annee_scolaire";
        $tb_conditions=[];
        $tb_conditions["fk_univ"]=$eschool_univ;
        $tabdatas['annee_scolaire'] = Admin::get_selectSQL_ALL_by($table, $tb_conditions);

        //var_dump($_POST, $_GET);
        if (isset($_POST['btn_voir_dmd']) && isset($_POST['anneeScolaire'])) {
            $id_anneescol = intval(htmlspecialchars($_POST['anneeScolaire']));

            $groupe = Admin::get_grpeliste_By($eschool_univ,$id_anneescol);
            //var_dump($groupe);
            if (!empty($groupe)) {
                foreach ($groupe as $key => $value) {
                    //var_dump("---------------- = ".$key);
                    //var_dump($value);
                    $groupeinfo = Admin::get_grpinfo_tmat_tcoef(intval($value['groupe_id']));
                    $effectif = Admin::get_grpinfo_tfm(intval($value['groupe_id']));
                    $get_classe_prof = Admin::get_classe_prof(intval($value['groupe_id']));
                    $get_classe_pp = Admin::get_classe_pp(intval($value['groupe_id']));
                    //var_dump($get_classe_pp);
                    ($tabdatas['groupe'][$key])['groupe_libelle']=$value['groupe_libelle'];
                    ($tabdatas['groupe'][$key])['groupe_id']=$value['groupe_id'];
                    ($tabdatas['groupe'][$key])['mat_coef']=$groupeinfo;
                    ($tabdatas['groupe'][$key])['effectif']=$effectif;
                    ($tabdatas['groupe'][$key])['liste_prof']=$get_classe_prof;
                    ($tabdatas['groupe'][$key])['classe_pp']=$get_classe_pp;
                    
                }
                //var_dump($tabdatas['groupe']);
            }
            
        }

        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);

        $_POST = NULL;
        $_GET = NULL;
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_Admin ::: admin_classe_infosAction => " . $fct_exec;
		$log_user =" Classe informations";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/

        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);

    }
    public function prof_listAllAction()
    {
        
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();
        $tabdatas['allProf'] = Admin::getProf_Byuniv($tabdata_user->fk_iduniv);
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        //var_dump($_POST);//exit();
        $tabdatas['menu'] = "Gesion des Professeurs";
        $tabdatas['sousmenu'] = "Liste des professeurs";

        //$tabdatas['allclasses']=Admin::getClasses();
        if (isset($_POST['btn_setuser'])) {

            //var_dump($_POST);

            $id_prof = intval(htmlspecialchars($_POST['id_prof']));
            $change_user=Model_public::change_user($id_prof,2);
            //exit();

 
        }

        //var_dump($tabdatas['allProf']);
        foreach ($tabdatas['allProf'] as $key => $value) {

            ($tabdatas['allProf'][$key])['allProfMat'] = Prof::getProfMat($value['id_prof_prof']);
            //var_dump(($tabdatas['allProf'][$key])['allProfMat']);
            ($tabdatas['allProf'][$key])['allProfGroupe'] = Prof::getProfGroupe($value['id_prof_prof']);
            //var_dump(($tabdatas['allProf'][$key])['allProfGroupe']);
        }
        //var_dump($tabdatas['allProf']);
        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);

        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }
    public function prof_infosAction()
    {
        //var_dump($_POST);exit;
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $eschool_univ =  intval($tabdata_user->fk_iduniv);
        
        $id_pers = $tabdata_user->id_pers_personne;
        $fct_exec = "||";

        //var_dump($tabdata_user);//exit();
        $id_prof = intval(htmlspecialchars($_POST['id_prof']));
        $tab_info_proftmp =  Prof::getUniqProf($id_prof);
        $tab_info_prof = (object)$tab_info_proftmp[0];
        //var_dump($tab_info_prof);//exit();
        $filenameprof = '../files/' . $tab_info_prof->prof_id_pers . '/' . $tab_info_prof->prof_id_pers . '.jpg';

        if (file_exists($filenameprof)) {
            $tabdatas['lien_photo_prof'] = $tabdatas['liens'] . $tab_info_prof->prof_id_pers . '/' . $tab_info_prof->prof_id_pers . '.jpg';
        } else {
            $tabdatas['lien_photo_prof'] = $tabdatas['base_liens'] . 'public/assets/img/m.png ';
        }

        $tabdatas['prof_menu'] = "prof_menu_info";

        //var_dump($tab_info_prof->id_prof_prof);exit();

        if (isset($tab_info_prof->id_prof_prof)) {

            $tabdatas['prof_info'] = "success";
            $tabdatas['nom_prenom_prof'] = $tab_info_prof->nom_prenom;
            $tabdatas['sexe_prof'] = $tab_info_prof->sexe;
            $tabdatas['email_prof'] = $tab_info_prof->email;
            $tabdatas['contact_prof'] = $tab_info_prof->contact;
            $tabdatas['id_prof'] = $tab_info_prof->id_prof_prof;
            $tabdatas['prof_id_pers'] = $tab_info_prof->prof_id_pers;
        } else {
            $tabdatas['prof_info'] = "erreur";
        }

        /*
            if (isset($_POST['id_matiere']) && isset($_POST['id_prof']) && isset($_POST['ajout_prof_mat'])) {

                $id_prof = intval(htmlspecialchars($_POST['id_prof']));
                $id_matiere = intval(htmlspecialchars($_POST['id_matiere']));
                $tabdatas['profSetMat_result'] = Prof::setProfMat($id_matiere, $id_prof);
                //var_dump($tabdatas['profSetMat_result']);
                unset($_POST['id_matiere']);
                unset($_POST['ajout_prof_mat']);
                if ($tabdatas['profSetMat_result'] == 'ajouter') {
                    $tabdatas['toast_notif']['etat'] = "success";
                    $tabdatas['toast_notif']['infos'] = "Groupe suprimer";
                } else {
                    $tabdatas['toast_notif']['etat'] = "danger";
                    $tabdatas['toast_notif']['infos'] = "Erreur d'attibution de matière au professeur";
                }

                $tabdatas['prof_menu'] = "prof_menu_mat";
            } elseif (isset($_POST['idmat']) && isset($_POST['id_prof']) && isset($_POST['btn_sup_prof_mat'])) {

                $id_prof = intval(htmlspecialchars($_POST['id_prof']));
                $id_matiere = intval(htmlspecialchars($_POST['idmat']));
                $tabdatas['profSetSupMat_result'] = Prof::setSupProfMat($id_matiere, $id_prof);
                //var_dump($tabdatas['profSetSupMat_result']);
                unset($_POST['idmat']);
                unset($_POST['btn_sup_prof_mat']);


                $tabdatas['prof_menu'] = "prof_menu_mat";
            } 
            elseif (isset($_POST['id_prof']) && isset($_POST['ajout_profgroupe'])) {

                //var_dump($_POST);exit;

                $id_prof = intval(htmlspecialchars($_POST['id_prof']));
                $id_groupe = intval(htmlspecialchars($_POST['id_groupe']));
                $id_prof_mmat = intval(htmlspecialchars($_POST['id_prof_mmat']));
                $tabdatas['add_matgpre_prof'] = Prof::setProfGroupe($id_groupe, $id_prof, $id_prof_mmat);
                //var_dump($tabdatas['allProfGroupe']);

                unset($_POST['id_groupe']);
                unset($_POST['id_prof_mmat']);
                unset($_POST['ajout_profgroupe']);

                $tabdatas['prof_menu'] = "prof_menu_groupe";
            }
        */

        if( isset($_POST['ajout_profgroupe_all']) && isset($_POST['id_groupe_all']) && isset($_POST['id_prof_mmat_all']) && isset($_POST['id_prof']) ){

            if (!empty($_POST['id_groupe_all']) && $_POST['id_groupe_all'] != "") {
                foreach ($_POST['id_groupe_all'] as $key => $value) {
                    $id_groupe = intval(htmlspecialchars($value));
                    
                    if (!empty($_POST['id_prof_mmat_all']) && $_POST['id_prof_mmat_all'] != "") {
                        foreach ($_POST['id_prof_mmat_all'] as $keys => $values) {
                            $id_prof_mmat = intval(htmlspecialchars($values));
                            $tabdatas['add_matgpre_prof'] = Prof::setProfGroupe($id_groupe, $id_prof, $id_prof_mmat);
                            //var_dump($tabdatas['allProfGroupe']);
                            $tabdatas['profSetSupMat_result'] = Prof::setProfMat($id_prof_mmat, $id_prof);
                        }
                    }
                }
            }
  
            $tabdatas['toast_notif']['etat'] = "success";
            $tabdatas['toast_notif']['infos'] = "Mise à jour éffecuée";

            $tabdatas['prof_menu'] = "4";
        }
        elseif (isset($_POST['id_groupe']) && isset($_POST['id_prof']) && isset($_POST['btn_sup_prof_groupe'])) {

            $id_groupe = intval(htmlspecialchars($_POST['id_groupe']));
            $id_mat = intval(htmlspecialchars($_POST['id_mat']));
            $tabdatas['result'] = Prof::setSupProfGroupe($id_groupe, $id_prof, $id_mat);
            unset($_POST['idmat']);
            unset($_POST['btn_sup_prof_groupe']);
            if ($tabdatas['result'] == 'supprimer') {
                $tabdatas['toast_notif']['etat'] = "success";
                $tabdatas['toast_notif']['infos'] = "Groupe suprimer";
            } else {
                $tabdatas['toast_notif']['etat'] = "danger";
                $tabdatas['toast_notif']['infos'] = "Erreur de suppression du Groupe";
            }

            $tabdatas['prof_menu'] = "4";
        } 

        $tabdatas['menu'] = "Gesion des Professeurs";
        $tabdatas['sousmenu'] = "Informations sur le professeur";

        $tabdatas['allProfMat'] = Prof::getProfMat($id_prof);
        $tabdatas['get_AllMatiereBy'] = Admin::get_AllMatiereBy($eschool_univ);
        //var_dump($tabdatas['get_AllMatiereBy']);

        //$tabdatas['allmatiere']=Admin::getMatiere();
        $tabdatas['allmatiere'] = Admin::get_AllMatiereBy($eschool_univ);

        $tabdatas['allProfGroupe'] = Prof::getProfGroupe($id_prof);
        $tabdatas['allGroupe'] = Admin::getAllGroupe($eschool_univ);

        //var_dump($tabdatas['allProfGroupe']);//exit();

        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);

        $_POST = NULL;
        $_GET = NULL;
        
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_Admin ::: prof_infosAction => " . $fct_exec;
		$log_user =" Page Information du professeur ";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$eschool_univ);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/

        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);
        $_SESSION['page'] = 'admin_prof_infos';
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function ceerSalleAction()
    {
        $fct_exec = "||";
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $eschool_univ =  intval($tabdata_user->fk_iduniv);

        if (isset($_POST["cree_salle_btn"])) {
            //var_dump($_POST);exit();
            $cree_salle_code = htmlspecialchars($_POST["cree_salle_code"]);
            $cree_salle_titre = htmlspecialchars($_POST["cree_salle_titre"]);
            $cree_salle_infos = htmlspecialchars($_POST["cree_salle_infos"]);
            $tabdatas['set_SalleBy'] = Admin::set_SalleBy($cree_salle_code, $cree_salle_titre, $cree_salle_infos, $eschool_univ);
            $fct_exec = "setSalle(" . $eschool_univ . ") ||";
        }
        //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();
        $tabdatas['allsalle'] = Admin::getAll_univSalle($eschool_univ);
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($eschool_univ);
        $tabdatas['menu'] = "Gestion des Salles";
        $tabdatas['sousmenu'] = "Création de Salle";


        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_Admin ::: ceerSalleAction => " . $fct_exec;
        modeldb::set_AllLog($info);
        /*:::::::Fin Enregistrement des logs::::::::::*/

        //var_dump($_POST,$tabdatas);
        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }
    public function ceerSalle_ListeAction()
    {
        $fct_exec = "||";
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $eschool_univ =  intval($tabdata_user->fk_iduniv);

        //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();
        $tabdatas['allsalle'] = Admin::getAll_univSalle($eschool_univ);
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($eschool_univ);
        $tabdatas['menu'] = "Gestion des Salles";
        $tabdatas['sousmenu'] = "Création de Salle";

        //var_dump($_POST,$tabdatas);
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_Admin ::: ceerSalle_ListeAction => " . $fct_exec;
        modeldb::set_AllLog($info);
        /*:::::::Fin Enregistrement des logs::::::::::*/
        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function salle_progAction()
    {
        $fct_exec = "||";
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $eschool_univ =  intval($tabdata_user->fk_iduniv);
        //var_dump( $eschool_univ );
        //$tabdatas['allsalle']=Admin::getAllSalle();
        if (isset($_POST['btn_salle_affiche']) &&  isset($_POST['salle_date']) && $_POST['salle_date'] != "") {

            $filtre_date = htmlspecialchars($_POST['salle_date']);

            $id_anneescol = intval(Admin::get_idanneeByY());
            //var_dump($eschool_univ,$filtre_date);

            $tabdatas['get_salle_emplTpsBy'] = Admin::get_salle_emplTpsBy($eschool_univ, $filtre_date);
            $tabdatas['allsalle'] = Admin::getAll_univSalle($eschool_univ);
            $tabdatas['dateProg'] = $filtre_date;

            $fct_exec = $fct_exec . "get_salle_emplTpsBy (" . $eschool_univ . ", " . $filtre_date . ")||";
            //var_dump($filtre_date,$tabdatas['get_salle_emplTpsBy'],$tabdatas['allsalle']);

        }



        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($eschool_univ);

        $tabdatas['menu'] = "Gestion des Salles";
        $tabdatas['sousmenu'] = "Etat des Salles";


        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_Admin ::: salle_progAction => " . $fct_exec;
        modeldb::set_AllLog($info);
        /*:::::::Fin Enregistrement des logs::::::::::*/
        //var_dump($tabdatas['allclasses']);
        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);


        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function ceermatiereAction()
    {
        $tabdatas = [];
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $eschool_univ =  intval($tabdata_user->fk_iduniv);
        //**CREATION ANNEE :: Utilisateur Connecter /Admin/ et cree_anne_scol_btn
        if (isset($_POST["cree_matiere_btn"]) && isset($_SESSION['user']) && ($_SESSION['user']['type_pers'] == '4')) {
            $tabdatas['cree_matiere_etat'] = Admin::setMatiere($eschool_univ);
        } else {
            //var_dump($tabdatas['allAnneeScolaire']);exit();
            //View::renderTemplate('Accueil/admin/admin_creerannee.html',$tabdatas);
        }
 
        if (isset($_POST) && !empty($_POST) && isset($_POST['btn_matimg']) && isset($_POST['id_matiere'])) {
            $id_mat = intval($_POST['id_matiere']);
            //var_dump($_POST);
            //var_dump($_FILES,$_FILES['photo_mat']['tmp_name']);
            $path_parts = pathinfo($_FILES['photo_mat']['tmp_name']);
            $img_tmp_name = $_FILES['photo_mat']['tmp_name'];
            //var_dump(mime_content_type($img_tmp_name));
            //exit();
            $reduction = 400;
            $img_final_name = $id_mat;
            //$extension_upload = $path_parts['extension'];
            $extension_upload = 'jpg';

            $chemin_destination = '../BanqueDefichiers/matiere/';

            if (is_dir($chemin_destination)) {
                $fichier =  $chemin_destination . $id_mat . '.jpg';
                if (file_exists($fichier)) {
                    unlink($fichier);
                }
            } else {
                mkdir($chemin_destination, 0777);
            }

            $result_User_Img = User_Img::create_jpeg_imgMini_save($img_tmp_name, $reduction, $chemin_destination, $img_final_name, $extension_upload);

            //echo $result_User_Img;
        }

        //$tabdatas['allmatiere']=Admin::getMatiere($univ);
        $tabdatas['allmatiere'] = Admin::get_AllMatiereBy($eschool_univ);
        $tabdatas['menu'] = "Administration";
        $tabdatas['sousmenu'] = "Matières";


        foreach ($tabdatas['allmatiere'] as $key => $value) {
            $fichier =  '../BanqueDefichiers/matiere/' . intval($value['id_matiere_matiere']) . '.jpg';
            if (file_exists($fichier)) {
                ($tabdatas['allmatiere'][$key])['liens'] = "/BanqueDefichiers/matiere/" . intval($value['id_matiere_matiere']) . '.jpg';
            }
        }

        //var_dump($tabdatas['allmatiere']);

        $_POST = NULL;
        $_GET = NULL;
        unset($_POST);
        unset($_GET);
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function attributionAction()
    {



        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $eschool_univ =  intval($tabdata_user->fk_iduniv);

        //$tabdatas['allmatiere']=Admin::getMatiere();
        $tabdatas['allmatiere'] = Admin::get_AllMatiereBy($eschool_univ);
        //$tabdatas['allclasses'] = Admin::getClasses();
        $tabdatas['allclasses'] = Admin::getClassesBy($eschool_univ);
        $tabdatas['allAnneeScolaire'] = Admin::get_anneeBy($eschool_univ);




        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }
    //$tabdatas['menu']="Gestion des Etudiants";
    public function admin_eleve_classeAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;

        $tabdatas['allUniqAnneeScolaire'] = Admin::get_anneeBy($tabdata_user->fk_iduniv);
        //var_dump( $tabdatas['allUniqAnneeScolaire']);

        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);

        $tabdatas['menu'] = "Gestion des Etudiants";
        $tabdatas['sousmenu'] = "Affectation à une classe";


        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);


        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function eleve_parents()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;

        $tabdatas['allUniqAnneeScolaire'] = Admin::get_anneeBy($tabdata_user->fk_iduniv);
        //var_dump( $tabdatas['allUniqAnneeScolaire']);

        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);

        $tabdatas['menu'] = "Gestion des parents";
        $tabdatas['sousmenu'] = " ";


        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);


        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function parent_listAllAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;

        //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);

        $tabdatas['menu'] = "Gestion des parents";


        //var_dump($_POST);
        if (isset($_POST['btn_setuser'])) {

            //var_dump($_POST);
            //var_dump($_POST);exit();

            $id_parent = intval(htmlspecialchars($_POST['btn_setuser']));
            $change_user=Model_public::change_user($id_parent,3);
            //exit();

 
        }

        if (isset($_POST['input_parent_id']) && $_POST['input_parent_id'] != "" && $_POST['input_parent_id'] != NULL) {


            $id_parent = intval(htmlspecialchars($_POST['input_parent_id']));
            $tabdatas['get_AllParentsBy'] = Admin::get_AllParentsBy($id_parent);
            //var_dump($tabdatas['get_AllParentsBy']);
            $tabdatas['parent_list_enfts'] = [];

            if ($tabdatas['get_AllParentsBy'] != NULL) {

                $id_pers_parent = intval(($tabdatas['get_AllParentsBy'][0])['id_pers_personne']);
                $file = $tabdatas['liens'] . $id_pers_parent . '/' . $id_pers_parent . '.jpg';
                $test_file = @fopen($file, 'r');

                //var_dump($test_file);
                //var_dump($file);

                if ($test_file) {
                    $tabdatas['lien_photo_parent'] =  $file;
                } else {
                    //var_dump("$file not found");
                }

                $matricule_eleves = explode(";", ($tabdatas['get_AllParentsBy'][0])['matricule']);
                //var_dump($matricule_eleves);

                foreach ($matricule_eleves as $key => $value) {
                    //var_dump($value);
                    $tmp_elev = Admin::get_elevBymatricule($value);
                    //var_dump($tmp_elev);

                    if (isset($tmp_elev) && $tmp_elev != NULL) {
                        array_push($tabdatas['parent_list_enfts'], $tmp_elev);
                    }
                }


                // set_Update_parent_enfant($id_parent, $id_enfant,$etat_pe)
                if (isset($_POST['btn_ajoute_enfant']) && $_POST['id_enfant'] != "" && $_POST['id_enfant'] != NULL) {
                    $id_enfant = intval(htmlspecialchars($_POST['id_enfant']));
                    Admin::set_Update_parent_enfant($id_parent, $id_enfant, 2);
                }
                if (isset($_POST['btn_sup_enfant']) && $_POST['id_enfant'] != "" && $_POST['id_enfant'] != NULL) {
                    $id_enfant = intval(htmlspecialchars($_POST['id_enfant']));
                    Admin::set_Update_parent_enfant($id_parent, $id_enfant, 1);
                }

                $tabdatas['get_parent_enfant'] = Admin::get_parent_enfant($id_parent);

                //var_dump($tabdatas['get_parent_enfant']);
                //var_dump($tabdatas['get_AllParentsBy']);
                $_SESSION['page'] = 'admin_eleve_parents';
            }

        } else {
            $tabdatas['get_AllParents'] = Admin::get_AllParents();
            //var_dump($tabdatas['get_AllParents']);
            $tabdatas['sousmenu'] = "Liste des parents";
        }

        $_POST = NULL;
        $_GET = NULL;

        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function elev_listAllAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        //var_dump($_POST);
       
        $tabdatas['menu'] = "Gestion des Etudiants";
        $tabdatas['sousmenu'] = "Liste des Etudiants";
        $fct_exec ="";

        if (isset($_POST['btn_send_elevphoto'])) {
            //var_dump($_FILES);
            //var_dump($_POST );exit();
            $id_pers_elev=intval(htmlspecialchars($_POST['id_pers']));
            $tabdatas['sendImg_etat'] = User_Img::send_elevImg($id_pers_elev);
            $elev_lien_photo = $tabdatas['liens'] . $id_pers_elev . '/' . $id_pers_elev . '.jpg';
            $fct_exec = $fct_exec . '|| sendImg($id_pers_elev) & liens=' . $elev_lien_photo . ' || ';
        }

        if (isset($_POST['btn_setuser'])) {

            //var_dump($_POST);
            //var_dump($_POST);exit();

            $id_eleve = intval(htmlspecialchars($_POST['id_eleve']));
            $change_user=Model_public::change_user($id_eleve,1);
            //exit();

 
        }
 
        //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();   get_elevBy
        if (isset($_POST['btn_voir_eleve']) && $_POST['id_eleve'] != "" && $_POST['id_eleve'] != NULL) {
            $id_eleve = intval(htmlspecialchars($_POST['id_eleve']));
            $tabdatas['get_etud_preinscriptionBy'] = Admin::get_etud_preinscriptionBy($id_eleve);
            $tabdatas['get_etud_parcoursBy'] = Admin::get_etud_parcoursBy($id_eleve);

            $tabdatas['get_elevBy'] = Admin::get_elevBy($id_eleve);
            //var_dump($tabdatas['get_etud_parcoursBy']);
            $_SESSION['page'] = "admin_eleve_infos";
            $tabdatas['sousmenu'] = "Liste des Etudiants > Informations etudiant";
            $id_eleve_pers = intval(($tabdatas['get_elevBy'][0])['id_pers_personne']);
            //'$tabdata_user->liens.$id_eleve_pers'
            $file =  '/files/' . $id_eleve_pers .'/'. $id_eleve_pers . '.jpg';
            //var_dump($file);
            $tabdatas['lien_photo_etud'] =  $file;
 
            //var_dump($tabdatas['lien_photo']);
            $id_etud_personne = intval(($tabdatas['get_elevBy'][0])['id_pers_personne']);
            $tabdatas['panel'] = 1;

            $dossier = dirname(getcwd(), 1) . '/files' . '/' . $id_etud_personne . '/' . 'dossier/';
            $liens =   $tabdatas['base_liens'] . 'files' . '/' . $id_etud_personne . '/' . 'dossier/';
            $get_dossierContenu = Upload_files::get_dossierContenu($dossier, $liens);

            if ($get_dossierContenu != 0) {
                $tabdatas['get_dossierContenu'] = $get_dossierContenu;
                //var_dump( $tabdatas['get_dossierContenu']);
            }



        }
        $tabdatas['allEleves'] = Admin::get_AllUniv_Elev($tabdata_user->fk_iduniv);
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        foreach ($tabdatas['allEleves'] as $key => $value) {
            ($tabdatas['allEleves'][$key])['grpe_elev'] = Admin::get_univElev_grpBy($value['id_eleve_eleve']);
            //var_dump($tabdatas['allEleves'][$key]);
        }

        
        //var_dump($tabdatas['allEleves'][0]);
        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);
        //var_dump($_SESSION['page']);
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function notifAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $fk_iduniv=intval($tabdata_user->fk_iduniv);
        //if (isset($_POST['info_infoBundle_Info'])) { var_dump($_POST); exit();}
        //var_dump($tabdata_user->id_pers_personne);exit();
        /*       
			$get_sms_notif= Admin::get_sms_notif($fk_iduniv);
			var_dump($get_sms_notif);//exit;
			foreach ($get_sms_notif as $key => $value) {

				$id_notif=intval($value['id_notif']);
				$dest_tab=[];
				$get_sms_notif_usernum= Admin::get_sms_notif_usernum($id_notif,$fk_iduniv);
				var_dump($get_sms_notif_usernum);
				foreach ($get_sms_notif_usernum as $keys => $values) {
					array_push($dest_tab,$values['contact']);
				}

				//$exp=$global_univ_info[0]['non_univ'];
				$msg=htmlspecialchars($value['notif_titre']).' '.htmlspecialchars($value['notif_desc']);
				//$get_sms_notif= Smseco::send_sms_json($exp,$id_notif,$msg,$dest_tab);
				var_dump($msg);
				var_dump($dest_tab);

			}
        */

        $tabdatas['tab_panel']=1;

        if (isset($_POST['info_infoBundle_Info'])) {

            $document_name = "";
            $document_name = "";
            $cible = "";
            $send_methode = "";
            $list_eleves = [];
            $list_prof = [];
            $list_admin= [];
            $tabdatas['tab_panel']=2;
            //var_dump($_POST);exit;
            //info_infoBundle_Info[image][file]
            $notif_titre = htmlspecialchars($_POST['info_infoBundle_Info']['Info']);
            $notif_desc = htmlspecialchars($_POST['info_infoBundle_Info']['Description']);
            $notif_debut = htmlspecialchars($_POST['info_infoBundle_Info']['Datedebut']);
            $notif_fin = htmlspecialchars($_POST['info_infoBundle_Info']['Datefin']);

            $cible = htmlspecialchars($_POST['cible']);


            if (isset(($_POST['info_infoBundle_Info']['document'])['name'])) {
                $document_name = htmlspecialchars(($_POST['info_infoBundle_Info']['document'])['name']);
            }

            if (isset($_POST['input_methode_email']) && $_POST['input_methode_email'] == "on") {
                $send_methode = $send_methode . "email;";
            }

            if (isset($_POST['input_methode_sms']) && $_POST['input_methode_sms'] == "on") {
                $send_methode = $send_methode . "sms;";
            }
            if (isset($_POST['input_methode_notif']) && $_POST['input_methode_notif'] == "on") {
                $send_methode = $send_methode . "notif;";
            }

            if (isset($_POST['input_eleve_chek']) && $_POST['input_eleve_chek'] != "" && $_POST['input_eleve_chek'] != NULL) {
                $list_eleves =  $_POST['input_eleve_chek'];
                $list_parents = [];
                //var_dump($list_eleves);
                foreach ($list_eleves as $key => $value) {
                    $id_pm = (Admin::get_enfant_idParent(intval($value)))[0];
                    array_push($list_parents, $id_pm['id_parent']);
                }
                //var_dump( $list_parents );

                //exit;
            }
            elseif (isset($_POST['input_prof_chek']) && $_POST['input_prof_chek'] != "" && $_POST['input_prof_chek'] != NULL) {
                $list_prof =  $_POST['input_prof_chek'];
            }
            elseif (isset($_POST['input_admin_chek']) && $_POST['input_admin_chek'] != "" && $_POST['input_admin_chek'] != NULL ) {
                $list_admin =  $_POST['input_admin_chek'];
            }
            else { }

            $createur_notif = intval($tabdata_user->id_pers_personne);
            //var_dump($createur_notif);
            //var_dump($document_name, $cible, $send_methode, $list_eleves, $list_prof);
            //var_dump($send_methode);exit;

            $set_notifications_id = Admin::set_notifications($notif_titre, $notif_desc, $notif_debut, $notif_fin, $send_methode, $createur_notif, $fk_iduniv);
            //var_dump('set_notifications_id',$set_notifications_id);
            $set_notifications_id = intval($set_notifications_id);
            //var_dump($cible);
            //var_dump($set_notifications_id);
            if ($set_notifications_id > 0) {

                if (isset((($_FILES['info_infoBundle_Info']['name'])['image'])['file'])  && (($_FILES['info_infoBundle_Info']['name'])['image'])['file'] != "") {
                    $tabdatas['send_notifImg'] = User_Img::send_notifImg($set_notifications_id);
                    //var_dump($_FILES['info_infoBundle_Info']);
                }

                if (isset((($_FILES['info_infoBundle_Info']['name'])['document'])['file']) && (($_FILES['info_infoBundle_Info']['name'])['document'])['file'] != "") {
                    $tabdatas['send_notifDocs'] = User_Img::send_notifDocs($set_notifications_id);
                    //var_dump($_FILES['info_infoBundle_Info']);
                }
                //exit();

                //var_dump('send_notifImg' ,$tabdatas['send_notifImg'], 'send_notifDocs',$tabdatas['send_notifDocs']);

                switch ($cible) {
                    case "professeur":
                        $list_user = $list_prof;
                        $type_user = 2;
                    break;
                    case "eleves":
                        foreach ($list_parents as $keys => $values) {
                            $tabdatas['set_usersNotif'] = Admin::set_usersNotif($values, $set_notifications_id, 3);
                        }
                        $list_user = $list_eleves;
                        $type_user = 1;
                    break;
                    case "Administration":
                        //var_dump($cible);
                        $list_user = $list_admin;
                        $type_user = 4;
                    break;
                    case "all":
                        //var_dump($cible);
                        $id_pm = Admin::get_all_adminid();
                        foreach ($id_pm as $key => $value) {
                            $tabdatas['set_usersNotif'] = Admin::set_usersNotif(intval($value['id_admin_admin']), $set_notifications_id, 4);
                        }

                        $id_pm = Admin::get_all_eleveid();
                        foreach ($id_pm as $key => $value) {
                            $tabdatas['set_usersNotif'] = Admin::set_usersNotif(intval($value['id_eleve_eleve']), $set_notifications_id, 1);
                        }

                        $id_pm = Admin::get_all_parentid();
                        foreach ($id_pm as $key => $value) {
                            $tabdatas['set_usersNotif'] = Admin::set_usersNotif(intval($value['id_parent_parent']), $set_notifications_id, 3);
                        }

                        $id_pm = Admin::get_all_profid();
                        foreach ($id_pm as $key => $value) {
                            $tabdatas['set_usersNotif'] = Admin::set_usersNotif(intval($value['id_prof_prof']), $set_notifications_id, 2);
                        }
                        $list_user = [];
                        break;

                    default:
                        //$list_user = $list_eleves;
                        //$type_user = 1;
                        break;
                }

                foreach ($list_user as $key => $value) {
                    $tabdatas['set_usersNotif'] = Admin::set_usersNotif($value, $set_notifications_id, $type_user);
                }
            } 
            else {}
        }

        if (isset($_POST['btn_action_desact_notif'])) {
            $id_notif = intval(htmlspecialchars($_POST['id_notif']));
            $tabdatas['univInfos'] = Admin::set_allNotifs_etat($id_notif, 2);
            $tabdatas['tab_panel']=2;
        } elseif (isset($_POST['btn_action_active_notif'])) {
            $id_notif = intval(htmlspecialchars($_POST['id_notif']));
            $tabdatas['univInfos'] = Admin::set_allNotifs_etat($id_notif, 1);
            $tabdatas['tab_panel']=2;
        } elseif (isset($_POST['btn_action_sup_notif'])) {
            $id_notif = intval(htmlspecialchars($_POST['id_notif']));
            $tabdatas['univInfos'] = Admin::set_allNotifs_etat($id_notif, 0);
            $tabdatas['tab_panel']=2;
        }
        //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();
        //$tabdatas['allEleves']=Admin::get_AllUniv_Elev($tabdata_user->fk_iduniv);

        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($fk_iduniv);
        $tabdatas['get_allNotifs'] = Admin::get_allNotifs($fk_iduniv);
        //var_dump( $tabdatas['univInfos'] );
        $table="annee_scolaire";
        $tb_conditions=[];
        $tb_conditions["fk_univ"]= $fk_iduniv;
        $tabdatas['univ_anneescol'] = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
        //var_dump( $tabdatas['univ_anneescol'] );

        $tabdatas['menu'] = "Notification";
        $tabdatas['sousmenu'] = "Création & Envoi";

        //var_dump($tabdatas['allEleves']);
        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);


        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }
    public function rolesAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;

        //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();
        $tabdatas['getRole'] = Admin::getRole();
        //var_dump($tabdatas['getRole']);
        //$tabdatas['allCpteAdmin'] = modelUser::get_activeAlluserBy(4);
        $tabdatas['allCpteAdmin'] = modelUser::get_activeAll_univUserBy(4, $tabdata_user->fk_iduniv);
        //var_dump($tabdatas['allCpteAdmin']);

        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $tabdatas['menu'] = "Rôles & privillèges";
        $tabdatas['sousmenu'] = "";

        //var_dump($tabdatas['allCpteAdmin']);
        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }
    public function chatAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;

        //$tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();
        $tabdatas['getRole'] = Admin::getRole();

        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $tabdatas['menu'] = "Chat";
        $tabdatas['sousmenu'] = "";

        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function admin_ceerNiveauAction()
    {
        $tabdatas = [];
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;

        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $eschool_univ =  intval($tabdata_user->fk_iduniv);

        //**CREATION ANNEE :: Utilisateur Connecter /Admin/ et cree_anne_scol_btn
        if (isset($_POST["cree_matiere_btn"]) && isset($_SESSION['user']) && ($_SESSION['user']['type_pers'] == '4')) {

            $lib_niveau = htmlspecialchars($_POST['titre']);
            $desc_niveau = htmlspecialchars($_POST['descriptions']);
            //var_dump($_POST);
            $tabdatas['setNiveau'] = Admin::setNiveau($lib_niveau, $desc_niveau, $eschool_univ);
            //var_dump($tabdatas['setNiveau'],$tabdatas['setNiveau'][0]);
        } else {
            //var_dump($tabdatas['allAnneeScolaire']);exit();
            //View::renderTemplate('Accueil/admin/admin_creerannee.html',$tabdatas);
        }



        //$tabdatas['allniveau']=Admin::getNiveau();
        $tabdatas['allniveau'] = Admin::getNiveauBy($eschool_univ);
        //var_dump($tabdatas['allniveau']);
        $tabdatas['menu'] = "Administration";
        $tabdatas['sousmenu'] = "Niveau";


        $_POST = NULL;
        $_GET = NULL;
        unset($_POST);
        unset($_GET);
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function stage_etudiantsAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $id_anneescol = 0;
        //var_dump($_POST);
        $tabdatas['menu'] = "Gestion des stages";
        $tabdatas['panel'] = 0;
        //$tabdatas['sousmenu']="Liste des Etudiants";
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $eschool_univ =  intval($tabdata_user->fk_iduniv);

        if (isset($_POST['etudiant']) && isset($_POST['anneeScolaire'])  && isset($_POST['classe'])  && isset($_POST['direct_ecole']) && $_POST['etudiant'] != "" && $_POST['anneeScolaire'] != "" && $_POST['classe'] != "" && $_POST['direct_ecole'] != "") {

            $tabdatas['panel'] = 2;

            $theme_stage = htmlspecialchars($_POST['theme_stage']);
            $fk_idetudiant = intval(htmlspecialchars($_POST['etudiant']));
            $fk_idgroupe = intval(htmlspecialchars($_POST['classe']));
            $fk_idprof_directEtud = intval(htmlspecialchars($_POST['direct_ecole']));
            $fk_idanneeScol = intval(htmlspecialchars($_POST['anneeScolaire']));
            $nom_entreprise = htmlspecialchars($_POST['entreprise']);
            $ville_entreprise = htmlspecialchars($_POST['ville_entreprise']);
            $loca_entreprise = htmlspecialchars($_POST['loca_entreprise']);
            $tel_entreprise = htmlspecialchars($_POST['tel_entreprise']);
            $email_entreprise = htmlspecialchars($_POST['mail_entreprise']);
            $maitre_stage = htmlspecialchars($_POST['maitre_stage']);
            $tel_maitre_stage = htmlspecialchars($_POST['tel_maitre_stage']);
            $poste_maitre_stage = htmlspecialchars($_POST['job_maitre_stage']);
            $date_debut = htmlspecialchars($_POST['date_debut']);
            $date_fin = htmlspecialchars($_POST['date_fin']);



            $tabdatas['stage_etudiant'] = Admin::stage_etudiant($theme_stage, $fk_idetudiant, $fk_idgroupe, $fk_idprof_directEtud, $fk_idanneeScol, $nom_entreprise, $ville_entreprise, $loca_entreprise, $tel_entreprise, $email_entreprise, $maitre_stage, $tel_maitre_stage, $poste_maitre_stage, $date_debut, $date_fin);

            //var_dump($tabdatas['stage_etudiant']);


        }

        if (isset($_POST['btn_voir_listStage']) && isset($_POST['anneeScolaire_stage'])) {
            $id_anneescol = intval(htmlspecialchars($_POST['anneeScolaire_stage']));
            $tabdatas['panel'] = 0;
        }

        if (isset($_POST['btn_etud_listStage']) && isset($_POST['etudiant_stgeinfo']) && isset($_POST['anneeScolaire_stginfo'])) {
            $id_etudiant = intval(htmlspecialchars($_POST['etudiant_stgeinfo']));
            $id_anneescol = intval(htmlspecialchars($_POST['anneeScolaire_stginfo']));
            $tabdatas['get_all_stgEtudiant'] = Admin::get_all_stgEtudiant($id_etudiant, $id_anneescol);
            $tabdatas['panel'] = 1;

            //var_dump($tabdatas['get_all_stgEtudiant']);
        }

        if (isset($_POST['btn_hist_etudiant']) && isset($_POST['etudiant_hist']) && isset($_POST['classe_hist'])) {
            $id_etudiant = intval(htmlspecialchars($_POST['etudiant_hist']));
            $tabdatas['get_Etudiant_allstage'] = Admin::get_Etudiant_allstage($id_etudiant);
            $tabdatas['panel'] = 3;

            //var_dump($tabdatas['get_Etudiant_allstage']);
        }

        $tabdatas['allEleves'] = Admin::get_AllUniv_Elev($tabdata_user->fk_iduniv);


        $tabdatas['getAnneeScolaire'] = Admin::get_anneeBy($tabdata_user->fk_iduniv);

        $tabdatas['getAllElev'] = Admin::get_AllUniv_Elev($tabdata_user->fk_iduniv);
        $tabdatas['getAllProf'] = Admin::getProf_Byuniv($tabdata_user->fk_iduniv);
        //$tabdatas['getNiveau']=Admin::getNiveau();
        $tabdatas['getNiveau'] = Admin::getNiveauBy($eschool_univ);

        $tabdatas['get_stage_etudiant'] = Admin::get_stage_etudiant($id_anneescol);
        //var_dump($tabdatas['get_stage_etudiant']);
        //var_dump($tabdatas['getAllElev']);
        //var_dump($tabdatas['get_Etudiant_allstage']);
        $_POST = NULL;
        $_GET = NULL;
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }
    public function anciens_etudiantsAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $tabdatas['panel'] = 0;
        $tabdatas['base_liens'] = $tabdata_user->base_liens;

        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $eschool_univ =  intval($tabdata_user->fk_iduniv);


        if (isset($_POST['filliere_anci']) && isset($_POST['btn_voir_ancien'])) {
            $id_filliere = htmlspecialchars($_POST['filliere_anci']);
            $id_eleve = 0;
            $tabdatas['get_Ancien_EtudiantBy'] = Admin::get_Ancien_EtudiantBy($id_filliere, $id_eleve);
            //var_dump($tabdatas['get_Ancien_EtudiantBy']);
            $tabdatas['panel'] = 0;
        }
        if (isset($_POST['info_ancien']) && isset($_POST['filliere'])) {
            //var_dump($_POST);
            $id_eleve = 0;

            $id_filliere = htmlspecialchars($_POST['filliere']);
            $tabdatas['get_Ancien_EtudiantBy'] = Admin::get_Ancien_EtudiantBy($id_filliere, $id_eleve);

            $id_eleve = htmlspecialchars($_POST['info_ancien']);

            $tabdatas['get_UniqueAncien_EtudiantBy'] = Admin::get_Ancien_EtudiantBy($id_filliere, $id_eleve);
            //var_dump($tabdatas['get_UniqueAncien_EtudiantBy']);
            $tabdatas['panel'] = 1;
        }

        $tabdatas['menu'] = "Réseaux des anciens";
        //$tabdatas['getClasses']=Admin::getClasses();
        $tabdatas['getClasses'] = Admin::getClassesBy($eschool_univ);
        //var_dump($tabdatas['getClasses']);
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        //$tabdatas['get_AllAncien_EtudiantBy']=Admin::get_AllAncien_EtudiantBy();
        //var_dump($tabdatas['get_AllAncien_EtudiantBy']);

        $_POST = NULL;
        $_GET = NULL;
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function admin_inscrip_etudAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $tabdatas['panel'] = 0;
        $tabdatas['base_liens'] = $tabdata_user->base_liens;

        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $id_univ = intval($tabdata_user->fk_iduniv);
        //var_dump($tabdatas['univInfos']);

        $tabdatas['getClassesBy'] = Admin::getClassesBy($id_univ);
        $tabdatas['getNiveauBy'] = Admin::getNiveauBy($id_univ);

        
        if (isset($_POST) && isset($_FILES) && isset($_POST['parcours']) && isset($_POST['niveauetude'])) {
            //var_dump($_POST);
            //var_dump($_FILES);
            $parcours=htmlspecialchars($_POST['parcours']);
            $niveauetude=htmlspecialchars($_POST['niveauetude']);
            $file_excell_name =$_FILES["file_excell"]["name"];
            $path_parts = pathinfo($file_excell_name);
            $file_excell =$path_parts['extension'];
            $file_excell_error =$_FILES["file_excell"]["error"];
            $file_excell_tmp_name =$_FILES["file_excell"]["tmp_name"];
            $file_excell_tmp_type =$_FILES["file_excell"]["type"];
            

            if (!empty($_FILES['file_excell']) && $file_excell_tmp_type == "application/vnd.ms-excel" &&  $file_excell_error== 0) {
                //var_dump($path_parts);


                //UPLOAD DU FICHIER CSV, vérification et insertion en BASE

                /*$req = $pdo->prepare('INSERT INTO entreprise ( RaisonSociale, Activite, pays, ville, Email, Telephone, Gerant,
                AutreContact, TelephoneDirecteur, EmailDirecteur, Fonction) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');*/
                $file = new \SplFileObject($file_excell_tmp_name);
                $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);
                $i=0;
                foreach ($file as $key => $value) {
                    $i++;
                    if ($i==100) { break;}
                    //var_dump($key,$value);
  
                    $infos=explode(";",$value[0]);
                    //var_dump(count($infos));
                   // var_dump($infos);
                    if (count($infos)==15) {
                       
                        if (($infos[0] === 'nom' && $infos[1] === 'prenom') || ($infos[0] === '' && $infos[1] === '')) {
                            $tabdatas['toast_notif']['etat'] = "danger";
                            $tabdatas['toast_notif']['infos'] = "Erreur lors de l'import du fichier";     

                        }
                        else{
                            //var_dump('oui');
                            //var_dump($infos[0],$infos[1]);

                            $nom = str_replace(' ', '', strtoupper($infos[0]));
                            $prenom = strtoupper(htmlspecialchars($infos[1]));
                            if ($infos[4]== '') {
                                $tmp_prenomtb=explode(" ",$infos[1]);
                                $mail1=strtolower($nom).".".strtolower($tmp_prenomtb[0]);
                                if ($infos[3] != "") { 
                                    $code = substr($infos[3], -3);
                                    $mail1= $mail1.$code;
                                    $tmp_mail=explode("@",($tabdatas['univInfos'][0])['email_univ']);
                                    $mail1= $mail1."@".$tmp_mail[1];
                                }
                                else{
                                    $mail1=$mail1.rand(100,999);
                                }
                            }
                            else{$mail1 = htmlspecialchars($infos[4]);}
                            //var_dump($mail1);
                            //$mail2 = htmlspecialchars($_POST['mail2']);
                            $datenaiss = htmlspecialchars($infos[6]);
                            $lieunaiss = htmlspecialchars($infos[7]);
                            $contact = htmlspecialchars($infos[5]);
                            $sexe = htmlspecialchars($infos[2]);

                            $pass =str_replace(' ', '', ($tabdatas['univInfos'][0])['initiale_univ']).'@2020';
                            
                            $pass1 = sha1($pass);
                            $anneScol="2020-2021";

                            $infos_tb=[];
                            $infos_tb[0]=$nom;
                            $infos_tb[1]=$prenom;
                            $infos_tb[2]=$sexe;
                            //matricule
                            if (htmlspecialchars($infos[3]) == "") {
                                //$carteetudiant  
                                $infos_tb[3]=  "CI00000000";
                            } else {
                                $infos_tb[3]=htmlspecialchars($infos[3]);
                            }
                            $infos_tb[4]=$mail1;
                            $infos_tb[5]=$contact;
                            if ($datenaiss=="" || $datenaiss == 0) {$infos_tb[6]="1990-01-01";  }
                            else{$infos_tb[6]=$datenaiss;}
 
                            $infos_tb[7]=$lieunaiss;
                            //nationalite
                            $infos_tb[8]=$infos[8];
                            
                            //pere et mere
                            $infos_tb[9]=htmlspecialchars($infos[9]);
                            $infos_tb[10]=htmlspecialchars($infos[10]);
                            $infos_tb[11]=htmlspecialchars($infos[11]);
                            $infos_tb[12]=htmlspecialchars($infos[12]);
                            $infos_tb[13]=htmlspecialchars($infos[13]);
                            $infos_tb[14]=htmlspecialchars($infos[14]);

                            $infos_tb[15]=htmlspecialchars($pass1);
                            $infos_tb[16]=$id_univ;

                            $infos_tb[17]=$parcours;
                            $infos_tb[18]=$niveauetude;
                            //var_dump($infos_tb);
          
          
                            $get_verif_eleveInscript = User::get_verif_eleveInscript($mail1, $id_univ, $anneScol);
                            //var_dump($get_verif_eleveInscript);

                            //AJOUT DES INFORMATIONS SUR L'ELEVE DS TABLE PERSONNE ET TABLE ELEVE 
                            if (empty($get_verif_eleveInscript ) || $get_verif_eleveInscript ==0) {
                                $set_eleve_Personnes = User::set_importeleve_Personnes($infos_tb);
                                //var_dump($set_eleve_Personnes);
                                $tabdatas['toast_notif']['etat'] = "success";
                                $tabdatas['toast_notif']['infos'] = "Import du fichier ".$file_excell_tmp_name." effectuée";     

                            }
                            else{
                                $tabdatas['toast_notif']['etat'] = "danger";
                                $tabdatas['toast_notif']['infos'] = "Erreur lors de l'Import du fichier ".$file_excell_tmp_name." ";     
                            }


                        }
                    }                    
                    else{
                            $tabdatas['toast_notif']['etat'] = "succes";
                            $tabdatas['toast_notif']['infos'] = "Import du fichier ".$file_excell_tmp_name." terminer";     
                    }
                }
   
            }
            else{
                    $tabdatas['toast_notif']['etat'] = "danger";
                    $tabdatas['toast_notif']['infos'] = "le fichier ".$file_excell_tmp_name." n'est pas supporter , merci de le convetir en Csv";     
            }
        }


        if (isset($_POST['btn_voir_dmd']) && isset($_POST['anneeScolaire_stage'])) {
            //var_dump($_POST);exit();
            $annscolaire =  htmlspecialchars($_POST['anneeScolaire_stage']);
            $tabdatas['get_AllEtudiant_dmdInscripBy'] = Admin::get_AllEtudiant_dmdInscripBy($annscolaire, $id_univ);
            //var_dump('get_AllEtudiant_dmdInscripBy',$tabdatas['get_AllEtudiant_dmdInscripBy']);
            if (isset($_POST['etudiant_infos'])) {

                $id_etudiant = intval(htmlspecialchars($_POST['etudiant_infos']));
                $tabdatas['get_UniqEtudiant_dmdInscripBy'] = Admin::get_UniqEtudiant_dmdInscripBy($annscolaire, $id_etudiant);
                //var_dump('get_UniqEtudiant_dmdInscripBy',$tabdatas['get_UniqEtudiant_dmdInscripBy']);

                $id_etud_personne = intval(($tabdatas['get_UniqEtudiant_dmdInscripBy'][0])['id_pers_personne']);
                $tabdatas['panel'] = 1;

                $dossier = dirname(getcwd(), 1) . '/files' . '/' . $id_etud_personne . '/' . 'dossier/';
                $liens =   $tabdatas['base_liens'] . 'files' . '/' . $id_etud_personne . '/' . 'dossier/';
                $get_dossierContenu = Upload_files::get_dossierContenu($dossier, $liens);

                if ($get_dossierContenu != 0) {
                    $tabdatas['get_dossierContenu'] = $get_dossierContenu;
                    //var_dump( $tabdatas['get_dossierContenu']);
                }
            }
        } 
        elseif (isset($_POST['id_etudiant']) && isset($_POST['id_pers_etudiant']) && isset($_POST['matricule']) && isset($_POST['filiere_id']) && isset($_POST['niveau_id']) && isset($_POST['statut_affect']) && isset($_POST['statut_boursier']) && isset($_POST['statut_redoub']) && isset($_POST['btn_activeinscrip'])) {
           

            $id_anneescol =  htmlspecialchars($_POST['id_anneescol']);
            $id_etudiant = intval(htmlspecialchars($_POST['id_etudiant']));
            $id_pers_etudiant = intval(htmlspecialchars($_POST['id_pers_etudiant']));
            $matricule =  htmlspecialchars($_POST['matricule']);
            $statut_boursier =  htmlspecialchars($_POST['statut_boursier']);
            $statut_affect =  htmlspecialchars($_POST['statut_affect']);
            $statut_redoub =  htmlspecialchars($_POST['statut_redoub']);
            

            //var_dump($_POST);
            $tb_conditions = [];
            $table = 'eleve';
            $tb_conditions['id_eleve_eleve'] = $id_etudiant;
            $get_eleve_info = Admin::get_selectSQL_ALL_by($table, $tb_conditions);

            if (!empty($get_eleve_info) && $get_eleve_info!=0) {
                $get_eleve_info = $get_eleve_info[0];
                //var_dump($get_eleve_info);
                $tb_conditions = [];
                $table = 'personne';
                $tb_conditions['type_pers'] = 3;
                $tb_conditions['email'] = $get_eleve_info['emailurget'];
                $tb_conditions['nom_prenom'] = $get_eleve_info['nomurget'];
                $tb_conditions['contact'] = $get_eleve_info['telurget'];
                $tb_conditions['fk_iduniv'] = $id_univ;
                $get_parent_info = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
                
                if (!empty($get_parent_info) && $get_parent_info!=0) {
                    //echo 'existe';
                    $get_parent_info =$get_parent_info[0] ;
                    //var_dump($get_parent_info);

                    $tb_conditions = [];
                    $table = 'parent';
                    $tb_conditions['id_parent_parent'] = $get_parent_info['id_type'];
                    $get_tbeparent_info = Admin::get_selectSQL_ALL_by($table, $tb_conditions);

                    //var_dump($get_tbeparent_info);

                    if (!empty($get_tbeparent_info) && $get_tbeparent_info!=0  ) {

                        //var_dump(strstr($get_tbeparent_info[0]['matricule'], $matricule));

                        if (  (strstr($get_tbeparent_info[0]['matricule'], $matricule))  ) {  }
                        else {
                            $tb_conditions = [];
                            $tb_infos= [];
                            $table = 'parent';
                            $tb_conditions['id_parent_parent'] = $get_parent_info['id_type'];
                            $tb_infos['matricule'] = $get_tbeparent_info[0]['matricule'].';'.$matricule;
                            $maj_tbeparent_info = Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
                            //var_dump($maj_tbeparent_info);

                        }
 
                    }
                    

                }
                else {
                    $nom = $get_eleve_info['nomurget'];  $prenom ='';
                    $sexe = 'M';
                    $email = $get_eleve_info['emailurget'];
                    $tel = $get_eleve_info['telurget'];
                    $pass1 = sha1( $get_eleve_info['telurget']); $pass2 = $pass1 ;
                    $type = 3;
                    $datenaiss = '1900-01-01';
                    $lieunaiss = '';
                    $set_elevparent= User::setPersonnes($nom, $prenom, $sexe, $email, $tel, $pass1, $pass2, $type, $datenaiss, $lieunaiss, $id_univ);
                    //var_dump($set_elevparent);
                }
            }

           
            //exit();
            //anciennete  ======> niveau précédent
            //niveauetude  ======> niveau demander
            //parcours ======> filiere demander

            //SELECT * FROM `preinscription`

            $filiere_tb = explode("_", htmlspecialchars($_POST['filiere_id']));
            $niveau_tb = explode("_", htmlspecialchars($_POST['niveau_id']));

            $filiere_id = intval($filiere_tb[0]);
            $niveau_id = intval($niveau_tb[0]);
            $parcours = $filiere_tb[1];
            $niveauetude = $niveau_tb[1];

            //var_dump($filiere_id,$niveau_id,$niveauetude,$parcours);
            $tabdatas['set_MajPreinscriptionBy'] = Admin::set_MajPreinscriptionBy($id_anneescol, $id_etudiant, $niveau_id, $filiere_id);

            $tabdatas['set_MajTble_eleveBy'] = Admin::set_MajTble_eleveBy($id_etudiant, $matricule, $niveauetude, $parcours, $statut_affect, $statut_redoub, $statut_boursier);

            //var_dump( $tabdatas['set_MajPreinscriptionBy'],$tabdatas['set_MajTble_eleveBy']);

            if ($tabdatas['set_MajPreinscriptionBy'] == 1 && $tabdatas['set_MajTble_eleveBy'] == 1) {
                $tabdatas['set_MajEtat_persBy'] = Admin::set_MajEtat_persBy($id_pers_etudiant, 0);
                //var_dump( $tabdatas['set_MajEtat_persBy']);

            }


            $tabdatas['get_AllEtudiant_dmdInscripBy'] = Admin::get_AllEtudiant_dmdInscripBy($id_anneescol, $id_univ);
        }

        if (isset($_POST['etudiant_persid']) && isset($_POST['btn_sup_etudiant']) && isset($_POST['anneeScolaire_stage'])) {
            $id_personne = intval($_POST['etudiant_persid']);
            $tabdatas['detele_etudiant_enattente_inscript'] = Admin::detele_etudiant_enattente_inscript($id_personne, $id_univ);
            $annscolaire =  htmlspecialchars($_POST['anneeScolaire_stage']);
            $tabdatas['get_AllEtudiant_dmdInscripBy'] = Admin::get_AllEtudiant_dmdInscripBy($annscolaire, $id_univ);
        }


        if (isset($_POST['etudiant_persid']) && isset($_POST['btn_sup_correct']) && isset($_POST['anneeScolaire_stage'])) {


            $id_personne = intval($_POST['etudiant_persid']);

            $table="personne";            
            $tb_infos=[]; 
            $tb_conditions=[];
            $tb_conditions["id_pers_personne"]=$id_personne;
            $tb_infos['etat_pers']=4; 
            $tabdatas['set_updateSQL_ALL_by'] = Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);

            
           
            $annscolaire =  htmlspecialchars($_POST['anneeScolaire_stage']);
            $tabdatas['get_AllEtudiant_dmdInscripBy'] = Admin::get_AllEtudiant_dmdInscripBy($annscolaire, $id_univ);

        }

        

        $tabdatas['menu'] = "Demande d'Inscription des étudiants";
        //Anne scolaire de preinscription ds table preinscription!= anneescolaire
        $tabdatas['getAnneeScolaire'] = modelUser::get_preinsc_anneScol();
        //var_dump($tabdatas['getAnneeScolaire']);

        $tabdatas['getClassesBy'] = Admin::getClassesBy($id_univ);
        //$tabdatas['getNiveau']=Admin::getNiveau();
        $tabdatas['getNiveau'] = Admin::getNiveauBy($id_univ);
        //var_dump($tabdatas['getNiveau']);
        //$tabdatas['get_AllAncien_EtudiantBy']=Admin::get_AllAncien_EtudiantBy();
        //var_dump($tabdatas['get_AllAncien_EtudiantBy']);

        $_POST = NULL;
        $_GET = NULL;
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function admin_releverAction()
    {

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $fct_exec = "|| ";
        $tabdatas['base_liens'] = $tabdata_user->base_liens;

        $tabdatas['panel'] = 1;

        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $id_univ = intval($tabdata_user->fk_iduniv);
        //var_dump($_POST);

        if (isset($_POST['idmodele_relever']) && $_POST['idmodele_relever'] != "") {

            //var_dump($_POST);//exit();
            $idmodele_relever = intval(htmlspecialchars($_POST['idmodele_relever']));
            $tabdatas['get_modelename_By'] = Admin::get_modelename_By($idmodele_relever);
            //var_dump( $tabdatas['get_modelename_By']);//exit();
            $tabdatas['libelle_bulletin'] = ($tabdatas['get_modelename_By'][0])['libelle_bulletin'];

            $tabdatas['panel'] = 2;
        }
        if (isset($_POST['departementid']) && $_POST['departementid'] != "" && isset($_POST['idmodele_relever']) && $_POST['idmodele_relever'] != "") {
            //var_dump($_POST);
            $departementid = intval(htmlspecialchars($_POST['departementid']));
            $idmodele_relever = intval(htmlspecialchars($_POST['idmodele_relever']));
            $tabdatas['set_addMaj_bultinDepart_By'] = Admin::set_addMaj_bultinDepart_By($departementid, $idmodele_relever);
            $fct_exec = 'set_addMaj_bultinDepart_By(' . $departementid . ',' . $idmodele_relever . ');||';
            //var_dump( $tabdatas['set_addMaj_bultinDepart_By']);
            $tabdatas['panel'] = 1;
        }

        if (isset($_POST['id_niveau']) && isset($_POST['id_filiere']) && isset($_POST['id_bilan']) && isset($_POST['btn_add_bilan'])) {
            $niveau = htmlspecialchars($_POST['id_niveau']);
            $niveau_tab = explode("|_|", $niveau);

            $filiere = htmlspecialchars($_POST['id_filiere']);
            $filiere_tab = explode("|_|", $filiere);

            $bilan = htmlspecialchars($_POST['id_bilan']);
            $bilan_tab = explode("|_|", $bilan);

            $tabdatas['get_mat_nivoFilBy'] = Admin::get_mat_nivoFilBy(intval($niveau_tab[0]), intval($filiere_tab[0]), $id_univ);

            $tabdatas['get_B_li_bilanNivFil_By'] = Admin::get_B_li_bilanNivFil_By(intval($bilan_tab[0]), intval($niveau_tab[0]), intval($filiere_tab[0]), $id_univ);
            if (!empty($tabdatas['get_B_li_bilanNivFil_By'])) {
                foreach ($tabdatas['get_mat_nivoFilBy'] as $key => $value) {
                    foreach ($tabdatas['get_B_li_bilanNivFil_By'] as $keyn => $valuen) {
                        if ($value['id_matiere'] == $valuen['fk_matiere']) {
                            unset($tabdatas['get_mat_nivoFilBy'][$key]);
                        }
                    }
                }
            }


            //var_dump($tabdatas['get_B_li_bilanNivFil_By']);
            //var_dump($tabdatas['get_mat_nivoFilBy']);
            //var_dump($tabdatas['get_B_li_bilanNivFil_By']);
            $tabdatas['panel'] = 3;
            $tabdatas['id_niveau'] = $niveau_tab[0];
            $tabdatas['niveau'] = $niveau_tab[1];

            $tabdatas['id_filiere'] = $filiere_tab[0];
            $tabdatas['filiere'] = $filiere_tab[1];

            $tabdatas['id_bilan'] = $bilan_tab[0];
            $tabdatas['bilan'] = $bilan_tab[1];
            $tabdatas['id_univ'] = $id_univ;
        }
        $tabdatas['get_bultinDepart_By'] = Admin::get_bultinDepart_By($id_univ);
        //var_dump($tabdatas['get_bultinDepart_By']);
        $tabdatas['menu'] = "Examen et Evaluation";
        $tabdatas['sousmenu'] = "Relever de Notes";


        $tabdatas['getAnneeScolaire'] = Admin::get_anneeBy($id_univ);
        $tabdatas['get_all_departBy'] = Admin::get_all_departBy($id_univ);
        $tabdatas['get_all_bulletinModeleBy'] = Admin::get_all_bulletinModeleBy($id_univ);


        $tabdatas['getClassesBy'] = Admin::getClassesBy($id_univ);
        //$tabdatas['getNiveau']=Admin::getNiveau();
        $tabdatas['getNiveau'] = Admin::getNiveauBy($id_univ);
        $tabdatas['getBilanBy'] = Admin::getBilanBy();
        //var_dump($tabdatas['getClassesBy'], $tabdatas['getNiveau'], $tabdatas['getBilanBy']);
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_Admin ::: admin_releverAction => " . $fct_exec;
        modeldb::set_AllLog($info);
        /*:::::::Fin Enregistrement des logs::::::::::*/

        $_POST = NULL;
        $_GET = NULL;
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }
    public function eval_pvAction()
    {
        $fct_exec = "||";
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $fk_iduniv = $tabdata_user->fk_iduniv;
        $id_pers = $tabdata_user->id_pers_personne;
        
        $tabdatas['panel'] = 1;
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $id_univ = intval($tabdata_user->fk_iduniv);
        //var_dump($_POST);

        if (isset($_POST['id_annee']) && isset($_POST['id_groupe']) && isset($_POST['btn_relever'])) {

            $tab_annee = explode("_", htmlspecialchars($_POST['id_annee']));
            $tab_grpe = explode("_", htmlspecialchars($_POST['id_groupe']));
            $tabdatas['id_annee'] = intval($tab_annee[1]);
            $id_annee = intval($tabdatas['id_annee']);
            $tabdatas['annee'] = $tab_annee[2];
            $tabdatas['groupe'] = $tab_grpe[1];
            $tabdatas['id_groupe'] = intval($tab_grpe[0]);
            //var_dump($_POST);
            //var_dump($tabdatas['id_annee'],$tabdatas['annee'],$tabdatas['id_groupe'],$tabdatas['groupe']);
            $tabdatas['get_gpreEtudiantsBy'] = Admin::get_gpreEtudiantsBy($tabdatas['id_groupe'], $id_univ);
            $fct_exec = "get_gpreEtudiantsBy(" . $tabdatas['id_groupe'] . "," . $id_univ . ") ||";
            //var_dump($tabdatas['get_gpreEtudiantsBy']);
            $tabdatas['panel'] = 2;

            //var_dump($_POST);
            
            $table='annee_partie';
            $tb_conditions= [];
            $tb_conditions['id_anneeScolaire']= $tabdatas['id_annee'];
            $list_part_annee=Admin::get_selectSQL_ALL_by($table, $tb_conditions);
            //var_dump($list_part_annee);exit();
            if (!empty($list_part_annee ) && !empty($tabdatas['get_gpreEtudiantsBy'])) {
                foreach ($list_part_annee as $cle => $val) {
                    $part_anneid=intval($val['id_annee_partie']);
                    $tb_moy_eleve= [];
                    $tb_moy_all= [];
                    foreach ($tabdatas['get_gpreEtudiantsBy'] as $key => $value) {
                        $id_eleve= intval($value['id_eleve_eleve']);
                        //Liste des moyennes des etudiants
                        $tabdatas['get_eleve_all_moy'] = Admin::get_eleve_all_moy($id_eleve,$id_annee,$tabdatas['id_groupe']);
                        //var_dump('get_eleve_all_moy',$tabdatas['get_eleve_all_moy']);exit();
                        $moy_part_annee=0;
                        $tb_mat2=[];
                        $tb_mat_MP=[];
                        $tb_mat_keys=[];
                        $var_coef=0;
                        $var_moyen_coef=0;
                        $tb_mat1= Admin::get_grp_MatRepartie_By($id_univ, $tabdatas['id_groupe'], $id_annee);
                        if ($tb_mat1) {
                            foreach ($tb_mat1 as $key => $value) {
                                $var_test=0;
                                if ( intval($value['id_annee_partie'])== $part_anneid ) {
                                    $moyenne_tmp = Admin::get_elevmoy_by($id_eleve,intval($value['matiere_id_tmp']),$tabdatas['id_groupe'],$part_anneid);
                                    //var_dump($moyenne_tmp);var_dump($value);exit;
                                    //var_dump('value',$value);
                                    //var_dump('moyenne_tmp',$moyenne_tmp);
                                    if ($moyenne_tmp['moyenne']==null) {
                                        $moyenne_tmp['moyenne']=0;
                                    }
                                    $tb_mat2[$key]=$value;
                                    $tb_mat2[$key]['moyenne']=floatval($moyenne_tmp['moyenne']);

                                    if ($value['coeficient_tmp'] != '0') { 
                                        $var_coef =$var_coef + floatval($value['coeficient_tmp']); 
                                        $var_moyen_coef=$var_moyen_coef + floatval($value['coeficient_tmp'])* $moyenne_tmp['moyenne'];
                                    }
                                    else { 
                                        $var_coef =$var_coef + floatval($value['credit_tmp']); 
                                        $var_moyen_coef=$var_moyen_coef + floatval($value['credit_tmp'])* $moyenne_tmp['moyenne'];
                                    }
                                }
                            }
                        }
                        //var_dump('get_grp_MatRepartie_By',$tabdatas['get_grp_MatRepartie_By']);
                        $get_grp_MatRepartie_WithMP_By = Admin::get_grp_MatRepartie_WithMP_By($id_univ, $tabdatas['id_groupe'], $id_annee);
                        //var_dump('get_grp_MatRepartie_WithMP_By',$get_grp_MatRepartie_WithMP_By);

                        if ($get_grp_MatRepartie_WithMP_By!=0) {
                            # code...
                        
                            foreach ($get_grp_MatRepartie_WithMP_By as $key => $value) {

                                if ( intval($value['id_annee_partie'])== $part_anneid ) {

                                    $moyenne_tmp = Admin::get_elevmoy_by($id_eleve,intval($value['matiere_id_tmp']),$tabdatas['id_groupe'],$part_anneid);
                                    //var_dump($moyenne_tmp);
                                    //var_dump('value',$value);
                                    //var_dump('moyenne_tmp',$moyenne_tmp);
                                    if ($moyenne_tmp['moyenne']==null) {
                                        $moyenne_tmp['moyenne']=0;
                                    }

                                    if ($value['coeficient_tmp'] != '0') { 
                                        $var_coef =$var_coef + floatval($value['coeficient_tmp']); 
                                        $var_moyen_coef=$var_moyen_coef + floatval($value['coeficient_tmp'])* $moyenne_tmp['moyenne'];
                                    }
                                    else { 
                                        $var_coef =$var_coef + floatval($value['credit_tmp']); 
                                        $var_moyen_coef=$var_moyen_coef + floatval($value['credit_tmp'])* $moyenne_tmp['moyenne'];
                                    }

                                    $tb_mat_MP[$key]=$value;
                                    $tb_mat_MP[$key]['moyenne']=floatval($moyenne_tmp['moyenne']);
                                }
                            }
                        }  

                        //var_dump('var_coef',$var_coef);
                        //var_dump('var_moyen_coef',$var_moyen_coef);
                        if ($var_coef==0) {
                            $var_coef=1;
                        }
                        $moy_part_annee =  number_format((float)$var_moyen_coef/$var_coef,2);
                    
                        $tb_moy_eleve[$id_eleve]['moyenne']=(float)$moy_part_annee;
                        array_push($tb_moy_all,(float)$moy_part_annee);
                    }
                    //var_dump('tb_moy_eleve',$tb_moy_eleve);
                    array_multisort($tb_moy_all,SORT_DESC);
                    //var_dump('tb_moy_all',$tb_moy_all);
                    if (!empty($tb_moy_eleve ) ) {
                        foreach ($tb_moy_eleve as $key => $value) {
                            $rang = array_search($value['moyenne'], $tb_moy_all) + 1;
                            $effectif=count($tb_moy_all);
                            $tb_moy_eleve[$key]['rang']=$rang;
                            $tb_moy_eleve[$key]['effectif']= $effectif;
                            //var_dump('tb_moy_eleve',$tb_moy_eleve);
                            //exit();
                            $table='moyenne_periode';
                            $tb_conditions= [];
                            $tb_conditions['fk_id_periode']= $part_anneid;
                            $tb_conditions['fk_id_eleve']= $key;
                            $tb_conditions['fk_id_groupe']=  $tabdatas['id_groupe'];
                            $db_moy_part_annee=Admin::get_selectSQL_ALL_by($table, $tb_conditions);
                            //var_dump('db_moy_part_annee',$db_moy_part_annee);
                            
                            if ($db_moy_part_annee==0) {
                                $table='moyenne_periode';
                                $tb_conditions= [];
                                $tb_conditions['fk_id_periode']= $part_anneid;
                                $tb_conditions['fk_id_eleve']=  $key;
                                $tb_conditions['fk_id_groupe']=  $tabdatas['id_groupe'];
                                
                                $tb_infos['fk_id_periode']= $part_anneid;
                                $tb_infos['fk_id_eleve']=  $key;
                                $tb_infos['fk_id_groupe']=  $tabdatas['id_groupe'];
                                $tb_infos['moy_period']= $value['moyenne'];
                                $tb_infos['rang']=  $rang;
                                $tb_infos['effectif_grp']=  $effectif;
                                
                                $db_moy_part_annee=Admin::set_insertSQL($table,$tb_infos, $tb_conditions);
                                $fct_exec =' Admin::set_insertSQL(Ajout moyenne etudiant)===='.$table.' & '.$part_anneid.' & '.$key.'  & '.$tabdatas['id_groupe'].' & '.$key.' & Moyenne ='.$tb_infos['moy_period'].' & rang ='.$rang.' & Effectif ='.$effectif.'   ||';
                            }
                            else { 
                                $table='moyenne_periode';
                                $tb_conditions= [];
                                $tb_conditions['fk_id_periode']= $part_anneid;
                                $tb_conditions['fk_id_eleve']=  $key;
                                $tb_conditions['fk_id_groupe']=  $tabdatas['id_groupe'];
                                
                                $tb_infos['moy_period']= $value['moyenne'];
                                $tb_infos['rang']=  $rang;
                                $tb_infos['effectif_grp']=  $effectif;
                                $tb_infos['date_creation']=  date('Y-m-d H:i:s');
                                
                                $db_moy_part_annee=Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
                                $fct_exec =' Admin::set_updateSQL_ALL_by(Mise à jour moyenne etudiant)===='.$table.' & '.$part_anneid.' & '.$key.'  & '.$tabdatas['id_groupe'].' & '.$key.' & Moyenne ='.$tb_infos['moy_period'].' & rang ='.$rang.' & Effectif ='.$effectif.'   ||';

                            }
                        }
                    }

                }
            }

        }
        elseif (isset($_POST['btn_pv']) && isset($_POST['id_annee']) && isset($_POST['id_groupe'])) {
            $_SESSION['page'] ='admin_vue_pv';

            $tab_annee = explode("_", htmlspecialchars($_POST['id_annee']));
            $tab_grpe = explode("_", htmlspecialchars($_POST['id_groupe']));

            $id_annee = intval($tab_annee[1]);
            $tabdatas['id_annee']= $id_annee ;

            $tabdatas['info_annee']= htmlspecialchars($_POST['id_annee']);
            $tabdatas['info_groupe']= htmlspecialchars($_POST['id_groupe']);

            $tabdatas['annee'] = $tab_annee[2];
            $tabdatas['groupe'] = $tab_grpe[1];
            $tabdatas['id_groupe'] = intval($tab_grpe[0]);

            $table='annee_partie';
            $tb_conditions= [];
            $tb_conditions['id_anneeScolaire']= $tabdatas['id_annee'];
            $tabdatas['get_anneScol_Partie_By']=Admin::get_selectSQL_ALL_by($table, $tb_conditions);
            //var_dump( $tabdatas['get_anneScol_Partie_By']);
            

            $table='annee_session';
            $tb_conditions= [];
            $tabdatas['get_annee_session']=Admin::get_selectSQL_ALL_by($table, $tb_conditions);

            if (isset($_POST['periode']) && isset($_POST['session'])) {
                $id_periode = intval(htmlspecialchars($_POST['periode']));
                $id_session = intval(htmlspecialchars($_POST['session']));
                
                foreach ($tabdatas['get_annee_session'] as $key => $value) {
                   if (intval($value['id_session_session']) == $id_session) {
                       $tabdatas['session_lib']=$value['Libelle_session'];
                   }
                }

                foreach ($tabdatas['get_anneScol_Partie_By'] as $key => $value) {
                   if (intval($value['id_annee_partie']) == $id_periode) {
                       $tabdatas['periode_lib']=$value['libele_partie'];
                   }
                }
                $table='annee_scolaire';
                $tb_conditions= [];
                $tb_conditions['id_anscol_annee_scolaire']= $id_annee;
                $info_annee_scolaire=Admin::get_selectSQL_ALL_by($table, $tb_conditions);
                
                $tabdatas['info_anscol_lib']=explode(" ",($info_annee_scolaire[0])['annee_libelle']);

                $get_gpreEtudiantsBy = Admin::get_gpreEtudiantsBy($tabdatas['id_groupe'], $id_univ);
                //var_dump($get_gpreEtudiantsBy);
                $tabdatas['info_etudiants']=[];

                $get_grp_MatRepartie_WithMP_By = Admin::get_grp_MatRepartie_WithMP_By($id_univ, $tabdatas['id_groupe'], $id_annee);
                //var_dump('get_grp_MatRepartie_WithMP_By',$get_grp_MatRepartie_WithMP_By);


                foreach ($get_gpreEtudiantsBy as $cle => $val) {
                   ($tabdatas['info_etudiants'][$cle])['id_pers_personne']=$val['id_pers_personne'];
                   ($tabdatas['info_etudiants'][$cle])['id_type']=$val['id_type'];
                   ($tabdatas['info_etudiants'][$cle])['matricule']=$val['matricule'];
                   ($tabdatas['info_etudiants'][$cle])['nom_prenom']=$val['nom_prenom'];
                   ($tabdatas['info_etudiants'][$cle])['sexe']=$val['sexe'];

                    if ($get_grp_MatRepartie_WithMP_By!=0) {
                        $tb_mat2=[];
                        $tb_mat_MP=[];
                        $tb_mat_keys=[];
                      
                        foreach ($get_grp_MatRepartie_WithMP_By as $key => $value) {

                            if (intval($value['part_annee_id_tmp'])==$id_periode) {
                                
                                $tmp_idmatMP= intval($value['matiere_parent_id_tmp']);
                                $tmp_idmatid= intval($value['matiere_id_tmp']);

                                if (!isset($tb_mat_MP[$tmp_idmatMP])) {
                                    $tb_mat_MP[$tmp_idmatMP]["lib_mp"]=$value['MatP_lib'];
                                    $tb_mat_MP[$tmp_idmatMP]["MatP_code"]=$value['MatP_code'];
                                    $tb_mat_MP[$tmp_idmatMP]["nbre_ecue"]=0;
                                }
                                if (intval($value['coeficient_tmp']) != 0) {
                                   $credit = intval($value['coeficient_tmp']);
                                }
                                else{
                                    $credit = intval($value['credit_tmp']);
                                }

                                $tb_mat_MP[$tmp_idmatMP]["nbre_ecue"] ++;
                               ( $tb_mat_MP[$tmp_idmatMP][$tmp_idmatid])['credit']=$credit;
                               ( $tb_mat_MP[$tmp_idmatMP][$tmp_idmatid])['libmat']=$value['libele'];

                                $table='moyenne';
                                $tb_conditions= [];
                                $tb_conditions['id_groupe']= $tabdatas['id_groupe'];
                                $tb_conditions['id_eleve']= $val['id_type'];
                                $tb_conditions['id_matiere']= $value['matiere_id_tmp'];
                                $tb_conditions['id_session']= $id_session;
                                $tb_conditions['fk_part_annee']= $id_periode;
                                $moyenne_info=Admin::get_selectSQL_ALL_by($table, $tb_conditions);
                                //var_dump($moyenne_info);

                                $tmp_idmat= intval($value['matiere_id_tmp']);
                                
                                ($tabdatas['info_etudiants'][$cle])['ecue'][$tmp_idmat]= array($moyenne_info[0]['moyenne'], $value['matiere_parent_id_tmp']);
                                ($tabdatas['info_etudiants'][$cle])['ecue'][$tmp_idmat]['lib_mateft']=$value['libele'];
                                
                            }

                        }
                    } 
                }
                $tabdatas['tb_mat_MP']=$tb_mat_MP;
                //var_dump($tb_mat_MP);
                //var_dump($tabdatas['tb_mat_MP']);
                //var_dump(COUNT(($tabdatas['info_etudiants'][0])['ecue']));
                //var_dump($tabdatas['info_etudiants']);
                //var_dump(($tabdatas['info_etudiants'][0])['ecue']);
                //var_dump(($tabdatas['info_etudiants'][1])['ecue']);
                $tabdatas['nbre_ecue']=COUNT(($tabdatas['info_etudiants'][0])['ecue'])+COUNT($tb_mat_MP);
                

  








                



            }

        }



        
        $tabdatas['menu'] = "Evaluations";
        $tabdatas['sousmenu'] = "Autorisations";


        //var_dump($tabdatas['allannee']);


        $tabdatas['univ_id'] = $id_univ;


        $tabdatas['getAnneeScolaire'] = Admin::get_anneeBy($id_univ);
        $tabdatas['getClassesBy'] = Admin::getClassesBy($id_univ);
        $tabdatas['getNiveau'] = Admin::getNiveauBy($id_univ);

        //var_dump( $tabdatas['getAnneeScolaire'], $tabdatas['getClassesBy'], $tabdatas['getNiveau']);
        $_POST = NULL;
        $_GET = NULL;

        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_Admin ::: eval_pvAction => " . $fct_exec;
        $log_user =" Page PV/Bulletin ";
        modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
        //:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function admin_vue_bulletinAction()
    {
        $fct_exec = "||";
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $tabdatas['panel'] = 1;
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $id_univ = intval($tabdata_user->fk_iduniv);


        //var_dump($_GET);
        //var_dump($_POST);
        $str =  sha1("inscriptionélèves");
        //fk_iduniv_groupe_idelev
        if ($str == $_GET['action']) {
            $tab_infos = explode("_", $_GET['infos']);
            //var_dump($tab_infos);
            $fk_iduniv = intval($id_univ);
            $id_group = intval($tab_infos[1]);
            $id_etudiant = intval($tab_infos[2]);

            $tabdatas['get_grpeWithBultinInfos_By'] = Admin::get_grpeWithBultinInfos_By($id_group, $fk_iduniv);

            if( isset($tabdatas['get_grpeWithBultinInfos_By'][0]) ){
                $tabdatas['get_grpeWithBultinInfos_By'] = $tabdatas['get_grpeWithBultinInfos_By'][0];
                //var_dump('get_grpeWithBultinInfos_By',$tabdatas['get_grpeWithBultinInfos_By']);
                $id_annee = intval($tabdatas['get_grpeWithBultinInfos_By']['groupe_annee']);
                $tabdatas['get_anneScol_Partie_By'] = Admin::get_anneScol_Partie_By($id_annee);
                if( isset($tabdatas['get_anneScol_Partie_By'][0]) ){
                    //var_dump('get_anneScol_Partie_By',$tabdatas['get_anneScol_Partie_By']);
                    $tabdatas['annee_lib'] =explode(" ", ($tabdatas['get_anneScol_Partie_By'][0])['annee_libelle']);
                    //var_dump('annee_lib',$tabdatas['annee_lib']);

                    $tabdatas['get_etudiPers_Infos_By'] = (Admin::get_etudiPers_Infos_By($fk_iduniv, $id_etudiant))[0];
                    //var_dump('get_etudiPers_Infos_By',$tabdatas['get_etudiPers_Infos_By']);
                    $id_eleve_idpers = intval($tabdatas['get_etudiPers_Infos_By']['id_pers_personne']);
                    $img_liens = '../files/'. $id_eleve_idpers.'/'. $id_eleve_idpers.'.jpg';

                    if (file_exists($img_liens)) {  
                        //var_dump('exit',$img_liens); 
                        $tabdatas['get_etudiPers_Infos_By']['photo_exit']= 'oui'; }
                    else{ $tabdatas['get_etudiPers_Infos_By']['photo_exit']= 'non' ;}

                    //var_dump($tabdatas['get_etudiPers_Infos_By']['photo_exit']);


                    if (isset($_POST['semmestre_id'])) {

                        $part_anneid=intval($_POST['semmestre_id']);
                        $table='annee_partie';
                        $tb_conditions=[];
                        $tb_conditions['id_annee_partie']= $part_anneid;
                        $tabdatas['tb_annee_partie']=Admin::get_selectSQL_ALL_by($table,$tb_conditions);
                        //var_dump('tb_annee_partie',$tabdatas['tb_annee_partie']);

                        $table='moyenne_matf';
                        $tb_conditions=[];
                        $tb_conditions['fk_id_eleve']= $id_etudiant;
                        $tb_conditions['fk_id_partAnnee']= $part_anneid;
                        $tb_conditions['fk_id_grpe']= $id_group;
                        $tabdatas['tble_moy_rang']=Admin::get_selectSQL_ALL_by($table,$tb_conditions);
                        //var_dump('tble_moy_rang',$tabdatas['tble_moy_rang']);

                        
                        $table='moyenne_periode';
                        $tb_conditions=[];
                        $tb_conditions['fk_id_periode']= $part_anneid;
                        $tb_conditions['fk_id_groupe']= $id_group;
                        $tabdatas['tble_moy_f']=Admin::get_selectSQL_ALL_by($table,$tb_conditions);
                        //var_dump('tble_moy_rang',$tabdatas['tble_moy_rang']);

                        $get_absence_eleve=Admin::get_absence_eleve(intval(($tabdatas['tb_annee_partie'][0])['id_anneeScolaire']),$part_anneid,$id_etudiant);
                        //var_dump('get_absence_eleve',$get_absence_eleve);

                        $tabdatas['absence_eleve_justif']='00:00:00';
                        $tabdatas['absence_eleve_nonjustif']='00:00:00';
                         
                        if ($get_absence_eleve != 0 && !empty($get_absence_eleve)) {
                        foreach ($get_absence_eleve as $key => $value) {
                            //non justifier
                            if($value['abs_justif']==0){
                                //var_dump($value['emploitps_h_debut']);
                                //var_dump($value['emploitps_h_fin']);
                                
                                $t1 =$value['emploitps_h_debut'];
                                $t2 =$value['emploitps_h_fin'];
                                $tf=Admin::diff_time($t1 , $t2);
                                //var_dump($tf);
                                $tabdatas['absence_eleve_nonjustif']=Admin::add_time($tabdatas['absence_eleve_nonjustif'] , $tf);
                                //var_dump($tabdatas['absence_eleve_nonjustif']);


                            }
                            //justifier
                            elseif($value['abs_justif']==1){

                                //var_dump($value['emploitps_h_debut']);
                                //var_dump($value['emploitps_h_fin']);  

                                $t1 =$value['emploitps_h_debut'];
                                $t2 =$value['emploitps_h_fin'];
                                $tf=Admin::diff_time($t1 , $t2);
                                $tabdatas['absence_eleve_justif']=Admin::add_time($tabdatas['absence_eleve_justif'] , $tf);

                                //var_dump($tabdatas['absence_eleve_justif']);

                            }
                        }
                        }



                        $tb_moy = [];
                        foreach ($tabdatas['tble_moy_f'] as $keys => $val) {
                            array_push($tb_moy,(float)$val['moy_period']);
                        }
                        //var_dump('tb_moy',$tb_moy);
                        $tabdatas['min_moy'] = min($tb_moy);
                        $tabdatas['max_moy'] = max($tb_moy);
                        $tabdatas['fc_moy'] = array_sum($tb_moy)/count($tb_moy);
                        //var_dump('min_moy= '.$tabdatas['min_moy'].'  max_moy='.$tabdatas['max_moy'].'   fc_moy='.$tabdatas['fc_moy']);

                        $tabdatas['get_eleve_all_moy'] = Admin::get_eleve_all_moy($id_etudiant,$id_annee,$id_group);
                        //var_dump('get_eleve_all_moy',$tabdatas['get_eleve_all_moy']);
                        $tb_mat2=[];
                        $tb_mat_MP=[];
                        $tb_mat_keys=[];

                        $var_coef=0;
                        $var_moyen_coef=0;

                        $tb_mat1= Admin::get_grp_MatRepartie_By($id_univ, $id_group, $id_annee);
                        //var_dump($tb_mat1);exit;
                        if (!empty($tb_mat1)) {
                            foreach ($tb_mat1 as $key => $value) {
                                $var_test=0;
                                if ( intval($value['id_annee_partie'])== $part_anneid ) {

                                    $moyenne_tmp = Admin::get_elevmoy_by($id_etudiant,intval($value['matiere_id_tmp']),$id_group,$part_anneid);
                                    //var_dump($moyenne_tmp);var_dump($value);exit;
                                    if ($moyenne_tmp['moyenne']==null) {
                                        $moyenne_tmp['moyenne']=0;
                                    }
                                    $tb_mat2[$key]=$value;
                                    $tmp_moye=floatval($moyenne_tmp['moyenne']);
                                    $tb_mat2[$key]['moyenne']=$tmp_moye;
                                    $tb_mat2[$key]['prof_name']=Admin::get_mat_grp_prof(intval($value['matiere_id_tmp']),intval($value['groupe_id_tmp']));

                                    if( intval($tmp_moye)==20) { $tb_mat2[$key]['Appreciation']='Excellent'; }
                                    elseif( $tmp_moye<20 && $tmp_moye>=16) { $tb_mat2[$key]['Appreciation']='Très bien'; }
                                    elseif( $tmp_moye<16 && $tmp_moye>=14) { $tb_mat2[$key]['Appreciation']='Bien'; }
                                    elseif( $tmp_moye<14 && $tmp_moye>=12) { $tb_mat2[$key]['Appreciation']='Assez Bien'; }
                                    elseif( $tmp_moye<12 && $tmp_moye>=10) { $tb_mat2[$key]['Appreciation']='Passable'; }
                                    elseif( $tmp_moye<10 && $tmp_moye>=5) { $tb_mat2[$key]['Appreciation']='Insuffisant'; }
                                    else{ $tb_mat2[$key]['Appreciation']='Très faible'; }

                                    if ($value['coeficient_tmp'] != '0') { 
                                        $var_coef =$var_coef + floatval($value['coeficient_tmp']); 
                                        $var_moyen_coef=$var_moyen_coef + floatval($value['coeficient_tmp'])* $moyenne_tmp['moyenne'];
                                    }
                                    else { 
                                        $var_coef =$var_coef + floatval($value['credit_tmp']); 
                                        $var_moyen_coef=$var_moyen_coef + floatval($value['credit_tmp'])* $moyenne_tmp['moyenne'];
                                    }
                                    
                                }
                            }
                        }
                        $tabdatas['get_grp_MatRepartie_By']= $tb_mat2;
                        //var_dump('get_grp_MatRepartie_By',$tabdatas['get_grp_MatRepartie_By']);

                        $get_grp_MatRepartie_WithMP_By = Admin::get_grp_MatRepartie_WithMP_By($id_univ, $id_group, $id_annee);
                        //*var_dump('get_grp_MatRepartie_WithMP_By',$get_grp_MatRepartie_WithMP_By);*
                        if ($get_grp_MatRepartie_WithMP_By!=0) {
                        
                            foreach ($get_grp_MatRepartie_WithMP_By as $key => $value) {

                                if ( intval($value['id_annee_partie'])== $part_anneid ) {

                                    $moyenne_tmp = Admin::get_elevmoy_by($id_etudiant,intval($value['matiere_id_tmp']),$id_group,$part_anneid);
                                    //var_dump($moyenne_tmp);

                                    if ($moyenne_tmp['moyenne']==null) {
                                        $moyenne_tmp['moyenne']=0;
                                    }

                                    if ($value['coeficient_tmp'] != '0') { 
                                        $var_coef =$var_coef + floatval($value['coeficient_tmp']); 
                                        $var_moyen_coef=$var_moyen_coef + floatval($value['coeficient_tmp'])* $moyenne_tmp['moyenne'];
                                    }
                                    else { 
                                        $var_coef =$var_coef + floatval($value['credit_tmp']); 
                                        $var_moyen_coef=$var_moyen_coef + floatval($value['credit_tmp'])* $moyenne_tmp['moyenne'];
                                    }

                                    $tb_mat_MP[$key]=$value;
                                    $tmp_moye =floatval($moyenne_tmp['moyenne']);
                                    $tb_mat_MP[$key]['moyenne']=$tmp_moye;
                                    $tb_mat_MP[$key]['prof_name']=Admin::get_mat_grp_prof(intval($value['matiere_id_tmp']),intval($value['groupe_id_tmp']));
                                    ;

                                    if( intval($tmp_moye)==20) { $tb_mat_MP[$key]['Appreciation']='Excellent'; }
                                    elseif( $tmp_moye<20 && $tmp_moye>=16) { $tb_mat_MP[$key]['Appreciation']='Très bien'; }
                                    elseif( $tmp_moye<16 && $tmp_moye>=14) { $tb_mat_MP[$key]['Appreciation']='Bien'; }
                                    elseif( $tmp_moye<14 && $tmp_moye>=12) { $tb_mat_MP[$key]['Appreciation']='Assez Bien'; }
                                    elseif( $tmp_moye<12 && $tmp_moye>=10) { $tb_mat_MP[$key]['Appreciation']='Passable'; }
                                    elseif( $tmp_moye<10 && $tmp_moye>=5) { $tb_mat_MP[$key]['Appreciation']='Insuffisant'; }
                                    else{ $tb_mat_MP[$key]['Appreciation']='Très faible'; }

                                }
                            }
                        }    
                        $tabdatas['var_moyen_coef']=$var_moyen_coef;
                        $tabdatas['var_coef']=$var_coef;

                        $tabdatas['get_grp_MatRepartie_WithMP_By']=$tb_mat_MP;
                        //var_dump('get_grp_MatRepartie_WithMP_By',$tabdatas['get_grp_MatRepartie_WithMP_By']);
                        $table='moyenne_periode';
                        $tb_conditions= [];
                        $tb_conditions['fk_id_periode']= $part_anneid;
                        $tb_conditions['fk_id_eleve']= $id_etudiant;
                        $tb_conditions['fk_id_groupe']=  $id_group;
                        $db_moy_part_annee=Admin::get_selectSQL_ALL_by($table, $tb_conditions);
                        
                        $tabdatas['db_moy_part_annee']=$db_moy_part_annee[0];
                        //var_dump('db_moy_part_annee',$tabdatas['db_moy_part_annee']);

                    }
                }
            }

            //var_dump($tabdatas['get_grpeWithBultinInfos_By']);
            //var_dump($tabdatas['get_grp_MatRepartie_WithMP_By']);


        } 
        else {
            # code...
        }

        $tabdatas['menu'] = "Examen et Evaluation";
        $tabdatas['sousmenu'] = "Bulletin de notes";


        //var_dump($tabdatas['allannee']);


        $tabdatas['univ_id'] = $id_univ;


        $tabdatas['getAnneeScolaire'] = Admin::get_anneeBy($id_univ);
        $tabdatas['getClassesBy'] = Admin::getClassesBy($id_univ);
        $tabdatas['getNiveau'] = Admin::getNiveauBy($id_univ);

        //var_dump( $tabdatas['getAnneeScolaire'], $tabdatas['getClassesBy'], $tabdatas['getNiveau']);
        $_POST = NULL;
        $_GET = NULL;
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_Admin ::: eval_pvAction => " . $fct_exec;
        modeldb::set_AllLog($info);
        /*:::::::Fin Enregistrement des logs::::::::::*/
        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function logsAction()
    {

        $fct_exec = "||";

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;

        $tabdatas['notif_file'] = [];
        $id_notif_tab = [];
 
        //$tabdatas['get_userNotifs']=modelUser::get_userNotifs($tabdata_user->id_type, 4);
        $tabdatas['get_userNotifs'] = modelUser::get_userNotifsBy($tabdata_user->id_type, 4, $tabdata_user->fk_iduniv);
        $tabdatas['menu'] = "Logs";
        $tabdatas['sousmenu'] = "infos";
        //var_dump($_POST);
        if (isset($_POST['btn_search_logs'])) {

            $debut= $_POST['date_debut'];
            $fin= $_POST['date_fin'];
            $type_user= intval($_POST['type_user']);

            $tabdatas['get_all_logs_By'] = modeldb::get_all_logs_By($debut,$fin,$type_user,$tabdata_user->fk_iduniv);
             $tabdatas['post'] = $_POST;
            //var_dump($tabdatas['get_all_logs_By']);
        }


        //var_dump($tabdatas['notif_file']);
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
 


        $_POST = NULL;
        $_GET = NULL;
        unset($_POST);
        unset($_GET);
        //var_dump($tabdatas);

        View::renderTemplate('Accueil/admin/logs.html', $tabdatas);
    }
    public function logs_errorAction()
    {

        $fct_exec = "||";

        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;

        $tabdatas['notif_file'] = [];
        $id_notif_tab = [];
 
        //$tabdatas['get_userNotifs']=modelUser::get_userNotifs($tabdata_user->id_type, 4);
        $tabdatas['get_userNotifs'] = modelUser::get_userNotifsBy($tabdata_user->id_type, 4, $tabdata_user->fk_iduniv);
        $tabdatas['menu'] = "Error logs";
        $tabdatas['sousmenu'] = "error";
        //var_dump($_POST);exit();
        if (isset($_POST['btn_search_logs'])) {

            $debut= $_POST['date_debut'];
            $fin= $_POST['date_fin'];
            //var_dump($debut,$fin);
            $type_user= intval($_POST['type_user']);

            $tabdatas['get_all_logs_By'] = modeldb::get_all_errorlogs_By($debut,$fin,$type_user,$tabdata_user->fk_iduniv);
            $tabdatas['post'] = $_POST;
            //var_dump($tabdatas['get_all_logs_By']);
        }


        //var_dump($tabdatas['notif_file']);
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
 


        $_POST = NULL;
        $_GET = NULL;
        unset($_POST);
        unset($_GET);
        //var_dump($tabdatas);

        View::renderTemplate('Accueil/admin/logs_error.html', $tabdatas);
    }

    
    public function admin_elev_cieScolAction()
    {
        $fct_exec = "||";
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $id_pers = $tabdata_user->id_pers_personne;
        $id_univ = intval($tabdata_user->fk_iduniv); 
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($id_univ);
        //var_dump($tabdatas['univInfos']);
        $log_user =" Page Vie scolaire ";

        //var_dump($_POST,$_GET);exit;

        $tabdatas['allannee'] = Admin::get_anneeBy($id_univ);
        $fct_exec=$fct_exec. "Admin::get_anneeBy(".$id_univ.") || ";
        //var_dump($_POST);
        if (isset($_POST['moy_annee']) && isset($_POST['moy_classe']) && isset($_POST['btn_afficheMoy'])  ) {
            
            $id_groupe = intval( htmlspecialchars($_POST['moy_classe']) );
            $tabdatas['moy_classe'] =  $id_groupe;
            $tabdatas['moy_annee'] = intval( htmlspecialchars($_POST['moy_annee']) );

            $tabdatas['suivi_classe'] = $id_groupe;

            $table= "groupe";
            $tb_conditions=[]; 
            $tb_conditions['groupe_id'] =$id_groupe;
            $tabdatas['info_groupe'] = Model_public::get_selectSQL_ALL_by($table, $tb_conditions);

            $tabdatas['getAll_elvDSgrp'] = Prof::getAll_elvDSgrp($id_groupe);
            $tabdatas['effectif_classe']= count($tabdatas['getAll_elvDSgrp']);
            
            $fct_exec= $fct_exec."Affichage liste de classe  :  Prof::getAll_elvDSgrp(".$id_groupe.");|| ";

            $tabdatas['get_gpreALLMat_By'] = Admin::get_gpreALLMat_By($id_groupe);
            //var_dump( $tabdatas['get_gpreALLMat_By']);
            $fct_exec= $fct_exec."Affichage liste des matieres de la classe  :  Prof::get_gpreALLMat_By(".$id_groupe.");|| ";

            $tabdatas["getEmploiTpsBy"] = Admin::get_GroupEmploiDutpsBy($id_groupe);
            //var_dump( $tabdatas['getEmploiTpsBy']);
            
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

            
        }

        if ( isset($_POST['id_classe']) && isset($_POST['effectif_classe']) ) {
            //{{ eleve.id_eleve_eleve }}_{{ eleve.matricule }}_{{ eleve.nom_prenom }}_0_{{ suivi_classe }}

            $id_groupe=intval( htmlspecialchars($_POST['id_classe'])) ;
            $tabdatas['effectif_groupe']=intval(htmlspecialchars($_POST['effectif_classe'])) ;
            $table= "groupe";
            $tb_conditions=[]; 
            $tb_conditions['groupe_id'] =$id_groupe;
            $tabdatas['info_groupe'] = Model_public::get_selectSQL_ALL_by($table, $tb_conditions);
            $tabdatas['getAll_elvDSgrp'] = Prof::getAll_elvDSgrp($id_groupe);

            $table= "annee_scolaire";
            $tb_conditions=[]; 
            $tb_conditions['id_anscol_annee_scolaire'] =($tabdatas['info_groupe'][0])['groupe_annee'];
            $annescol = (Model_public::get_selectSQL_ALL_by($table, $tb_conditions))[0];
            $tabdatas['annescol']=(explode(' ',$annescol['annee_libelle']))[0];
            //var_dump($tabdatas['getAll_elvDSgrp']);

            if (isset($_POST['btn_fich_presence'])) {
                //var_dump($annescol,$tabdatas['annescol']);
                //var_dump($tabdatas['info_groupe'],$tabdatas['getAll_elvDSgrp'],$tabdatas['univInfos']);
                $fct_exec= $fct_exec."Affichage liste de classe  :  Prof::getAll_elvDSgrp(".$id_groupe.");|| ";
                $log_user =$log_user.' : Impression de fiche de présence du groupe ='.$id_groupe;
                $_SESSION['page'] ="admin_fiche_presence";
            }
            elseif (isset($_POST['btn_fich_note'])) {
                $_SESSION['page'] ='admin_fiche_note';
            }
            elseif (isset($_POST['btn_fich_classe'])) {
                $_SESSION['page'] ='admin_fiche_classe';
            }
            elseif (isset($_POST['btn_certif_eleve'])) {
                //var_dump($_POST);
                $_SESSION['page']='admin_model_attest';
                $infos=explode('_',htmlspecialchars($_POST['btn_certif_eleve']) );
                //var_dump($infos);
                $id_eleve = intval($infos[0]);
                $tabdatas['get_elevBy'] = Admin::get_elevBy($id_eleve);
                $tabdatas['get_eleves_allGrpe'] = Eleve::get_eleves_allGrpe($id_eleve);
                //var_dump($tabdatas['get_elevBy']);
            }
            else{}




        }


        $tabdatas['menu'] = "Gestion des Etudiants";
        $tabdatas['sousmenu'] = "Vie scolaire";


        //var_dump( $tabdatas['getAnneeScolaire'], $tabdatas['getClassesBy'], $tabdatas['getNiveau']);
        $_POST = NULL;
        $_GET = NULL;
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_Admin ::: admin_elev_cieScolAction => " . $fct_exec;
		
		modeldb::set_Ajax_Log($info,$log_user,$id_pers,$id_univ);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/

        //var_dump("post = ",$_POST,"get =",$_GET,"session =",$_SESSION);
        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function admin_notesAction()
    {
        $fct_exec = "||";
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $id_pers_personne = intval($tabdata_user->id_pers_personne);
        $tabdatas['panel'] = 1;
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $id_univ = intval($tabdata_user->fk_iduniv);
        $tabdatas['univ_id'] = $id_univ;
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
        $fct_exec="|";
        
        
        $table="groupe";
        $tb_conditions=[];
        $tb_conditions["groupe_annee"]=$tabdata_user->lib_user_id_annee;
        $tabdatas['allgroupeBy'] = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
        //var_dump($tabdatas['allgroupeBy']);

        $table="annee_partie";
        $tb_conditions=[];
        $tb_conditions["id_anneeScolaire"]=$tabdata_user->lib_user_id_annee;
        $tabdatas['allpartanneeBy'] = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
        //var_dump($tabdatas['allpartanneeBy']);

        //var_dump($_POST);


        $tabdatas['allannee'] = Admin::get_anneeBy($id_univ);
        $tabdatas['allSession'] = Prof::getSession();
        
        if (isset($_POST['prof_eval_btnsubmit']) && $_POST['moy_classe']!=""  && $_POST['prof_eval_periode']!="" && $_POST['prof_eval_mat']!="" && $_POST['prof_eval_libelle']!="") {
            //var_dump($_POST);exit();

            $prof_eval_date =  (htmlspecialchars($_POST['prof_eval_date']));
            $prof_eval_date =  strtotime($prof_eval_date);
            $prof_eval_date =  date("d/m/Y",  $prof_eval_date) ;

            $id_grpe = intval(htmlspecialchars($_POST['moy_classe']));
            $id_mat = intval(htmlspecialchars($_POST['prof_eval_mat']));
            $id_periode = intval(htmlspecialchars($_POST['prof_eval_periode']));

            $id_session = intval(htmlspecialchars($_POST['session_id']));
            $eval_lib= htmlspecialchars($_POST['prof_eval_libelle']);
            $eval_desc= htmlspecialchars($_POST['prof_eval_desc']);
            $eval_type= htmlspecialchars($_POST['prof_eval_select_type']);

            $eval_date= $prof_eval_date;
            $eval_hDebut= htmlspecialchars($_POST['prof_eval_hDebut']);
            $eval_hFin= htmlspecialchars($_POST['prof_eval_hFin']);
            $coef = floatval(htmlspecialchars($_POST['coef']));
            $notation = intval(htmlspecialchars($_POST['notation']));

            $tabdatas['setClasse_Eval'] = Admin::setClasse_Eval($id_grpe ,$id_mat ,$eval_lib ,$eval_desc, $id_session ,$eval_type, $eval_date,$eval_hDebut ,$eval_hFin, $id_periode,$coef,$notation,$id_pers_personne );

            $tabdatas['toast_notif']['etat'] = "success";
            $tabdatas['toast_notif']['infos'] = "Création de l'évaluation ".$eval_lib." effectuée";     

            if (isset($_POST['moy_classe_multiple'])) {
                $moy_classe_multiple = $_POST['moy_classe_multiple'];
                if (!empty($moy_classe_multiple)) {

                    foreach ($moy_classe_multiple as $key => $value) {
                        $id_grpe = intval(htmlspecialchars($value));
                        $tabdatas['setClasse_Eval'] = Admin::setClasse_Eval($id_grpe ,$id_mat ,$eval_lib ,$eval_desc, $id_session ,$eval_type, $eval_date,$eval_hDebut ,$eval_hFin, $id_periode,$coef,$notation,$id_pers_personne );
                    }

                }
            }            

            if (  $tabdatas['setClasse_Eval']!= 0) {
                $tabdatas['toast_notif']['etat'] = "success";
                $tabdatas['toast_notif']['infos'] = "Evaluation : ".$eval_lib." Creer";
            }
            else {
                $tabdatas['toast_notif']['etat'] = "danger";
                $tabdatas['toast_notif']['infos'] = "Erreur lors de la création de l'évaluation :".$eval_lib;
            }

        }
        elseif (isset($_POST['btn_notes_eval'])) {

            $tabdatas['panel'] = 2;
            $id_eval = intval(htmlspecialchars($_POST['btn_notes_eval']));

            if (isset($_POST['btn_active_eval']) && isset($_POST['btn_notes_evalname']) ) {

                $btn_notes_evalname = htmlspecialchars($_POST['btn_notes_evalname']);

                $table= "prof_eval"; 
                $tb_infos=[];
                $tb_infos["eval_etat"]=1;
                $tb_conditions=[];
                $tb_conditions["prof_eval_id"]=$id_eval;
                $sqlupdate = Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);

                $id_pers=$id_pers_personne;
                $fk_iduniv=$id_univ;
                $fct_exec="Mise à jour etat evaluation ".$btn_notes_evalname." | id_eval=". $id_eval." etat=".$tb_infos["eval_etat"]." & resultat = ".$sqlupdate;

                $tabdatas['toast_notif']['etat'] = "success";
                $tabdatas['toast_notif']['infos'] = "Activation de l'évaluation  ".$btn_notes_evalname; 

                /*:::::::DEBUT Enregistrement des logs::::::::::*/
                $info = "Crt_Admin ::: admin_notesAction => " . $fct_exec;
                $log_user =" Page Evaluation - Saisi de Notes - Activation d'évaluation  ";
                modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
                //:::::::::::::LOGS::::::::::::::::::
                /*:::::::Fin Enregistrement des logs::::::::::*/
            }
            elseif (isset($_POST['btn_desactive_eval']) && isset($_POST['btn_notes_evalname']) ) {

                $btn_notes_evalname = htmlspecialchars($_POST['btn_notes_evalname']);

                $table= "prof_eval"; 
                $tb_infos=[];
                $tb_infos["eval_etat"]=3;
                $tb_conditions=[];
                $tb_conditions["prof_eval_id"]=$id_eval;
                $sqlupdate = Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);

                $id_pers=$id_pers_personne;
                $fk_iduniv=$id_univ;
                $fct_exec="Mise à jour etat evaluation ".$btn_notes_evalname." | id_eval=". $id_eval." etat=".$tb_infos["eval_etat"]." & resultat = ".$sqlupdate;

                $tabdatas['toast_notif']['etat'] = "success";
                $tabdatas['toast_notif']['infos'] = "Désactivation de l'évaluation  ".$btn_notes_evalname; 

                /*:::::::DEBUT Enregistrement des logs::::::::::*/
                $info = "Crt_Admin ::: admin_notesAction => " . $fct_exec;
                $log_user =" Page Evaluation - Saisi de Notes - Désactivation d'évaluation  ";
                modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
                //:::::::::::::LOGS::::::::::::::::::
                /*:::::::Fin Enregistrement des logs::::::::::*/
            }
                    
            $tabdatas['prof_eval_date']=Prof::getProfGrpEvalWithDateBy($id_eval);
            $tabdatas['prof_eval_salle']=Prof::getEvalSalle($id_eval);

            
            $evalinfos = Admin::get_profeval_infosBy($id_eval);
            if ($evalinfos==0) {
               $evalinfos = Admin::get_admineval_infosBy($id_eval);
            }
            $tabdatas['evalinfos'] =  $evalinfos;
            //var_dump($tabdatas['evalinfos']);
            $tmp_evalmoy_eleve =[];
            $tmp_evalmoy_eleve=Prof::getAllEleveByGroup($id_eval);
            //var_dump($evalinfos[0]['eval_session']);
            //var_dump($tmp_evalmoy_eleve);
            
            $prof_eval_grpEleve_tmp =[];
            if(!empty($tabdatas['evalinfos']) && !empty($tmp_evalmoy_eleve) && (intval($evalinfos[0]['eval_session']) == 2) ) {
                foreach ( $tmp_evalmoy_eleve as $key => $value) {
                   
                        $id_groupe =intval($evalinfos[0]['id_groupe']) ;
                        $id_eleve = $value["id_eleve_eleve"];
                        $id_matiere = intval($evalinfos[0]['id_mat']) ;
                        $fk_part_annee = intval($evalinfos[0]['fk_idpartAneeScol']) ;

                        $get_eleve_moyetatvalidBy=Admin::get_eleve_moyetatvalidBy($id_groupe,$id_eleve,$id_matiere,1,$fk_part_annee);
                        //var_dump($get_eleve_moyetatvalidBy);

                        if ($get_eleve_moyetatvalidBy==0) {
                            array_push($prof_eval_grpEleve_tmp,$value);
                        }
                   
                }
            }
            else {$prof_eval_grpEleve_tmp=$tmp_evalmoy_eleve; }
            //var_dump($prof_eval_grpEleve_tmp);


            $tabdatas['prof_eval_grpEleve']=[];
            foreach ( $prof_eval_grpEleve_tmp as $key => $value) {
                $reps = Prof::setGetInitEleveEvalNote(intval($prof_eval_grpEleve_tmp[$key]['id_eleve_eleve'] ), $id_eval);

                //var_dump($reps);
                
                $table="personne"; 
                $tb_conditions=[];
                $tb_conditions["id_pers_personne"]=$reps[0]["fk_ipers"];
                $infos_createur = Admin::get_selectSQL_ALL_by($table, $tb_conditions);

                //var_dump($infos_createur);
                if ($infos_createur==0) {$reps[0]['createur']="";}
                else {$reps[0]['createur']=$infos_createur[0]["nom_prenom"];}


                array_push($tabdatas['prof_eval_grpEleve'],$reps);
            }
            

        }
        elseif(isset($_POST['btn_edit_eval'])){
            //var_dump($_POST);
            $tabdatas['panel'] = 1;
            $id_eval = intval(htmlspecialchars($_POST['btn_edit_eval']));
            $evalinfos = Admin::get_profeval_infosBy($id_eval);
            if ($evalinfos==0) {
               $evalinfos = Admin::get_admineval_infosBy($id_eval);
               if ($evalinfos!=0) {
                   $evalinfos=$evalinfos[0];
               }
            }
            else{$evalinfos=$evalinfos[0];}

            $tabdatas['evalinfos']=$evalinfos;
        }
        elseif(isset($_POST['prof_eval_btnmodif'])){
            //var_dump($_POST);

            $table="prof_eval";
            $tb_infos=[]; 
            $tb_conditions=[];
            $tb_conditions["prof_eval_id"]= intval(htmlspecialchars($_POST['prof_eval_btnmodif'])) ;
            $tb_infos["fk_idpartAneeScol"]=intval(htmlspecialchars($_POST['prof_eval_periode'])) ;
            $tb_infos["id_groupe"]=intval(htmlspecialchars($_POST['moy_classe'])) ;
            $tb_infos["id_mat"]=intval(htmlspecialchars($_POST['prof_eval_mat'])) ;
            $tb_infos["eval_libelle"]=htmlspecialchars($_POST['prof_eval_libelle']); 
            $tb_infos["eval_desc"]=htmlspecialchars($_POST['prof_eval_desc']); 
            $tb_infos["eval_session"]=htmlspecialchars($_POST['session_id']); 
            $tb_infos["eval_type"]=htmlspecialchars($_POST['prof_eval_select_type']); 
            $tb_infos["eval_etat"]=1; 
            $tabdatas['prof_eval'] = Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);

            $table="prof_eval_emploitps";
            $tb_infos=[]; 
            $tb_conditions=[];
            $tb_conditions["eval_id"]= intval(htmlspecialchars($_POST['prof_eval_btnmodif'])) ;
            $tb_infos["eval_date"]=htmlspecialchars($_POST['prof_eval_date']); 
            $tb_infos["eval_hDebut"]=htmlspecialchars($_POST['prof_eval_hDebut']); 
            $tb_infos["eval_hFin"]=htmlspecialchars($_POST['prof_eval_hFin']); 
            $tb_infos["coef"]=floatval(htmlspecialchars($_POST['coef'])); 
            $tb_infos["notation"]=intval(htmlspecialchars($_POST['notation'])); 
            $tabdatas['prof_eval_emploitps'] = Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);

            $tabdatas['toast_notif']['etat'] = "success";
            $tabdatas['toast_notif']['infos'] = "Mise à jour de l'évaluation  ".htmlspecialchars($_POST['prof_eval_libelle']); 

        }
        elseif(isset($_POST['btn_del_eval'])){
            //var_dump($_POST);
            $table="prof_eval";
            $tb_conditions=[];
            $tb_conditions["prof_eval_id"]= intval(htmlspecialchars($_POST['btn_del_eval'])) ;
            $tabdatas['btn_del_eval'] = Admin::set_deleteSQL_ALL_by($table,$tb_conditions);

            $table="prof_eval_emploitps";
            $tb_conditions=[];
            $tb_conditions["eval_id"]= intval(htmlspecialchars($_POST['btn_del_eval'])) ;
            $tabdatas['btn_del_eval'] = Admin::set_deleteSQL_ALL_by($table,$tb_conditions);  

            $tabdatas['toast_notif']['etat'] = "success";
            $tabdatas['toast_notif']['infos'] = "Evaluation suprimer";          
        }
        elseif(isset($_POST['btn_notes_bilan'])){
            //var_dump($_POST);
            $tabdatas['panel'] = 3;
            //$notes_annee= intval(htmlspecialchars($_POST['notes_annee'])); 
            $id_partannee= intval(htmlspecialchars($_POST['notes_période'])); 
            $id_groupe= intval(htmlspecialchars($_POST['notes_classe'])); 
            $id_mat= intval(htmlspecialchars($_POST['notes_mat'])); 
            $id_session= intval(htmlspecialchars($_POST['notes_session'])); 


            $tabdatas['eval_info']['id_partannee']=$id_partannee;
            $tabdatas['eval_info']['id_groupe']=$id_groupe;
            $tabdatas['eval_info']['id_mat']=$id_mat;
            $tabdatas['eval_info']['id_session']=$id_session;

            $table="annee_partie"; 
            $tb_conditions=[];
            $tb_conditions["id_annee_partie"]=$id_partannee;
            $tabdatas['eval_info']['lib_part_anne'] = (Admin::get_selectSQL_ALL_by($table, $tb_conditions))[0]["libele_partie"];

            $table="annee_session"; 
            $tb_conditions=[];
            $tb_conditions["id_session_session"]=$id_session;
            $tabdatas['eval_info']['lib_session'] = (Admin::get_selectSQL_ALL_by($table, $tb_conditions))[0]["Libelle_session"];

            $table="groupe"; 
            $tb_conditions=[];
            $tb_conditions["groupe_id"]=$id_groupe;
            $tabdatas['eval_info']['lib_groupe'] = (Admin::get_selectSQL_ALL_by($table, $tb_conditions))[0]["groupe_libelle"];

            $table="matiere"; 
            $tb_conditions=[];
            $tb_conditions["id_matiere_matiere"]=$id_mat;
            $info_mat=Admin::get_selectSQL_ALL_by($table, $tb_conditions);
            $tabdatas['eval_info']['lib_mat'] = "(".($info_mat)[0]["libele"].")-".($info_mat)[0]["libele"];

            $tabdatas["grpematiere"] = Admin::get_mat_By_gpeperiode($id_groupe,$id_partannee);
            
            //var_dump($tabdatas['grpematiere']);
            //{{eval_info.id_groupe}}_{{valueelv.id_eleve_eleve}}_{{eval_info.id_mat}}_{{eval_info.id_session}}_{{moyenne}}_{{eval_info.id_partannee}}
            if (isset($_POST['input_eleve_matmoy']) && !empty($_POST['input_eleve_matmoy'])) {
                $moy_id_prof=0;
                foreach ($_POST['input_eleve_matmoy'] as $key => $value) {
                    $infomoyset = explode("_",  $value );
                    $moy_id_groupe = $infomoyset[0] ;
                    $moy_id_eleve_eleve = $infomoyset[1] ;
                    $moy_id_matiere_matiere = $infomoyset[2] ;
                    $moy_id_session_session = $infomoyset[3] ;
                    $moy_moyenne = $infomoyset[4] ;
                    $fk_part_annee = $infomoyset[5] ;

                    $moy_id_prof=Admin::get_mat_grp_prof($moy_id_matiere_matiere,$moy_id_groupe);
                    if (!empty($moy_id_prof)) {
                        $moy_id_prof=$moy_id_prof['id_prof'];
                    }
                    //var_dump($moy_id_prof['id_prof']);

                    $tabdatas['setEleve_annee_moyBY']=Admin::setEleve_annee_moyBY($moy_id_groupe, $moy_id_eleve_eleve , $moy_id_matiere_matiere, $moy_id_prof, $moy_id_session_session, $moy_moyenne, $fk_part_annee, $id_pers_personne);
                }
                
                

            }
            //$tabdatas['getetat_envoimoy']= Prof::get_moy_etat_envoi($id_groupe, $id_mat, $etatmoy_prof, $id_session, $id_partannee);

            $tmp_evalmoy_eleve= Admin::getAllElevByGrp($id_groupe);
            $tabdatas['getAllElevByGrp'] =[];
            //var_dump($tmp_evalmoy_eleve);
            if (!empty($tmp_evalmoy_eleve) && $id_session == 2) {
                foreach ( $tmp_evalmoy_eleve as $key => $value) {
                    $id_eleve = $value["id_eleve_eleve"];
                    $id_matiere = intval($id_mat) ;
                    $fk_part_annee = intval($id_partannee) ;
                    $get_eleve_moyetatvalidBy=Admin::get_eleve_moyetatvalidBy($id_groupe,$id_eleve,$id_matiere,1,$fk_part_annee);
                    //var_dump($get_eleve_moyetatvalidBy);
                    if ($get_eleve_moyetatvalidBy==0) {
                        array_push($tabdatas['getAllElevByGrp'],$value);
                    }
                   
                }
            }
            else {$tabdatas['getAllElevByGrp']=$tmp_evalmoy_eleve; }
            //var_dump($tabdatas['getAllElevByGrp']);

            $tabdatas['get_infos_eval_By'] = Admin::get_infos_eval_By($id_partannee, $id_groupe, $id_mat, $id_session);
            //var_dump($tabdatas['getAllElevByGrp']);

            if ($tabdatas['get_infos_eval_By'] != 0) {
                foreach ($tabdatas['get_infos_eval_By'] as $key => $value) {
                   ($tabdatas['get_infos_eval_By'][$key])['notes_eleves'] = Admin::get_infos_notes_By($value['prof_eval_id']);
                }
            }
            //var_dump($tabdatas['get_infos_eval_By']);
            //var_dump($tabdatas['get_infos_eval_By'][0]);

            $get_etat_moyenne_By = Admin::get_etat_moyenne_By($id_groupe,$id_mat,$id_partannee,$id_session);
            //var_dump($get_etat_moyenne_By);
            if ( !empty($get_etat_moyenne_By) && isset(($get_etat_moyenne_By[0])['etat_moy']) && ($get_etat_moyenne_By[0])['etat_moy']!=0 ) {
                $tabdatas['getetat_envoimoy']=1;
                $tabdatas['saisi_pers']=($get_etat_moyenne_By[0])['nom_prenom'];
                $tabdatas['saisi_pers_tel']=($get_etat_moyenne_By[0])['contact'];
            }
            else{$tabdatas['getetat_envoimoy']=0;}



        }



        //var_dump($tabdatas['allSession']);
        // ici
        $tabdatas['menu'] = "Examen et Evaluation";
        $tabdatas['sousmenu'] = "Notes";


        //var_dump($tabdatas['allannee']);
        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_Admin ::: admin_notesAction => " . $fct_exec;
		$log_user =" Examen et Ealuation , Gestion des notes ";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers_personne,$id_univ);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/

        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

    public function admin_finanneeAction()
    {
        $fct_exec = "||";
        $tabdatas = Crt_Admin::infosuser();
        $tabdata_user = (object)$tabdatas;
        $id_pers_personne = intval($tabdata_user->id_pers_personne);
        $tabdatas['panel'] = 1;
        $tabdatas['univInfos'] = Admin::get_UnivInfosBy($tabdata_user->fk_iduniv);
        $id_univ = intval($tabdata_user->fk_iduniv);
        $tabdatas['univ_id'] = $id_univ;
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
        //var_dump($_POST);
        
       
        $tabdatas['allannee'] = Admin::get_anneeBy($id_univ);
        $tabdatas['allSession'] = Prof::getSession();

        if (isset($_POST["moy_annee_filtre"])) {

            $tabdatas['moy_annee_filtre'] = intval( htmlspecialchars($_POST["moy_annee_filtre"]) );

            $tabdatas['get_all_critereadmin'] = Admin::get_all_critereadmin($tabdatas['moy_annee_filtre']);
            //var_dump($tabdatas['get_all_critereadmin']);

            foreach ($tabdatas['allannee'] as $key => $value) {
                if ($value['id_anscol_annee_scolaire']==$tabdatas['moy_annee_filtre']) {
                    $tabdatas['moy_annee_lib'] = $value['annee_libelle'];
                }
            }

            $tabdatas['get_anneScol_Partie_By'] = Admin::get_anneScol_Partie_By($tabdatas['moy_annee_filtre']);

            $tabdatas['getClassesBy'] = Admin::getGroupeBy($tabdatas['moy_annee_filtre']);
            //var_dump($tabdatas['getClassesBy']);//exit();


            if ( isset($_POST["critere_depart"]) && isset($_POST["btn_critere_add"])) {
                $critere_verif_periodemoyenne = htmlspecialchars($_POST["critere_verif_periodemoyenne"]) ;
                $critere_verif_periodecredit = htmlspecialchars($_POST["critere_verif_periodecredit"]);
                $critere_admis_min = intval( htmlspecialchars($_POST["critere_admis_min"]) );
                $critere_auto_min = intval( htmlspecialchars($_POST["critere_auto_min"]) );
                $critere_depart = intval( htmlspecialchars($_POST["critere_depart"]) );
                $table="critere_admission";
                $tb_infos=[];
                $tb_conditions=[];
                $tb_conditions["fk_idanneescol"]=$tabdatas['moy_annee_filtre'];
                $tb_infos["fk_idanneescol"]=$tabdatas['moy_annee_filtre'];
                $tb_conditions["fk_iddepart"]=$critere_depart;
                $tb_infos["fk_iddepart"]=$critere_depart;
                $tb_infos["periode_moy"]=$critere_verif_periodemoyenne;
                $tb_infos["periode_credit"]=$critere_verif_periodecredit;
                $tb_infos["credit_admin_min"]=$critere_admis_min;
                $tb_infos["credit_auto_min"]=$critere_auto_min;
                $tabdatas['getClassesBy'] = Admin::set_insertSQL($table,$tb_infos, $tb_conditions);
            }



            if (isset($_POST["moy_classe"])) {
                $tabdatas['moy_classe'] = intval( htmlspecialchars($_POST["moy_classe"]) );

                //$tabdatas['getAllElevByGrp'] = Admin::get_selectSQL_ALL_by($table, $tb_conditions);

                $tabdatas['getAllElevByGrp'] = Admin::getAllElevByGrp($tabdatas['moy_classe']);

                $tabdatas['get_grpinfo_tfm'] = Admin::get_grpinfo_tfm($tabdatas['moy_classe']);
                //var_dump($tabdatas['get_grpinfo_tfm']);
               
                foreach ($tabdatas['getAllElevByGrp'] as $key => $value) {
                    $table="moyenne_periode";
                    $tb_conditions=[];
                    $tb_conditions["fk_id_eleve"]=intval($value['id_eleve_eleve']);
                    $tb_conditions["fk_id_groupe"]=$tabdatas['moy_classe'];
                    $tb_conditions["etat"]=1;
                    ($tabdatas['getAllElevByGrp'][$key])['moyenne'] = Admin::get_selectSQL_ALL_by($table, $tb_conditions);

                    foreach ($tabdatas['get_anneScol_Partie_By'] as $cle => $part) {
                        $get_bilan_moyenneinfosBy = Admin::get_MPgroupeBy_moyenneinfosBy(intval($value['id_eleve_eleve']),$tabdatas['moy_classe'],intval($part['id_annee_partie']) );
                        //var_dump($get_bilan_moyenneinfosBy );exit;

                        $tmp_credit = 0;
                        $tmp_coef = 0;
                        $tmp_moy = 0;
                        $tmp_session = 1;

                        foreach ( $get_bilan_moyenneinfosBy as $ky => $cred) {
                            
                            if ( intval($cred['tcredit']) != 0 ) {
                                $tmp_moy=floatval($cred['tmoycoef']) / intval($cred['tcredit']);
                                if ( $tmp_moy>= 10 ) {
                                    $tmp_credit = $tmp_credit + intval($cred['tcredit']);
                                    $tmp_coef = $tmp_coef + intval($cred['coeficient_tmp']);
                                    $tmp_session = $tmp_session * intval($cred['id_session']) ;
                                }                            
                            }                            
                        }
                        //var_dump('part annee :'.$part['id_annee_partie'],$get_bilan_moyenneinfosBy);
                        $tmp_array=[];
                        $tmp_array['id_partanne']=intval($part['id_annee_partie']);
                        $tmp_array['tmp_credit']=$tmp_credit;
                        $tmp_array['tmp_session']=$tmp_session;

                        ($tabdatas['getAllElevByGrp'][$key])['infos'][intval($part['id_annee_partie'])] = $tmp_array;

                        //var_dump($get_bilan_moyenneinfosBy);
                    }
                    //var_dump($tabdatas['getAllElevByGrp'][0]);exit();

                }
                //var_dump($tabdatas['getAllElevByGrp'][1]);//exit();
                
                $table="groupe";
                $tb_conditions=[];
                $tb_conditions["groupe_id"]=$tabdatas['moy_classe'];
                $tabdatas['infos_groupe'] = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
                //var_dump($tabdatas['infos_groupe']);



                if (isset($_POST["moy_annee_filtre"]) &&  isset($_POST["bilan_result"]) && isset($_POST["moy_classe"]) && isset($_POST["next_annee"])) {

                    $table="fin_anneescol"; 
                    $tb_infos=[];
                    $tb_conditions=[]; 
                    $tb_conditions["id_groupe"]=$tabdatas['moy_classe']; 
                    $tb_conditions["id_annee_scola"]= $tabdatas['moy_annee_filtre'];  
                    $getbd = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
                    //var_dump( $getbd); 
                    //Verification si annee terminer
                    if ($getbd == 0) {
                        //exit();
                        //get id annee suivant
                        $id_niveau=intval(($tabdatas['infos_groupe'][0])['fk_idniveau']);
                        $get_niveau_next = Admin::get_niveau_next($id_niveau,$id_univ);
                        if ($get_niveau_next == 0) {$id_next_nivo=0; }
                        else{$id_next_nivo=intval($get_niveau_next[0]["id_niveau"]); }
                        //var_dump($get_niveau_next, $_POST); 
                        //get next annee lib
                        $table="annee_scolaire";
                        $tb_conditions=[];
                        $tb_conditions["id_anscol_annee_scolaire"]=intval(htmlspecialchars($_POST["next_annee"]));
                        $tb_conditions["fk_univ"]=$id_univ;
                        $tmp_anne_next= Admin::get_selectSQL_ALL_by($table, $tb_conditions);
                        $tmp_anne_nextlib=explode(" ", $tmp_anne_next[0]["annee_libelle"]);
                        //var_dump($tmp_anne_nextlib);
                        //exit();
                        //Action pour chaque etudiants
                        foreach ($_POST["bilan_result"] as $key => $value) {
                            $tmp_bilan_val = explode("/_/",$value);
                            $tmp_idelev = $tmp_bilan_val[0];
                            $tmp_etat = $tmp_bilan_val[1];
                            //var_dump($tmp_etat);
                            //Retrait des groupe des etudiants
                            $table="eleve_estds_groupe"; 
                            $tb_infos=[];
                            $tb_conditions=[]; 
                            $tb_infos["elv_ds_grpe_etat"]=0;  
                            $tb_conditions["elv_ds_grpe_idelev"]=$tmp_idelev;  
                            $tb_conditions["elv_ds_grpe_groupe"]=$tabdatas['moy_classe'];  
                            $updateetat = Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
                            //Table personne
                            $table="personne"; 
                            $tb_infos=[];
                            $tb_conditions=[]; 
                            $tb_infos["etat_pers"]=4;  
                            $tb_conditions["type_pers"]=1;  
                            $tb_conditions["id_type"]=$tmp_idelev;    
                            $updateetat = Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
                            //Préinscription etudiants
                            $table="preinscription"; 
                            $tb_infos=[];
                            $tb_conditions=[]; 
                            $tb_conditions["id_eleve"]=$tmp_idelev; 
                            $tb_conditions["annee_scola"]=$tmp_anne_nextlib[0];  
                            $tb_infos["annee_scola"]=$tmp_anne_nextlib[0];  
                            if ($tmp_etat=="Redouble") {$tb_infos["niveau"]=$id_niveau;}
                            else{$tb_infos["niveau"]=$id_next_nivo;}
                            $tb_infos["classe"]=$tabdatas['moy_classe'];  
                            $tb_infos["id_eleve"]=$tmp_idelev;  
                            $tb_infos["etat_preinscription"]=$tmp_etat;  
                            $updateetat = Admin::set_insertSQL($table,$tb_infos, $tb_conditions);
                        }
                        //Ajout dans table fin annee
                        $table="fin_anneescol"; 
                        $tb_infos=[];
                        $tb_conditions=[]; 
                        $tb_conditions["id_groupe"]=$tabdatas['moy_classe']; 
                        $tb_conditions["id_annee_scola"]= $tabdatas['moy_annee_filtre'];  
                        $tb_infos["id_groupe"]=$tabdatas['moy_classe'];  
                        $tb_infos["id_annee_scola"]= $tabdatas['moy_annee_filtre'];  
                        $tb_infos["id_personne"]= $id_pers_personne;  
                        $updateetat = Admin::set_insertSQL($table,$tb_infos, $tb_conditions);

                        $tabdatas['toast_notif']['etat'] = "success";
                        $tabdatas['toast_notif']['infos'] = "Fin d'année pour ".($tabdatas['infos_groupe'][0])['groupe_libelle'];
                    }

                }

                $tabdatas['getbd_fin_anneescol'] = Admin::get_gpre_finanne($tabdatas['moy_classe'],$tabdatas['moy_annee_filtre']);
                //var_dump($tabdatas['getbd_fin_anneescol']);
                //
                
                $table="classe";
                $tb_conditions=[];
                $tb_conditions["id_classe_classe"]=intval(($tabdatas['infos_groupe'][0])['groupe_classe']);
                $tabdatas['infos_classe'] = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
                //var_dump($tabdatas['infos_classe']);exit;
                $tabdatas['grpe_critere'] = Admin::get_all_critereadminBY($tabdatas['moy_annee_filtre'],intval(($tabdatas['infos_classe'][0])['id_departement']));
                //var_dump($tabdatas['grpe_critere']);

            }

            $tabdatas['get_all_departBy'] = Admin::get_all_departBy($tabdatas['moy_annee_filtre']);
            //var_dump($tabdatas['get_all_departBy']);




        }



        //var_dump($tabdatas['moy_annee_filtre']);
        // ici
        $tabdatas['menu'] = "Délibération";
        $tabdatas['sousmenu'] = "";

        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Crt_Admin ::: admin_finanneeAction => " . $fct_exec;
		$log_user =" Examen et Ealuation , Délibération ";
		modeldb::set_Ajax_Log($info,$log_user,$id_pers_personne,$id_univ);
		//:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/


        //var_dump($tabdatas['allannee']);

        View::renderTemplate('Accueil/admin/' . $_SESSION['page'] . '.html', $tabdatas);
    }

}
