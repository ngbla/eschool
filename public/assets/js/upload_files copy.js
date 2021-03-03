$(document).ready(function(){



    //import a partir de 'config.js';

    consoleInfo(Appconfigs.envi, 'test console', 'ok',Appconfigs.liens_serv);

    //consoleInfo(Appconfigs.envi, 'test console', 'ok', location.hostname);

    //consoleInfo(Appconfigs.envi, 'test console', 'ok', window.location);


    //consoleInfo(envi, titre, info, valueur)


    document.querySelector('#upload-button').addEventListener('click', function() {

        // user has not chosen any file

        $(".progress-bar").attr("style", "width: 0%");

        $(".progress-bar").attr("aria-valuenow", "0");



        if(document.querySelector('#file_input').files.length == 0) {

            alert('Erreur !  Veuillez choisir un fichier!');

            return;

        }



        // first file that was chosen

        var file = document.querySelector('#file_input').files[0];


        // allowed types

        var mime_types = [ 'video/mp4', 'application/msword' , 'application/vnd.ms-excel' , ' application/vnd.ms-powerpoint' , 'application/pdf' , 'application/xlsx' , 'application/docx' , 'application/pptx', 'application/xls'];

        // validate MIME type

        consoleInfo(Appconfigs.envi,'Upload file', 'test types',mime_types.indexOf(file.type));



        if(mime_types.indexOf(file.type) == -1) {

            alert('Erreur ! type de fichier non supporter');

            return;

        }



        // max 400 MB size allowed

        if(file.size > 400*1024*1024) {

            alert('Erreur ! taile du fichier supérieur à 4MB');

            return;

        }


        // validation is successful

        //alert('You have chosen the file ' + file.name);



        if ( typeof($("#id_personne").val()) !=undefined && typeof($("#id_matiere").val()) !=undefined &&  $("#id_personne").val() !="" && $("#id_matiere").val() != "") {



            ajaxSend_files(file, $("#id_matiere").val(), $("#id_personne").val()) ;

            

        }

        else{

            alert('Erreur ! Veuillez Choisir une matière!')

        }


        // upload file now

    });



    document.querySelector('#uploadvideo-button').addEventListener('click', function() {



        if ( $('#video_libelle').val()!= "" && typeof($('#liens_video').val()) != undefined && $('#liens_video').val() != "" && $('#liens_video').val() != 0 ) {

                

            if ( typeof($("#id_personne").val()) !=undefined && typeof($("#id_matiere").val()) !=undefined &&  $("#id_personne").val() !="" && $("#id_matiere").val() != "") {



                url_info = Appconfigs.liens_serv+'App/Models/Elearning/ajax.model.php';

                values_info = 'action=set_profmatiere_video&video_libelle='+$('#video_libelle').val()+'&id_persprof='+ $('#id_personne').val()+'&id_matiere='+$('#id_matiere').val()+'&lienvideo='+$('#liens_video').val();

                idelemt ="#uploadvideo-button"; 

                root_AjaxMethod(url_info, values_info, fct_active_toast ,idelemt);

                

            }

            else{

                alert('Erreur ! Veuillez Choisir une matière!');

                return;

            }



            

        }

        else{

            alert('Veuillez saisir une adresse valide !');

            return;

        }





    });


    function fct_active_toast(jsondata) {

        if (typeof(jsondata) !=undefined && jsondata != "") {
            var info  = '';
            jsondata = JSON.parse(jsondata);

            consoleInfo(Appconfigs.envi, 'Upload lien video', 'result',jsondata);

            if (jsondata == 'erreur') {
                classvar = 'alert-danger';
            }
            else{
                classvar = 'alert-success';
            }


            $('#toast_body').removeClass("alert-danger");
            $('#toast_body').removeClass("alert-success");
            $('#toast_body').removeClass("alert-warning");

            $('#toast_header').removeClass("alert-danger");
            $('#toast_header').removeClass("alert-success");
            $('#toast_header').removeClass("alert-warning");
            consoleInfo(Appconfigs.envi, 'Upload lien video', 'idelemt',idelemt);

            switch (idelemt) {
                case "#uploadvideo-button":
   
                    var info = 'La vidéo : '+$('#video_libelle').val('') + '  Liens : ('+$('#liens_video').val()+') à été ajoutée avec '+jsondata;
                    $("#toast_body").text(info);

                    consoleInfo(Appconfigs.envi, 'Upload lien video', 'info',info);

                    
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
            $('.toast').toast({delay: 200000});
            $('.toast').toast('show');




        }

        

    }


    function ajaxSend_files(fichier, id_matiere,id_personne) {

        var data = new FormData();

        // file selected by the user

        // in case of multiple files append each of them


        data.append('file', fichier);

        data.append('id_mat', id_matiere);

        data.append('id_pers', id_personne);

        data.append('action', 'upload_file');

        



        var request = new XMLHttpRequest();

        request.open('post', Appconfigs.liens_serv+'App/Models/Prof_AjaxOnline.php'); 



        // upload progress event

        request.upload.addEventListener('progress', function(e) {

            var percent_complete = (e.loaded / e.total)*100;

            

            // Percentage of upload completed

            consoleInfo(Appconfigs.envi, 'Upload File', 'Envoie fichier de cours',percent_complete);



            $(".progress-bar").attr("style", "width: "+percent_complete+"%");

            $(".progress-bar").attr("aria-valuenow", percent_complete);

        });



        // AJAX request finished event

        request.addEventListener('load', function(e) {

            // HTTP status message

            consoleInfo(Appconfigs.envi, 'Upload File', 'request.status',request.status);

            // request.response will hold the response from the server

            consoleInfo(Appconfigs.envi, 'Upload File', 'request.response',request.response);



            jsondata = JSON.parse(request.response);

            consoleInfo(Appconfigs.envi, 'Upload File', 'jsondata',jsondata);





            if (request.status == 200 || request.status == 0) {

                infos_result = 'Documents '+fichier.name+ ' envoyé avec '+ jsondata;

                if (jsondata =="success" ) {

                    document.getElementById("file_input").value = "";
                    //classtype alert-danger , alert-success , alert-warning
                    classtype = 'alert-success';
                    root_fct_showinfos_toast(infos_result,classtype);

                    //$(".progress-bar").attr("style", "width: 0%");
                    //$(".progress-bar").attr("aria-valuenow", "0");

                }
                else{

                    alert('Erreur veuillez réessayer');
                    classtype = 'alert-warning';
                    root_fct_showinfos_toast(infos,classtype);

                }

                

            }
            else{
                infos = 'Documents '+fichier+ ' non envoyé ! Erreur ';
                classtype = 'alert-danger';
                root_fct_showinfos_toast(infos_result,classtype);
                alert('Erreur Réseaux! veuillez réessayer');

            }









        });



        // send POST request to server side script

        request.send(data);

        

    }

    $('#file_input').change(function(e){

            $(".progress-bar").attr("style", "width: 0%");

            $(".progress-bar").attr("aria-valuenow", "0");
    });
/*
    $('#file_input').change(function(e){
            var fileName = e.target.files[0].name;
            alert('The file "' + fileName +  '" has been selected.');
        });
*/



});