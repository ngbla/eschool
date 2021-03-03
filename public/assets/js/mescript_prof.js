$(document).ready(function() {

    /*
     ** VERSION 01/05/2020 08H59
     */
    envi = Appconfigs.envi;
    liens = Appconfigs.liens_serv;
    var jsondata = {};
    //Creation d'evenement utilisateur     

    //alert('ok');


    $('#dataTable_list_fichier').DataTable({
        dom: 'Blfrtip',
        buttons: [{
                extend: 'copyHtml5',
                text: '<span style="font-size: 20px; color: Mediumslateblue;"><i class="fas fa fa-copy "></i></span>',
                titleAttr: 'Copy'
            },
            {
                extend: 'excelHtml5',
                text: '<span style="font-size: 20px; color: green;"><i class="fas fa fa-file-excel"></i></span>',
                titleAttr: 'Excel'
            },
            {
                extend: 'csvHtml5',
                text: '<span style="font-size: 20px; color: Dodgerblue;"><i class="fas fa fa-file-csv"></i></span>',
                titleAttr: 'CSV'
            },
            {
                extend: 'pdfHtml5',
                text: '<span style="font-size: 20px; color: Tomato;"><i class="fas fa fa-file-pdf"></i></span>',
                titleAttr: 'PDF'
            }

        ]
    });
    $('#dataTable_prof_classe').DataTable({
        dom: 'Blfrtip',
        buttons: [{
                extend: 'copyHtml5',
                text: '<span style="font-size: 20px; color: Mediumslateblue;"><i class="fas fa fa-copy "></i></span>',
                titleAttr: 'Copy'
            },
            {
                extend: 'excelHtml5',
                text: '<span style="font-size: 20px; color: green;"><i class="fas fa fa-file-excel"></i></span>',
                titleAttr: 'Excel'
            },
            {
                extend: 'csvHtml5',
                text: '<span style="font-size: 20px; color: Dodgerblue;"><i class="fas fa fa-file-csv"></i></span>',
                titleAttr: 'CSV'
            },
            {
                extend: 'pdfHtml5',
                text: '<span style="font-size: 20px; color: Tomato;"><i class="fas fa fa-file-pdf"></i></span>',
                titleAttr: 'PDF'
            }

        ]
    });
    $('#dataTable_prof_groupe').DataTable({
        dom: 'Blfrtip',
        buttons: [{
                extend: 'copyHtml5',
                text: '<span style="font-size: 20px; color: Mediumslateblue;"><i class="fas fa fa-copy "></i></span>',
                titleAttr: 'Copy'
            },
            {
                extend: 'excelHtml5',
                text: '<span style="font-size: 20px; color: green;"><i class="fas fa fa-file-excel"></i></span>',
                titleAttr: 'Excel'
            },
            {
                extend: 'csvHtml5',
                text: '<span style="font-size: 20px; color: Dodgerblue;"><i class="fas fa fa-file-csv"></i></span>',
                titleAttr: 'CSV'
            },
            {
                extend: 'pdfHtml5',
                text: '<span style="font-size: 20px; color: Tomato;"><i class="fas fa fa-file-pdf"></i></span>',
                titleAttr: 'PDF'
            }

        ]
    });

    $('#dataTable_prof_listMoyElev').DataTable({
        dom: 'Blfrtip',
        buttons: [{
                extend: 'copyHtml5',
                text: '<span style="font-size: 20px; color: Mediumslateblue;"><i class="fas fa fa-copy "></i></span>',
                titleAttr: 'Copy'
            },
            {
                extend: 'excelHtml5',
                text: '<span style="font-size: 20px; color: green;"><i class="fas fa fa-file-excel"></i></span>',
                titleAttr: 'Excel'
            },
            {
                extend: 'csvHtml5',
                text: '<span style="font-size: 20px; color: Dodgerblue;"><i class="fas fa fa-file-csv"></i></span>',
                titleAttr: 'CSV'
            },
            {
                extend: 'pdfHtml5',
                text: '<span style="font-size: 20px; color: Tomato;"><i class="fas fa fa-file-pdf"></i></span>',
                titleAttr: 'PDF'
            }

        ]
    });
    $('#dataTable_list_evalnonprog').DataTable({
        dom: 'Blfrtip',
        buttons: [{
                extend: 'copyHtml5',
                text: '<span style="font-size: 20px; color: Mediumslateblue;"><i class="fas fa fa-copy "></i></span>',
                titleAttr: 'Copy'
            },
            {
                extend: 'excelHtml5',
                text: '<span style="font-size: 20px; color: green;"><i class="fas fa fa-file-excel"></i></span>',
                titleAttr: 'Excel'
            },
            {
                extend: 'csvHtml5',
                text: '<span style="font-size: 20px; color: Dodgerblue;"><i class="fas fa fa-file-csv"></i></span>',
                titleAttr: 'CSV'
            },
            {
                extend: 'pdfHtml5',
                text: '<span style="font-size: 20px; color: Tomato;"><i class="fas fa fa-file-pdf"></i></span>',
                titleAttr: 'PDF'
            }

        ]
    });


    function fctGetProfMat(jsondata) {

        $('#prof_eval_select_mat').html(' <option value=" " disabled selected hidden>Choisissez une matière</option>');

        if (typeof(jsondata) != undefined && jsondata != "") {

            jsondata = JSON.parse(jsondata);

            for (var x in jsondata) {
                $('#prof_eval_select_mat').append(' <option value="' + jsondata[x].id_matiere_matiere + '">' + jsondata[x].code + '-' + jsondata[x].libele + '</option>');
            }
        }

        titre = "evenement sur: #prof_eval_select_mat // ";
        info = "Ajax send  resultat :: fctGetProfMat() = ";
        valueur = jsondata;
        consoleInfo(envi, titre, info, valueur);

    }

    function fct_Getanneepart(jsondata) {

        $('#prof_eval_periode').html(' <option value=" " disabled selected hidden>Choisissez une Période</option>');

        if (typeof(jsondata) != undefined && jsondata != "") {

            jsondata = JSON.parse(jsondata);

            for (var x in jsondata) {
                $('#prof_eval_periode').append(' <option value="' + jsondata[x].id_annee_partie + '">' + jsondata[x].libele_partie + '</option>');
            }
        }

        titre = "evenement sur: #prof_eval_select_mat // ";
        info = "Ajax send  resultat :: fctGetProfMat() = ";
        valueur = jsondata;
        consoleInfo(envi, titre, info, valueur);

    }

    function fct_Set_eleve_eval_notes_result(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {

            jsondata = JSON.parse(jsondata);

            //for (var x in jsondata) {  }

            titre = "evenement sur: Fct (fct_Set_eleve_eval_notes_result) // ";
            info = "Ajax send  resultat ::  ";
            valueur = jsondata;
            consoleInfo(envi, titre, info, valueur);


            titre = "evenement sur: Fct (fct_Set_eleve_eval_notes_result) // ";
            info = "idelemt ::  ";
            valueur = idelemt;
            consoleInfo(envi, titre, info, valueur);

            jsondata_split = (jsondata).split("_");


            switch (jsondata_split[0]) {
                case "majnote":
                    if (jsondata_split[1] == 'ok') { $(idelemt).css("border", "1px solid green"); } else { $(idelemt).css("border", "1px solid red"); }
                    break;

                default:
                    break;
            }

        }



    }

    $(document.body).on("change", "#prof_eval_select_groupe", function(e) {
        //doStuff
        //alert('change prof_eval_select_groupe');

        var id_prof = $('#type_id').val();
        var id_groupe = $('#prof_eval_select_groupe').val();
        titre = "//evenement Change sur: #prof_eval_select_groupe";
        info = "  // Valeur=prof --> " + id_prof + " groupe =";
        valueur = id_groupe;
        consoleInfo(envi, titre, info, valueur);
        var id_groupe = $('#prof_eval_select_groupe').val();
        //alert(id_prof,id_groupe);

        if (id_groupe == 0 || id_groupe == "") {} else {

            url_info = liens + 'App/Models/Prof_AjaxOnline.php';
            values_info = 'action=getProfmatiereByGrp&id_prof=' + id_prof + '&id_groupe=' + id_groupe;

            idelemt = "#prof_eval_select_groupe";

            root_AjaxMethod(url_info, values_info, fctGetProfMat, idelemt);


            url_info = liens + 'App/Models/Prof_AjaxOnline.php';
            values_info = 'action=get_anneepartie_ByGrpe&id_groupe=' + id_groupe;

            idelemt = "#prof_eval_select_groupe";

            root_AjaxMethod(url_info, values_info, fct_Getanneepart, idelemt);
        }


    });


    $('#dataTable_list_annee .input_note_eleve').on('change', function(event) {

        if (typeof(event.target.id) != undefined) {

            titre = "::PROF EVENT:::  Change sur:(.input_note_eleve)";
            info = "  //ID Valeur= " + event.target.id;
            valueur = $('#dataTable_list_annee #' + event.target.id + '').val();
            consoleInfo(envi, titre, info, valueur);

            result_split = (event.target.id).split("_");
            note = $('#dataTable_list_annee #' + event.target.id + '').val();
            //id_eleve_eleve  & _ & eval_id


            url_info = liens + 'App/Models/Prof_AjaxOnline.php';
            values_info = 'action=Set_eleve_eval_notes&id_eleve_eleve=' + result_split[0] + '&eval_id=' + result_split[1] + '&note_val=' + note + '&global_univ=' + $('#global_univ').val() + '&global_admin=' + $('#global_admin').val();

            titre = "::PROF EVENT:::  Ajax  liens : " + url_info;
            info = "  //liens =" + url_info + "  Action =";
            valueur = values_info;
            consoleInfo(envi, titre, info, valueur);
            idelemt = '#dataTable_list_annee #' + event.target.id + '';

            root_AjaxMethod(url_info, values_info, fct_Set_eleve_eval_notes_result, idelemt);

        } else {
            titre = "::PROF EVENT:::  Change sur:(.input_note_eleve)";
            info = "  // Valeur  event.target.id undefined ";
            valueur = event.target.id;
            consoleInfo(envi, titre, info, valueur);

        }


    });

    $('#prof_emploitps_cal').on('click', function(event) {
        $('#timeline_prof .fc-today-button').click();
    });


    $(document.body).on("change", "#prof_eval_hDebut", function(e) {

        //alert();
        if (typeof($('#prof_eval_hDebut').val()) != undefined && typeof($('#prof_eval_hFin').val()) != undefined) {

            if ($('#prof_eval_hDebut').val() != "" && $('#prof_eval_hFin').val() != "") {
                timedebut = $('#prof_eval_hDebut').val();
                timefin = $('#prof_eval_hFin').val();
                timeoperation(timedebut, timefin);

            }

        }


    });
    $(document.body).on("change", "#prof_eval_hFin", function(e) {

        if (typeof($('#prof_eval_hDebut').val()) != undefined && typeof($('#prof_eval_hFin').val()) != undefined) {

            if ($('#prof_eval_hDebut').val() != "" && $('#prof_eval_hFin').val() != "") {
                timedebut = $('#prof_eval_hDebut').val();
                timefin = $('#prof_eval_hFin').val();
                timeoperation(timedebut, timefin);

            }

        }


    });

    function timeoperation(timedebut, timefin) {

        timedebut_tab = timedebut.split(":");
        timefin_tab = timefin.split(":");

        //year, month, day, hour, minute
        var ddebut = new Date(2020, 07, 15, timedebut_tab[0], timedebut_tab[1]);
        var dfin = new Date(2020, 07, 15, timefin_tab[0], timefin_tab[1]);

        titre = "evenement sur: #prof_eval_hDebut // #prof_eval_hFin";
        info = "Heure debut = " + ddebut + " && Heure Fin = " + dfin;

        diff = dateDiff(ddebut, dfin);

        valueur = diff.hour + "H : " + diff.min + "Min";

        consoleInfo(envi, titre, info, valueur);
        $('#prof_eval_durée').val(valueur);

    }

    function dateDiff(date1, date2) {
        var diff = {} // Initialisation du retour
        var tmp = date2 - date1;

        tmp = Math.floor(tmp / 1000); // Nombre de secondes entre les 2 dates
        diff.sec = tmp % 60; // Extraction du nombre de secondes

        tmp = Math.floor((tmp - diff.sec) / 60); // Nombre de minutes (partie entière)
        diff.min = tmp % 60; // Extraction du nombre de minutes

        tmp = Math.floor((tmp - diff.min) / 60); // Nombre d'heures (entières)
        diff.hour = tmp % 24; // Extraction du nombre d'heures

        tmp = Math.floor((tmp - diff.hour) / 24); // Nombre de jours restants
        diff.day = tmp;

        return diff;
    }
    //SAISIE ABSENCES
    $('.btn_abs_eleve').on('click', function(event) {

        consoleInfo(envi, "click btn_abs_eleve", "id = ", event.target.id);
        valueur = $('#' + event.target.id + '').attr("value");
        consoleInfo(envi, "click btn_abs_eleve", "Value = ", valueur);
        values_motif = valueur.split("_");

        $('#modal_absenceLabel').html(values_motif[1] + ' <br> ' + values_motif[2]);
        $('#ideleve_absence').val(values_motif[0]);
        $('#suivi_mat').val(values_motif[3]);
        $('#suivi_classe').val(values_motif[4]);

    });

    $('.optradio_presence').on('click', function(event) {

        datejr = $('#datejr').val();
        datedebut = $('#datedebut').val();
        datefin = $('#datefin').val();
        idprof = $('#idtype_prof').val();


        consoleInfo(envi, "click optradio_presence", "id = ", event.target.id);
        valueur = $('#' + event.target.id + '').attr("value");
        etat_presence = $('#' + event.target.id + '').attr("option");
        consoleInfo(envi, "click optradio_presence Le " + datejr + " de " + datedebut + " à " + datefin, "Value = ", valueur + " Option = " + etat_presence);
        values_motif = valueur.split("_");

        //4_2_51
        //ideleve _ idgroupe_ idmatiere
        url_info = liens + 'App/Models/Elearning/ajax.model.php';
        values_info = 'action=absenceeleve&id_eleve=' + values_motif[0] + '&idgroupe=' + values_motif[1] + '&idmatiere=' + values_motif[2] + '&datedebut=' + datedebut + '&datefin=' + datefin + '&datejr=' + datejr + '&etat_presence=' + etat_presence + '&idprof=' + idprof;
        idelemt = '#' + event.target.id;
        root_AjaxMethod(url_info, values_info, ajax_result_toast, idelemt);

        //$('#modal_absenceLabel').html(values_motif[1]+' <br> '+values_motif[2]);
        //$('#ideleve_absence').val(values_motif[0]);
        //$('#suivi_mat').val(values_motif[3]);
        //$('#suivi_classe').val(values_motif[4]);

    });


    //ACTIVE VUE PLAN BY ELEVE

    var active_courplan_classe_all, var_all_i;
    active_courplan_classe_all = document.querySelectorAll(".btn_active_courplan_classe");
    for (var_all_i = 0; var_all_i < active_courplan_classe_all.length; var_all_i++) {
        titre = "evenement sur: .btn_active_courplan_classe // ";
        info = "Liste id = ";
        valueur = active_courplan_classe_all[var_all_i].id;
        consoleInfo(envi, titre, info, valueur);

        $('#' + active_courplan_classe_all[var_all_i].id).on('click', function(event) {
            event.preventDefault();

            titre = "evenement sur:  #" + this.id;
            info = "Couleur =";
            valueur = $('#' + this.id).css("color");
            consoleInfo(envi, titre, info, valueur);
            //rouge -->vert
            if ($('#' + this.id).css("color") == "rgb(255, 0, 0)") {
                $('#' + this.id).css("color", "rgb(0, 128, 0)");
                rqtetype_valeur = 1;
            }
            //vert -->rouge
            else {
                $('#' + this.id).css("color", "rgb(255, 0, 0)");
                rqtetype_valeur = 0;
            }


            ///($_POST['sa_courplan_id'] ));
            ///($_POST['sa_groupe_id'] ));
            //1-maj sa_etatvue_groupe  et 2- maj sa_progression
            ///($_POST['rqtetype'] ));
            ///($_POST['rqtetype_valeur'] ));
            rqtetype = 1;
            sa_courplan_id = ((this.id).split("_"))[1];
            sa_groupe_id = $('#idgroupe_plan').val();

            if (sa_groupe_id != "" && sa_groupe_id != 0) {
                url_info = liens + 'App/Models/Elearning/ajax.model.php';
                values_info = 'action=update_eleve_vue&sa_courplan_id=' + sa_courplan_id + '&sa_groupe_id=' + sa_groupe_id + '&rqtetype=' + rqtetype + '&rqtetype_valeur=' + rqtetype_valeur;
                idelemt = '#' + this.id;
                root_AjaxMethod(url_info, values_info, ajax_result_toast, idelemt);
            } else {
                titre = "evenement sur: .btn_active_courplan_classe // ";
                info = "Erreur Ajax requette /  sa_groupe_id = ";
                valueur = sa_groupe_id;
                consoleInfo(envi, titre, info, valueur);
            }



        });

    }

    var input_coursuivie_etat_all, var_all_j;
    input_coursuivie_etat_all = document.querySelectorAll(".input_coursuivie_etat");
    for (var_all_j = 0; var_all_j < input_coursuivie_etat_all.length; var_all_j++) {
        titre = "evenement sur: .input_coursuivie_etat_all ";
        info = "Liste id = ";
        valueur = input_coursuivie_etat_all[var_all_j].id;
        consoleInfo(envi, titre, info, valueur);

        $('#' + input_coursuivie_etat_all[var_all_j].id).on('click', function(event) {
            //event.preventDefault();

            titre = "evenement sur:  #" + this.id;
            valueur = $('#' + this.id).prop("checked");;

            if (valueur == true) {
                info = "(Input activer )Etat check =";
                rqtetype_valeur = 1;
            } else if (valueur == false) {
                info = "(Input Désactiver )Etat check =";
                rqtetype_valeur = 0;
            }
            consoleInfo(envi, titre, info, valueur);


            ///($_POST['sa_courplan_id'] ));
            ///($_POST['sa_groupe_id'] ));
            //1-maj sa_etatvue_groupe  et 2- maj sa_progression
            ///($_POST['rqtetype'] ));
            ///($_POST['rqtetype_valeur'] ));
            rqtetype = 2;
            sa_courplan_id = ((this.id).split("_"))[1];
            sa_groupe_id = $('#idgroupe_plan').val();

            if (sa_groupe_id != "" && sa_groupe_id != 0) {
                url_info = liens + 'App/Models/Elearning/ajax.model.php';
                values_info = 'action=update_eleve_vue&sa_courplan_id=' + sa_courplan_id + '&sa_groupe_id=' + sa_groupe_id + '&rqtetype=' + rqtetype + '&rqtetype_valeur=' + rqtetype_valeur;
                idelemt = '#' + this.id;
                root_AjaxMethod(url_info, values_info, ajax_result_toast, idelemt);
            } else {
                titre = "evenement sur: .btn_active_courplan_classe // ";
                info = "Erreur Ajax requette /  sa_groupe_id = ";
                valueur = sa_groupe_id;
                consoleInfo(envi, titre, info, valueur);
            }



        });

    }

    function ajax_result_toast(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {

            jsondata = JSON.parse(jsondata);
            if (jsondata == "success") {
                classtype = "alert-success";
            } else {
                classtype = "alert-danger";
            }
            infos_stat = "MISE A JOUR";
            root_fct_showinfos_toast(infos_stat, classtype);
        }

    }




});