$(document).ready(function() {

    envi = Appconfigs.envi;
    liens = Appconfigs.liens_serv;
    consoleInfo(Appconfigs.envi, 'liens', '$liens', liens);

    document.querySelector('#upload-button_eval').addEventListener('click', function() {
        // user has not chosen any file
        event.preventDefault();

        $(".progress-bar").attr("style", "width: 0%");
        $(".progress-bar").attr("aria-valuenow", "0");

        root_fct_load_notif();

        consoleInfo(Appconfigs.envi, 'Upload file', '$("#eval_id").val()', $("#eval_id").val());

        if (document.querySelector('#file_input').files.length == 0 || $("#eval_id").val() == 0 || $("#eval_id").val() == "" || typeof($("#eval_id").val()) == undefined || $("#eval_id").val() == null) {

            alert('Erreur !  Evaluation inconnu!');
            root_fct_erreur_notif();
            return;

        } else {

            // first file that was chosen

            var file = document.querySelector('#file_input').files[0];

            // allowed types

            var mime_types = ['video/mp4', 'application/msword', 'application/vnd.ms-excel', ' application/vnd.ms-powerpoint', 'application/pdf', 'application/xlsx', 'application/docx', 'application/doc', 'application/pptx', 'application/xls', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

            // validate MIME type

            consoleInfo(Appconfigs.envi, 'Upload file', 'test types', mime_types.indexOf(file.type));
            consoleInfo(Appconfigs.envi, 'Upload file', ' types', (file.type));

            if (mime_types.indexOf(file.type) == -1 || file.size > 200 * 1024 * 1024) {

                alert('Erreur ! type de fichier non supporter');
                root_fct_erreur_notif();

                return;

            } else {

                // validation is successful
                //alert('You have chosen the file ' + file.name);

                if (typeof($("#id_personne").val()) != undefined && typeof($("#id_personne").val()) != 0 && $("#id_personne").val() != "" && $("#id_personne").val() != "") {

                    ajaxSend_eval_files(file, $("#eval_id").val(), $("#id_personne").val());

                } else {
                    root_fct_erreur_notif();
                    alert('Erreur ! Etudiant inconnu!');

                }


            }
            // upload file now
        }

    });

    function ajaxSend_eval_files(fichier, eval_id, id_personne) {

        var data = new FormData();
        // file selected by the user
        // in case of multiple files append each of them

        data.append('file', fichier);
        data.append('eval_id', eval_id);
        data.append('id_pers', id_personne);
        data.append('action', 'upload_eval_file');
        var request = new XMLHttpRequest();
        request.open('post', liens + 'App/Models/Prof_AjaxOnline.php');
        consoleInfo(Appconfigs.envi, 'liens', '$liens', liens + 'App/Models/Prof_AjaxOnline.php');

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
                    location.reload();

                    //$(".progress-bar").attr("style", "width: 0%");
                    //$(".progress-bar").attr("aria-valuenow", "0");

                } else {

                    infos = 'Documents ' + fichier.name + ' non envoyé ! Erreur ';
                    root_fct_erreur_notif();
                    classtype = 'alert-warning';
                    root_fct_showinfos_toast(infos, classtype);

                }

            } else {
                infos = 'Documents ' + fichier.name + ' non envoyé ! Erreur ';
                classtype = 'alert-danger';
                root_fct_showinfos_toast(infos, classtype);
                root_fct_erreur_notif();
                alert('Erreur Réseaux! veuillez réessayer');

            }

        });

        // send POST request to server side script
        request.send(data);

    }

});