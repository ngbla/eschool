<?php

namespace App\Models;

use PDO;
use Symfony\Component\Finder\Finder;


/**
 * Example user model
 *
 * PHP version 7.0
 */
class User_Img extends \Core\Model
{

    /**
     * Get all the users as an associative array
     *
     * @return array
    */
    
    public static function create_jpeg_imgMini_save($img_tmp_name,$reduction,$chemin_destination,$img_final_name,$extension_upload){
        $type_upload = mime_content_type($img_tmp_name);
        //var_dump( $type_upload);
        $taille = getimagesize($img_tmp_name);
        $largeur = $taille[0];
        $hauteur = $taille[1];
        $largeur_miniature = $reduction;
        $hauteur_miniature = $hauteur / $largeur * $reduction;
        /*if ($type_upload == 'image/png') {

            $taille = getimagesize($img_tmp_name);
            $largeur = $taille[0];
            $hauteur = $taille[1];
            $largeur_miniature = $reduction;
            $hauteur_miniature = $hauteur / $largeur * $reduction;

            $image = imagecreatefrompng($img_tmp_name);

            $bg = imagecreatetruecolor($largeur_miniature, $hauteur_miniature);
            
            imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
            imagealphablending($bg, TRUE);
            imagecopyresampled($bg, $image, 0, 0, 0, 0, $largeur_miniature, $hauteur_miniature, $largeur, $hauteur);
            imagedestroy($image);
            $quality = 40; // 0 = worst / smaller file, 100 = better / bigger file 
            imagejpeg($bg, $chemin_destination.$img_final_name.'.jpg', $quality);
            imagedestroy($bg);
            return 1;

        }*/
        if ($type_upload == 'image/png') {
            $im = imagecreatefrompng($img_tmp_name);
        }
        elseif ($type_upload == 'image/jpg' || $type_upload == 'image/jpeg') {
            $im = imagecreatefromjpeg($img_tmp_name);
        }

        $im_miniature = imagecreatetruecolor($largeur_miniature, $hauteur_miniature);
        imagecopyresampled($im_miniature, $im, 0, 0, 0, 0, $largeur_miniature, $hauteur_miniature, $largeur, $hauteur);
        imagejpeg($im_miniature, $chemin_destination.$img_final_name.'.jpg', 40);

        //imagedestroy($im_miniature);
        //imagedestroy($im);

        return 1;
    }
    
