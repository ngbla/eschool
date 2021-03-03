<?php
namespace App\Controllers;

require_once('../App/Models/User.php');
require_once('../App/Models/Log.php');
require_once('../App/Models/Admin.php');

use \Core\View;
use App\Models\Log as modeldb;
use App\Models\User as modelUser;
use App\Models\Admin;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Crt_accueil extends \Core\Controller
{
    /**
     * Show the index page
     *
     * @return void
     */
    public function accueilAction(){

        //var_dump($_SESSION);
        //var_dump($_SESSION["page"]);
        //exit();
        $tabdata_user = (object) $_SESSION["user"];

        $tabdatas = [
            'id_pers_personne'  =>  $tabdata_user->id_pers_personne,
            'nom_prenom' =>  $tabdata_user->nom_prenom,
            'sexe' =>  $tabdata_user->sexe,
            'email' =>  $tabdata_user->email,
            'contact' =>  $tabdata_user->contact,
            'type_pers' =>  $tabdata_user->type_pers,
            'id_type' =>  $tabdata_user->id_type,
            'id_type' =>  $tabdata_user->id_type
            ];
        //var_dump($tabdata );
        //exit();
        
        switch ($_SESSION["page"]) {
            case 'info':
                $_POST=NULL;
                $_GET=NULL;
                unset($_POST);
                unset($_GET);
                View::renderTemplate('Accueil/information.html',$tabdatas);
            break;
            case 'result':
                $_POST=NULL;
                $_GET=NULL;
                unset($_POST);
                unset($_GET);
                View::renderTemplate('Accueil/resultat.html',$tabdatas);
            break;

            case 'prof_moy':
                $_POST=NULL;
                $_GET=NULL;
                unset($_POST);
                unset($_GET);
                View::renderTemplate('Accueil/prof/prof_moy.html',$tabdatas);
            break; 
            case 'prof_suivi_etudi':
                $_POST=NULL;
                $_GET=NULL;
                unset($_POST);
                unset($_GET);
                View::renderTemplate('Accueil/prof/prof_etud_suivi.html',$tabdatas);
            break;              

            case 'parent_moy':
                $_POST=NULL;
                $_GET=NULL;
                unset($_POST);
                unset($_GET);
                View::renderTemplate('Accueil/parent/parent_moy.html',$tabdatas);
            break; 
            case 'parent_etud':
                $_POST=NULL;
                $_GET=NULL;
                unset($_POST);
                unset($_GET);
                View::renderTemplate('Accueil/parent/parent_etud.html',$tabdatas);
            break;                              
            case 'admin_user_active':
                $allCpteactive = modelUser::getAlluserBy(1);
                $tabdatas['allCpteElev'] = $allCpteactive;
                $allCpteactive = modelUser::getAlluserBy(2);
                $tabdatas['allCpteProf'] = $allCpteactive;
                $allCpteactive = modelUser::getAlluserBy(3);
                $tabdatas['allCpteParent'] = $allCpteactive;
                $allCpteactive = modelUser::getAlluserBy(4);
                $tabdatas['allCpteAdmin'] = $allCpteactive;
                $_POST=NULL;
                $_GET=NULL;
                unset($_POST);
                unset($_GET);
                View::renderTemplate('Accueil/admin/admin_active_user.html',$tabdatas);
            break; 
            case 'admin_creerannee':
                $tabdatas['allAnneeScolaire']=Admin::getAnneeScolaire();
                //var_dump($_SESSION,$_POST);exit();
                $_POST=NULL;
                $_GET=NULL;
                unset($_POST);
                unset($_GET);
                View::renderTemplate('Accueil/admin/admin_creerannee.html',$tabdatas);
            break; 
            default:
                $_POST=NULL;
                $_GET=NULL;
                unset($_POST);
                unset($_GET);
                View::renderTemplate('Accueil/accueil.html',$tabdatas);
            break;
        }
    }

    public function adminAction(){

        $tabdata_user = (object) $_SESSION["user"];
        //var_dump($_SESSION);exit();
        $tabdatas = [
            'id_pers_personne'  =>  $tabdata_user->id_pers_personne,
            'nom_prenom' =>  $tabdata_user->nom_prenom,
            'sexe' =>  $tabdata_user->sexe,
            'email' =>  $tabdata_user->email,
            'contact' =>  $tabdata_user->contact,
            'type_pers' =>  $tabdata_user->type_pers,
            'id_type' =>  $tabdata_user->id_type,
            'id_type' =>  $tabdata_user->id_type
        ];
        //var_dump($tabdata );
        //exit();

        //**CREATION ANNEE :: Utilisateur Connecter /Admin/ et cree_anne_scol_btn
        if( isset($_POST["cree_anne_scol_btn"]) && isset($_SESSION['user']) && ($_SESSION['user']['type_pers']=='4') ) {
            $creer_annee = Admin::setAnneeScolaire();
            $tabdatas['creation_annee']=$creer_annee;
        }
        else {
            $tabdatas['creation_annee']=-2;
        }
        $_POST=NULL;
        $_GET=NULL;
        unset($_POST);
        unset($_GET);
        View::renderTemplate('Accueil/admin/'.$_SESSION['page'].'.html',$tabdatas);

    }


}
