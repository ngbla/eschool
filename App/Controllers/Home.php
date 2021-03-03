<?php

namespace App\Controllers;


require_once('../App/Models/User.php');
require_once('../App/Models/Log.php');
//require_once('../vendor/zendframework/Zendsession/src/Container.php');


use \Core\View;
use App\Models\Log as modeldb;
use App\Models\User;
use App\Models\Model_public;
use Zend\Stdlib\Message;

//use Zend\Session;


/**
 * Home controller
 *
 * PHP version 7.0
*/
class Home extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction(){
        $tabdatas=[];
        $tabdatas['get_infos'] = $_GET;

        //var_dump("_POST",$_POST);var_dump("get",$_GET);
        //exit();
        //var_dump("get_infos", $tabdatas['get_infos']);
        
        $str =  sha1("inscriptionélèves");

        if (isset($_GET['verif']) && isset($_GET['cible']) && $_GET['verif']==$str ) {

            $id_univ = intval(htmlspecialchars($_GET['cible']));
            $tabdatas['univInfos']=Model_public::get_univInfo_By($id_univ);

            if (empty($tabdatas['univInfos']) || $tabdatas['univInfos']==0) {
                //header('Location: http://'.$_SERVER["HTTP_HOST"]);
                header('Location: https://'.$_SERVER["HTTP_HOST"]);
                exit();
            }
            else {

                if ( isset($_GET['p']) ) {
                    $info = "Visite page !";
                    $log_user ="  Page =". $_GET['p'] ;
                    modeldb::setLog($info,$log_user);
        
                    if (isset($_GET["type_user"]) && $_GET["type_user"]!="") {
        
                        $str =  sha1("inscriptionélèves");
                
                        if ($_GET['p'] == 'inscript' && $_GET['verif'] == $str ) {
                            //var_dump("get",$_GET);
                            unset($_GET['p']);
                            View::renderTemplate('inscript.html',$tabdatas);
                        }
                        else {
                            //$_GET=NULL;
                            //unset($_GET);
                            //unset($_GET['p']);
                            View::renderTemplate('Home/index.html',$tabdatas);
                        }
        
                    }
                    else {
                        header('Location: https://'.$_SERVER["HTTP_HOST"]);
                        exit();
                    }
        
                }
                else {
                    //var_dump($_GET);
                    $info = "Visite page !";
                    $log_user ="  Page = Connexion" ;
                    modeldb::setLog($info,$log_user);
                    if (isset($_GET["type_user"]) && $_GET["type_user"]!="") {

                        if (isset($_COOKIE['login']) && isset($_COOKIE['pass']) && isset($_COOKIE['type']) && isset($_COOKIE['id_univ'])) {
                           
                            Home::loginAction();
                        }
                        else {
                            View::renderTemplate('Home/index.html',$tabdatas);
                        }
                       
                    }
                    else {
                        header('Location: https://'.$_SERVER["HTTP_HOST"]);
                        exit();
                    }
                    
                }
            }

        }
        else {
            header('Location: https://'.$_SERVER["HTTP_HOST"]);
            exit();
        }

    }
    public function reset_passAction(){
        $tabdatas=[];
        $tabdatas['get_infos'] = $_GET;

        //var_dump($tabdatas['get_infos']);

        //var_dump($_POST,$_GET);
        $str =  sha1("inscriptionélèves");
        if (isset($_GET['verif']) && isset($_POST['email_reset']) && isset($_GET['cible']) && $_GET['verif']==$str ) {

            $id_univ = intval(htmlspecialchars($_GET['cible']));
            $tabdatas['email_reset'] = htmlspecialchars($_POST['email_reset']);
            $tabdatas['univInfos']=Model_public::get_univInfo_By($id_univ);
            $fct_exec="Réinitialisation de Mots de Passe | ";
            $id_pers=0;
            $fk_iduniv=$id_univ;
            //var_dump("univInfos",$tabdatas['univInfos']);

            if (empty($tabdatas['univInfos']) || $tabdatas['univInfos']==0) {
                //header('Location: http://'.$_SERVER["HTTP_HOST"]);
                //exit();
                header('Location: https://'.$_SERVER["HTTP_HOST"]);
                exit();
            }
            else {
                $table="personne";
                $tb_conditions=[];
                $tb_conditions["email"]=$tabdatas['email_reset'];
                $tabdatas["user_reset"]=Model_public::get_selectSQL_ALL_by($table, $tb_conditions);
                //var_dump($tabdatas["user_reset"]);
                if (!empty($tabdatas["user_reset"]) && $tabdatas["user_reset"]!=0) {

                    $user_code_rese=random_int(1100,9999);
                    //$user_code_rese=uniqid('eschol_');
                    //var_dump($user_code_rese);exit();
                    $id_pers=($tabdatas['user_reset'][0])['id_pers_personne'];
                    $from_email = addslashes(($tabdatas['univInfos'][0])['email_univ'].'/_/'.($tabdatas['univInfos'][0])['apps_mail']);
                    //$from_email = ($tabdatas['univInfos'][0])['apps_mail'].'/_/'.($tabdatas['univInfos'][0])['apps_mail'];
                    $email_users = addslashes($tabdatas['email_reset']);
                    $user_nomp = addslashes(($tabdatas["user_reset"][0])['nom_prenom']);
                    //var_dump($from_email);
                    $email_sujet = "CHANGEMENT DE MOT DE PASSE";
                    $infos = "Utiliser le code : ".$user_code_rese;
                    //var_dump($from_email,$email_users,$user_nomp,$email_sujet,$infos);
                    $reps = User::EnvoiMail($from_email,$email_users,$user_nomp,$email_sujet,$infos);
                    //var_dump('reps',$reps);
                    // Teste si la chaîne contient le mot
                    //$reps="n'a code=".$user_code_rese;
                    $tabdatas['reps']=$reps;
                    $fct_exec=$fct_exec."Réponse mail envoie =".$reps." , code =".$user_code_rese." | ";
                    if(strpos($reps, "n'a") == false){
                        //var_dump("Envoyer !");
                        $tabdatas['etat_msg']=1;
                        $table="reset_pass";

                        $tb_conditions=[];
                        $tb_conditions['id_personne']= $id_pers;
                        $tb_infos=$tb_conditions;
                        $info_reset=Model_public::get_selectSQL_ALL_by($table, $tb_conditions);
                        //var_dump($info_reset);
                        if (!empty($info_reset) || $info_reset!=0) {
                            $tb_infos=$tb_conditions;
                            $tb_infos['etat_reset']= 3;
                            $tb_conditions['etat_reset']= 0;
                            $maj_reset=Model_public::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
                            //var_dump($maj_reset);
                        }
                        $tb_conditions=[];
                        $tb_conditions['id_personne']= $id_pers;
                        $tb_conditions['code_reset']= $user_code_rese;
                        $tb_infos=$tb_conditions;
                        $tb_infos['etat_reset']= 0;
                        $add_reset=Model_public::set_insertSQL($table,$tb_infos, $tb_conditions);
                        $page='reset_pass';
                        
                    } else{
                        //var_dump("Non envoyer!");
                        $tabdatas['etat_msg']=0;
                        $page='index';
                    }

                }
                else {
                    $page='index';
                    $fct_exec=$fct_exec.' Personne inconnu !' ;
                }
            }
            

            /*:::::::DEBUT Enregistrement des logs::::::::::*/
            $info = "Home ::: reset_passAction => " . $fct_exec;
            $log_user ="Réinitialisation de Mots de Passe";
            modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
            //:::::::::::::LOGS::::::::::::::::::
            /*:::::::Fin Enregistrement des logs::::::::::*/
            //var_dump($page,$fct_exec);
            View::renderTemplate('Home/'.$page.'.html',$tabdatas);

        }
        else {
            //header('Location: http://'.$_SERVER["HTTP_HOST"]);
            header('Location: https://'.$_SERVER["HTTP_HOST"]);
            exit();
        }
     

        
    }

     public function maj_passAction(){
        $tabdatas=[];
        $tabdatas['get_infos'] = $_GET;
        //var_dump($_POST,$_GET) 2020-11-25 20:59:52

        //var_dump(date("Y-m-d H:i:s"));exit;
        $fct_exec=" | ";
        $id_pers=0;
        $fk_iduniv=0;
        $str =  sha1("inscriptionélèves");

        if (isset($_POST["code_reset"]) && isset($_POST["pass1"]) && isset($_POST["pass2"])) {

            $id_univ = intval(htmlspecialchars($_GET['cible']));
            $pass1 = htmlspecialchars($_POST['pass1']);
            $pass2 = htmlspecialchars($_POST['pass2']);
            $code_reset = htmlspecialchars($_POST['code_reset']);
            $email_reset = htmlspecialchars($_POST['email_reset']);
            $type_user = intval(htmlspecialchars($_GET['type_user']));
            $cible = intval(htmlspecialchars($_GET['cible']));
            $fk_iduniv=$cible;

            $tabdatas['univInfos']=Model_public::get_univInfo_By($id_univ);
            //var_dump("univInfos",$tabdatas['univInfos']);

            if (empty($tabdatas['univInfos']) || $tabdatas['univInfos']==0) {
                header('Location: https://'.$_SERVER["HTTP_HOST"]);
                exit();
            }
            else {
               
                if ($pass1==$pass2) {
                    $table="personne";
                    $tb_conditions=[];
                    $tb_conditions['email']=$email_reset;
                    $tb_conditions['type_pers']=$type_user;
                    $tb_conditions['etat_pers']=1;
                    $tb_conditions['fk_iduniv']=$cible;
                    $info_user=Model_public::get_selectSQL_ALL_by($table, $tb_conditions);
                    $id_pers=intval($info_user[0]['id_pers_personne']);
                    
                    //var_dump($info_user);//exit();
                    if (!empty($info_user) && $info_user!=0) {
                        $table="reset_pass";
                        $tb_conditions=[];
                        $tb_conditions['id_personne']=$id_pers;
                        $tb_conditions['etat_reset']=0;
                        $info_reset=Model_public::get_selectSQL_ALL_by($table, $tb_conditions);
                        //var_dump($info_reset);
                        //var_dump($code_reset);//exit();

                        if (!empty($info_reset) && $info_reset!=0 && intval($info_reset[0]['code_reset'])==$code_reset) {
                            $str =  sha1($pass1);
                            $table="personne";
                            $tb_conditions=[];
                            $tb_infos=[];
                            $tb_conditions['id_pers_personne']=$id_pers;
                            $tb_infos['pass']=$str;
                            $info_reset=Model_public::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
                            
                            $table="reset_pass";
                            $tb_conditions=[];
                            $tb_infos=[];
                            $tb_conditions['id_personne']=$id_pers;
                            $tb_conditions['code_reset']=$code_reset;
                            $tb_infos['etat_reset']=1;
                            $tb_infos['date_reset']=date("Y-m-d H:i:s");
                            $info_reset=Model_public::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);

                            $page='index';
                            $fct_exec=$fct_exec." Modification de Mots de Passe effectuée code =".$code_reset."|";
                            //var_dump($fct_exec);
                        }
                        else {
                            $page='reset_pass';
                            $fct_exec=$fct_exec." Code incorrect |";
                        }

                    }
                    else {
                        $page='reset_pass';
                        $fct_exec=$fct_exec." Utilisateur inconnu |";
                    }
                }
                else {
                    $page='reset_pass';
                    $fct_exec=$fct_exec." Mot de passe non identique|";
                }
                
            }

        }
        else {
            //header('Location: http://'.$_SERVER["HTTP_HOST"]);
            header('Location: https://'.$_SERVER["HTTP_HOST"]);
            exit();
        }


        /*:::::::DEBUT Enregistrement des logs::::::::::*/
        $info = "Home ::: maj_passAction => " . $fct_exec;
        $log_user ="Réinitialisation de Mots de Passe";
        modeldb::set_Ajax_Log($info,$log_user,$id_pers,$fk_iduniv);
        //:::::::::::::LOGS::::::::::::::::::
        /*:::::::Fin Enregistrement des logs::::::::::*/
        View::renderTemplate('Home/'.$page.'.html',$tabdatas);
    
    }   
    public function demarerzoomAction(){
        var_dump($_GET['p']);exit;
        View::renderTemplate('https://www.uges.x-pertizgroup.com/public/ZOOM/index.html');
    }
    public static function loginAction(){
    
        //var_dump("home login",$_POST);//exit;
        //var_dump($_POST,$_GET,$_SERVER["HTTP_HOST"],$_SERVER["REQUEST_URI"]); exit();
        //var_dump($_SERVER);exit;
        if (isset($_SERVER['HTTP_REFERER'])) {
            $_SESSION['eschool_refurl']=$_SERVER['HTTP_REFERER'];
        }
        else {
             $_SESSION['eschool_refurl']='http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        }

        $tabdatas=[];
        $tabdatas['get_infos'] = $_GET;
        $str =  sha1("inscriptionélèves");
        //var_dump("str", $str);

        if (isset($_GET['verif']) && isset($_GET['cible']) && $_GET['verif']==$str ) {

            $id_univ = intval(htmlspecialchars($_GET['cible']));
            $tabdatas['univInfos']=Model_public::get_univInfo_By($id_univ);
            //var_dump("tabdatas", $tabdatas);
            //var_dump("sesion",$_SESSION);//var_dump("get",$_GET);
            //var_dump("univInfos",$tabdatas['univInfos']);exit;
            if (empty($tabdatas['univInfos']) || $tabdatas['univInfos']==0) {            
                //header('Location: http://'.$_SERVER["HTTP_HOST"]);
                header('Location: https://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
                exit();
            }
            else {


                if (isset($_POST['email_login']) && isset($_POST['pass_login']) && isset($_POST['s0']) ) {
                    setcookie('login', "", time() - 3600);
                    setcookie('pass', "", time() - 3600);
                    setcookie('type', "", time() - 3600);
                    setcookie('id_univ', "", time() - 3600);
                    setcookie('user', "", time() - 3600);
                    setcookie('avatar', "", time() - 3600);

                    setcookie (session_id(), "", time() - 3600);
                    session_destroy();
                    session_write_close();

                    $login = htmlspecialchars($_POST['email_login']);
                    $pass = htmlspecialchars($_POST['pass_login']);
                    $pass= sha1( $pass);
                    $type = htmlspecialchars($_POST['s0']);
                    $news_conexion=0;
                }
                elseif(isset($_COOKIE['login']) && isset($_COOKIE['pass']) && isset($_COOKIE['type']) && isset($_COOKIE['id_univ'])) {
                                
                    $login = htmlspecialchars($_COOKIE['login']);
                    $pass = htmlspecialchars($_COOKIE['pass']);
                    $type = htmlspecialchars($_COOKIE['type']);
                    $news_conexion=1;
                }
                else {  header('Location: https://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]); exit(); }
                //var_dump("_COOKIE",$_COOKIE);exit;
                //var_dump("_COOKIE",$_COOKIE);exit;
                //Vérification des identifiants
                $db = \App\Models\User::getVerifUser($login,$pass,$type,$id_univ);
                //var_dump($db);exit;//var_dump($db[0]['id_pers_personne'])
    
                if (count($db)===0 ) {

                    /*:::::::DEBUT Enregistrement des logs::::::::::*/
                    $fct_exec= $login."  type=". $type ." Introuvable ou mauvais mots de passe";
                    $info = "Home ::: loginAction => " . $fct_exec;
                    $log_user =" Page Accueil ";
                    modeldb::set_Ajax_Log($info,$log_user,1,$id_univ);
                    //:::::::::::::LOGS::::::::::::::::::
                    /*:::::::Fin Enregistrement des logs::::::::::*/
                    
                    $tabdatas['login'] = 'error_id_univ';

                    //::DEBUT::Enregistrement de la connexion
                    //$id_personne=0;
                    //échec /réussite
                    //$message='échec connexion user : '.$login.' - type :'.$type;
                    //$etat_conex=0;
                    //Model_public::user_connexion($id_personne,$id_univ,$message,$etat_conex);
                    //::FIN::Enregistrement de la connexion
                    if (!empty($_COOKIE['login'])) {
                        setcookie('login', "", time() - 3600);
                        setcookie('pass', "", time() - 3600);
                        setcookie('type', "", time() - 3600);
                        setcookie('id_univ', "", time() - 3600);
                        setcookie('user', "", time() - 3600);
                        setcookie('avatar', "", time() - 3600);
                        setcookie (session_id(), "", time() - 3600);
                        session_destroy();
                        session_write_close();
                        //var_dump($_SESSION);
                    }


                    View::renderTemplate('Home/index.html',$tabdatas);
                } 
                else {
                   
                    $dbresult = (object) $db[0];

                    //::DEBUT::Enregistrement de la connexion
                    $id_personne=intval($db[0]['id_pers_personne']);
                   
                    $table="connexion";
                    $tb_conditions=[];
                    $tb_conditions["conex_id_personne"]=$id_personne;
                    $log_conect = Model_public::get_selectSQL_ALL_by($table, $tb_conditions);

                    //échec /réussite
                    $message='réussite';
                    $etat_conex=1;

                    if ($news_conexion==0 || $log_conect==0) {
                        Model_public::user_connexion($id_personne,$id_univ,$message,$etat_conex);
                        /*:::::::DEBUT Enregistrement des logs::::::::::*/
                        $fct_exec= $login."  type=". $type ." Connexion Réussie";
                        $info = "Home ::: loginAction => " . $fct_exec;
                        $log_user =" Connexion : Réussie !! ";
                        modeldb::set_Ajax_Log($info,$log_user,1,$id_univ);
                        //:::::::::::::LOGS::::::::::::::::::
                        /*:::::::Fin Enregistrement des logs::::::::::*/
                    }
                    

                    // On écrit un cookie pour se souvenir de l'utilisateur
                    if(!isset($_COOKIE['login'])) {
                        session_start();
                        
                        setcookie('login', $login, time() + 1200, null, null, false, true); 
                        setcookie('pass', $pass, time() + 1200, null, null, false, true); 
                        setcookie('type', $type, time() + 1200, null, null, false, true); 
                        setcookie('id_univ', $id_univ, time() + 1200, null, null, false, true); 

                    }

                    //var_dump($_COOKIE);
                        
                    $tabdata = [
                        'id_pers_personne'  => $dbresult->id_pers_personne,
                        'fk_iduniv'  => $dbresult->fk_iduniv,
                        'nom_prenom' => $dbresult->nom_prenom,
                        'date_naiss' => $dbresult->date_naiss,
                        'lieu_naiss' => $dbresult->lieu_naiss,
                        'sexe' => $dbresult->sexe,
                        'email' => $dbresult->email,
                        'contact' => $dbresult->contact,
                        'type_pers' => $dbresult->type_pers,
                        'id_type' => $dbresult->id_type
                        
                    ];
                    $_SESSION["user"]=$tabdata;
                    $_POST=NULL;
                    $_GET=NULL;
                    unset($_POST);
                    unset($_GET);
                    $typ_user = intval($dbresult->type_pers);
                    //var_dump($dbresult->type_pers,$typ_user);exit();
    
                    $tabdatas['univInfos']=User::getUnivInfos();
                    
                    switch ($typ_user) {
                        case 1:
                                $_SESSION['p'] = "eleve_accueil";
                                $_GET['p']="eleve_accueil";
                                header('Refresh: 1;URL=index.php?p=eleve_accueil');
                                exit;
                                //var_dump("admin type",$typ_user);
                                //View::renderTemplate('Accueil/admin/admin_accueil.html',$tabdata);
                        break;
                        case 3:
                            $_SESSION['p'] = "parent_accueil";
                            $_GET['p']="parent_accueil";
                            header('Refresh: 1;URL=index.php?p=parent_accueil');
                            exit;
                        break;
                        case 2:
                            $_SESSION['p'] = "prof_accueil";
                            $_GET['p']="prof_accueil";
                            header('Refresh: 1;URL=index.php?p=prof_accueil');
                            exit;
                            //var_dump('prof type',$typ_user);
                            //View::renderTemplate('Accueil/prof/prof_accueil.html',$tabdata);
                        break;
                        case 4:
                            $_SESSION['p'] = "accueil";
                            $_GET['p']="accueil";
                            header('Refresh: 1;URL=index.php?p=accueil');
                            exit;
                            //var_dump("admin type",$typ_user);
                            //View::renderTemplate('Accueil/admin/admin_accueil.html',$tabdata);
                        break;
                        
                        default:
                            View::renderTemplate('Accueil/accueil.html',$tabdata);
                        break;
                    }
                    
                }

            }
        
        } 
        else {
            header('Location: https://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
            exit();
        }

    }
    public function inscriptionAction(){

        //var_dump("sesion",$_SESSION);var_dump("post",$_POST);var_dump("get",$_GET);

        $str =  sha1("inscriptionélèves");
        if (isset($_GET['verif']) && isset($_GET['cible']) && $_GET['verif']==$str ) {
            $tabdata=[];
            $tabdata['get_infos'] = $_GET;

            $id_univ = intval(htmlspecialchars($_GET['cible']));
            $tabdata['univInfos']=Model_public::get_univInfo_By($id_univ);
      

            if (empty($tabdata['univInfos']) || $tabdata['univInfos']==0) {
                //header('Location: http://'.$_SERVER["HTTP_HOST"]);
                header('Location: https://'.$_SERVER["HTTP_HOST"]);
                exit();
            }
            else {
                
                $nom = htmlspecialchars($_POST['nom']);
                $prenom = htmlspecialchars($_POST['prenom']);
                $datenaiss = htmlspecialchars($_POST['datenaiss']);
                $lieunaiss = htmlspecialchars($_POST['lieunaiss']);
                $sexe = htmlspecialchars($_POST['genre']);
                $email = htmlspecialchars($_POST['email']);
                $tel = htmlspecialchars($_POST['tel']);

                $pass1 = htmlspecialchars($_POST['pass1']);
                $pass2 = htmlspecialchars($_POST['pass2']);

                $pass1= sha1( $pass1);
                $pass2= sha1( $pass2);
                $type = htmlspecialchars($_POST['s0']);

                if ($pass1  != $pass2) { 

                    $tabdata['infos_message'] = 'mots de passe différents !!';
                    $info =  'mots de passe différents !!';
                    $name= 'admin';
                    Home::Fct_vardump('debug',$name,$info);
                    $info = "Création de Compte : Erreur!!";
                    $log_user =$nom." ". $prenom." email=".$email." type=". $type ;
                    modeldb::setLog($info,$log_user);
                    $_POST=NULL;
                    $_GET=NULL;
                    unset($_POST);
                    unset($_GET);
                    $tmp_p = 'inscript.html';
                    $tmp_var = $tabdata;

                }
                else {
                    // var_dump($id_univ);exit();

                    $dbresult = \App\Models\User::setPersonnes($nom,$prenom, $sexe, $email, $tel, $pass1, $pass2, $type, $datenaiss, $lieunaiss, $id_univ );
                    //var_dump( $dbresult);
                    $tabdata = [
                        'email_inscrip'  => $email,
                        'etat_inscrip'  => $dbresult,
                    ];

                    
                    $dbresult = intval($dbresult);
                    if ($dbresult==1) {
                        $info = "Création de Compte : Reussite!!";
                        $log_user =$nom." ". $prenom." email=".$email." type=". $type ;
                        modeldb::setLog($info,$log_user);
            
                        $tmp_p = 'Home/index.html';
                        $tmp_var = $tabdata;
                        $info="Votre Inscription à été éffectué et votre compte est en cours de validation , Merci de patientez ! vous serai notifiez";


                    }
                    elseif ($dbresult==0) {
                        $info = "Création de Compte : Compte existant!!";
                        $log_user =$nom." ". $prenom." email=".$email." type=". $type ;
                        modeldb::setLog($info,$log_user);
          
                        $tmp_p = 'inscript.html';
                        $tmp_var = $tabdata;
                        $info="Erreur lors de l'inscription ! Compte existant ,Merci de contactez l'administrateur avec le code  :INSCRIPT2020".$dbresult;
                    }
                    elseif ($dbresult==-2) {
                        $info = "Création de Compte : Erreur!!";
                        $log_user =$nom." ". $prenom." email=".$email." type=". $type ;
                        modeldb::setLog($info,$log_user);

                        $tmp_p = 'inscript.html';
                        $tmp_var = $tabdata;
                        $info="Erreur lors de l'inscription ! Merci de contactez l'administrateur avec le code  :INSCRIPT2020".$dbresult;

                    }
                    else {
                        $tmp_p = 'inscript.html';
                        $tmp_var = $tabdata;
                        $info="Erreur lors de l'inscription ! Merci de contactez l'administrateur avec le code  :INSCRIPT2020".$dbresult;
                    }

                    $name= 'setPersonnes result: '.$dbresult;
                    Home::Fct_vardump('debug',$name,$info);
                }


                View::renderTemplate($tmp_p,$tmp_var);
                $_POST=NULL;
                $_GET=NULL;
                unset($_POST);
                unset($_GET);
                exit();
            }
        }
        else {
            //header('Location: http://'.$_SERVER["HTTP_HOST"]);
            header('Location: https://'.$_SERVER["HTTP_HOST"]);
            exit();
        }
     

    
    }

    public static function Fct_vardump($var_envi,$name,$info){

        if($var_envi == "debug"){ 
            //echo "<br/>"; 
            //var_dump($name,$info ); 
            echo '<script>console.log("'.$name.'= '.$info.'");</script>';
            echo '<script>alert("'.$info.'");</script>';
        }
    }


}