    public static function sendImg(){
        //var_dump($_FILES);
        $result = "";
        if ( (isset($_FILES['photo_user'])) && isset($_FILES['photo_user']['error'])) {

            $msgResut = "";
            switch ($_FILES['photo_user']['error']){     
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

            $result= $msgResut;
            //var_dump($_FILES['photo_user']['error']);
            //var_dump(UPLOAD_ERR_OK);


            if( $_FILES['photo_user']['error'] == UPLOAD_ERR_OK ){
                
                $files_path = ($_FILES['photo_user']['name']);
                $extension_upload = pathinfo($files_path, PATHINFO_EXTENSION);
                $extensions_autorisees = array('image/jpg', 'image/jpeg', 'image/png');

                $type_upload = mime_content_type($_FILES['photo_user']['tmp_name']);
                if (in_array($type_upload, $extensions_autorisees)) {

                    $img_error = $_FILES['photo_user']['error'] ; 
                    $img_type = $_FILES['photo_user']['type'] ;
                    $img_tmp_name = $_FILES['photo_user']['tmp_name'];
                    $img_size = $_FILES['photo_user']['size'] ;
                    $img_name = $_FILES['photo_user']['name'] ;
                    //var_dump($_FILES);//exit;
                    //var_dump(mime_content_type($img_tmp_name));//exit;
                    //var_dump($_SESSION['user']['id_pers_personne']);//exit;

                    //$chemin_destination = '../wai.ci/ftp/FTPUSER/'.$_FILES['file']['name'].'/';
                    $chemin_destination = '../files/'.$_SESSION['user']['id_pers_personne'].'/';
                    
                    if(is_dir($chemin_destination)){

                        $fichier =  $chemin_destination.$_SESSION['user']['id_pers_personne'].'.jpg';

                        if( file_exists ( $fichier)){
                            unlink( $fichier ) ;
                            $fichier =  $chemin_destination.'tiny'.$_SESSION['user']['id_pers_personne'].'.jpg';
                            unlink( $fichier ) ;
                        }

                    }else{
                        
                        mkdir($chemin_destination,0777);
                    }	
            
                    $reduction = 400;
                    $img_final_name = $_SESSION['user']['id_pers_personne'];
                    $reps1 = User_Img::create_jpeg_imgMini_save($img_tmp_name,$reduction,$chemin_destination,$img_final_name,$extension_upload);
                    //var_dump($reps );
                    $reduction = 100;
                    $img_final_name = 'tiny'.$_SESSION['user']['id_pers_personne'];
                    $reps2 =User_Img::create_jpeg_imgMini_save($img_tmp_name,$reduction,$chemin_destination,$img_final_name,$extension_upload);
                    //var_dump($reps1,$reps2 );
                    //exit;
                    if ($reps1 == 1 && $reps2 ==1) {
                        $result= 'IMG_SUCCESS_SEND' ;
                    }
                    else {
                        $result= 'IMG_ERROR_SEND' ;
                    }

                    unset($_FILES['photo_user']);
                    unset($_POST['btn_send_photo']);
                    
            
                }  
                else {
                    $result= 'IMG_ERROR_TYPE' ;
                }   
            }  
            else {
                $result= "IMAGE_codeerror_INCONNU".$_FILES['photo_user']['error'];
            }

        }  
        else {
            $result="IMAGE_INTROUVABLE";
        }
        //var_dump($result) ;
        return $result;
        //exit;
  
    }
    public static function send_elevImg($id_pers_elev){
        //var_dump($_FILES);
        $result = "";
        if ( (isset($_FILES['photo_user'])) && isset($_FILES['photo_user']['error'])) {

            $msgResut = "";
            switch ($_FILES['photo_user']['error']){     
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

            $result= $msgResut;
            //var_dump($_FILES['photo_user']['error']);
            //var_dump(UPLOAD_ERR_OK);


            if( $_FILES['photo_user']['error'] == UPLOAD_ERR_OK ){
                
                $files_path = ($_FILES['photo_user']['name']);
                $extension_upload = pathinfo($files_path, PATHINFO_EXTENSION);
                $extensions_autorisees = array('image/jpg', 'image/jpeg', 'image/png');

                $type_upload = mime_content_type($_FILES['photo_user']['tmp_name']);
                if (in_array($type_upload, $extensions_autorisees)) {

                    $img_error = $_FILES['photo_user']['error'] ; 
                    $img_type = $_FILES['photo_user']['type'] ;
                    $img_tmp_name = $_FILES['photo_user']['tmp_name'];
                    $img_size = $_FILES['photo_user']['size'] ;
                    $img_name = $_FILES['photo_user']['name'] ;

                    //$chemin_destination = '../wai.ci/ftp/FTPUSER/'.$_FILES['file']['name'].'/';
                    $chemin_destination = '../files/'.$id_pers_elev.'/';
                    
                    if(is_dir($chemin_destination)){

                        $fichier =  $chemin_destination.$id_pers_elev.'.jpg';

                        if( file_exists ( $fichier)){
                            unlink( $fichier ) ;
                            $fichier =  $chemin_destination.'tiny'.$id_pers_elev.'.jpg';
                            unlink( $fichier ) ;
                        }

                    }else{
                        
                        mkdir($chemin_destination,0777);
                    }	
            
                    $reduction = 400;
                    $img_final_name = $id_pers_elev;
                    $reps1 = User_Img::create_jpeg_imgMini_save($img_tmp_name,$reduction,$chemin_destination,$img_final_name,$extension_upload);
                    //var_dump($reps );
                    $reduction = 100;
                    $img_final_name = 'tiny'.$id_pers_elev;
                    $reps2 =User_Img::create_jpeg_imgMini_save($img_tmp_name,$reduction,$chemin_destination,$img_final_name,$extension_upload);
                    //var_dump($reps1,$reps2 );
                    //exit;
                    if ($reps1 == 1 && $reps2 ==1) {
                        $result= 'IMG_SUCCESS_SEND' ;
                    }
                    else {
                        $result= 'IMG_ERROR_SEND' ;
                    }

                    unset($_FILES['photo_user']);
                    unset($_POST['btn_send_photo']);
                    
            
                }  
                else {
                    $result= 'IMG_ERROR_TYPE' ;
                }   
            }  
            else {
                $result= "IMAGE_codeerror_INCONNU".$_FILES['photo_user']['error'];
            }

        }  
        else {
            $result="IMAGE_INTROUVABLE";
        }
        //var_dump($result) ;
        return $result;
        //exit;
  
    }

    public static function send_notifImg($id_notif){

        $tmp_file_img_error = (($_FILES['info_infoBundle_Info']['error'])['image'])['file'] ;
        $tmp_file_type = (($_FILES['info_infoBundle_Info']['type'])['image'])['file'] ;
        $tmp_file_img_tmp_name = (($_FILES['info_infoBundle_Info']['tmp_name'])['image'])['file'] ;
        $tmp_file_img_size = (($_FILES['info_infoBundle_Info']['size'])['image'])['file'] ;
        $tmp_file_img_name = (($_FILES['info_infoBundle_Info']['name'])['image'])['file'] ;

        //var_dump( $tmp_file_img_name  );exit;

        if ( $tmp_file_img_name != '' ) {

            $extension_upload = pathinfo($tmp_file_img_name, PATHINFO_EXTENSION);
            $extensions_autorisees = array('image/jpg', 'image/jpeg', 'image/png');
            $type_upload = mime_content_type($tmp_file_img_tmp_name);

            if (in_array($type_upload, $extensions_autorisees)) {

                if ( (isset( ($_FILES['info_infoBundle_Info']['name'])['image'] )) ) {

                    if ( $tmp_file_img_error != UPLOAD_ERR_OK) {
                        $msgResut ='error images';
                        switch ( $tmp_file_img_error  ){     
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
                    }    

                    else if( $tmp_file_img_error== UPLOAD_ERR_OK ){
                        

                        //$chemin_destination = '../wai.ci/ftp/FTPUSER/'.$_FILES['file']['name'].'/';
                        $chemin_destination = '../notifications/'.$id_notif.'/';
                        
                        if(is_dir($chemin_destination)){

                            $fichier =  $chemin_destination.$id_notif.'.jpg';

                            if( file_exists ( $fichier)){
                                unlink( $fichier ) ;
                            }

                        }else{
                            
                            mkdir($chemin_destination,0777);
                        }	

    
                        $reduction = 400;
                        $img_final_name = $id_notif;
                        $reps1 = User_Img::create_jpeg_imgMini_save($tmp_file_img_tmp_name,$reduction,$chemin_destination,$img_final_name,$extension_upload);
                        //var_dump($reps );
                        $reduction = 100;
                        $img_final_name = 'tiny'.$id_notif;
                        $reps2 =User_Img::create_jpeg_imgMini_save($tmp_file_img_tmp_name,$reduction,$chemin_destination,$img_final_name,$extension_upload);
                        //var_dump($reps );
                        //exit;
                        if ($reps1 == 1 && $reps2 ==1) {
                            return 'IMG_SUCCESS_SEND' ;
                        }
                        else {
                            return 'IMG_ERROR_SEND' ;
                        }

                        unset( $tmp_file_img_name );
                    
                    }     
                
                }  
            }  
            else {
                User_Img::send_notifDocs_Byvar($tmp_file_img_error, $tmp_file_type ,$tmp_file_img_tmp_name ,$tmp_file_img_size ,$tmp_file_img_name,$id_notif );
            }


        }  

    }

    public static function send_notifDocs_Byvar($tmp_file_img_error, $tmp_file_type ,$tmp_file_img_tmp_name ,$tmp_file_img_size ,$tmp_file_img_name, $id_notif ){

        $extension_upload = pathinfo($tmp_file_img_name, PATHINFO_EXTENSION);

        $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png', 'pdf', 'doc', 'docx', 'xlsx', 'xls');

        if (in_array($extension_upload, $extensions_autorisees)) {

            //var_dump('extension_upload' ,$extension_upload );exit;

            if ( isset( $tmp_file_img_error ) ) {

                if ( $tmp_file_img_error != UPLOAD_ERR_OK ) {
                    $msgResut ='error doc';
                    switch ( $tmp_file_img_error ){     
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
                }     
                else if( $tmp_file_img_error  == 0 ){

                    $chemin_destination = '../notifications/'.$id_notif.'/';

                    //Verification si documents existe
                    if(is_dir($chemin_destination)){

                        if (isset( $tmp_file_img_name  )) {
                            $fichier =  $chemin_destination.$tmp_file_img_name.'.'.$extension_upload;
                        }
                        else {
                            $fichier =  $chemin_destination.$id_notif.'_doc.'.$extension_upload;
                        }


                        if( file_exists ( $fichier)){
                            unlink( $fichier ) ;
                        }

                    }else{
                        mkdir($chemin_destination,0777);
                    }	

                    //Creation du fichier
                    if (isset( $tmp_file_img_name  )) {
                        $fichier =  $chemin_destination.$tmp_file_img_name.'.'.$extension_upload;
                    }
                    else {
                        $fichier =  $chemin_destination.$id_notif.'_doc.'.$extension_upload;
                    }
                    ;

                    if ( move_uploaded_file( $tmp_file_img_tmp_name, $fichier ) ){
                        unset( $tmp_file_img_name );
                        return 1;
                    } 
                    else {
                        unset( $tmp_file_img_name );
                        return 0;
                    }

                
                }     
            
            }  

        }
    }

    public static function send_notifDocs($id_notif){

            $tmp_file_img_error = (($_FILES['info_infoBundle_Info']['error'])['document'])['file'] ;
            $tmp_file_type = (($_FILES['info_infoBundle_Info']['type'])['document'])['file'] ;
            $tmp_file_img_tmp_name = (($_FILES['info_infoBundle_Info']['tmp_name'])['document'])['file'] ;
            $tmp_file_img_size = (($_FILES['info_infoBundle_Info']['size'])['document'])['file'] ;
            $tmp_file_img_name = (($_FILES['info_infoBundle_Info']['name'])['document'])['file'] ;


            //var_dump('tmp_file_img_error',$tmp_file_img_error);
            //var_dump('tmp_file_type',$tmp_file_type);
            //var_dump('tmp_file_img_tmp_name',$tmp_file_img_tmp_name);
            //var_dump('tmp_file_img_name',$tmp_file_img_name);

            //var_dump( $infosfichier);exit;
            $extension_upload = pathinfo($tmp_file_img_name, PATHINFO_EXTENSION);
            $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png', 'pdf', 'doc', 'docx', 'xlsx', 'xls');

            if (in_array($extension_upload, $extensions_autorisees)) {

                //var_dump('extension_upload' ,$extension_upload );exit;

                if ( (isset( (($_FILES['info_infoBundle_Info']['error'])['document'])['file'] )) ) {

                    if ( $tmp_file_img_error != UPLOAD_ERR_OK ) {
                        $msgResut ='error doc';
                        switch ( $tmp_file_img_error ){     
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
                    }     
                    else if( $tmp_file_img_error  == 0 ){
                        

                        //$chemin_destination = '../wai.ci/ftp/FTPUSER/'.$_FILES['file']['name'].'/';
                        $chemin_destination = '../notifications/'.$id_notif.'/';

                        //Verification si documents existe
                        if(is_dir($chemin_destination)){

                            if (isset( ($_POST['info_infoBundle_Info']['document'])['name']  )) {
                                $fichier =  $chemin_destination.($_POST['info_infoBundle_Info']['document'])['name'].'.'.$extension_upload;
                            }
                            else {
                                $fichier =  $chemin_destination.$id_notif.'_doc.'.$extension_upload;
                            }


                            if( file_exists ( $fichier)){
                                unlink( $fichier ) ;
                            }

                        }else{
                            mkdir($chemin_destination,0777);
                        }	

                        //Creation du fichier
                        if (isset( ($_POST['info_infoBundle_Info']['document'])['name']  )) {
                            $fichier =  $chemin_destination.($_POST['info_infoBundle_Info']['document'])['name'].'.'.$extension_upload;
                        }
                        else {
                            $fichier =  $chemin_destination.$id_notif.'_doc.'.$extension_upload;
                        }
                        ;

                        if ( move_uploaded_file( $tmp_file_img_tmp_name, $fichier ) ){
                            unset( $tmp_file_img_name );
                            return 1;
                        } 
                        else {
                            unset( $tmp_file_img_name );
                            return 0;
                        }

                    
                    }     
                
                }  

            }


    }

    public static function get_dossierContenu($dossier, $liens, $notifid){

        //var_dump($dossier);
        $dir_files = [];
        $liens = dirname($liens, 1);
        
        if( file_exists ( $dossier)){
           //echo 'exit ok';
           $finder = new Finder();
           // find all files in the current directory
           $finder->files()->in($dossier);
   
           // check if there are any search results
           if ($finder->hasResults()) {
               // ...
           }
   
           foreach ($finder as $file) {
               //$followLinks = $file->followLinks();
               $fileNameWithExtension = $file->getRelativePathname();
               $relativeFilePath = $liens.'/notifications/'.$notifid.'/'.$fileNameWithExtension;
   
               $absoluteFilePath = $file->getRealPath();
               $contents = $file->getContents();
               $infosfichier = pathinfo($absoluteFilePath);
               $extension_upload = $infosfichier['extension'];
               $dir_files["$fileNameWithExtension"] = array($relativeFilePath ,$extension_upload);
           }
           //var_dump($dir_files);exit;
           return $dir_files;
        }
        else{
            //echo 'non exit';
            return $dir_files;
        }
        //exit;

        
    }
}
