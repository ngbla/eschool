<?php

//use App\Config;
session_start();
//var_dump($_GET);exit();
//var_dump($_SERVER);
//var_dump($_POST);
//var_dump($_GET);exit;

//echo '<div id="logo_load_page" style="text-align: center;"><img style="display: block;margin-left: auto;margin-right: auto;width: 20%;" src="loading.gif" alt=""></div>';
//var_dump(session_id());
//var_dump(session_id('1ca1e3aa208109b1480787c372afc127'));

require dirname(__DIR__) . '/vendor/autoload.php';
//var_dump('_GET',$_GET,'_POST',$_POST);
error_reporting(E_ALL);
ini_set('display_errors', 'On');
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');
//var_dump($_SERVER);exit;
//var_dump('_POST',$_POST);//
//var_dump('_FILES',$_FILES);
//var_dump('_GET',$_GET);
//exit;
//Routing
$router = new Core\Router();
//REDIRECTION VERS HTTPS A ACTIVER
if ($_SERVER['REQUEST_SCHEME'] == 'http') {
    //header('Location: https://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
    //exit();
}
//var_dump($_SERVER['REQUEST_SCHEME']);
//var_dump('_GET',$_GET,'_POST',$_POST,'_SESSION',$_SESSION);exit;
$str =  sha1("inscriptionélèves");

