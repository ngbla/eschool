<?php

namespace App\Controllers;


require_once('../App/Models/User.php');
require_once('../App/Models/Eleve.php');
require_once('../App/Models/Log.php');
require_once('../App/Models/Model_public.php');
//require_once('../vendor/zendframework/Zendsession/src/Container.php');


use \Core\View;
use App\Models\Log as modeldb;
use App\Models\User;
use App\Models\Eleve;
use App\Models\Model_public;
use App\Config;



/**
 * Home controller
 *
 * PHP version 7.0
*/
class Crt_newinsciption extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function inscriptionAction(){

        //var_dump($_SERVER['DOCUMENT_ROOT'] );
        //var_dump($_SERVER );
        //var_dump($_GET,$_POST);
        //var_dump($_SESSION['eschool_ville']);
        if (Config::ENVI == 'local') { $protocole=Config::PROTOC_LOCAL; } 
        else { $protocole=Config::PROTOC_LIGNE;  }
        

        if (isset($_SESSION['eschool_ville'])) {
            $tabledata = [];
            //var_dump($_POST,$_GET,$_FILES);//exit;
    
            $page = 'Inscription.html';
            
            $info = "Visite page !";
            $log_user ="  Page Inscription nouveau élèves";
            modeldb::setLog($info,$log_user);

            $tabledata['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] ;
    
            //var_dump( $log_user );
            //var_dump($_SESSION);
            $id_univ = intval($_SESSION['eschool_ville']);
            $get_univInfo_By=(Model_public::get_univInfo_By($id_univ))[0];
            //var_dump($get_univInfo_By);
            if (empty($get_univInfo_By) || $get_univInfo_By==0 || $get_univInfo_By=="") {
                header('Location: '.$protocole.'://'.$_SERVER["HTTP_HOST"]);
                exit();
            }
            else {
                $tabledata['eschool_ville'] = $get_univInfo_By['ville_univ'];
                $tabledata['non_univ'] = $get_univInfo_By['non_univ'];
                $tabledata['email_univ'] = $get_univInfo_By['email_univ'];
                $tabledata['contact_univ'] = $get_univInfo_By['contact_univ'];
                $tabledata['initiale_univ'] = $get_univInfo_By['initiale_univ'];
            }


            
            $tabledata['get_liste_niveau'] =Model_public::get_liste_niveau($_SESSION['eschool_ville']);
            $tabledata['get_liste_filieres'] =Model_public::get_liste_filieres($_SESSION['eschool_ville']);
            


            //var_dump('get_liste_niveau',$tabledata['get_liste_niveau'] );
            //var_dump('get_liste_filieres',$tabledata['get_liste_filieres'] );


            if (isset( $_POST) && !empty($_POST)) {
                $tabledata['post'] = $_POST;
    
                if ( isset( $_POST['FormNewInscriptionE1']) && $_POST['FormNewInscriptionE1'] == "phase1" ) {
    
                    $email_eleve = htmlspecialchars($_POST['mail1']) ; 
                    $datenais =htmlspecialchars($_POST['datenaiss']) ; 
                    $infos_elev =  User::get_eleveinfos($email_eleve, $datenais);
                    //var_dump($infos_elev);
                    if ($infos_elev == 0) {
                        $page = 'Validation.html';
                    }
                    else {
                        $tabledata['inscript_reps'] = 'warning';
                        $tabledata['msg_infos'] = $infos_elev['nom_prenom'].' , Vous êtes déja inscrit !';
                    }
                    
                    
    
                }    
            }


            unset($_SESSION['user']);
            unset($_SESSION['page']);
            //unset($_GET);
            //var_dump($id_univ);
            $tabledata['id_univ'] =$id_univ;
    
            View::renderTemplate('Inscription/'.$page, $tabledata );
        }
        else{
            header('Location: '.$protocole.'://'.$_SERVER["HTTP_HOST"]);
            exit();
        }


    }
    public function incription_formAction(){

        $page ="Inscription" ; 
        if (Config::ENVI == 'local') { $protocole=Config::PROTOC_LOCAL; } 
        else { $protocole=Config::PROTOC_LIGNE;  }
        //$tabledata['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] ;
        //var_dump('_SESSION',$_SESSION);
        $tabledata = [];


        //var_dump('incription_formAction',$_FILES,$_POST);exit();

        $id_univ = intval($_SESSION['eschool_ville']);
        $get_univInfo_By=(Model_public::get_univInfo_By($id_univ))[0];
        if (empty($get_univInfo_By) || $get_univInfo_By==0 || $get_univInfo_By=="") {
            header('Location: '.$protocole.'://'.$_SERVER["HTTP_HOST"]);
            exit();
        }
        else {
            $tabledata['eschool_ville'] = $get_univInfo_By['ville_univ'];
            $tabledata['non_univ'] = $get_univInfo_By['non_univ'];
            $tabledata['email_univ'] = $get_univInfo_By['email_univ'];
            $tabledata['contact_univ'] = $get_univInfo_By['contact_univ'];
            $tabledata['initiale_univ'] = $get_univInfo_By['initiale_univ'];
        }

        if ( isset( $_POST) && !empty($_POST) ) {

            
           
            unset($_POST['action_inscription']);
            unset($_POST['FormNewInscriptionE1']);
            //var_dump($_POST,$_GET,$_FILES);exit;

            $nom = htmlspecialchars($_POST['nom']);
            $prenom = htmlspecialchars($_POST['prenom']);
            $mail1 = htmlspecialchars($_POST['mail1']);
            //$mail2 = htmlspecialchars($_POST['mail2']);
            $datenaiss = htmlspecialchars($_POST['datenaiss']);
            $lieunaiss = htmlspecialchars($_POST['lieunaiss']);
            $contact = htmlspecialchars($_POST['contact']);
            $sexe = htmlspecialchars($_POST['sexe']);


            $pass = $contact;
            $pass1 = sha1($pass);
            $pass2 = $pass1;
            $type = '1';
            $anneScol="2020-2021";

            $get_verif_eleveInscript = User::get_verif_eleveInscript($mail1, $id_univ, $anneScol);
            //var_dump( $get_verif_eleveInscript);exit;

            if (empty($get_verif_eleveInscript ) || $get_verif_eleveInscript ==0) {
                //AJOUT DES INFORMATIONS SUR L'ELEVE DS TABLE PERSONNE ET TABLE ELEVE 
                $set_eleve_Personnes = User::set_eleve_Personnes($nom,$prenom, $sexe,$mail1,$contact,$pass1,$pass2,$type, $datenaiss, $lieunaiss, $id_univ );
            }
            else { $set_eleve_Personnes=1;   }


            $info = "Soumission formulaire inscription eleve!";
            $log_user =" Création d'élève univ(".$id_univ."):".$nom." ".$prenom." ".$mail1." ".$datenaiss." (resultat=".$set_eleve_Personnes.")" ;
            modeldb::setLog($info,$log_user);
            

            if ($set_eleve_Personnes == 1) {

                $email_eleve = htmlspecialchars($_POST['mail1']) ; 
                $datenais =htmlspecialchars($_POST['datenaiss']) ; 
                $infos_elev =  User::get_eleveinfos($email_eleve, $datenais);
    
               
                if ($infos_elev == 0) { 
                    $tabledata['post'] = $_POST;
                    $tabledata['inscript_reps'] = 'danger';
                    $tabledata['msg_infos'] = 'Inscription introuvable , Si vous venez de vous inscrire :  Veuillez contactez l\'administrateur!  Sinon inscrivez vous!';
                }
                else {
                    $id_eleve_eleve = intval($infos_elev['id_eleve_eleve']);
                    //var_dump($id_eleve_eleve);
                    $eleve_lastInscript =  User::get_last_eleveInscript($id_eleve_eleve, $anneScol);
                    //var_dump($eleve_lastInscript) ;exit;

                    if ($eleve_lastInscript == 0) {  
                        $tabledata['post'] = $_POST;
                        $tabledata['inscript_reps'] = 'danger';
                        $tabledata['msg_infos'] = 'Recu introuvable , Si vous venez de vous inscrire :  Veuillez contactez l\'administrateur!  Sinon inscrivez vous!';
                    }
                    else {
                        //var_dump($eleve_lastInscript[0]);
                        //var_dump($infos_elev);


                        $tabledata['eleve_lastInscript'] = $eleve_lastInscript[0];
                        $tabledata['infos_elev'] = $infos_elev;
                        $tabledata['inscript_reps'] = 'success';
                        $page ="model_recu" ;
                    }
    
                }
                //var_dump($tabledata['infos_elev']) ;
            }
            else {
                $tabledata['post'] = $_POST;
                $tabledata['inscript_reps'] = 'danger';
                $tabledata['msg_infos'] = 'Erreur lors de l\inscription, Vous êtes déja inscrit !';
            }
            
            
        }
        else {
            $info = "Soumission formulaire inscription eleve!";
            $log_user =" Aucune informations";
            $tabledata['inscript_reps'] = 'danger';
            $tabledata['msg_infos'] = 'Erreur lors de l\inscription, Veuillez remplir le formulaire !';
            modeldb::setLog($info,$log_user);
        }

        $tabledata['id_univ'] =$id_univ;

        View::renderTemplate('Inscription/'.$page.'.html',$tabledata);

    }
    public function paiement_scolariteAction(){
        $tabledata = [];
        if (Config::ENVI == 'local') { $protocole=Config::PROTOC_LOCAL; } 
        else { $protocole=Config::PROTOC_LIGNE;  }
        //var_dump('paiement_scolariteAction',$_POST,$_GET,$_FILES);exit;

        $page = 'paiement_scolarite.html';

        $id_univ = intval($_SESSION['eschool_ville']);
        $get_univInfo_By=(Model_public::get_univInfo_By($id_univ))[0];
        if (empty($get_univInfo_By) || $get_univInfo_By==0 || $get_univInfo_By=="") {
            header('Location: '.$protocole.'://'.$_SERVER["HTTP_HOST"]);
            exit();
        }
        else {
            $tabledata['eschool_ville'] = $get_univInfo_By['ville_univ'];
            $tabledata['non_univ'] = $get_univInfo_By['non_univ'];
            $tabledata['email_univ'] = $get_univInfo_By['email_univ'];
            $tabledata['contact_univ'] = $get_univInfo_By['contact_univ'];
            $tabledata['initiale_univ'] = $get_univInfo_By['initiale_univ'];
        }
  
        
        $info = "Accès page";
        $log_user =" Page de paiement de scolarite";
        modeldb::setLog($info,$log_user);

        $tabledata['id_univ'] =$id_univ;

        View::renderTemplate('Inscription/'.$page, $tabledata );

    }

