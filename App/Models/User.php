<?php

namespace App\Models;

use PDO;

//require_once('../App/Models/Model_public.php');
//use App\Models\Model_public;

/*
 * Example user model
 *
 * PHP version 7.0
*/

class User extends \Core\Model
{

    /**
     * Get all the users as an associative array
     *
     * @return array
     */
    public static function getAlluser()
    {
        $db = static::getDB();
        // etat_pers = 0'
        $sql = 'SELECT * FROM personne WHERE etat_pers = 0';
        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAlluserBy($type, $fk_iduniv)
    {
        $db = static::getDB();
        // etat_pers = 0'
        switch ($type) {
            case 1:
                //$sql='SELECT * FROM personne INNER JOIN eleve ON personne.fk_iduniv = '.$fk_iduniv.' AND personne.id_type = eleve.id_eleve_eleve WHERE etat_pers = 3 AND type_pers = 1';
                $sql = 'SELECT * FROM 
                (SELECT * FROM personne INNER JOIN eleve ON personne.fk_iduniv = ' . $fk_iduniv . ' AND personne.id_type = eleve.id_eleve_eleve WHERE etat_pers = 3 AND type_pers = 1)tble1
                LEFT JOIN(SELECT * FROM eleve_estds_groupe,groupe WHERE eleve_estds_groupe.elv_ds_grpe_groupe=groupe.groupe_id)tble2
                ON tble1.id_eleve_eleve=tble2.elv_ds_grpe_idelev';
                break;
            case 2:
                $sql = 'SELECT * FROM personne INNER JOIN prof ON personne.fk_iduniv = ' . $fk_iduniv . ' AND personne.id_type = prof.id_prof_prof WHERE etat_pers = 3 AND type_pers = 2';
                break;
            case 3:
                $sql = 'SELECT * FROM personne INNER JOIN parent ON personne.fk_iduniv = ' . $fk_iduniv . ' AND personne.id_type = parent.id_parent_parent WHERE etat_pers = 3 AND type_pers = 3';
                break;
            case 4:
                $sql = 'SELECT * FROM personne INNER JOIN admintab ON personne.fk_iduniv = ' . $fk_iduniv . ' AND personne.id_type = admintab.id_admin_admin WHERE etat_pers = 3 AND type_pers = 4';
                break;

            default:

                break;
        }
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function get_activeAlluserBy($type)
    {
        $db = static::getDB();
        // etat_pers = 0'
        switch ($type) {
            case 1:
                $sql = 'SELECT * FROM personne INNER JOIN eleve ON personne.id_type = eleve.id_eleve_eleve WHERE etat_pers = 1 AND type_pers = 1';
                break;
            case 2:
                $sql = 'SELECT * FROM personne INNER JOIN prof ON personne.id_type = prof.id_prof_prof WHERE etat_pers = 1 AND type_pers = 2';
                break;
            case 3:
                $sql = 'SELECT * FROM personne INNER JOIN parent ON personne.id_type = parent.id_parent_parent WHERE etat_pers = 1 AND type_pers = 3';
                break;
            case 4:
                $sql = 'SELECT * FROM (SELECT * FROM personne INNER JOIN admintab ON personne.id_type = admintab.id_admin_admin WHERE etat_pers = 1 AND type_pers = 4)tmp_admin INNER JOIN (SELECT id_role AS id_roles, lib_role FROM roles)tmp_role ON tmp_admin.id_role = tmp_role.id_roles ';
                break;

            default:

                break;
        }
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function get_activeAll_univUserBy($type, $fk_iduniv)
    {
        $db = static::getDB();
        // etat_pers = 0'
        switch ($type) {
            case 1:
                $sql = 'SELECT * FROM personne INNER JOIN eleve ON personne.id_type = eleve.id_eleve_eleve WHERE etat_pers = 1 AND personne.fk_iduniv=' . $fk_iduniv . ' AND personne.type_pers = 1';
                break;
            case 2:
                $sql = 'SELECT * FROM personne INNER JOIN prof ON personne.id_type = prof.id_prof_prof WHERE etat_pers = 1 AND personne.fk_iduniv=' . $fk_iduniv . ' AND personne.type_pers = 2';
                break;
            case 3:
                $sql = 'SELECT * FROM personne INNER JOIN parent ON personne.id_type = parent.id_parent_parent WHERE etat_pers = 1 AND personne.fk_iduniv=' . $fk_iduniv . ' AND personne.type_pers = 3';
                break;
            case 4:
                $sql = 'SELECT * FROM (SELECT * FROM personne INNER JOIN admintab ON personne.id_type = admintab.id_admin_admin WHERE etat_pers = 1 AND personne.fk_iduniv=' . $fk_iduniv . ' AND personne.type_pers = 4)tmp_admin INNER JOIN (SELECT id_role AS id_roles, lib_role FROM roles)tmp_role ON tmp_admin.id_role = tmp_role.id_roles ';
                break;

            default:

                break;
        }
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getVerifUser($login, $pass, $type, $fk_iduniv)
    {
        $type = intval($type);
        $db = static::getDB();

        if ($type == 4) {
            $sql = ' SELECT * FROM 
            (SELECT * FROM personne WHERE  personne.email = "' . $login . '" AND personne.pass ="' . $pass . '" AND personne.type_pers="' . $type . '" AND personne.etat_pers = 1)perso ,admintab 
            WHERE perso.id_type = admintab.id_admin_admin LIMIT 1';
            //var_dump($login,$pass,$sql);
            //exit();
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //var_dump($result);//exit();

            if (!empty($result) && $result[0]['id_role'] == 1) {
                return  $result;
            } else {

                $sql = 'SELECT * FROM personne WHERE fk_iduniv = "' . $fk_iduniv . '" AND email = "' . $login . '" AND pass ="' . $pass . '" AND type_pers="' . $type . '" AND etat_pers = 1';
                //var_dump($login,$pass,$sql);
                //exit();
                $stmt = $db->query($sql);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            //var_dump($db);
            $sql = 'SELECT * FROM personne WHERE fk_iduniv = "' . $fk_iduniv . '" AND email = "' . $login . '" AND pass ="' . $pass . '" AND type_pers="' . $type . '" AND etat_pers = 1';
            //var_dump($login,$pass,$sql);
            //exit();
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    public static function setPersonnes($nom, $prenom, $sexe, $email, $tel, $pass1, $pass2, $type, $datenaiss, $lieunaiss, $id_univ)
    {

        /*var_dump($_POST,$_GET);
        //Textes complets	id_pers_personne	nom_prenom	date_naiss	lieu_naiss	sexe	email	contact	pass	type_pers 	id_type	etat_pers 

        exit();*/
        $db = static::getDB();
        $sql = 'SELECT * FROM personne WHERE email = "' . $email . '" AND fk_iduniv ="' . $id_univ . '"  AND pass ="' . $pass1 . '"  AND type_pers="' . $type . '"';
        //var_dump($db);var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //var_dump($result);var_dump($sql); var_dump($stmt);var_dump( empty($result) );
        //exit();

        if (empty($result) || $result == 0) {
            $etat_pers = 3;
            switch ($type) {
                case '1':
                    $matricule = [
                        'matricule' =>  htmlspecialchars($_POST['matricule']),
                    ];
                    $sql = ' INSERT INTO eleve (matricule) VALUES ( :matricule); ';
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute($matricule);
                    $etat_pers = 4;

                    break;
                case '2':

                    $matricule = [
                        'etat' =>  1,
                    ];
                    $sql = ' INSERT INTO prof (etat) VALUES ( :etat); ';
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute($matricule);


                    break;
                case '3':
                    $matricule = [
                        'matricule' =>  htmlspecialchars($_POST['matricule']),
                    ];
                    $sql = ' INSERT INTO parent (matricule) VALUES ( :matricule); ';
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute($matricule);


                    break;
                case '4':
                    $matricule = [
                        'etat' =>  1,
                    ];
                    $sql = ' INSERT INTO admintab (etat) VALUES ( :etat); ';
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute($matricule);
                    break;
                default:
                    exit();
                    break;
            }

            $lastid =  $db->lastInsertId();

            $data = [
                'fk_iduniv' => $id_univ,
                'nom_prenom' => $nom . " " . $prenom,
                'date_naiss' => $datenaiss,
                'lieu_naiss' => $lieunaiss,
                'sexe' => $sexe,
                'email' => $email,
                'contact' => $tel,
                'pass' => $pass1,
                'type_pers' => $type,
                'id_type' => $lastid,
                'etat_pers' => $etat_pers

            ];

            $sql = ' INSERT INTO personne (fk_iduniv, nom_prenom,date_naiss	,lieu_naiss, sexe, email, contact, pass, type_pers, id_type, etat_pers) VALUES (:fk_iduniv,  :nom_prenom,:date_naiss,:lieu_naiss , :sexe, :email, :contact, :pass, :type_pers, :id_type, :etat_pers); ';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);

            //var_dump($result);

            if ($result == TRUE) {
                return 1;
            } else {
                return -2;
            }
        } else {
            return 0;
        }
    }

    //Inscription pédagogique élèves
    public static function set_eleve_Personnes($nom, $prenom, $sexe, $email, $tel, $pass1, $pass2, $type, $datenaiss, $lieunaiss, $id_univ)
    {

        /*var_dump($_POST,$_GET);
        //Textes complets	id_pers_personne	nom_prenom	date_naiss	lieu_naiss	sexe	email	contact	pass	type_pers 	id_type	etat_pers 

        exit();*/
        $db = static::getDB();
        $sql = 'SELECT * FROM personne WHERE email = "' . $email . '" AND fk_iduniv ="' . $id_univ . '" AND pass ="' . $pass1 . '" AND type_pers="' . $type . '"';
        //var_dump($db);var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //var_dump($result);var_dump($sql); var_dump($stmt);var_dump( empty($result) );
        //exit();

        if (empty($result) || $result == 0) {


            switch ($type) {
                case '1':

                    if (isset($_POST['nationnalite']) && isset($_POST['seriebac'])) {

                        if (htmlspecialchars($_POST['carteetudiant']) == "") {
                            $carteetudiant =  "CI00000000";
                        } else {
                            $carteetudiant =  htmlspecialchars($_POST['carteetudiant']);
                        }



                        if (!isset($_POST['nationnalite'])) {
                            $_POST['nationnalite'] =  "nationnalite";
                        }
                        if (!isset($_POST['telfixe'])) {
                            $_POST['telfixe'] =  "telfixe";
                        }
                        if (!isset($_POST['nomurget'])) {
                            $_POST['nomurget'] =  "nomurget";
                        }
                        if (!isset($_POST['telurget'])) {
                            $_POST['telurget'] =  "telurget";
                        }
                        if (!isset($_POST['commune'])) {
                            $_POST['commune'] =  "commune";
                        }
                        if (!isset($_POST['quartie'])) {
                            $_POST['quartie'] =  "quartie";
                        }
                        if (!isset($_POST['stuationmat'])) {
                            $_POST['stuationmat'] =  "stuationmat";
                        }
                        if (!isset($_POST['civilite'])) {
                            $_POST['civilite'] =  "civilite";
                        }
                        if (!isset($_POST['niveauetude'])) {
                            $_POST['niveauetude'] =  "niveauetude";
                        }
                        if (!isset($_POST['option'])) {
                            $_POST['option'] =  "option";
                        }
                        if (!isset($_POST['parcours'])) {
                            $_POST['parcours'] =  "parcours";
                        }
                        if (!isset($_POST['profilcandidat'])) {
                            $_POST['profilcandidat'] =  "profilcandidat";
                        }
                        if (!isset($_POST['anciennete'])) {
                            $_POST['anciennete'] =  "anciennete";
                        }
                        if (!isset($_POST['seriebac'])) {
                            $_POST['seriebac'] =  "seriebac";
                        }
                        if (!isset($_POST['numbac'])) {
                            $_POST['numbac'] =  "numbac";
                        }
                        if (!isset($_POST['diplome'])) {
                            $_POST['diplome'] =  "diplome";
                        }
                        if (!isset($_POST['activite'])) {
                            $_POST['activite'] =  "activite";
                        }

                        if (!isset($_POST['emailurget'])) {
                            $_POST['emailurget'] =  "emailurget";
                        }
                        if (!isset($_POST['num_menet'])) {
                            $_POST['num_menet'] =  "num_menet";
                        }
                        if (!isset($_POST['nom_payeur'])) {
                            $_POST['nom_payeur'] =  "nom_payeur";
                        }
                        if (!isset($_POST['email_payeur'])) {
                            $_POST['email_payeur'] =  "email_payeur";
                        }
                        if (!isset($_POST['tel_payeur'])) {
                            $_POST['tel_payeur'] =  "tel_payeur";
                        }


                        $matricule = [
                            'matricule' =>  $carteetudiant,
                            'nationnalite' => htmlspecialchars($_POST['nationnalite']),
                            'telfixe' => htmlspecialchars($_POST['telfixe']),
                            'nomurget' => htmlspecialchars($_POST['nomurget']),
                            'emailurget' => htmlspecialchars($_POST['emailurget']),
                            'telurget' => htmlspecialchars($_POST['telurget']),
                            'commune' => htmlspecialchars($_POST['commune']),
                            'quartie' => htmlspecialchars($_POST['quartie']),
                            'stuationmat' => htmlspecialchars($_POST['stuationmat']),
                            'civilite' => htmlspecialchars($_POST['civilite']),
                            'niveauetude' => htmlspecialchars($_POST['niveauetude']),
                            'options' => htmlspecialchars($_POST['option']),
                            'parcours' => htmlspecialchars($_POST['parcours']),
                            'profilcandidat' => htmlspecialchars($_POST['profilcandidat']),
                            'anciennete' => htmlspecialchars($_POST['anciennete']),
                            'seriebac' => htmlspecialchars($_POST['seriebac']),
                            'numbac' => htmlspecialchars($_POST['numbac']),
                            'diplome' => htmlspecialchars($_POST['diplome']),
                            'activite' => htmlspecialchars($_POST['activite']),

                            'num_mesrs_menet' => htmlspecialchars($_POST['num_mesrs']),
                            'num_affectation' => htmlspecialchars($_POST['num_affect']),

                            'nom_pere' => htmlspecialchars($_POST['nom_pere']),
                            'profession_pere' => htmlspecialchars($_POST['prof_pere']),
                            'tel_pere' => htmlspecialchars($_POST['tel_pere']),
                            'nom_mere' => htmlspecialchars($_POST['nom_mere']),
                            'profession_mere' => htmlspecialchars($_POST['prof_mere']),
                            'tel_mere' => htmlspecialchars($_POST['tel_mere']),


                            'num_menet' => htmlspecialchars($_POST['num_menet']),
                            'nom_payeur' => htmlspecialchars($_POST['nom_payeur']),
                            'email_payeur' => htmlspecialchars($_POST['email_payeur']),
                            'tel_payeur' => htmlspecialchars($_POST['tel_payeur'])
                        ];
                    } else {

                        $matricule = [
                            'matricule' =>  htmlspecialchars($_POST['matricule']),
                            'nationnalite' =>  'nationnalite',
                            'telfixe' =>  'telfixe',
                            'emailurget' =>  '',
                            'nomurget' =>  'nomurget',
                            'telurget' =>  'telurget',
                            'commune' =>  'commune',
                            'quartie' =>  'quartie',
                            'stuationmat' =>  'stuationmat',
                            'civilite' =>  'civilite',
                            'niveauetude' =>  'niveauetude',
                            'options' =>  'option',
                            'parcours' =>  'parcours',
                            'profilcandidat' =>  'profilcandidat',
                            'anciennete' =>  'anciennete',
                            'seriebac' =>  'seriebac',
                            'numbac' =>  'numbac',
                            'diplome' =>  'diplome',
                            'activite' =>  'activite',

                            'num_mesrs_menet' =>  '',
                            'num_affectation' =>  '',

                            'nom_pere' =>  '',
                            'profession_pere' =>  '',
                            'tel_pere' =>  '',
                            'nom_mere' =>  '',
                            'profession_mere' =>  '',
                            'tel_mere' =>  '',

                            'num_menet' => '',
                            'nom_payeur' => '',
                            'email_payeur' => '',
                            'tel_payeur' => ''
                        ];
                    }


                    $sql = ' INSERT INTO eleve (matricule, nationnalite, telfixe, nomurget, emailurget, telurget, commune, quartie, stuationmat, civilite, niveauetude, options, parcours, profilcandidat, anciennete, seriebac, numbac, diplome, activite , num_mesrs_menet , num_affectation , nom_pere , profession_pere , tel_pere , nom_mere , profession_mere , tel_mere , num_menet , nom_payeur , email_payeur , tel_payeur) VALUES ( :matricule , :nationnalite, :telfixe, :nomurget, :emailurget, :telurget, :commune, :quartie, :stuationmat, :civilite, :niveauetude, :options, :parcours, :profilcandidat, :anciennete, :seriebac, :numbac, :diplome, :activite , :num_mesrs_menet , :num_affectation , :nom_pere , :profession_pere , :tel_pere , :nom_mere , :profession_mere , :tel_mere , :num_menet , :nom_payeur , :email_payeur , :tel_payeur); ';
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute($matricule);


                    break;
                case '2':

                    $matricule = [
                        'etat' =>  1,
                    ];
                    $sql = ' INSERT INTO prof (etat) VALUES ( :etat); ';
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute($matricule);


                    break;
                case '3':
                    $matricule = [
                        'matricule' =>  htmlspecialchars($_POST['matricule']),
                    ];
                    $sql = ' INSERT INTO parent (matricule) VALUES ( :matricule); ';
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute($matricule);


                    break;
                case '4':
                    $matricule = [
                        'etat' =>  1,
                    ];
                    $sql = ' INSERT INTO admintab (etat) VALUES ( :etat); ';
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute($matricule);
                    break;
                default:
                    exit();
                    break;
            }

            $lastid =  $db->lastInsertId();
            $ideleve = $lastid;
            $data = [
                'fk_iduniv' => $id_univ,
                'nom_prenom' => $nom . " " . $prenom,
                'date_naiss' => $datenaiss,
                'lieu_naiss' => $lieunaiss,
                'sexe' => $sexe,
                'email' => $email,
                'contact' => $tel,
                'pass' => $pass1,
                'type_pers' => $type,
                'id_type' => $lastid,
                'etat_pers' => 4

            ];
            $db_pers = static::getDB();
            $sql = ' INSERT INTO personne (fk_iduniv, nom_prenom,date_naiss	,lieu_naiss, sexe, email, contact, pass, type_pers, id_type,etat_pers) VALUES (:fk_iduniv,  :nom_prenom,:date_naiss,:lieu_naiss , :sexe, :email, :contact, :pass, :type_pers, :id_type,:etat_pers); ';
            $stmts = $db_pers->prepare($sql);
            $results = $stmts->execute($data);
            $id_personne =  $db_pers->lastInsertId();
            //var_dump($result);

            if ($results == TRUE) {


                if ($_FILES['fichierphoto']['name'] == '' || $_FILES['fichierphoto']['error'] == 4 || $_FILES['fichierphoto']['size'] == 0) {
                    //var_dump('fichierphoto vide');
                    $filea = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/img/user_tmp.jpg';
                    $chemin_destination = '../files/' . $id_personne . '/';
                    if (!is_dir($chemin_destination)) {
                        mkdir($chemin_destination, 0777);
                    }
                    $fichier =  $chemin_destination . $id_personne . '.jpg';
                    if (copy($filea, $fichier)) {
                    }
                    $fichier =  $chemin_destination . 'tiny' . $id_personne . '.jpg';
                    if (copy($filea, $fichier)) {
                    }
                } else {
                    if (isset($_FILES['fichierphoto'])) {
                        User::send_Eleve_Img($id_personne);
                    }
                }
                //var_dump('_SESSION',$_SESSION);
                if (($_FILES['docsToUpload']['name'])[0] == '' || ($_FILES['docsToUpload']['error'])[0] == 4 || ($_FILES['docsToUpload']['size'])[0] == 0) {
                    //var_dump('docsToUpload vide');
                    $filea = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/img/inscript.jpg';
                    $chemin_destination = '../files/' . $id_personne . '/';
                    if (!is_dir($chemin_destination)) {
                        mkdir($chemin_destination, 0777);
                    }
                    $chemin_destination = $chemin_destination . '/' . 'dossier' . '/';
                    if (!is_dir($chemin_destination)) {
                        mkdir($chemin_destination, 0777);
                    }
                    $fichier =  $chemin_destination . 'Inscrit.jpg';
                    if (file_exists($fichier)) {
                        unlink($fichier);
                    }
                    if (copy($filea, $fichier)) {
                    }
                } else {

                    if (isset($_FILES['docsToUpload'])) {

                        $nbres_files = count(($_FILES['docsToUpload'])['name']);
                        //var_dump('nbres_files', $nbres_files  ); 
                        if ($nbres_files > 0) {
                            for ($i = 0; $i < $nbres_files; $i++) {
                                //var_dump($i ); 
                                $tmp_file_img_error = (($_FILES['docsToUpload'])['error'])[$i];
                                $tmp_file_type = (($_FILES['docsToUpload'])['type'])[$i];
                                $tmp_file_img_tmp_name = (($_FILES['docsToUpload'])['tmp_name'])[$i];
                                $tmp_file_img_size = (($_FILES['docsToUpload'])['size'])[$i];
                                $tmp_file_img_name = (($_FILES['docsToUpload'])['name'])[$i];
                                User::uploadFiles($id_personne, $tmp_file_img_error, $tmp_file_type, $tmp_file_img_tmp_name, $tmp_file_img_size, $tmp_file_img_name);
                            }
                        }
                    }
                }


                //Verification inscription
                //id_eleve	id_annee_scola	date_inscription
                $lib_niveau = htmlspecialchars($_POST['niveauetude']);
                $lib_filere = htmlspecialchars($_POST['parcours']);
                $id_univ = intval(htmlspecialchars($_SESSION['eschool_ville']));
                $get_uniq_filieresBy =  Model_public::get_uniq_filieresBy($lib_filere, $id_univ);
                $get_uniq_niveauBy = Model_public::get_uniq_niveauBy($lib_niveau, $id_univ);

                $niveau =  intval(($get_uniq_niveauBy[0])['id_niveau']);
                $classe =  intval(($get_uniq_filieresBy[0])['id_classe_classe']);

                //var_dump(($get_uniq_filieresBy[0])['id_classe_classe'], ($get_uniq_niveauBy[0])['id_niveau']);


                $data = [
                    'id_eleve' => $ideleve,
                    'annee_scola' => "2020-2021",
                    'niveau' => $niveau,
                    'classe' => $classe

                ];

                $sql = ' INSERT INTO preinscription (id_eleve,annee_scola,niveau,classe) VALUES ( :id_eleve,:annee_scola,:niveau,:classe); ';
                $stmt = $db->prepare($sql);
                $result = $stmt->execute($data);





                return 1;
            } else {
                return -2;
            }
        } else {
            return 0;
        }
    }
    //Set Inscription pédagogique élèves   avec import  
    public static function set_importeleve_Personnes($infos_tb)
    {

        /*var_dump($_POST,$_GET);
        //Textes complets	id_pers_personne	nom_prenom	date_naiss	lieu_naiss	sexe	email	contact	pass	type_pers 	id_type	etat_pers 

        exit();*/
        $nom=$infos_tb[0];
        $prenom=$infos_tb[1];
        $sexe=$infos_tb[2];
        $carteetudiant=$infos_tb[3];
        $email=$infos_tb[4];
        $contact=$infos_tb[5];

        $datenaiss=$infos_tb[6];
        $lieunaiss=$infos_tb[7];
        $nationalite=$infos_tb[8];

        $nom_pere=$infos_tb[9];
        $profession_pere=$infos_tb[10];
        $tel_pere=$infos_tb[11];
        $nom_mere=$infos_tb[12];
        $profession_mere=$infos_tb[13];
        $tel_mere=$infos_tb[14];

        $pass1=$infos_tb[15];
        $pass2=$infos_tb[15];

        $id_univ=$infos_tb[16];
        $type=1;


        $db = static::getDB();
        $sql = 'SELECT * FROM personne WHERE email = "' . $email . '" AND fk_iduniv ="' . $id_univ . '" AND pass ="' . $pass1 . '" AND type_pers="' . $type . '"';
        //var_dump($db);var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //var_dump($result);var_dump($sql); var_dump($stmt);var_dump( empty($result) );
        //exit();

        if (empty($result) || $result == 0) {


            $matricule = [
                'matricule' =>  $carteetudiant,
                'nationnalite' =>  $nationalite,
                'telfixe' => "",
                'nomurget' => "",
                'emailurget' => "",
                'telurget' => "",
                'commune' => "",
                'quartie' => "",
                'stuationmat' => "",
                'civilite' => "",
                'niveauetude' => $infos_tb[18],
                'options' => "",
                'parcours' =>  $infos_tb[17],
                'profilcandidat' => "",
                'anciennete' => "",
                'seriebac' => "",
                'numbac' => "",
                'diplome' =>"",
                'activite' => "",
                'num_mesrs_menet' => "",
                'num_affectation' => "",

                'nom_pere' =>  $nom_pere,
                'profession_pere' => $profession_pere,
                'tel_pere' => $tel_pere,
                'nom_mere' =>  $nom_mere,
                'profession_mere' => $profession_mere,
                'tel_mere' => $tel_mere,

                'num_menet' => "",
                'nom_payeur' =>"",
                'email_payeur' => "",
                'tel_payeur' => ""
            ];

            $sql = ' INSERT INTO 
                eleve (matricule, nationnalite, telfixe, nomurget, emailurget, telurget, commune, quartie, stuationmat, civilite, niveauetude, options, parcours, profilcandidat, anciennete, seriebac, numbac, diplome, activite , num_mesrs_menet , num_affectation , nom_pere , profession_pere , tel_pere , nom_mere , profession_mere , tel_mere , num_menet , nom_payeur , email_payeur , tel_payeur) VALUES ( :matricule , :nationnalite, :telfixe, :nomurget, :emailurget, :telurget, :commune, :quartie, :stuationmat, :civilite, :niveauetude, :options, :parcours, :profilcandidat, :anciennete, :seriebac, :numbac, :diplome, :activite , :num_mesrs_menet , :num_affectation , :nom_pere , :profession_pere , :tel_pere , :nom_mere , :profession_mere , :tel_mere , :num_menet , :nom_payeur , :email_payeur , :tel_payeur);
            ';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($matricule);

            $lastid =  $db->lastInsertId();
            $ideleve = $lastid;
            $data = [
                'fk_iduniv' => $id_univ,
                'nom_prenom' => $nom . " " . $prenom,
                'date_naiss' => $datenaiss,
                'lieu_naiss' => $lieunaiss,
                'sexe' => $sexe,
                'email' => $email,
                'contact' => $contact,
                'pass' => $pass1,
                'type_pers' => $type,
                'id_type' => $lastid,
                'etat_pers' => 4
      
            ];
            //var_dump($data);
            $db_pers = static::getDB();
            $sql = ' INSERT INTO 
                personne (fk_iduniv, nom_prenom,date_naiss	,lieu_naiss, sexe, email, contact, pass, type_pers, id_type,etat_pers) VALUES (:fk_iduniv,  :nom_prenom,:date_naiss,:lieu_naiss , :sexe, :email, :contact, :pass, :type_pers, :id_type,:etat_pers); 
            ';
            $stmts = $db_pers->prepare($sql);
            $results = $stmts->execute($data);
            $id_personne =  $db_pers->lastInsertId();
            //var_dump($result);
            if ($results == TRUE) {

                //Verification inscription
                //id_eleve	id_annee_scola	date_inscription
                $lib_niveau = $infos_tb[18];
                //$niveauetude;
                $lib_filere =$infos_tb[17];
                //$parcours;
                $id_univ = $id_univ;
                $get_uniq_filieresBy =  Model_public::get_uniq_filieresBy($lib_filere, $id_univ);
                $get_uniq_niveauBy = Model_public::get_uniq_niveauBy($lib_niveau, $id_univ);

                $niveau =  intval(($get_uniq_niveauBy[0])['id_niveau']);
                $classe =  intval(($get_uniq_filieresBy[0])['id_classe_classe']);

                //var_dump(($get_uniq_filieresBy[0])['id_classe_classe'], ($get_uniq_niveauBy[0])['id_niveau']);

                $data = [
                    'id_eleve' => $ideleve,
                    'annee_scola' => "2020-2021",
                    'niveau' => $niveau,
                    'classe' => $classe
                ];

                $sql = ' INSERT INTO preinscription (id_eleve,annee_scola,niveau,classe) VALUES ( :id_eleve,:annee_scola,:niveau,:classe); ';
                $stmt = $db->prepare($sql);
                $result = $stmt->execute($data);

                return 1;
            } else {
                return -2;
            }
        } else {
            return 0;
        }
    }

    
    public static function send_Eleve_Img($id_eleve)
    {

        if ((isset($_FILES['fichierphoto']))) {

            if ($_FILES['fichierphoto']['error']) {
                $msgResut = "error";
                switch (intval($_FILES['file']['error'])) {
                    case 1: // UPLOAD_ERR_INI_SIZE     
                        $msgResut = "Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) !";
                        break;
                    case 2: // UPLOAD_ERR_FORM_SIZE     
                        $msgResut =  "Le fichier dépasse la limite autorisée dans le formulaire HTML !";
                        break;
                    case 3: // UPLOAD_ERR_PARTIAL     
                        $msgResut =  "L'envoi du fichier a été interrompu pendant le transfert !";
                        break;
                    case 4: // UPLOAD_ERR_NO_FILE     
                        $msgResut =  "Le fichier que vous avez envoyé a une taille nulle !";
                        break;
                }
                return  $msgResut;
            } else if ($_FILES['fichierphoto']['error'] == UPLOAD_ERR_OK) {

                $files_path = $_FILES['fichierphoto']['name'];
                $extension_upload = pathinfo($files_path, PATHINFO_EXTENSION);
                $extensions_autorisees = array('jpg', 'jpeg', 'png');

                if (in_array($extension_upload, $extensions_autorisees)) {
                    //$chemin_destination = '../wai.ci/ftp/FTPUSER/'.$_FILES['file']['name'].'/';
                    $chemin_destination = '../files/' . $id_eleve . '/';

                    if (is_dir($chemin_destination)) {

                        $fichier =  $chemin_destination . $id_eleve . '.jpg';

                        if (file_exists($fichier)) {
                            unlink($fichier);
                        }
                    } else {

                        mkdir($chemin_destination, 0777);
                    }


                    $taille = getimagesize($_FILES['fichierphoto']['tmp_name']);
                    $largeur = $taille[0];
                    $hauteur = $taille[1];
                    $largeur_miniature = 400;
                    $hauteur_miniature = $hauteur / $largeur * 400;

                    if ($extension_upload == 'png') {
                        $im = imagecreatefrompng($_FILES['fichierphoto']['tmp_name']);
                    }
                    if ($extension_upload == 'jpg' || $extension_upload == 'jpeg') {
                        $im = imagecreatefromjpeg($_FILES['fichierphoto']['tmp_name']);
                    }


                    $im_miniature = imagecreatetruecolor($largeur_miniature, $hauteur_miniature);
                    imagecopyresampled($im_miniature, $im, 0, 0, 0, 0, $largeur_miniature, $hauteur_miniature, $largeur, $hauteur);
                    imagejpeg($im_miniature, $chemin_destination . $id_eleve . '.jpg', 40);

                    $largeur_miniature = 100;
                    $hauteur_miniature = $hauteur / $largeur * 100;

                    if ($extension_upload == 'png') {
                        $im = imagecreatefrompng($_FILES['fichierphoto']['tmp_name']);
                    }
                    if ($extension_upload == 'jpg' || $extension_upload == 'jpeg') {
                        $im = imagecreatefromjpeg($_FILES['fichierphoto']['tmp_name']);
                    }

                    $im_miniature = imagecreatetruecolor($largeur_miniature, $hauteur_miniature);
                    imagecopyresampled($im_miniature, $im, 0, 0, 0, 0, $largeur_miniature, $hauteur_miniature, $largeur, $hauteur);
                    imagejpeg($im_miniature, $chemin_destination . 'tiny' . $id_eleve . '.jpg', 10);

                    unset($_FILES['fichierphoto']);

                    return 'images upload';
                } else {
                    return 'error type fichier';
                }
            } else {
                return 'error fichier introuvable';
            }
        }
    }

    public static function setEtatPers($idpers, $idtype, $type, $mode, $id_role)
    {
        $db = static::getDB();

        $sql_infos = 'SELECT * FROM personne WHERE id_pers_personne = "' . $idpers . '" AND type_pers ="' . $type . '" AND id_type="' . $idtype . '"';
        $stmt_infos = $db->query($sql_infos);
        $result_infos = $stmt_infos->fetchAll(PDO::FETCH_ASSOC);
        //var_dump( $sql_infos);

        if (empty($result_infos)) {

            return 0;
        } else {
            $result_infos = (object)$result_infos[0];
            //var_dump($result_infos,$idpers,$idtype,$type,$mode);exit;
            
            $table='infosuniv';
            $tb_conditions=[];
            $tb_conditions['id_univ']=intval($result_infos->fk_iduniv);
            $infos_univ=(Model_public::get_selectSQL_ALL_by($table, $tb_conditions))[0];
            //var_dump( $infos_univ);exit();

            switch ($mode) {
                case 0:
                    $mode = -1;
                    break;
                case 1:
                    $mode = 1;
                    break;

                default:
                    $mode = -2;
                    break;
            }

            $sql = 'UPDATE personne SET etat_pers = ' . $mode . ' WHERE  id_pers_personne = ' . $idpers;
            $stmt = $db->query($sql);
            $stmt->execute();
            if ($id_role == NULL) {
                $id_role = 0;
            }

            if ($type == 4) {
                $sql = 'UPDATE admintab SET id_role = ' . $id_role . ' WHERE  id_admin_admin = ' . $idtype;
                $stmt = $db->query($sql);
                $stmt->execute();
            }

            $from_email = $infos_univ['email_univ'].'/_/'.$infos_univ['apps_mail'];
            $email_users = addslashes($result_infos->email);
            $user_nomp = addslashes($result_infos->nom_prenom);
            //var_dump($user_nomp);
            $email_sujet = "ACTIVATION DE COMPTE ";
            $infos = "Félicitations, nous vous informons de l’activation de votre compte. Vous pouvez vous connecter en cliquant sur le bouton ci-dessous . <br> Cordialement.";

            $reps = User::EnvoiMail($from_email,  $email_users,  $user_nomp, $email_sujet, $infos);
            //var_dump($reps);

            return $reps;
        }
    }

    public static function getUnivInfos()
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM infosuniv';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    // **Envoi de email
    public static function EnvoiMail($from_email, $email_users, $user_nomp, $email_sujet, $infos)
    {
        //var_dump($_SERVER['HTTP_HOST']);

        //$to = "ngbla.elvis@gmail.com";
        //$subject = "HTML email";
        //$message = "<html><head><title>HTML email</title></head><body> <p>This email is Test !</p></body></html> ";
        // Always set content-type when sending HTML email
        //$headers = "MIME-Version: 1.0" . "\r\n";
        //$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        // More headers
        //$headers .= 'From: <info@etspoincare.com>' . "\r\n";
        //$headers .= 'Cc: ngbla.elvis@gmail.com' . "\r\n";
        //mail($to,$subject,$message,$headers); exit;

        $tb_mail=explode('/_/',$from_email);
        $from_email=$tb_mail[0];
        if (isset($tb_mail[1])) {$send_email=$tb_mail[1];}
        else {$send_email=$tb_mail[0]; }
        $liens=$_SERVER['HTTP_HOST'];

        if (filter_var($email_users, FILTER_VALIDATE_EMAIL)) {

                //$to = "ngbla.elvis@gmail.com";
                //$subject = "HTML email";
                //$message = "<html><head><title>Test Email</title></head><body><p>This email is test!</p></body></html>";
                // Always set content-type when sending HTML email
                //$headers = "MIME-Version: 1.0" . "\r\n";
                //$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                // More headers
                //$headers .= 'From: <info@etspoincare.com>' . "\r\n";
                //$headers .= 'Cc: ngbla.elvis@gmail.com' . "\r\n";
                //mail($to,$subject,$message,$headers); exit;
                         
                $to = $email_users;
                //$to = "ngbla.elvis@gmail.com";
                $subject = $email_sujet;
                //$subject = "HTML email";

                //$message = "<html><head><title>Test Email</title></head><body><p>This email is test!</p></body></html>";
   
            
                $txt = "<html><head><title>" . $email_sujet . "</title></head><body>";
                $txt =$txt." <div><div style='background:#f9fbfe;background-color:#f9fbfe;Margin:0px auto;max-width:600px'>         <table role='presentation' style='background:#f9fbfe;background-color:#f9fbfe;width:100%' cellspacing='0' cellpadding='0' border='0' align='center'> <tbody><tr> <td style='direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top'>  
                <div class='m_8806712116619041824mj-column-per-100' style='font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%'>    <div style='font-family:montserrat,arial;font-size:20px;font-weight:600;line-height:35px;text-align:center;color:#000028;height:37px'>Bonjour ";
                $txt =$txt." ".$user_nomp;
                $txt =$txt." ,</div></div></td></tr></tbody> </table></div> <div class='m_8806712116619041824border-mobile' style='background:#f9fbfe;background-color:#f9fbfe;Margin:0px auto;max-width:600px'>  <table role='presentation' style='background:#f9fbfe;background-color:#f9fbfe;width:100%' cellspacing='0' cellpadding='0' border='0' align='center'>  <tbody> <tr>  <td style='direction:ltr;font-size:0px;padding:10px;text-align:center;vertical-align:top'>       <div style='background:white;background-color:white;Margin:0px auto;border-radius:10px;max-width:580px'>  <table role='presentation' style='background:white;background-color:white;width:100%;border-radius:10px' cellspacing='0' cellpadding='0' border='0' align='center'>    <tbody>   <tr>    <td style='direction:ltr;font-size:0px;padding:0;text-align:center;vertical-align:top'>    <div class='m_8806712116619041824mj-column-per-60' style='font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%'>    <table role='presentation' style='vertical-align:top' width='100%' cellspacing='0' cellpadding='0' border='0'>   <tbody><tr>   <td class='m_8806712116619041824align' style='font-size:0px;padding:10px 25px;padding-top:4px;padding-bottom:0px;word-break:break-word' align='left'>    <div style='font-family:montserrat,arial;font-size:14px;font-weight:600;line-height:16px;text-align:left;color:#000227'>    " . $email_sujet . "    </div>   </td>   </tr>  <tr>    <td class='m_8806712116619041824align' style='font-size:0px;padding:10px 25px;padding-bottom:0px;word-break:break-word' align='left'>    <div style='font-family:montserrat,arial;font-size:14px;line-height:18px;text-align:left;color:#323030'>  " . $infos . "   </div>    </td>   </tr>   </tbody>   </table>    </div>   <div class='m_8806712116619041824mj-column-per-30' style='font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%'>   <table role='presentation' style='vertical-align:top' width='100%' cellspacing='0' cellpadding='0' border='0'>   <tbody>  <tr>  <td role='presentation' style='border:none' valign='middle' align='center'>   <a href='https://" .$liens. "' style='background:#0a2456;color:white;font-family:arial;font-size:12px;font-weight:600;line-height:120%;Margin:0;text-decoration:none;text-transform:none;padding:10px 25px;display:inline-block;border-radius:10px' bgcolor='#0A2456' target='_blank' data-saferedirecturl='https://" .$liens. "'>  Se connecter</a>    </td>   </tr>    </tbody>  </table>  </div>   </td>   </tr>   </tbody>  </table>    </div>    </td>  </tr> </tbody>   </table>  </div>  </div> ";
                //print_r($txt);
                $txt .= "</body></html>";
                //print_r($txt);

                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                // More headers
                $headers .= 'From: <'.$send_email.'>' . "\r\n";
                //$headers .= 'From: <info@etspoincare.com>' . "\r\n";
                $headers .= 'Cc: ' .$from_email.''.  "\r\n";
                //$headers .= 'Cc: ngbla.elvis@gmail.com' . "\r\n";
                //$headers .= 'Reply-To:'.$from_email.''. "\r\n";
                //$headers .= 'Reply-To: ngbla.elvis@gmail.com'. "\r\n";

                //print_r($txt); message
                $mail_envoi=mail($to,$subject,$txt,$headers);
                //var_dump($mail_envoi); 
                if ($mail_envoi) {
                    return "Le message  : " . $subject . " a été envoyé à : " . $user_nomp . " avec l 'email : " . $email_users;
                } else {
                    return "Le message  : " . $subject . " n 'a pu être envoyé à : " . $user_nomp . " avec l 'email : " . $email_users;
                }
        }
        else {
            return "Email  : " . $email_users . " Invalide ! " ;
        }

 
    }



    public static function get_userNotifs($id_user, $type_user)
    {

        $db = static::getDB();
        $sql = 'SELECT tmp_notif_f.*,tmp_pers.* FROM (SELECT * FROM( SELECT * FROM notif_user WHERE usernotif_iduser = ' . $id_user . ' AND usernotif_typeuser = ' . $type_user . ')tpm_alertnotif INNER JOIN notification ON tpm_alertnotif.usernotif_id = notification.id_notif)tmp_notif_f, (SELECT id_pers_personne AS pers_id,nom_prenom,email,contact FROM personne )tmp_pers WHERE tmp_notif_f.createur_notif = tmp_pers.pers_id AND tmp_notif_f.notif_etat =1 AND tmp_notif_f.usernotif_etat = 1';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result;
        }
    }

    public static function get_userNotifsBy($id_user, $type_user, $id_univ)
    {

        $db = static::getDB();
        $sql = 'SELECT tmp_notif_f.*,tmp_pers.* FROM (SELECT * FROM( SELECT * FROM notif_user WHERE usernotif_iduser =  ' . $id_user . ' AND usernotif_typeuser = ' . $type_user . ')tpm_alertnotif INNER JOIN (SELECT * FROM notification WHERE fk_iduniv = ' . $id_univ . ' AND notif_etat = 1  AND notification.notif_debut <= Now() AND Now() <= notification.notif_fin ) tmp_notification ON tpm_alertnotif.usernotif_id = tmp_notification.id_notif)tmp_notif_f, (SELECT id_pers_personne AS pers_id,nom_prenom,email,contact FROM personne )tmp_pers WHERE tmp_notif_f.createur_notif = tmp_pers.pers_id AND tmp_notif_f.notif_etat =1 AND tmp_notif_f.usernotif_etat = 1';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result;
        }
    }



    public static function get_userNotifs_old($id_user, $type_user)
    {

        $db = static::getDB();
        $sql = 'SELECT * FROM (SELECT * FROM(SELECT * FROM notification INNER JOIN (SELECT id_type as createur_idtype,id_pers_personne AS pers_id ,nom_prenom,contact FROM personne WHERE type_pers = 4)tmp_pers ON notification.createur_notif = tmp_pers.createur_idtype)tmp_f WHERE tmp_f.notif_etat =1)tmp_final_notif INNER JOIN (SELECT * FROM notif_user WHERE usernotif_iduser =' . $id_user . ' AND usernotif_typeuser = ' . $type_user . ' AND usernotif_etat = 1)tmp_usernotif ON tmp_final_notif.id_notif = tmp_usernotif.usernotif_id ';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result;
        }
    }

    public static function get_lastAnnee()
    {

        $db = static::getDB();
        $sql = 'SELECT MAX(id_anscol_annee_scolaire) AS id_lastannee FROM annee_scolaire';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result;
        }
    }

    public static function get_eleveinfos($email_eleve, $datenais)
    {

        $db = static::getDB();
        $sql = 'SELECT * FROM eleve INNER JOIN (SELECT * FROM personne WHERE email = "' . $email_eleve . '" AND date_naiss ="' . $datenais . '" AND type_pers = 1 )tmp_eleve ON eleve.id_eleve_eleve = tmp_eleve.id_type';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result[0];
        }
    }
    public static function get_eleveinfosBy($email_eleve, $datenais, $fk_iduniv)
    {

        $db = static::getDB();
        $sql = 'SELECT * FROM eleve INNER JOIN (SELECT * FROM personne WHERE email = "' . $email_eleve . '" AND date_naiss ="' . $datenais . '" AND type_pers = 1 AND fk_iduniv = ' . $fk_iduniv . ')tmp_eleve ON eleve.id_eleve_eleve = tmp_eleve.id_type';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result[0];
        }
    }

