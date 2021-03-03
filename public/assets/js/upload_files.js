$(document).ready(function() {

    $(".spinner_load").hide();
    $(".badge_success").hide();
    $(".badge_erreur").hide();
    var id_cours_plan_tmp = 0;
    /**
     * root_fct_erreur_notif
        root_fct_load_notif
        root_fct_succes_notif
     */

    //import a partir de 'config.js';

    consoleInfo(Appconfigs.envi, 'test console', 'ok', Appconfigs.liens_serv);

    //consoleInfo(Appconfigs.envi, 'test console', 'ok', location.hostname);

    //consoleInfo(Appconfigs.envi, 'test console', 'ok', window.location);


    //consoleInfo(envi, titre, info, valueur)


    document.querySelector('#upload-button').addEventListener('click', function() {
        // user has not chosen any file

        $(".progress-bar").attr("style", "width: 0%");
        $(".progress-bar").attr("aria-valuenow", "0");

        root_fct_load_notif();

        //consoleInfo(Appconfigs.envi, 'Upload file', '$("#upload_selectsection").val()', $("#upload_selectsection").val());
        //consoleInfo(Appconfigs.envi, 'Upload file', '$("#upload_selectpartie").val()', $("#upload_selectpartie").val());



        if (document.querySelector('#file_input').files.length == 0 || $("#upload_selectsection").val() == 0 || $("#upload_selectsection").val() == "" || typeof($("#upload_selectsection").val()) == undefined || $("#upload_selectsection").val() == null) {

            alert('Erreur !  Veuillez choisir une section et un fichier');

            root_fct_erreur_notif();

            return;

        } else {

            // first file that was chosen

            var file = document.querySelector('#file_input').files[0];

            // allowed types

            var mime_types = ['video/mp4', 'application/msword', 'application/vnd.ms-excel', ' application/vnd.ms-powerpoint', 'application/pdf', 'application/xlsx', 'application/docx', 'application/pptx', 'application/xls', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.wordprocessingml.template', 'application/msword', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

            // validate MIME type

            consoleInfo(Appconfigs.envi, 'Upload file  test types=' + file.type, 'file.type' + file.type, mime_types.indexOf(file.type));
            size = 200 * 1024 * 1024;
            consoleInfo(Appconfigs.envi, 'Upload file  size=' + file.size, ' file.size' + file.size, size);

            if (mime_types.indexOf(file.type) == -1 || file.size > 200 * 1024 * 1024) {

                alert('Erreur ! Type de fichier non supporter');
                root_fct_erreur_notif();

                return;

            } else {

                // validation is successful
                //alert('You have chosen the file ' + file.name);

                if (typeof($("#id_personne").val()) != undefined && typeof($("#id_matiere").val()) != undefined && $("#id_personne").val() != "" && $("#id_matiere").val() != "") {

                    id_cours_plan_tmp = 0;

                    if (typeof($("#datedebut_dispo").val()) != undefined && typeof($("#datefin_dispo").val()) != undefined && $("#datedebut_dispo").val() != '' && $("#datefin_dispo").val() != '') {


                        if ($("#upload_selectpartie").val() != 0 && $("#upload_selectpartie").val() != "0" && $("#upload_selectpartie").val() != "" && $("#upload_selectpartie").val() != null && typeof($("#upload_selectpartie").val()) != null) {
                            id_cours_plan_tmp = $("#upload_selectpartie").val();
                        } else {
                            id_cours_plan_tmp = $("#upload_selectsection").val();
                        }
                        consoleInfo(Appconfigs.envi, 'upload_selectpartie = ' + $("#upload_selectpartie").val(), 'upload_selectsection', $("#upload_selectsection").val());

                        ajaxSend_files(file, $("#id_matiere").val(), $("#id_personne").val(), id_cours_plan_tmp);


                    } else {

                        alert('Erreur ! Veuillez Choisir les dates!');
                        root_fct_erreur_notif();

                    }
                } else {
                    root_fct_erreur_notif();
                    alert('Erreur ! Veuillez Choisir une matière!');

                }


            }
            // upload file now
        }

    });



    document.querySelector('#uploadvideo-button').addEventListener('click', function() {



        if ($('#video_libelle').val() != "" && typeof($('#liens_video').val()) != undefined && $('#liens_video').val() != "" && $('#liens_video').val() != 0) {



            if (typeof($("#id_personne").val()) != undefined && typeof($("#id_matiere").val()) != undefined && $("#id_personne").val() != "" && $("#id_matiere").val() != "") {

                if (typeof($("#datedebut_dispo").val()) != undefined && typeof($("#datefin_dispo").val()) != undefined && $("#datedebut_dispo").val() != '' && $("#datefin_dispo").val() != '') {

                    if ($("#upload_selectpartie").val() != 0 && $("#upload_selectpartie").val() != "0" && $("#upload_selectpartie").val() != "" && $("#upload_selectpartie").val() != null && typeof($("#upload_selectpartie").val()) != null) {

                        partie = $("#upload_selectpartie").val();
                    } else {
                        partie = $("#upload_selectsection").val();
                    }
                    //alert(partie);
                    var liens_video = $('#liens_video').val();
                    consoleInfo(Appconfigs.envi, 'liens_video', 'result', liens_video);
                    //https://www.youtube.com/watch?v=mXB3bZG1ivc&ab_channel=DavidFaitDesTops
                    var tab_liens_video = liens_video.split("=");
                    consoleInfo(Appconfigs.envi, 'tab_liens_video', 'result', tab_liens_video);

                    if (typeof(tab_liens_video) != undefined && tab_liens_video.length != 0 && (tab_liens_video[0] == "https://www.youtube.com/watch?v" || tab_liens_video[0] == "http://www.youtube.com/watch?v")) {

                        var tab_liens_video1 = (tab_liens_video[1]).split("&");
                        consoleInfo(Appconfigs.envi, 'tab_liens_video1', 'result', tab_liens_video1);

                        if (typeof(tab_liens_video1) != undefined && tab_liens_video1.length != 0) {
                            liens_video = "https://www.youtube.com/embed/" + tab_liens_video1[0];
                        } else {
                            liens_video = "https://www.youtube.com/embed/" + tab_liens_video[1];
                        }

                    } else {
                        $('#liens_video').val(liens_video);
                    }

                    consoleInfo(Appconfigs.envi, 'liens_video', 'result', liens_video);



                    url_info = Appconfigs.liens_serv + 'App/Models/Elearning/ajax.model.php';
                    values_info = 'action=set_profmatiere_video&video_libelle=' + $('#video_libelle').val() + '&id_persprof=' + $('#id_personne').val() + '&id_matiere=' + $('#id_matiere').val() + '&lienvideo=' + liens_video + '&id_cours_plan=' + partie + '&datedebut_dispo=' + $('#datedebut_dispo').val() + '&datefin_dispo=' + $('#datefin_dispo').val();

                    idelemt = "#uploadvideo-button";

                    root_AjaxMethod(url_info, values_info, fct_active_toast, idelemt);
                } else {
                    root_fct_erreur_notif();
                    alert('veuillez choisir des dates valables')
                }

            } else {

                alert('Erreur ! Veuillez Choisir une matière!');
                root_fct_erreur_notif();
                return;

            }

        } else {

            alert('Veuillez saisir une adresse valide !');
            root_fct_erreur_notif();

            return;

        }





    });


    function fct_active_toast(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {
            var info = '';
            jsondata = JSON.parse(jsondata);
            root_fct_succes_notif();
            consoleInfo(Appconfigs.envi, 'Upload lien video', 'result', jsondata);

            if (jsondata == 'erreur') {
                classvar = 'alert-danger';
            } else {
                classvar = 'alert-success';
            }


            $('#toast_body').removeClass("alert-danger");
            $('#toast_body').removeClass("alert-success");
            $('#toast_body').removeClass("alert-warning");

            $('#toast_header').removeClass("alert-danger");
            $('#toast_header').removeClass("alert-success");
            $('#toast_header').removeClass("alert-warning");
            consoleInfo(Appconfigs.envi, 'Upload lien video', 'idelemt', idelemt);

            switch (idelemt) {
                case "#uploadvideo-button":

                    var info = 'La vidéo : ' + $('#video_libelle').val('') + '  Liens : (' + $('#liens_video').val() + ') à été ajoutée avec ' + jsondata;
                    $("#toast_body").text(info);

                    consoleInfo(Appconfigs.envi, 'Upload lien video', 'info', info);


                    $('#toast_body').addClass(classvar);
                    $('#toast_header').addClass(classvar);

                    if (jsondata != 'erreur') {
                        $('#video_libelle').val('');
                        $('#liens_video').val('');
                    }

                    break;

                default:
                    break;
            }


            //show hide
            $('.toast').toast({ delay: 200000 });
            $('.toast').toast('show');




        }



    }


    function ajaxSend_files(fichier, id_matiere, id_personne, id_cours_plan_tmp) {

        var data = new FormData();

        // file selected by the user

        // in case of multiple files append each of them


        data.append('file', fichier);

        data.append('id_mat', id_matiere);

        data.append('id_pers', id_personne);

        data.append('datedebut_dispo', $("#datedebut_dispo").val());
        data.append('datefin_dispo', $("#datefin_dispo").val());

        data.append('partie', id_cours_plan_tmp);

        data.append('action', 'upload_file');

        var request = new XMLHttpRequest();

        request.open('post', Appconfigs.liens_serv + 'App/Models/Prof_AjaxOnline.php');

        // upload progress event

        request.upload.addEventListener('progress', function(e) {

            var percent_complete = (e.loaded / e.total) * 100;



            // Percentage of upload completed

            consoleInfo(Appconfigs.envi, 'Upload File', 'Envoie fichier de cours', percent_complete);



            $(".progress-bar").attr("style", "width: " + percent_complete + "%");

            $(".progress-bar").attr("aria-valuenow", percent_complete);

        });



        // AJAX request finished event

        request.addEventListener('load', function(e) {

            // HTTP status message

            consoleInfo(Appconfigs.envi, 'Upload File', 'request.status', request.status);

            // request.response will hold the response from the server

            consoleInfo(Appconfigs.envi, 'Upload File', 'request.response', request.response);



            jsondata = JSON.parse(request.response);

            consoleInfo(Appconfigs.envi, 'Upload File', 'jsondata', jsondata);

            if (request.status == 200 || request.status == 0) {

                infos_result = 'Documents ' + fichier.name + ' envoyé avec ' + jsondata;

                if (jsondata == "success") {

                    document.getElementById("file_input").value = "";
                    //classtype alert-danger , alert-success , alert-warning
                    classtype = 'alert-success';
                    root_fct_succes_notif();
                    root_fct_showinfos_toast(infos_result, classtype);

                    //$(".progress-bar").attr("style", "width: 0%");
                    //$(".progress-bar").attr("aria-valuenow", "0");

                } else {

                    alert('Erreur veuillez réessayer');
                    root_fct_erreur_notif();
                    classtype = 'alert-warning';
                    root_fct_showinfos_toast(infos, classtype);

                }

            } else {
                infos = 'Documents ' + fichier + ' non envoyé ! Erreur ';
                classtype = 'alert-danger';
                root_fct_showinfos_toast(infos_result, classtype);
                root_fct_erreur_notif();
                alert('Erreur Réseaux! veuillez réessayer');

            }

        });

        // send POST request to server side script
        request.send(data);

    }

    $('#file_input').change(function(e) {

        $(".progress-bar").attr("style", "width: 0%");

        $(".progress-bar").attr("aria-valuenow", "0");
    });

    $('#upload_selectsection').change(function(e) {

        $(".progress-bar").attr("style", "width: 0%");
        $(".progress-bar").attr("aria-valuenow", "0");

        if ($('#upload_selectsection').val() != 0 && $('#upload_selectsection').val() != "") {

            url_info = Appconfigs.liens_serv + 'App/Models/Elearning/ajax.model.php';

            values_info = 'action=get_prof_sectionpartie&id_section=' + $('#upload_selectsection').val() + '&id_persprof=' + $('#id_personne').val() + '&id_matiere=' + $('#id_matiere').val();

            idelemt = "#upload_selectpartie";

            root_AjaxMethod(url_info, values_info, fct_setupload_partie, idelemt);

        }


    });

    function fct_setupload_partie(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {
            jsondata = JSON.parse(jsondata);
            consoleInfo(Appconfigs.envi, 'Upload get partie', 'result', jsondata);

            if (typeof(jsondata) != undefined && jsondata != [] && jsondata != '') {

                for (var x in jsondata) {

                    $(idelemt).append('<option value="' + jsondata[x].id_cours_plan + '">' + jsondata[x].plan_position_num + '-' + jsondata[x].plan_titre + '</option>');
                }

            } else {
                $(idelemt).html('');
            }


        }



    }

    /*
        $('#file_input').change(function(e){
                var fileName = e.target.files[0].name;
                alert('The file "' + fileName +  '" has been selected.');
            });
    */




});