    public function impression_FicheAction(){
        $tabledata = [];
        $page = 'Inscription.html';
        $annee_inscript = '2020-2021';
        //var_dump('impression_FicheAction',$_POST,$_GET,$_FILES);exit;
        $id_univ = intval($_SESSION['eschool_ville']);
        $get_univInfo_By=(Model_public::get_univInfo_By($id_univ))[0];

        if (Config::ENVI == 'local') { $protocole=Config::PROTOC_LOCAL; } 
        else { $protocole=Config::PROTOC_LIGNE;  }
        
        if (empty($get_univInfo_By) || $get_univInfo_By==0 || $get_univInfo_By=="") {
            header('Location: '.$protocole.'://'.$_SERVER["HTTP_HOST"]);
            exit();
        }
        else {
            $tabledata['eschool_ville'] = $get_univInfo_By['ville_univ'];
            $tabledata['non_univ'] = $get_univInfo_By['non_univ'];
            $tabledata['email_univ'] = $get_univInfo_By['email_univ'];
            $tabledata['contact_univ'] = $get_univInfo_By['contact_univ'];
            $tabledata['initiale_univ'] = $get_univInfo_By['initiale_univ'];
        }

        if (isset($_POST['mail_eleve']) && isset($_POST['datenaiss'])) {
            # code...
       
            $email_eleve = htmlspecialchars($_POST['mail_eleve']) ; 
            $datenais =htmlspecialchars($_POST['datenaiss']) ; 
            $infos_elev =  User::get_eleveinfosBy($email_eleve, $datenais,$id_univ);

            
            

            if ($infos_elev == 0) { 
                $tabledata['post'] = $_POST;
                $tabledata['inscript_reps'] = 'danger';
                $tabledata['msg_infos'] = 'Inscription introuvable , Si vous venez de vous inscrire :  Veuillez contactez l\'administrateur!   Sinon inscrivez vous!';
            }
            else {
                $id_eleve_eleve = intval($infos_elev['id_eleve_eleve']);
                //var_dump($id_eleve_eleve);
                $eleve_lastInscript =  User::get_last_eleveInscript($id_eleve_eleve, $annee_inscript);

                if ($eleve_lastInscript == 0) {  
                    $tabledata['post'] = $_POST;
                    $tabledata['inscript_reps'] = 'danger';
                    $tabledata['msg_infos'] = 'Recu introuvable , Si vous venez de vous inscrire :  Veuillez contactez l\'administrateur!  Sinon inscrivez vous!';
                }
                else {
       
                    $tabledata['eleve_lastInscript'] = $eleve_lastInscript[0];
                    $tabledata['infos_elev'] = $infos_elev;
                    $tabledata['inscript_reps'] = 'success';
                    $page ="model_recu.html" ;
                }

            }

        }

        
        $info = "Accès page";
        $log_user =" Impression de recu";
        modeldb::setLog($info,$log_user);


        $tabledata['id_univ'] =$id_univ;

        View::renderTemplate('Inscription/'.$page, $tabledata );

    }
    
    

}
