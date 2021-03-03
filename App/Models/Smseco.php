<?php

namespace App\Models;
 
use PDO;
  
date_default_timezone_set("Africa/Abidjan");
/**
 * Example user model
 *
 * PHP version 7.0
*/
class Smseco extends \Core\Model
{

    public static function send_sms_json($exp,$id_msg,$msg,$dest_tab)
    {
        $uri = 'http://www.smseco.com/api/json/sendsms/';
        $dest = "";
        //$exp ="22542272720 - AIBS";
        //var_dump($exp);
        if ($exp=="ATLANTIQUE INTERNATIONAL BUSINESS SCHOOL (AIBS)") {
            $exp ="AIBS";
            $login= "servicecomaibs@gmail.com";
            $pass="aibs@2014";
        }
        $dest_tab_count = count($dest_tab);

        foreach ($dest_tab as $key => $value) {
            if ($dest_tab_count == 1 || ($dest_tab_count-1) == intval($key)) {
                $dest = $dest.'{"numero":"'.$value.'"}';
            }
            elseif ($dest_tab_count != 1 && ($dest_tab_count-1) != intval($key)) {
                $dest = $dest.'{"numero":"'.$value.'"},';
            }
            else { }
            //{\"numero\":\"07352518\"}
        }
        //servicecomaibs@gmail.com
        $data ='JSON={"compte":{"login":"'.$login.'","password":"'.$pass.'"},"message":{"expediteur":"'.$exp.'","msgid":"'.$id_msg.'","msg":"'.$msg.'"},"destinataires":['.$dest.']}';

        //var_dump($data,strlen($data));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$uri);
        curl_setopt($ch,CURLOPT_HTTPHEADER,array("Accept: application/json","Accept: application/json","ContentType: application/json", "Content-Length: ". strlen($data)));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec ($ch);
        //var_dump($ch);
        curl_close ($ch); // close curl handle
        return $output;

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
