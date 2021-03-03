<?php

namespace App\Models;
 
use PDO;
  
date_default_timezone_set("Africa/Abidjan");
/**
 * Example user model
 *
 * PHP version 7.0
*/
class Smsmondial extends \Core\Model
{
    public static function send_sms($exp,$id_msg,$msg,$dest_tab)
    {
        //'DGEO INFO'
        $param = array(
            'username' => 'DGEO INFO',
            'password' => 'x3dUiTFizPq6W4C',
            'sender' => 'DGEO INFO',
            'text' => $exp.' : '.$msg,
            'type' => 'text',
            'datetime' => date("Y-m-d H:i:s"),
        );
        $post = 'to=' . implode(';', $dest_tab);
        //var_dump($post);
        foreach ($param as $key => $val){$post .= '&' . $key . '=' . rawurlencode($val);}
        $url = "http://africasmshub.mondialsms.net/api/api_http.php";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Connection: close"));
        $result = curl_exec($ch);
        if(curl_errno($ch)) {$result = "cURL ERROR: " . curl_errno($ch) . " " . curl_error($ch);} 
        else {
            $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            switch($returnCode) {
                case 200 :
                    break;
                default :
                    $result = "HTTP ERROR: " . $returnCode;
            }
        }
        curl_close($ch);
        return $result;

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
