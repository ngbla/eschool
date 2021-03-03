<?php 
	header("Access-Control-Allow-Origin: *");
	require_once('../../App/Config.php');
	require_once('../../Core/Model.php');

	require_once('Prof.php');
	require_once('Upload_files.php');
		
	require_once('User.php');
	require_once('Log.php');

	use Core\Model;
	use App\Models;
	use App\Config;

	use App\Models\Log;
	use App\Models\User;
	use App\Models\Prof;
	use App\Models\Upload_files;

	//var_dump($_POST);exit;
	
 
	//Mise à jour bd
	if ((isset($_POST['action'])) && ($_POST['action'] == 'getProfmatiereByGrp')) {

		unset($_POST['action']);
		$id_prof =intval(htmlspecialchars($_POST['id_prof']) );
		$id_groupe =intval(htmlspecialchars($_POST['id_groupe']) );
		
        //var_dump($_SESSION);exit();
		//$result['getProfMatriere'] = Prof::getProfMatriere($id_prof);
		$result['getProfMatriere'] = Prof::getProfMatriereBy($id_prof, $id_groupe);

		//var_dump($result);exit();

		// Historique
		/**/
		$info = "Get info prof";
		$log_user =" Id prof =".$id_prof." Recup liste matiere ";
		Log::setLog($info,$log_user);

			//return $ersultrqt;
		echo json_encode($result['getProfMatriere'] );
	

	}
	if ((isset($_POST['action'])) && ($_POST['action'] == 'get_anneepartie_ByGrpe')) {

		unset($_POST['action']);
		$id_groupe =intval(htmlspecialchars($_POST['id_groupe']) );

		$result['getProfMatriere'] = Prof::get_anneepartie_ByGrpe($id_groupe);

		$info = "Get Groupe part annee";
		$log_user =" Id id_groupe =".$id_groupe." Recup liste part annee ";

		Log::setLog($info,$log_user);

			//return $ersultrqt;
		echo json_encode($result['getProfMatriere'] );
	

	}

	//Mise à jour bd Notes
	if ((isset($_POST['action'])) && ($_POST['action'] == 'Set_eleve_eval_notes')) {

		unset($_POST['action']);
		
		$fct_exec="|";

		if ( isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}

		$id_eleve_eleve =intval(htmlspecialchars($_POST['id_eleve_eleve']) );
		$eval_id =intval(htmlspecialchars($_POST['eval_id']) );
		$note_val=floatval(htmlspecialchars($_POST['note_val']) );

		//var_dump($_SESSION);exit();
		$result['Set_eleve_eval_notes'] = Prof::setProfEval_elevnote($id_eleve_eleve, $eval_id,$global_admin, $note_val);
		$fct_exec=$fct_exec." Prof::setProfEval_elevnote(".$id_eleve_eleve.", ".$eval_id.", ".$global_admin.",".$note_val.");| ";

		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : Set_eleve_eval_notes => " . $fct_exec;
		$log_user ="Saisie de note pour : Eleve =".$id_eleve_eleve." & Evaluation =".$eval_id." & Notes=".$note_val;
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::

		echo json_encode($result['Set_eleve_eval_notes'] );
	}

	//Mise à jour bd
	if ((isset($_POST['action'])) && ($_POST['action'] == 'upload_file')) {

		unset($_POST['action']);
		//var_dump($_POST);
		//exit;
		
		if (isset($_POST['id_pers'])  && isset($_POST['id_mat']) && isset($_FILES['file'])  ) {

			//var_dump($_FILES);

			$id_pers =intval(htmlspecialchars($_POST['id_pers']) );
			$id_mat =intval(htmlspecialchars($_POST['id_mat']) );

			$datedebut_dispo =(htmlspecialchars($_POST['datedebut_dispo']) );
			$datefin_dispo =(htmlspecialchars($_POST['datefin_dispo']) );
			$partie =intval(htmlspecialchars($_POST['partie']) );

			//var_dump($_SESSION);exit();
			$result['send_uploadFiles'] = Upload_files::send_uploadFiles($id_mat, $id_pers, $_FILES['file'], $datedebut_dispo , $datefin_dispo , $partie);
	
			//var_dump($result);exit();
	
			// Historique
			/**/
			$info = "Set prof cours docs";
			$log_user =" Id id_pers =".$id_pers." Add documents to matiere = ". $id_mat;
			Log::setLog($info,$log_user);
	
			//return $ersultrqt;
			echo json_encode($result['send_uploadFiles'] );
			
		}

	

	}

		//Mise à jour bd
	if ((isset($_POST['action'])) && ($_POST['action'] == 'upload_eval_file')) {

		unset($_POST['action']);
		//var_dump($_POST);
		
		if (isset($_POST['id_pers'])  && isset($_POST['eval_id']) && isset($_FILES['file'])  ) {

			//var_dump($_FILES,$_POST);

			$id_pers =intval(htmlspecialchars($_POST['id_pers']) );
			$eval_id =intval(htmlspecialchars($_POST['eval_id']) );

			//var_dump($_SESSION);exit();
			$result['send_uploadFiles'] = Upload_files::send_upload_eval_Files($eval_id, $id_pers, $_FILES['file']);
	
			//var_dump($result);exit();
	
			// Historique
			/**/
			$info = "Set Eleve evaluation docs";
			$log_user =" Id id_pers =".$id_pers." Add documents to Evaluation = ". $eval_id;
			Log::setLog($info,$log_user);
	
			//return $ersultrqt;
			echo json_encode($result['send_uploadFiles'] );
			
		}
		else {
			echo json_encode('erreur post vide');
		}

	

	}




	




 ?>