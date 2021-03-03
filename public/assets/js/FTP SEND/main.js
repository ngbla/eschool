$(document).ready(function(){

    envi = "debug";
    //var liens = 'http://uges.x-pertizgroup.com/';
    var liens = 'http://localhost:8083/timeflow/elearning-pronote/';
    alert(window.location.hostname);



    $('#dataTable_list_annee').DataTable();
    $('#dataTableeleve').DataTable();
    $('#dataTableprof').DataTable();
    $('#dataTableparent').DataTable();
    $('#dataTableadmin').DataTable();
    $('#dataTable').DataTable();
    var dataTable_Lundi=$('#dataTable_Lundi').DataTable();
    $('#dataTable_Mardi').DataTable();
    $('#dataTable_Mercredi').DataTable();
    $('#dataTable_Jeudi').DataTable();
    $('#dataTable_Vendredi').DataTable();
    $('#dataTable_Samedi').DataTable();
    $('#dataTable_Dimanche').DataTable();

    $('#dataTable_attrib_listeElev_class').DataTable({
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

    $('#dataTable_attrib_list_groupByfilliere').DataTable();
    $('#dataTable_attrib_grpMat').DataTable();
    

    $('#dataTable_attrib_listeElev_NOclass').DataTable();
    $('#dataTable_attrib_emptpsByAn').DataTable();
    
    
    
    $('#dataTable_classe_list_annee').DataTable();
    var dataTable_classe_list_annee = $('#dataTable_classe_list_annee').DataTable();
    var dataTable_classe_classMat = $('#dataTable_classe_classMat').DataTable();
    var dataTable_attrib_mat = $('#dataTable_attrib_mat').DataTable();
    var eleve_attrib_classe_select_classe_val = 0;
    //id="dataTable_attrib_mat"
    
    //$('select').selectize({ sortField: 'text' });
    $("select").select2();


    function consoleInfo(envi, titre, info, valueur) {
        if(envi=="debug"){
            console.log(titre+' : ',info+'  ',"( "+valueur +" )");
            console.log("****************************************");
        }
    }
    
    //DETECT TOUS LES EVENEMENTS SOURIS
    document.addEventListener("click", function (event){
        //event.preventDefault();
        titre="evenement sur: "+event.target;
        info="id ="+event.target.id+"  Class="+event.target.class;
        valueur=event.target.value;
        consoleInfo(envi, titre, info, valueur);
        if (typeof(event.target.id) != undefined && event.target.id != "") {


            //ADMIN MENU ELEVE SUP OU AJOUT CLASSE
            if (typeof( $('#'+event.target.id+'').attr("value") ) != undefined) {

                if (  $('#'+event.target.id+'').attr("value")=="sup_ElevGroup" || $('#'+event.target.id+'').attr("value")=="add_ElevGroup"  ) {

                    values=[];
                    result = event.target.id;
                    values = result.split("_");

                    titre="sup_ElevGroup ou add_ElevGroup values ="+values;
                    info="values[1] ="+values[1]+"  values[0]="+values[0];
                    valueur=result;
                    consoleInfo(envi, titre, info, valueur);
                    
                }

                if ( $('#'+event.target.id+'').attr("value")=="sup_ElevGroup" ) {

                    ajoutSupElveClasse(0, values[1], values[0]);
                }
                else if( $('#'+event.target.id+'').attr("value")=="add_ElevGroup" ) {
  
                    ajoutSupElveClasse(1, values[1], values[0]);

                }
                
            }

            

            switch (event.target.id) {
                case "attribution_emlploiTps_mat_Lundi":
                    select_jour = "Lundi" ;
                     getProfListMajSelect(select_jour); 
                break;
            
                default:

                break;
            }


        }

  


    });



});