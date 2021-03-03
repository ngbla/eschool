<?php

namespace App\Models;

use PDO;
date_default_timezone_set("Africa/Abidjan");
/**
 * Example user model
 *
 * PHP version 7.0
 */
class Log extends \Core\Model
{

    /**
     * Get all the users as an associative array
     *
     * @return array
     */
    public static function getIp(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function setLog($info,$log_user){
        $ip = Log::getIp();
        $db = static::getDB();
        $data = [
            'log_info' => $info,
            'log_date' => date("Y/m/d H:m:s"),
            'log_user' => $log_user,
            'log_ip' => $ip
        ];
        //var_dump($db);
        $sql=' INSERT INTO log (log_info, log_date, log_user, log_ip) VALUES ( :log_info, :log_date, :log_user, :log_ip);';
        $stmt= $db->prepare($sql);
        $result = $stmt->execute($data);
        if ( $result == TRUE) {
            return 1;
        } else {
            return -2 ;
        }
    }

    public static function set_AllLog($info){
        $ip = Log::getIp();
        $db = static::getDB();
        $fk_idpers = 0;

        if (isset($_SESSION['user']['id_pers_personne'])) {
           // var_dump($_SESSION['user']['id_pers_personne']);
            $fk_idpers  = intval( $_SESSION['user']['id_pers_personne'] );
        }


        if (isset($_POST) && !empty($_POST)) {

            $log_user = '_POST= '. (htmlspecialchars(json_encode((object)$_POST)));

            if (isset($_GET) && !empty($_GET)) {
                $log_user =  $log_user .'  _GET =  '. (htmlspecialchars(json_encode((object)$_GET)));
            }
        }
        elseif(!isset($_POST) && isset($_GET)){
            $log_user =  '  _GET =  '. (htmlspecialchars(json_encode((object)$_GET)));
        }
        else{
            $log_user =  'Accès';
        }

        $sql_test=' SELECT * FROM log WHERE log_info= "'.$info.'" AND log_user ="'.$log_user.'" AND log_ip = "'.$ip.'" AND fk_idpers = "'.$fk_idpers.'" LIMIT 1';
        //var_dump($sql_test);
        //exit();

        $stmt_test = $db->query($sql_test);
        $result_test = $stmt_test->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result_test);
        if ( empty($result_test) || $result_test==0) {

            $data = [
                'log_info' => $info,
                'log_date' => date("Y/m/d H:m:s"),
                'log_user' => $log_user,
                'log_ip' => $ip,
                'fk_idpers' => $fk_idpers
            ];
            //var_dump($db);


            $sql=' INSERT INTO log (log_info, log_date, log_user, log_ip, fk_idpers) VALUES ( :log_info, :log_date, :log_user, :log_ip, :fk_idpers);';
        
        
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            if ( $result == TRUE) {
                return 1;
            } else {
                return -2 ;
            }

        }
        else{
            return 0;
        }
    }

    public static function set_Ajax_Log($info,$details,$id_pers,$id_univ){
        $ip = Log::getIp();
        $db = static::getDB();
 
        $sql_test=' SELECT * FROM ( SELECT *,TIMEDIFF(CURRENT_TIMESTAMP, log_date) AS nb_heure,DATEDIFF(CURRENT_TIMESTAMP, log_date) AS nb_jours FROM log)tmp_log WHERE tmp_log.log_info= "'.$info.'" AND tmp_log.log_user ="'.$details.'" AND tmp_log.log_ip = "'.$ip.'" AND tmp_log.fk_idpers = '.$id_pers.' AND tmp_log.fk_iduniv = '.$id_univ.' AND "00:05:00">tmp_log.nb_heure AND tmp_log.nb_jours=0 ORDER BY tmp_log.log_id ASC LIMIT 1';

        //exit();

        $stmt_test = $db->query($sql_test);
        $result_test = $stmt_test->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($result_test);
        if ( empty($result_test) || $result_test==0) {

            $data = [
                'log_info' => $info,
                'log_date' => date("Y/m/d H:m:s"),
                'log_user' => $details,
                'log_ip' => $ip,
                'fk_idpers' => $id_pers,
                'fk_iduniv' => $id_univ
            ];
            //var_dump($db);

            $sql=' INSERT INTO log (log_info, log_date, log_user, log_ip, fk_idpers, fk_iduniv) VALUES ( :log_info, :log_date, :log_user, :log_ip, :fk_idpers, :fk_iduniv);';
        
        
            $stmt= $db->prepare($sql);
            $result = $stmt->execute($data);
            if ( $result == TRUE) {
                return 1;
            } else {
                return -2 ;
            }

        }
        else{
            return 0;
        }
    }