//TEST 1 : INSCRIPTION ETUDIANTS 
if ( isset($_GET['inscription']) && $_GET['inscription'] == $str && isset($_GET['cible']) && $_GET['cible'] !="") {

    $controlleur = 'Crt_newinsciption';

    $_SESSION['eschool_ville'] = intval(htmlspecialchars($_GET['cible']));
    //var_dump($_SESSION);
    //var_dump("TEST 1");
   

    if (isset($_POST['action_inscription']) && $_POST['action_inscription'] == $str && isset($_POST['FormNewInscriptionE1']) && $_POST['FormNewInscriptionE1'] == 'phase3') {
        //var_dump('phase3',$_FILES,$_POST,$_GET);exit();
        $router->add('', ['controller' => $controlleur, 'action' => 'incription_form']);
    }
    elseif ( isset($_POST['paiement_scolarite']) && $_POST['paiement_scolarite'] !="" ) {
        $router->add('', ['controller' => 'Crt_paiement', 'action' => 'paiement_scolarite']);
    }
    elseif ( isset($_POST['btn_imprime_fiche']) && isset($_POST['datenaiss']) && isset($_POST['mail_eleve']) ) {
        $router->add('', ['controller' => 'Crt_newinsciption', 'action' => 'impression_Fiche']);
    }
    else {
        
        $router->add('', ['controller' => $controlleur, 'action' => 'inscription']);
    }
    
    

}
//TEST 2 : PAGE DE CONNEXION
elseif (empty($_SESSION) && empty($_POST) && !isset($_GET['inscription'])) {
    //var_dump($_GET);
    if (isset($_GET["type_user"]) && $_GET["type_user"]!="") {
        $router->add('', ['controller' => 'Home', 'action' => 'index']);
    }
    else {
        header('Location: http://'.$_SERVER["HTTP_HOST"]);
        //header('Location: https://'.$_SERVER["HTTP_HOST"]);
        exit();
    }

    //var_dump("TEST 2");
} 
//TEST 3 : PERSONNE CONNECTEZ OU EN COURS
else {
    //DEMANDE DE CONNEXION UTILISATEUR
    if (isset($_POST["email_login"]) && isset($_POST["pass_login"]) && isset($_POST["s0"])) {
        if (isset($_SESSION['user'])) {$_SESSION['user'] = NULL;unset($_SESSION['user']);}
        unset($_GET['p']);
        $router->add('', ['controller' => 'Home', 'action' => 'login']);
        $router->add('{Home}/{login}');
    }

    //**TEST SI INSCRIPTION USER : page p et 
    elseif (isset($_GET['p'])  && !isset($_POST["email_login"]) && !isset($_SESSION['user']) && !isset($_POST["cree_anne_scol_btn"])) {

        if ($_GET['p'] == 'inscript' && !isset($_POST["email"])) {
            $_POST = NULL;
            $router->add('', ['controller' => 'Home', 'action' => 'index']);
            $router->add('{Home}/{index}');
        } 
        elseif ($_GET['p'] == 'inscript' && isset($_POST["email"])) {
            $router->add('', ['controller' => 'Home', 'action' => 'inscription']);
            $router->add('{Home}/{index}');
        } 
        else {}
    }

    //*UTILISATEUR CONNECTEZ :: Acces à une page
    elseif ( isset($_SESSION['user']) && (isset($_GET['p']) || isset($_GET['r'])) ) {

        // TEST DEMADE DE DECONNEXION
        if (isset($_GET['p'])  && $_GET['p']== 'logout') {

            //exit();
            setcookie("login", "", time() - 3600);
            setcookie("pass", "", time() - 3600);
            setcookie("type", "", time() - 3600);
            setcookie("id_univ", "", time() - 3600);
            //session_reset();
            //echo gethostname();
            setcookie (session_id(), "", time() - 3600);
            session_destroy();
            session_write_close();

            $_POST = NULL;
            $_GET = NULL;
            $_SESSION['p'] = NULL;
            unset($_GET['page']);
            unset($_POST);
            unset($_SESSION['user']);

            //var_dump($_SESSION,$_COOKIE);
            
            if (isset($_SESSION['eschool_refurl'])) {
                header('Location: '.$_SESSION['eschool_refurl']);
            }
            else {
                header('Location: https://'.$_SERVER["HTTP_HOST"]);
            }
            exit;
            $controlleur = 'Home';
            $action = 'index';
        } 
        // ACCESS A PAGE
        else {
            if (isset($_SESSION['page'])) { unset($_SESSION['page']);}

            //VERIFICATION DE LUTILISATEUR SI CONNECTEZ
            if (isset($_SESSION['user']['type_pers'])) {
                //REDIRECTION SUIVANT TYPE PERSONNE
                //ANCIENNE REDIRECTION MANUELLE
                //var_dump($_GET);exit();
                if (isset($_GET['p'])) {
                    
                    $variable = htmlspecialchars($_GET['p']);
                    $_SESSION['page'] = $variable;

                    switch ($_SESSION['user']['type_pers']) {
                        case '1':

                            //Acces au elerning

                            $pagetest = explode('_', $_GET['p']);

                            //var_dump($_GET['p'],$pagetest[0]);

                            if ($pagetest[0] == "elearning") {

                                /**
        
                                * Elearning
        
                                */
                                $controlleur = 'Elearning\Crt_E_eleve';
                                $action = htmlspecialchars($_GET['p']);
                            }
    
                            //Acces au autres pages

                            else {

                                $controlleur = 'Crt_eleve';

                                switch ($_GET['p']) {

                                    case 'eleve_accueil':

                                        $action = 'accueil';

                                        break;

                                    case 'note_eleve':
                                        $action = 'note';
                                    break;

                                    case 'pv_eleve':

                                        $action = 'pv';

                                        break;

                                    case 'absences_eleve':

                                        $action = 'absences';

                                        break;

                                    case 'convocation_eleve':

                                        $action = 'convocation';

                                        break;

                                    case 'information_eleve':

                                        $action = 'information';

                                        break;

                                    case 'agenda_eleve':

                                        $action = 'agenda';

                                        break;

                                    case 'fichier_perso':

                                        $action = 'fichier';

                                        break;

                                    case 'moy_eleve':

                                        $action = 'moy';

                                        break;

                                    case 'pro_cours_eleve':
                                        $action = 'procours';
                                    break;
                                    case 'stages':
                                        $action = 'stages';
                                    break;
                                    case 'evaluation_eleve':
                                        $action = 'evaluation';
                                    break;

                                    default:
                                        $action = 'accueil';
                                    break;
                                }
                            }



                            break;

                        case '2':

                            //Acces au elerning

                            $pagetest = explode('_', $variable);

                            //var_dump($_SESSION['page'],$_GET['p'],$pagetest[0]);
                            //var_dump( $pagetest );//exit;

                            if ($pagetest[0] == "elearning") {

                                /**
                                 * Elearning
                                 */
                                $controlleur = 'Elearning\Crt_E_prof';
                                $action = $variable;
                            } else {

                                # code...

                                $controlleur = 'Crt_prof';

                                //if ($_GET['p'] == 'accueil') { $_GET['p'] ='prof_accueil';}

                                //var_dump($_GET);

                                //exit;

                                switch ($_GET['p']) {

                                    case 'prof_accueil':

                                        $action = 'accueil';

                                        break;



                                    case 'prof_moy':

                                        $action = 'moy';

                                        break;



                                    case 'prof_etud_suivi':

                                        $action = 'etudsuivi';

                                        break;



                                    case 'prof_evaluation':



                                        $action = 'evaluation';

                                        if (isset($_POST['btn_voir_eval'])) {



                                            $type = explode("_", $_POST['btn_voir_eval']);

                                            //var_dump( $type);



                                            if ($type[0] == "voir") {

                                                $action = 'evaluationInfo';
                                            } elseif ($type[0] == "sup") {

                                                $action = 'evaluation';
                                            } else {

                                                $action = 'evaluation';
                                            }
                                        } else {

                                            $action = 'evaluation';
                                        }



                                        break;



                                    case 'prof_saisi_note':



                                        if (isset($_POST['btn_voir_eval'])) {



                                            $type = explode("_", $_POST['btn_voir_eval']);

                                            //var_dump( $type);



                                            if ($type[0] == "voir") {

                                                $action = 'evaluationInfo';
                                            } elseif ($type[0] == "sup") {

                                                $action = 'saisi_note';
                                            } else {

                                                $action = 'saisi_note';
                                            }
                                        } else {

                                            $action = 'saisi_note';
                                        }



                                        break;



                                    case 'prof_group_info':
                                        $action = 'group_info';
                                    break;
                                    case 'prof_etudStage':
                                        $action = 'prof_etudStage';
                                    break;
                                    case 'prof_eval_eleveupload':
                                        $action = 'prof_eval_eleveupload';
                                    break;
                                        

                                    default:

                                        $action = 'accueil';

                                        break;
                                }
                            }

                            break;



                        case '3':

                            $controlleur = 'Crt_parent';

                            //var_dump($_GET);

                            switch ($_GET['p']) {



                                case 'accueil':

                                    $action = 'accueil';

                                    $_SESSION['page'] = "parent_accueil";

                                    break;

                                case 'parent_accueil':

                                    $action = 'accueil';

                                    break;

                                case 'parent_moy':

                                    $action = 'moy';

                                    break;

                                case 'parent_etud':

                                    $action = 'etud';

                                    break;

                                case 'absences_eleve':

                                    $action = 'absences';

                                    break;

                                case 'convocation_eleve':

                                    $action = 'convocation';

                                    break;

                                case 'information_eleve':

                                    $action = 'information';

                                    break;

                                case 'agenda_eleve':

                                    $action = 'agenda';

                                    break;

                                case 'fichier_perso':

                                    $action = 'fichier';

                                    break;

                                default:

                                    break;
                            }

                            break;



                        case '4':

                            $controlleur = 'Crt_Admin';

                            //var_dump($_GET['p']);exit;

                            switch ($_GET['p']) {

                                case 'admin_creerannee':

                                    $action = 'creerannee';

                                    break;

                                case 'admin_ceerClasse':

                                    $action = 'ceerclasse';

                                    break;

                                case 'admin_classe_emploiTps':

                                    $action = 'classe_emploiTps';

                                    break;

                                case 'admin_classe_attrib_filliere':

                                    $action = 'classe_attrib_filliere';

                                    break;



                                case 'admin_elev_listAll':
                                    $action = 'elev_listAll';
                                break;

                                case 'admin_parent_listAll':

                                    $action = 'parent_listAll';

                                    break;



                                case 'admin_ceerMatiere':

                                    $action = 'ceermatiere';

                                    break;

                                case 'admin_attribution':

                                    $action = 'attribution';



                                    break;

                                case 'admin_eleve_classe':

                                    $action = 'admin_eleve_classe';

                                    break;

                                case 'admin_eleve_parents':

                                    $action = 'eleve_parents';

                                    break;

                                case 'admin_inscrip_etud':

                                    $action = 'admin_inscrip_etud';

                                    break;

                                case 'admin_prof_listAll':

                                    //var_dump($_POST);//exit;

                                    if ( (isset($_POST['btn_infos']) && $_POST['btn_infos'] == 'infos') || (isset($_POST['ajout_prof_mat']) && $_POST['ajout_prof_mat'] == 'ajoutMat') || (isset($_POST['btn_sup_prof_mat']) && $_POST['btn_sup_prof_mat'] == 'sup_mat')  || (isset($_POST['ajout_profgroupe']) && $_POST['ajout_profgroupe'] == 'ajoutGroupe')  || (isset($_POST['btn_sup_prof_groupe']) && $_POST['btn_sup_prof_groupe'] == 'sup_groupe') || (isset($_POST['ajout_profgroupe_all']))  ) {

                                        unset($_POST['btn_infos']);
                                        $action = 'prof_infos';
                                        
                                    } else {
                                        $action = 'prof_listAll';
                                    }



                                    break;



                                case 'admin_ceerSalle':

                                    $action = 'ceerSalle';

                                    break;

                                case 'admin_ceerSalle_Liste':

                                    $action = 'ceerSalle_Liste';

                                    break;



                                case 'admin_salle_prog':

                                    $action = 'salle_prog';

                                    break;



                                case 'admin_user_active':

                                    $action = 'user_active';

                                    break;



                                case 'admin_eval_pv':

                                    $action = 'eval_pv';

                                    break;

                                case 'admin_eval_moyenne':

                                    $action = 'eval_moyenne';

                                    break;



                                case 'admin_eval_auto':

                                    $action = 'eval_auto';

                                    break;



                                case 'admin_notif':

                                    $action = 'notif';

                                    break;



                                case 'admin_roles':

                                    $action = 'roles';

                                    break;



                                case 'admin_chat':
                                    $action = 'chat';
                                break;
                                case 'stage_etudiants':
                                    $action = 'stage_etudiants';
                                break;

                                case 'admin_ceerNiveau':
                                    $action = 'admin_ceerNiveau';
                                break;
                                case 'anciens_etudiants':
                                    $action = 'anciens_etudiants';
                                break;
                                case 'admin_repart_mat':
                                    $action = 'admin_repart_mat';
                                break;
                                case 'admin_vue_bulletin':
                                    $action = 'admin_vue_bulletin';
                                break;
                                case 'admin_relever':
                                    $action = 'admin_relever';
                                break;
                            case 'admin_classe_infos':
                                    $action = 'admin_classe_infos';
                                break;
                                case 'logs':
                                    $action = 'logs';
                                break;
                                case 'logs_error':
                                    $action = 'logs_error';
                                break;
                                case 'admin_elev_cieScol':
                                    $action = 'admin_elev_cieScol';
                                break;
                                case 'admin_comptabilite':
                                    $action = 'admin_comptabilite';
                                break;                            
                                default:
                                    $action = 'accueil';
                                break;
                            }

                            break;



                        default:
                            unset($_SESSION['user']);
                            unset($_SESSION['page']);
                            unset($_GET['p']);
                            unset($_GET);
                            unset($_POST);
                            $controlleur = 'Home';
                            $action = 'index';
                            break;
                    }
                }
                //NOUVELLE REDIRECTION AURO
                elseif (isset($_GET['r'])) {
                    $_SESSION['page'] = htmlspecialchars($_GET['r']);
                    switch ($_SESSION['user']['type_pers']) {
                        case '1':
                            $controlleur = 'Crt_eleve';
                            $action =  $_SESSION['page'];
                        break;

                        case '2':
                            $controlleur = 'Crt_prof';
                            $action =  $_SESSION['page'];
                        break;

                        case '3':
                            $controlleur = 'Crt_parent';
                            $action =  $_SESSION['page'];
                        break;

                        case '4':
                            $controlleur = 'Crt_Admin';
                            $action =  $_SESSION['page'];
                        break;

                        default:
                            unset($_SESSION['user']);
                            unset($_SESSION['page']);
                            unset($_GET['p']);
                            unset($_GET);
                            unset($_POST);
                            $controlleur = 'Home';
                            $action = 'index';
                        break;
                    }
                }
                else {
                    $_POST = NULL;
                    $_GET = NULL;
                    $_SESSION['user'] = NULL;
                    unset($_GET['p']);
                    unset($_POST);
                    unset($_SESSION['user']);
                    $controlleur = 'Home';
                    $action = 'index';
                }

            } 
            else {
                $_POST = NULL;
                $_GET = NULL;
                $_SESSION['user'] = NULL;
                unset($_GET['p']);
                unset($_POST);
                unset($_SESSION['user']);
                $controlleur = 'Home';
                $action = 'index';
            }

        }

        $router->add('', ['controller' => $controlleur, 'action' => $action]);
        $router->add('{controller}/{action}');
    } 
    else {

        if (isset($_POST["email_reset"])) {
            
            //var_dump($_POST);exit();
            $controller='Home';
            if (isset($_POST["code_reset"]) && isset($_POST["pass1"]) && isset($_POST["pass2"])) {
                $action='maj_pass';
            }
            else {
                $action='reset_pass';
            } 
        }
        else {
            $controller='Home';
            $action='index';
        }

        $router->add('', ['controller' => $controller, 'action' => $action]);
    }

    //var_dump("TEST 3");
}

//var_dump( $router->getRoutes() );exit;



if (empty($router->getRoutes())) {
    $router->add('', ['controller' => 'Home', 'action' => 'index']);
}

/*exit;


echo '<pre>';
var_dump( $router->getRoutes() );
echo '</pre>';
*/
$router->dispatch($_SERVER['QUERY_STRING']);
////::