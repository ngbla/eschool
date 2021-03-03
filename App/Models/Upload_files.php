<?php

namespace App\Models;

use PDO;
use Symfony\Component\Finder\Finder;
class Upload_files extends \Core\Model{

    public static function send_uploadFiles($id_mat, $id_pers, $file , $datedebut_dispo , $datefin_dispo , $partie){

        //var_dump($id_mat, $id_pers, $file);
        //exit;

        $tmp_file_img_error = $file['error'] ;
        $tmp_file_type = $file['type'] ;
        $tmp_file_img_tmp_name = $file['tmp_name'] ;
        $tmp_file_img_size = $file['size'] ;
        $tmp_file_img_name = $file['name'];

        //var_dump('UPLOAD_ERR_OK',UPLOAD_ERR_OK);
        //var_dump('tmp_file_img_error',$tmp_file_img_error);
        //var_dump('tmp_file_type',$tmp_file_type);
        //var_dump('tmp_file_img_tmp_name',$tmp_file_img_tmp_name);
        //var_dump('tmp_file_img_name',$tmp_file_img_name);
        $infosfichier = pathinfo($tmp_file_img_name);
        $extension_upload = $infosfichier['extension'];
        $extensions_autorisees = array('mp4','pdf', 'doc', 'docx', 'xlsx', 'xls', 'pptx', 'ppt');

        if (in_array($extension_upload, $extensions_autorisees)) {

            //var_dump('extension_upload' ,$extension_upload );exit;

            if ( isset( $tmp_file_img_error )) {

                if ( $tmp_file_img_error != UPLOAD_ERR_OK ) {
                    //$msgResut ='error doc';
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
                else if( $tmp_file_img_error  == UPLOAD_ERR_OK ){
                    

                    $chemin_destination = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['SCRIPT_NAME' ],3).'/BanqueDefichiers/COURS/'.$id_pers.'/';
                    
                    //Verification si documents existe
                    if(is_dir($chemin_destination)){

                        $chemin_destination = $chemin_destination.$id_mat.'/';

                        if(is_dir($chemin_destination)){
                            $fichier =  $chemin_destination.$tmp_file_img_name.'.'.$extension_upload;
                            if( file_exists ($fichier)){ unlink( $fichier ) ; }
                        }else{
                            mkdir($chemin_destination,0777);
                        }

                    }
                    else{

                        mkdir($chemin_destination,0777);
                        $chemin_destination = $chemin_destination.$id_mat.'/';

                        if(is_dir($chemin_destination)){
                            $fichier =  $chemin_destination.$tmp_file_img_name.'.'.$extension_upload;
                            if( file_exists ($fichier)){ unlink( $fichier ) ; }
                        }else{
                            mkdir($chemin_destination,0777);
                        }
                    }	

                    //Creation du fichier
                    $fichier =  $chemin_destination.$tmp_file_img_name;

                    if ( move_uploaded_file( $tmp_file_img_tmp_name, $fichier ) ){
                                
                        /** $id_mat, $id_pers, $file ,, $datedebut_dispo , $datefin_dispo , $partie
                        * table docsvideo_de_cours
                        */
                        //	id_docsvideocour courplan_id id_matiere id_pers_prof type  video_libelle lien_docsvideo date_video dispo_datedebut dispo_datefin etat_docsvideo

                        $db = static::getDB();

                        
                        $sql=' SELECT * FROM docsvideo_de_cours WHERE courplan_id= "'.$partie.'" AND id_matiere= "'.$id_mat.'" AND id_pers_prof= "'.$id_pers.'" AND type= "d" AND lien_docsvideo= "'.$tmp_file_img_name.'"  LIMIT 1';
                        //var_dump($login,$pass,$sql);
                        //exit();
                        $stmt = $db->query($sql);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                        if ( empty($result) || $result==0) {
                
                            $data = [
                                'courplan_id' => $partie,
                                'id_matiere' => $id_mat,
                                'id_pers_prof' => $id_pers,
                                'type' => 'd',
                                'video_libelle' => $tmp_file_img_name,
                                'lien_docsvideo' => $tmp_file_img_name,
                                'dispo_datedebut' => $datedebut_dispo,
                                'dispo_datefin' => $datefin_dispo
                            ];
                            //var_dump($db);
                                    
                            $sql=' INSERT INTO docsvideo_de_cours (courplan_id, id_matiere, id_pers_prof , type , video_libelle, lien_docsvideo , dispo_datedebut, dispo_datefin) VALUES ( :courplan_id, :id_matiere, :id_pers_prof , :type , :video_libelle , :lien_docsvideo , :dispo_datedebut, :dispo_datefin);';
                            $stmt= $db->prepare($sql);
                            $result = $stmt->execute($data);
                            $lastid =  $db->lastInsertId();
                            //var_dump($lastid);
                            if ( $result == TRUE) {  return 'success';  } 
                            else {   return 'error'; }
                
                        }
                        else {  return 'error'; }

                    } 
                    else {  return 'error';  }

                    unset( $tmp_file_img_name );
                    unset( $file );

                
                }     
            
            }  

        }
    }
    
