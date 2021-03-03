<?php

namespace App\Models;
 
use PDO;
  
date_default_timezone_set("Africa/Abidjan");
/**
 * Example user model
 *
 * PHP version 7.0
*/
class Admin extends \Core\Model
{
    public static function getUnivInfos()
    {

        $db = static::getDB();
        //var_dump(($_SESSION['user'])['fk_iduniv']);
        $sql = ' SELECT * FROM infosuniv WHERE id_univ =' . ($_SESSION['user']['fk_iduniv']);
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result); 

        return $result;
    }
    public static function getBrowser($u_agent){
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";
        $ub= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
    
        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i',$u_agent))
        {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i',$u_agent))
        {
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif(preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";
        }
    
        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
    
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }
    
        // check if we have a number
        if ($version==null || $version=="") {$version="?";}
    
        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    }
    
    public static function get_UnivInfosBy($id_univ)
    {

        $db = static::getDB();
        //var_dump(($_SESSION['user'])['fk_iduniv']);
        $sql = ' SELECT * FROM infosuniv WHERE id_univ =' . $id_univ;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result); 

        return $result;
    }

    public static function get_all_adminid()
    {

        $db = static::getDB();

        $sql = ' SELECT id_admin_admin FROM admintab WHERE etat =1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_all_eleveid()
    {

        $db = static::getDB();

        $sql = '  SELECT id_eleve_eleve FROM eleve WHERE eleve_etat =1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_all_parentid()
    {

        $db = static::getDB();

        $sql = 'SELECT id_parent_parent FROM parent WHERE etat_parent =1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_all_profid()
    {

        $db = static::getDB();

        $sql = 'SELECT id_prof_prof FROM prof WHERE etat =1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Get all the users as an associative array
     *
     * @return array
     */



    public static function getRole()
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM roles ORDER BY id_role ASC';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getAnneeScolaireUniq()
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM annee_scolaire WHERE etat_annee = 1 ORDER by annee_libelle DESC';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getAnneeScolaireBy($id)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM annee_scolaire INNER JOIN annee_partie ON annee_scolaire.id_anscol_annee_scolaire =annee_partie.id_anneeScolaire  WHERE etat_annee = 1 AND etat_anneepart = 1 AND id_anscol_annee_scolaire = ' . $id . ' ORDER BY annee_scolaire.id_anscol_annee_scolaire ASC';
        //var_dump($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);
        //exit();
        return $result;
    }

    public static function getUniqAnneeScolaire()
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM annee_scolaire WHERE etat_annee = 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function setclasse()
    {

        $db = static::getDB();

        $departement_id = intval(htmlspecialchars($_POST["departement"]));
        $cree_classe_nom = htmlspecialchars($_POST["cree_classe_nom"]);
        $cree_classe_INFOS = htmlspecialchars($_POST["cree_classe_INFOS"]);

        $sql = 'SELECT * FROM classe WHERE libelle= "' . $cree_classe_nom . '" AND  id_departement = ' . $departement_id . '   LIMIT 1';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        //exit();
        if (empty($result) || $result == 0) {

            $data = [
                'libelle' => $cree_classe_nom,
                'infos' => $cree_classe_INFOS,
                'id_departement' => $departement_id
            ];
            //var_dump($db);
            $sql = ' INSERT INTO classe (id_departement,libelle, infos) VALUES (:id_departement, :libelle, :infos);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                /*
                $classmat_tab =NULL;
                foreach ($_POST as $key => $value) {
                        $classmat_tab = explode("_",$key);
                        if ($classmat_tab[0]==="classmat") {
                            $data = [
                                'id_classe' => $lastid,
                                'id_matiere' => $classmat_tab[1]
                            ];
                            $sql=' INSERT INTO classe_matiere (id_classe, id_matiere) VALUES ( :id_classe, :id_matiere);';
                            $stmt= $db->prepare($sql);
                            $result = $stmt->execute($data);
        
                        }
                   
                }
                */
                return 1;
            } else {

                return -1;
            }
        } else {

            return 0;
        }
    }

    //::::MENU GESTION DES CLASSES::::::::::
    public static function set_update_adminUnivBy($id_univ, $id_admin)
    {

        $db = static::getDB();

        $sql_admin = 'SELECT id_pers_personne FROM personne WHERE   type_pers = 4 AND etat_pers = 1 AND id_type =' . $id_admin . ' LIMIT 1';
        $stmt_admin = $db->query($sql_admin);
        $result_admin = $stmt_admin->fetchAll(PDO::FETCH_ASSOC);

        $sql = 'SELECT id_univ FROM infosuniv WHERE id_univ = ' . $id_univ . ' LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //var_dump($sql,$stmt,$result);exit();
        if (empty($result) || $result == 0 || empty($result_admin)  || $result_admin == 0) {
            return 0;
        } else {
            $data = [
                'fk_iduniv' => $id_univ,
                'id_pers_personne' => $id_admin
            ];

            $sql = 'UPDATE personne SET fk_iduniv =:fk_iduniv    WHERE id_pers_personne = :id_pers_personne;';

            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);

            if ($result == TRUE) {
                return 1;
            } else {
                return 0;
            }
        }
    }


    public static function setDeleteclasse($id_classe)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM classe WHERE id_classe_classe= "' . $id_classe . '"  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        //exit();
        if (empty($result) || $result == 0) {
        } else {

            $sql = 'DELETE FROM classe_matiere WHERE id_classe = ' . $id_classe;
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();


            $sql = 'DELETE FROM classe WHERE id_classe_classe = ' . $id_classe;
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
        }

        return  $result;
    }

    public static function setUpdateclasse()
    {

        $db = static::getDB();

        $id_classe = intval(htmlspecialchars($_POST["select_modfi_classe"]));

        $sql = ' SELECT * FROM classe WHERE id_classe_classe= "' . $id_classe . '"  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        if (empty($result) || $result == 0) {
            return 'classeUpdate_echec';
        } else {

            $sql = 'DELETE * FROM classe_matiere WHERE id_classe = ' . $id_classe;
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();

            $classmat_tab = NULL;
            foreach ($_POST as $key => $value) {
                //if ( preg_match("#classmat#", $key) ){ }
                $classmat_tab = explode("_", $key);
                if ($classmat_tab[0] === "classmat") {

                    //var_dump($key,$classmat_tab[1],$classmat_tab[2]);

                    $data = [
                        'id_classe' => $id_classe,
                        'id_matiere' => $classmat_tab[1]
                    ];
                    $sql = ' INSERT INTO classe_matiere (id_classe, id_matiere) VALUES ( :id_classe, :id_matiere);';
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute($data);
                }
            }

            return 'classeUpdate_ok';
        }
    }

    public static function set_filiereNivo_mat($id_nivo, $id_filiere)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM classe WHERE id_classe_classe= ' . $id_filiere . '  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql2 = ' SELECT * FROM niveau WHERE id_niveau= ' . $id_nivo . '  LIMIT 1';
        $stmt2 = $db->query($sql2);
        $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        if (empty($result) || $result == 0 || empty($result2) || $result2 == 0) {
            return 0;
        } else {

            //id_classe_matiere	id_classe	id_matiere	id_niveau	etat

            $sql = 'DELETE FROM classe_matiere WHERE id_classe = ' . $id_filiere . ' AND id_niveau = ' . $id_nivo;
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();

            $classmat_tab = NULL;
            foreach ($_POST as $key => $value) {
                //if ( preg_match("#classmat#", $key) ){ }
                $classmat_tab = explode("_", $key);
                if ($classmat_tab[0] === "classmat") {

                    //var_dump($key,$classmat_tab[1],$classmat_tab[2]);

                    $data = [
                        'id_classe' => $id_filiere,
                        'id_niveau' => $id_nivo,
                        'id_matiere' => $classmat_tab[1]
                    ];
                    $sql = ' INSERT INTO classe_matiere (id_classe,id_niveau, id_matiere) VALUES ( :id_classe,:id_niveau, :id_matiere);';
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute($data);
                }
            }

            return 1;
        }
    }



    public static function getClasses()
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        $sql = ' SELECT * FROM classe ,departement WHERE classe.id_departement = departement.id_depat AND classe.etat = 1 ORDER BY classe.libelle ASC';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_infosClassesBy($id_filiere)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        $sql = 'SELECT * FROM classe WHERE id_classe_classe = ' . $id_filiere;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function getClassesBy($id_univ)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        $sql = ' SELECT * FROM classe ,departement WHERE classe.id_departement = departement.id_depat AND classe.etat = 1 AND departement.fk_iduniv = ' . $id_univ . ' ORDER BY classe.libelle ASC';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getProf($id_univ)
    {
        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = '  SELECT * FROM (SELECT id_pers_personne,nom_prenom,email,sexe,id_type,contact FROM personne WHERE etat_pers=1 AND type_pers=2 AND fk_iduniv='.$id_univ.') pers INNER JOIN (SELECT id_prof_prof FROM prof WHERE etat =1) tprof ON pers.id_type = tprof.id_prof_prof  ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function getAdmin($id_univ)
    {
        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = '  SELECT * FROM (SELECT id_pers_personne,nom_prenom,email,sexe,id_type,contact FROM personne WHERE etat_pers=1 AND type_pers=4 AND fk_iduniv='.$id_univ.') pers INNER JOIN (SELECT * FROM admintab WHERE etat =1) tpadmin ON pers.id_type = tpadmin.id_admin_admin  ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function getProf_Byuniv($id_univ)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = '  SELECT * FROM (SELECT id_pers_personne,nom_prenom,email,sexe,id_type,contact FROM personne WHERE etat_pers=1 AND type_pers=2 AND fk_iduniv=' . $id_univ . ') pers INNER JOIN (SELECT id_prof_prof FROM prof WHERE etat =1) tprof ON pers.id_type = tprof.id_prof_prof  ';
  
  
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    /* * 
    *TABLE  : parent_enfants
    *	id_parent 	id_enfant 	etat_parent_enfant 	 
    */
    public static function set_parent_enfant($id_parent, $id_enfant)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM parent_enfants WHERE id_parent= "' . $id_parent . '" AND id_enfant= "' . $id_enfant . '" LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        if (empty($result) || $result == 0) {

            $data = [
                'id_parent' => $id_parent,
                'id_enfant' => $id_enfant
            ];

            $sql = ' INSERT INTO parent_enfants (id_parent, id_enfant) VALUES ( :id_parent, :id_enfant);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                return 1;
            } else {
                return -1;
            }
        } else {
            return 0;
        }
    }

    public static function get_parent_enfant($id_parent)
    {

        $db = static::getDB();

        $sql = 'SELECT * FROM parent_enfants WHERE id_parent =' . $id_parent;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_enfant_idParent($id_enfant)
    {

        $db = static::getDB();

        $sql = 'SELECT id_parent FROM parent_enfants WHERE  etat_parent_enfant > 0 AND id_enfant=' . $id_enfant;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function set_Update_parent_enfant($id_parent, $id_enfant, $etat_pe)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM parent_enfants WHERE id_parent= "' . $id_parent . '" AND id_enfant= "' . $id_enfant . '" LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        if (empty($result) || $result == 0) {
            Admin::set_parent_enfant($id_parent, $id_enfant);
            Admin::set_Update_parent_enfant($id_parent, $id_enfant, $etat_pe);
        } else {

            $data = [
                'etat_parent_enfant' => $etat_pe,
                'id_parent' => $id_parent,
                'id_enfant' => $id_enfant,
            ];
            //var_dump($db);
            $sql = 'UPDATE parent_enfants SET etat_parent_enfant =:etat_parent_enfant  WHERE id_parent = :id_parent AND id_enfant = :id_enfant;';

            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
        }
    }



    public static function get_elevBymatricule($matricule)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT * FROM (SELECT pers.id_pers_personne,pers.nom_prenom,pers.date_naiss,pers.lieu_naiss,pers.sexe,pers.email,pers.contact,elev.matricule,elev.id_eleve_eleve FROM (SELECT * FROM personne WHERE type_pers = 1)pers 
        INNER JOIN (SELECT * FROM eleve WHERE  matricule ="'.$matricule.'")elev 
        ON pers.id_type = elev.id_eleve_eleve) tmp_all_elv;';
        //var_dump($login,$pass,$sql);
        //print_r($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function set_MajEtat_persBy($id_personne, $etat_pers)
    {
        $db = static::getDB();

        $sql = ' SELECT * FROM personne WHERE id_pers_personne= "' . $id_personne . '"  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        if (empty($result) || $result == 0) {
            return 0;
        } else {

            $data = [
                'etat_pers' => $etat_pers,
                'id_pers_personne' => $id_personne,
            ];
            //var_dump($db);
            $sql = 'UPDATE personne SET etat_pers =:etat_pers WHERE id_pers_personne = :id_pers_personne ;';

            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            return 1;
        }
    }

    /* * 
    *TABLE  : parent
    *	id_parent_parent 	matricule 	anneeScolaire 	etat_parent 
    */
    public static function get_AllParents()
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        $sql = ' SELECT * FROM parent INNER JOIN (SELECT id_type, nom_prenom, date_naiss, lieu_naiss, sexe, email, contact FROM personne WHERE type_pers = 3 AND etat_pers = 1)tmp_per ON parent.id_parent_parent = tmp_per.id_type ';

        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_AllParentsBy($id_parent)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM parent INNER JOIN (SELECT id_pers_personne, id_type, nom_prenom, date_naiss, lieu_naiss, sexe, email, contact FROM personne WHERE type_pers = 3 AND etat_pers = 1)tmp_per ON parent.id_parent_parent = tmp_per.id_type WHERE parent.id_parent_parent = ' . $id_parent;

        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function get_Allenfant_parentBy($id_eleve)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM parent_enfants  INNER JOIN  (SELECT * FROM personne WHERE type_pers = 3)tmp_per 
            ON parent_enfants.id_parent = tmp_per.id_type   WHERE parent_enfants.id_enfant =' . $id_eleve;

        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    //::::: SQL :::::: INFOS ELEVES
    public static function getinfos_AllElev()
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = '  SELECT pers.id_pers_personne,pers.nom_prenom,pers.date_naiss,pers.lieu_naiss,pers.sexe,pers.email,pers.contact,elev.matricule,elev.id_eleve_eleve FROM (SELECT * FROM personne WHERE  type_pers = 1)pers INNER JOIN (SELECT * FROM eleve ) elev  ON pers.id_type = elev.id_eleve_eleve ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function getAllElev()
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = '  SELECT pers.id_pers_personne,pers.nom_prenom,pers.date_naiss,pers.lieu_naiss,pers.sexe,pers.email,pers.contact,elev.matricule,elev.id_eleve_eleve FROM (SELECT * FROM personne WHERE etat_pers = 1 AND type_pers = 1)pers INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1) elev  ON pers.id_type = elev.id_eleve_eleve ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_AllUniv_Elev($id_univ)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT * FROM (SELECT * FROM personne WHERE etat_pers = 1 AND type_pers = 1)pers INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1) elev  ON pers.id_type = elev.id_eleve_eleve WHERE pers.fk_iduniv=' . $id_univ;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_univElev_grpBy($id_eleve)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT * FROM
        (SELECT * FROM eleve_estds_groupe,groupe WHERE eleve_estds_groupe.elv_ds_grpe_idelev=' . $id_eleve . ' AND eleve_estds_groupe.elv_ds_grpe_etat=1 AND eleve_estds_groupe.elv_ds_grpe_groupe=groupe.groupe_id)tmpgpe, niveau,classe WHERE tmpgpe.fk_idniveau=niveau.id_niveau AND tmpgpe.groupe_classe=classe.id_classe_classe';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
          
    public static function get_elevBy($id_eleve)
    {

        $db = static::getDB();

        $sql = 'SELECT * FROM 
            (SELECT * FROM 
                (SELECT personne.id_pers_personne,personne.nom_prenom,personne.date_naiss,personne.lieu_naiss,personne.sexe,personne.email,personne.contact ,personne.id_type FROM personne WHERE etat_pers = 1 AND personne.id_type='.$id_eleve.' AND type_pers = 1)pers 
                INNER JOIN (SELECT * FROM eleve WHERE eleve.eleve_etat = 1 AND eleve.id_eleve_eleve= '.$id_eleve.') elev  
                ON pers.id_type = elev.id_eleve_eleve) tmp_fina_ok_elev 
                WHERE tmp_fina_ok_elev.id_eleve_eleve =' . $id_eleve;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getAllElevByGrp($id_grp)
    {

        $id_grp = intval($id_grp);
        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = ' SELECT * FROM (SELECT pers.id_pers_personne,pers.nom_prenom,pers.date_naiss,pers.lieu_naiss,pers.sexe,pers.email,pers.contact,elev.statut_affecter,elev.matricule,elev.id_eleve_eleve FROM (SELECT * FROM personne WHERE etat_pers = 1 AND type_pers = 1)pers INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1) elev  ON pers.id_type = elev.id_eleve_eleve)infoelev INNER JOIN (SELECT * FROM eleve_estds_groupe WHERE elv_ds_grpe_groupe = ' . $id_grp . ')elevgrpbyid ON infoelev.id_eleve_eleve = elevgrpbyid.elv_ds_grpe_idelev ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function get_allelev_grp($id_elev)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT * FROM eleve_estds_groupe,groupe WHERE eleve_estds_groupe.elv_ds_grpe_idelev ='.$id_elev.' AND eleve_estds_groupe.elv_ds_grpe_etat = 1 AND groupe.groupe_id = eleve_estds_groupe.elv_ds_grpe_groupe ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_elev_allgrp_Byannee($id_elev,$id_anne)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT * FROM eleve_estds_groupe,groupe WHERE eleve_estds_groupe.elv_ds_grpe_groupe=groupe.groupe_id  AND groupe.groupe_annee='.$id_anne.'  AND eleve_estds_groupe.elv_ds_grpe_idelev='.$id_elev.' ORDER BY eleve_estds_groupe.eleve_estds_groupe_dateajout  DESC ';
        //var_dump($login,$pass,$sql);
        //exit();
        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }    
    public static function getAllElevNotGrp($id_annee,$id_groupe)
    {
        $id_annee = intval($id_annee);
        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        
        //$sql = ' SELECT * FROM(SELECT pers.id_pers_personne,pers.nom_prenom,pers.sexe,pers.email,pers.contact,elev.matricule,elev.id_eleve_eleve FROM (SELECT * FROM personne WHERE etat_pers = 1 AND type_pers = 1)pers INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1) elev  ON pers.id_type = elev.id_eleve_eleve)tbelev WHERE tbelev.id_eleve_eleve NOT IN (SELECT eleve_estds_groupe.elv_ds_grpe_idelev as id_eleve_eleve FROM (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_annee = ' . $id_annee . ')grpanee INNER JOIN eleve_estds_groupe ON grpanee.groupe_id = eleve_estds_groupe.elv_ds_grpe_groupe) ';
        //var_dump($login,$pass,$sql);
        //exit();
        $sql='SELECT * FROM 
        (SELECT * FROM
        (SELECT pers.id_pers_personne,pers.nom_prenom,pers.sexe,pers.email,pers.contact,elev.matricule,elev.id_eleve_eleve,elev.parcours,elev.niveauetude FROM 
                (SELECT * FROM personne WHERE etat_pers = 1 AND type_pers = 1)pers 
                INNER JOIN (SELECT * FROM eleve WHERE eleve_etat = 1) elev  
                ON pers.id_type = elev.id_eleve_eleve
        )tbelev 
        WHERE tbelev.id_eleve_eleve 
        NOT IN 
        (SELECT eleve_estds_groupe.elv_ds_grpe_idelev as id_eleve_eleve 
            FROM (SELECT groupe_id,groupe_libelle FROM groupe WHERE groupe_annee = '.$id_annee.')grpanee 
            INNER JOIN  eleve_estds_groupe 
            ON grpanee.groupe_id = eleve_estds_groupe.elv_ds_grpe_groupe
            )
        )tmp_elenongrp_ok
        INNER JOIN

        (SELECT groupe.*,niveau.libelle_niveau,classe.libelle AS classe_lib FROM groupe,niveau,classe WHERE groupe.groupe_id='.$id_groupe.' AND groupe.fk_idniveau=niveau.id_niveau AND groupe.groupe_classe = classe.id_classe_classe)tmphhgp

        ON tmp_elenongrp_ok.niveauetude = tmphhgp.libelle_niveau
        WHERE tmp_elenongrp_ok.parcours = tmphhgp.classe_lib';

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public static function setMatiere($fk_iduniv)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $cree_matiere_code = htmlspecialchars($_POST["cree_matiere_code"]);
        $cree_matiere_titre = htmlspecialchars($_POST["cree_matiere_titre"]);
        $cree_matiere_infos = htmlspecialchars($_POST["cree_matiere_infos"]);
        //unset($_POST);
        

        //$sql = ' SELECT * FROM matiere WHERE code= "' . $cree_matiere_code . '" AND libele = "' . $cree_matiere_titre . '" AND fk_iduniv =' . $fk_iduniv . ' LIMIT 1';
        $sql = ' SELECT * FROM matiere WHERE code= REPLACE("'.$cree_matiere_code.'", " ", "_") AND libele = "' . $cree_matiere_titre . '" AND fk_iduniv =' . $fk_iduniv . ' LIMIT 1';
        
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        //exit();
        if (empty($result) || $result == 0) {

            $data = [
                'code' =>  str_replace(" ","_",$cree_matiere_code),
                'description' => $cree_matiere_infos,
                'libele' => $cree_matiere_titre,
                'fk_iduniv' => $fk_iduniv
            ];
            //var_dump($db);
            $sql = ' INSERT INTO matiere (code, description, libele, fk_iduniv) VALUES ( :code, :description, :libele, :fk_iduniv);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                return 1;
            } else {

                return -1;
            }
        } else {

            return 0;
        }
    }
    public static function get_all_absence_elev($id_eleve,$id_group)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT *FROM
        (SELECT * FROM(SELECT * FROM(SELECT * FROM(SELECT * FROM(SELECT emploitps_id,emploitps_date,emploitps_h_debut,emploitps_h_fin,emploitps_id_prof, emploitps_anneescolaire,emploitps_periode,emploitps_groupe_id,mat_libelle,Code_salle,salle_libelle FROM(SELECT emploitps_id,emploitps_date,emploitps_salle,emploitps_h_debut,emploitps_h_fin,emploitps_id_prof, emploitps_anneescolaire,emploitps_periode,emploitps_groupe_id,mat_libelle FROM(SELECT * FROM groupe_emploitps WHERE emploitps_etat = 1 AND emploitps_groupe_id='.$id_group.')tmp_grp_prog INNER JOIN (SELECT id_matiere_matiere,libele AS mat_libelle FROM matiere)tmp_mat ON tmp_grp_prog.emploitps_id_mat =  tmp_mat.id_matiere_matiere)tmp_prog_mat INNER JOIN (SELECT id_salle,Code_salle,libelle AS salle_libelle FROM salle)tmp_salle ON tmp_prog_mat.emploitps_salle = tmp_salle.id_salle)tmp_p_s_m INNER JOIN (SELECT id_type, nom_prenom FROM personne WHERE type_pers = 2)tmp_prof ON tmp_p_s_m.emploitps_id_prof = tmp_prof.id_type) tmp_p_s_m_pr INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe)tmp_grp ON tmp_p_s_m_pr.emploitps_groupe_id = tmp_grp.groupe_id)tmp_f INNER JOIN (SELECT id_annee_partie,libele_partie FROM annee_partie)tmp_per ON tmp_f.emploitps_periode = tmp_per.id_annee_partie)tmp_f_all WHERE emploitps_groupe_id='.$id_group.')tmp_ok_prog
        INNER JOIN
        absence_eleve
        ON tmp_ok_prog.emploitps_id=absence_eleve.fk_emploitps AND absence_eleve.fk_id_eleve='.$id_eleve;
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {return  $result;} 
        else {  return 0; }
    }
    
    

    /** TABLE :!: Niveau
     * id_niveau	libelle_niveau	desc_niveau
    */
    public static function setNiveau($lib_niveau, $desc_niveau, $eschool_univ)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();
        //unset($_POST);
        $sql = ' SELECT * FROM niveau WHERE libelle_niveau= "' . $lib_niveau . '" AND  fk_id_univ=' . $eschool_univ . ' LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        //exit();
        if (empty($result) || $result == 0) {

            $data = [
                'libelle_niveau' => $lib_niveau,
                'desc_niveau' => $desc_niveau,
                'fk_id_univ' => $eschool_univ
            ];
            //var_dump($db);
            $sql = ' INSERT INTO niveau (libelle_niveau, desc_niveau,fk_id_univ) VALUES ( :libelle_niveau, :desc_niveau, :fk_id_univ);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                return 1;
            } else {
                return -1;
            }
        } else {

            return 0;
        }
    }
    public static function setDeletegrpMatCoef($groupe_matiere_coef_id)
    {
 
        $db = static::getDB();
        //groupe_matiere_coef_id 	matiere_id_tmp 	coeficient_tmp 	matiere_parent_id_tmp 	groupe_id_tmp 	part_annee_id_tmp 
        $sql = ' SELECT * FROM groupe_matiere_coef WHERE groupe_matiere_coef_id= "' . $groupe_matiere_coef_id . '"  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();

        if (empty($result) || $result == 0) {
            return "deletegrpmat_echec";
        } else {
            $sql = 'DELETE FROM groupe_matiere_coef WHERE groupe_matiere_coef_id = ' . $groupe_matiere_coef_id;
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            return "deletegrpmat_ok";
        }
    }
    public static function setDelete_filMatCoef($filiere_niveau_matCoef_id)
    {

        $db = static::getDB();
        //groupe_matiere_coef_id 	matiere_id_tmp 	coeficient_tmp 	matiere_parent_id_tmp 	groupe_id_tmp 	part_annee_id_tmp 
        $sql = ' SELECT * FROM filiere_niveau_matCoef WHERE filiere_niveau_matCoef_id= ' . $filiere_niveau_matCoef_id . '  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);//exit();

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            $sql = 'DELETE FROM filiere_niveau_matCoef WHERE filiere_niveau_matCoef_id = ' . $filiere_niveau_matCoef_id;
            //var_dump($sql);
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            return 1;
        }
    }
    public static function getGrpMatiereNonRepartiByPartie($groupeid, $classeid)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat
        //var_dump($groupeid, $classeid);
        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = ' SELECT *  FROM(SELECT * FROM (SELECT libele_partie,groupe_matiere_coef_id,matiere_id_tmp, coeficient_tmp,credit_tmp, matiere_parent_id_tmp FROM(SELECT * FROM (SELECT id_annee_partie,libele_partie FROM annee_partie WHERE etat_anneepart=1)tmp_partann INNER JOIN (SELECT * FROM groupe_matiere_coef WHERE groupe_id_tmp = ' . $groupeid . ')tmp_grpmat ON tmp_partann.id_annee_partie = tmp_grpmat.part_annee_id_tmp)tmp_grpmat_partann)tmp_p_gpmat INNER JOIN (SELECT libele,id_matiere FROM (SELECT * FROM (SELECT id_matiere_matiere,libele FROM matiere WHERE etat=1)mat INNER JOIN (SELECT id_matiere,id_classe FROM classe_matiere WHERE etat =1 AND id_classe=' . $classeid . ')filliermat ON mat.id_matiere_matiere = filliermat.id_matiere)classmat INNER JOIN (SELECT groupe_classe FROM groupe WHERE groupe_id=' . $groupeid . ' AND groupe_etat = 1)grpfilliere ON classmat.id_classe = grpfilliere.groupe_classe )tmp_gpmat_a ON tmp_gpmat_a.id_matiere =tmp_p_gpmat.matiere_id_tmp )tmp_01_a INNER JOIN (SELECT libele as parent_libelle,id_matiere_matiere FROM matiere WHERE etat =1)tmp_01_b ON tmp_01_a.matiere_parent_id_tmp =tmp_01_b.id_matiere_matiere';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }



    public static function getMatiereSansPartie_SansCoef($groupeid, $classeid)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat
        //var_dump($groupeid, $classeid);
        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = ' SELECT * FROM(SELECT id_classe,id_matiere,code,libelle as lib_classe,libele as lib_mat FROM (SELECT id_classe_matiere,id_classe,id_matiere,classejoin.etat,classen.libelle FROM (SELECT * FROM classe_matiere WHERE id_classe =' . $classeid . ' AND etat = 1) classejoin  INNER JOIN (SELECT * FROM classe WHERE etat = 1) classen ON classejoin.id_classe = classen.id_classe_classe) nclasse INNER JOIN (SELECT id_matiere_matiere ,code ,libele FROM matiere WHERE etat = 1)nmatiere ON nclasse.id_matiere = nmatiere.id_matiere_matiere)tmp_tab_allmat WHERE tmp_tab_allmat.id_matiere NOT IN (SELECT id_matiere FROM(SELECT *  FROM(SELECT * FROM (SELECT libele_partie,groupe_matiere_coef_id,matiere_id_tmp, coeficient_tmp, matiere_parent_id_tmp FROM(SELECT * FROM (SELECT id_annee_partie,libele_partie FROM annee_partie WHERE etat_anneepart=1)tmp_partann INNER JOIN (SELECT * FROM groupe_matiere_coef WHERE groupe_id_tmp = ' . $groupeid . ')tmp_grpmat ON tmp_partann.id_annee_partie = tmp_grpmat.part_annee_id_tmp)tmp_grpmat_partann)tmp_p_gpmat INNER JOIN (SELECT libele,id_matiere FROM (SELECT * FROM (SELECT id_matiere_matiere,libele FROM matiere WHERE etat=1)mat INNER JOIN (SELECT id_matiere,id_classe FROM classe_matiere WHERE etat =1 AND id_classe=' . $classeid . ')filliermat ON mat.id_matiere_matiere = filliermat.id_matiere)classmat INNER JOIN (SELECT groupe_classe FROM groupe WHERE groupe_id=' . $groupeid . ' AND groupe_etat = 1)grpfilliere ON classmat.id_classe = grpfilliere.groupe_classe )tmp_gpmat_a ON tmp_gpmat_a.id_matiere =tmp_p_gpmat.matiere_id_tmp )tmp_01_a INNER JOIN (SELECT libele as parent_libelle,id_matiere_matiere FROM matiere WHERE etat =1)tmp_01_b ON tmp_01_a.matiere_parent_id_tmp =tmp_01_b.id_matiere_matiere) tmp_00_finaltab)';
        //var_dump($login,$pass,$sql);
        //exit();

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getSousmatiereBy($groupeid)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT * FROM (SELECT * FROM (SELECT id_matiere_matiere,code,libele FROM matiere WHERE etat=1)mat INNER JOIN (SELECT * FROM groupe_matiere_coef  WHERE  groupe_id_tmp=' . $groupeid . ')grpmat ON mat.id_matiere_matiere =grpmat.matiere_id_tmp)matres INNER JOIN (SELECT id_matiere_matiere,code,libele as matparent FROM matiere WHERE etat=1)matold ON matres.matiere_parent_id_tmp = matold.id_matiere_matiere';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getMatiereBy($groupeid)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = ' SELECT * FROM (SELECT id_matiere_matiere,code,libele FROM matiere WHERE etat=1)mat INNER JOIN (SELECT * FROM groupe_matiere_coef  WHERE matiere_parent_id_tmp=0 AND groupe_id_tmp=' . $groupeid . ')grpmat ON mat.id_matiere_matiere =grpmat.matiere_id_tmp';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getMatiere()
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        $sql = ' SELECT * FROM matiere WHERE etat = 1 ORDER BY code ASC';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_AllMatiereBy($fk_iduniv)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        $sql = ' SELECT * FROM matiere WHERE etat = 1 AND fk_iduniv = ' . $fk_iduniv . ' ORDER BY code ASC';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getNiveau()
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM niveau ORDER BY libelle_niveau ASC';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_infosNiveauBy($id_niveau, $fk_id_univ)
    {

        $db = static::getDB();

        $sql = 'SELECT * FROM niveau WHERE id_niveau =' . $id_niveau . ' AND fk_id_univ = ' . $fk_id_univ;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public static function getNiveauBy($fk_id_univ)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM niveau WHERE fk_id_univ =' . $fk_id_univ . ' ORDER BY libelle_niveau ASC';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function getBilanBy()
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM bulettin_bilan ORDER BY libelle_bilan ASC';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function getClasseMatiereBy($id_classe)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        $sql = 'SELECT id_classe,id_matiere,code,libelle,libele FROM (SELECT id_classe_matiere,id_classe,id_matiere,classejoin.etat,classen.libelle FROM (SELECT * FROM classe_matiere WHERE id_classe =' . $id_classe . ' AND etat = 1) classejoin  INNER JOIN (SELECT * FROM classe WHERE etat = 1) classen ON classejoin.id_classe = classen.id_classe_classe) nclasse INNER JOIN (SELECT id_matiere_matiere ,code ,libele FROM matiere WHERE etat = 1)nmatiere ON nclasse.id_matiere = nmatiere.id_matiere_matiere';
        //var_dump($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function get_mat_nivoFilBy($idnivo, $id_filiere, $id_univ)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        $sql = 'SELECT classe_matiere.*,classe.libelle,niveau.libelle_niveau,matiere.code AS code_mat , matiere.libele as lib_mat FROM classe_matiere,classe,niveau,matiere WHERE matiere.fk_iduniv =' . $id_univ . ' AND classe_matiere.id_classe=classe.id_classe_classe AND classe_matiere.id_niveau =niveau.id_niveau AND classe_matiere.id_matiere=matiere.id_matiere_matiere AND classe_matiere.etat = 1 AND classe_matiere.id_classe=' . $id_filiere . '  AND classe_matiere.id_niveau =' . $idnivo;
        //var_dump($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function get_filiereNivo_MatiereBy($id_filiere, $id_nivo)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        $sql = 'SELECT id_classe,id_matiere,id_niveau ,code,libelle,libele FROM (SELECT id_classe_matiere,id_classe,id_matiere,id_niveau ,classejoin.etat,classen.libelle FROM (SELECT * FROM classe_matiere WHERE id_niveau =' . $id_nivo . ' AND id_classe =' . $id_filiere . ' AND etat = 1) classejoin INNER JOIN (SELECT * FROM classe WHERE etat = 1) classen ON classejoin.id_classe = classen.id_classe_classe) nclasse INNER JOIN (SELECT id_matiere_matiere ,code ,libele FROM matiere WHERE etat = 1)nmatiere ON nclasse.id_matiere = nmatiere.id_matiere_matiere';
        //var_dump($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_gpre_inClass_MatBy($groupeid)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = ' SELECT id_classe,id_matiere,code_mat,lib_mat FROM( SELECT * FROM(SELECT id_classe,id_matiere FROM classe_matiere WHERE etat = 1)tmp_class_mat INNER JOIN (SELECT id_matiere_matiere,code AS code_mat,libele AS lib_mat FROM matiere WHERE etat = 1)tmp_mat ON tmp_class_mat.id_matiere= tmp_mat.id_matiere_matiere)tmp_f WHERE tmp_f.id_classe IN (SELECT groupe_classe FROM groupe WHERE groupe_id = ' . $groupeid . ' AND groupe_etat = 1)';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_gpreALLMat_By($groupeid)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT groupe_matiere_coef.groupe_id_tmp AS id_classe ,groupe_matiere_coef.matiere_id_tmp AS id_matiere,matiere.id_matiere_matiere,matiere.libele AS lib_mat, matiere.code AS code_mat FROM groupe_matiere_coef,matiere WHERE  groupe_matiere_coef.groupe_id_tmp='.$groupeid.' AND groupe_matiere_coef.matiere_id_tmp = matiere.id_matiere_matiere GROUP BY lib_mat  
        ORDER BY `groupe_matiere_coef`.`groupe_id_tmp` ASC';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function get_mat_By_gpeperiode($groupeid,$periode)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT groupe_matiere_coef.part_annee_id_tmp, groupe_matiere_coef.groupe_id_tmp AS id_classe ,groupe_matiere_coef.matiere_id_tmp AS id_matiere,matiere.id_matiere_matiere,matiere.libele AS lib_mat, matiere.code AS code_mat FROM groupe_matiere_coef,matiere WHERE groupe_matiere_coef.part_annee_id_tmp ='.$periode.' AND groupe_matiere_coef.groupe_id_tmp='.$groupeid.' AND groupe_matiere_coef.matiere_id_tmp = matiere.id_matiere_matiere GROUP BY lib_mat  
        ORDER BY `groupe_matiere_coef`.`groupe_id_tmp` ASC';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    //::::MENU GESTION DES CLASSES::::::::::
    public static function setEmploiTps()
    {
        $db = static::getDB();
        //var_dump($_POST);exit();
        $emploitps_jour = intval(htmlspecialchars($_POST["emploitps_jour"]));
        $emploitps_id_mat = intval(htmlspecialchars($_POST["emploitps_id_mat"]));
        $emploitps_id_prof = intval(htmlspecialchars($_POST["emploitps_id_prof"]));
        $emploitps_h_debut = intval(htmlspecialchars($_POST["emploitps_h_debut"]));
        $emploitps_h_fin = intval(htmlspecialchars($_POST["emploitps_h_fin"]));
        $emploitps_groupe_id = intval(htmlspecialchars($_POST["emploitps_groupe_id"]));

        // emploitps_id 	emploitps_jour 	emploitps_id_mat 	emploitps_id_prof 	emploitps_h_debut 	emploitps_h_fin 	emploitps_groupe_id 	emploitps_etat 

        $sql = ' SELECT * FROM groupe_emploitps WHERE emploitps_jour= "' . $emploitps_jour . '" AND emploitps_id_mat= "' . $emploitps_id_mat . '" AND emploitps_id_prof= "' . $emploitps_id_prof . '" AND emploitps_h_debut= "' . $emploitps_h_debut . '"  AND emploitps_h_fin= "' . $emploitps_h_fin . '" AND emploitps_groupe_id= "' . $emploitps_groupe_id . '" LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();

        if (empty($result) || $result == 0) {

            $data = [
                'emploitps_jour' => $emploitps_jour,
                'emploitps_id_mat' => $emploitps_id_mat,
                'emploitps_id_prof' => $emploitps_id_prof,
                'emploitps_h_debut' => $emploitps_h_debut,
                'emploitps_h_fin' => $emploitps_h_fin,
                'emploitps_groupe_id' => $emploitps_groupe_id
            ];
            //var_dump($db);
            $sql = ' INSERT INTO groupe_emploitps (emploitps_jour, emploitps_id_mat, emploitps_id_prof, emploitps_h_debut, emploitps_h_fin, emploitps_groupe_id) VALUES ( :emploitps_jour, :emploitps_id_mat, :emploitps_id_prof, :emploitps_h_debut, :emploitps_h_fin, :emploitps_groupe_id);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            //$lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                return 1;
            } else {

                return 0;
            }
        } else {

            return 0;
        }
    }
    public static function setEmploiTpsByPost($fk_iduniv)
    {
        $db = static::getDB();
        //var_dump($_POST);exit();
        $anneescolaire = intval(htmlspecialchars($_POST["attribution_emlploiTps_anneescolaire"]));
        $periode = intval(htmlspecialchars($_POST["attribution_emlploiTps_periode"]));
        $groupe = intval(htmlspecialchars($_POST["attribution_emlploiTps_groupe"]));
        $date = (htmlspecialchars($_POST["attribution_emlploiTps_date"]));
        $mat = intval(htmlspecialchars($_POST["attribution_emlploiTps_mat"]));
        $salle = intval(htmlspecialchars($_POST["attribution_emlploiTps_salle"]));
        $heuredebut = (htmlspecialchars($_POST["attribution_emlploiTps_heuredebut"]));
        $heurefin = (htmlspecialchars($_POST["attribution_emlploiTps_heurefin"]));
        $enseignant = intval(htmlspecialchars($_POST["attribution_emlploiTps_enseignant"]));

        //emploitps_id 	emploitps_date 	emploitps_id_mat 	emploitps_salle 	emploitps_h_debut 	emploitps_h_fin 	emploitps_id_prof 	emploitps_etat 	emploitps_anneescolaire 	emploitps_periode 	emploitps_groupe_id
        $sql = 'SELECT * FROM groupe_emploitps WHERE emploitps_date= "' . $date . '" AND emploitps_h_debut= "' . $heuredebut . '" AND emploitps_h_fin= "' . $heurefin . '" AND fk_iduniv= "' . $fk_iduniv . '" LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();

        if (empty($result) || $result == 0) {

            $data = [
                'emploitps_date' => $date,
                'emploitps_id_mat' => $mat,
                'emploitps_salle' => $salle,
                'emploitps_h_debut' => $heuredebut,
                'emploitps_h_fin' => $heurefin,
                'emploitps_id_prof' => $enseignant,
                'emploitps_anneescolaire' => $anneescolaire,
                'emploitps_periode' => $periode,
                'emploitps_groupe_id' => $groupe
            ];
            //var_dump($db);
            $sql = ' INSERT INTO groupe_emploitps (emploitps_date, emploitps_id_mat, emploitps_salle, emploitps_h_debut, emploitps_h_fin, emploitps_id_prof , emploitps_anneescolaire, emploitps_periode, emploitps_groupe_id) VALUES ( :emploitps_date, :emploitps_id_mat, :emploitps_salle, :emploitps_h_debut, :emploitps_h_fin, :emploitps_id_prof , :emploitps_anneescolaire, :emploitps_periode, :emploitps_groupe_id);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            //$lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                return 'creer';
            } else {

                return 'erreur';
            }
        } else {

            return 'existe';
        }
    }
    public static function setGrpToEleve($groupe, $idelev)
    {
        $db = static::getDB();
        //var_dump($_POST);exit();
        //eleve_estds_groupe_id	elv_ds_grpe_idelev	elv_ds_grpe_groupe	elv_ds_grpe_etat
        $sql = ' SELECT * FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev="' . $idelev . '" AND elv_ds_grpe_groupe="' . $groupe . '"  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();

        if (empty($result) || $result == 0) {

            $data = [
                'elv_ds_grpe_idelev' => $idelev,
                'elv_ds_grpe_groupe' => $groupe
            ];
            //var_dump($db);
            $sql = ' INSERT INTO eleve_estds_groupe (elv_ds_grpe_idelev, elv_ds_grpe_groupe) VALUES ( :elv_ds_grpe_idelev, :elv_ds_grpe_groupe);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            //$lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                return 1;
            } else {
                return 0;
            }
        } else {

            $data = [
                'elv_ds_grpe_idelev' => $idelev,
                'elv_ds_grpe_groupe' => $groupe,
                'elv_ds_grpe_etat' => 1
            ];
            //var_dump($db);
            $sql = 'UPDATE eleve_estds_groupe SET elv_ds_grpe_etat =:elv_ds_grpe_etat  WHERE elv_ds_grpe_idelev = :elv_ds_grpe_idelev AND elv_ds_grpe_groupe = :elv_ds_grpe_groupe;';

            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);

            return 1;
        }
    }
    public static function getEmploiDutpsBy($id_annee)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT * FROM(SELECT * FROM(SELECT * FROM(SELECT * FROM(SELECT emploitps_id,emploitps_date,emploitps_h_debut,emploitps_h_fin,emploitps_id_prof, emploitps_anneescolaire,emploitps_periode,emploitps_groupe_id,mat_libelle,Code_salle,salle_libelle FROM(SELECT emploitps_id,emploitps_date,emploitps_salle,emploitps_h_debut,emploitps_h_fin,emploitps_id_prof, emploitps_anneescolaire,emploitps_periode,emploitps_groupe_id,mat_libelle FROM(SELECT * FROM groupe_emploitps WHERE emploitps_etat = 1)tmp_grp_prog INNER JOIN (SELECT id_matiere_matiere,libele AS mat_libelle FROM matiere)tmp_mat ON tmp_grp_prog.emploitps_id_mat =  tmp_mat.id_matiere_matiere)tmp_prog_mat INNER JOIN (SELECT id_salle,Code_salle,libelle AS salle_libelle FROM salle)tmp_salle ON tmp_prog_mat.emploitps_salle = tmp_salle.id_salle)tmp_p_s_m INNER JOIN (SELECT id_type, nom_prenom FROM personne WHERE type_pers = 2)tmp_prof ON tmp_p_s_m.emploitps_id_prof = tmp_prof.id_type) tmp_p_s_m_pr INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe)tmp_grp ON tmp_p_s_m_pr.emploitps_groupe_id = tmp_grp.groupe_id)tmp_f INNER JOIN (SELECT id_annee_partie,libele_partie FROM annee_partie)tmp_per ON tmp_f.emploitps_periode = tmp_per.id_annee_partie)tmp_f_all WHERE emploitps_anneescolaire=' . $id_annee;
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_salle_emplTpsBy($fk_iduniv, $date_lib)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT * FROM(
            SELECT * FROM(
                SELECT * FROM(
                    SELECT * FROM(
                        SELECT emploitps_id,emploitps_date,emploitps_h_debut,emploitps_h_fin,emploitps_id_prof,fk_iduniv, emploitps_anneescolaire,emploitps_periode,emploitps_groupe_id,mat_libelle,Code_salle,salle_libelle 
                        FROM

                            (SELECT fk_iduniv,emploitps_id,emploitps_date,emploitps_salle,emploitps_h_debut,emploitps_h_fin,emploitps_id_prof, emploitps_anneescolaire,emploitps_periode,emploitps_groupe_id,mat_libelle 
                            FROM
                             (SELECT * FROM groupe_emploitps WHERE emploitps_etat = 1)tmp_grp_prog 
                            INNER JOIN 
                             (SELECT id_matiere_matiere,libele AS mat_libelle FROM matiere)tmp_mat 
                            ON 
                             tmp_grp_prog.emploitps_id_mat =  tmp_mat.id_matiere_matiere)tmp_prog_mat 

                        INNER JOIN 
                          (SELECT id_salle,Code_salle,libelle AS salle_libelle FROM salle WHERE fk_univ=' . $fk_iduniv . ')tmp_salle
                        ON 
                         tmp_prog_mat.emploitps_salle = tmp_salle.id_salle
                    )tmp_p_s_m 

                    INNER JOIN (SELECT id_type, nom_prenom FROM personne WHERE type_pers = 2)tmp_prof 
                    ON tmp_p_s_m.emploitps_id_prof = tmp_prof.id_type) tmp_p_s_m_pr 
                INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe)tmp_grp 
                ON tmp_p_s_m_pr.emploitps_groupe_id = tmp_grp.groupe_id)tmp_f 
            INNER JOIN (SELECT id_annee_partie,libele_partie FROM annee_partie)tmp_per 
            ON tmp_f.emploitps_periode = tmp_per.id_annee_partie)tmp_f_all 
            WHERE fk_iduniv=' . $fk_iduniv . ' AND emploitps_date="' . $date_lib . '"';
        //print_r($sql);
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public static function get_GroupEmploiDutpsBy($id_groupe)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT * FROM(SELECT * FROM(SELECT * FROM(SELECT * FROM(SELECT emploitps_id,emploitps_date,emploitps_h_debut,emploitps_h_fin,emploitps_id_prof, emploitps_anneescolaire,emploitps_periode,emploitps_groupe_id,mat_libelle,Code_salle,salle_libelle FROM(SELECT emploitps_id,emploitps_date,emploitps_salle,emploitps_h_debut,emploitps_h_fin,emploitps_id_prof, emploitps_anneescolaire,emploitps_periode,emploitps_groupe_id,mat_libelle FROM(SELECT * FROM groupe_emploitps WHERE emploitps_etat = 1)tmp_grp_prog INNER JOIN (SELECT id_matiere_matiere,libele AS mat_libelle FROM matiere)tmp_mat ON tmp_grp_prog.emploitps_id_mat =  tmp_mat.id_matiere_matiere)tmp_prog_mat INNER JOIN (SELECT id_salle,Code_salle,libelle AS salle_libelle FROM salle)tmp_salle ON tmp_prog_mat.emploitps_salle = tmp_salle.id_salle)tmp_p_s_m INNER JOIN (SELECT id_type, nom_prenom FROM personne WHERE type_pers = 2)tmp_prof ON tmp_p_s_m.emploitps_id_prof = tmp_prof.id_type) tmp_p_s_m_pr INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe)tmp_grp ON tmp_p_s_m_pr.emploitps_groupe_id = tmp_grp.groupe_id)tmp_f INNER JOIN (SELECT id_annee_partie,libele_partie FROM annee_partie)tmp_per ON tmp_f.emploitps_periode = tmp_per.id_annee_partie)tmp_f_all WHERE emploitps_groupe_id=' . $id_groupe;
        //var_dump($login,$pass,$sql);
        //print_r($sql) ;exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function get_allGroupEmploiDutpsBy($id_annee)
    {
        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat
        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT * FROM(SELECT * FROM(SELECT * FROM(SELECT * FROM
            (SELECT emploitps_id,emploitps_date,emploitps_h_debut,emploitps_h_fin,emploitps_id_prof, emploitps_anneescolaire,emploitps_periode,emploitps_groupe_id,mat_libelle,Code_salle,salle_libelle FROM(SELECT emploitps_id,emploitps_date,emploitps_salle,emploitps_h_debut,emploitps_h_fin,emploitps_id_prof, emploitps_anneescolaire,emploitps_periode,emploitps_groupe_id,mat_libelle FROM(SELECT * FROM groupe_emploitps WHERE emploitps_etat = 1)tmp_grp_prog INNER JOIN (SELECT id_matiere_matiere,libele AS mat_libelle FROM matiere)tmp_mat ON tmp_grp_prog.emploitps_id_mat =  tmp_mat.id_matiere_matiere)tmp_prog_mat INNER JOIN (SELECT id_salle,Code_salle,libelle AS salle_libelle FROM salle)tmp_salle ON tmp_prog_mat.emploitps_salle = tmp_salle.id_salle)tmp_p_s_m INNER JOIN (SELECT id_type, nom_prenom FROM personne WHERE type_pers = 2)tmp_prof ON tmp_p_s_m.emploitps_id_prof = tmp_prof.id_type) tmp_p_s_m_pr INNER JOIN (SELECT groupe_id,groupe_libelle FROM groupe)tmp_grp ON tmp_p_s_m_pr.emploitps_groupe_id = tmp_grp.groupe_id)tmp_f INNER JOIN (SELECT id_annee_partie,libele_partie FROM annee_partie)tmp_per ON tmp_f.emploitps_periode = tmp_per.id_annee_partie)tmp_f_all  
        WHERE  tmp_f_all.emploitps_date = CURDATE() AND tmp_f_all.emploitps_anneescolaire=' . $id_annee;
        //var_dump($login,$pass,$sql);
        //print_r($sql) ;exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public static function getPartAnneeByGroup($id_group)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT  * FROM(SELECT id_annee_partie,libele_partie,id_anneeScolaire FROM annee_partie WHERE etat_anneepart = 1 )tmp_partann INNER JOIN (SELECT groupe_annee FROM groupe WHERE groupe_id=' . $id_group . ' AND groupe_etat=1)tmp_grpann ON tmp_partann.id_anneeScolaire = tmp_grpann.groupe_annee';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public static function setDeleteGrpEleve($groupe, $idelev)
    {
        $db = static::getDB();
        //var_dump($_POST);exit();


        // emploitps_id 	emploitps_jour 	emploitps_id_mat 	emploitps_id_prof 	emploitps_h_debut 	emploitps_h_fin 	emploitps_groupe_id 	emploitps_etat 

        $sql = ' SELECT * FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev="' . $idelev . '" AND elv_ds_grpe_groupe="' . $groupe . '"  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();

        if (empty($result) || $result == 0) {

            return 0;
        } else {

            //var_dump($db);
            $sql = ' DELETE FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev="' . $idelev . '" AND elv_ds_grpe_groupe="' . $groupe . '"';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                return 1;
            } else {

                return 0;
            }
        }
    }

    public static function getAllEffectifs()
    {
        $result = [];
        $db = static::getDB();
        //var_dump($_POST);exit();
        //var_dump($_POST);exit();

        //NOMBRE D'ELEVE
        $sql = 'SELECT COUNT(id_eleve_eleve) AS nbreelev FROM eleve WHERE eleve_etat=1';
        $stmt = $db->query($sql);
        $result['nbreelev'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE PROF
        $sql = 'SELECT COUNT(id_prof_prof) AS nbreprof FROM prof WHERE etat=1';
        $stmt = $db->query($sql);
        $result['nbreprof'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE PARENT
        $sql = 'SELECT COUNT(id_parent_parent) AS nbreparent FROM parent WHERE etat_parent=1';
        $stmt = $db->query($sql);
        $result['nbreparent'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE CLASSES
        $sql = 'SELECT COUNT(groupe_id) AS nbreclasse FROM groupe WHERE groupe_etat=1';
        $stmt = $db->query($sql);
        $result['nbreclasse'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE matiere
        $sql = 'SELECT COUNT(id_matiere_matiere) AS nbrematirer FROM matiere WHERE etat=1';
        $stmt = $db->query($sql);
        $result['nbrematirer'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE ANNEE
        $sql = 'SELECT COUNT(id_anscol_annee_scolaire) AS nbreannee FROM annee_scolaire WHERE etat_annee=1';
        $stmt = $db->query($sql);
        $result['nbreannee'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE FILIERE
        $sql = 'SELECT COUNT(id_classe_classe) AS nbrefiliere FROM classe WHERE etat=1';
        $stmt = $db->query($sql);
        $result['nbrefiliere'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE SALLE
        $sql = 'SELECT COUNT(id_salle) AS nbresalle FROM salle WHERE etat=1';
        $stmt = $db->query($sql);
        $result['nbresalle'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public static function get_AllEffectifsBy($id_univ)
    {
        $result = [];
        $db = static::getDB();
        //var_dump($_POST);exit();
        //var_dump($_POST);exit();

        //NOMBRE D'ELEVE
        $sql = 'SELECT COUNT(tmp_elvfinal.id_eleve_eleve) AS nbreelev FROM ( SELECT * FROM(SELECT id_eleve_eleve FROM eleve WHERE etat_reso_ancien = 0)tmp_elev INNER JOIN (SELECT id_pers_personne,type_pers,id_type,fk_iduniv FROM personne WHERE etat_pers=1 AND type_pers = 1 AND fk_iduniv =' . $id_univ . ')tmmp_pers WHERE tmp_elev.id_eleve_eleve = tmmp_pers.id_type)tmp_elvfinal';
        $stmt = $db->query($sql);
        $result['nbreelev'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE ANCIEN ELEVE
        $sql = 'SELECT COUNT(tmp_elvfinal.id_eleve_eleve) AS nbre_ancien_elev FROM ( SELECT * FROM(SELECT id_eleve_eleve FROM eleve WHERE etat_reso_ancien = 1)tmp_elev INNER JOIN (SELECT id_pers_personne,type_pers,id_type,fk_iduniv FROM personne WHERE etat_pers=1 AND type_pers = 1 AND fk_iduniv =' . $id_univ . ')tmmp_pers WHERE tmp_elev.id_eleve_eleve = tmmp_pers.id_type)tmp_elvfinal';
        $stmt = $db->query($sql);
        $result['nbre_ancien_elev'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE PROF
        $sql = 'SELECT COUNT(personne.id_type) AS nbreprof FROM personne,prof WHERE personne.fk_iduniv=' . $id_univ . ' AND personne.type_pers = 2 AND personne.etat_pers = 1 AND prof.id_prof_prof =personne.id_type';
        $stmt = $db->query($sql);
        $result['nbreprof'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE PARENT
        $sql = 'SELECT COUNT(personne.id_type) AS nbreparent FROM personne,parent WHERE personne.fk_iduniv=' . $id_univ . ' AND personne.type_pers = 3 AND personne.etat_pers = 1 AND personne.id_type =parent.id_parent_parent';
        $stmt = $db->query($sql);
        $result['nbreparent'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE ADMIN
        $sql = 'SELECT COUNT(personne.id_type) AS nbreadmin FROM personne,admintab WHERE personne.fk_iduniv=' . $id_univ . ' AND personne.type_pers = 4 AND personne.etat_pers = 1 AND personne.id_type =admintab.id_admin_admin';
        $stmt = $db->query($sql);
        $result['nbreadmin'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE GROUPE
        $sql = 'SELECT COUNT(groupe.groupe_id) AS nbreclasse FROM groupe,classe,departement WHERE groupe_etat = 1 AND groupe.groupe_classe = classe.id_classe_classe AND classe.id_departement = departement.id_depat AND departement.fk_iduniv = ' . $id_univ;
        $stmt = $db->query($sql);
        $result['nbreclasse'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE matiere
        $sql = 'SELECT COUNT(id_matiere_matiere) AS nbrematirer FROM matiere WHERE etat=1 AND fk_iduniv = ' . $id_univ;
        $stmt = $db->query($sql);
        $result['nbrematirer'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE ANNEE
        $sql = 'SELECT COUNT(id_anscol_annee_scolaire) AS nbreannee FROM annee_scolaire WHERE etat_annee=1 AND fk_univ =  ' . $id_univ;
        $stmt = $db->query($sql);
        $result['nbreannee'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE FILIERE
        $sql = 'SELECT COUNT(id_classe_classe) AS nbrefiliere FROM classe,departement WHERE classe.etat = 1 AND  classe.id_departement = departement.id_depat AND departement.fk_iduniv = ' . $id_univ;
        $stmt = $db->query($sql);
        $result['nbrefiliere'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE SALLE
        $sql = 'SELECT COUNT(id_salle) AS nbresalle FROM salle WHERE etat=1 AND fk_univ = ' . $id_univ;
        $stmt = $db->query($sql);
        $result['nbresalle'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //NOMBRE DE SALLE
        $sql = 'SELECT COUNT(id_niveau) AS nbreniveaux FROM niveau WHERE  fk_id_univ = ' . $id_univ;
        $stmt = $db->query($sql);
        $result['nbreniveaux'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public static function getAllSalle()
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM salle WHERE etat = 1 ';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return  $result;
        //var_dump($sql,$stmt,$result);

        //exit();
    }

    public static function getAll_univSalle($fk_univ)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM salle WHERE etat = 1 AND fk_univ = ' . $fk_univ;
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return  $result;

    }
    public static function setSalle()
    {

        $db = static::getDB();

        $cree_salle_code = htmlspecialchars($_POST["cree_salle_code"]);
        $cree_salle_titre = htmlspecialchars($_POST["cree_salle_titre"]);
        $cree_salle_infos = htmlspecialchars($_POST["cree_salle_infos"]);
        //	id_salle 	libelle 	Code_salle 	description 	etat 
        $sql = ' SELECT * FROM salle WHERE libelle= "' . $cree_salle_titre . '" AND Code_salle= "' . $cree_salle_code . '"   LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        //exit();
        if (empty($result) || $result == 0) {

            $data = [
                'libelle' => $cree_salle_titre,
                'Code_salle' => $cree_salle_code,
                'description' => $cree_salle_infos
            ];
            //var_dump($db);
            $sql = ' INSERT INTO salle (libelle, Code_salle, description) VALUES ( :libelle, :Code_salle, :description);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                return "creer";
            } else {

                return "erreur";
            }
        } else {
            return "existe";
        }
    }
    public static function set_SalleBy($cree_salle_code, $cree_salle_titre, $cree_salle_infos, $fk_univ)
    {

        $db = static::getDB();


        //	id_salle 	libelle 	Code_salle 	description  fk_univ 	etat 
        $sql = ' SELECT * FROM salle WHERE libelle= "' . $cree_salle_titre . '" AND Code_salle= "' . $cree_salle_code . '" AND fk_univ= "' . $fk_univ . '"   LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        //exit();
        if (empty($result) || $result == 0) {

            $data = [
                'libelle' => $cree_salle_titre,
                'Code_salle' => $cree_salle_code,
                'description' => $cree_salle_infos,
                'fk_univ' => $fk_univ
            ];
            //var_dump($db);
            $sql = ' INSERT INTO salle (libelle, Code_salle, description, fk_univ) VALUES ( :libelle, :Code_salle, :description, :fk_univ);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return -1;
        }
    }
    public static function getPartieMatiereBy($groupeid, $partannee)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat

        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT id_matiere_matiere,libele FROM (SELECT matiere_id_tmp FROM groupe_matiere_coef WHERE groupe_id_tmp = ' . $groupeid . ' AND part_annee_id_tmp = ' . $partannee . ') AS grpmat INNER JOIN (SELECT id_matiere_matiere,libele FROM matiere WHERE etat = 1)listmat ON grpmat.matiere_id_tmp =listmat.id_matiere_matiere ';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    public static function getAllEval_ByAnne($id_annee)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat
        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT * FROM( SELECT * FROM(SELECT * FROM(SELECT *  FROM (SELECT * FROM prof_eval WHERE eval_etat = 1)tmp_eval0 INNER JOIN (SELECT groupe_id AS grpeid,groupe_libelle FROM groupe WHERE groupe_annee = ' . $id_annee . ' AND groupe_etat = 1 )tmp_grpe ON tmp_eval0.id_groupe = tmp_grpe.grpeid)tmp_eval1 INNER JOIN (SELECT id_type,nom_prenom,contact FROM personne WHERE type_pers = 2)tmp_prof on tmp_eval1.id_prof = tmp_prof.id_type)tmp_eval2 INNER JOIN (SELECT id_matiere_matiere, code,libele FROM matiere)tmp_mat on tmp_eval2.id_mat = tmp_mat.id_matiere_matiere)tmp_ngee_eval

        INNER JOIN 
        (SELECT eval_id, eval_date, eval_hDebut, eval_hFin, coef, notation FROM prof_eval_emploitps)tmp_ngee_evaldate
        
        ON tmp_ngee_eval.prof_eval_id = tmp_ngee_evaldate.eval_id';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getAllEval_adminByAnne($id_annee)
    {

        $db = static::getDB();
        //id_matiere_matiere	code	description	libele	etat
        //$sql=' SELECT * FROM classe WHERE etat = 1 ORDER BY libelle ASC';
        $sql = 'SELECT * FROM
                ( SELECT * FROM
                    (SELECT * FROM
                        (SELECT *  FROM 
                            (SELECT * FROM prof_eval WHERE eval_etat = 1)tmp_eval0 
                        INNER JOIN 
                            (SELECT groupe_id AS grpeid,groupe_libelle FROM groupe WHERE groupe_annee = ' . $id_annee . ' AND groupe_etat = 1 )tmp_grpe 
                        ON tmp_eval0.id_groupe = tmp_grpe.grpeid)tmp_eval1 
                    INNER JOIN 
                        (SELECT id_pers_personne,nom_prenom,contact,id_type FROM personne WHERE type_pers = 4)tmp_prof 
                    ON tmp_eval1.id_prof = tmp_prof.id_type)tmp_eval2 
                    INNER JOIN 
                        (SELECT id_matiere_matiere, code,libele FROM matiere)tmp_mat 
                    ON tmp_eval2.id_mat = tmp_mat.id_matiere_matiere)tmp_ngee_eval
                INNER JOIN 
                    (SELECT eval_id, eval_date, eval_hDebut, eval_hFin, coef, notation FROM prof_eval_emploitps)tmp_ngee_evaldate
                ON tmp_ngee_eval.prof_eval_id = tmp_ngee_evaldate.eval_id';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    //::::MENU GESTION DES CLASSES::::::::::
    public static function setEvalProg_tps($evalProg_date, $evalProg_salle, $evalProg_salle_heuredebut, $evalProg_salle_heurefin, $evalProg_ideval, $evalProg_coef, $evalProg_notation)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM prof_eval_emploitps WHERE eval_id= "' . $evalProg_ideval . '" LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //var_dump($sql,$stmt,$result);exit();
        if (empty($result) || $result == 0) {
            return "evalprog_erreur_exist";
        } else {
            //prof_eval_emploiTps_id	eval_id	eval_salle_id	eval_date	eval_hDebut	eval_hFin  coef	notation	etat_evalTps
            $data = [
                'eval_salle_id' => $evalProg_salle,
                'eval_date' => $evalProg_date,
                'eval_hDebut' => $evalProg_salle_heuredebut,
                'eval_hFin' => $evalProg_salle_heurefin,
                'etat_evalTps' => 1,
                'coef' => $evalProg_coef,
                'notation' => $evalProg_notation,
                'eval_id' => $evalProg_ideval
            ];
            //var_dump($db);
            $sql = 'UPDATE prof_eval_emploitps SET eval_salle_id =:eval_salle_id  ,eval_date =:eval_date ,eval_hDebut =:eval_hDebut ,eval_hFin =:eval_hFin ,etat_evalTps =:etat_evalTps, coef=:coef ,notation=:notation   WHERE eval_id = :eval_id;';

            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            //$lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {

                $data = [
                    'eval_etat' => 2,
                    'prof_eval_id' => $evalProg_ideval
                ];
                //var_dump($db);
                $sql = 'UPDATE prof_eval SET eval_etat =:eval_etat  WHERE prof_eval_id = :prof_eval_id;';

                $stmt = $db->prepare($sql);
                $result = $stmt->execute($data);

                unset($_POST['btn_set_evalProg']);
                unset($_POST['evalProg_date']);

                return "evalprog_ok";
            } else {
                return "evalprog_erreur_rqt";
            }
        }
    }

    //::::MENU GESTION DES CLASSES::::::::::

    //Non terminer
    public static function get_elevMoyBymat($moy_annee, $moy_grpe, $moy_mat, $moy_session)
    {

        $db = static::getDB();
        $sql = 'SELECT * FROM moyenne WHERE id_groupe = ' . $moy_grpe . ' AND id_matiere = ' . $moy_mat . ' AND id_session = ' . $moy_session;
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result;
        }
    }

    public static function get_info_elevMoyBymat($moy_grpe, $moy_mat, $moy_session)
    {

        $db = static::getDB();
        $sql = 'SELECT * FROM (SELECT * FROM moyenne WHERE id_groupe = ' . $moy_grpe . ' AND id_matiere=' . $moy_mat . ' AND id_session = ' . $moy_session . ')tmp_moy INNER JOIN (SELECT id_eleve_eleve,matricule ,nom_prenom,date_naiss,lieu_naiss,sexe,contact FROM(SELECT nom_prenom,date_naiss,lieu_naiss,sexe,contact,id_type FROM personne WHERE type_pers = 1)tmp_pers INNER JOIN (SELECT id_eleve_eleve, matricule FROM eleve)tmp_elev ON tmp_pers.id_type = tmp_elev.id_eleve_eleve)tmp_elevpers ON tmp_elevpers.id_eleve_eleve = tmp_moy.id_eleve';
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result;
        }
    }

    public static function set_info_elevMoy_OK_By($id_groupe, $id_eleve, $id_matiere, $id_prof,  $id_session,  $etat_moy)
    {

        $db = static::getDB();
        //id_groupe	id_eleve	id_matiere	id_prof	id_session	moyenne	etat_moy 
        $sql = ' SELECT * FROM moyenne WHERE id_groupe = ' . $id_groupe . ' AND id_eleve=' . $id_eleve . ' AND id_matiere=' . $id_matiere . ' AND id_prof=' . $id_prof . ' AND id_session=' . $id_session . '  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return "setelevMoy_erreur";
        } else {
            $data = [
                'id_groupe' => $id_groupe,
                'id_eleve' => $id_eleve,
                'id_matiere' => $id_matiere,
                'id_prof' => $id_prof,
                'id_session' => $id_session,
                'etat_moy' => $etat_moy
            ];

            $sql = 'UPDATE moyenne SET etat_moy = :etat_moy WHERE id_groupe =:id_groupe AND id_eleve =:id_eleve AND id_matiere =:id_matiere AND id_prof =:id_prof AND id_session =:id_session;';
            //var_dump($sql);
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            //$lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {

                unset($_POST['id_groupe']);
                unset($_POST['id_eleve']);
                unset($_POST['id_matiere']);
                unset($_POST['id_prof']);
                unset($_POST['id_session']);
                unset($_POST['etat_moy']);

                return "setelevMoy_update";
            } else {
                return "setelevMoy_erreur";
            }
        }
    }

    public static function get_info_onMoy($moy_grpe, $moy_mat, $moy_annee, $moy_session)
    {

        $db = static::getDB();
        $sql = 'SELECT groupe_libelle FROM groupe WHERE groupe_id =' . $moy_grpe . ' UNION SELECT libele AS mat_lib FROM matiere WHERE id_matiere_matiere = ' . $moy_mat . ' UNION SELECT annee_libelle FROM annee_scolaire WHERE id_anscol_annee_scolaire = ' . $moy_annee . ' UNION SELECT Libelle_session FROM annee_session WHERE id_session_session = ' . $moy_session;
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result;
        }
    }

    /* * 
    *TABLE  : notification
    * NOTIFICATIONS
    *	id_notif notif_titre	notif_desc 	notif_debut 	notif_fin 	notif_methode	createur_notif notif_etat 	 
    * TABLE :  notif_user
    * usernotif_iduser 	usernotif_id 	usernotif_typeuser 	usernotif_etat 
    */

    public static function set_notifications($notif_titre, $notif_desc, $notif_debut, $notif_fin, $notif_methode, $createur_notif,$fk_iduniv)
    {
        $db = static::getDB();

        //unset($_POST);
        $sql = ' SELECT * FROM notification WHERE notif_titre= "' . $notif_titre . '" AND notif_desc = "' . $notif_desc . '" AND notif_debut = "' . $notif_debut . '" AND notif_fin = "' . $notif_fin . '" AND notif_methode = "' . $notif_methode . '" AND fk_iduniv = "' . $fk_iduniv . '"  LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        //exit();
        if (empty($result) || $result == 0) {

            $data = [
                'notif_titre' => $notif_titre,
                'notif_desc' => $notif_desc,
                'notif_debut' => $notif_debut,
                'notif_fin' => $notif_fin,
                'notif_methode' => $notif_methode,
                'createur_notif' => $createur_notif,
                'fk_iduniv' => $fk_iduniv,
                'notif_etat' => 2
            ];
            //var_dump($db);
            $sql = ' INSERT INTO notification (notif_titre, notif_desc, notif_debut, notif_fin, notif_methode, createur_notif, fk_iduniv, notif_etat) VALUES ( :notif_titre, :notif_desc, :notif_debut, :notif_fin, :notif_methode, :createur_notif, :fk_iduniv, :notif_etat);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                return $lastid;
            } else {
                return -1;
            }
        } else {
            return 0;
        }
    }

    public static function set_usersNotif($usernotif_iduser, $usernotif_id, $usernotif_typeuser)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $sql = ' SELECT * FROM notif_user WHERE usernotif_iduser= "' . $usernotif_iduser . '" AND usernotif_id = "' . $usernotif_id . '"  AND usernotif_typeuser = "' . $usernotif_typeuser . '"  LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);
        //exit();
        if (empty($result) || $result == 0) {

            $data = [
                'usernotif_iduser' => $usernotif_iduser,
                'usernotif_id' => $usernotif_id,
                'usernotif_typeuser' => $usernotif_typeuser
            ];
            //var_dump($db);
            $sql = ' INSERT INTO notif_user (usernotif_iduser, usernotif_id, usernotif_typeuser) VALUES ( :usernotif_iduser, :usernotif_id, :usernotif_typeuser);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            //var_dump($lastid);
            if ($result == TRUE) {
                return 1;
            } else {
                return -1;
            }
        } else {
            return 0;
        }
    }

    public static function get_allNotifs($fk_id_univ)
    {

        $db = static::getDB();
        $sql = 'SELECT * FROM(SELECT * FROM notification INNER JOIN (SELECT id_pers_personne,id_type,nom_prenom,contact FROM personne WHERE type_pers  = 4)tmp_pers ON notification.createur_notif = tmp_pers.id_pers_personne)tmp_f WHERE tmp_f.notif_etat > 0 AND tmp_f.fk_iduniv='.$fk_id_univ;
        //print_r($sql);
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result;
        }
    }

    public static function set_allNotifs_etat($id_notif, $etat_notif)
    {

        $db = static::getDB();
        $sql = ' SELECT * FROM notification WHERE id_notif = ' . $id_notif . '  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return "setupdatenotif_erreur";
        } else {
            $data = [
                'id_notif' => $id_notif,
                'notif_etat' => $etat_notif
            ];

            $sql = 'UPDATE notification SET notif_etat = :notif_etat WHERE id_notif =:id_notif ;';
            //var_dump($sql);
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            //$lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                unset($_POST);
                return "setupdatenotif_update";
            } else {
                return "setupdatenotif_erreur";
            }
        }
    }


    public static function update_elevMatr($id_eleve, $matricule)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM eleve WHERE id_eleve_eleve= ' . $id_eleve . '  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);
        if (empty($result) || $result == 0) {
            return "danger";
        } else {

            $data = [
                'matricule' => $matricule,
                'id_eleve_eleve' => $id_eleve
            ];
            //var_dump($db);
            $sql = 'UPDATE eleve SET matricule =:matricule  WHERE id_eleve_eleve = :id_eleve_eleve;';

            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            return "success";
        }
    }

    /* * 
        *TABLE  : stage_etudiant
        id_stage_etudiant theme_stage	fk_idetudiant	fk_idgroupe	fk_idprof_directEtud fk_idanneeScol	nom_entreprise	ville_entreprise	loca_entreprise	tel_entreprise	email_entreprise	maitre_stage	tel_maitre_stage	poste_maitre_stage	date_debut	date_fin 	etat_stage

    */
    public static function stage_etudiant($theme_stage, $fk_idetudiant, $fk_idgroupe, $fk_idprof_directEtud, $fk_idanneeScol, $nom_entreprise, $ville_entreprise, $loca_entreprise, $tel_entreprise, $email_entreprise, $maitre_stage, $tel_maitre_stage, $poste_maitre_stage, $date_debut, $date_fin)
    {

        $db = static::getDB();

        //	id_salle 	libelle 	Code_salle 	description 	etat 
        $sql = ' SELECT * FROM stage_etudiant WHERE fk_idetudiant= "' . $fk_idetudiant . '" AND fk_idgroupe= "' . $fk_idgroupe . '"  AND fk_idprof_directEtud= "' . $fk_idprof_directEtud . '"  AND fk_idanneeScol= "' . $fk_idanneeScol . '" AND nom_entreprise= "' . $nom_entreprise . '" AND theme_stage= "' . $theme_stage . '" LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        if (isset($_SESSION['user']['id_type']) && intval($_SESSION['user']['id_type']) == 4) {
            $etat_stage = 1;
        } else {
            $etat_stage = 0;
        }


        //exit();
        if (empty($result) || $result == 0) {

            $data = [
                'theme_stage' => $theme_stage,
                'fk_idetudiant' => $fk_idetudiant,
                'fk_idgroupe' => $fk_idgroupe,
                'fk_idprof_directEtud' => $fk_idprof_directEtud,
                'fk_idanneeScol' => $fk_idanneeScol,
                'nom_entreprise' => $nom_entreprise,
                'ville_entreprise' => $ville_entreprise,
                'loca_entreprise' => $loca_entreprise,
                'tel_entreprise' => $tel_entreprise,
                'email_entreprise' => $email_entreprise,
                'maitre_stage' => $maitre_stage,
                'tel_maitre_stage' => $tel_maitre_stage,
                'poste_maitre_stage' => $poste_maitre_stage,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'etat_stage' => $etat_stage,
            ];
            //var_dump($db);
            $sql = ' INSERT INTO stage_etudiant (theme_stage, fk_idetudiant, fk_idgroupe,fk_idprof_directEtud, fk_idanneeScol, nom_entreprise, ville_entreprise,loca_entreprise, tel_entreprise, email_entreprise,maitre_stage, tel_maitre_stage, poste_maitre_stage,date_debut,date_fin,	etat_stage) VALUES ( :theme_stage, :fk_idetudiant, :fk_idgroupe,:fk_idprof_directEtud, :fk_idanneeScol, :nom_entreprise,:ville_entreprise, :loca_entreprise, :tel_entreprise, :email_entreprise, :maitre_stage, :tel_maitre_stage,:poste_maitre_stage,:date_debut,:date_fin, :etat_stage);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                return 1;
            } else {

                return -1;
            }
        } else {
            return 0;
        }
    }

    public static function get_stage_etudiant($id_anneescol)
    {

        if ($id_anneescol == 0) {
            $sql = 'SELECT stage_etudiant.*,tmp_pers.nom_prenom,tmp_eleve.matricule,	tmp_eleve.etat_reso_ancien,tmp_grpe.* ,tmp_prof.nom_prenom AS prof_nomprenom,tmp_prof.prof_tel  FROM 
            stage_etudiant,(SELECT id_anscol_annee_scolaire FROM annee_scolaire ORDER by annee_libelle DESC LIMIT 1)tmp_anee,(SELECT nom_prenom,id_type FROM personne WHERE type_pers = 1 AND etat_pers = 1)tmp_pers, (SELECT matricule,id_eleve_eleve,	etat_reso_ancien FROM eleve)tmp_eleve , (SELECT groupe.groupe_id, groupe.groupe_libelle,niveau.libelle_niveau,classe.libelle AS lib_classe FROM groupe,niveau,classe  WHERE niveau.id_niveau = groupe.fk_idniveau AND classe.id_classe_classe = groupe.groupe_classe)tmp_grpe,(SELECT nom_prenom,id_type,contact AS prof_tel FROM personne WHERE type_pers = 2 )tmp_prof
            WHERE stage_etudiant.fk_idanneeScol =tmp_anee.id_anscol_annee_scolaire AND stage_etudiant.fk_idetudiant=tmp_pers.id_type AND tmp_eleve.id_eleve_eleve = stage_etudiant.fk_idetudiant AND tmp_grpe.groupe_id = stage_etudiant.fk_idgroupe AND tmp_prof.id_type = stage_etudiant.fk_idprof_directEtud';
        } else {

            $sql = 'SELECT stage_etudiant.*,tmp_pers.nom_prenom,tmp_eleve.matricule,	tmp_eleve.etat_reso_ancien,tmp_grpe.* ,tmp_prof.nom_prenom AS prof_nomprenom,tmp_prof.prof_tel FROM stage_etudiant,(SELECT id_anscol_annee_scolaire FROM annee_scolaire WHERE id_anscol_annee_scolaire = ' . $id_anneescol . ' ORDER by annee_libelle DESC )tmp_anee,(SELECT nom_prenom,id_type FROM personne WHERE type_pers = 1 AND etat_pers = 1)tmp_pers, (SELECT matricule,id_eleve_eleve,etat_reso_ancien FROM eleve)tmp_eleve , (SELECT groupe.groupe_id, groupe.groupe_libelle,niveau.libelle_niveau,classe.libelle AS lib_classe FROM groupe,niveau,classe WHERE niveau.id_niveau = groupe.fk_idniveau AND classe.id_classe_classe = groupe.groupe_classe)tmp_grpe,(SELECT nom_prenom,id_type,contact AS prof_tel FROM personne WHERE type_pers = 2 )tmp_prof WHERE stage_etudiant.fk_idanneeScol =tmp_anee.id_anscol_annee_scolaire AND stage_etudiant.fk_idetudiant=tmp_pers.id_type AND tmp_eleve.id_eleve_eleve = stage_etudiant.fk_idetudiant AND tmp_grpe.groupe_id = stage_etudiant.fk_idgroupe AND tmp_prof.id_type = stage_etudiant.fk_idprof_directEtud ';
        }


        //print_r($sql);


        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            //vide
            //vide
            return  $result;
        } else {
            //vide
            return 0;
        }
    }

    public static function get_all_stgEtudiant($id_etudiant, $id_anneescol)
    {

        $sql = 'SELECT * FROM (
            SELECT stage_etudiant.*,tmp_pers.nom_prenom,tmp_eleve.matricule,tmp_grpe.* ,tmp_prof.nom_prenom AS prof_nomprenom,tmp_prof.prof_tel  FROM 
            stage_etudiant,(SELECT id_anscol_annee_scolaire FROM annee_scolaire WHERE id_anscol_annee_scolaire = ' . $id_anneescol . ' ORDER by annee_libelle DESC )tmp_anee,(SELECT nom_prenom,id_type FROM personne WHERE type_pers = 1 AND etat_pers = 1)tmp_pers, (SELECT matricule,id_eleve_eleve FROM eleve)tmp_eleve , (SELECT groupe.groupe_id, groupe.groupe_libelle,niveau.libelle_niveau,classe.libelle AS lib_classe FROM groupe,niveau,classe  WHERE niveau.id_niveau = groupe.fk_idniveau AND classe.id_classe_classe = groupe.groupe_classe)tmp_grpe,(SELECT nom_prenom,id_type,contact AS prof_tel FROM personne WHERE type_pers = 2 )tmp_prof
            WHERE stage_etudiant.fk_idanneeScol =tmp_anee.id_anscol_annee_scolaire AND stage_etudiant.fk_idetudiant=tmp_pers.id_type AND tmp_eleve.id_eleve_eleve = stage_etudiant.fk_idetudiant AND tmp_grpe.groupe_id = stage_etudiant.fk_idgroupe AND tmp_prof.id_type = stage_etudiant.fk_idprof_directEtud)tmp_final WHERE tmp_final.fk_idanneeScol = ' . $id_anneescol . ' AND tmp_final.fk_idetudiant = ' . $id_etudiant;


        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            //vide
            //vide
            return  $result;
        } else {
            //vide
            return 0;
        }
        //print_r($sql);

    }
    public static function get_Etudiant_allstage($id_etudiant)
    {

        $sql = 'SELECT * FROM (
            SELECT stage_etudiant.*,tmp_pers.nom_prenom,tmp_eleve.matricule,tmp_grpe.* ,tmp_prof.nom_prenom AS prof_nomprenom,tmp_prof.prof_tel ,tmp_anee.annee_libelle FROM 
            stage_etudiant,(SELECT id_anscol_annee_scolaire,annee_libelle FROM annee_scolaire ORDER by annee_libelle DESC )tmp_anee,(SELECT nom_prenom,id_type FROM personne WHERE type_pers = 1 AND etat_pers = 1)tmp_pers, (SELECT matricule,id_eleve_eleve FROM eleve)tmp_eleve , (SELECT groupe.groupe_id, groupe.groupe_libelle,niveau.libelle_niveau,classe.libelle AS lib_classe FROM groupe,niveau,classe  WHERE niveau.id_niveau = groupe.fk_idniveau AND classe.id_classe_classe = groupe.groupe_classe)tmp_grpe,(SELECT nom_prenom,id_type,contact AS prof_tel FROM personne WHERE type_pers = 2 )tmp_prof
            WHERE stage_etudiant.fk_idanneeScol =tmp_anee.id_anscol_annee_scolaire AND stage_etudiant.fk_idetudiant=tmp_pers.id_type AND tmp_eleve.id_eleve_eleve = stage_etudiant.fk_idetudiant AND tmp_grpe.groupe_id = stage_etudiant.fk_idgroupe AND tmp_prof.id_type = stage_etudiant.fk_idprof_directEtud)tmp_final WHERE  tmp_final.fk_idetudiant = ' . $id_etudiant;


        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            //vide
            //vide
            return  $result;
        } else {
            //vide
            return 0;
        }
        //print_r($sql);

    }
    public static function get_Ancien_EtudiantBy($id_filliere, $id_eleve)
    {

        if ($id_eleve == 0) {
            $sql = 'SELECT * FROM 
            (SELECT * FROM 
                eleve_estds_groupe,(SELECT eleve.*,personne.id_pers_personne, personne.nom_prenom,personne.sexe,personne.email,personne.contact FROM eleve,personne WHERE eleve.etat_reso_ancien = 1 AND personne.type_pers = 1 AND personne.id_type = eleve.id_eleve_eleve ) tmp_eleve WHERE eleve_estds_groupe.elv_ds_grpe_idelev =tmp_eleve.id_eleve_eleve 
            ) tmp_eleve_estds_groupe
            INNER JOIN 
                (SELECT groupe.*,classe.libelle AS lib_classe, niveau.libelle_niveau, annee_scolaire.annee_libelle FROM groupe,classe,niveau,annee_scolaire WHERE groupe.groupe_classe = classe.id_classe_classe AND groupe.groupe_classe =' . $id_filliere . ' AND niveau.id_niveau = groupe.fk_idniveau AND annee_scolaire.id_anscol_annee_scolaire = groupe.groupe_annee)tmp_grp

            ON tmp_eleve_estds_groupe.elv_ds_grpe_groupe = tmp_grp.groupe_id  
            ORDER BY tmp_eleve_estds_groupe.eleve_estds_groupe_dateajout DESC';
        } else {
            $sql = 'SELECT * FROM 
            (SELECT * FROM 
                eleve_estds_groupe,(SELECT eleve.*,personne.id_pers_personne, personne.nom_prenom,personne.sexe,personne.email,personne.contact FROM eleve,personne WHERE eleve.etat_reso_ancien = 1 AND personne.type_pers = 1 AND personne.id_type = eleve.id_eleve_eleve AND eleve.id_eleve_eleve = ' . $id_eleve . ' ) tmp_eleve WHERE eleve_estds_groupe.elv_ds_grpe_idelev =tmp_eleve.id_eleve_eleve 
            ) tmp_eleve_estds_groupe
            INNER JOIN 
                (SELECT groupe.*,classe.libelle AS lib_classe, niveau.libelle_niveau, annee_scolaire.annee_libelle FROM groupe,classe,niveau,annee_scolaire WHERE groupe.groupe_classe = classe.id_classe_classe AND groupe.groupe_classe =' . $id_filliere . ' AND niveau.id_niveau = groupe.fk_idniveau AND annee_scolaire.id_anscol_annee_scolaire = groupe.groupe_annee)tmp_grp

            ON tmp_eleve_estds_groupe.elv_ds_grpe_groupe = tmp_grp.groupe_id  
            ORDER BY tmp_eleve_estds_groupe.eleve_estds_groupe_dateajout DESC';
        }



        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            //vide
            //vide
            return  $result;
        } else {
            //vide
            return 0;
        }
        //print_r($sql);

    }

    public static function setMAJ_Ancien_EtudiantBy($id_etudiant, $etat_reso)
    {

        $db = static::getDB();
        $sql = ' SELECT id_eleve_eleve FROM eleve WHERE id_eleve_eleve= "' . $id_etudiant . '"  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        if (empty($result) || $result == 0) {
            return 0;
        } else {

            $data = [
                'id_eleve_eleve' => $id_etudiant,
                'etat_reso_ancien' => $etat_reso
            ];
            //var_dump($db);
            $sql = 'UPDATE eleve SET etat_reso_ancien =:etat_reso_ancien  WHERE id_eleve_eleve = :id_eleve_eleve ;';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            return 1;
        }
    }

    public static function set_ancienInfos($tble_sqlchamp, $info, $id_etudiant)
    {

        $db = static::getDB();

        if ($tble_sqlchamp == "contact" || $tble_sqlchamp == "email") {
            $sql = ' SELECT * FROM personne WHERE type_pers = 1 AND id_type = ' . $id_etudiant . '  LIMIT 1';
        } else {
            $sql = ' SELECT id_eleve_eleve FROM eleve WHERE id_eleve_eleve= "' . $id_etudiant . '"  LIMIT 1';
        }

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        if (empty($result) || $result == 0) {
            return 0;
        } else {


            if ($tble_sqlchamp == "contact" || $tble_sqlchamp == "email") {
                $data = [
                    'id_type' => $id_etudiant,
                    'type_pers' => 1,
                    $tble_sqlchamp => $info
                ];
                //var_dump($db);
                $sql = 'UPDATE personne SET ' . $tble_sqlchamp . ' =:' . $tble_sqlchamp . '  WHERE id_type = :id_type AND  type_pers =:type_pers;';
            } else {
                $data = [
                    'id_eleve_eleve' => $id_etudiant,
                    $tble_sqlchamp => $info
                ];
                //var_dump($db);
                $sql = 'UPDATE eleve SET ' . $tble_sqlchamp . ' =:' . $tble_sqlchamp . '  WHERE id_eleve_eleve = :id_eleve_eleve ;';
            }

            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            return 1;
        }
    }




    public static function get_AllAncien_EtudiantBy()
    {

        $sql = 'SELECT eleve.* ,personne.nom_prenom,personne.sexe,personne.email,personne.contact FROM eleve,personne WHERE etat_reso_ancien = 1 AND personne.id_type = eleve.id_eleve_eleve AND personne.type_pers = 1 ORDER BY personne.nom_prenom ASC';


        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            //vide
            //vide
            return  $result;
        } else {
            //vide
            return 0;
        }
        //print_r($sql);

    }




    public static function get_AllEtudiant_dmdInscripBy($annscolaire, $id_univ)
    {

        $sql = 'SELECT tmp_pers.*,eleve.* FROM
        (SELECT preinscription.date_inscription,preinscription.annee_scola,personne.* FROM preinscription,personne
        WHERE  
        preinscription.annee_scola="' . $annscolaire . '"
        AND personne.type_pers=1 
        AND personne.fk_iduniv=' . $id_univ . '
        AND personne.id_type=preinscription.id_eleve)tmp_pers,eleve
        
        WHERE tmp_pers.id_type=eleve.id_eleve_eleve  
        ORDER BY tmp_pers.date_inscription DESC';


        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            //vide
            //vide
            return  $result;
        } else {
            //vide
            return 0;
        }
        //print_r($sql);

    }

    public static function get_UniqEtudiant_dmdInscripBy($annscolaire, $id_etudiant)
    {

        $sql = 'SELECT * FROM 
        (SELECT preinscription.date_inscription ,preinscription.annee_scola ,eleve.* FROM 
        preinscription,eleve
        WHERE  preinscription.id_eleve=' . $id_etudiant . '
        AND preinscription.annee_scola="' . $annscolaire . '"
        AND preinscription.id_eleve=eleve.id_eleve_eleve 
        
        ORDER BY preinscription.date_inscription DESC)tmp_elev,personne
        
        WHERE personne.type_pers=1 AND personne.id_type=tmp_elev.id_eleve_eleve AND personne.etat_pers  <> 0';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            //vide
            //vide
            return  $result;
        } else {
            //vide
            return 0;
        }
        //print_r($sql);

    }
    /*
     * 	tABLE :: preinscription
        id_eleve
        id_annee_scola
        date_inscription
        niveau
        classe
    */
    public static function set_MajPreinscriptionBy($id_anneescol, $id_etudiant, $niveau_id, $filiere_id)
    {
        $db = static::getDB();

        $sql = ' SELECT * FROM preinscription WHERE id_eleve= "' . $id_etudiant . '" AND annee_scola= "' . $id_anneescol . '" LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        if (empty($result) || $result == 0) {
            return 0;
        } else {

            $data = [
                'id_eleve' => $id_etudiant,
                'annee_scola' => $id_anneescol,
                'niveau' => $niveau_id,
                'classe' => $filiere_id,
            ];
            //var_dump($db);
            $sql = 'UPDATE preinscription SET niveau =:niveau, classe =:classe WHERE annee_scola = :annee_scola AND id_eleve = :id_eleve;';

            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            return 1;
        }
    }

    /* Table :: eleve
     * 	
        id_eleve_eleve    matricule      nationnalite   telfixe
        nomurget   telurget    commune    quartie     stuationmat
        civilite   niveauetude   options   parcours   profilcandidat  anciennete
        seriebac   numbac  diplome  activite
        niveau   statut_affecter  statut_redoublant  satut_brourse   eleve_etat  etat_reso_ancien
        anciennete  ======> niveau précédent
		niveauetude  ======> niveau demander
		  parcours ======> filiere demander
    */

    public static function get_all_univ_etudiant($annscolaire, $id_annee , $id_univ)
    {

        $sql = 'SELECT * FROM 
            (SELECT * FROM

            (SELECT groupe.*,eleve_estds_groupe.elv_ds_grpe_idelev,eleve_estds_groupe.eleve_estds_groupe_dateajout FROM eleve_estds_groupe,groupe WHERE groupe.groupe_annee='.$id_annee.' AND groupe.groupe_id= eleve_estds_groupe.elv_ds_grpe_groupe AND eleve_estds_groupe.elv_ds_grpe_etat=1)tmp_elev_classe

            INNER JOIN 

            (SELECT preinscription.*,personne.id_pers_personne,personne.nom_prenom,personne.date_naiss,personne.lieu_naiss,personne.sexe,personne.email,personne.contact FROM preinscription,personne WHERE preinscription.annee_scola="'.$annscolaire.'" AND personne.fk_iduniv='.$id_univ.' AND personne.type_pers=1 AND preinscription.id_eleve=personne.id_type)tmp_preinscript

            ON tmp_elev_classe.elv_ds_grpe_idelev = tmp_preinscript.id_eleve)tmp_infos_etud

        INNER JOIN eleve ON tmp_infos_etud.id_eleve= eleve.id_eleve_eleve';


        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            //vide
            //vide
            return  $result;
        } else {
            //vide
            return 0;
        }
        //print_r($sql);

    }
    

    public static function set_MajTble_eleveBy($id_etudiant, $matricule, $niveauetude, $parcours, $statut_affecter, $statut_redoublant, $satut_brourse)
    {
        $db = static::getDB();

        $sql = ' SELECT * FROM eleve WHERE id_eleve_eleve= "' . $id_etudiant . '"  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        if (empty($result) || $result == 0) {
            return 0;
        } else {

            $data = [
                'id_eleve_eleve' => $id_etudiant,
                'matricule' => $matricule,
                'niveauetude' => $niveauetude,
                'parcours' => $parcours,
                'statut_affecter' => $statut_affecter,
                'statut_redoublant' => $statut_redoublant,
                'satut_brourse' => $satut_brourse
            ];
            //var_dump($db);
            $sql = 'UPDATE eleve SET matricule =:matricule, niveauetude =:niveauetude , parcours =:parcours , statut_affecter =:statut_affecter , statut_redoublant =:statut_redoublant , satut_brourse =:satut_brourse WHERE id_eleve_eleve = :id_eleve_eleve;';

            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            return 1;
        }
    }

    public static function get_all_departBy($id_univ)
    {

        $sql = 'SELECT * FROM departement WHERE fk_iduniv = ' . $id_univ;
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            //vide
            //vide
            return  $result;
        } else {
            //vide
            return 0;
        }
        //print_r($sql);

    }
    public static function get_all_bulletinModeleBy($id_univ)
    {

        $sql = 'SELECT * FROM bulletin WHERE fk_univ = ' . $id_univ;
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            //vide
            //vide
            return  $result;
        } else {
            //vide
            return 0;
        }
        //print_r($sql);

    }

    public static function set_addMaj_bultinDepart_By($id_depart, $id_relever)
    {


        $db = static::getDB();
        $sql = ' SELECT * FROM bulletin_depart_liaison WHERE fk_idDepart= "' . $id_depart . '"  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        if (empty($result) || $result == 0) {

            $data = [
                'fk_idBulletin' => $id_relever,
                'fk_idDepart' => $id_depart,
            ];
            //var_dump($db);
            $sql = ' INSERT INTO bulletin_depart_liaison (fk_idBulletin, fk_idDepart) VALUES ( :fk_idBulletin, :fk_idDepart);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            return 1;
        } else {

            $data = [
                'fk_idBulletin' => $id_relever,
                'fk_idDepart' => $id_depart
            ];
            //var_dump($db);
            $sql = 'UPDATE bulletin_depart_liaison SET fk_idBulletin =:fk_idBulletin  WHERE fk_idDepart = :fk_idDepart ;';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            return 1;
        }
    }

    public static function get_bultinDepart_By($id_univ)
    {

        $sql = 'SELECT * FROM bulletin_depart_liaison,departement,bulletin WHERE  bulletin_depart_liaison.fk_idDepart=departement.id_depat AND bulletin_depart_liaison.fk_idBulletin = bulletin.id AND bulletin.fk_univ = ' . $id_univ . ' ORDER BY departement.lib_depat ASC';

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }

    public static function get_modelename_By($idmodele_relever)
    {

        $sql = 'SELECT * FROM bulletin WHERE id = ' . $idmodele_relever;

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }
    public static function get_B_li_bilanNivFil_By($id_bilan, $id_niveau, $id_filiere, $id_univ)
    {

        $sql = 'SELECT * FROM 
        (SELECT bulletin_lier_BilanNivFiliere.* FROM bulletin_lier_BilanNivFiliere WHERE bulletin_lier_BilanNivFiliere.fk_bul_bilan = ' . $id_bilan . ' AND bulletin_lier_BilanNivFiliere.fk_niveau = ' . $id_niveau . ' AND bulletin_lier_BilanNivFiliere.fk_filiere = ' . $id_filiere . '  AND bulletin_lier_BilanNivFiliere.fk_iduniv = ' . $id_univ . ')tmp_liebul 
        INNER JOIN (SELECT matiere.id_matiere_matiere , matiere.code AS code_mat ,matiere.libele AS lib_mat   FROM matiere WHERE matiere.fk_iduniv=' . $id_univ . ')tmp_mat 
        ON tmp_liebul.fk_matiere = tmp_mat.id_matiere_matiere';

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }

    public static function set_bulletin_lier_BilanNivFiliere($fk_bul_bilan, $fk_niveau, $fk_filiere, $fk_matiere, $fk_iduniv)
    {

        $db = static::getDB();
        //fk_bul_bilan	fk_niveau	fk_filiere	fk_matiere	fk_iduniv


        $sql = ' SELECT * FROM bulletin_lier_BilanNivFiliere WHERE fk_bul_bilan= "' . $fk_bul_bilan . '" AND fk_niveau= "' . $fk_niveau . '" AND fk_filiere= "' . $fk_filiere . '" AND fk_matiere= "' . $fk_matiere . '" AND fk_iduniv= "' . $fk_iduniv . '"  LIMIT 1';

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result);

        if (!empty($result)) {
            return 0;
        } else {

            $data = [
                'fk_bul_bilan' => $fk_bul_bilan,
                'fk_niveau' => $fk_niveau,
                'fk_filiere' => $fk_filiere,
                'fk_matiere' => $fk_matiere,
                'fk_iduniv' => $fk_iduniv
            ];
            //var_dump($db);
            $sql = ' INSERT INTO bulletin_lier_BilanNivFiliere (fk_bul_bilan, fk_niveau, fk_filiere, fk_matiere, fk_iduniv) VALUES ( :fk_bul_bilan, :fk_niveau, :fk_filiere, :fk_matiere, :fk_iduniv);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();

            return 1;
        }
    }

    public static function set_delete_lier_BilanNivFiliere($fk_bul_bilan, $fk_niveau, $fk_filiere, $fk_matiere, $fk_iduniv)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM bulletin_lier_BilanNivFiliere WHERE fk_bul_bilan= "' . $fk_bul_bilan . '" AND fk_niveau= "' . $fk_niveau . '" AND fk_filiere= "' . $fk_filiere . '" AND fk_matiere= "' . $fk_matiere . '" AND fk_iduniv= "' . $fk_iduniv . '"  LIMIT 1';

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        //exit();
        if (empty($result) || $result == 0) {
            return 0;
        } else {

            $sql = 'DELETE FROM bulletin_lier_BilanNivFiliere WHERE fk_bul_bilan= ' . $fk_bul_bilan . ' AND fk_niveau= ' . $fk_niveau . ' AND fk_filiere= ' . $fk_filiere . ' AND fk_matiere= ' . $fk_matiere . ' AND fk_iduniv= ' . $fk_iduniv;
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            return 1;
        }
    }


    public static function get_gpreEtudiantsBy($id_group, $fk_iduniv)
    {

        $sql = 'SELECT * FROM (SELECT * FROM eleve_estds_groupe,eleve WHERE eleve.id_eleve_eleve=eleve_estds_groupe.elv_ds_grpe_idelev AND eleve_estds_groupe.elv_ds_grpe_groupe=' . $id_group . ')tmp_elev,personne WHERE personne.type_pers = 1 AND personne.id_type=tmp_elev.id_eleve_eleve AND personne.fk_iduniv=' . $fk_iduniv;

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }


    public static function get_grpeWithBultinInfos_By($id_group, $fk_iduniv)
    {

        $sql = 'SELECT * FROM (SELECT classe.id_departement, classe.libelle,niveau.libelle_niveau,niveau.fk_id_univ ,groupe.* FROM groupe,niveau,classe WHERE groupe.groupe_id=' . $id_group . ' AND groupe.fk_idniveau=niveau.id_niveau AND groupe.groupe_classe=classe.id_classe_classe)tmpf1 
        INNER JOIN
        (SELECT bulletin.libelle_bulletin, departement.lib_depat,bulletin_depart_liaison.* FROM bulletin_depart_liaison,bulletin,departement WHERE bulletin_depart_liaison.fk_idDepart=departement.id_depat AND bulletin_depart_liaison.fk_idBulletin=bulletin.id)tmpf2
        ON
        tmpf1.id_departement = tmpf2.fk_idDepart
        WHERE tmpf1.fk_id_univ = ' . $fk_iduniv;

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }

    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ ANNEE SCOLAIRE µµµµµµµµµµµµµµµµµµµ ANNEE SCOLAIRE PARTIE µµµµµµµµµµµµµµµµµµµµ
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ


    public static function get_anneScol_Partie_By($id_annee)
    {

        $sql = 'SELECT * FROM annee_partie,annee_scolaire WHERE annee_partie.id_anneeScolaire=' . $id_annee . ' AND annee_partie.id_anneeScolaire = annee_scolaire.id_anscol_annee_scolaire';

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }

    public static function setAnneeScolaire($fk_univ)
    {
        //var_dump("post",$_POST);exit();
        $db = static::getDB();

        $cree_anne_scol = htmlspecialchars($_POST["cree_anne_scol"]);
        $cree_anne_scol_dateDebut = htmlspecialchars($_POST["cree_anne_scol_dateDebut"]);
        $cree_anne_scol_dateFin = htmlspecialchars($_POST["cree_anne_scol_dateFin"]);
        $cree_anne_scol_part = (int)(htmlspecialchars($_POST["cree_anne_scol_part"]));
        /* $cree_anne_scol_Part1 = htmlspecialchars($_POST["cree_anne_scol_Part1"]);
        $cree_anne_scol_Part1_dateDebut = htmlspecialchars($_POST["cree_anne_scol_Part1_dateDebut"]);
        $cree_anne_scol_Part1_dateDebut = htmlspecialchars($_POST["cree_anne_scol_Part2_dateFin"]);*/



        $sql = ' SELECT * FROM annee_scolaire WHERE annee_libelle= "' . $cree_anne_scol . '" AND fk_univ =' . $fk_univ . ' AND etat_annee = 1 LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {

            $data = [
                'annee_libelle' => $cree_anne_scol . ' (' . intval($cree_anne_scol_part) . ')',
                'annee_date_debut' => $cree_anne_scol_dateDebut,
                'annee_date_fin' => $cree_anne_scol_dateFin,
                'fk_univ' => $fk_univ
            ];
            //var_dump($db);

            $sql = ' INSERT INTO annee_scolaire (annee_libelle, annee_date_debut, annee_date_fin, fk_univ) VALUES ( :annee_libelle, :annee_date_debut, :annee_date_fin, :fk_univ);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {

                for ($i = 0; $i < $cree_anne_scol_part; $i++) {
                    $data = [
                        'libele_partie' => htmlspecialchars($_POST["cree_anne_scol_Part" . ($i + 1)]),
                        'partie_dateDebut' => htmlspecialchars($_POST["cree_anne_scol_Part" . ($i + 1) . "_dateDebut"]),
                        'partie_dateFin' =>  htmlspecialchars($_POST["cree_anne_scol_Part" . ($i + 1) . "_dateFin"]),
                        'id_anneeScolaire' => $lastid
                    ];
                    $sql = ' INSERT INTO annee_partie (libele_partie, partie_dateDebut, partie_dateFin, id_anneeScolaire) VALUES ( :libele_partie, :partie_dateDebut, :partie_dateFin, :id_anneeScolaire);';
                    $stmt = $db->prepare($sql);
                    $result = $stmt->execute($data);
                }
                unset($_POST);
                return 1;
            } else {
                return -1;
            }
        } else {
            return 0;
        }
    }

    public static function getAnneeScolaire()
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM annee_scolaire INNER JOIN annee_partie ON annee_scolaire.id_anscol_annee_scolaire =annee_partie.id_anneeScolaire ORDER BY annee_scolaire.id_anscol_annee_scolaire ASC';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public static function getAnneeScolaireAllBy($fk_univ)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM (SELECT * FROM annee_scolaire WHERE fk_univ = ' . $fk_univ . ')annee_scolaire  INNER JOIN annee_partie ON annee_scolaire.id_anscol_annee_scolaire =annee_partie.id_anneeScolaire ORDER BY annee_scolaire.id_anscol_annee_scolaire ASC';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_anneeBy($fk_univ)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM annee_scolaire WHERE  fk_univ= ' . $fk_univ . ' AND etat_annee = 1 ORDER by annee_libelle DESC';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function get_idanneeByY()
    {

        $db = static::getDB();
        $datelib = (date('Y') - 1) . "-" . date("Y");
        $sql = 'SELECT id_anscol_annee_scolaire FROM annee_scolaire WHERE annee_libelle = "' . $datelib . '" LIMIT 1';
        //var_dump($login,$pass,$sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result[0]['id_anscol_annee_scolaire']);exit;
        if (empty($result) || $result == 0) {

            return 0;
        } else {
            return $result[0]['id_anscol_annee_scolaire'];
        }
    }
  
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ Fonction global µµµµµµµµµµµµµµµµµµµ emploi du temps µµµµµµµµµµµµµµµµµµµµ
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ
    //	id_horaires	debut_h	fin_h	infos_h	
    public static function set_insertSQL($table,$tb_infos, $tb_conditions){
        $db = static::getDB();

        //var_dump($table,$tb_infos, $tb_conditions);//exit;
        $nbr_tb_conditions = count($tb_conditions);
        $var_i = 1 ;
        $nbr_tb_infos = count($tb_infos);

        if ($nbr_tb_conditions>0) { 
            
            $conditions=' WHERE  '; 
                
            foreach ($tb_conditions as $key => $value) {
                
                if ($var_i == $nbr_tb_conditions ) { $conditions= $conditions.' '.$key.' = "'.$value.'"' ; }
                else { $conditions= $conditions.' '.$key.' = "'.$value.'" AND ' ; }
                $var_i = $var_i +1;
            }
        
        }else { $conditions='   '; }

        $sql = ' SELECT * FROM '.$table.'  '.$conditions.' LIMIT 1';
        //var_dump('sql 1 =',$sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $champ1 = '';
        $champ2 = '';

        if ((empty($result) || $result == 0) && ($nbr_tb_infos > 0) ) {

            foreach ($tb_infos as $key => $value) {
                $data[$key] = $value;
                if ($champ1 == '') { $champ1= $key.' ' ;  }
                else {  $champ1= $champ1.' ,'.$key.' ' ; }

                if ($champ2 == '') { $champ2 = ' :'.$key.' ' ; }
                else {  $champ2= $champ2.' , :'.$key.' ' ; }
                
            }
            
            //var_dump($db);

            $sql = ' INSERT INTO '.$table.'  ('.$champ1.') VALUES ( '.$champ2.');';
            //print_r($sql);//exit;
            //var_dump('sql 2=',$sql);
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) { return 1; } 
            else { return -1;  }
        } 
        else { return 0; }


    }
    public static function get_selectSQL_ALL_by($table, $tb_conditions)
    {
        //var_dump($tb_conditions);exit();
        $nbr_tb_conditions = count($tb_conditions);
        $var_i = 1 ;

        if ($nbr_tb_conditions>0) { 
            
            $conditions=' WHERE  '; 
                
            foreach ($tb_conditions as $key => $value) {
                
                if ($var_i == $nbr_tb_conditions ) { $conditions= $conditions.' '.$key.' = "'.$value.'"' ; }
                else { $conditions= $conditions.' '.$key.' = "'.$value.'" AND ' ; }
                $var_i = $var_i +1;
            }
        
        }else { $conditions='   '; }

        $sql = ' SELECT * FROM '.$table.'  '.$conditions.' ;';

        //print_r($sql);echo "\n";

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }

    public static function set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions)
    {

        $db = static::getDB();

        //var_dump($table,$tb_infos, $tb_conditions);//exit;
        $nbr_tb_conditions = count($tb_conditions);
        $var_i = 1 ;
        $nbr_tb_infos = count($tb_infos);

        if ($nbr_tb_conditions>0) { 
            
            $conditions=' WHERE  '; 
                
            foreach ($tb_conditions as $key => $value) {
                
                if ($var_i == $nbr_tb_conditions ) { $conditions= $conditions.' '.$key.' = "'.$value.'"' ; }
                else { $conditions= $conditions.' '.$key.' = "'.$value.'" AND ' ; }
                $var_i = $var_i +1;
            }
        
        }else { $conditions='   '; }

        $sql = ' SELECT * FROM '.$table.'  '.$conditions.' LIMIT 1';
        //var_dump($sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data ='';

        if (empty($result) || $result == 0 || $nbr_tb_infos == 0) {
            return 0;
        } else {
            //var_dump($db);
            //. $table . '="' . $valeur . '"
            foreach ($tb_infos as $key => $value) {

                
                if ($data == '') { $data= $key.' = "'.$value.'"  ';  }
                else {  $data= $data.' ,'.$key.' = "'.$value.'"  '; }
                
            }

            $sql = 'UPDATE '.$table.' SET   '.$data.'  '.$conditions.';';
            //print_r($sql);
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            return 1;
        }
    }

    public static function set_deleteSQL_ALL_by($table,$tb_conditions)
    {

        $db = static::getDB();

        //var_dump($table,$tb_infos, $tb_conditions);//exit;
        $nbr_tb_conditions = count($tb_conditions);
        $var_i = 1 ;

        if ($nbr_tb_conditions>0) { 
            
            $conditions=' WHERE  '; 
                
            foreach ($tb_conditions as $key => $value) {
                
                if ($var_i == $nbr_tb_conditions ) { $conditions= $conditions.' '.$key.' = "'.$value.'"' ; }
                else { $conditions= $conditions.' '.$key.' = "'.$value.'" AND ' ; }
                $var_i = $var_i +1;
            }
        
        }else { $conditions='   '; }

        $sql = ' SELECT * FROM '.$table.'  '.$conditions.' LIMIT 1';
        //var_dump($sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0 || $nbr_tb_conditions == 0) {
            return 0;
        } else {

            $sql = 'DELETE FROM '.$table.'  '.$conditions.';';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            return 1;
        }
    }

    public static function set_updateSQL_Moyenne_by($fk_id_mat,$fk_id_partAnnee, $fk_id_grpe,$moyenne,$rang){
        $db = static::getDB();

        $sql = 'SELECT * FROM moyenne_matf   WHERE   fk_id_mat = '.$fk_id_mat.' AND  fk_id_partAnnee = '.$fk_id_partAnnee.' AND  fk_id_grpe = '.$fk_id_grpe.' AND  CAST(moy_mat_final AS decimal(18,2)) = CAST('.$moyenne.' AS decimal(18,2))';
        //var_dump($sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0 ) {
            return 0;
        } 
        else {

            $sql = 'UPDATE moyenne_matf SET rang_mat = '.intval($rang).' WHERE fk_id_mat = '.$fk_id_mat.' AND fk_id_partAnnee ='.$fk_id_partAnnee.' AND fk_id_grpe ='.$fk_id_grpe.' AND CAST(moy_mat_final AS decimal(18,2)) = CAST('.$moyenne.' AS decimal(18,2)) ;';
            //print_r($sql);
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            return $result;
        }
    }



    

    public static function get_allactivecompte($id_annescol)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT personne.* FROM personne ,etat_by_annee WHERE personne.id_pers_personne=etat_by_annee.fk_id_personne AND etat_by_annee.fk_idanneescol='.$id_annescol.' AND etat_by_annee.etat_pers=1';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }  

    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ Admin µµµµµµµµµµµµµµµµµµµµµµµ Filiere µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ


    public static function get_filiereNiv_matcoef($id_univ, $id_anneescol, $id_niveau, $id_filiere)
    {

        $sql = 'SELECT tmp_f.*, annee_partie.libele_partie  FROM
        (SELECT tmp_fn_m.*,matiere.code,matiere.libele AS lib_matiere  FROM 
        (SELECT filiere_niveau_matCoef.* 
        FROM filiere_niveau_matCoef
        WHERE 
        filiere_niveau_matCoef.fk_idanneescol=' . $id_anneescol . ' 
        AND filiere_niveau_matCoef.fk_matiere_parent_id_tmp =0 
        AND filiere_niveau_matCoef.fk_niveau_id= ' . $id_niveau . ' 
        AND filiere_niveau_matCoef.fk_filiere_id=' . $id_filiere . ' )tmp_fn_m,matiere
        WHERE
        tmp_fn_m.fk_matiere_id_tmp=matiere.id_matiere_matiere)tmp_f,annee_partie
        WHERE
        tmp_f.fk_part_annee_id_tmp =annee_partie.id_annee_partie
        AND annee_partie.id_anneeScolaire = tmp_f.fk_idanneescol';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            //vide
            //vide
            return  $result;
        } else {
            //vide
            return 0;
        }
        //print_r($sql);

    }

    public static function get_filNiv_matcoef_WithMP($id_univ, $id_anneescol, $id_niveau, $id_filiere)
    {

        $sql = 'SELECT * FROM (
            SELECT filiere_niveau_matCoef.* ,matiere.code,matiere.libele AS lib_matiere , annee_partie.libele_partie FROM filiere_niveau_matCoef, annee_partie,matiere 

            WHERE 
            filiere_niveau_matCoef.fk_idanneescol=' . $id_anneescol . ' AND 
            filiere_niveau_matCoef.fk_matiere_parent_id_tmp <> 0 AND 
            filiere_niveau_matCoef.fk_filiere_id=' . $id_filiere . ' AND 
            filiere_niveau_matCoef.fk_matiere_id_tmp=matiere.id_matiere_matiere AND 
            filiere_niveau_matCoef.fk_part_annee_id_tmp =annee_partie.id_annee_partie AND 
            filiere_niveau_matCoef.fk_niveau_id=' . $id_niveau . '
            
            )tmp_matcoef 
        INNER JOIN
        (SELECT matiere.id_matiere_matiere AS matp_id , matiere.code AS matp_code , matiere.libele AS matp_lib FROM matiere WHERE matiere.fk_iduniv = ' . $id_univ . ' )tmp_matiere
        WHERE tmp_matcoef.fk_matiere_parent_id_tmp = tmp_matiere.matp_id ';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            //vide
            //vide
            return  $result;
        } else {
            //vide
            return 0;
        }
        //print_r($sql);

    }


    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ Admin µµµµµµµµµµµµµµµµµµµµµµµ ROLE µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ
    //	(admintab)  id_admin_admin   id_role   etat
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ
    public static function update_role($id_admin_admin, $id_role)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM admintab WHERE id_admin_admin= ' . $id_admin_admin . '  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);
        if (empty($result) || $result == 0) {
            return 0;
        } else {

            $data = [
                'id_admin_admin' => $id_admin_admin,
                'id_role' => $id_role
            ];
            //var_dump($db);
            $sql = 'UPDATE admintab SET id_role =:id_role  WHERE id_admin_admin = :id_admin_admin;';

            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            return 1;
        }
    }

    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ Admin µµµµµµµµµµµµµµµµµµµµµµµ Groupe µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ

    //::::: SQL :::::: GROUPE  ¨¨¨£¨£^§/..?.?;;
    /* Table :: groupe
        groupe_id
        groupe_libelle
        fk_idniveau
        groupe_annee
        groupe_classe
        groupe_etat
    */


    public static function get_grp_MatRepartie_By($id_univ, $id_groupe, $id_anneescol)
    {

        $sql = 'SELECT * FROM 
        (SELECT tmp_grp.*,matiere.* FROM
        (SELECT * FROM groupe_matiere_coef WHERE groupe_matiere_coef.groupe_id_tmp=' . $id_groupe . ' AND groupe_matiere_coef.matiere_parent_id_tmp=0)tmp_grp,matiere
        WHERE tmp_grp.matiere_id_tmp = matiere.id_matiere_matiere)tmp_f
        INNER JOIN 
        (SELECT annee_partie.id_annee_partie,annee_partie.libele_partie FROM annee_partie WHERE id_anneeScolaire = ' . $id_anneescol . ')tmp_anepart
        ON tmp_f.part_annee_id_tmp=tmp_anepart.id_annee_partie
        WHERE tmp_f.fk_iduniv=' . $id_univ;

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }
    public static function get_grp_MatRepartie_WithMP_By($id_univ, $id_groupe, $id_anneescol)
    {

        $sql = 'SELECT tmp_a.*, tmp_mp.MatP_lib, tmp_mp.MatP_code  FROM
        (SELECT * FROM 
        (SELECT tmp_grp.*,matiere.* FROM
        (SELECT * FROM groupe_matiere_coef WHERE groupe_matiere_coef.groupe_id_tmp=' . $id_groupe . ' AND groupe_matiere_coef.matiere_parent_id_tmp <>0)tmp_grp,matiere
        WHERE tmp_grp.matiere_id_tmp = matiere.id_matiere_matiere)tmp_f
        INNER JOIN 
        (SELECT annee_partie.id_annee_partie,annee_partie.libele_partie FROM annee_partie WHERE id_anneeScolaire = ' . $id_anneescol . ')tmp_anepart
        ON tmp_f.part_annee_id_tmp=tmp_anepart.id_annee_partie
        WHERE tmp_f.fk_iduniv=' . $id_univ . ')tmp_a, (SELECT matiere.libele AS MatP_lib,matiere.code AS MatP_code, id_matiere_matiere AS MatP_id FROM matiere)tmp_mp
        WHERE tmp_mp.MatP_id = tmp_a.matiere_parent_id_tmp  ORDER BY tmp_a.matiere_parent_id_tmp DESC';

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }

 
    public static function setGroupe()
    {

        $db = static::getDB();
        //var_dump($_SESSION);exit();
        $annee_id = intval(htmlspecialchars($_POST["annee_id"]));
        $classe_id = intval(htmlspecialchars($_POST["classe_id"]));
        $nomgroupe = htmlspecialchars($_POST["nomgroupe"]);
        $niveaugroupe = intval(htmlspecialchars($_POST["niveaugroupe"]));


        //GET UNIV ID
        $sql_univ = 'SELECT departement.fk_iduniv FROM classe,departement WHERE classe.id_classe_classe=' . $classe_id . ' AND classe.id_departement=departement.id_depat';
        $stmt_univ = $db->query($sql_univ);
        $result_univ = $stmt_univ->fetchAll(PDO::FETCH_ASSOC);

        $id_univ = intval($result_univ[0]['fk_iduniv']);

        //Admin::setGroupeMat_f($matiere_id_tmp, $coeficient_tmp, $matiere_parent_id_tmp, $groupe_id_tmp, $part_annee_id_tmp, $mat_credit);



        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql = ' SELECT * FROM groupe WHERE groupe_annee= "' . $annee_id . '" AND groupe_classe= "' . $classe_id . '" AND groupe_libelle= "' . $nomgroupe . '" AND fk_idniveau= "' . $niveaugroupe . '" LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();

        if (empty($result) || $result == 0) {

            $data = [
                'groupe_libelle' => $nomgroupe,
                'fk_idniveau' => $niveaugroupe,
                'groupe_annee' => $annee_id,
                'groupe_classe' => $classe_id
            ];
            //var_dump($db);
            $sql = ' INSERT INTO groupe (groupe_libelle,  fk_idniveau ,groupe_annee ,groupe_classe) VALUES ( :groupe_libelle,:fk_idniveau, :groupe_annee, :groupe_classe);';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                $groupe_id_tmp = $lastid;

                $get_filiereNiv_matcoef = Admin::get_filiereNiv_matcoef($id_univ, $annee_id, $niveaugroupe, $classe_id);
                $get_filNiv_matcoef_WithMP = Admin::get_filNiv_matcoef_WithMP($id_univ, $annee_id, $niveaugroupe, $classe_id);


                foreach ($get_filiereNiv_matcoef as $key => $value) {
                    $matiere_id_tmp = $value['fk_matiere_id_tmp'];
                    $coeficient_tmp = $value['coeficient_tmp'];
                    $matiere_parent_id_tmp = $value['fk_matiere_parent_id_tmp'];
                    $part_annee_id_tmp = $value['fk_part_annee_id_tmp'];
                    $mat_credit = $value['credit_tmp'];

                    Admin::setGroupeMat_f($matiere_id_tmp, $coeficient_tmp, $matiere_parent_id_tmp, $groupe_id_tmp, $part_annee_id_tmp, $mat_credit);
                }

                foreach ($get_filNiv_matcoef_WithMP as $key => $value) {
                    $matiere_id_tmp = $value['fk_matiere_id_tmp'];
                    $coeficient_tmp = $value['coeficient_tmp'];
                    $matiere_parent_id_tmp = $value['fk_matiere_parent_id_tmp'];
                    $part_annee_id_tmp = $value['fk_part_annee_id_tmp'];
                    $mat_credit = $value['credit_tmp'];

                    Admin::setGroupeMat_f($matiere_id_tmp, $coeficient_tmp, $matiere_parent_id_tmp, $groupe_id_tmp, $part_annee_id_tmp, $mat_credit);
                }


                return 1;
            } else {

                return -1;
            }
        } else {

            return $result;
        }
    }

    public static function getGroupeBy($annee_id)
    {

        $db = static::getDB();
        //var_dump($_POST);exit();
        if (isset($_POST["annee_id"])) {
            $annee_id = intval(htmlspecialchars($_POST["annee_id"]));
        }

        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql = ' SELECT niveau.libelle_niveau,classe.libelle AS class_lib,groupe.* FROM groupe,niveau,classe WHERE groupe.groupe_etat = 1 AND groupe.groupe_annee=' . $annee_id . ' AND groupe.groupe_classe=classe.id_classe_classe AND groupe.fk_idniveau = niveau.id_niveau';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();
        return $result;
    }

    public static function get_univALLgrpeBy($annee_id, $id_univ)
    {

        //	groupe_id   groupe_libelle   fk_idniveau   groupe_annee    groupe_classe   groupe_etat
        $sql = 'SELECT * FROM (SELECT groupe.*,niveau.id_niveau,niveau.fk_id_univ FROM groupe,niveau WHERE groupe.fk_idniveau=niveau.id_niveau)tmp_grp WHERE tmp_grp.fk_id_univ=' . $id_univ . ' AND tmp_grp.groupe_annee =' . $annee_id . ' AND tmp_grp.groupe_etat=1';
        $result = Admin::sql_query_get($sql);
        //print_r($sql);//exit();
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }

    public static function get_elevegroupesBy($id_eleve, $annee_id)
    {

        $db = static::getDB();
        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql = 'SELECT * FROM (SELECT * FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev = ' . $id_eleve . ' AND elv_ds_grpe_etat = 1)tmp_elvdsgrp INNER JOIN (SELECT groupe.*,niveau.*,classe.libelle FROM groupe,niveau,classe WHERE groupe.groupe_annee=' . $annee_id . ' AND groupe.groupe_etat=1 AND niveau.id_niveau = groupe.fk_idniveau AND classe.id_classe_classe = groupe.groupe_classe)tmp_grpe ON tmp_elvdsgrp.elv_ds_grpe_groupe = tmp_grpe.groupe_id';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();
        return $result;
    }

    public static function getAllGroupe($id_univ)
    {

        $db = static::getDB();
        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql = 'SELECT * FROM (
             SELECT * FROM (SELECT * FROM (SELECT * FROM groupe,niveau WHERE groupe_etat = 1 AND groupe.fk_idniveau = niveau.id_niveau)grp INNER JOIN (SELECT id_anscol_annee_scolaire,annee_libelle FROM annee_scolaire WHERE etat_annee = 1)anne ON grp.groupe_annee = anne.id_anscol_annee_scolaire)grpann INNER JOIN (SELECT id_classe_classe,libelle FROM classe WHERE etat = 1)clas on grpann.groupe_classe = clas.id_classe_classe
             )tmp_final
            WHERE tmp_final.fk_id_univ=' . $id_univ;
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //print_r($sql);
        //var_dump($sql,$stmt,$result);exit();
        return $result;
    }
    public static function get_allFilliere()
    {

        $db = static::getDB();
        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql = ' SELECT id_classe_classe,libelle FROM classe WHERE etat = 1 ';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();
        return $result;
    }

    public static function get_grpeliste_By($id_univ,$id_annee)
    {

        $db = static::getDB();
        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql = 'SELECT * FROM groupe,niveau WHERE groupe.groupe_etat=1 AND  groupe_annee='.$id_annee.' AND groupe.fk_idniveau = niveau.id_niveau AND  groupe.groupe_classe IN (SELECT classe.id_classe_classe FROM departement,classe WHERE departement.fk_iduniv='.$id_univ.' AND departement.id_depat=classe.id_departement AND departement.etat_depat=1 AND classe.etat=1)';
        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }
    

    public static function get_allGroupe_active($id_filli)
    {

        $db = static::getDB();
        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql = ' SELECT groupe_id,groupe_libelle FROM (SELECT * FROM (SELECT * FROM (SELECT * FROM groupe WHERE groupe_etat = 1)grp INNER JOIN (SELECT id_anscol_annee_scolaire,annee_libelle FROM annee_scolaire WHERE etat_annee = 1)anne ON grp.groupe_annee = anne.id_anscol_annee_scolaire)grpann INNER JOIN (SELECT id_classe_classe,libelle FROM classe WHERE etat = 1)clas on grpann.groupe_classe = clas.id_classe_classe)tmp_ok_grpby WHERE id_classe_classe = ' . $id_filli;
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();
        return $result;
    }

    public static function get_allidGrp_filiereGrpeBy($id_filiere, $id_niveau, $annee_id)
    {
        $db = static::getDB();
        //groupe_id 	groupe_libelle 	groupe_annee 	groupe_classe 	groupe_etat 
        $sql = 'SELECT groupe_id FROM groupe WHERE fk_idniveau=' . $id_niveau . ' AND groupe_annee=' . $annee_id . ' AND groupe_classe=' . $id_filiere;
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();
        return $result;
    }

    //::::: SQL :::::: MATIERE
    //2
    public static function setGroupeMat_f($matiere_id_tmp, $coeficient_tmp, $matiere_parent_id_tmp, $groupe_id_tmp, $part_annee_id_tmp, $mat_credit)
    {

        $db = static::getDB();
        //groupe_matiere_coef_id 	matiere_id_tmp  credit_tmp 	coeficient_tmp 	matiere_parent_id_tmp 	groupe_id_tmp 	part_annee_id_tmp 
        $sql = ' SELECT * FROM groupe_matiere_coef WHERE matiere_id_tmp= "' . $matiere_id_tmp . '" AND matiere_parent_id_tmp= "' . $matiere_parent_id_tmp . '" AND groupe_id_tmp= "' . $groupe_id_tmp . '" AND part_annee_id_tmp= "' . $part_annee_id_tmp . '"  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);exit();

        if (empty($result) || $result == 0) {

            $data = [
                'matiere_id_tmp' => $matiere_id_tmp,
                'coeficient_tmp' => $coeficient_tmp,
                'credit_tmp' => $mat_credit,
                'matiere_parent_id_tmp' => $matiere_parent_id_tmp,
                'groupe_id_tmp' => $groupe_id_tmp,
                'part_annee_id_tmp' => $part_annee_id_tmp
            ];
            //var_dump($db);
            $sql = ' INSERT INTO groupe_matiere_coef (matiere_id_tmp, coeficient_tmp, credit_tmp,matiere_parent_id_tmp ,groupe_id_tmp , part_annee_id_tmp) VALUES ( :matiere_id_tmp, :coeficient_tmp, :credit_tmp, :matiere_parent_id_tmp , :groupe_id_tmp , :part_annee_id_tmp );';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                return 1;
            } else {

                return 0;
            }
        } else {

            return 0;
        }
    }
    //1 vers 2
    public static function setGroupeMat()
    {
        $state = 1;
        $db = static::getDB();
        //var_dump($_POST);exit();
        $groupe_id = intval(htmlspecialchars($_POST["groupe_id"]));
        $part_annee = intval(htmlspecialchars($_POST["part_annee"]));
        $matiere = intval(htmlspecialchars($_POST["matiere"]));
        $mat_coef = intval(htmlspecialchars($_POST["mat_coef"]));
        $mat_credit = intval(htmlspecialchars($_POST["mat_credit"]));


        if (isset($_POST["sous_mat1"])) {

            $sous_matiere = intval(htmlspecialchars($_POST["sous_mat1"]));
            $state  = Admin::setGroupeMat_f($matiere, $mat_coef, $sous_matiere, $groupe_id, $part_annee, $mat_credit);
            //$sous_matiere, $sous_mat_coef,$matiere, $groupe_id, $part_annee

        } else {
            $state  = Admin::setGroupeMat_f($matiere, $mat_coef, 0, $groupe_id, $part_annee, $mat_credit);
        }


        return $state;
    }

    //::::: SQL :::::: MATIERE
    //2

    // TABLE : filiere_niveau_matCoef
    //	filiere_niveau_matCoef_id	fk_matiere_id_tmp	fk_matiere_parent_id_tmp	fk_filiere_id	fk_niveau_id	fk_part_annee_id_tmp    fk_idanneescol 	coeficient_tmp	credit_tmp

    public static function set_filiere_matcoef($matiere_id_tmp, $coeficient_tmp, $matiere_parent_id_tmp, $fk_niveau_id, $fk_filiere_id, $part_annee_id_tmp, $fk_idanneescol, $mat_credit)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM filiere_niveau_matCoef WHERE fk_matiere_id_tmp= ' . $matiere_id_tmp . ' AND  fk_filiere_id= ' . $fk_filiere_id . ' AND fk_part_annee_id_tmp= ' . $part_annee_id_tmp . ' AND fk_idanneescol= ' . $fk_idanneescol . '  AND fk_niveau_id= ' . $fk_niveau_id . ' LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);
 
        if (empty($result) || $result == 0) {

            $data = [
                'fk_matiere_id_tmp' => $matiere_id_tmp,
                'fk_matiere_parent_id_tmp' => $matiere_parent_id_tmp,
                'fk_filiere_id' => $fk_filiere_id,
                'fk_niveau_id' => $fk_niveau_id,
                'fk_part_annee_id_tmp' => $part_annee_id_tmp,
                'fk_idanneescol' => $fk_idanneescol,
                'coeficient_tmp' => $coeficient_tmp,
                'credit_tmp' => $mat_credit
            ];
            //var_dump($db);
            $sql = ' INSERT INTO filiere_niveau_matCoef (fk_matiere_id_tmp, fk_matiere_parent_id_tmp, fk_filiere_id,fk_niveau_id ,fk_part_annee_id_tmp , fk_idanneescol ,coeficient_tmp, credit_tmp) VALUES ( :fk_matiere_id_tmp, :fk_matiere_parent_id_tmp, :fk_filiere_id, :fk_niveau_id, :fk_part_annee_id_tmp , :fk_idanneescol , :coeficient_tmp, :credit_tmp );';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ($result == TRUE) {
                return 1;
            } else {

                return 0;
            }
        } else {

            return 0;
        }
    }


    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ ELEVE µµµµµµµµµµµµµµµµµµµµµµµ PERSONNE µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ
    //µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµ µµµµµµ µµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµµ

    public static function get_etudiPers_Infos_By($id_univ, $id_etudiant)
    {

        $sql = 'SELECT * FROM eleve,personne WHERE personne.fk_iduniv=' . $id_univ . ' AND personne.id_type=' . $id_etudiant . ' AND personne.type_pers=1 AND personne.id_type=eleve.id_eleve_eleve';

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }

    public static function detele_etudiant_enattente_inscript($id_personne, $fk_iduniv)
    {

        $db = static::getDB();
        //groupe_matiere_coef_id 	matiere_id_tmp 	coeficient_tmp 	matiere_parent_id_tmp 	groupe_id_tmp 	part_annee_id_tmp 
        $sql = ' SELECT * FROM personne WHERE id_pers_personne= ' . $id_personne . ' AND type_pers= 1 AND fk_iduniv= ' . $fk_iduniv . '  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        if (empty($result) || $result == 0) {
            var_dump($result);
            return 0;
        } elseif (isset($result[0]['id_type'])) {
            //var_dump($result[0]['id_pers_personne'],$result[0]['id_type']);exit();

            $sql = 'DELETE FROM personne WHERE id_pers_personne = ' . $id_personne;
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();

            $sql = 'DELETE FROM eleve WHERE id_eleve_eleve = ' . intval($result[0]['id_type']);
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            return 1;
        } else {
            return 0;
        }
    }

    public static function set_updateTbleeleve_etudiantinfos($id_etudiant, $table, $valeur)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM eleve WHERE id_eleve_eleve= ' . $id_etudiant . '  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        if (empty($result) || $result == 0) {
            return 99;
        } else {
            //var_dump($db);
            $sql = 'UPDATE eleve SET ' . $table . '="' . $valeur . '"  WHERE id_eleve_eleve =' . $id_etudiant . ' ;';
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            return 1;
        }
    }

    public static function set_updateTblePers_etudiantinfos($id_etudiant, $table, $valeur)
    {

        $db = static::getDB();

        $sql = ' SELECT * FROM personne WHERE type_pers=1 AND id_type='. $id_etudiant .'  LIMIT 1';
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($sql,$stmt,$result);

        if (empty($result) || $result == 0) {
            return 99;
        } else {
            //var_dump($db);
            $sql = 'UPDATE personne SET ' . $table . '="' . $valeur . '"  WHERE id_type =' . $id_etudiant . ' AND type_pers=1 ;';
            //var_dump($sql);
            $stmt = $db->prepare($sql);
            $result = $stmt->execute();
            return 1;
        }
    }

    public static function get_etud_preinscriptionBy($id_etudiant)
    {

        $sql = 'SELECT * FROM
        (SELECT * FROM preinscription  INNER JOIN niveau ON preinscription.niveau = niveau.id_niveau WHERE preinscription.id_eleve='.$id_etudiant.' )tmp_tbe
        INNER JOIN classe ON tmp_tbe.classe = classe.id_classe_classe';

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }
    public static function get_etud_parcoursBy($id_etudiant)
    {

        $sql = 'SELECT tmp_fb.*,classe.libelle FROM
        (SELECT tmp_fa.*,niveau.libelle_niveau FROM
        (SELECT * FROM
            (SELECT elv_ds_grpe_idelev ,elv_ds_grpe_groupe ,eleve_estds_groupe_dateajout FROM eleve_estds_groupe WHERE elv_ds_grpe_idelev='.$id_etudiant.')tmp_elvgrp
        INNER JOIN
            (SELECT groupe.groupe_id, groupe.groupe_libelle, groupe.fk_idniveau, groupe.groupe_classe, groupe.groupe_annee, groupe.groupe_etat, annee_scolaire.annee_libelle  
            FROM   groupe, annee_scolaire WHERE groupe.groupe_annee=annee_scolaire.id_anscol_annee_scolaire )tmp_anee
        ON tmp_elvgrp.elv_ds_grpe_groupe = tmp_anee.groupe_id)tmp_fa,niveau  
        WHERE tmp_fa.fk_idniveau = niveau.id_niveau)tmp_fb , classe 
        WHERE tmp_fb.groupe_classe = classe.id_classe_classe';

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }

    public static function get_nbrepers_by($id_type,$etat,$fk_iduniv)
    {
        $sql = 'SELECT COUNT(id_pers_personne) AS nbre FROM personne WHERE type_pers='.$id_type.' AND fk_iduniv='.$fk_iduniv.' AND etat_pers = '.$etat;

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  intval($result[0]['nbre']);
        } else {
            return 0;
        }
    } 
    public static function get_persconect_by($type,$fk_iduniv)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT MAX(tmp_userconnec.conex_etat) AS etat_conex , MAX(tmp_userconnec.conex_date_heure) AS d_h_conex , tmp_userconnec.* FROM (
            SELECT tmp_conex.*, personne.nom_prenom,personne.type_pers FROM (
                SELECT connexion.* FROM connexion WHERE  fk_iduniv='.$fk_iduniv.' AND  CAST(conex_date_heure AS DATE)  >= (CAST(NOW() AS DATE) - 7) )tmp_conex, personne 
                WHERE personne.id_pers_personne=tmp_conex.conex_id_personne AND personne.type_pers='.$type.' ORDER BY nom_prenom
        )tmp_userconnec GROUP BY tmp_userconnec.nom_prenom ORDER BY d_h_conex DESC';

        //print_r($sql);echo "\n";

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }
    public static function get_useronline_by($type,$fk_iduniv)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT connexion.* ,personne.nom_prenom,personne.contact FROM connexion,personne WHERE connexion.conex_etat =1 AND connexion.fk_iduniv='.$fk_iduniv.' AND connexion.conex_id_personne=personne.id_pers_personne AND personne.type_pers='.$type.' ORDER BY `personne`.`nom_prenom` ASC';

        //print_r($sql);echo "\n";

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            foreach ($result as $key => $value) {
                $ua=Admin::getBrowser($value['conex_navigateur']);
                $yourbrowser= $ua['name'] . " " . $ua['version'] . " Sur " .$ua['platform'] ;
                $result[$key]['navigateur']=$yourbrowser;
            }
            return  $result;
        } else {
            return 0;
        }
    }
    public static function get_allElv_grpmat_coef_by($id_groupe,$id_matiere,$id_annee)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM
        (SELECT * FROM
        (SELECT eleve_estds_groupe.elv_ds_grpe_idelev FROM eleve_estds_groupe WHERE eleve_estds_groupe.elv_ds_grpe_groupe='.$id_groupe.' AND eleve_estds_groupe.elv_ds_grpe_etat=1)tmp_gp_elev
        LEFT JOIN
        (SELECT * FROM moyenne,(SELECT annee_partie.id_annee_partie , annee_partie.libele_partie FROM annee_partie WHERE annee_partie.id_anneeScolaire = '.$id_annee.')tmp 
        WHERE moyenne.fk_part_annee=tmp.id_annee_partie AND id_groupe='.$id_groupe.' AND id_matiere='.$id_matiere.')tmp_moy
        ON tmp_gp_elev.elv_ds_grpe_idelev=tmp_moy.id_eleve)tm_f_note
        INNER JOIN
        (SELECT eleve.id_eleve_eleve, eleve.matricule,eleve.statut_affecter,eleve.statut_redoublant,personne.nom_prenom,personne.sexe,personne.date_naiss,personne.lieu_naiss FROM eleve,personne WHERE personne.type_pers=1 AND eleve.id_eleve_eleve = personne.id_type)tmp_f_per
        ON tm_f_note.elv_ds_grpe_idelev = tmp_f_per.id_eleve_eleve';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }

    public static function get_eleve_all_moy($id_elev,$id_annee,$id_grpe)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM
                (SELECT tmp_groupe_matiere_coef.matiere_id_tmp,matiere.libele,matiere.code FROM (SELECT * FROM groupe_matiere_coef WHERE  groupe_matiere_coef.groupe_id_tmp='.$id_grpe.'  )tmp_groupe_matiere_coef,matiere WHERE tmp_groupe_matiere_coef.matiere_id_tmp = matiere.id_matiere_matiere GROUP BY tmp_groupe_matiere_coef.matiere_id_tmp)tmp_coef
            LEFT JOIN
                (SELECT tmp_a.*,tmp_part.libele_partie  FROM 
                    (SELECT * FROM moyenne WHERE moyenne.id_eleve='.$id_elev.' AND  moyenne.id_groupe='.$id_grpe.')tmp_a
                INNER JOIN 
                    (SELECT annee_partie.libele_partie,annee_partie.id_annee_partie FROM annee_partie WHERE annee_partie.id_anneeScolaire='.$id_annee.')tmp_part
                ON tmp_a.fk_part_annee=tmp_part.id_annee_partie)tmp_moy  
            ON  tmp_coef.matiere_id_tmp=tmp_moy.id_matiere';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }

    public static function get_all_gpr_matETprof($id_group)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM
        (SELECT groupe_matiere_coef.matiere_id_tmp, groupe_matiere_coef.groupe_id_tmp,matiere.code,matiere.libele FROM groupe_matiere_coef,matiere WHERE  groupe_matiere_coef.groupe_id_tmp='.$id_group.' AND groupe_matiere_coef.matiere_id_tmp=matiere.id_matiere_matiere)tmp_mat
        LEFT JOIN
        (SELECT prof_classe.*,personne.nom_prenom FROM prof_classe,personne WHERE prof_classe.id_groupe='.$id_group.' AND personne.type_pers=2 AND prof_classe.id_prof=personne.id_type AND etat_prof_classe=1)tmp_prof
        ON tmp_mat.matiere_id_tmp = tmp_prof.id_mat WHERE tmp_mat.groupe_id_tmp = tmp_prof.id_groupe';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }
    public static function get_elevmoy_by($id_eleve,$id_mat,$id_group,$part_anneeid)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT MAX(moyenne) AS moyenne FROM moyenne WHERE moyenne.id_groupe='.$id_group.' AND moyenne.id_eleve='.$id_eleve.' AND moyenne.id_matiere='.$id_mat.' AND moyenne.fk_part_annee='.$part_anneeid;
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result[0];
        } else {
            return 0;
        }
    }

    public static function get_mat_grp_prof($id_mat,$id_group)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT UPPER(personne.nom_prenom) AS prof_nom,personne.sexe,prof_classe.* FROM prof_classe,personne WHERE personne.type_pers=2 AND prof_classe.id_prof=personne.id_type AND prof_classe.etat_prof_classe=1 AND prof_classe.id_groupe='.$id_group.' AND prof_classe.id_mat='.$id_mat;
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result[0];
        } else {
            return 0;
        }
    }
    //get nbre de credit et coef by periode
    public static function get_grpinfo_tmat_tcoef($id_group)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT tmp_tmat.nbremat, tmp_coefcred.* FROM
        (SELECT COUNT(tmp_info.matiere_id_tmp) AS nbremat,tmp_info.groupe_id_tmp,tmp_info.libele_partie,tmp_info.part_annee_id_tmp FROM
        (SELECT groupe_matiere_coef.*,annee_partie.libele_partie FROM groupe_matiere_coef,annee_partie WHERE groupe_id_tmp='.$id_group.' AND groupe_matiere_coef.part_annee_id_tmp=annee_partie.id_annee_partie)tmp_info GROUP BY tmp_info.libele_partie)tmp_tmat,
        (SELECT SUM(tmp_info.coeficient_tmp) AS nbrecoef,SUM(tmp_info.credit_tmp) AS nbrecredit,tmp_info.groupe_id_tmp,tmp_info.libele_partie,tmp_info.part_annee_id_tmp FROM (SELECT groupe_matiere_coef.*,annee_partie.libele_partie FROM groupe_matiere_coef,annee_partie WHERE groupe_id_tmp='.$id_group.' AND groupe_matiere_coef.part_annee_id_tmp=annee_partie.id_annee_partie)tmp_info GROUP BY tmp_info.libele_partie)tmp_coefcred
        WHERE tmp_coefcred.groupe_id_tmp=tmp_tmat.groupe_id_tmp AND tmp_coefcred.part_annee_id_tmp=tmp_tmat.part_annee_id_tmp ORDER BY tmp_coefcred.libele_partie DESC';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }   
    //get effectif by sexe
    public static function get_grpinfo_tfm($id_group)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT COUNT(elv_ds_grpe_idelev) AS total,tmp_eleve.sexe FROM (SELECT eleve_estds_groupe.*,personne.sexe FROM eleve_estds_groupe,personne WHERE eleve_estds_groupe.elv_ds_grpe_groupe='.$id_group.' AND eleve_estds_groupe.elv_ds_grpe_etat=1 AND eleve_estds_groupe.elv_ds_grpe_idelev=personne.id_type AND personne.type_pers=1)tmp_eleve GROUP BY tmp_eleve.sexe ORDER BY tmp_eleve.sexe ASC';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }       

    public static function get_classe_prof($id_group)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT personne.nom_prenom,personne.contact,personne.sexe , prof_classe.* FROM prof_classe,personne WHERE prof_classe.etat_prof_classe=1 AND prof_classe.id_prof=personne.id_type AND personne.type_pers=2 AND prof_classe.id_groupe='.$id_group.' GROUP BY personne.nom_prenom';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }     
    public static function get_classe_pp($id_group)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT personne.nom_prenom,personne.contact,personne.sexe ,prof_principale.* FROM prof_principale,personne WHERE fk_id_group='.$id_group.' AND personne.type_pers=2 AND personne.id_type=prof_principale.fk_id_prof';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }  


    public static function setClasse_Eval($id_grpe ,$id_mat ,$eval_lib ,$eval_desc, $id_session ,$eval_type, $eval_date,$eval_hDebut ,$eval_hFin, $id_periode,$coef,$notation,$fk_idpers ){
                        
        $db = static::getDB();
        //id_prof 	id_groupe 	etat_prof_classe 
        $sql=' SELECT * FROM prof_eval WHERE id_prof= 0 AND fk_idpartAneeScol= '.$id_periode.' AND id_groupe= '.$id_grpe.' AND id_mat= '.$id_mat.' AND eval_libelle= "'.$eval_lib.'" AND eval_type= "'.$eval_type.'" AND fk_idpers='.$fk_idpers.' LIMIT 1';
        //var_dump($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {

            $data = [
                'fk_idpartAneeScol' => $id_periode,
                'id_prof' => 0,
                'id_groupe' => $id_grpe,
                'id_mat' => $id_mat,
                'eval_libelle' => $eval_lib,
                'eval_desc' => $eval_desc,
                'eval_session' => $id_session,
                'eval_type' => $eval_type,
                'fk_idpers' => $fk_idpers,
                'eval_etat' => 1
            ];
            //var_dump($db);
                    
            $sql=' INSERT INTO prof_eval (fk_idpartAneeScol, id_prof, id_groupe, id_mat, eval_libelle, eval_desc, eval_session, eval_type, fk_idpers, eval_etat) VALUES (:fk_idpartAneeScol,  :id_prof, :id_groupe, :id_mat, :eval_libelle, :eval_desc, :eval_session, :eval_type, :fk_idpers, :eval_etat);';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            //var_dump($lastid);
            if ( $result == TRUE) { 
                
                $data = [
                    'eval_id' => $lastid,
                    'eval_date' => $eval_date,
                    'eval_hDebut' => $eval_hDebut,
                    'eval_hFin' => $eval_hFin,
                    'coef' => $coef,
                    'notation' => $notation,
                ];
                //var_dump($db);
                $sql=' INSERT INTO prof_eval_emploitps (eval_id, eval_date, eval_hDebut, eval_hFin, coef, notation) VALUES ( :eval_id, :eval_date, :eval_hDebut, :eval_hFin, :coef, :notation);';
                $stmt= $db->prepare($sql);
                $result = $stmt->execute($data);
                //$lastid =  $db->lastInsertId();
                return 1;
            } 
            else {return 0;}
        }
        else {          

            $sql=' UPDATE prof_eval SET eval_etat = 3 WHERE id_prof =0 AND id_groupe = '.$id_grpe.' AND id_mat = '.$id_mat.' AND eval_libelle = "'.$eval_lib.'" AND eval_type = "'.$eval_type.'" AND fk_idpers ='.$fk_idpers.';';
            //var_dump($sql);
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            //$lastid =  $db->lastInsertId();
            if( $result == TRUE) { return 0;} else {return 1;   }
        }
    }


    public static function get_eval_adminevalBy($id_anneescol)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM (SELECT eval.*,groupe.groupe_libelle,annee_partie.id_anneeScolaire, annee_partie.libele_partie,matiere.code,matiere.libele,personne.nom_prenom,personne.contact FROM (SELECT * FROM prof_eval,prof_eval_emploitps WHERE id_prof=0 AND prof_eval.prof_eval_id=prof_eval_emploitps.eval_id)eval , groupe,annee_partie,matiere,personne WHERE  eval.id_groupe=groupe.groupe_id AND eval.fk_idpartAneeScol = annee_partie.id_annee_partie AND eval.id_mat = matiere.id_matiere_matiere AND eval.fk_idpers = personne.id_pers_personne ) tmp_admineval WHERE  tmp_admineval.id_anneeScolaire='.$id_anneescol;
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    } 
    public static function get_eval_profevalBy($id_anneescol)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM 
	    (SELECT eval.*,groupe.groupe_libelle,annee_partie.id_anneeScolaire, annee_partie.libele_partie,matiere.code,matiere.libele FROM 
		(SELECT prof_eval.*,prof_eval_emploitps.* ,personne.nom_prenom,personne.contact FROM prof_eval,prof_eval_emploitps,personne WHERE prof_eval.id_prof !=0 AND prof_eval.prof_eval_id=prof_eval_emploitps.eval_id AND prof_eval.id_prof=personne.id_type AND personne.type_pers=2)eval , 
     	groupe,annee_partie,matiere WHERE  eval.id_groupe=groupe.groupe_id AND eval.fk_idpartAneeScol = annee_partie.id_annee_partie AND eval.id_mat = matiere.id_matiere_matiere  ) tmp_admineval WHERE  tmp_admineval.id_anneeScolaire='.$id_anneescol;
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    } 

    public static function get_profeval_infosBy($id_eval)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM 
	    (SELECT eval.*,groupe.groupe_libelle,annee_partie.id_anneeScolaire, annee_partie.libele_partie,matiere.code,matiere.libele FROM 
		(SELECT prof_eval.*,prof_eval_emploitps.* ,personne.nom_prenom,personne.contact FROM prof_eval,prof_eval_emploitps,personne WHERE prof_eval.prof_eval_id='.$id_eval.' AND prof_eval.id_prof !=0 AND prof_eval.prof_eval_id=prof_eval_emploitps.eval_id AND prof_eval.id_prof=personne.id_type AND personne.type_pers=2)eval , 
     	groupe,annee_partie,matiere WHERE  eval.id_groupe=groupe.groupe_id AND eval.fk_idpartAneeScol = annee_partie.id_annee_partie AND eval.id_mat = matiere.id_matiere_matiere  ) tmp_admineval WHERE  tmp_admineval.prof_eval_id='.$id_eval;
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    } 

    public static function get_admineval_infosBy($id_eval)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM 
                (SELECT eval.*,groupe.groupe_libelle,annee_partie.id_anneeScolaire, annee_partie.libele_partie,matiere.code,matiere.libele,personne.nom_prenom,personne.contact FROM 

                    (SELECT * FROM 
                    prof_eval,prof_eval_emploitps WHERE prof_eval.prof_eval_id='.$id_eval.' AND id_prof=0 AND prof_eval.prof_eval_id=prof_eval_emploitps.eval_id)eval , 
                    groupe,annee_partie,matiere,personne WHERE  

                eval.id_groupe=groupe.groupe_id AND eval.fk_idpartAneeScol = annee_partie.id_annee_partie AND eval.id_mat = matiere.id_matiere_matiere AND eval.fk_idpers = personne.id_pers_personne ) tmp_admineval 
                WHERE  tmp_admineval.prof_eval_id='.$id_eval;
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    } 

    public static function get_infos_eval_By($id_partannee, $id_groupe, $id_mat, $id_session)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM 
                (SELECT prof_eval.prof_eval_id, prof_eval.fk_idpartAneeScol,prof_eval.id_groupe,prof_eval.id_mat,prof_eval.eval_libelle,prof_eval.eval_session, 
                prof_eval_emploitps.eval_id, prof_eval_emploitps.coef,prof_eval_emploitps.notation
                FROM prof_eval,prof_eval_emploitps 
                WHERE prof_eval.prof_eval_id=prof_eval_emploitps.eval_id AND prof_eval.eval_etat <> 3 AND prof_eval.fk_idpartAneeScol='.$id_partannee.' AND prof_eval.id_groupe='.$id_groupe.' AND prof_eval.id_mat='.$id_mat.')tmp_eval
            INNER JOIN annee_session
            ON annee_session.id_session_session=tmp_eval.eval_session
            WHERE tmp_eval.eval_session = '.$id_session;
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    } 
    public static function get_infos_notes_By($id_eval)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT notes.*,eleve.matricule,personne.nom_prenom  
                FROM notes,eleve,personne 
                WHERE notes.id_eleve_eleve=eleve.id_eleve_eleve AND notes.id_eleve_eleve=personne.id_type AND personne.type_pers=1  AND etat_note=1 AND notes.id_evaluation='.$id_eval.'
                ORDER BY personne.nom_prenom ASC';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }    


    public static function get_bilan_moyenneinfosBy($fk_id_eleve,$fk_id_grpe,$id_partannee)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM
                (SELECT moyenne_matf.*,moyenne.id_session,moyenne.id_prof,moyenne.fk_idpers_saisie FROM moyenne_matf,moyenne WHERE 
                moyenne_matf.fk_id_eleve ='.$fk_id_eleve.' AND moyenne_matf.fk_id_grpe='.$fk_id_grpe.' AND moyenne_matf.fk_id_partAnnee = '.$id_partannee.' AND
                moyenne_matf.fk_id_eleve= moyenne.id_eleve AND moyenne_matf.fk_id_grpe= moyenne.id_groupe AND  
                moyenne_matf.fk_id_mat= moyenne.id_matiere AND moyenne_matf.moy_mat_final= moyenne.moyenne AND
                moyenne_matf.fk_id_partAnnee = moyenne.fk_part_annee)tmp_moyess
                INNER JOIN
                (SELECT  groupe_matiere_coef.matiere_parent_id_tmp , groupe_matiere_coef.coeficient_tmp,groupe_matiere_coef.credit_tmp,groupe_matiere_coef.part_annee_id_tmp,matiere.id_matiere_matiere,matiere.code,matiere.libele FROM groupe_matiere_coef,matiere WHERE groupe_matiere_coef.groupe_id_tmp='.$fk_id_grpe.' AND groupe_matiere_coef.part_annee_id_tmp='.$id_partannee.' AND groupe_matiere_coef.matiere_id_tmp=matiere.id_matiere_matiere ORDER BY groupe_matiere_coef.part_annee_id_tmp ASC)tmp_matcredit
                ON  tmp_moyess.fk_id_mat = tmp_matcredit.id_matiere_matiere WHERE tmp_moyess.fk_id_partAnnee=tmp_matcredit.part_annee_id_tmp ORDER BY tmp_matcredit.matiere_parent_id_tmp ASC';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }    
    public static function get_MPgroupeBy_moyenneinfosBy($fk_id_eleve,$fk_id_grpe,$id_partannee)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT SUM(tmpf.credit_tmp) AS tcredit,SUM(tmpf.moy_mat_final * tmpf.credit_tmp) AS tmoycoef,tmpf.* FROM  (SELECT * FROM
           (SELECT moyenne_matf.*,moyenne.id_session,moyenne.id_prof,moyenne.fk_idpers_saisie FROM moyenne_matf,moyenne WHERE 
                moyenne_matf.fk_id_eleve ='.$fk_id_eleve.' AND moyenne_matf.fk_id_grpe='.$fk_id_grpe.' AND moyenne_matf.fk_id_partAnnee = '.$id_partannee.' AND
                moyenne_matf.fk_id_eleve= moyenne.id_eleve AND moyenne_matf.fk_id_grpe= moyenne.id_groupe AND  
                moyenne_matf.fk_id_mat= moyenne.id_matiere AND moyenne_matf.moy_mat_final= moyenne.moyenne AND
                moyenne_matf.fk_id_partAnnee = moyenne.fk_part_annee)tmp_moyess
                INNER JOIN
                (SELECT  groupe_matiere_coef.matiere_parent_id_tmp , groupe_matiere_coef.coeficient_tmp,groupe_matiere_coef.credit_tmp,groupe_matiere_coef.part_annee_id_tmp,matiere.id_matiere_matiere,matiere.code,matiere.libele FROM groupe_matiere_coef,matiere WHERE groupe_matiere_coef.groupe_id_tmp='.$fk_id_grpe.' AND groupe_matiere_coef.part_annee_id_tmp='.$id_partannee.' AND groupe_matiere_coef.matiere_id_tmp=matiere.id_matiere_matiere ORDER BY groupe_matiere_coef.part_annee_id_tmp ASC)tmp_matcredit
                ON  tmp_moyess.fk_id_mat = tmp_matcredit.id_matiere_matiere WHERE tmp_moyess.fk_id_partAnnee=tmp_matcredit.part_annee_id_tmp ORDER BY tmp_matcredit.matiere_parent_id_tmp ASC)tmpf
                GROUP BY tmpf.matiere_parent_id_tmp';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    } 

    public static function get_eleve_moyetatvalidBy($id_groupe,$id_eleve,$id_matiere,$id_session,$fk_part_annee)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM moyenne WHERE id_groupe='.$id_groupe.' AND id_eleve='.$id_eleve.' AND id_matiere='.$id_matiere.'  AND id_session='.$id_session.' AND fk_part_annee='.$fk_part_annee.' AND moyenne >= 10';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }       
     
    public static function get_niveau_next($id_niveau,$id_univ)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM niveau WHERE fk_id_univ='.$id_univ.' AND ordre_niveau IN (SELECT SUM(ordre_niveau +1) AS next_niveau FROM niveau WHERE id_niveau='.$id_niveau.') LIMIT 1';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    } 
    public static function get_gpre_finanne($id_gpre,$id_annee)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT fin_anneescol.*,personne.nom_prenom,personne.contact FROM fin_anneescol,personne WHERE fin_anneescol.id_personne=personne.id_pers_personne AND fin_anneescol.id_groupe='.$id_gpre.' AND fin_anneescol.id_annee_scola='.$id_annee.' AND fin_anneescol.etat_finannee=1';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    } 

    public static function get_absence_eleve($idannee,$idpartannee,$id_eleve)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM absence_eleve,groupe_emploitps 
                WHERE absence_eleve.fk_emploitps =groupe_emploitps.emploitps_id 
                AND groupe_emploitps.emploitps_periode='.$idpartannee.'
                AND groupe_emploitps.emploitps_anneescolaire='.$idannee.'
                AND absence_eleve.fk_id_eleve='.$id_eleve;
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    } 

    //SEt uset etat
    public static function set_user_etatconect($conex_id_personne,$fk_iduniv)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT MAX(conex_id) AS tmp_idconex FROM connexion WHERE conex_id_personne='.$conex_id_personne.' AND fk_iduniv='.$fk_iduniv;

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            $db = static::getDB();
            $date=date("Y-m-d H:i:s");
            $sql = 'UPDATE connexion SET connexion.conex_etat = 1, connexion.conex_date_heure="'.$date.'"  WHERE connexion.conex_id ='.$result[0]['tmp_idconex'];
            //print_r( $sql );
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            return  $result;

        } else {
            return 0;
        }
    }    
    
    //critere d'admission
    public static function get_all_critereadmin($id_anneescol)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM critere_admission,departement,annee_scolaire WHERE critere_admission.fk_idanneescol='.$id_anneescol.' AND critere_admission.fk_iddepart=departement.id_depat AND critere_admission.fk_idanneescol=annee_scolaire.id_anscol_annee_scolaire';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    } 
    
    public static function get_all_critereadminBY($id_anneescol,$id_departement)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM critere_admission,departement,annee_scolaire WHERE critere_admission.fk_idanneescol='.$id_anneescol.' AND critere_admission.fk_iddepart='.$id_departement.' AND critere_admission.fk_iddepart=departement.id_depat AND critere_admission.fk_idanneescol=annee_scolaire.id_anscol_annee_scolaire';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }          
    //SMSM
    public static function get_sms_notif($id_univ)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT * FROM `notification` WHERE notif_methode LIKE "%sms%" AND notif_etat=1 AND (CAST(NOW() AS DATE) ) >= CAST(notif_debut AS DATE) AND CAST(notif_fin AS DATE) >= (CAST(NOW() AS DATE) ) AND fk_iduniv='.$id_univ;
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    }   
    public static function get_sms_notif_usernum($id_notif,$id_univ)
    {
        //	conex_id   conex_id_personne     fk_iduniv     conex_ip     conex_date_heure     conex_navigateur     conex_continent_pays     conex_coordonne     conex_etat  conex_message
        $sql = 'SELECT notif_user.*,personne.nom_prenom,personne.contact,personne.id_pers_personne FROM notif_user,personne WHERE notif_user.usernotif_id='.$id_notif.' AND personne.type_pers=notif_user.usernotif_typeuser AND notif_user.usernotif_iduser=personne.id_type AND personne.fk_iduniv='.$id_univ.' AND notif_user.usernotif_etat=1';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    } 

    //HEURE OPERATION
    public static function diff_time($t2 , $t1){
        //Heures au format (hh:mm:ss) la plus grande puis le plus petite 
        //var_dump($t1 , $t2);
      
       $tab=explode(":", $t1); 
       $tab2=explode(":", $t2);
     
       if (!isset($tab[2])) {  $tab[2]="00"; }
       if (!isset($tab2[2])) { $tab2[2]="00"; }  

       //var_dump($tab , $tab2);   
      
       $h=$tab[0]; 
       $m=$tab[1]; 
       $s=$tab[2]; 
       $h2=$tab2[0]; 
       $m2=$tab2[1]; 
       $s2=$tab2[2];  
      
       if ($h2>$h) { 
       $h=$h+24; 
       }  
       if ($m2>$m) { 
       $m=$m+60; 
       $h2++; 
       } 
       if ($s2>$s) { 
       $s=$s+60; 
       $m2++; 
       } 
       
       $ht=$h-$h2; 
       $mt=$m-$m2; 
       $st=$s-$s2; 
       if (strlen($ht)==1) { 
       $ht="0".$ht; 
       }  
       if (strlen($mt)==1) { 
       $mt="0".$mt; 
       }  
       if (strlen($st)==1) { 
       $st="0".$st; 
       }  
       return $ht.":".$mt.":".$st;  
      
    }
    public static function add_time($t2 , $t1){
        //Heures au format (hh:mm:ss) la plus grande puis le plus petite 
      
       $tab=explode(":", $t1); 
       $tab2=explode(":", $t2); 

       if (!isset($tab[2])) {  $tab[2]="00"; }
       if (!isset($tab2[2])) { $tab2[2]="00"; }  

       //var_dump($tab , $tab2);   

       $h=$tab[0]; 
       $m=$tab[1]; 
       $s=$tab[2]; 
       $h2=$tab2[0]; 
       $m2=$tab2[1]; 
       $s2=$tab2[2];  
      
       if ($h2>$h) { 
       $h=$h+24; 
       }  
       if ($m2>$m) { 
       $m=$m+60; 
       $h2++; 
       } 
       if ($s2>$s) { 
       $s=$s+60; 
       $m2++; 
       } 
       
       $ht=$h+$h2; 
       $mt=$m+$m2; 
       $st=$s+$s2; 
       if (strlen($ht)==1) { 
       $ht="0".$ht; 
       }  
       if (strlen($mt)==1) { 
       $mt="0".$mt; 
       }  
       if (strlen($st)==1) { 
       $st="0".$st; 
       }  
       return $ht.":".$mt.":".$st;  
      
    }
    //MOYENNE
        //INSERT INTO `prof_matiere` (`id_prof`, `id_mat`, `etat_prof_mat`) VALUES ('1', '1', '1'); 
    //MOYENNE SET
    public static function setEleve_annee_moyBY($moy_id_groupe, $moy_id_eleve_eleve , $moy_id_matiere_matiere, $moy_id_prof, $moy_id_session_session, $moy_moyenne, $fk_part_annee, $fk_idpers_saisie){
        $db = static::getDB();
        //id_groupe 	id_eleve 	id_matiere 	id_prof 	id_session 	moyenne 	etat_moy 0-active admin//1-activer eleve// 2-desactiver 
        $sql=' SELECT * FROM moyenne WHERE id_groupe= "'.$moy_id_groupe.'" AND id_eleve= "'.$moy_id_eleve_eleve.'" AND id_matiere= "'.$moy_id_matiere_matiere.'"  AND id_session= "'.$moy_id_session_session.'"  AND fk_part_annee= "'.$fk_part_annee.'" LIMIT 1';
        //print_r($sql);
        //exit();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ( empty($result) || $result==0) {

            $data = [
                'id_groupe' => $moy_id_groupe,
                'id_eleve' => $moy_id_eleve_eleve,
                'id_matiere' => $moy_id_matiere_matiere,
                'id_prof' => $moy_id_prof,
                'id_session' => $moy_id_session_session,
                'moyenne' => $moy_moyenne,
                'fk_part_annee' => $fk_part_annee,
                'fk_idpers_saisie' => $fk_idpers_saisie,
                'etat_moy' => 1,
            ];
                    
            $sql=' INSERT INTO moyenne (id_groupe, id_eleve , id_matiere, id_prof, id_session , moyenne , fk_part_annee, fk_idpers_saisie, etat_moy) VALUES ( :id_groupe, :id_eleve , :id_matiere, :id_prof , :id_session, :moyenne , :fk_part_annee, :fk_idpers_saisie, :etat_moy);';
            //print_r($sql);
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            $lastid =  $db->lastInsertId();
            
            //var_dump($lastid);
            if ( $result == TRUE) { return 1;} else {return 0; }
        }
        else {          

            $sql='UPDATE moyenne SET moyenne.moyenne = '.$moy_moyenne.',moyenne.fk_idpers_saisie= '.$fk_idpers_saisie.' WHERE moyenne.id_groupe= '.$moy_id_groupe.' AND moyenne.id_eleve= '.$moy_id_eleve_eleve.' AND moyenne.id_matiere= '.$moy_id_matiere_matiere.' AND moyenne.id_prof= '.$moy_id_prof.' AND moyenne.id_session= '.$moy_id_session_session.' AND moyenne.fk_part_annee= '.$fk_part_annee.';';
            $stmt= $db->prepare($sql);
            $result = $stmt->execute();
            //print_r($sql);
            //var_dump("update=".$result);
            //$lastid =  $db->lastInsertId();
            if ( $result == TRUE) { return 1;} else {return 0;   }
        }


       
    }
    public static function get_etat_moyenne_By($id_groupe,$id_matiere,$fk_part_annee,$id_session)
    {
        
        $sql = 'SELECT tmp_moy.*, personne.nom_prenom, personne.contact FROM 
                (SELECT COUNT(etat_moy) AS etat_moy,fk_idpers_saisie FROM moyenne WHERE id_groupe='.$id_groupe.' AND id_matiere='.$id_matiere.' AND fk_part_annee='.$fk_part_annee.' AND id_session='.$id_session.')tmp_moy 
                LEFT JOIN personne ON personne.id_pers_personne=tmp_moy.fk_idpers_saisie';
        //print_r($sql);

        $result = Admin::sql_query_get($sql);
        if ($result != 0 && !empty($result)) {
            return  $result;
        } else {
            return 0;
        }
    } 
    //Fucntion permettant d'executer les sql pour recuperer des valeur (prend le sql en paramettre et retourne le resultat);
    public static function sql_query_get($sql)
    {

        $db = static::getDB();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result) || $result == 0) {
            return 0;
        } else {
            return $result;
        }
    }
}
