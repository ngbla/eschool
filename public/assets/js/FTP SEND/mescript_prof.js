$(document).ready(function(){

    /*
    ** VERSION 01/05/2020 08H59
    */
   envi = "debug";
   //var liens = 'http://uges.x-pertizgroup.com/';

   var liens = 'http://localhost:8083/timeflow/elearning-pronote/';
   alert(window.location.hostname);
   
   $('#dataTable_list_fichier').DataTable({
    dom: 'Bfrtip',
    buttons: [
        {
            extend:    'copyHtml5',
            text:      '<span style="font-size: 20px; color: Mediumslateblue;"><i class="fas fa fa-copy "></i></span>',
            titleAttr: 'Copy'
        },
        {
            extend:    'excelHtml5',
            text:      '<span style="font-size: 20px; color: green;"><i class="fas fa fa-file-excel"></i></span>',
            titleAttr: 'Excel'
        },
        {
            extend:    'csvHtml5',
            text:      '<span style="font-size: 20px; color: Dodgerblue;"><i class="fas fa fa-file-csv"></i></span>',
            titleAttr: 'CSV'
        },
        {
            extend:    'pdfHtml5',
            text:      '<span style="font-size: 20px; color: Tomato;"><i class="fas fa fa-file-pdf"></i></span>',
            titleAttr: 'PDF'
        }

    ]
});




    var jsondata ={};
    //Creation d'evenement utilisateur

    function AjaxMethod(url, parameters, successCallback,idelemt) {
        //show loading... image
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'text',
            data: parameters,
            success: successCallback,
            error: function(xhr, textStatus, errorThrown) {
                console.log('error');
                alert('error! Veuillez contactez l Administrateur');
            }
        });
    }

    function consoleInfo(envi, titre, info, valueur) {
        if(envi=="debug"){
            console.log(titre+' : ',info+'  ',"( "+valueur +" )");
            console.log("****************************************");
        }
    }
    
    function fctGetProfMat(jsondata) {

        $('#prof_eval_select_mat').html(' <option value="0">Choisissez une matière</option>');

        if (typeof(jsondata) !=undefined && jsondata != "") {

            jsondata = JSON.parse(jsondata);

            for (var x in jsondata) {
                $('#prof_eval_select_mat').append(' <option value="'+jsondata[x].id_matiere_matiere+'">'+jsondata[x].code+'-'+jsondata[x].libele+'</option>');  
            }
        }

        titre="evenement sur: #prof_eval_select_mat // ";
        info="Ajax send  resultat :: fctGetProfMat() = ";
        valueur=jsondata;
        consoleInfo(envi, titre, info, valueur);

    }

    function fct_Set_eleve_eval_notes_result(jsondata) {

        if (typeof(jsondata) !=undefined && jsondata != "") {

            jsondata = JSON.parse(jsondata);

            //for (var x in jsondata) {  }

            titre="evenement sur: Fct (fct_Set_eleve_eval_notes_result) // ";
            info="Ajax send  resultat ::  ";
            valueur=jsondata;
            consoleInfo(envi, titre, info, valueur);

            
            titre="evenement sur: Fct (fct_Set_eleve_eval_notes_result) // ";
            info="idelemt ::  ";
            valueur=idelemt;
            consoleInfo(envi, titre, info, valueur);

            jsondata_split = (jsondata).split("_");

        
            switch (jsondata_split[0]) {
                case "majnote":
                    if ( jsondata_split[1]== 'ok') {  $(idelemt).css("border", "1px solid green");   }
                    else{  $(idelemt).css("border", "1px solid red");   }                          
                break;
                
                default:
                break;
            }
  
        }


        
    }

    $('#prof_eval_select_groupe').on('change', function(event) {

        var id_prof = $('#type_id').val();
        titre="//evenement Change sur: #prof_eval_select_groupe";
        info="  // Valeur= ";
        valueur= id_prof;
        consoleInfo(envi, titre, info, valueur);
       var id_groupe = $('#prof_eval_select_groupe').val();
       
       if (id_groupe == 0 || id_groupe == "") {}
       else{

            url_info = liens+'App/Models/Prof_AjaxOnline.php';
            values_info = 'action=getProfmatiereByGrp&id_prof='+ id_prof;

            idelemt = "#prof_eval_select_groupe";

            AjaxMethod(url_info, values_info, fctGetProfMat,idelemt);
 
        }


    });


    $('#dataTable_list_annee .input_note_eleve').on('change', function(event) {

        if (typeof(event.target.id)!=undefined ) {

            titre="::PROF EVENT:::  Change sur:(.input_note_eleve)";
            info="  //ID Valeur= "+ event.target.id;
            valueur= $('#dataTable_list_annee #'+event.target.id+'').val();
            consoleInfo(envi, titre, info, valueur);

            result_split = (event.target.id).split("_");
            note = $('#dataTable_list_annee #'+event.target.id+'').val();
            //id_eleve_eleve  & _ & eval_id


            url_info = liens+'App/Models/Prof_AjaxOnline.php';
            values_info = 'action=Set_eleve_eval_notes&id_eleve_eleve='+ result_split[0] +'&eval_id='+ result_split[1]+'&note_val='+ note;

            titre="::PROF EVENT:::  Ajax  liens : "+url_info;
            info="  //liens ="+url_info+"  Action =";
            valueur= values_info;
            consoleInfo(envi, titre, info, valueur);
            idelemt = '#dataTable_list_annee #'+event.target.id+'';

            AjaxMethod(url_info, values_info, fct_Set_eleve_eval_notes_result,idelemt);
            
        }
        else{
            titre="::PROF EVENT:::  Change sur:(.input_note_eleve)";
            info="  // Valeur  event.target.id undefined ";
            valueur= event.target.id;
            consoleInfo(envi, titre, info, valueur);

        }


    });
    
    $('#prof_emploitps_cal').on('click', function(event) {
        $('#timeline_prof .fc-today-button').click();
    });

     





});