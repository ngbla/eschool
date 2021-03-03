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

    
    var eval_prog_table= $('#eval_prog_table').DataTable({
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

    var form_attribution_info = {

        affiche : function() {
            //return this.firstName + " " + this.lastName;
            for (var x in this) {
                 consoleInfo("debug", "form_attribution_info", x+"  Valeur =", form_attribution_info[x]);
            }
        }

    };


    function consoleInfo(envi, titre, info, valueur) {
        if(envi=="debug"){
            console.log(titre+' : ',info+'  ',"( "+valueur +" )");
            console.log("****************************************");
        }
    }

    //Menu Admin attribution form slide
    var attribution_from_next = 1;
    //::attribution :: Button Suivant
    $('#attribution_form_suivant').on('click', function(event) {

        
        
        consoleInfo(envi, "#attribution_annee_id", "recupe valeur","valeur de attribution_annee_id="+$('#attribution_annee_id').val());
        consoleInfo(envi, "#attribution_classe_id", "recupe valeur","valeur de attribution_classe_id="+$('#attribution_classe_id').val());
        consoleInfo(envi, "#attribution_form_suivant", "event (click) ","valeur carousel page="+attribution_from_next);
        
        //event.preventDefault();
        //$("#attribution_myCarousel").carousel("pause");
        //$("#attribution_myCarousel").carousel("next");
        //$("#attribution_myCarousel").carousel("prev");
        //$("#myCarousel").carousel(attribution_from_next);
        attribution_from_next++;

        switch (attribution_from_next) {

            case 1:
            break;
            case 2:

                if (  $('#attribution_annee_id').val()== 0 || $('#attribution_classe_id').val()==0 || $('#attribution_groupe_nom').val()=="") {
                    attribution_from_next--;
                    setTimeout(function(){ 
                        $("#attribution_myCarousel").carousel("prev");
                        consoleInfo(envi, "#attribution_form_suivant", "event (click) ","valeur carousel page="+attribution_from_next);
                        alert('Veuillez Choisir l Année Scolaire et la Classe !');
                    }, 1000);

                }
                else{

                    form_attribution_info.idanneescolaire = $('#attribution_annee_id').val();
                    form_attribution_info.idclasse = $('#attribution_classe_id').val();
                    form_attribution_info.nomgroupe = $('#attribution_groupe_nom').val();


                    values = 'action=setGroupe&annee_id='+ $('#attribution_annee_id').val()+'&classe_id='+$('#attribution_classe_id').val()+'&nomgroupe='+$('#attribution_groupe_nom').val();

                    $.ajax({
                        url: liens+'App/Models/info.model.php',
                        type: 'POST',
                        dataType: 'text',
                        async:true,
                        crossDomain:true,
                        data: values,
                    })
                    .done(function(data) {
                    
                        //consoleInfo(envi, "Attribution", " Ajax Retour  valeur :: data :: = "+data);
                        //consoleInfo(envi, "Attribution", " Ajax Retour  JSAON valeur :: data :: = "+JSON.parse(data));

                        if (typeof(data) != undefined ) {
                            data= JSON.parse(data);

                            if (typeof(data.setGroupe[0].groupe_id) != undefined && data.setGroupe[0].groupe_id != undefined) {
                                form_attribution_info.groupe_id = data.setGroupe[0].groupe_id;

                                //consoleInfo(envi, "exite deja", " data.setGroupe[0].groupe_id  ",data.setGroupe[0].groupe_id);

                            }
                            else{

                                form_attribution_info.groupe_id = data.setGroupe;
                            }

                            form_attribution_info.affiche();

                            $("#attribution_form_prec").show();

                            consoleInfo(envi, "Attribution", " Ajax Send post=  ",values);

                            values = 'action=getClassesMatiere&annee_id='+ $('#attribution_annee_id').val()+'&classe_id='+$('#attribution_classe_id').val();
                            $.ajax({
                                url: liens+'App/Models/info.model.php',
                                type: 'POST',
                                dataType: 'text',
                                async:true,
                                crossDomain:true,
                                data: values,
                            })
                            .done(function(data) {
                               
                                $('#attrib_etap2_partannee_id').html("<option>Veuillez choisir une partie</option>");

                                $('#attrib_etap2_mat_id').html('<option value="0">Veuillez choisir une matière</option>');
                                $('#attrib_etap2_mats1_id').html('<option value="0">Veuillez choisir une sous matière</option>');
                                $('#attrib_etap2_mat_ids2').html('<option value="0">Veuillez choisir une sous matière</option>');
                                $('#attrib_etap2_mat_ids3').html('<option value="0">Veuillez choisir une sous matière</option>');
                                var matierehtml="";
                                
                                consoleInfo(envi, "Attribution", " Ajax Retour  valeur :: data :: = "+data);
                                //consoleInfo(envi, "Attribution", " Ajax Retour  JSAON valeur :: data :: = "+JSON.parse(data));
        
                                if (typeof(data) != undefined ) {
                                    data= JSON.parse(data);

                                    for (var x in data.anneescolairepart) {
                                        $('#attrib_etap2_partannee_id').append('<option value="'+data.anneescolairepart[x].id_annee_partie+'">'+data.anneescolairepart[x].libele_partie+'</option>');
                                    }
                                    for (var y in data.classematiere) {

                                        $('#attrib_etap2_mat_id').append('<option value="'+data.classematiere[y].id_matiere+'">'+data.classematiere[y].libele+'</option>');
                                        $('#attrib_etap2_mats1_id').append('<option value="'+data.classematiere[y].id_matiere+'">'+data.classematiere[y].libele+'</option>');
                                        $('#attrib_etap2_mat_ids2').append('<option value="'+data.classematiere[y].id_matiere+'">'+data.classematiere[y].libele+'</option>');
                                        $('#attrib_etap2_mat_ids3').append('<option value="'+data.classematiere[y].id_matiere+'">'+data.classematiere[y].libele+'</option>');
        
                                    }
                                    
                                }
        
                              })
                            .fail(function() { });



                            values = 'action=getGroupList&id_groupe='+form_attribution_info.groupe_id  ;

                            $.ajax({
                                url: liens+'App/Models/info.model.php',
                                type: 'POST',
                                dataType: 'text',
                                async:true,
                                crossDomain:true,
                                data: values,
                            })
                            .done(function(data) {
         
                                $('#dataTable_attrib_mat_tbody').html("");
                                //consoleInfo(envi, "Attribution", " Ajax Retour  valeur :: data :: = "+data);
                                //consoleInfo(envi, "Attribution", " Ajax Retour  JSAON valeur :: data :: = "+JSON.parse(data));

                                if (typeof(data) != undefined ) {
                                    data= JSON.parse(data);
                                    /*<th>Nom Partie</th>
                                    <th>matière</th>
                                    <th>Coéfficient</th>
                                    <th>matière parent</th>
                                    <th>Action</th>*/

                                    for (var p in data.getSousmatiereBy) {
                                        $('#dataTable_attrib_mat_tbody').append('<tr ><td >'+data.getSousmatiereBy[p].part_annee_id_tmp+'</td><td >'+data.getSousmatiereBy[p].libele+'</td><td >'+data.getSousmatiereBy[p].coeficient_tmp+'</td> <td >'+data.getSousmatiereBy[p].matparent+'</td> <td ></td></tr >');
                                    }
                                    for (var k in data.getMatiereBy) {
                                        $('#dataTable_attrib_mat_tbody').append('<tr ><td >'+data.getMatiereBy[k].part_annee_id_tmp+'</td><td >'+data.getMatiereBy[k].libele+'</td><td >'+data.getMatiereBy[k].coeficient_tmp+'</td> <td >'+data.getMatiereBy[k].matparent+'</td> <td ></td></tr >');
                                    }
                                    
                                }

                            })
                            .fail(function() { });

        
                        }

                    })
                    .fail(function() { });




                }
                
            break;
            case 3:

                if (  $('#attribution_annee_id').val()== 0 || $('#attribution_classe_id').val()==0 ) {
                    
                    setTimeout(function(){ 
                        $("#attribution_myCarousel").carousel("prev");
                        attribution_from_next--;
                        consoleInfo(envi, "#attribution_form_suivant", "event (click) ","valeur carousel page="+attribution_from_next);
                        alert('Veuillez Choisir l Année Scolaire et la Classe !');
                    }, 1000);
                    setTimeout(function(){ 
                        $("#attribution_myCarousel").carousel("prev");
                        attribution_from_next--;
                        consoleInfo(envi, "#attribution_form_suivant", "event (click) ","valeur carousel page="+attribution_from_next);
                        alert('Veuillez Choisir l Année Scolaire et la Classe !');
                    }, 1500);

                }
                else{
                    

                    values = 'action=getClassesMatiere&annee_id='+ $('#attribution_annee_id').val()+'&classe_id='+$('#attribution_classe_id').val();

                    consoleInfo(envi, "Attribution", " Ajax Send post=  ",values);

                    $.ajax({
                        url: liens+'App/Models/info.model.php',
                        type: 'POST',
                        dataType: 'text',
                        async:true,
                        crossDomain:true,
                        data: values,
                    })
                    .done(function(data) {
                       
                        $('#attribution_etape2_infos').html("");
                        var matierehtml="";
          
                        consoleInfo(envi, "Attribution", " Ajax Retour  valeur :: data :: = "+data);
                        //consoleInfo(envi, "Attribution", " Ajax Retour  JSAON valeur :: data :: = "+JSON.parse(data));
                        

                        if (typeof(data) != undefined ) {
                            data= JSON.parse(data);

                            for (var y in data.classematiere) {

                                consoleInfo(envi, "Attribution", " Ajax Retour valeur :: getClasseMatiereBy :: = "+  data.classematiere[y].libele);

                                        $('#attribution_emlploiTps_mat_Lundi').append('<option value="'+data.classematiere[y].id_matiere+'">'+data.classematiere[y].libele+'</option>');

                            }
                            
                        }
                        else { }
                        
                      })
                    .fail(function() {  });


                }
                
            break;
            case 4:
                $("#attrib_etapfinal_info").html(form_attribution_info.nomgroupe);
                $("#attribution_form_suivant").hide();
            break;
            default:
                attribution_from_next = 1;
            break;
        }


    });
    //::attribution ::hIDE prec
    $("#attribution_form_prec").hide();
    $('#attribution_form_prec').on('click', function(event) {

        consoleInfo(envi, "#attribution_form_prec", "click","valeur ="+attribution_from_next);

        attribution_from_next--;

        switch (attribution_from_next) {

            case 1:

                $("#attribution_form_prec").hide();

                
            break;
            case 2:

                $("#attribution_form_prec").show();
                
            break;
            case 3:
                $("#attribution_form_prec").show();
            break;
        
            default:

                $("#attribution_form_prec").hide();

            break;
        }

    });


    $('#bt_ajout_mat_attrib_Lundi').on('click', function(event) {
        
        titre="(Attribution ::Emploi du temps :: ajout matiere::) evenement sur: "+event.target;
        info="id ="+event.target.id+"  Class="+event.target.class;
        valueur=event.target.value;
        consoleInfo(envi, titre, info, valueur);
        attriAjoutMatTab("Lundi");
    });

    //  BTN ajout matiere
    $('#btn_attrib_etap2_ajout_mat').on('click', function(event) {

        if ( $('#attrib_etap2_partannee_id').val() == 0 || $('#attrib_etap2_partannee_id').val() == "" || $('#attrib_etap2_mat_id').val() == "" || $('#attrib_etap2_mat_id').val() == "" || $('#attrib_etap2_mat_coef').val() == 0 || $('#attrib_etap2_mat_coef').val() == "") {

            alert("Veuillez remplir tous les champs nécessaire !!");

        }
        else{


            values = 'action=setGroupeMat&groupe_id='+ $('#input_grpid').val() +'&part_annee='+$('#attrib_etap2_partannee_id').val() +'&matiere='+$('#attrib_etap2_mat_id').val() +'&mat_coef='+$('#attrib_etap2_mat_coef').val();

            if ($('#attrib_etap2_mats1_id').val() != 0 && $('#attrib_etap2_mats1_coef').val() != "" && $('#attrib_etap2_mats1_coef').val() != 0) {
                values = values + '&sous_mat1='+$('#attrib_etap2_mats1_id').val() +'&sous_mat1_coef='+$('#attrib_etap2_mats1_coef').val() ;
            }

            if ($('#attrib_etap2_mat_ids2').val() != 0 && $('#attrib_etap2_mat_coefs2').val() != "" && $('#attrib_etap2_mat_coefs2').val() != 0) {
                values = values + '&sous_mat2='+$('#attrib_etap2_mat_ids2').val() +'&sous_mat2_coef='+$('#attrib_etap2_mat_coefs2').val() ;
            }

            if ($('#attrib_etap2_mat_ids3').val() != 0 && $('#attrib_etap2_mat_coefs3').val() != "" && $('#attrib_etap2_mat_coefs3').val() != 0) {
                values = values + '&sous_mat3='+$('#attrib_etap2_mat_ids3').val() +'&sous_mat3_coef='+$('#attrib_etap2_mat_coefs3').val() ;
            }
            
            consoleInfo(envi, "attribution ajout matiere", "values ",values);

            

            $.ajax({
                url: liens+'App/Models/info.model.php',
                type: 'POST',
                dataType: 'text',
                async:true,
                crossDomain:true,
                data: values,
            })
            .done(function(data) {

                //data= JSON.parse(data);
                consoleInfo(envi, "attribution ajout matiere", "Ajax result ",data.setGroupeMat);
                location.reload();

 
            })
            .fail(function() { });



        }
    });
    

    $('.modif_doc_rev').on('click', function(event) {

        console.log("***** Active compte   click *****");

        values='' ;
        result='' ;	
        val1="" ;
        val2="" ;
        val3="" ;
        val4="" ;
        
        //var result = $('.modif_doc_rev').serialize();
        result = event.target.value;
        console.log("modif_doc_rev = "+result );
        //DOC_12%3B2
        values = result.split("_");
        console.log("modif_doc_rev = "+values );
        val1 = values[0];
        val2 = values[1];
        val3 = values[2];
        val4 = values[3];

        values = 'idpers='+values[0]+'&idtype='+values[1]+'&type='+values[2]+'&mode='+values[3]+'&action=activecpte';

        console.log(values );
        
        $.ajax({
            url: liens+'App/Models/info.model.php',
            type: 'POST',
            dataType: 'text',
            async:true,
            crossDomain:true,
            data: values,
        })
        .done(function(data) {
            console.log("result  modif_doc_rev= "+ data );
            //alert('data='+data);
            if (data == 1) {
                window.location.reload();
                console.log("Mise a jour etat = effectue" );

            }
            else {
                window.location.reload();
                console.log("Mise a jour etat = erreur" );
                alert("Erreur ! ");
            }

          })
        .fail(function() {
        
            alert("Erreur ! ");

        });
        
        

    });

    $('#admin_creerAnnee_listPart_select').on('change', function(event) {
        $('#admin_creerAnnee_listPart').html("");
        htmlinfo = "";
        //var result = $('#admin_creerAnnee_listPart_select').serialize();
        var result = $('#admin_creerAnnee_listPart_select').val();
        consoleInfo(envi, "#admin_creerAnnee_listPart_select", "change",result);
		//var values = $('.modif_doc_rev').val();
        //alert( values );
        for (var index = 0; index < result; index++) {
            
           htmlinfo += "<div class='form-group form-inline'><label >Titre Année Scolaire Partie "+(index+1)+" : &nbsp; </label><div class='form-group'><div class='input-group date'><input type='text' name='cree_anne_scol_Part"+(index+1)+"' placeholder='Ex: Semestre "+(index+1)+"' class='form-control' required/> </div></div></div><div class='form-group form-inline'><label >Début P"+(index+1)+" : &nbsp; </label><div class='form-group'><div class='input-group date'><input type='date' name='cree_anne_scol_Part"+(index+1)+"_dateDebut' class='form-control' required/></div></div><label >&nbsp;Fin P"+(index+1)+" : &nbsp; </label> <div class='form-group'><div class='input-group date'><input type='date' name='cree_anne_scol_Part"+(index+1)+"_dateFin' class='form-control' required/></div></div></div><hr>";
            
        }


        $('#admin_creerAnnee_listPart').html(htmlinfo);
    });

    $('#attribution_annee_id').on('change', function(event) {
        $('#admin_creerAnnee_listPart').html("");
        htmlinfo = "";
        //var result = $('#admin_creerAnnee_listPart_select').serialize();
        var result = $('#admin_creerAnnee_listPart_select').val();
        consoleInfo(envi, "#admin_creerAnnee_listPart_select", "change",result);
		//var values = $('.modif_doc_rev').val();
        //alert( values );
        for (var index = 0; index < result; index++) {
            
           htmlinfo += "<div class='form-group form-inline'><label >Titre Année Scolaire Partie "+(index+1)+" : &nbsp; </label><div class='form-group'><div class='input-group date'><input type='text' name='cree_anne_scol_Part"+(index+1)+"' placeholder='Ex: Semestre "+(index+1)+"' class='form-control' required/> </div></div></div><div class='form-group form-inline'><label >Début P"+(index+1)+" : &nbsp; </label><div class='form-group'><div class='input-group date'><input type='date' name='cree_anne_scol_Part"+(index+1)+"_dateDebut' class='form-control' required/></div></div><label >&nbsp;Fin P"+(index+1)+" : &nbsp; </label> <div class='form-group'><div class='input-group date'><input type='date' name='cree_anne_scol_Part"+(index+1)+"_dateFin' class='form-control' required/></div></div></div><hr>";
            
        }


        $('#admin_creerAnnee_listPart').html(htmlinfo);
    });

    $('#attribution_classe_id').on('change', function(event) {
        $('#admin_creerAnnee_listPart').html("");
        htmlinfo = "";
        //var result = $('#admin_creerAnnee_listPart_select').serialize();
        var result = $('#admin_creerAnnee_listPart_select').val();
        consoleInfo(envi, "#admin_creerAnnee_listPart_select", "change",result);
		//var values = $('.modif_doc_rev').val();
        //alert( values );
        for (var index = 0; index < result; index++) {
            
           htmlinfo += "<div class='form-group form-inline'><label >Titre Année Scolaire Partie "+(index+1)+" : &nbsp; </label><div class='form-group'><div class='input-group date'><input type='text' name='cree_anne_scol_Part"+(index+1)+"' placeholder='Ex: Semestre "+(index+1)+"' class='form-control' required/> </div></div></div><div class='form-group form-inline'><label >Début P"+(index+1)+" : &nbsp; </label><div class='form-group'><div class='input-group date'><input type='date' name='cree_anne_scol_Part"+(index+1)+"_dateDebut' class='form-control' required/></div></div><label >&nbsp;Fin P"+(index+1)+" : &nbsp; </label> <div class='form-group'><div class='input-group date'><input type='date' name='cree_anne_scol_Part"+(index+1)+"_dateFin' class='form-control' required/></div></div></div><hr>";
            
        }


        $('#admin_creerAnnee_listPart').html(htmlinfo);
    });
    
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

            if ( typeof( $('#'+event.target.id+'').attr("option") ) != undefined ) {
                var option = $('#'+event.target.id+'').attr("option");
                switch (option) {
                    case "btn_evalProg_click":

                        evalProg_ideval = (event.target.id).split("_");
                        titre="::ADMIN EVENT:::  click sur:(.btn_prgoEval) ::ID Eval ="+evalProg_ideval[1];

                        info="  //ID Valeur= "+event.target.id;
                        resultinfo = event.target.value;
                        consoleInfo(envi, titre, info, valueur);

                        values = resultinfo.split("_");

                        $('#evalprog_grpe').html(values[1]);
                        $('#evalprog_mat').html(values[2]+'-'+values[3]);
                        $('#evalprog_prof').html(values[4]);
                        $('#evalprog_tel').html(values[5]);
                        $('#evalProg_ideval').html('<input type="hidden" name="evalProg_ideval" value="'+evalProg_ideval[1]+'">') ;
                        
                        break;
                
                    default:
                        break;
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

    function ajoutSupElveClasse(params1, params2, params3){

        values = 'action=setClassesElveve&mode='+ params1+'&groupe='+params2+'&idelev='+params3;

        $.ajax({
            url: liens+'App/Models/info.model.php',
            type: 'POST',
            dataType: 'text',
            async:true,
            crossDomain:true,
            data: values,
        })
        .done(function(data) {

            
            titre="ajoutSupElveClasse ";
            info="Ajax ajoutSupElveClasse result";
            valueur=data;
            consoleInfo(envi, titre, info, valueur);

            affich_eleve_attrib_classe_select_classe();
        

        })
        .fail(function() { });

        affich_eleve_attrib_classe_select_classe();
    }

    function getProfListMajSelect(params) {

        values = 'action=getListProf';

        consoleInfo(envi, "Attribution", " Ajax Send post=  ",values);

        $.ajax({
            url: liens+'App/Models/info.model.php',
            type: 'POST',
            dataType: 'text',
            async:true,
            crossDomain:true,
            data: values,
        })
        .done(function(data) {
           
            $('#enseignant_'+params).html('<option value="0">Selectioner un professeurs</option>');
            var matierehtml="";
            consoleInfo(envi, "Attribution", " Ajax Retour  valeur :: data :: = "+data);
            //consoleInfo(envi, "Attribution", " Ajax Retour  JSAON valeur :: data :: = "+JSON.parse(data));
            

            if (typeof(data) != undefined ) {
                data= JSON.parse(data);

                for (var x in data.listeProf) {

                    consoleInfo(envi, "Attribution", " Ajax Retour valeur :: getListProf :: = "+ data.listeProf[x].nom_prenom);
                    $('#enseignant_'+params).append('<option value="'+data.listeProf[x].id_prof_prof+'">'+data.listeProf[x].nom_prenom+'</option>');
                }
                
            }

          })
        .fail(function() { });

        
    }

    //BOUTON CREATION DEMATIERE
    $('.cree_matiere_btn_add').on('click', function(event) {

        var result = $('#'+event.target.id+'').attr("value");
        //DOC_12%3B2
        values = result.split("/_/");
     

        titre="click cree_matiere_btn_add";
        info="event sur = "+event.target;
        valueur="id ="+event.target.id+" && valeur ="+$('#'+event.target.id+'').attr("value");
        consoleInfo(envi, titre, info, valueur);

        var rowNode = dataTable_classe_classMat
            .row.add( [ values[0], values[1], '<a title="Delete Matière" class="btn-default btn-xs purple" href="#"><i class="fa fa-trash"><input type="hidden" value="'+values[1]+'" name="classmat_'+event.target.id+'"></i></a> ' ] )
            .draw()
            .node();
            
        
        $( rowNode )
            .css( 'color', 'green' )
            .animate( { color: 'black' } );
        /*   <th >Code Matière</th>
            <th >Nom Matière</th>
            <th>Action</th>
                   
            <td class="fieldtype_action field-152-td nowrap">
                <a title="Delete Matière" class=" btn-default btn-xs purple" href="#" onclick="open_dialog('#'); return false;">    <i class="fa fa-trash"></i></a> 
            </td>
            */
    });

    $('#dataTable_classe_classMat tbody').on( 'click', 'tr td a', function () {
        dataTable_classe_classMat
            .row( $(this).parents('tr') )
            .remove()
            .draw();
    } );

    $('#dataTable_classe_list_annee tbody').on( 'click', 'tr td a', function () {
        
            $(this).parents('tr')
            .css( 'color', 'red' );
    } );

    function attriAjoutMatTab(jour) {
        var dataTable;
        switch (jour) {
            case "Lundi":
                dataTable = dataTable_Lundi;
                break;
        
            default:
                break;
        }
        
        titre="(Attribution ::Emploi du temps :: Champ valeur::)  ";
        info="matiere =("+$("#attribution_emlploiTps_mat_"+jour+"").val()+")  heure debut="+$("#heuredebut_"+jour+"").val()+"  heure fin="+$("#heurefin_"+jour+"").val()+"  professeur="+$("#enseignant_"+jour+"").val();
        valueur=event.target.value;
        consoleInfo(envi, titre, info, valueur);

        var rowNode = dataTable
        .row.add( [ $("#heuredebut_"+jour+"").val(), $("#heurefin_"+jour+"").val(),$("#attribution_emlploiTps_mat_"+jour+" option:selected").text(),$("#enseignant_"+jour+" option:selected").text(), '<a title="Delete Matière" class="btn-default btn-xs purple" href="#"><i class="fa fa-trash"><input type="hidden" value="'+$("#attribution_emlploiTps_mat_"+jour+"").val()+'_"'+$("#enseignant_"+jour+"").val()+'"" ></i></a> ' ] )
        .draw()
        .node();
        
    
        $( rowNode )
        .css( 'color', 'green' )
        .animate( { color: 'black' } );

        values = 'action=setEmploiTps&emploitps_jour='+jour +'&emploitps_id_mat='+$("#attribution_emlploiTps_mat_"+jour).val() +'&emploitps_id_prof='+$("#enseignant_"+jour).val() +'&emploitps_h_debut='+$("#heuredebut_"+jour+"").val() +'&emploitps_h_fin='+$("#heurefin_"+jour+"").val()  +'&emploitps_groupe_id='+form_attribution_info.groupe_id;

       // emploitps_id 	emploitps_jour 	emploitps_id_mat 	emploitps_id_prof 	emploitps_h_debut 	emploitps_h_fin 	emploitps_groupe_id 	emploitps_etat 
        $.ajax({
            url: liens+'App/Models/info.model.php',
            type: 'POST',
            dataType: 'text',
            async:true,
            crossDomain:true,
            data: values,
        })
        .done(function(data) {
        
            if (typeof(data) != undefined && data != "") {
                data= JSON.parse(data);
            }


        })
        .fail(function() { });


  
    }

    // FUNCTION MISE A JOUR PAGE ATTRIBUTION ETAP 2
    function attrribution_etap2_html(semestre_livelle,semestreid) {
        var divhtml ='<div class="card shadow mb-4" id="attri_sem_'+semestreid+'"><a href="#collapse_sem_'+semestreid+'" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="#collapse_sem_'+semestreid+'"><h6 class="m-0 font-weight-bold text-primary">'+semestre_livelle+'</h6></a><div class="collapse show" id="#collapse_sem_'+semestreid+'" ><div class="card-body"><div class="row"><div class="col-xl-12 col-lg-12">  <div class="card shadow mb-4"><div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6 class="m-0 font-weight-bold text-primary">Ajout de Matière</h6></div><div class="card-body">   <div class="table-responsive" style="font-size: 0.7em;"><table class="table table-bordered" id="attribution_part_an_Allclasse_'+semestreid+'" style="width:100%" cellspacing="0" ><thead><tr><th >Code Matière</th><th >Nom Matière</th><th >Classe</th><th>Coefficient</th><th>Choisir</th><th>Sous matières</th></tr></thead><tfoot><tr><th >Code Matière</th><th >Nom Matière</th><th >Classe</th><th>Coefficient</th><th>Choisir</th><th>Sous matières</th></tr></tfoot> <tbody id="tbody_'+semestreid+'"></tbody> </table>   </div> </div></div> </div>  </div></div></div></div>';
        return divhtml;
    }

    //::::MENU ADMIN ELEVES::::::::::

    $('#eleve_attrib_classe_select_annee').on('change', function(event) {
        
        ajax_attribClasse_elv_majgrpSelect();

    } );

    $('#eleve_attrib_classe_select_classe').on('change', function(event) {
        
        affich_eleve_attrib_classe_select_classe();

    } );

    function affich_eleve_attrib_classe_select_classe() {

        if ( $('#eleve_attrib_classe_select_classe').val() != 0) {

            eleve_attrib_classe_select_classe_val = $('#eleve_attrib_classe_select_classe').val();

            values = 'action=getGrpElve&annee_id='+ $('#eleve_attrib_classe_select_annee').val()+'&classe_id='+eleve_attrib_classe_select_classe_val;

            $.ajax({
                url: liens+'App/Models/info.model.php',
                type: 'POST',
                dataType: 'text',
                async:true,
                crossDomain:true,
                data: values,
            })
            .done(function(data) {

                if (typeof(data) != undefined && data != "") {
                    
                    data= JSON.parse(data);
                    
                    $('#eleve_attrib_classe_ListElvClss').html('');
                    if (typeof(data.getGrpElve) != undefined && data.getGrpElve != "" ) {
                        
                        for (var x in data.getGrpElve) {
                            $('#eleve_attrib_classe_ListElvClss').append('<tr><td>'+data.getGrpElve[x].matricule+'</td><td>'+data.getGrpElve[x].nom_prenom+'</td><td>'+data.getGrpElve[x].sexe+'</td><td>'+data.getGrpElve[x].contact+'</td><td><a  title="Retirer l Eleve" class="btn-danger btn-xs purple " href="#"><i class="fa fa-window-close sup_ElevGroup" value="sup_ElevGroup" id="'+data.getGrpElve[x].id_eleve_eleve+'_'+$("#eleve_attrib_classe_select_classe").val()+'"></i> </a></td></tr>');
    
                            consoleInfo(envi, "Elve classe Attribution", " Ajax Retour  valeur :: getGrpElve :: = "+data.getGrpElve);
                        }
                    }
    
                    $('#eleve_attrib_classe_ListElvSansClss').html('');
                    if (typeof(data.getGrpNotElve) != undefined && data.getGrpNotElve != "" ) {
            
                        for (var y in data.getGrpNotElve) {
                            $('#eleve_attrib_classe_ListElvSansClss').append('<tr><td>'+data.getGrpNotElve[y].matricule+'</td><td>'+data.getGrpNotElve[y].nom_prenom+'</td><td>'+data.getGrpNotElve[y].sexe+'</td><td>'+data.getGrpNotElve[y].contact+'</td><td><a  title="Ajouter Eleve" class="btn-default btn-xs purple " href="#"><i class="fa fa-plus add_ElevGroup" value="add_ElevGroup" id="'+data.getGrpNotElve[y].id_eleve_eleve+'_'+$("#eleve_attrib_classe_select_classe").val()+'"></i> </a></td></tr>');
    
                            consoleInfo(envi, "Elve classe Attribution", " Ajax Retour  valeur :: getGrpNotElve :: = "+data.getGrpNotElve);
    
                       
                        }
                    }
                    
                }
    
            })
            .fail(function() { });
    

            
        }else{

            alert("Veuillez choisir une classe");



        }

    }

    ajax_attribClasse_elv_majgrpSelect();

    function ajax_attribClasse_elv_majgrpSelect() {
        result =$('#eleve_attrib_classe_select_annee').val();
        values = 'action=getAllGrpByannee&annee_id='+ result;

        $.ajax({
            url: liens+'App/Models/info.model.php',
            type: 'POST',
            dataType: 'text',
            async:true,
            crossDomain:true,
            data: values,
        })
        .done(function(data) {
        

            
            
            //consoleInfo(envi, "Attribution", " Ajax Retour  valeur :: data :: = "+data);
            //consoleInfo(envi, "Attribution", " Ajax Retour  JSAON valeur :: data :: = "+JSON.parse(data));

            if (typeof(data) != undefined ) {
                data= JSON.parse(data);
                $('#eleve_attrib_classe_select_classe').html('<option value="0">Choisissez une classe</option>');

                for (var x in data.getAllGrpByannee) {
                    $('#eleve_attrib_classe_select_classe').append('<option value="'+ data.getAllGrpByannee[x].groupe_id+'">'+ data.getAllGrpByannee[x].groupe_libelle+'</option>');
                }
                
            }

        })
        .fail(function() { });
        
    }

    //::::MENU GESTION DES CLASSES::::::::::
    $('#attribution_emlploiTps_anneescolaire').on('change', function(event) {

        if ( typeof($('#attribution_emlploiTps_anneescolaire').val()) != undefined && $('#attribution_emlploiTps_anneescolaire').val() != 0) {

            values = 'action=attribution_emlploiTps_getPeriode&annee_id='+ $('#attribution_emlploiTps_anneescolaire').val();

            $.ajax({
                url: liens+'App/Models/info.model.php',
                type: 'POST',
                dataType: 'text',
                data: values,
            })
            .done(function(data) {
    
                
                
                //consoleInfo(envi, "Attribution", " Ajax Retour  valeur :: data :: = "+data);
                //consoleInfo(envi, "Attribution", " Ajax Retour  JSAON valeur :: data :: = "+JSON.parse(data));
    
                if (typeof(data) != undefined ) {

                    data= JSON.parse(data);

                    $('#attribution_emlploiTps_periode').html('<option value="0">Veuillez choisir la période</option>');
                    $('#attribution_emlploiTps_groupe').html('<option value="0">Veuillez choisir la Classe</option>');
                    $('#dataTable_attrib_emptpsByAn_tbody').html('');
                    
                    for (var x in data.getAllpartAnneeBy) {
                        $('#attribution_emlploiTps_periode').append('<option value="'+data.getAllpartAnneeBy[x].id_annee_partie+'">'+data.getAllpartAnneeBy[x].libele_partie+'</option>');
                    }

                    for (var y in data.getAllGrpBy) {
                        $('#attribution_emlploiTps_groupe').append('<option value="'+data.getAllGrpBy[y].groupe_id+'">'+data.getAllGrpBy[y].groupe_libelle+'</option>');
                    }

                    /*
                    <th>Date</th> emploitps_date: "2020-04-08"
                    <th>Groupe</th> groupe_libelle: "GCOB 1"
                    <th>matière</th> mat_libelle: "ECONOMIE"
                    <th>Salle</th> Code_salle: "101"  salle_libelle: "Salle de TD"
                    <th>Heure de Début</th> emploitps_h_debut: "10:00"
                    <th>Heure de Fin</th> emploitps_h_fin: "12:00"
                    <th>Professeur</th> nom_prenom: "professeur prof math"
                    <th>Période</th> libele_partie: "SEMESTRE 1"
                    <th>Action</th> emploitps_id: "5"

                    emploitps_anneescolaire: "1"
                    emploitps_groupe_id: "1"
                    emploitps_id_prof: "1"
                    emploitps_periode: "1"
                    groupe_id: "1"
                    id_annee_partie: "1"
                    id_type: "1"
                    */
                    for (var z in data.getEmploiTpsBy) {
                        $('#dataTable_attrib_emptpsByAn_tbody').append('<tr> <td>'+data.getEmploiTpsBy[z].emploitps_date+'</td> <td>'+data.getEmploiTpsBy[z].groupe_libelle+'</td> <td>'+data.getEmploiTpsBy[z].mat_libelle+'</td> <td>'+data.getEmploiTpsBy[z].Code_salle+'-'+data.getEmploiTpsBy[z].salle_libelle+'</td> <td>'+data.getEmploiTpsBy[z].emploitps_h_debut+'</td> <td>'+data.getEmploiTpsBy[z].emploitps_h_fin+'</td> <td>'+data.getEmploiTpsBy[z].nom_prenom+'</td> <td>'+data.getEmploiTpsBy[z].libele_partie+'</td> <td><a href="#" id="'+data.getEmploiTpsBy[z].emploitps_id+'" class="btn btn-primary btn-circle"><i class="fas fa-edit"></i></a><a href="#" id="'+data.getEmploiTpsBy[z].emploitps_id+'" class="btn btn-info btn-circle"><i class="fas fa-info"></i></a><a href="#" id="'+data.getEmploiTpsBy[z].emploitps_id+'" class="btn btn-danger btn-circle"><i class="fas fa-trash"></i></a></td> </tr> ');
                    }

                    
                    
                }
    
            })
            .fail(function() { });
    
            
        }


        

    } );

    
    $('#attribution_emlploiTps_periode').on('change', function(event) {
        listMatBygrpPartanne();      

    } );
    $('#attribution_emlploiTps_groupe').on('change', function(event) {
        listMatBygrpPartanne();       

    } );

    function listMatBygrpPartanne() {

        if ( typeof($('#attribution_emlploiTps_periode').val()) != undefined && $('#attribution_emlploiTps_periode').val() != 0 && typeof($('#attribution_emlploiTps_groupe').val()) != undefined && $('#attribution_emlploiTps_groupe').val() != 0 ) {

            values = 'action=attribution_emlploiTps_matiere&part_id='+ $('#attribution_emlploiTps_periode').val()+'&grpe_id='+ $('#attribution_emlploiTps_groupe').val();

            $.ajax({
                url: liens+'App/Models/info.model.php',
                type: 'POST',
                dataType: 'text',
                data: values,
            })
            .done(function(data) {

                //consoleInfo(envi, "Attribution", " Ajax Retour  valeur :: data :: = "+data);
                //consoleInfo(envi, "Attribution", " Ajax Retour  JSAON valeur :: data :: = "+JSON.parse(data));
    
                if (typeof(data) != undefined ) {
                    data= JSON.parse(data);
                    $('#attribution_emlploiTps_mat').html('<option value="0">Veuillez choisir la matière</option>');
    
                    for (var x in data.getMatiereByPartGrp) {
                        $('#attribution_emlploiTps_mat').append('<option value="'+data.getMatiereByPartGrp[x].id_matiere_matiere+'">'+data.getMatiereByPartGrp[x].libele +'</option>');
                    }   
                }
    
            })
            .fail(function() { });
    
            
        }


        
    }

    
    //::::MENU EVALUATION::::::::::

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


    $('#eval_prog_annee').on('change', function(event) {

        titre="::ADMIN EVENT:::  Change sur:(#eval_prog_annee)";
        info="  //ID Valeur= ";
        valueur=  $('#eval_prog_annee').val();
        consoleInfo(envi, titre, info, valueur);


        url_info = liens+'App/Models/info.model.php';
        values_info = 'action=get_eval_prog&id_annee='+ $('#eval_prog_annee').val();
        idelemt ="#eval_prog_annee"; 

        AjaxMethod(url_info, values_info, fct_tabSet_eval_prog ,idelemt);

    });


    function fct_tabSet_eval_prog(jsondata) {

        if (typeof(jsondata) !=undefined && jsondata != "") {
            $('#eval_prog_table_tbody').html("");
            jsondata = JSON.parse(jsondata);
            //Date	Groupe	matière	Professeur	Action
            titre="evenement sur: Fct (fct_tabSet_eval_prog) // ";
            info="Ajax send  resultat ::  ";
            valueur=jsondata;
            consoleInfo(envi, titre, info, valueur);

            for (var x in jsondata.get_eval_prog) {

                var rowNode = eval_prog_table
                .row.add( [ jsondata.get_eval_prog[x].date_creation_eval, jsondata.get_eval_prog[x].groupe_libelle, jsondata.get_eval_prog[x].code+'-'+jsondata.get_eval_prog[x].libele, jsondata.get_eval_prog[x].nom_prenom,jsondata.get_eval_prog[x].contact,  '<!-- :: BUTTON ::--> <button option="btn_evalProg_click" type="button" class="btn btn-primary btn_prgoEval" id="btnprogEvalid_'+jsondata.get_eval_prog[x].prof_eval_id+'" data-toggle="modal" data-target="#progEval_modal" value="'+jsondata.get_eval_prog[x].date_creation_eval+'_'+jsondata.get_eval_prog[x].groupe_libelle+'_'+jsondata.get_eval_prog[x].code+'_'+jsondata.get_eval_prog[x].libele+'_'+jsondata.get_eval_prog[x].nom_prenom+'_'+jsondata.get_eval_prog[x].contact+'">Programmer</button><!-- :: BUTTON ::--> ' ] )
                .draw()
                .node();
            
                $( rowNode )
                    .css( 'color', '#858796' )
                    .animate( { color: '#858796' } );
            }

            

  
        }
        
    }



    



});