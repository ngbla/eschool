<?php 
	header("Access-Control-Allow-Origin: *");
	require_once('../../App/Config.php');
	require_once('../../Core/Model.php');

	require_once('Admin.php');
	require_once('Prof.php');
	require_once('Model_public.php');
	require_once('Smseco.php');
	require_once('Smsmondial.php');
	
		
	require_once('User.php');
	require_once('Log.php');

	use App\Models\Log;
	use App\Models\User;
	use App\Models\Admin;
	use App\Models\Prof;
	use App\Models\Model_public;
	use App\Models\Smseco;
	use App\Models\Smsmondial;
	use Core\Model;
	use App\Config;

	//var_dump($_SERVER);
	//var_dump($_POST);exit();
 
	//Mise à jour bd
	if ((isset($_POST['action'])) && ($_POST['action'] == 'activecpte')) {

		unset($_POST['action']);
		$fct_exec = "|| ";

		$idpers =htmlspecialchars($_POST['idpers']) ;
		$idtype =htmlspecialchars($_POST['idtype']) ;
		$type =htmlspecialchars($_POST['type']) ;
		$mode =htmlspecialchars($_POST['mode']) ;
		

		if ( isset($_POST['id_role']) ) {
			$id_role = htmlspecialchars($_POST['id_role']) ;
		}
		else {
			$id_role = 0;
		}

		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}

		if ($mode==0) {
			$table="personne";
			$tb_conditions=[];
			$tb_conditions['id_pers_personne']=$idpers;
			$resultat = Admin::set_deleteSQL_ALL_by($table,$tb_conditions);
			
			$fct_exec =$fct_exec." Admin::set_deleteSQL_ALL_by(".$idpers.",".$idtype.",".$type.",".$mode.",".$id_role.");";
			$fct_exec =$fct_exec." Id Personne =".$idpers." supression user mode:  ". $mode ." || Resultat : $resultat ";
			switch ($type) {
				case 1:
					# ELEVE...
					$table="eleve";
					$tb_conditions=[];
					$tb_conditions['id_eleve_eleve']=$idtype;
					break;
				case 2:
					# PROF...
					$table="prof";
					$tb_conditions=[];
					$tb_conditions['id_prof_prof']=$idtype;
					break;
				case 3:
					# PARENT...
					$table="parent";
					$tb_conditions=[];
					$tb_conditions['id_parent_parent']=$idtype;
					break;
				case 4:
					# ADMIN...
					$table="admintab";
					$tb_conditions=[];
					$tb_conditions['id_admin_admin']=$idtype;
					break;
				default:
					# code...
					break;
			}

			$resultat = Admin::set_deleteSQL_ALL_by($table,$tb_conditions);
			
		}
		else{
			$resultat = User::setEtatPers($idpers,$idtype,$type,$mode,$id_role);

			$fct_exec =$fct_exec." User::setEtatPers(".$idpers.",".$idtype.",".$type.",".$mode.",".$id_role.");";
			$fct_exec =$fct_exec." Id Personne =".$idpers." Mise a jour Etat à ". $mode ." || Resultat : $resultat ";
		}

		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : activecpte => " . $fct_exec;
		$log_user =" Activation compte";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::

		echo $resultat ;
		echo 1;

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'change_role')) {

		unset($_POST['action']);
		
		$resultat = 0;
		$fct_exec = "|| ";

		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}


		if ( isset($_POST['infos']) ) {
			//{{value.id_role}}_{{value.id_admin_admin}}
			$infos =htmlspecialchars($_POST['infos']) ;
			$infos_tab = explode("_",$infos);
			$id_admin_admin=intval($infos_tab[1]);
			$id_role=intval($infos_tab[0]);
			$resultat = Admin::update_role($id_admin_admin,$id_role);

			$info = "Mise à jour Role Admin";
			$log_user =" Id Admin =".$id_admin_admin." Mise a jour Role à ". $id_role ." Resultat : $resultat ";
			Log::setLog($info,$log_user);
		}

		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : change_role => " . $fct_exec;
		$log_user =" Changement de Rôle utilisateur";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::

		echo json_encode($resultat);

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'getClassesMatiere')){

		unset($_POST['action']);
		$fct_exec = "|| ";

		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}

		//var_dump($_POST);
		if (isset($_POST['annee_id'])) {
			$annee_id =htmlspecialchars($_POST['annee_id']) ;
			$result["anneescolairepart"] = Admin::getAnneeScolaireBy($annee_id);
		}
		if (isset($_POST['classe_id'])) {
			$classe_id =htmlspecialchars($_POST['classe_id']) ;
			$result["classematiere"] = Admin::getClasseMatiereBy($classe_id);
		}
		if (isset($_POST['filiere_id']) && isset($_POST['niveau_id'])) {
			$filiere_id =intval(htmlspecialchars($_POST['filiere_id'])) ;
			$niveau_id =intval(htmlspecialchars($_POST['niveau_id'])) ;
			$result["classematiere"] = Admin::get_filiereNivo_MatiereBy($filiere_id,$niveau_id);
		}


		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : getClassesMatiere => " . $fct_exec;
		$log_user ="Get Infos : annee partie and classe matiere";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::

		//print_r( $result);
		echo json_encode($result);
	}


	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'getGrpeClassMatiere')){

		if (isset($_POST['groupeid'])) {

			$fct_exec="|| ";

			if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
				$global_admin = intval(htmlspecialchars($_POST['global_admin']));
				$global_univ = intval(htmlspecialchars($_POST['global_univ']));
			}
			else {
				$global_admin = 0;
				$global_univ = 0;
			}

			$groupeid =intval(htmlspecialchars($_POST['groupeid'])) ;

			
			$result["grpematiere"] = Admin::get_gpreALLMat_By($groupeid);
			$fct_exec="Admin::get_gpreALLMat_By(".$groupeid.")";
			// Historique
			/**/
			//:::::::::::::LOGS::::::::::::::::::
			$info = "AJAX ::: Action : getGrpeClassMatiere => " . $fct_exec;
			$log_user =" Récupération des matières d'une classe";
			Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
			//:::::::::::::LOGS::::::::::::::::::


			//print_r( $result);
			echo json_encode($result);

		}

	}
	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'get_Matiere_by_gpePeriode')){

		if (isset($_POST['groupeid']) && isset($_POST['periode'])) {

			$fct_exec="|| ";

			if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
				$global_admin = intval(htmlspecialchars($_POST['global_admin']));
				$global_univ = intval(htmlspecialchars($_POST['global_univ']));
			}
			else {
				$global_admin = 0;
				$global_univ = 0;
			}

			$groupeid =intval(htmlspecialchars($_POST['groupeid'])) ;
			$periode =intval(htmlspecialchars($_POST['periode'])) ;

			
			$result["grpematiere"] = Admin::get_mat_By_gpeperiode($groupeid,$periode);
			$fct_exec="Admin::get_mat_By_gpeperiode(".$groupeid.",".$periode.")";
			// Historique
			/**/
			//:::::::::::::LOGS::::::::::::::::::
			$info = "AJAX ::: Action : getGrpeClassMatiere => " . $fct_exec;
			$log_user =" Récupération des matières d'une classe par période";
			Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
			//:::::::::::::LOGS::::::::::::::::::


			//print_r( $result);
			echo json_encode($result);

		}

	}
	

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'getListProf')){

		unset($_POST['action']);
		
		$fct_exec = "|| ";

		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}

		$result["listeProf"] = Admin::getProf($global_univ);
		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : getListProf => " . $fct_exec;
		$log_user ="Get infos : Listes prof ";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::
		echo json_encode($result);
		//$result_return =(object) $result;
		///print_r($result_return)  ;

	}
	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'getListAdmin')){

		unset($_POST['action']);
		
		$fct_exec = "|| ";

		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}

		$result["listeProf"] = Admin::getAdmin($global_univ);
		$fct_exec=$fct_exec." Admin::getAdmin(".$global_univ.")";
		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : getListAdmin => " . $fct_exec;
		$log_user ="Get infos : Listes Admin ";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::
		echo json_encode($result);
		//$result_return =(object) $result;
		///print_r($result_return)  ;

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'setGroupe')){

		unset($_POST['action']);
				
		$fct_exec = "|| ";

		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}

		$result["setGroupe"] = Admin::setGroupe();
		
		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : setGroupe => " . $fct_exec;
		$log_user ="Set Get infos : Ajout ou recup Groupe";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::
		echo json_encode($result);

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'setGroupeMat')){

		unset($_POST['action']);
				
		$fct_exec = "|| ";

		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}

		$result["setGroupeMat"] = Admin::setGroupeMat();

		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : setGroupeMat => " . $fct_exec;
		$log_user ="Set Matiere :  Ajout ou recup Groupe";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::
		echo json_encode($result);

		//$result_return =(object) $result;
		///print_r($result_return)  ;

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'getGroupList')){

		unset($_POST['action']);
		
		$fct_exec = "|| ";

		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}

		$id_groupe = htmlspecialchars($_POST['id_groupe']);
		$result["getSousmatiereBy"] = Admin::getSousmatiereBy($id_groupe);
		$result["getMatiereBy"] = Admin::getMatiereBy($id_groupe);

		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : getGroupList => " . $fct_exec;
		$log_user ="Get infos :  Listes prof ";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::
		//print_r( $result);
		echo json_encode($result);
		//$result_return =(object) $result;
		///print_r($result_return)  ;

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'setEmploiTps')){

		unset($_POST['action']);
		
		$fct_exec = "|| ";

		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}

		$result["setEmploiTps"] = Admin::setEmploiTps();

		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : setEmploiTps => " . $fct_exec;
		$log_user ="Set  infos :  Ajout setEmploiTps";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::
		echo json_encode($result);
		//$result_return =(object) $result;
		///print_r($result_return)  ;

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'getAllGrpByannee')){
		unset($_POST['action']);
		//var_dump("post",$_POST);exit();
		$fct_exec="";
		$annee_id = intval(htmlspecialchars($_POST["annee_id"]));
		
		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) && isset($_POST['global_univ'])) {
				$global_admin = intval(htmlspecialchars($_POST['global_admin']));
				$global_univ = intval(htmlspecialchars($_POST['global_univ']));
			}
			else {
				$global_admin = 0;
				$global_univ = 0;
			}
		
		$result["getAllGrpByannee"] = Admin::get_univALLgrpeBy($annee_id,$global_univ);
		$fct_exec = "Admin::get_univALLgrpeBy(".$annee_id .",".$global_univ. ") || ";

		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : getAllGrpByannee => " . $fct_exec;
		$log_user =" Récupération liste des classes par année scolaire et univ";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::
		//print_r( $result);
		echo json_encode($result);
		//$result_return =(object) $result;
		///print_r($result_return)  ;

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'getGrpElve')){

		unset($_POST['action']);
		
		$fct_exec = "|| ";

		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}

		$id_grp = intval(htmlspecialchars($_POST['classe_id'])); 
		$id_annee = intval(htmlspecialchars($_POST['annee_id'])); 
		//var_dump("id_grp",$id_grp);
		//var_dump("id_annee",$id_annee);exit();

		$result["getGrpElve"] = Admin::getAllElevByGrp($id_grp);
		$result["getGrpNotElve"] = Admin::getAllElevNotGrp($id_annee,$id_grp);

		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : getGrpElve => " . $fct_exec;
		$log_user ="Get infos :  getGrpElve and getGrpNotElve";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::
		echo json_encode($result);
		//$result_return =(object) $result;
		///print_r($result_return)  ;

	}

	//::::A CONTINUER ................ ::::::::::

	elseif ( (isset($_POST['action'])) && ($_POST['action'] == 'get_allEleveInGrpe') ){

		unset($_POST['action']);

		if (isset($_POST['groupeid'])) {

			$id_groupe = intval(htmlspecialchars($_POST['groupeid'])); 
			$fct_exec="|| ";

			if (isset($_POST['global_admin']) && isset($_POST['global_univ']) && isset($_POST['global_univ'])) {
				$global_admin = intval(htmlspecialchars($_POST['global_admin']));
				$global_univ = intval(htmlspecialchars($_POST['global_univ']));
			}
			else {
				$global_admin = 0;
				$global_univ = 0;
			}
 
			
			$result["getAllElevByGrp"] = Admin::getAllElevByGrp($id_groupe);
			$fct_exec="Admin::getAllElevByGrp(".$id_groupe.")";
			// Historique
			/**/
			//:::::::::::::LOGS::::::::::::::::::
			$info = "AJAX ::: Action : get_allEleveInGrpe => " . $fct_exec;
			$log_user =" Récupération des élèves d'une classe";
			Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
			//:::::::::::::LOGS::::::::::::::::::

			echo json_encode($result);
			///print_r($result_return)  ;

		}

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'setClassesElveve')){

		unset($_POST['action']);
		$result =[];
		//var_dump("post",$_POST);exit();
		$groupe = intval(htmlspecialchars($_POST['groupe'])); 
		$idelev = intval(htmlspecialchars($_POST['idelev'])); 
		$mode = intval(htmlspecialchars($_POST['mode'])); 

		switch ($mode) {
			case 0:
				$result["setClassesElveve"] = Admin::setDeleteGrpEleve($groupe,$idelev);
			break;
			case 1:
				$result["setClassesElveve"] = Admin::setGrpToEleve($groupe,$idelev);
			break;
			
			default:
				# code...
			break;
		}

		$info = "Set  infos : ";
		$log_user =" setClassesElveve ::: ---> mode = ".$mode." // idelev = ".$idelev."// groupe".$groupe ;
		Log::setLog($info,$log_user);
		echo json_encode($result);


	}

	//::::MENU GESTION DES CLASSES::::::::::
	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'attribution_emlploiTps_getPeriode')){
		unset($_POST['action']);

		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) && isset($_POST['global_univ'])) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}
		//var_dump("post",$_POST);exit();
		$id = intval(htmlspecialchars($_POST['annee_id'])) ;

		$fct_exec='';

		$result["getAllpartAnneeBy"] = Admin::getAnneeScolaireBy($id);
		$fct_exec="Admin::getAnneeScolaireBy(".$id.")";

		//$result["getAllGrpBy"] = Admin::getGroupeBy($id);
		$result["getAllGrpBy"] = Admin::get_univALLgrpeBy($id, $global_univ);
		//var_dump($result["getAllGrpBy"]);
		$fct_exec="Admin::get_univALLgrpeBy(".$id.",".$global_univ.")";

		/*Historique*/
		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : attribution_emlploiTps_getPeriode => " . $fct_exec;
		$log_user =" Récupération liste de classe et période";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::

		//print_r( $result);
		echo json_encode($result);

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'attribution_emlploiTps_getProg')){

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

		//var_dump("post",$_POST);exit();
		$id_groupe = intval(htmlspecialchars($_POST['id_groupe'])) ;
		$result["getEmploiTpsBy"] = Admin::get_GroupEmploiDutpsBy($id_groupe);
		$fct_exec=" Admin::get_GroupEmploiDutpsBy(".$id_groupe.");";


		//print_r( $result);
		echo json_encode($result);
		//$result_return =(object) $result;
		///print_r($result_return)  ;
		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : attribution_emlploiTps_getProg => " . $fct_exec;
		$log_user ="Recuperation de l'emploi du temps";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::
	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'attribution_emlploiTps_getProgall')){

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

		//var_dump("post",$_POST);exit();
		$id_anneescol = intval(htmlspecialchars($_POST['id_anneescol'])) ;
		$result["getEmploiTpsBy"] = Admin::get_allGroupEmploiDutpsBy($id_anneescol);
		$fct_exec=" Admin::get_allGroupEmploiDutpsBy(".$id_anneescol.");";

		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : attribution_emlploiTps_getProg => " . $fct_exec;
		$log_user ="Recuperation de l'emploi du temps";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::
		//print_r( $result);
		echo json_encode($result);
		//$result_return =(object) $result;
		///print_r($result_return)  ;
	}	

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'attribution_emlploiTps_matiere')){

		unset($_POST['action']);
		//var_dump("post",$_POST);exit();
		$groupeid = intval(htmlspecialchars($_POST['grpe_id'])) ;
		$partannee = intval(htmlspecialchars($_POST['part_id'])) ;
		$result["getMatiereByPartGrp"] = Admin::getPartieMatiereBy($groupeid, $partannee);
		//var_dump($result['setEmploiTps']);exit();
		$info = "get infos : ";
		$log_user =" getMatiereByPartGrp " ;
		Log::setLog($info,$log_user);
		//print_r( $result);
		echo json_encode($result);
		//$result_return =(object) $result;
		///print_r($result_return)  ;
	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'get_eval_prog')){

		unset($_POST['action']);
		$fct_exec=" | ";
		if ( isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}
         
		//var_dump("post",$_POST);exit();
		$id_annee = intval(htmlspecialchars($_POST['id_annee'])); 

		//$result["get_eval_prog"] = Admin::getAllEval_ByAnne($id_annee);
		$result["get_eval_prog"] = [];

		$getAllEval_ByAnne = Admin::getAllEval_ByAnne($id_annee);
		foreach ($getAllEval_ByAnne as $key => $value) {
			array_push($result["get_eval_prog"],$value);
		}
		$getAllEval_adminByAnne = Admin::getAllEval_adminByAnne($id_annee);
		foreach ($getAllEval_adminByAnne as $key => $value) {
			array_push($result["get_eval_prog"],$value);
		}
		
        //var_dump($getAllEval_ByAnne[0],$getAllEval_adminByAnne[0]);

		$fct_exec=$fct_exec."Admin::getAllEval_ByAnne(".$id_annee."); ";


		echo json_encode($result);


		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : get_eval_prog => " . $fct_exec;
		$log_user =" Récupération liste des évaluations programmer";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::


	}


	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'get_allFilliere')){

		unset($_POST['action']);
		$result["get_allFilliere"] = Admin::get_allFilliere();
		$info = "Get infos : ";
		$log_user =" Listes Fillières (get_allFilliere)" ;
		Log::setLog($info,$log_user);

		echo json_encode($result);


	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'get_allGroupe_active')){

		unset($_POST['action']);
         
		//var_dump("post",$_POST);exit();
		$filliere_id = intval(htmlspecialchars($_POST['filliere_id'])); 

		$result["get_allGroupe_active"] = Admin::get_allGroupe_active($filliere_id);
		//var_dump($result);exit();
		$info = "get  infos : ";
		$log_user ="Liste des groupes par fillières (get_allGroupe_active)" ;
		Log::setLog($info,$log_user);
		//print_r( $result);
		echo json_encode($result);


	}

	
	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'update_elevMatr')){

		unset($_POST['update_elevMatr']);
		$result ="";
		//var_dump("post",$_POST);exit();
		$id_eleve = intval(htmlspecialchars($_POST['id_eleve'])); 
		$matricule = htmlspecialchars($_POST['matricule']); 
		$result= Admin::update_elevMatr($id_eleve,$matricule);

		$info = "Set Eleve matricule: ";
		$log_user =" update_elevMatr ::: ---> id_eleve = ".$id_eleve." // matricule = ".$matricule;
		Log::setLog($info,$log_user);
		echo json_encode($result);

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'setUpdate_elevGrp')){

		unset($_POST['setUpdate_elevGrp']);
		$result ="";
		//var_dump("post",$_POST);exit();
		$id_eleve = intval(htmlspecialchars($_POST['id_eleve'])); 
		$idgroupe = intval(htmlspecialchars($_POST['idgroupe'])); 
		$result= Admin::setGrpToEleve($idgroupe,$id_eleve);
		if ($result == 1) {
			$result = "success";
		}

		$info = "Set Eleve to groupe: ";
		$log_user =" setGrpToEleve ::: ---> id_eleve = ".$id_eleve." // idgroupe = ".$idgroupe;
		Log::setLog($info,$log_user);
		echo json_encode($result);

	}


	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'get_elevegroupes')){

		unset($_POST['action']);
         
		//var_dump("post",$_POST);exit();
		if( isset($_POST['id_eleve']) && isset($_POST['anneeScol']) ){

			$id_eleve = intval(htmlspecialchars($_POST['id_eleve'])); 
			$anneeScol = intval(htmlspecialchars($_POST['anneeScol'])); 

			$result = Admin::get_elevegroupesBy($id_eleve, $anneeScol);
			//var_dump($result);exit();
			$info = "get  infos : ";
			$log_user ="Liste des groupes de l'étudiant By id_eleve= ".$id_eleve."   anneeScol =".$anneeScol ;
			Log::setLog($info,$log_user);
			//print_r( $result);
			echo json_encode($result);

		}
		else{
			echo 0;
		}

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'get_groupes')){

		unset($_POST['action']);

		$fct_exec="";
         
		//var_dump("post",$_POST);exit();
		if(isset($_POST['anneeScol']) ){

			$anneeScol = intval(htmlspecialchars($_POST['anneeScol'])); 

			$result = Admin::getGroupeBy($anneeScol);
			$fct_exec = "Admin::getGroupeBy(".$anneeScol.")";
			echo json_encode($result);
		}
		else{
			echo 0;
		}

		if ( isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}
		
		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : get_groupes => " . $fct_exec;
		$log_user =" Récupération liste des groupes par année scolaire et univ";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'get_univALLgrpeBy')){

		unset($_POST['action']);
         
		

		if(isset( $_POST['infos_iduniv_id_annee'] )){
			$infos_tab= explode("_",$_POST['infos_iduniv_id_annee']);
			//var_dump($infos_tab);exit();
			$id_univ = intval($infos_tab[0]); 
			$annee_id = intval($infos_tab[1]); 

			$result = Admin::get_univALLgrpeBy($annee_id,$id_univ);

			//var_dump($result);exit();
			$info = "(AJAX) get infos : ";
			$log_user ="Liste des groupes (get_univALLgrpeBy)By anneeScol =".$annee_id."    iduniv =".$id_univ ;
			Log::setLog($info,$log_user);
			//print_r( $result);
			echo json_encode($result);

		}
		else{
			echo 0;
		}

	}

	
	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'set_resoAncien_etat')){

		unset($_POST['action']);
		$result= 0;
		//var_dump("post",$_POST);
		if(isset($_POST['id_etudiant']) && isset($_POST['etat_reso'])){
			
			$id_etudiant = intval(htmlspecialchars($_POST['id_etudiant'])); 
		
			$etat_reso = intval(htmlspecialchars($_POST['etat_reso'])); 
			//var_dump($id_etudiant );
			if ($etat_reso == 0 || $etat_reso ==1) {
				//var_dump($etat_reso );
				
				$result = Admin::setMAJ_Ancien_EtudiantBy($id_etudiant,$etat_reso);
				//var_dump($result );
				
				
			}

		}
		//exit();
		$info = "set infos : ";
		$log_user ="MAJ Reso des anciens etat =".$etat_reso." Resultat =".$result ;
		Log::setLog($info,$log_user);
	

		echo json_encode($result);

	}


	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'set_ancienInfos')){

		unset($_POST['action']);
		$result= 0;
		//var_dump("post",$_POST);
		if(isset($_POST['typeinfos']) && isset($_POST['info']) && isset($_POST['ancien_idetudiant'])){
			
			$typeinfos = htmlspecialchars($_POST['typeinfos']); 
			$info = htmlspecialchars($_POST['info']); 
			$id_etudiant = intval(htmlspecialchars($_POST['ancien_idetudiant'])); 
			$tble_sqlchamp = "";

			switch ($typeinfos) {
				case 'metier':
					$tble_sqlchamp = 'options';
				break;
				case 'poste':
					$tble_sqlchamp = 'profilcandidat';
				break;
				case 'entreprise':
					$tble_sqlchamp = 'activite';
				break;
				case 'tel':
					$tble_sqlchamp = 'contact';
				break;	
				case 'email':
					$tble_sqlchamp = 'email';
				break;
				
				default:
					echo json_encode($result);
					exit;
				break;
			}
		
			$result = Admin::set_ancienInfos($tble_sqlchamp,$info,$id_etudiant);

		}
		//exit();
		$info = "set infos : ";
		$log_user ="MAJ set_ancienInfos// tble_sqlchamp =".$tble_sqlchamp." && info =".$info." && id_etudiant =".$id_etudiant." && Resultat =".$result ;
		Log::setLog($info,$log_user);

		echo json_encode($result);

	}


	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'bulletin_lier_BilanNivFiliere')){

		unset($_POST['action']);
		$result= 0;
		//var_dump("post",$_POST);
		$infos="";
		if(isset($_POST['infos']) && $_POST['infos']!= "" && $_POST['infos']!= null){
			
			$infos = htmlspecialchars($_POST['infos']); 
			$infos_tab = explode("_",$infos); 
			//var_dump($infos_tab);
			//0{{value.id_niveau}}_1{{value.id_classe}}_2{{value.id_matiere}}_3{{id_bilan}}_4{{id_univ}}
			//$fk_bul_bilan,$fk_niveau,$fk_filiere,$fk_matiere,$fk_iduniv
			$result = Admin::set_bulletin_lier_BilanNivFiliere( intval($infos_tab[3]),intval($infos_tab[0]),intval($infos_tab[1]),intval($infos_tab[2]),intval($infos_tab[4]));

		}
		//exit();
		$info = "set infos : ";
		$log_user ="MAJ bulletin_lier_BilanNivFiliere// tble_sqlchamp =".$bulletin_lier_BilanNivFiliere." && info =".$infos;
		Log::setLog($info,$log_user);

		echo json_encode($result);

	}

	
	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'bulletin_lier_BilanNivFiliereSUP')){

		unset($_POST['action']);
		$result= 0;
		//var_dump("post",$_POST);
		$infos="";
		if(isset($_POST['infos']) && $_POST['infos']!= "" && $_POST['infos']!= null){
			
			$infos = htmlspecialchars($_POST['infos']); 
			$infos_tab = explode("_",$infos); 

			//0{{value.fk_iduniv}}_1{{value.fk_bul_bilan}}_2{{value.fk_niveau}}_3{{value.fk_filiere}}_4{{value.fk_matiere}}

			//$fk_bul_bilan,$fk_niveau,$fk_filiere,$fk_matiere,$fk_iduniv
			$result = Admin::set_delete_lier_BilanNivFiliere( intval($infos_tab[1]),intval($infos_tab[2]),intval($infos_tab[3]),intval($infos_tab[4]),intval($infos_tab[0]));

		}
		//exit();
		$info = "set infos : ";
		$log_user ="MAJ bulletin_lier_BilanNivFiliere// tble_sqlchamp =".$bulletin_lier_BilanNivFiliere." && info =".$infos;
		Log::setLog($info,$log_user);

		echo json_encode($result);

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'delete_courlement')){

		unset($_POST['action']);
		$result= 0;
		//var_dump("post",$_POST);
		$infos="";
		$fct="";
		if(isset($_POST['infos']) && $_POST['infos']!= "" && $_POST['infos']!= null){
			
			$infos = htmlspecialchars($_POST['infos']); 
			$infos_tab = explode("_",$infos); 

			switch ($infos_tab[0]) {
				case 'deldocs':
					$result=intval(Model_public::delete_courresource(intval($infos_tab[1])));
					$fct="|| Model_public::delete_courresource(".intval($infos_tab[1]).") ||";
					break;
				case 'delpartie':
					$result=intval(Model_public::delete_courpartie(intval($infos_tab[1])));
					$fct="|| Model_public::delete_courpartie(".intval($infos_tab[1]).") ||";
					break;
				case 'delsection':
					$result=intval(Model_public::delete_coursection(intval($infos_tab[1])));
					$fct="|| Model_public::delete_coursection(".intval($infos_tab[1]).") ||";
					break;		
				default:
					$result= 0;
					break;
			}

		}
		//exit();
		$info = "Ajax Delete infos : ";
		$log_user ="delete_courlement ,reqte =".$fct."// info =".$infos;
		Log::setLog($info,$log_user);

		echo json_encode($result);

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'MAJetudiantInfos')){

		unset($_POST['action']);
		$result= 0;
		$infos="";
		$fct="";  
		if(isset($_POST['infos']) && $_POST['infos']!= "" && $_POST['infos']!= null){
			$infos = htmlspecialchars($_POST['infos']); 
			$valeur = htmlspecialchars($_POST['valeur']); 
			$choix = htmlspecialchars($_POST['table']); 
						
			//14µanciennete( id_eleve_eleve /tble)
			//var_dump($infos_tab,$valeur);
			$infos_tab = explode("µ",$infos); 
			$id_etudiant= intval($infos_tab[0]);
			$table= htmlspecialchars($infos_tab[1]);
			if ($table=='pass') {$valeur=sha1($valeur);}
						
			switch ($choix) {
				case 'eleve':
					$result= intval(Admin::set_updateTbleeleve_etudiantinfos($id_etudiant, $table,$valeur));
					$fct="|Admin::set_updateTbleeleve_etudiantinfos(".$id_etudiant.", ".$table.",".$valeur.") |";

				break;
				case 'personne':
					$result= intval(Admin::set_updateTblePers_etudiantinfos($id_etudiant, $table,$valeur));
					$fct="|Admin::set_updateTblePers_etudiantinfos(".$id_etudiant.", ".$table.",".$valeur.") |";
				break;
				default:
					$result= 0;
				break;
			}
			
			
		}

		$info = "Ajax Update infos : ";
		$log_user ="MAJetudiantInfos ,reqte =".$fct."// info =".$infos."// result =".$result;
		Log::setLog($info,$log_user);

		echo json_encode($result);

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'MAJprofInfos')){

		unset($_POST['action']);
		$result= 0;
		$infos="";
		$fct="";  
		if(isset($_POST['infos']) && $_POST['infos']!= "" && $_POST['infos']!= null){
			$infos = htmlspecialchars($_POST['infos']); 
			$valeur = htmlspecialchars($_POST['valeur']); 
			$bd = htmlspecialchars($_POST['table']); 
						
			//14µanciennete( id_eleve_eleve /tble)
			//var_dump($infos_tab,$valeur);
			$infos_tab = explode("µ",$infos); 
			$id_personne= intval($infos_tab[0]);
			$champs= htmlspecialchars($infos_tab[1]);

			$table=$bd;
			$tb_infos=[];
			$tb_infos[$champs]=$valeur;

			$tb_conditions=[];			
			$tb_conditions['id_pers_personne']=$id_personne;
			$result=Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);

			$fct="|Admin::set_updateTblePers_profinfos(".$id_personne.", ".$table.",".$valeur.") |";


			$info = "Ajax Update infos : ";
			$log_user ="MAJprofInfos ,reqte =".$fct."// info =".$infos."// result =".$result;
			Log::setLog($info,$log_user);

			echo json_encode($result);

		}
	}
	

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'MAJ_eval_Infos')){

		unset($_POST['action']);
		//condition id_eval-valeur , champ-valeur 
		$result= 0;
		$infos="";
		$cond ="";
		$fct="";  
		$table="";  

		if( isset($_POST['cond']) && $_POST['cond'] != "" && $_POST['cond'] != null && isset($_POST['table']) ){

			$table = htmlspecialchars($_POST['table']);
			$cond = htmlspecialchars($_POST['cond']); 
			$infos = htmlspecialchars($_POST['infos']); 

			$tb_cond = explode("µ",$cond); 
			$tb_conditions[$tb_cond[0]] = intval($tb_cond[1]); 

			$tble_infos = explode("µ",$infos); 
			$tb_infos[$tble_infos[0]] = $tble_infos[1]; 
			 
			//var_dump($table,$tb_infos,$tb_conditions);
			$result= Model_public::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
			
			$fct="|Admin::set_updateSQL_ALL_by(".$table.", ".htmlspecialchars($_POST['infos']).",".htmlspecialchars($_POST['cond']).") |";

		}

		$info = "Ajax Update infos : ";
		$log_user ="MAJ_eval_Infos ,reqte =".$fct."// info =".htmlspecialchars($_POST['infos'])."// result =".$result;
		Log::setLog($info,$log_user);

		echo json_encode($result);

	}


	//EMPLOI DU TEMPS
	elseif ( (isset($_POST['action'])) && ($_POST['action'] == 'ajax_get_mat_prof') ){

		unset($_POST['action']);

		if (isset($_POST['prog_groupe'])) {

			$id_groupe = intval(htmlspecialchars($_POST['prog_groupe'])); 

			if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
				$global_admin = intval(htmlspecialchars($_POST['global_admin']));
				$global_univ = intval(htmlspecialchars($_POST['global_univ']));
			}
			else {
				$global_admin = 0;
				$global_univ = 0;
			}
 
			
			$result["get_all_gpr_matETprof"] = Admin::get_all_gpr_matETprof($id_groupe);
			$fct_exec="Admin::get_all_gpr_matETprof(".$id_groupe.")";
			// Historique
			/**/
			//:::::::::::::LOGS::::::::::::::::::
			$info = "AJAX ::: Action : ajax_get_mat_prof => " . $fct_exec;
			$log_user =" Récupération des matières d'une classe et des enseignants";
			Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
			//:::::::::::::LOGS::::::::::::::::::

			echo json_encode($result);
			///print_r($result_return)  ;

		}

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'mod_maj_eTps_info')){

		unset($_POST['action']);
		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}
		//condition id_eval-valeur , champ-valeur 
		$result= 0;
		$infos="";
		$cond ="";
		$fct_exec="";  
		$table="";  

		if( isset($_POST['mod_tableChps']) && $_POST['mod_tableChps'] != "" && $_POST['mod_tableChps'] != null && isset($_POST['mod_tableChps']) ){

			$cond = htmlspecialchars($_POST['mod_tableChps']); 
			$mod_id_eTps = htmlspecialchars($_POST['mod_id_eTps']); 
			$mod_value = htmlspecialchars($_POST['mod_value']); 
			$infos=$mod_id_eTps." ; ".$mod_value;

			$tb_cond = explode("@@",$cond); 

			$tb_conditions[$tb_cond[2]] = intval($mod_id_eTps); 
			$table=$tb_cond[0];
			$tb_infos[$tb_cond[1]] = $mod_value; 
			//var_dump($table,$tb_infos,$tb_conditions);
			$result= Model_public::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
			$fct_exec="|Model_public::set_updateSQL_ALL_by(".$table.", ".$infos.",".$cond.") |";

		}

		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : mod_maj_eTps_info => " . $fct_exec;
		$log_user =" Mise à jour Emploi du Temps ";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::

		echo json_encode($result);

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'mod_sup_eTps')){

		unset($_POST['action']);
		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}
		//condition id_eval-valeur , champ-valeur 
		$result= 0;
		$infos="";
		$cond ="";
		$fct_exec="";  
		$table="";  

		if( isset($_POST['mod_tableChps']) && $_POST['mod_tableChps'] != "" && $_POST['mod_tableChps'] != null && isset($_POST['mod_tableChps']) ){

			$cond = htmlspecialchars($_POST['mod_tableChps']); 
			$mod_id_eTps = htmlspecialchars($_POST['mod_id_eTps']); 
			$infos=$mod_id_eTps;
			//action= &         mod_tableChps=groupe_emploitps@@emploitps_id     
			$tb_cond = explode("@@",$cond); 

			$tb_conditions[$tb_cond[1]] = intval($mod_id_eTps); 
			$table=$tb_cond[0];
			$result= Model_public::set_deleteSQL_ALL_by($table,$tb_conditions);
			$fct_exec="|Model_public::set_deleteSQL_ALL_by(".$table.", ".$infos.",".$cond.") |";

		}

		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : mod_sup_eTps => " . $fct_exec;
		$log_user =" Delete Emploi du Temps ";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::

		echo json_encode($result);

	}

	//EMPLOI DU TEMPS
 	elseif ( (isset($_POST['action'])) && ($_POST['action'] == 'ajax_get_all_abs_elev') ){

		unset($_POST['action']);

		if (isset($_POST['id_group']) && isset($_POST['id_eleve'])) {

			$id_group = intval(htmlspecialchars($_POST['id_group'])); 
			$id_eleve = intval(htmlspecialchars($_POST['id_eleve'])); 

			if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
				$global_admin = intval(htmlspecialchars($_POST['global_admin']));
				$global_univ = intval(htmlspecialchars($_POST['global_univ']));
			}
			else {
				$global_admin = 0;
				$global_univ = 0;
			}
			
			$result["get_all_absence_elev"] = Admin::get_all_absence_elev($id_eleve,$id_group);
			$fct_exec="Admin::get_all_absence_elev(".$id_eleve.",".$id_group.")";
			// Historique
			/**/
			//:::::::::::::LOGS::::::::::::::::::
			$info = "AJAX ::: Action : ajax_get_all_abs_elev => " . $fct_exec;
			$log_user =" Récupération des absence d'un etudiants";
			Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
			//:::::::::::::LOGS::::::::::::::::::
			echo json_encode($result);
			///print_r($result_return)  ;

		}

	}

	
 	elseif ( (isset($_POST['action'])) && ($_POST['action'] == 'ajax_del_abs_elev') ){

		unset($_POST['action']);

		if ( isset($_POST['fk_emploitps']) ) {

			if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
				$global_admin = intval(htmlspecialchars($_POST['global_admin']));
				$global_univ = intval(htmlspecialchars($_POST['global_univ']));
			}
			else {
				$global_admin = 0;
				$global_univ = 0;
			}

			$fk_emploitps = intval(htmlspecialchars($_POST['fk_emploitps'])); 
			$table='absence_eleve';
			$tb_conditions=[];
			$tb_conditions['fk_emploitps']=$fk_emploitps;
			$result["get_all_absence_elev"] = Admin::set_deleteSQL_ALL_by($table,$tb_conditions);
			$fct_exec="Admin::set_deleteSQL_ALL_by(".$fk_emploitps.")  table=".$table;
				
			//:::::::::::::LOGS::::::::::::::::::
			$info = "AJAX ::: Action : ajax_del_abs_elev => " . $fct_exec;
			$log_user =" Supression Absence eleve";
			Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
			if ($result != 0) { $result="success"; }
			else { $result="danger"; }
			//:::::::::::::LOGS::::::::::::::::::
			echo json_encode($result);
			///print_r($result_return)  ;
		}


	}

 	elseif ( (isset($_POST['action'])) && ($_POST['action'] == 'set_classepp') ){

		unset($_POST['action']);

		if ( isset($_POST['id_prof']) && isset($_POST['idgroupe'])) {
			//var_dump($_POST);
			if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
				$global_admin = intval(htmlspecialchars($_POST['global_admin']));
				$global_univ = intval(htmlspecialchars($_POST['global_univ']));
			}
			else {
				$global_admin = 0;
				$global_univ = 0;
			}
			$fct_exec="|";
			$id_prof = intval(htmlspecialchars($_POST['id_prof'])); 
			$idgroupe = intval(htmlspecialchars($_POST['idgroupe'])); 
			$table='prof_principale';
			$tb_conditions=[];
			$tb_conditions['fk_id_group']=$idgroupe;
			$classe_pp = Admin::get_selectSQL_ALL_by($table, $tb_conditions);
			//var_dump('classe_pp',$classe_pp);
			if ($classe_pp ==0 || empty($classe_pp )) {
				$table='prof_principale';
				$tb_conditions=[];
				$tb_infos['fk_id_group']=$idgroupe;
				$tb_infos['fk_id_prof']=$id_prof;
				$tb_conditions['fk_id_group']=$idgroupe;
				$tb_conditions['fk_id_prof']=$id_prof;
				$result = Admin::set_insertSQL($table,$tb_infos, $tb_conditions);

				$fct_exec="Admin::set_insertSQL(id_prof=".$id_prof.", idgroupe=".$idgroupe."); table =".$table."|";
				//echo 'ok';
			}
			else {
				$table='prof_principale';
				$tb_conditions=[];
				$tb_conditions['fk_id_group']=$idgroupe;
				$tb_infos['fk_id_prof']=$id_prof;
				$result =  Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
				$fct_exec="Admin::set_updateSQL_ALL_by(id_prof=".$id_prof.", idgroupe=".$idgroupe."); table =".$table."|";

			}
			//var_dump('result',$result);
			//:::::::::::::LOGS::::::::::::::::::
			$info = "AJAX ::: Action : set_classepp => " . $fct_exec;
			$log_user =" Ajout PP à Classe";
			Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
			//:::::::::::::LOGS::::::::::::::::::
			echo json_encode($result);
			///print_r($result_return)  ;
		}


	}


	elseif ( (isset($_POST['action'])) && ($_POST['action'] == 'envoi_sms_auto') ){

		unset($_POST['action']);

		if (isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {

			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
			$global_univ_info = Admin::get_UnivInfosBy($global_univ);
			$send_sms_json = 0;
			$msg_send_result="Envoi sms ....";


			$get_sms_notif= Admin::get_sms_notif($global_univ);
			//var_dump($get_sms_notif);
			if (!empty($get_sms_notif) && $get_sms_notif !=0 ) {
				foreach ($get_sms_notif as $key => $value) {
					$id_notif=intval($value['id_notif']);
					$dest_tab=[];
					$get_sms_notif_usernum= Admin::get_sms_notif_usernum($id_notif,$global_univ);
					//var_dump('get_sms_notif_usernum',$get_sms_notif_usernum);
					if (!empty($get_sms_notif_usernum) && $get_sms_notif_usernum !=0 ) {

						$exp=$global_univ_info[0]['non_univ'];
						//$exp="ATLANTIQUE INTERNATIONAL BUSINESS SCHOOL (AIBS)";
						$msg=htmlspecialchars($value['notif_titre']).' \n '.htmlspecialchars($value['notif_desc']);


						foreach ($get_sms_notif_usernum as $keys => $values) {
							$values['contact'] =preg_replace("/\s+/", "", $values['contact']); 
							$tb_tel = explode("225",$values['contact']);
							if (count($tb_tel) == 1) {$values['contact'] =$tb_tel[0];}
							else {$values['contact'] =$tb_tel[1];}

							if ($exp=="ATLANTIQUE INTERNATIONAL BUSINESS SCHOOL (AIBS)") {
								array_push($dest_tab,$values['contact']);
							}
							else {
								$values['contact']="225".$values['contact']; 
								array_push($dest_tab,$values['contact']);
							}
						}
						//var_dump($dest_tab);

						if ($exp=="ATLANTIQUE INTERNATIONAL BUSINESS SCHOOL (AIBS)") {
							$send_sms_json= Smseco::send_sms_json($exp,$id_notif,$msg,$dest_tab);
							$jsonobj= json_decode($send_sms_json);
							$msg_send_result = "(".$jsonobj->statut.")|-|".($jsonobj->texte);
							//var_dump($send_sms_json);
						}
						else {
							$exp = strtoupper($global_univ_info[0]['initiale_univ']);
							$send_sms_json= Smsmondial::send_sms($exp,$id_notif,$msg,$dest_tab);
							$msg_send_result =$send_sms_json;
							//var_dump($send_sms_json);
						}
						//MISE A JOUR TABLE NOTIF USER
						foreach ($get_sms_notif_usernum as $keys => $values) {
							$table="notif_user";
							$tb_infos=[];
							$tb_infos['msg_send_result']=$msg_send_result;
							$tb_infos['usernotif_etat']=2;
							$tb_conditions=[];
							$tb_conditions['usernotif_typeuser']=$values['usernotif_typeuser'];
							$tb_conditions['usernotif_iduser']=$values['usernotif_iduser'];
							$tb_conditions['usernotif_id']=$id_notif;
							Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
						}

					}					
				}
			}

			echo json_encode($msg_send_result);

		}
		else {
			echo json_encode(0);
		}



	}
	
	
	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'get_annee_eval')){

		unset($_POST['action']);

		if ( isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}

		$fct_exec=" | ";
         
		//var_dump("post",$_POST);exit();
		if(isset($_POST['annee_id']) ){

			$annee_id = intval(htmlspecialchars($_POST['annee_id'])); 

			$result['get_eval_adminevalBy'] = Admin::get_eval_adminevalBy($annee_id);
			$result['get_eval_profevalBy'] = Admin::get_eval_profevalBy($annee_id);
			//var_dump($result3);
			$fct_exec = $fct_exec."Admin::get_eval_adminevalBy(".$annee_id.")";
			$fct_exec = $fct_exec."Admin::get_eval_adminevalBy(".$annee_id.")";
			echo json_encode($result);
		}
		else{
			echo 0;
		}
		
		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : get_annee_eval => " . $fct_exec;
		$log_user =" Récupération liste des évaluations année scolaire et univ";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::

	}

		//Mise à jour bd Notes
	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'Set_eleve_eval_notes')) {


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
		//var_dump($global_admin,$global_univ,$_POST);

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

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'Set_activemod_eleve_notes')) {


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
		//var_dump($global_admin,$global_univ,$_POST);

		$id_eleve_eleve =intval(htmlspecialchars($_POST['id_eleve_eleve']) );
		$eval_id =intval(htmlspecialchars($_POST['eval_id']) );

		$table="notes";
		$tb_infos=[];
		$tb_infos["etat_note"]=0;
		
		$tb_conditions["id_evaluation"]=$eval_id;
		$tb_conditions["id_eleve_eleve"]=$id_eleve_eleve;

		$result=Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);


		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : Set_eleve_eval_notes => " . $fct_exec;
		$log_user ="Activation modification de note : Eleve =".$id_eleve_eleve." & Evaluation =".$eval_id;
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::

		echo json_encode($result );
	}	
	
	//COUR PROFESSEUR MOD
	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'changecourplan_pos')) {

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
		//var_dump($global_admin,$global_univ,$_POST);

		$infos =htmlspecialchars($_POST['infos']);
		//possection_1
		$info_tab = explode('_',$infos);

		$table="cours_plan";
		$tb_conditions=[];
		$tb_infos=[];
		switch ($info_tab[0]) {
			case 'possection':
				$tb_conditions["plan_position"]="SECTION";
			break;
			case 'pospartie':
				$tb_conditions["plan_position"]="PARTIE";
			break;			
			default:
				exit();
			break;
		}

		$tb_infos["plan_position_num"]=intval(htmlspecialchars($_POST['valeur']) );
		$tb_conditions["id_cours_plan"]=intval($info_tab[1]);
		$result=Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);


		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : changecourplan_pos => " . $fct_exec;
		$log_user ="Changement Position plan : id_cours_plan =".intval($info_tab[1])." & plan_position_num =".$tb_infos["plan_position_num"];
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::

		echo json_encode($result );
	}	
	
	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'get_courplan_contenu')) {

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
		//var_dump($global_admin,$global_univ,$_POST);

		$infos =htmlspecialchars($_POST['infos']);
		$table="cours_plan";
		$tb_conditions=[];
		$tb_conditions["id_cours_plan"]=intval($infos);
		$cours_plan=Admin::get_selectSQL_ALL_by($table, $tb_conditions);
		//var_dump($cours_plan);
		$result=[];
		$result["id_courplan"]=$cours_plan[0]["id_cours_plan"];
		$result["plan_titre"]=$cours_plan[0]["plan_titre"];
		$result["plan_description"]=htmlspecialchars_decode($cours_plan[0]["plan_description"]);


		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : get_courplan_contenu => " . $fct_exec;
		$log_user ="Modification du cour : id_cours_plan =".intval($infos);
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::

		echo json_encode($result );
	}	


	//ETAT CONNECTEZ UTILISATEURS
	
	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'auto_user_initetat')) {

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
		//var_dump($global_admin,$global_univ,$_POST);

		$table="connexion";
		$tb_infos=[];
		$tb_infos["conex_etat"]=0;
		$tb_conditions=[];
		$tb_conditions["fk_iduniv"]=$global_univ;
		$result=Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);

		echo json_encode($result );
	}	
	
	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'set_user_etatconect')) {

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

		$result=Admin::set_user_etatconect($global_admin,$global_univ);

		echo json_encode($result );
	}		

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'get_userconnectnow')) {

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

		$result['eleve_conect'] = Admin::get_useronline_by(1,$global_univ);
        $result['prof_conect'] = Admin::get_useronline_by(2,$global_univ);
        $result['parent_conect'] = Admin::get_useronline_by(3,$global_univ);
        $result['admin_conect'] = Admin::get_useronline_by(4,$global_univ);

		echo json_encode($result);
	}		
	
	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'compte_actions')) {

		unset($_POST['action']);
		$fct_exec="|";
		$result=0;

		if ( isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}

		$infos = explode("_",htmlspecialchars($_POST['infos']));
		//{{info.id_pers_personne}}_{{info.id_type}}_{{info.id_type}}_2
		switch ($infos[3]) {
			case 0:
				//$result= $infos[3].' Désactivation';
				$fct_exec="| Désactivation du compte : ".intval($infos[0]);
				$table="personne" ; 
				$tb_infos=[] ;
				$tb_conditions=[] ;
				$tb_conditions["id_pers_personne"]= intval($infos[0]);
				$tb_infos["etat_pers"]= 99;
				Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
				$result="success";
			break;
			case 1:
				//$result= $infos[3].' Activation';
				$fct_exec="| Activation du compte : ".intval($infos[0]);
				$table="personne" ; 
				$tb_infos=[] ;
				$tb_conditions=[] ;
				$tb_conditions["id_pers_personne"]= intval($infos[0]);
				$tb_infos["etat_pers"]= 1;
				Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
				$result="success";
			break;	
			case 2:
				//$result= $infos[3].' Suprimer';
				$fct_exec="| Suppression du compte : ".intval($infos[0]);
				$table="personne" ; 
				$tb_infos=[] ;
				$tb_conditions=[] ;
				$tb_conditions["id_pers_personne"]=intval($infos[0]);
				$tb_infos["etat_pers"]= 66;

				Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
				$result="success";
			break;					
			default:
				$result=' aucun';
			break;
		}
		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : compte_actions => " . $fct_exec;
		$log_user ="Opération sur les comptes =".$infos[0]." -- ".$infos[1]." -- ".$infos[2];
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::

		echo json_encode($result);
	}	

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'compte_modif_pass')) {

		unset($_POST['action']);
		$fct_exec="|";
		$result=0;

		if ( isset($_POST['global_admin']) && isset($_POST['global_univ']) ) {
			$global_admin = intval(htmlspecialchars($_POST['global_admin']));
			$global_univ = intval(htmlspecialchars($_POST['global_univ']));
		}
		else {
			$global_admin = 0;
			$global_univ = 0;
		}

		$pass =htmlspecialchars($_POST['pass']);
		$id_personne = intval(htmlspecialchars($_POST['id_personne']));
		
		//$result= $infos[3].' Désactivation';
		$fct_exec="| Modification du mots de passe du compte : ".$id_personne;
		$table="personne" ; 
		$tb_infos=[] ;
		$tb_conditions=[] ;
		$tb_conditions["id_pers_personne"]= $id_personne;
		$tb_infos["pass"]= sha1($pass);
		Admin::set_updateSQL_ALL_by($table,$tb_infos, $tb_conditions);
		$result="success";

		//:::::::::::::LOGS::::::::::::::::::
		$info = "AJAX ::: Action : compte_modif_pass => " . $fct_exec;
		$log_user ="Modification de  mots de passe ";
		Log::set_Ajax_Log($info,$log_user,$global_admin,$global_univ);
		//:::::::::::::LOGS::::::::::::::::::

		echo json_encode($result);
	}	

?>