    public static function get_last_eleveInscript($id_eleve, $annee_scola)
    {

        $db = static::getDB();
        $sql = 'SELECT * FROM preinscription WHERE id_eleve = ' . $id_eleve . ' AND annee_scola = "' . $annee_scola . '"';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result;
        }
    }
    public static function get_preinsc_anneScol()
    {

        $db = static::getDB();
        $sql = 'SELECT annee_scola FROM preinscription GROUP BY annee_scola';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result;
        }
    }

    public static function get_verif_eleveInscript($email, $id_univ, $anneScol)
    {

        $db = static::getDB();
        $sql = 'SELECT * FROM
        (SELECT * FROM personne WHERE type_pers=1 AND personne.fk_iduniv=' . $id_univ . ' AND personne.email="' . $email . '")tmp_pers , preinscription
        WHERE tmp_pers.id_type=preinscription.id_eleve AND preinscription.annee_scola="' . $anneScol . '" LIMIT 1';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result;
        }
    }
    public static function uploadFiles($id_pers, $tmp_file_img_error, $tmp_file_type, $tmp_file_img_tmp_name, $tmp_file_img_size, $tmp_file_img_name)
    {

        //var_dump($id_mat, $id_pers, $file);
        //exit;

        //var_dump('UPLOAD_ERR_OK',UPLOAD_ERR_OK);
        //var_dump('tmp_file_img_error',$tmp_file_img_error);
        //var_dump('tmp_file_type',$tmp_file_type);
        //var_dump('tmp_file_img_tmp_name',$tmp_file_img_tmp_name);
        //var_dump('tmp_file_img_name',$tmp_file_img_name);
        $infosfichier = pathinfo($tmp_file_img_name);
        $extension_upload = $infosfichier['extension'];
        $extensions_autorisees = array('pdf', 'doc', 'docx', 'xlsx', 'xls', 'pptx', 'ppt', 'jpg', 'jpeg', 'png');

        if (in_array($extension_upload, $extensions_autorisees)) {

            //var_dump('extension_upload' ,$extension_upload );exit;

            if (isset($tmp_file_img_error)) {

                if ($tmp_file_img_error != UPLOAD_ERR_OK) {
                    //$msgResut ='error doc';
                    switch ($tmp_file_img_error) {
                        case 1: // UPLOAD_ERR_INI_SIZE     
                            $msgResut = "Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) !";
                            break;
                        case 2: // UPLOAD_ERR_FORM_SIZE     
                            $msgResut =  "Le fichier dépasse la limite autorisée dans le formulaire HTML !";
                            break;
                        case 3: // UPLOAD_ERR_PARTIAL     
                            $msgResut =  "L'envoi du fichier a été interrompu pendant le transfert !";
                            break;
                        case 4: // UPLOAD_ERR_NO_FILE     
                            $msgResut =  "Le fichier que vous avez envoyé a une taille nulle !";
                            break;
                    }
                    return  $msgResut;
                } else if ($tmp_file_img_error  == UPLOAD_ERR_OK) {


                    //$chemin_destination = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['SCRIPT_NAME' ],3).'/BanqueDefichiers/COURS/'.$id_pers.'/';
                    $chemin_destination = '../files/' . $id_pers . '/';

                    //var_dump($tmp_file_img_name);
                    $file_name_tmp = User::replaceSpecialChar($tmp_file_img_name);
                    //var_dump($nom);
                    $file_name_tmp = str_replace(' ', '_', $file_name_tmp);
                    //var_dump($nom);

                    //Verification si documents existe
                    if (is_dir($chemin_destination)) {

                        $chemin_destination = $chemin_destination . '/' . 'dossier' . '/';

                        if (is_dir($chemin_destination)) {
                            $fichier =  $chemin_destination . $file_name_tmp . '.' . $extension_upload;
                            if (file_exists($fichier)) {
                                unlink($fichier);
                            }
                        } else {
                            mkdir($chemin_destination, 0777);
                        }
                    } else {

                        mkdir($chemin_destination, 0777);
                        $chemin_destination = $chemin_destination . '/' . 'dossier' . '/';

                        if (is_dir($chemin_destination)) {
                            $fichier =  $chemin_destination . $file_name_tmp . '.' . $extension_upload;
                            if (file_exists($fichier)) {
                                unlink($fichier);
                            }
                        } else {
                            mkdir($chemin_destination, 0777);
                        }
                    }

                    //Creation du fichier
                    $fichier =  $chemin_destination . $tmp_file_img_name;

                    if (move_uploaded_file($tmp_file_img_tmp_name, $fichier)) {

                        return 1;
                    } else {
                        return 0;
                    }

                    unset($tmp_file_img_name);
                    unset($file);
                }
            }
        }
    }

    //Remplace les caractères dans un texte
    public static function replaceSpecialChar($str)
    {

        $ch0 = array(
            "œ" => "oe",
            "Œ" => "OE",
            "æ" => "ae",
            "Æ" => "AE",
            "À" => "A",
            "Á" => "A",
            "Â" => "A",
            "à" => "A",
            "Ä" => "A",
            "Å" => "A",
            "&#256;" => "A",
            "&#258;" => "A",
            "&#461;" => "A",
            "&#7840;" => "A",
            "&#7842;" => "A",
            "&#7844;" => "A",
            "&#7846;" => "A",
            "&#7848;" => "A",
            "&#7850;" => "A",
            "&#7852;" => "A",
            "&#7854;" => "A",
            "&#7856;" => "A",
            "&#7858;" => "A",
            "&#7860;" => "A",
            "&#7862;" => "A",
            "&#506;" => "A",
            "&#260;" => "A",
            "à" => "a",
            "á" => "a",
            "â" => "a",
            "à" => "a",
            "ä" => "a",
            "å" => "a",
            "&#257;" => "a",
            "&#259;" => "a",
            "&#462;" => "a",
            "&#7841;" => "a",
            "&#7843;" => "a",
            "&#7845;" => "a",
            "&#7847;" => "a",
            "&#7849;" => "a",
            "&#7851;" => "a",
            "&#7853;" => "a",
            "&#7855;" => "a",
            "&#7857;" => "a",
            "&#7859;" => "a",
            "&#7861;" => "a",
            "&#7863;" => "a",
            "&#507;" => "a",
            "&#261;" => "a",
            "Ç" => "C",
            "&#262;" => "C",
            "&#264;" => "C",
            "&#266;" => "C",
            "&#268;" => "C",
            "ç" => "c",
            "&#263;" => "c",
            "&#265;" => "c",
            "&#267;" => "c",
            "&#269;" => "c",
            "Ð" => "D",
            "&#270;" => "D",
            "&#272;" => "D",
            "&#271;" => "d",
            "&#273;" => "d",
            "È" => "E",
            "É" => "E",
            "Ê" => "E",
            "Ë" => "E",
            "&#274;" => "E",
            "&#276;" => "E",
            "&#278;" => "E",
            "&#280;" => "E",
            "&#282;" => "E",
            "&#7864;" => "E",
            "&#7866;" => "E",
            "&#7868;" => "E",
            "&#7870;" => "E",
            "&#7872;" => "E",
            "&#7874;" => "E",
            "&#7876;" => "E",
            "&#7878;" => "E",
            "è" => "e",
            "é" => "e",
            "ê" => "e",
            "ë" => "e",
            "&#275;" => "e",
            "&#277;" => "e",
            "&#279;" => "e",
            "&#281;" => "e",
            "&#283;" => "e",
            "&#7865;" => "e",
            "&#7867;" => "e",
            "&#7869;" => "e",
            "&#7871;" => "e",
            "&#7873;" => "e",
            "&#7875;" => "e",
            "&#7877;" => "e",
            "&#7879;" => "e",
            "&#284;" => "G",
            "&#286;" => "G",
            "&#288;" => "G",
            "&#290;" => "G",
            "&#285;" => "g",
            "&#287;" => "g",
            "&#289;" => "g",
            "&#291;" => "g",
            "&#292;" => "H",
            "&#294;" => "H",
            "&#293;" => "h",
            "&#295;" => "h",
            "Ì" => "I",
            "Í" => "I",
            "Î" => "I",
            "Ï" => "I",
            "&#296;" => "I",
            "&#298;" => "I",
            "&#300;" => "I",
            "&#302;" => "I",
            "&#304;" => "I",
            "&#463;" => "I",
            "&#7880;" => "I",
            "&#7882;" => "I",
            "&#308;" => "J",
            "&#309;" => "j",
            "&#310;" => "K",
            "&#311;" => "k",
            "&#313;" => "L",
            "&#315;" => "L",
            "&#317;" => "L",
            "&#319;" => "L",
            "&#321;" => "L",
            "&#314;" => "l",
            "&#316;" => "l",
            "&#318;" => "l",
            "&#320;" => "l",
            "&#322;" => "l",
            "Ñ" => "N",
            "&#323;" => "N",
            "&#325;" => "N",
            "&#327;" => "N",
            "ñ" => "n",
            "&#324;" => "n",
            "&#326;" => "n",
            "&#328;" => "n",
            "&#329;" => "n",
            "Ò" => "O",
            "Ó" => "O",
            "Ô" => "O",
            "Õ" => "O",
            "Ö" => "O",
            "Ø" => "O",
            "'" => "_",
            "&#332;" => "O",
            "&#334;" => "O",
            "&#336;" => "O",
            "&#416;" => "O",
            "&#465;" => "O",
            "&#510;" => "O",
            "&#7884;" => "O",
            "&#7886;" => "O",
            "&#7888;" => "O",
            "&#7890;" => "O",
            "&#7892;" => "O",
            "&#7894;" => "O",
            "&#7896;" => "O",
            "&#7898;" => "O",
            "&#7900;" => "O",
            "&#7902;" => "O",
            "&#7904;" => "O",
            "&#7906;" => "O",
            "ò" => "o",
            "ó" => "o",
            "ô" => "o",
            "õ" => "o",
            "ö" => "o",
            "ø" => "o",
            "&#333;" => "o",
            "&#335;" => "o",
            "&#337;" => "o",
            "&#417;" => "o",
            "&#466;" => "o",
            "&#511;" => "o",
            "&#7885;" => "o",
            "&#7887;" => "o",
            "&#7889;" => "o",
            "&#7891;" => "o",
            "&#7893;" => "o",
            "&#7895;" => "o",
            "&#7897;" => "o",
            "&#7899;" => "o",
            "&#7901;" => "o",
            "&#7903;" => "o",
            "&#7905;" => "o",
            "&#7907;" => "o",
            "ð" => "o",
            "&#340;" => "R",
            "&#342;" => "R",
            "&#344;" => "R",
            "&#341;" => "r",
            "&#343;" => "r",
            "&#345;" => "r",
            "&#346;" => "S",
            "&#348;" => "S",
            "&#350;" => "S",
            "&#347;" => "s",
            "&#349;" => "s",
            "&#351;" => "s",
            "&#354;" => "T",
            "&#356;" => "T",
            "&#358;" => "T",
            "&#355;" => "t",
            "&#357;" => "t",
            "&#359;" => "t",
            "Ù" => "U",
            "Ú" => "U",
            "Û" => "U",
            "Ü" => "U",
            "&#360;" => "U",
            "&#362;" => "U",
            "&#364;" => "U",
            "&#366;" => "U",
            "&#368;" => "U",
            "&#370;" => "U",
            "&#431;" => "U",
            "&#467;" => "U",
            "&#469;" => "U",
            "&#471;" => "U",
            "&#473;" => "U",
            "&#475;" => "U",
            "&#7908;" => "U",
            "&#7910;" => "U",
            "&#7912;" => "U",
            "&#7914;" => "U",
            "&#7916;" => "U",
            "&#7918;" => "U",
            "&#7920;" => "U",
            "ù" => "u",
            "ú" => "u",
            "û" => "u",
            "ü" => "u",
            "&#361;" => "u",
            "&#363;" => "u",
            "&#365;" => "u",
            "&#367;" => "u",
            "&#369;" => "u",
            "&#371;" => "u",
            "&#432;" => "u",
            "&#468;" => "u",
            "&#470;" => "u",
            "&#472;" => "u",
            "&#474;" => "u",
            "&#476;" => "u",
            "&#7909;" => "u",
            "&#7911;" => "u",
            "&#7913;" => "u",
            "&#7915;" => "u",
            "&#7917;" => "u",
            "&#7919;" => "u",
            "&#7921;" => "u",
            "&#372;" => "W",
            "&#7808;" => "W",
            "&#7810;" => "W",
            "&#7812;" => "W",
            "&#373;" => "w",
            "&#7809;" => "w",
            "&#7811;" => "w",
            "&#7813;" => "w",
            "Ý" => "Y",
            "&#374;" => "Y",
            "?" => "Y",
            "&#7922;" => "Y",
            "&#7928;" => "Y",
            "&#7926;" => "Y",
            "&#7924;" => "Y",
            "ý" => "y",
            "ÿ" => "y",
            "&#375;" => "y",
            "&#7929;" => "y",
            "&#7925;" => "y",
            "&#7927;" => "y",
            "&#7923;" => "y",
            "&#377;" => "Z",
            "&#379;" => "Z"
        );

        $str = strtr($str, $ch0);
        return $str;
    }
}
