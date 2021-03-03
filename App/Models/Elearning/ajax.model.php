<?php 
	/**
	 * View
	 *
	 * PHP version 7.0
	 */
	header("Access-Control-Allow-Origin: *");
	require_once('../../Config.php');
	require_once('../../../Core/Model.php');
	require_once('../Log.php');
	require_once('../Prof.php');
	require_once('../Model_public.php');


	require('Prof_elearn.php');

	use App\Models\Log; 
	use App\Models\Elearning\Prof_elearn;
	use App\Models\Prof;
	use App\Models\Model_public;

	//var_dump($_POST);exit();
	date_default_timezone_set("Africa/Abidjan");
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');	

	//Mise à jour bd

	if ((isset($_POST['action'])) && ($_POST['action'] == 'set_profmatiere_video')){

		unset($_POST['action']);
		//var_dump("post",$_POST);exit();
		$id_persprof = intval(htmlspecialchars($_POST['id_persprof'] ));
		$id_matiere = intval(htmlspecialchars($_POST['id_matiere'] ));
		$lienvideo = htmlspecialchars($_POST['lienvideo'] );
		$video_libelle = htmlspecialchars($_POST['video_libelle'] );

		$datedebut_dispo = htmlspecialchars($_POST['datedebut_dispo'] );
		$datefin_dispo = htmlspecialchars($_POST['datefin_dispo'] );
		$id_cours_plan = intval(htmlspecialchars($_POST['id_cours_plan'] ));
		//$datedebut_dispo , $datefin_dispo , $partie)
		$result = Prof_elearn::set_profmatiere_video($id_matiere, $id_persprof, $lienvideo, $video_libelle , $datedebut_dispo , $datefin_dispo , $id_cours_plan);
		//var_dump($result['set_profmatiere_video']);exit();
		$info = "Set infos : ";
		$log_user =" Ajout Liens video" ;
		Log::setLog($info,$log_user);
		//print_r( $result);
		echo json_encode($result);
		//$result_return =(object) $result;
		///print_r($result_return)  ;

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'get_prof_sectionpartie')){

		unset($_POST['action']);
		//var_dump("post",$_POST);exit();

		$id_section = intval(htmlspecialchars($_POST['id_section'] ));
		$id_persprof = intval(htmlspecialchars($_POST['id_persprof'] ));
		$id_matiere = htmlspecialchars($_POST['id_matiere'] );

		$result = Prof_elearn::get_cour_partieBy($id_persprof, $id_matiere, $id_section);
		//var_dump($result['set_profmatiere_video']);exit();
		$info = "Get infos : ";
		$log_user =" Get matiere plan partie" ;
		Log::setLog($info,$log_user);
		//print_r( $result);
		echo json_encode($result);
		//$result_return =(object) $result;
		///print_r($result_return)  ; 

	}

	elseif ((isset($_POST['action'])) && ($_POST['action'] == 'update_eleve_vue')){

		unset($_POST['action']);
		//var_dump("post",$_POST);exit();
		/**
		 * table : suivi_active_courplan
		 * sa_courplan_id	
		 * sa_groupe_id	
		 * sa_etatvue_groupe (0-vue grpe Desactiver //1-groupe vue activer)	
		 * sa_progression (0-non effectuer//1-terminer)
		 */

		$sa_courplan_id = intval(htmlspecialchars($_POST['sa_courplan_id'] ));
		$sa_groupe_id = intval(htmlspecialchars($_POST['sa_groupe_id'] ));
		//1-maj sa_etatvue_groupe  et 2- maj sa_progression
		$rqtetype = intval(htmlspecialchars($_POST['rqtetype'] ));
		$rqtetype_valeur = intval(htmlspecialchars($_POST['rqtetype_valeur'] ));

		$result = Prof_elearn:: set_suivi_active_courplan($sa_courplan_id, $sa_groupe_id, $rqtetype, $rqtetype_valeur );
		//var_dump($result['set_profmatiere_video']);exit();
		$info = "Set infos : ";
		$log_user =" update_eleve_vue (suivi_active_courplan) rqtetype= ".$rqtetype."  rqtetype_valeur= ".$rqtetype_valeur;
		Log::setLog($info,$log_user);
		//print_r( $result);
		echo json_encode($result);
		//$result_return =(object) $result;
		///print_r($result_return)  ; 

	}

	elseif ( (isset($_POST['action'])) && ($_POST['action'] == 'absenceeleve') ){

		unset($_POST['action']);
		//var_dump($_POST);
		//echo "ok succes";
		//exit;
		$id_eleve = intval(htmlspecialchars($_POST['id_eleve'] ));
		$id_matiere = intval(htmlspecialchars($_POST['idmatiere'] ));
		$id_groupe = intval(htmlspecialchars($_POST['idgroupe'] ));
		$id_pers_prof = intval(htmlspecialchars($_POST['idprof'] ));

		$table= "personne";
		$tb_conditions=[]; 
		$tb_conditions['id_pers_personne'] = $id_pers_prof;
		$infos_prof = Model_public::get_selectSQL_ALL_by($table, $tb_conditions);

		//var_dump("infos_prof",$infos_prof);
		
		if ($infos_prof==0) {
			$prof_nom = "NULL";
			$prof_idpers = "NULL";
		}
		else {
			$prof_nom = $infos_prof[0]['nom_prenom'];
			$prof_idpers = $infos_prof[0]['id_pers_personne'];
			$idprof = intval($infos_prof[0]['id_type']);
		}

		$abs_debut = htmlspecialchars($_POST['datedebut'] );
		$abs_fin = htmlspecialchars($_POST['datefin'] );
		$abs_date = htmlspecialchars($_POST['datejr'] );
		$etat_presence = htmlspecialchars($_POST['etat_presence'] );
		$abs_motifs = "ABSENCE";
		$result = 1;

		$table= "groupe_emploitps";
		$tb_conditions=[]; 
		$tb_conditions['emploitps_date'] = $abs_date;
		$tb_conditions['emploitps_h_debut'] = $abs_debut;
		$tb_conditions['emploitps_h_fin'] = $abs_fin;
		$tb_conditions['emploitps_groupe_id'] = $id_groupe;
		$tb_conditions['emploitps_id_mat'] = $id_matiere;
		$tb_conditions['emploitps_id_prof'] = $idprof;
		$tabdatas['info_emploitps'] = Model_public::get_selectSQL_ALL_by($table, $tb_conditions);
		//$result = "success";
		if (!empty($tabdatas['info_emploitps']) && $tabdatas['info_emploitps']!=0) {
			if ($etat_presence =='absent' ) {
				//var_dump($tabdatas['info_emploitps'] );
				$table="absence_eleve";
				$tb_infos=[];
				$tb_conditions=[];
				$tb_conditions["fk_emploitps"]= intval(($tabdatas['info_emploitps'][0])['emploitps_id']);
				$tb_conditions["fk_id_eleve"]=$id_eleve;
				$tb_infos= $tb_conditions;
				$tb_infos["abs_motifs"]='Absent du cour';
				$tb_infos["abs_justif"]= 0;
				$tb_infos["fk_createur"]=$prof_idpers;
				$tabdatas['set_absence_eleves'] = Model_public::set_insertSQL($table,$tb_infos, $tb_conditions);
				//var_dump("result",$result);
				$result = 1;
				if ($result == 1) {
					//Envoie de notification
					$notif_titre ="Absence élève";
					$notif_desc ="L'élève : ".$infos_eleve['nom_prenom']." Matricule : ".$infos_eleve['matricule']. " n'a pas assisté au cours de ".$matière." le ".$abs_date." de ".$abs_debut."  à  ".$abs_fin;
		
					$notif_debut=date("Y-m-d");
					$notif_fin= date('Y-m-d', strtotime("+7 day"));
					$notif_methode="email;sms;";
					$createur_notif=intval($prof_idpers);
		
					$idnotif  = Model_public::set_notifications( $notif_titre,$notif_desc ,$notif_debut,$notif_fin ,$notif_methode, $idprof );
					//var_dump("idnotif",$idnotif);

					$usernotif_typeuser = 1;
					$set_usersNotif = Model_public::set_usersNotif($id_eleve, $idnotif, $usernotif_typeuser);
					
					$usernotif_typeuser = 3;
					$eleve_parent  = Model_public::get_enfant_idParent($id_eleve);
					//var_dump("eleve_parent",$eleve_parent);

					//var_dump("eleve_parent",$eleve_parent);
					if ($eleve_parent != 0 ) {
						foreach ($eleve_parent as $key => $value) {
							$idparent =intval($value['id_parent']);
							//var_dump("value",$value['id_parent']);
							//var_dump("idparent",$idparent);
							if ($idnotif != 0) {
								$set_usersNotif = Model_public::set_usersNotif($idparent, $idnotif, $usernotif_typeuser);
								//var_dump("set_usersNotif",$set_usersNotif);
							}


						}
					}

				}

			}
			elseif ($etat_presence =='present') {
				$table="absence_eleve";
				$tb_infos=[];
				$tb_conditions=[];
				$tb_conditions["fk_emploitps"]= intval(($tabdatas['info_emploitps'][0])['emploitps_id']);
				$tb_conditions["fk_id_eleve"]=$id_eleve;
				$tb_conditions["fk_createur"]=$prof_idpers;
				$tb_conditions["abs_motifs"]='Absent du cour';
				$tb_conditions["abs_justif"]=0;
				$tb_infos= $tb_conditions;
				$tabdatas['del_absence_eleves'] = Model_public::set_deleteSQL_ALL_by($table,$tb_conditions);
			}
		}

		else {$result = 0;}
		//var_dump($result);
		//echo $result." <br>";
		if ($result == 1) {$reps = "success";	}
		else {$reps = "erreur";}
		
		/*$reps = "success";*/	
		
		echo json_encode($reps);

	}
	

 ?>