    public static function send_upload_eval_Files($eval_id, $id_pers, $file ){

        //var_dump($id_mat, $id_pers, $file);
        //exit;

        $tmp_file_img_error = $file['error'] ;
        $tmp_file_type = $file['type'] ;
        $tmp_file_img_tmp_name = $file['tmp_name'] ;
        $tmp_file_img_size = $file['size'] ;
        $tmp_file_img_name = $file['name'];

        //var_dump('UPLOAD_ERR_OK',UPLOAD_ERR_OK);
        //var_dump('tmp_file_img_error',$tmp_file_img_error);
        //var_dump('tmp_file_type',$tmp_file_type);
        //var_dump('tmp_file_img_tmp_name',$tmp_file_img_tmp_name);
        //var_dump('tmp_file_img_name',$tmp_file_img_name);
        $infosfichier = pathinfo($tmp_file_img_name);
        $extension_upload = $infosfichier['extension'];
        $extensions_autorisees = array('mp4','pdf', 'doc', 'docx', 'xlsx', 'xls', 'pptx', 'ppt');

        if (in_array($extension_upload, $extensions_autorisees)) {

            //var_dump('extension_upload' ,$extension_upload );exit;

            if ( isset( $tmp_file_img_error )) {

                if ( $tmp_file_img_error != UPLOAD_ERR_OK ) {
                    //$msgResut ='error doc';
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
                else if( $tmp_file_img_error  == UPLOAD_ERR_OK ){
                    

                    $chemin_destination = $_SERVER['DOCUMENT_ROOT'].dirname($_SERVER['SCRIPT_NAME' ],3).'/BanqueDefichiers/evaluations/'.$eval_id.'/';
                    
                    //Verification si documents existe
                    if(is_dir($chemin_destination)){

                        $chemin_destination = $chemin_destination.$id_pers.'/';

                        if(is_dir($chemin_destination)){
                            $fichier =  $chemin_destination.$tmp_file_img_name.'.'.$extension_upload;
                            if( file_exists ($fichier)){ unlink( $fichier ) ; }
                        }else{
                            mkdir($chemin_destination,0777);
                        }

                    }
                    else{

                        mkdir($chemin_destination,0777);
                        $chemin_destination = $chemin_destination.$id_pers.'/';

                        if(is_dir($chemin_destination)){
                            $fichier =  $chemin_destination.$tmp_file_img_name.'.'.$extension_upload;
                            if( file_exists ($fichier)){ unlink( $fichier ) ; }
                        }else{
                            mkdir($chemin_destination,0777);
                        }
                    }	

                    //Creation du fichier
                    $fichier =  $chemin_destination.$tmp_file_img_name;

                    if ( move_uploaded_file( $tmp_file_img_tmp_name, $fichier ) ){
                                
                        $db = static::getDB();

                        
                        $sql=' SELECT * FROM eval_docs  WHERE id_personne= "'.$id_pers.'" AND id_eval= "'.$eval_id.'" AND docs= "'.$tmp_file_img_name.'"   LIMIT 1';
                        //var_dump($login,$pass,$sql);
                        //exit();
                        $stmt = $db->query($sql);
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                        if ( empty($result) || $result==0) {
                
                            $data = [
                                'id_personne' => $id_pers,
                                'id_eval' => $eval_id,
                                'docs' => $tmp_file_img_name
                            ];
                            //var_dump($db);
                                    
                            $sql=' INSERT INTO eval_docs (id_personne, id_eval, docs ) VALUES ( :id_personne, :id_eval, :docs );';
                            $stmt= $db->prepare($sql);
                            $result = $stmt->execute($data);
                            $lastid =  $db->lastInsertId();
                            //var_dump($lastid);
                            if ( $result == TRUE) {  return 'success';  } 
                            else {   return 'error'; }
                
                        }
                        else {  return 'error'; }

                    } 
                    else {  return 'error';  }

                    unset( $tmp_file_img_name );
                    unset( $file );

                
                }     
            
            }  

        }
    }

    public static function get_dossierContenu($dossier,$liens){
        //($dossier, $liens, $notifid)


        
        if (is_dir($dossier)) {

            $dir_files = [];
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
                $relativeFilePath = $liens.$fileNameWithExtension;

                $absoluteFilePath = $file->getRealPath();
                $contents = $file->getContents();
                $infosfichier = pathinfo($absoluteFilePath);
                $extension_upload = $infosfichier['extension'];
                $dir_files["$fileNameWithExtension"] = array($relativeFilePath ,$extension_upload);
            }
            //var_dump($dir_files);exit;
            return $dir_files;

        }
        else {
            return 0;
        }

        
    }
    
}