    public static function get_all_logs_By($debut,$fin,$type_user,$fk_univ)
    {

        $db = static::getDB();
        $sql='';

        if ($type_user==0) {
            $sql ='SELECT * FROM
            (SELECT * FROM log WHERE log_date>="'.$debut.'" && "'.$fin.'">=log_date)tmp_log
            LEFT JOIN (SELECT  personne.id_pers_personne,personne.type_pers,personne.id_type , personne.nom_prenom, personne.date_naiss, personne.lieu_naiss, personne.sexe, personne.contact FROM personne WHERE personne.fk_iduniv='.$fk_univ.')tmp_pers
            ON tmp_log.fk_idpers= tmp_pers.id_pers_personne ORDER BY tmp_log.log_id ASC';
        }
        elseif ($type_user==5) {
            $sql ='SELECT * FROM log WHERE log_date>="'.$debut.'" && "'.$fin.'">=log_date AND fk_idpers=0 ORDER BY tmp_log.log_id ASC';
        }
        elseif ($type_user==1 || $type_user==3 || $type_user==3 || $type_user==4) {
            $sql ='SELECT * FROM
            (SELECT * FROM log WHERE log_date>="'.$debut.'" && "'.$fin.'">=log_date)tmp_log
            LEFT JOIN (SELECT  personne.id_pers_personne,personne.type_pers,personne.id_type , personne.nom_prenom, personne.date_naiss, personne.lieu_naiss, personne.sexe, personne.contact FROM personne WHERE personne.fk_iduniv='.$fk_univ.' AND personne.type_pers='.$type_user.')tmp_pers
            ON tmp_log.fk_idpers= tmp_pers.id_pers_personne WHERE tmp_pers.type_pers='.$type_user.' ORDER BY tmp_log.log_id ASC';
        }

        //print_r($sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result) || $result == 0) {  return 0; }
        else {  return $result;  }


    }

    public static function get_all_errorlogs_By($debut,$fin,$type_user,$fk_univ)
    {

        $db = static::getDB();
        $sql='';

        if ($type_user==0) {
            $sql ='SELECT * FROM
            (SELECT * FROM log_error WHERE date_error>="'.$debut.'" && "'.$fin.'">=date_error)tmp_log
            LEFT JOIN (SELECT  personne.id_pers_personne,personne.type_pers,personne.id_type , personne.nom_prenom, personne.date_naiss, personne.lieu_naiss, personne.sexe, personne.contact FROM personne WHERE personne.fk_iduniv='.$fk_univ.')tmp_pers
            ON tmp_log.fk_idpers= tmp_pers.id_pers_personne ORDER BY tmp_log.id_error ASC';
        }
        elseif ($type_user==5) {
            $sql ='SELECT * FROM log_error WHERE date_error>="'.$debut.'" && "'.$fin.'">=date_error AND fk_idpers=0 ORDER BY tmp_log.id_error ASC';
        }
        elseif ($type_user==1 || $type_user==3 || $type_user==3 || $type_user==4) {
            $sql ='SELECT * FROM
            (SELECT * FROM log_error WHERE date_error>="'.$debut.'" && "'.$fin.'">=date_error)tmp_log
            LEFT JOIN (SELECT  personne.id_pers_personne,personne.type_pers,personne.id_type , personne.nom_prenom, personne.date_naiss, personne.lieu_naiss, personne.sexe, personne.contact FROM personne WHERE personne.fk_iduniv='.$fk_univ.' AND personne.type_pers='.$type_user.')tmp_pers
            ON tmp_log.fk_idpers= tmp_pers.id_pers_personne WHERE tmp_pers.type_pers='.$type_user.' ORDER BY tmp_log.id_error ASC';
        }

        //print_r($sql);

        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result) || $result == 0) {  return 0; }
        else {  return $result;  }


    }
 

    
}
