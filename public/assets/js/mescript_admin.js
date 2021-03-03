$(document).ready(function() {

    /**
     * VARIABLES
     *
     */
    //alert(window.location.hostname);
    envi = Appconfigs.envi;

    liens = Appconfigs.liens_serv;
    if (typeof(global_univ) != undefined) {

        global_univ = $('#global_univ').val();
        global_admin = $('#global_admin').val();
    }


    // Setup 1 - add a text input to each footer cell
    $('#db_list_absence tfoot th').each(function() {
        var title = $(this).text();
        $(this).html('<input type="text" placeholder=" ' + title + '" />');
    });

    // Setup 1 - add a text input to each footer cell
    $('#eleve_absence_tbe tfoot th').each(function() {
        var title = $(this).text();
        $(this).html('<input type="text" placeholder=" ' + title + '" />');
    });

    var db_list_absence = $('#db_list_absence').DataTable({
        initComplete: function() {
            // Apply the search
            this.api().columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change clear', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
        },
        dom: 'Blfrtip',
        buttons: [{
                extend: 'copyHtml5',
                text: '<span style="font-size: 20px; color: Mediumslateblue;"><i class="fas fa fa-copy "></i></span>',
                title: 'Liste de classe {% if info_groupe.0 is defined %}{{info_groupe.0.groupe_libelle}}{% endif %}',
                titleAttr: 'Copy'
            }, {
                extend: 'excelHtml5',
                text: '<span style="font-size: 20px; color: green;"><i class="fas fa fa-file-excel"></i></span>',
                title: 'Liste de classe {% if info_groupe.0 is defined %}{{info_groupe.0.groupe_libelle}}{% endif %}',
                titleAttr: 'Excel'
            }, {
                extend: 'csvHtml5',
                text: '<span style="font-size: 20px; color: Dodgerblue;"><i class="fas fa fa-file-csv"></i></span>',
                title: 'Liste de classe {% if info_groupe.0 is defined %}{{info_groupe.0.groupe_libelle}}{% endif %}',
                titleAttr: 'CSV'
            }, {
                extend: 'pdfHtml5',
                text: '<span style="font-size: 20px; color: Tomato;"><i class="fas fa fa-file-pdf"></i></span>',
                title: 'Liste de classe {% if info_groupe.0 is defined %}{{info_groupe.0.groupe_libelle}}{% endif %}',
                titleAttr: 'PDF'
            }

        ]
    });
    var eleve_absence_tbe = $('#eleve_absence_tbe').DataTable({
        initComplete: function() {
            // Apply the search
            this.api().columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change clear', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
        },
        "autoWidth": true,
        dom: 'Blfrtip',
        scrollCollapse: true,
        paging: false,
        columnDefs: [
            { width: '20%', targets: 0 }
        ],
        fixedColumns: true,

        buttons: [{
                extend: 'copyHtml5',
                text: '<span style="font-size: 20px; color: Mediumslateblue;"><i class="fas fa fa-copy "></i></span>',
                titleAttr: 'Copy'
            }, {
                extend: 'excelHtml5',
                text: '<span style="font-size: 20px; color: green;"><i class="fas fa fa-file-excel"></i></span>',
                titleAttr: 'Excel'
            }, {
                extend: 'csvHtml5',
                text: '<span style="font-size: 20px; color: Dodgerblue;"><i class="fas fa fa-file-csv"></i></span>',
                titleAttr: 'CSV'
            }, {
                extend: 'pdfHtml5',
                text: '<span style="font-size: 20px; color: Tomato;"><i class="fas fa fa-file-pdf"></i></span>',
                titleAttr: 'PDF'
            }

        ]
    });

    /** */
    $('#dataTable').DataTable();

    // Setup - add a text input to each footer cell
    $('table tfoot th').each(function() {
        var title = $(this).text();
        $(this).html('<input style="width: 100%;" type="text" placeholder="Search ' + title + '" />');
    });

    // DataTable with search colom
    var dataTable_attrib_emptpsByAn = $('#dataTable_attrib_emptpsByAn').DataTable({
        initComplete: function() {
            // Apply the search
            this.api().columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change clear', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
        },
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

    // DataTable with search colom
    var dtbe_listall = $('#dtbe_listall').DataTable({
        initComplete: function() {
            // Apply the search
            this.api().columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change clear', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
        },
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
    /*********** */


    var allowedTypes = ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'tif'];

    var dataTable_list_annee = $('#dataTable_list_annee').DataTable({
        dom: 'Blfrtip',
        "order": [
            [1, "asc"]
        ],
        "pageLength": 50,
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

    $('#dataTable_list_classe').DataTable({
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

    $('#dataTable_list_profmat_news').DataTable({
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

    $('#dataTable_stageetud').DataTable({
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

    $('#dataTable_list_fillierenews').DataTable({
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




    $('#dataTable_list_creermatiere').DataTable({
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



    var dataTable_list_anneeNEWS = $('#dataTable_list_anneeNEWS').DataTable({
        initComplete: function() {
            // Apply the search
            this.api().columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change clear', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
        },
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


    $('#dataTableeleve').DataTable();
    $('#dataTableprof').DataTable();
    $('#dataTableparent').DataTable();
    $('#dataTableadmin').DataTable();
    $('#dataTable').DataTable();
    var dataTable_Lundi = $('#dataTable_Lundi').DataTable();
    $('#dataTable_Mardi').DataTable();
    $('#dataTable_Mercredi').DataTable();
    $('#dataTable_Jeudi').DataTable();
    $('#dataTable_Vendredi').DataTable();
    $('#dataTable_Samedi').DataTable();
    $('#dataTable_Dimanche').DataTable();

    $('#dataTable_attrib_listeElev_classnews').DataTable({
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


    $('#dataTable_attrib_list_groupByfilliere_news').DataTable({
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


    var eval_prog_table = $('#eval_prog_table').DataTable({
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

    var dataTable_salle_etat = $('#dataTable_salle_etat').DataTable({
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

    //$('#dataTable_attrib_grpMat').DataTable();


    $('#dataTable_attrib_listeElev_NOclass').DataTable();



    var dataTable_classe_list_annee = $('#dataTable_classe_list_annee').DataTable();

    var dataTable_classe_classMat = $('#dataTable_classe_classMat').DataTable({ "pageLength": 1000 });


    var dataTable_attrib_mat = $('#dataTable_attrib_mat').DataTable();
    var eleve_attrib_classe_select_classe_val = 0;
    //id="dataTable_attrib_mat"

    /**
     * FUNCTIONS
     **/


    /* ******* FUNCTIONS******  */
    function consoleInfo(envi, titre, info, valueur) {

        if (envi == "debug") {
            console.log(titre + ' : ', info + '  ', "( " + valueur + " )");
            console.log("****************************************");
        }
    }
    //ajax fction
    function AjaxMethod(url, parameters, successCallback, idelemt) {
        //show loading... image
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'text',
            data: parameters,
            success: successCallback,
            error: function(xhr, textStatus, errorThrown) {
                console.log('error');
                console.log('error! Veuillez contactez l Administrateur');
            }
        });
    }

    function fct_set_selectmoy_mat(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {
            $('#moy_mat').html('<option ></option><option value="" disabled selected>Choisissez la Matière</option>');
            jsondata = JSON.parse(jsondata);
            //Date	Groupe	matière	Professeur	Action
            titre = "evenement sur: Fct (fct_set_selectmoy_mat) // ";
            info = "Ajax send  resultat ::  ";
            valueur = jsondata;
            consoleInfo(envi, titre, info, valueur);

            for (var x in jsondata.grpematiere) {
                $('#moy_mat').append('<option value="' + jsondata.grpematiere[x].id_matiere + '">' + jsondata.grpematiere[x].code_mat + '-' + jsondata.grpematiere[x].lib_mat + '</option>');
            }




        }

    }

    function fct_set_selectmoy_classe(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {
            $('#moy_classe').html('<option ></option><option value="" disabled selected>Choisissez la classe</option>');
            jsondata = JSON.parse(jsondata);
            //Date	Groupe	matière	Professeur	Action
            titre = "evenement sur: Fct (fct_set_selectmoy_classe) // ";
            info = "Ajax send  resultat ::  ";
            valueur = jsondata;
            consoleInfo(envi, titre, info, valueur);

            for (var x in jsondata.getAllGrpByannee) {
                $('#moy_classe').append('<option value="' + jsondata.getAllGrpByannee[x].groupe_id + '">' + jsondata.getAllGrpByannee[x].groupe_libelle + '</option>');
            }

            if (typeof($('#moy_classe_multiple').val()) != undefined) {
                $('#moy_classe_multiple').html('');
                for (var x in jsondata.getAllGrpByannee) {
                    $('#moy_classe_multiple').append('<option value="' + jsondata.getAllGrpByannee[x].groupe_id + '">' + jsondata.getAllGrpByannee[x].groupe_libelle + '</option>');
                }
            }




        }

    }

    function ajoutSupElveClasse(params1, params2, params3) {

        values = 'action=setClassesElveve&mode=' + params1 + '&groupe=' + params2 + '&idelev=' + params3 + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();

        $.ajax({
                url: liens + 'App/Models/info.model.php',
                type: 'POST',
                dataType: 'text',
                async: true,
                crossDomain: true,
                data: values,
            })
            .done(function(data) {


                titre = "ajoutSupElveClasse ";
                info = "Ajax ajoutSupElveClasse result";
                valueur = data;
                consoleInfo(envi, titre, info, valueur);

                affich_eleve_attrib_classe_select_classe();


            })
            .fail(function() {});

        affich_eleve_attrib_classe_select_classe();
    }

    function getProfListMajSelect(params) {

        values = 'action=getListProf' + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();

        consoleInfo(envi, "Attribution", " Ajax Send post=  ", values);

        $.ajax({
                url: liens + 'App/Models/info.model.php',
                type: 'POST',
                dataType: 'text',
                async: true,
                crossDomain: true,
                data: values,
            })
            .done(function(data) {

                $('#enseignant_' + params).html('<option value="" disabled selected>Selectioner un professeurs</option>');
                var matierehtml = "";
                consoleInfo(envi, "Attribution", " Ajax Retour  valeur :: data :: = " + data);
                //consoleInfo(envi, "Attribution", " Ajax Retour  JSAON valeur :: data :: = "+JSON.parse(data));


                if (typeof(data) != undefined) {
                    data = JSON.parse(data);

                    for (var x in data.listeProf) {

                        consoleInfo(envi, "Attribution", " Ajax Retour valeur :: getListProf :: = " + data.listeProf[x].nom_prenom);
                        $('#enseignant_' + params).append('<option value="' + data.listeProf[x].id_prof_prof + '">' + data.listeProf[x].nom_prenom + '</option>');
                    }

                }

            })
            .fail(function() {});


    }


    function attriAjoutMatTab(jour) {
        var dataTable;
        switch (jour) {
            case "Lundi":
                dataTable = dataTable_Lundi;
                break;

            default:
                break;
        }

        titre = "(Attribution ::Emploi du temps :: Champ valeur::)  ";
        info = "matiere =(" + $("#attribution_emlploiTps_mat_" + jour + "").val() + ")  heure debut=" + $("#heuredebut_" + jour + "").val() + "  heure fin=" + $("#heurefin_" + jour + "").val() + "  professeur=" + $("#enseignant_" + jour + "").val();
        valueur = event.target.value;
        consoleInfo(envi, titre, info, valueur);

        var rowNode = dataTable
            .row.add([$("#heuredebut_" + jour + "").val(), $("#heurefin_" + jour + "").val(), $("#attribution_emlploiTps_mat_" + jour + " option:selected").text(), $("#enseignant_" + jour + " option:selected").text(), '<a title="Delete Matière" class="btn-default btn-xs purple" href="#"><i class="fa fa-trash"><input type="hidden" value="' + $("#attribution_emlploiTps_mat_" + jour + "").val() + '_"' + $("#enseignant_" + jour + "").val() + '"" ></i></a> '])
            .draw()
            .node();
        $(rowNode)
            .css('color', 'green')
            .animate({ color: 'black' });

        values = 'action=setEmploiTps&emploitps_jour=' + jour + '&emploitps_id_mat=' + $("#attribution_emlploiTps_mat_" + jour).val() + '&emploitps_id_prof=' + $("#enseignant_" + jour).val() + '&emploitps_h_debut=' + $("#heuredebut_" + jour + "").val() + '&emploitps_h_fin=' + $("#heurefin_" + jour + "").val() + '&emploitps_groupe_id=' + form_attribution_info.groupe_id + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();

        // emploitps_id 	emploitps_jour 	emploitps_id_mat 	emploitps_id_prof 	emploitps_h_debut 	emploitps_h_fin 	emploitps_groupe_id 	emploitps_etat 
        $.ajax({
                url: liens + 'App/Models/info.model.php',
                type: 'POST',
                dataType: 'text',
                async: true,
                crossDomain: true,
                data: values,
            })
            .done(function(data) {

                if (typeof(data) != undefined && data != "") {
                    data = JSON.parse(data);
                }


            })
            .fail(function() {});



    }

    // FUNCTION MISE A JOUR PAGE ATTRIBUTION ETAP 2
    function attrribution_etap2_html(semestre_livelle, semestreid) {
        var divhtml = '<div class="card shadow mb-4" id="attri_sem_' + semestreid + '"><a href="#collapse_sem_' + semestreid + '" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="#collapse_sem_' + semestreid + '"><h6 class="m-0 font-weight-bold text-primary">' + semestre_livelle + '</h6></a><div class="collapse show" id="#collapse_sem_' + semestreid + '" ><div class="card-body"><div class="row"><div class="col-xl-12 col-lg-12">  <div class="card shadow mb-4"><div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6 class="m-0 font-weight-bold text-primary">Ajout de Matière</h6></div><div class="card-body">   <div class="table-responsive" style="font-size: 0.7em;"><table class="table table-bordered" id="attribution_part_an_Allclasse_' + semestreid + '" style="width:100%" cellspacing="0" ><thead><tr><th >Code Matière</th><th >Nom Matière</th><th >Classe</th><th>Coefficient</th><th>Choisir</th><th>Sous matières</th></tr></thead><tfoot><tr><th >Code Matière</th><th >Nom Matière</th><th >Classe</th><th>Coefficient</th><th>Choisir</th><th>Sous matières</th></tr></tfoot> <tbody id="tbody_' + semestreid + '"></tbody> </table>   </div> </div></div> </div>  </div></div></div></div>';
        return divhtml;
    }



    function affich_eleve_attrib_classe_select_classe() {

        if ($('#eleve_attrib_classe_select_classe').val() != 0) {

            eleve_attrib_classe_select_classe_val = $('#eleve_attrib_classe_select_classe').val();

            values = 'action=getGrpElve&annee_id=' + $('#eleve_attrib_classe_select_annee').val() + '&classe_id=' + eleve_attrib_classe_select_classe_val + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();

            $.ajax({
                    url: liens + 'App/Models/info.model.php',
                    type: 'POST',
                    dataType: 'text',
                    async: true,
                    crossDomain: true,
                    data: values,
                })
                .done(function(data) {

                    if (typeof(data) != undefined && data != "") {

                        data = JSON.parse(data);

                        $('#eleve_attrib_classe_ListElvClss').html('');
                        if (typeof(data.getGrpElve) != undefined && data.getGrpElve != "") {

                            for (var x in data.getGrpElve) {
                                $('#eleve_attrib_classe_ListElvClss').append('<tr><td>' + data.getGrpElve[x].matricule + '</td><td>' + data.getGrpElve[x].nom_prenom + '</td><td>' + data.getGrpElve[x].sexe + '</td><td>' + data.getGrpElve[x].contact + '</td><td><a  title="Retirer l Eleve" class="btn-danger btn-xs purple " href="#"><i class="fa fa-window-close sup_ElevGroup" value="sup_ElevGroup" id="' + data.getGrpElve[x].id_eleve_eleve + '_' + $("#eleve_attrib_classe_select_classe").val() + '"></i> </a></td></tr>');

                                consoleInfo(envi, "Elve classe Attribution", " Ajax Retour  valeur :: getGrpElve :: = " + data.getGrpElve);
                            }
                        }

                        $('#eleve_attrib_classe_ListElvSansClss').html('');
                        if (typeof(data.getGrpNotElve) != undefined && data.getGrpNotElve != "") {

                            for (var y in data.getGrpNotElve) {
                                $('#eleve_attrib_classe_ListElvSansClss').append('<tr><td>' + data.getGrpNotElve[y].matricule + '</td><td>' + data.getGrpNotElve[y].nom_prenom + '</td><td>' + data.getGrpNotElve[y].sexe + '</td><td>' + data.getGrpNotElve[y].contact + '</td><td><a  title="Ajouter Eleve" class="btn-default btn-xs purple " href="#"><i class="fa fa-plus add_ElevGroup" value="add_ElevGroup" id="' + data.getGrpNotElve[y].id_eleve_eleve + '_' + $("#eleve_attrib_classe_select_classe").val() + '"></i> </a></td></tr>');

                                consoleInfo(envi, "Elve classe Attribution", " Ajax Retour  valeur :: getGrpNotElve :: = " + data.getGrpNotElve);


                            }
                        }

                    }

                })
                .fail(function() {});



        } else {

            alert("Veuillez choisir une classe");



        }

    }


    function ajax_attribClasse_elv_majgrpSelect() {
        result = $('#eleve_attrib_classe_select_annee').val();
        values = 'action=getAllGrpByannee&annee_id=' + result + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();

        $.ajax({
                url: liens + 'App/Models/info.model.php',
                type: 'POST',
                dataType: 'text',
                async: true,
                crossDomain: true,
                data: values,
            })
            .done(function(data) {




                //consoleInfo(envi, "Attribution", " Ajax Retour  valeur :: data :: = "+data);
                //consoleInfo(envi, "Attribution", " Ajax Retour  JSAON valeur :: data :: = "+JSON.parse(data));

                if (typeof(data) != undefined) {
                    data = JSON.parse(data);
                    $('#eleve_attrib_classe_select_classe').html('<option value="" disabled selected>Choisissez une classe</option>');

                    for (var x in data.getAllGrpByannee) {
                        $('#eleve_attrib_classe_select_classe').append('<option value="' + data.getAllGrpByannee[x].groupe_id + '">' + data.getAllGrpByannee[x].groupe_libelle + '</option>');
                    }

                }

            })
            .fail(function() {});

    }

    function fct_tabSet_eval_prog(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {
            $('#eval_prog_table_tbody').html("");
            jsondata = JSON.parse(jsondata);
            //Date	Groupe	matière	Professeur	Action
            titre = "evenement sur: Fct (fct_tabSet_eval_prog) // ";
            info = "Ajax send  resultat ::  ";
            valueur = jsondata;
            consoleInfo(envi, titre, info, valueur);

            for (var x in jsondata.get_eval_prog) {

                var rowNode = eval_prog_table
                    .row.add([jsondata.get_eval_prog[x].eval_libelle, jsondata.get_eval_prog[x].date_creation_eval, jsondata.get_eval_prog[x].groupe_libelle, jsondata.get_eval_prog[x].code + '-' + jsondata.get_eval_prog[x].libele, jsondata.get_eval_prog[x].nom_prenom, jsondata.get_eval_prog[x].contact, '<!-- :: BUTTON ::--> <button option="btn_evalProg_click" type="button" class="btn btn-primary btn_prgoEval" id="btnprogEvalid_' + jsondata.get_eval_prog[x].prof_eval_id + '" data-toggle="modal" data-target="#progEval_modal" value="' + jsondata.get_eval_prog[x].date_creation_eval + '_' + jsondata.get_eval_prog[x].groupe_libelle + '_' + jsondata.get_eval_prog[x].code + '_' + jsondata.get_eval_prog[x].libele + '_' + jsondata.get_eval_prog[x].nom_prenom + '_' + jsondata.get_eval_prog[x].contact + '_' + jsondata.get_eval_prog[x].eval_date + '_' + jsondata.get_eval_prog[x].eval_hDebut + '_' + jsondata.get_eval_prog[x].eval_hFin + '_' + jsondata.get_eval_prog[x].coef + '_' + jsondata.get_eval_prog[x].notation + '">Programmer</button><!-- :: BUTTON ::--> '])
                    .draw()
                    .node();

                $(rowNode)
                    .css('color', '#858796')
                    .animate({ color: '#858796' });
            }




        }

    }

    function listMatBygrpPartanne() {

        if (typeof($('#attribution_emlploiTps_periode').val()) != undefined && $('#attribution_emlploiTps_periode').val() != 0 && typeof($('#attribution_emlploiTps_groupe').val()) != undefined && $('#attribution_emlploiTps_groupe').val() != 0) {

            values = 'action=attribution_emlploiTps_matiere&part_id=' + $('#attribution_emlploiTps_periode').val() + '&grpe_id=' + $('#attribution_emlploiTps_groupe').val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();

            $.ajax({
                    url: liens + 'App/Models/info.model.php',
                    type: 'POST',
                    dataType: 'text',
                    data: values,
                })
                .done(function(data) {

                    //consoleInfo(envi, "Attribution", " Ajax Retour  valeur :: data :: = "+data);
                    //consoleInfo(envi, "Attribution", " Ajax Retour  JSAON valeur :: data :: = "+JSON.parse(data));

                    if (typeof(data) != undefined) {
                        data = JSON.parse(data);
                        $('#attribution_emlploiTps_mat').html('<option value="" disabled selected>Veuillez choisir la matière</option>');

                        for (var x in data.getMatiereByPartGrp) {
                            $('#attribution_emlploiTps_mat').append('<option value="' + data.getMatiereByPartGrp[x].id_matiere_matiere + '">' + data.getMatiereByPartGrp[x].libele + '</option>');
                        }
                    }

                })
                .fail(function() {});


        }

    }

    function fct_tabSet_classemat(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {

            $('#dataTable_classe_classMat_tbody').html("");
            dataTable_classe_classMat.clear().draw();
            jsondata = JSON.parse(jsondata);
            //Date	Groupe	matière	Professeur	Action
            titre = "evenement sur: Fct (fct_tabSet_classemat) // ";
            info = "Ajax send  resultat ::  ";
            valueur = jsondata;
            consoleInfo(envi, titre, info, valueur);

            for (var x in jsondata.classematiere) {

                var rowNode = dataTable_classe_classMat
                    .row.add([jsondata.classematiere[x].code, jsondata.classematiere[x].libele, '<a title="Delete Matière" class="btn-default btn-xs purple" href="#"><i class="fa fa-trash" style="font-size: x-large;"><input type="hidden" value="' + jsondata.classematiere[x].libele + '" name="classmat_' + jsondata.classematiere[x].id_matiere + '_' + jsondata.classematiere[x].code + '" ></i></a> '])
                    .draw()
                    .node();

                $(rowNode)
                    .css('color', '#858796')
                    .animate({ color: '#858796' });
            }

        }

    }

    function fct_get_allFilliere(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {

            $('#notif_fillieres').html('<option selected="" disabled="" value="">-- Sélectionner la Fillère --</option>');

            jsondata = JSON.parse(jsondata);

            titre = "evenement sur: Fct (fct_get_allFilliere) // ";
            info = "Ajax send  resultat ::  ";
            valueur = jsondata;
            consoleInfo(envi, titre, info, valueur);

            for (var x in jsondata.get_allFilliere) {
                $('#notif_fillieres').append('<option value="' + jsondata.get_allFilliere[x].id_classe_classe + '">' + jsondata.get_allFilliere[x].libelle + '</option>');

            }

        }

    }



    function fct_get_allProf(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {

            notif_hide_tab();

            $("#section-professeur").removeAttr("style");
            $("#section-professeur").attr("style", " display: flex;");

            jsondata = JSON.parse(jsondata);

            titre = "evenement sur: Fct (fct_get_allGroupe_active) // ";
            info = "Ajax send  resultat ::  ";
            valueur = jsondata;
            consoleInfo(envi, titre, info, valueur);

            for (var x in jsondata.listeProf) {
                $('#section-professeur tbody').append('<tr><td> <input class="input_prof_chek" name ="input_prof_chek[]" type="checkbox" style=" width: 20px;height: 20px;" value="' + jsondata.listeProf[x].id_prof_prof + '"> </td><td> ' + jsondata.listeProf[x].nom_prenom + '</td><td> ' + jsondata.listeProf[x].email + '</td><td> ' + jsondata.listeProf[x].contact + '</td></tr>');
            }

        }

    }

    function fct_get_admin(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {
            notif_hide_tab();

            $("#section-admin").removeAttr("style");
            $("#section-admin").attr("style", " display: flex;");

            jsondata = JSON.parse(jsondata);

            titre = "evenement sur: Fct (fct_get_allGroupe_active) // ";
            info = "Ajax send  resultat ::  ";
            valueur = jsondata;
            consoleInfo(envi, titre, info, valueur);

            for (var x in jsondata.listeProf) {
                $('#section-admin tbody').append('<tr><td> <input class="input_prof_chek" name ="input_admin_chek[]" type="checkbox" style=" width: 20px;height: 20px;" value="' + jsondata.listeProf[x].id_admin_admin + '"> </td><td> ' + jsondata.listeProf[x].nom_prenom + '</td><td> ' + jsondata.listeProf[x].email + '</td><td> ' + jsondata.listeProf[x].contact + '</td></tr>');
            }

        }

    }

    function notif_hide_tab() {
        $("#section-eleves").attr("style", " display: none;");
        $("#section-professeur").attr("style", " display: none;");
        $("#section-admin").attr("style", " display: none;");

        $('#section-professeur tbody').html('');
        $('#section-eleves tbody').html('');
        $('#section-admin tbody').html('');
    }


    function fct_get_allEleveInGrpe(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {

            $("#section-eleves").removeAttr("style");
            $("#section-professeur").attr("style", " display: none;");
            $("#section-eleves").attr("style", " display: flex;");

            $('#section-eleves tbody').html('');

            jsondata = JSON.parse(jsondata);

            titre = "evenement sur: Fct (fct_get_allGroupe_active) // ";
            info = "Ajax send  resultat ::  ";
            valueur = jsondata;
            consoleInfo(envi, titre, info, valueur);

            for (var x in jsondata.getAllElevByGrp) {
                $('#section-eleves tbody').append('<tr><td> <input class="input_eleve_chek" name ="input_eleve_chek[]" type="checkbox" style=" width: 20px;height: 20px;" value="' + jsondata.getAllElevByGrp[x].id_eleve_eleve + '"> </td> <td> ' + jsondata.getAllElevByGrp[x].matricule + '</td> <td> ' + jsondata.getAllElevByGrp[x].nom_prenom + '</td> </td> <td> ' + jsondata.getAllElevByGrp[x].date_naiss + ' à ' + jsondata.getAllElevByGrp[x].lieu_naiss + '</td> <td> ' + jsondata.getAllElevByGrp[x].email + '</td><td> ' + jsondata.getAllElevByGrp[x].contact + '</td></tr>');
            }

        }

    }


    /**
     * INITIALISATION
     **/

    consoleInfo(envi, "Infos Liens", "host = " + window.location.hostname, liens);

    $("select").select2();

    ajax_attribClasse_elv_majgrpSelect();

    /**
     * ACTIONS
     **/


    //$('select').selectize({ sortField: 'text' });

    var form_attribution_info = {

        affiche: function() {
            //return this.firstName + " " + this.lastName;
            for (var x in this) {
                consoleInfo("debug", "form_attribution_info", x + "  Valeur =", form_attribution_info[x]);
            }
        }

    };


    //::attribution :: Button Suivant
    $('#btn_creer_groupe').on('click', function(event) {
        if (typeof($('#attribution_annee_id').val()) != undefined && typeof($('#attribution_classe_id').val()) != undefined && typeof($('#attribution_id_niveau').val()) != undefined && typeof($('#attribution_groupe_nom').val()) != undefined && $('#attribution_annee_id').val() != "" && $('#attribution_classe_id').val() != "" && $('#attribution_id_niveau').val() != "" && $('#attribution_groupe_nom').val() != "") {

            values = 'action=setGroupe&annee_id=' + $('#attribution_annee_id').val() + '&classe_id=' + $('#attribution_classe_id').val() + '&nomgroupe=' + $('#attribution_groupe_nom').val() + '&niveaugroupe=' + $('#attribution_id_niveau').val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();

            $.ajax({
                    url: liens + 'App/Models/info.model.php',
                    type: 'POST',
                    dataType: 'text',
                    async: true,
                    crossDomain: true,
                    data: values,
                })
                .done(function(data) {

                    window.location.reload();
                    //consoleInfo(envi, "Attribution", " Ajax Retour  valeur :: data :: = "+data);
                    //consoleInfo(envi, "Attribution", " Ajax Retour  JSAON valeur :: data :: = "+JSON.parse(data));


                })
                .fail(function() {});

        } else {
            event.preventDefault();
            infos_stat = "Veuillez remplir tous les champs !";
            classtype = "alert-danger";
            root_fct_showinfos_toast(infos_stat, classtype);
        }


    });



    $('#bt_ajout_mat_attrib_Lundi').on('click', function(event) {

        titre = "(Attribution ::Emploi du temps :: ajout matiere::) evenement sur: " + event.target;
        info = "id =" + event.target.id + "  Class=" + event.target.class;
        valueur = event.target.value;
        consoleInfo(envi, titre, info, valueur);
        attriAjoutMatTab("Lundi");
    });

    //  BTN ajout GROUPE matiere à semestre
    $('#btn_attrib_etap2_ajout_mat').on('click', function(event) {

        if ($('#attrib_etap2_partannee_id').val() == 0 || $('#attrib_etap2_partannee_id').val() == "" || $('#attrib_etap2_mat_id').val() == "" || $('#attrib_etap2_mat_id').val() == "" || $('#attrib_etap2_mat_coef').val() == 0 || $('#attrib_etap2_mat_coef').val() == "") {

            alert("Veuillez remplir tous les champs nécessaire !!");

        } else {


            values = 'action=setGroupeMat&groupe_id=' + $('#input_grpid').val() + '&part_annee=' + $('#attrib_etap2_partannee_id').val() + '&matiere=' + $('#attrib_etap2_mat_id').val() + '&mat_coef=' + $('#attrib_etap2_mat_coef').val() + '&mat_credit=' + $('#attrib_etap2_mat_credit').val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();

            if ($('#attrib_etap2_mats1_id').val() != 0) {
                values = values + '&sous_mat1=' + $('#attrib_etap2_mats1_id').val();
            }


            consoleInfo(envi, "attribution ajout matiere", "values ", values);



            $.ajax({
                    url: liens + 'App/Models/info.model.php',
                    type: 'POST',
                    dataType: 'text',
                    async: true,
                    crossDomain: true,
                    data: values,
                })
                .done(function(data) {

                    //data= JSON.parse(data);
                    consoleInfo(envi, "attribution ajout matiere", "Ajax result ", data.setGroupeMat);
                    location.reload();


                })
                .fail(function() {});



        }
    });


    $('.modif_doc_rev').on('click', function(event) {

        console.log("***** Active/Delete compte   click *****");
        $('#myModal_fairepatientez').modal('show');

        values = '';
        //var result = $('.modif_doc_rev').serialize();
        result = event.target.value;
        values = result.split("_");

        consoleInfo(envi, "modif_doc_rev", result, values);

        identifiant_role = 'admin_role_' + values[0];

        if (((typeof($('#' + identifiant_role).val()) != undefined && $('#' + identifiant_role).val() != 0 && $('#' + identifiant_role).val() != null && $('#' + identifiant_role).val() != "" && values[2] == 4) || values[3] == 0) && values[2] == 4) {

            if (values[3] == 0) { id_role = 0; } else { id_role = $('#' + identifiant_role).val(); }
            consoleInfo(envi, "id role  =", id_role);

            values = 'idpers=' + values[0] + '&idtype=' + values[1] + '&type=' + values[2] + '&mode=' + values[3] + '&id_role=' + id_role + '&action=activecpte' + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();

            consoleInfo(envi, "modif_doc_rev", "Envoi ajax infos id role  =" + id_role, values);

            $.ajax({
                    url: liens + 'App/Models/info.model.php',
                    type: 'POST',
                    dataType: 'text',
                    async: true,
                    crossDomain: true,
                    data: values,
                })
                .done(function(data) {

                    consoleInfo(envi, "modif_doc_rev", "Resultats ajax  =", data);
                    if (data == 1) {
                        consoleInfo(envi, "modif_doc_rev", "Mise a jour etat = effectue", "1");
                    }
                    $('#myModal_fairepatientez').modal('hide');
                    window.location.reload();
                })
                .fail(function() {
                    $('#myModal_fairepatientez').modal('hide');
                    alert("Erreur ! Lors de l'activation du compte");
                    consoleInfo(envi, "modif_doc_rev", "Erreur ! Lors de l'activation du compte", "0");
                    window.location.reload();
                });
        } else {
            consoleInfo(envi, "modif_doc_rev", "id role absente rôle =", values[2]);
            //alert(values[2]);
            if (values[2] == 4) {
                $("#myModal_fairepatientez").modal("hide");
                alert('Veuillez choisir le rôle');
                window.location.reload();
                return;
            } else {
                id_role = 0;
                values = 'idpers=' + values[0] + '&idtype=' + values[1] + '&type=' + values[2] + '&mode=' + values[3] + '&id_role=' + id_role + '&action=activecpte' + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();;

                consoleInfo(envi, "modif_doc_rev", "AJAX Infos à envoyer (Compte non admin)", values);

                $.ajax({
                        url: liens + 'App/Models/info.model.php',
                        type: 'POST',
                        dataType: 'text',
                        async: true,
                        crossDomain: true,
                        data: values,
                    })
                    .done(function(data) {
                        //alert('data='+data);
                        if (data == 1) {}

                        $('#myModal_fairepatientez').modal('hide');
                        consoleInfo(envi, "modif_doc_rev", "AJAX Resultat", data);
                        window.location.reload();

                    })
                    .fail(function() {

                        $('#myModal_fairepatientez').modal('hide');
                        alert("Erreur Lors de l'activation du compte veuillez réessayer ! ");
                        window.location.reload();

                    });
            }

        }


    });

    $('.eleve_matricule').on('change', function(event) {

        titre = "//evenement Change sur: #eleve_matricule";
        info = "  // Valeur=id --> " + event.target.id;
        valueur = $('#' + event.target.id).val();
        consoleInfo(envi, titre, info, valueur);
        url_info = liens + 'App/Models/info.model.php';
        values_info = 'action=update_elevMatr&id_eleve=' + event.target.id + '&matricule=' + $('#' + event.target.id).val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();
        idelemt = '#' + event.target.id;

        root_AjaxMethod(url_info, values_info, ajax_result_toast, idelemt);

    });

    $('.tmp_eleve_filliere').on('change', function(event) {

        titre = "//evenement Change sur: #tmp_eleve_filliere";
        info = "  // Valeur=id --> " + event.target.id;
        valueur = $('#' + event.target.id).val();
        consoleInfo(envi, titre, info, valueur);
        url_info = liens + 'App/Models/info.model.php';
        //{{info.id_eleve_eleve}}_{{value.groupe_id}}
        values = valueur.split("_");
        values_info = 'action=setUpdate_elevGrp&id_eleve=' + values[0] + '&idgroupe=' + values[1] + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();
        idelemt = '#' + event.target.id;
        root_AjaxMethod(url_info, values_info, ajax_result_toast, idelemt);

    });



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


    $('#admin_creerAnnee_listPart_select').on('change', function(event) {
        $('#admin_creerAnnee_listPart').html("");
        htmlinfo = "";
        //var result = $('#admin_creerAnnee_listPart_select').serialize();
        var result = $('#admin_creerAnnee_listPart_select').val();
        consoleInfo(envi, "#admin_creerAnnee_listPart_select", "change", result);
        //var values = $('.modif_doc_rev').val();
        //alert( values );
        for (var index = 0; index < result; index++) {

            htmlinfo += "<div class='form-group form-inline'><label >Titre Année Scolaire Partie " + (index + 1) + " : &nbsp; </label><div class='form-group'><div class='input-group date'><input type='text' name='cree_anne_scol_Part" + (index + 1) + "' placeholder='Ex: Semestre " + (index + 1) + "' class='form-control' required/> </div></div></div><div class='form-group form-inline'><label >Début P" + (index + 1) + " : &nbsp; </label><div class='form-group'><div class='input-group date'><input type='date' name='cree_anne_scol_Part" + (index + 1) + "_dateDebut' class='form-control' required/></div></div><label >&nbsp;Fin P" + (index + 1) + " : &nbsp; </label> <div class='form-group'><div class='input-group date'><input type='date' name='cree_anne_scol_Part" + (index + 1) + "_dateFin' class='form-control' required/></div></div></div><hr>";

        }


        $('#admin_creerAnnee_listPart').html(htmlinfo);
    });

    $('#attribution_annee_id').on('change', function(event) {
        $('#admin_creerAnnee_listPart').html("");
        htmlinfo = "";
        //var result = $('#admin_creerAnnee_listPart_select').serialize();
        var result = $('#admin_creerAnnee_listPart_select').val();
        consoleInfo(envi, "#admin_creerAnnee_listPart_select", "change", result);
        //var values = $('.modif_doc_rev').val();
        //alert( values );
        for (var index = 0; index < result; index++) {

            htmlinfo += "<div class='form-group form-inline'><label >Titre Année Scolaire Partie " + (index + 1) + " : &nbsp; </label><div class='form-group'><div class='input-group date'><input type='text' name='cree_anne_scol_Part" + (index + 1) + "' placeholder='Ex: Semestre " + (index + 1) + "' class='form-control' required/> </div></div></div><div class='form-group form-inline'><label >Début P" + (index + 1) + " : &nbsp; </label><div class='form-group'><div class='input-group date'><input type='date' name='cree_anne_scol_Part" + (index + 1) + "_dateDebut' class='form-control' required/></div></div><label >&nbsp;Fin P" + (index + 1) + " : &nbsp; </label> <div class='form-group'><div class='input-group date'><input type='date' name='cree_anne_scol_Part" + (index + 1) + "_dateFin' class='form-control' required/></div></div></div><hr>";

        }


        $('#admin_creerAnnee_listPart').html(htmlinfo);
    });

    $('#attribution_classe_id').on('change', function(event) {
        $('#admin_creerAnnee_listPart').html("");
        htmlinfo = "";
        //var result = $('#admin_creerAnnee_listPart_select').serialize();
        var result = $('#admin_creerAnnee_listPart_select').val();
        consoleInfo(envi, "#admin_creerAnnee_listPart_select", "change", result);
        //var values = $('.modif_doc_rev').val();
        //alert( values );
        for (var index = 0; index < result; index++) {

            htmlinfo += "<div class='form-group form-inline'><label >Titre Année Scolaire Partie " + (index + 1) + " : &nbsp; </label><div class='form-group'><div class='input-group date'><input type='text' name='cree_anne_scol_Part" + (index + 1) + "' placeholder='Ex: Semestre " + (index + 1) + "' class='form-control' required/> </div></div></div><div class='form-group form-inline'><label >Début P" + (index + 1) + " : &nbsp; </label><div class='form-group'><div class='input-group date'><input type='date' name='cree_anne_scol_Part" + (index + 1) + "_dateDebut' class='form-control' required/></div></div><label >&nbsp;Fin P" + (index + 1) + " : &nbsp; </label> <div class='form-group'><div class='input-group date'><input type='date' name='cree_anne_scol_Part" + (index + 1) + "_dateFin' class='form-control' required/></div></div></div><hr>";

        }


        $('#admin_creerAnnee_listPart').html(htmlinfo);
    });


    //BOUTON CREATION DEMATIERE
    $('.cree_matiere_btn_add').on('click', function(event) {

        //var result = $('#'+event.target.id+'').attr("value");
        //DOC_12%3B2
        //values = result.split("/_/");
        //alert("ok");
        event.preventDefault();
        titre = "EVENT :: click cree_matiere_btn_add";
        info = "event sur = " + event.target;
        valueur = "id =" + event.target.id + " && valeur =" + $('#' + event.target.id + '').attr("value");
        consoleInfo(envi, titre, info, valueur);
        /*
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

    $('#dataTable_classe_classMat tbody').on('click', 'tr td a', function() {
        dataTable_classe_classMat
            .row($(this).parents('tr'))
            .remove()
            .draw();
    });

    $('#dataTable_classe_list_annee tbody').on('click', 'tr td a', function() {

        $(this).parents('tr')
            .css('color', 'red');
    });

    //::::MENU ADMIN ELEVES::::::::::

    $('#eleve_attrib_classe_select_annee').on('change', function(event) {

        ajax_attribClasse_elv_majgrpSelect();

    });

    $('#eleve_attrib_classe_select_classe').on('change', function(event) {

        affich_eleve_attrib_classe_select_classe();

    });



    //::::MENU GESTION DES CLASSES::::::::::
    $('#attribution_emlploiTps_anneescolaire').on('change', function(event) {

        if (typeof($('#attribution_emlploiTps_anneescolaire').val()) != undefined && $('#attribution_emlploiTps_anneescolaire').val() != 0 && $('#attribution_emlploiTps_anneescolaire').val() != "") {

            values = 'action=attribution_emlploiTps_getPeriode&annee_id=' + $('#attribution_emlploiTps_anneescolaire').val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();

            $.ajax({
                    url: liens + 'App/Models/info.model.php',
                    type: 'POST',
                    dataType: 'text',
                    data: values,
                })
                .done(function(data) {

                    //consoleInfo(envi, "Attribution", " Ajax Retour  valeur :: data :: = "+data);
                    //consoleInfo(envi, "Attribution", " Ajax Retour  JSAON valeur :: data :: = "+JSON.parse(data));

                    if (typeof(data) != undefined) {

                        data = JSON.parse(data);

                        $('#attribution_emlploiTps_periode').html('<option value="" disabled selected>Veuillez choisir la période</option><option value="all" >Toute période</option>');
                        $('#attribution_emlploiTps_groupe').html('<option value="" disabled selected>Veuillez choisir la Classe</option>');
                        $('#dataTable_attrib_emptpsByAn_tbody').html('');

                        //$('#mod_grpe').html('');
                        $('#mod_periode').html('');

                        for (var x in data.getAllpartAnneeBy) {
                            $('#attribution_emlploiTps_periode').append('<option value="' + data.getAllpartAnneeBy[x].id_annee_partie + '">' + data.getAllpartAnneeBy[x].libele_partie + '</option>');
                            $('#mod_periode').append('<option value="' + data.getAllpartAnneeBy[x].id_annee_partie + '">' + data.getAllpartAnneeBy[x].libele_partie + '</option>');
                        }

                        for (var y in data.getAllGrpBy) {
                            $('#attribution_emlploiTps_groupe').append('<option value="' + data.getAllGrpBy[y].groupe_id + '">' + data.getAllGrpBy[y].groupe_libelle + '</option>');
                            //$('#mod_grpe').append('<option value="' + data.getAllGrpBy[y].groupe_id + '">' + data.getAllGrpBy[y].groupe_libelle + '</option>');
                        }


                    }

                })
                .fail(function() {});


        }




    });

    $('#attribution_emlploiTps_periode').on('change', function(event) {
        listMatBygrpPartanne();

    });

    $('#attribution_emlploiTps_groupe').on('change', function(event) {
        listMatBygrpPartanne();

        if (typeof($('#attribution_emlploiTps_groupe').val()) != undefined && $('#attribution_emlploiTps_groupe').val() != 0 && $('#attribution_emlploiTps_groupe').val() != "") {

            //fct_attrib_eploiTps_groupe();
            fct_attrib_eploiTps_groupe_NEW();


        }

    });

    $('#prog_groupe').on('change', function(event) {

        if (typeof($('#prog_groupe').val()) != undefined && $('#prog_groupe').val() != 0 && $('#prog_groupe').val() != "") {
            fct_attrib_eploiTps_groupe_NEW();
        }

    });
    $('#select_filtres_anneescol').on('change', function(event) {

        if (typeof($('#select_filtres_anneescol').val()) != undefined && $('#select_filtres_anneescol').val() != 0 && $('#select_filtres_anneescol').val() != "") {
            fct_attrib_eploiTps_allgroupe();
        }

    });


    function fct_attrib_eploiTps_groupe() {

        values = 'action=attribution_emlploiTps_getProg&id_groupe=' + $('#attribution_emlploiTps_groupe').val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();

        $.ajax({
                url: liens + 'App/Models/info.model.php',
                type: 'POST',
                dataType: 'text',
                data: values,
            })
            .done(function(data) {

                if (typeof(data) != undefined) {

                    data = JSON.parse(data);
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
                    dataTable_attrib_emptpsByAn.clear().draw();

                    for (var z in data.getEmploiTpsBy) {


                        salle = "(" + data.getEmploiTpsBy[z].Code_salle + ")-" + data.getEmploiTpsBy[z].salle_libelle;
                        dateold = new Date(data.getEmploiTpsBy[z].emploitps_date);
                        console.log('data.getEmploiTpsBy[z].emploitps_date ==', data.getEmploiTpsBy[z].emploitps_date);
                        console.log('dateold== ', dateold);
                        datefor = dateold.toLocaleDateString('fr-FR', options);
                        console.log('datefor== ', datefor);
                        arraydate = datefor.split(" ");
                        console.log('arraydate== ', arraydate);
                        //data.getEmploiTpsBy[z].emploitps_date

                        var btn_etps_action = '<button type="button" class="btn btn-outline-info btn_etps_mod" data-toggle="modal" data-target="#mod_mod_eTps" option="mod_emploitps_btn" name="etpprog_' + data.getEmploiTpsBy[z].emploitps_id + '" id="etpprog_' + data.getEmploiTpsBy[z].emploitps_id + '" value="' + data.getEmploiTpsBy[z].groupe_libelle + '||' + data.getEmploiTpsBy[z].libele_partie + '||' + data.getEmploiTpsBy[z].emploitps_date + '||' + data.getEmploiTpsBy[z].mat_libelle + '|| (' + data.getEmploiTpsBy[z].Code_salle + ')' + data.getEmploiTpsBy[z].salle_libelle + '||' + data.getEmploiTpsBy[z].emploitps_h_debut + '||' + data.getEmploiTpsBy[z].emploitps_h_fin + '"><i class="fa fa-edit"></i> Modifier</button>    <br> <br>  <button type="button" class="btn btn-outline-danger" value="mod_emploitps_btn_sup" option="mod_emploitps_btn_sup" id="eTpsSup_' + data.getEmploiTpsBy[z].emploitps_id + '">   <i class="fa fa-trash"></i> Supprimer</button>';


                        var rowNode = dataTable_attrib_emptpsByAn
                            .row.add([arraydate[1] + '-' + arraydate[0], arraydate[2] + ' ' + arraydate[3], data.getEmploiTpsBy[z].emploitps_h_debut + ' - ' + data.getEmploiTpsBy[z].emploitps_h_fin, data.getEmploiTpsBy[z].libele_partie, data.getEmploiTpsBy[z].groupe_libelle, data.getEmploiTpsBy[z].mat_libelle, salle, data.getEmploiTpsBy[z].nom_prenom, btn_etps_action])
                            .draw()
                            .node();

                    }



                }

            })
            .fail(function() {});

    }

    function fct_attrib_eploiTps_allgroupe() {

        values = 'action=attribution_emlploiTps_getProgall&id_anneescol=' + $('#select_filtres_anneescol').val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();

        $.ajax({
                url: liens + 'App/Models/info.model.php',
                type: 'POST',
                dataType: 'text',
                data: values,
            })
            .done(function(data) {

                if (typeof(data) != undefined) {

                    data = JSON.parse(data);
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
                    dtbe_listall.clear().draw();

                    for (var z in data.getEmploiTpsBy) {


                        salle = "(" + data.getEmploiTpsBy[z].Code_salle + ")-" + data.getEmploiTpsBy[z].salle_libelle;
                        dateold = new Date(data.getEmploiTpsBy[z].emploitps_date);
                        console.log('data.getEmploiTpsBy[z].emploitps_date ==', data.getEmploiTpsBy[z].emploitps_date);
                        console.log('dateold== ', dateold);
                        datefor = dateold.toLocaleDateString('fr-FR', options);
                        console.log('datefor== ', datefor);
                        arraydate = datefor.split(" ");
                        console.log('arraydate== ', arraydate);
                        //data.getEmploiTpsBy[z].emploitps_date

                        var btn_etps_action = '<button type="button" class="btn btn-outline-info btn_etps_mod" data-toggle="modal" data-target="#mod_mod_eTps" option="mod_emploitps_btn" name="etpprog_' + data.getEmploiTpsBy[z].emploitps_id + '" id="etpprog_' + data.getEmploiTpsBy[z].emploitps_id + '" value="' + data.getEmploiTpsBy[z].groupe_libelle + '||' + data.getEmploiTpsBy[z].libele_partie + '||' + data.getEmploiTpsBy[z].emploitps_date + '||' + data.getEmploiTpsBy[z].mat_libelle + '|| (' + data.getEmploiTpsBy[z].Code_salle + ')' + data.getEmploiTpsBy[z].salle_libelle + '||' + data.getEmploiTpsBy[z].emploitps_h_debut + '||' + data.getEmploiTpsBy[z].emploitps_h_fin + '"><i class="fa fa-edit"></i> Modifier</button>    <br> <br>  <button type="button" class="btn btn-outline-danger" value="mod_emploitps_btn_sup" option="mod_emploitps_btn_sup" id="eTpsSup_' + data.getEmploiTpsBy[z].emploitps_id + '">   <i class="fa fa-trash"></i> Supprimer</button>';


                        var rowNode = dtbe_listall
                            .row.add([arraydate[1] + '-' + arraydate[0], arraydate[2] + ' ' + arraydate[3], data.getEmploiTpsBy[z].emploitps_h_debut + ' - ' + data.getEmploiTpsBy[z].emploitps_h_fin, data.getEmploiTpsBy[z].libele_partie, data.getEmploiTpsBy[z].groupe_libelle, data.getEmploiTpsBy[z].mat_libelle, salle, data.getEmploiTpsBy[z].nom_prenom, btn_etps_action])
                            .draw()
                            .node();
                    }



                }

            })
            .fail(function() {});

    }

    function fct_attrib_eploiTps_groupe_NEW() {

        values = 'action=attribution_emlploiTps_getProg&id_groupe=' + $('#prog_groupe').val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();

        $.ajax({
                url: liens + 'App/Models/info.model.php',
                type: 'POST',
                dataType: 'text',
                data: values,
            })
            .done(function(data) {

                if (typeof(data) != undefined) {

                    data = JSON.parse(data);
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
                    dataTable_attrib_emptpsByAn.clear().draw();

                    for (var z in data.getEmploiTpsBy) {


                        salle = "(" + data.getEmploiTpsBy[z].Code_salle + ")-" + data.getEmploiTpsBy[z].salle_libelle;
                        dateold = new Date(data.getEmploiTpsBy[z].emploitps_date);
                        console.log('data.getEmploiTpsBy[z].emploitps_date ==', data.getEmploiTpsBy[z].emploitps_date);
                        console.log('dateold== ', dateold);
                        datefor = dateold.toLocaleDateString('fr-FR', options);
                        console.log('datefor== ', datefor);
                        arraydate = datefor.split(" ");
                        console.log('arraydate== ', arraydate);
                        //data.getEmploiTpsBy[z].emploitps_date

                        var btn_etps_action = '<button type="button" class="btn btn-outline-info btn_etps_mod" data-toggle="modal" data-target="#mod_mod_eTps" option="mod_emploitps_btn" name="etpprog_' + data.getEmploiTpsBy[z].emploitps_id + '" id="etpprog_' + data.getEmploiTpsBy[z].emploitps_id + '" value="' + data.getEmploiTpsBy[z].groupe_libelle + '||' + data.getEmploiTpsBy[z].libele_partie + '||' + data.getEmploiTpsBy[z].emploitps_date + '||' + data.getEmploiTpsBy[z].mat_libelle + '|| (' + data.getEmploiTpsBy[z].Code_salle + ')' + data.getEmploiTpsBy[z].salle_libelle + '||' + data.getEmploiTpsBy[z].emploitps_h_debut + '||' + data.getEmploiTpsBy[z].emploitps_h_fin + '"><i class="fa fa-edit"></i> Modifier</button>    <br> <br>  <button type="button" class="btn btn-outline-danger" value="mod_emploitps_btn_sup" option="mod_emploitps_btn_sup" id="eTpsSup_' + data.getEmploiTpsBy[z].emploitps_id + '">   <i class="fa fa-trash"></i> Supprimer</button>';


                        var rowNode = dataTable_attrib_emptpsByAn
                            .row.add([arraydate[1] + '-' + arraydate[0], arraydate[2] + ' ' + arraydate[3], data.getEmploiTpsBy[z].emploitps_h_debut + ' - ' + data.getEmploiTpsBy[z].emploitps_h_fin, data.getEmploiTpsBy[z].libele_partie, data.getEmploiTpsBy[z].groupe_libelle, data.getEmploiTpsBy[z].mat_libelle, salle, data.getEmploiTpsBy[z].nom_prenom, btn_etps_action])
                            .draw()
                            .node();

                    }



                }

            })
            .fail(function() {});

    }

    $('#select_modfi_classe').on('change', function(event) {

        titre = "::ADMIN EVENT:::  Change sur:(#select_modfi_classe)";
        info = "  //ID Valeur= ";
        valueur = $('#select_modfi_classe').val();
        consoleInfo(envi, titre, info, valueur);
        $('#span_filiere').html($("#select_modfi_classe option:selected").text());

        get_filiereNiveau_mat();


    });

    $('#select_modfi_niveau').on('change', function(event) {

        titre = "::ADMIN EVENT:::  Change sur:(#select_modfi_niveau)";
        info = "  //ID Valeur= ";
        valueur = $('#select_modfi_niveau').val();
        consoleInfo(envi, titre, info, valueur);
        $('#span_niveau').html($("#select_modfi_niveau option:selected").text());

        get_filiereNiveau_mat();

    });

    function get_filiereNiveau_mat() {


        if (typeof($('#select_modfi_classe').val()) && $('#select_modfi_classe').val() != null && $('#select_modfi_classe').val() != "" && typeof($('#select_modfi_niveau').val()) && $('#select_modfi_niveau').val() != "" && $('#select_modfi_niveau').val() != null) {

            niveau = $('#select_modfi_niveau').val();
            filiere = $('#select_modfi_classe').val();

            url_info = liens + 'App/Models/info.model.php';
            values_info = 'action=getClassesMatiere&filiere_id=' + filiere + '&niveau_id=' + niveau + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();
            idelemt = "#select_modfi_niveau";

            AjaxMethod(url_info, values_info, fct_tabSet_classemat, idelemt);

        }



    }



    //::::MENU EVALUATION (MOYENNE )::::::::::

    $('#eval_prog_annee').on('change', function(event) {

        titre = "::ADMIN EVENT:::  Change sur:(#eval_prog_annee)";
        info = "  //ID Valeur= ";
        valueur = $('#eval_prog_annee').val();
        consoleInfo(envi, titre, info, valueur);


        url_info = liens + 'App/Models/info.model.php';
        values_info = 'action=get_eval_prog&id_annee=' + $('#eval_prog_annee').val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();
        idelemt = "#eval_prog_annee";

        AjaxMethod(url_info, values_info, fct_tabSet_eval_prog, idelemt);

    });

    $('#moy_annee').on('change', function(event) {

        titre = "::ADMIN EVENT:::  Change sur:(#moy_annee)";
        info = "  //ID Valeur= ";
        valueur = $('#moy_annee').val();
        consoleInfo(envi, titre, info, valueur);
        //alert($('#moy_annee').val()); 

        if (typeof($('#moy_annee').val()) != undefined && $('#moy_annee').val() != 0 && typeof($('#global_admin').val()) != undefined && $('#global_admin').val() != "") {

            url_info = liens + 'App/Models/info.model.php';
            values_info = 'action=getAllGrpByannee&annee_id=' + $('#moy_annee').val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();
            idelemt = "#moy_annee";

            AjaxMethod(url_info, values_info, fct_set_selectmoy_classe, idelemt);

        }

    });

    $('#moy_classe').on('change', function(event) {

        titre = "::ADMIN EVENT:::  Change sur:(#moy_classe)";
        info = "  //ID Valeur= ";
        valueur = $('#moy_classe').val();
        consoleInfo(envi, titre, info, valueur);
        //alert($('#moy_annee').val());

        if (typeof($('#moy_classe').val()) != undefined && typeof($('#global_admin').val()) != undefined && typeof($('#global_univ').val()) != undefined && $('#moy_classe').val() != 0) {

            url_info = liens + 'App/Models/info.model.php';
            values_info = 'action=getGrpeClassMatiere&groupeid=' + $('#moy_classe').val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();
            idelemt = "#moy_mat";

            AjaxMethod(url_info, values_info, fct_set_selectmoy_mat, idelemt);

        }

    });


    $('#elev_moy_annee').on('change', function(event) {

        titre = "::ADMIN EVENT:::  Change sur:(#elev_moy_annee)";
        info = "  //ID Valeur= ";
        valueur = $('#elev_moy_annee').val();
        consoleInfo(envi, titre, info, valueur);
        //alert($('#moy_annee').val()); 

        if (typeof($('#elev_moy_annee').val()) != undefined && $('#elev_moy_annee').val() != 0 && typeof($('#global_admin').val()) != undefined && $('#global_admin').val() != "") {

            url_info = liens + 'App/Models/info.model.php';
            values_info = 'action=getAllGrpByannee&annee_id=' + $('#elev_moy_annee').val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();
            idelemt = "#elev_moy_annee";

            AjaxMethod(url_info, values_info, fct_eleve_selectmoy_classe, idelemt);

        }

    });

    function fct_eleve_selectmoy_classe(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {
            $('#elev_moy_classe').html('<option ></option><option value="" disabled selected>Choisissez la classe</option>');
            jsondata = JSON.parse(jsondata);
            //Date	Groupe	matière	Professeur	Action
            titre = "evenement sur: Fct (fct_eleve_selectmoy_classe) // ";
            info = "Ajax send  resultat ::  ";
            valueur = jsondata;
            consoleInfo(envi, titre, info, valueur);

            for (var x in jsondata.getAllGrpByannee) {
                $('#elev_moy_classe').append('<option value="' + jsondata.getAllGrpByannee[x].groupe_id + '">' + jsondata.getAllGrpByannee[x].groupe_libelle + '</option>');
            }




        }

    }
    $('#elev_moy_classe').on('change', function(event) {

        titre = "::ADMIN EVENT:::  Change sur:(#elev_moy_classe)";
        info = "  //ID Valeur= ";
        valueur = $('#elev_moy_classe').val();
        consoleInfo(envi, titre, info, valueur);
        //alert($('#moy_annee').val());

        if (typeof($('#elev_moy_classe').val()) != undefined && typeof($('#global_admin').val()) != undefined && typeof($('#global_univ').val()) != undefined && $('#elev_moy_classe').val() != 0) {

            url_info = liens + 'App/Models/info.model.php';
            values_info = 'action=get_allEleveInGrpe&groupeid=' + $('#elev_moy_classe').val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();
            idelemt = "#moy_mat";

            AjaxMethod(url_info, values_info, fct_eleve_selectmoy_mat, idelemt);

        }

    });

    function fct_eleve_selectmoy_mat(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {
            $('#elev_moy_id').html('<option ></option><option value="" disabled selected>Choisissez l\'Elève / Etudiant</option>');
            jsondata = JSON.parse(jsondata);
            //Date	Groupe	matière	Professeur	Action
            titre = "evenement sur: Fct (fct_eleve_selectmoy_mat) // ";
            info = "Ajax send  resultat ::  ";
            valueur = jsondata;
            consoleInfo(envi, titre, info, valueur);

            for (var x in jsondata.getAllElevByGrp) {
                $('#elev_moy_id').append('<option value="' + jsondata.getAllElevByGrp[x].id_eleve_eleve + '"> (' + jsondata.getAllElevByGrp[x].matricule + ')-' + jsondata.getAllElevByGrp[x].nom_prenom + '</option>');
            }


        }

    }


    //PREVUSUALISE IMAGE

    //prev = document.querySelector('#prev');

    $('#photo_user').on('change', function(event) {

        var file = document.querySelector('#div_input_photo input[type=file]').files[0];

        var preview = document.querySelector('#user_tof');
        var reader = new FileReader();

        var imgType;

        imgType = file.name.split('.');
        imgType = imgType[imgType.length - 1].toLowerCase();

        if (allowedTypes.indexOf(imgType) != -1) {

            reader.addEventListener("load", function() {
                preview.src = reader.result;
            }, false);

            if (file) {
                reader.readAsDataURL(file);
            }

        }


    });



    //PREVUSUALISE IMAGE
    //prev = document.querySelector('#prev');

    $('#info_infoBundle_Info_image_file').on('change', function(event) {

        var file = document.querySelector('#notif_input_prevue input[type=file]').files[0];

        var preview = document.querySelector('#preview_thumbnail_img');
        var reader = new FileReader();

        var imgType;
        //alert(file);
        imgType = file.name.split('.');
        imgType = imgType[imgType.length - 1].toLowerCase();

        if (allowedTypes.indexOf(imgType) != -1) {

            reader.addEventListener("load", function() {
                preview.src = reader.result;
                //preview.append('');
            }, false);

            if (file) {
                reader.readAsDataURL(file);
            }

        } else {
            alert('image');
        }


    });


    //NOTIFICATION


    $('#notif_cible').on('change', function(event) {

        //"all"          "professeur"    "eleves"      "Administration" 

        $('#section-professeur tbody').html(" ");
        $('#section-eleves tbody').html(" ");

        $('#notif_fillieres').html('<option selected="" disabled="" value="">-- Sélectionner la Fillère --</option>');
        $('#notif_groupe').html('<option selected="" disabled="" value="" >-- Sélectionner le groupe --</option>');

        //alert('init');

        if (typeof($('#notif_cible').val()) != undefined && $('#notif_cible').val() != "" && $('#notif_cible').val() != null && $('#notif_cible').val() != 0) {

            titre = "::ADMIN EVENT:::  Change sur:(#notif_cible)";
            info = "  //ID Valeur= ";
            valueur = $('#notif_cible').val();
            consoleInfo(envi, titre, info, valueur);

            url_info = liens + 'App/Models/info.model.php';

            switch ($('#notif_cible').val()) {
                case "professeur":
                    values_info = 'action=getListProf' + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();
                    idelemt = "#notif_cible";
                    AjaxMethod(url_info, values_info, fct_get_allProf, idelemt);
                    break;
                case "Administration":
                    values_info = 'action=getListAdmin' + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();
                    idelemt = "#notif_cible";
                    AjaxMethod(url_info, values_info, fct_get_admin, idelemt);
                    break;

                default:
                    notif_hide_tab();
                    break;
            }

        }




    });


    $('#notif_annee').on('change', function(event) {

        //"all"          "professeur"    "eleves"      "Administration"  

        if (typeof($('#notif_annee').val()) != undefined && $('#notif_notif_anneefillieres').val() != "" && $('#notif_annee').val() != null && $('#notif_annee').val() != 0) {

            titre = "::ADMIN EVENT:::  Change sur:(#notif_annee)";
            info = " CIBLE " + $('#notif_cible').val() + " //Get univ classe by annee= ";
            valueur = $('#notif_annee').val();
            consoleInfo(envi, titre, info, valueur);

            if ($('#notif_cible').val() == "eleves") {
                url_info = liens + 'App/Models/info.model.php';
                values_info = 'action=get_groupes&anneeScol=' + $('#notif_annee').val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();
                idelemt = "#notif_annee";
                AjaxMethod(url_info, values_info, fct_get_allGroupe_active, idelemt);

            }



        }


    });

    function fct_get_allGroupe_active(jsondata) {

        if (typeof(jsondata) != undefined && jsondata != "") {

            $('#notif_groupe').html('<option selected="" disabled="" value="">-- Sélectionner le groupe --</option>');

            jsondata = JSON.parse(jsondata);

            titre = "evenement sur: Fct (fct_get_allGroupe_active) // ";
            info = "Ajax send  resultat ::  ";
            valueur = jsondata;
            consoleInfo(envi, titre, info, valueur);

            for (var x in jsondata) {
                $('#notif_groupe').append('<option value="' + jsondata[x].groupe_id + '">' + jsondata[x].groupe_libelle + '</option>');

            }

        }

    }

    $('#notif_groupe').on('change', function(event) {
        //"all"          "professeur"    "eleves"      "Administration"  
        if (typeof($('#notif_groupe').val()) != undefined && $('#notif_groupe').val() != "" && $('#notif_groupe').val() != null && $('#notif_groupe').val() != 0) {

            if ($('#notif_cible').val() == "eleves") {

                titre = "::ADMIN EVENT:::  Change sur:(#notif_groupe)";
                info = " CIBLE " + $('#notif_cible').val() + " //ID Valeur= ";
                valueur = $('#notif_groupe').val();
                consoleInfo(envi, titre, info, valueur);

                url_info = liens + 'App/Models/info.model.php';
                values_info = 'action=get_allEleveInGrpe&groupeid=' + $('#notif_groupe').val() + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();
                idelemt = "#notif_groupe";
                AjaxMethod(url_info, values_info, fct_get_allEleveInGrpe, idelemt);
            }
        }
    });

    $('#checkAllprofesseurs').on('click', function(event) {

        var_input_profchek = document.querySelectorAll("#section-professeur tbody .input_prof_chek");

        for (var key in var_input_profchek) {
            if (var_input_profchek.hasOwnProperty(key)) {

                console.log('elemtn n° =: ' + key + ' elemtn =' + var_input_profchek[key]);

                var_input_profchek[key].click();

            }
        }



    });

    $('#checkAlladmin').on('click', function(event) {
        var_input_admin_check = document.querySelectorAll("#section-admin tbody .input_prof_chek");
        for (var key in var_input_admin_check) {
            if (var_input_admin_check.hasOwnProperty(key)) {
                console.log('elemtn n° =: ' + key + ' elemtn =' + var_input_admin_check[key]);
                var_input_admin_check[key].click();
            }
        }
    });

    $('#checkAlleleves').on('click', function(event) {

        var_input_profchek = document.querySelectorAll("#section-eleves tbody .input_eleve_chek");

        for (var key in var_input_profchek) {
            if (var_input_profchek.hasOwnProperty(key)) {

                console.log('elemtn n° =: ' + key + ' elemtn =' + var_input_profchek[key]);

                var_input_profchek[key].click();

            }
        }



    });

    function root_dateDiff1(date1, date2) {
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
    //DETECT TOUS LES EVENEMENTS SOURIS
    document.addEventListener("click", function(event) {
        //event.preventDefault();

        if (typeof(event.target.id) != undefined && event.target.id != "") {

            //ADMIN MENU ELEVE SUP OU AJOUT CLASSE
            consoleInfo(envi, " document.addEventListener click info=" + event.target.id, "type of value = " + typeof($('#' + event.target.id).attr("value")) + "Value = ", $('#' + event.target.id + '').attr("value"));

            tabval_test = (event.target.id).split("_");
            if ((tabval_test[1] == "infos" && tabval_test[0] == "abs") || (tabval_test[2] == "infos" && tabval_test[1] == "abs")) {

                consoleInfo(envi, "click btn_abs_eleve", "id = ", event.target.id);
                valueur = $('#' + event.target.id + '').attr("value");
                consoleInfo(envi, "click btn_abs_eleve", "Value = ", valueur);
                values_motif = valueur.split("_");

                $('#modal_absenceLabel').html(values_motif[1] + ' <br> ' + values_motif[2]);
                $('#ideleve_absence').val(values_motif[0]);
                $('#suivi_classe').val(values_motif[4]);

            }

            if ((tabval_test[1] == "view" && tabval_test[0] == "abs") || (tabval_test[2] == "view" && tabval_test[1] == "abs")) {

                consoleInfo(envi, "click abs_view_infos", "id = ", event.target.id);
                valueur = $('#' + event.target.id + '').attr("value");
                consoleInfo(envi, "click abs_view_infos", "Value = ", valueur);
                values_motif = valueur.split("_");

                $('#view_modal_absenceLabel').html(values_motif[1] + ' <br> ' + values_motif[2]);
                $('#view_ideleve_absence').val(values_motif[0]);
                id_eleve = values_motif[0];
                $('#view_suivi_classe').val(values_motif[4]);
                id_group = values_motif[4];


                url_info = liens + 'App/Models/info.model.php';
                values_info = 'action=ajax_get_all_abs_elev&id_group=' + id_group + '&id_eleve=' + id_eleve + '&global_admin=' + global_admin + '&global_univ=' + global_univ;
                idelemt = '#' + event.target.id;

                root_AjaxMethod(url_info, values_info, fct_abs_view_infos, idelemt);

            }

            if (typeof($('#' + event.target.id).attr("value")) != undefined && $('#' + event.target.id).attr("value") != undefined) {

                if ($('#' + event.target.id + '').attr("value") == "sup_ElevGroup" || $('#' + event.target.id + '').attr("value") == "add_ElevGroup") {

                    values = [];
                    resultat = event.target.id;
                    values = resultat.split("_");

                    titre = "sup_ElevGroup ou add_ElevGroup values =" + values;
                    info = "values[1] =" + values[1] + "  values[0]=" + values[0];
                    valueur = resultat;
                    consoleInfo(envi, titre, info, valueur);

                }

                if (typeof($('#' + event.target.id + '').attr("option")) != undefined) {

                    var option = $('#' + event.target.id + '').attr("option");

                    switch (option) {

                        case "btn_evalProg_click":

                            evalProg_ideval = (event.target.id).split("_");
                            titre = "::ADMIN EVENT:::  click sur:(.btn_prgoEval) ::ID Eval =" + evalProg_ideval[1];

                            info = "  //ID Valeur= " + event.target.id;
                            resultinfo = event.target.value;
                            consoleInfo(envi, titre, info, valueur);

                            values = resultinfo.split("_");

                            $('#evalprog_grpe').html(values[1]);
                            $('#evalprog_mat').html(values[2] + '-' + values[3]);
                            $('#evalprog_prof').html(values[4]);
                            $('#evalprog_tel').html(values[5]);

                            $('#evalprog_tmpdate').html(values[6]);
                            $('#evalprog_hdebut').html(values[7]);
                            $('#evalprog_hfin').html(values[8]);



                            timedebut_tab_root = (values[7]).split(":");
                            timefin_tab_root = (values[8]).split(":");

                            $('#evalProg_coef').val(values[9]);
                            $('#evalProg_notation').val(values[10]);

                            //year, month, day, hour, minute
                            var ddebut_root = new Date(2020, 07, 15, timedebut_tab_root[0], timedebut_tab_root[1]);
                            var dfin_root = new Date(2020, 07, 15, timefin_tab_root[0], timefin_tab_root[1]);

                            titre = "evenement sur: #prof_eval_hDebut // #prof_eval_hFin";
                            info = "Heure debut = " + ddebut_root + " && Heure Fin = " + dfin_root;

                            diff_root = root_dateDiff1(ddebut_root, dfin_root);

                            valueur = diff_root.hour + "H : " + diff_root.min + "Min";

                            consoleInfo(envi, titre, info, valueur);
                            $('#evalprog_duree').html(valueur);



                            $('#evalProg_ideval').html('<input type="hidden" name="evalProg_ideval" value="' + evalProg_ideval[1] + '">');

                            $("#progEval_modal").modal();

                            break;


                        case "cree_matiere_btn_add":
                            event.preventDefault();
                            var result = $('#' + event.target.id + '').attr("value");
                            //DOC_12%3B2
                            values = result.split("/_/");
                            //alert("ok");

                            titre = "click cree_matiere_btn_add";
                            info = "event sur = " + event.target;
                            valueur = "id =" + event.target.id + " && valeur =" + $('#' + event.target.id + '').attr("value");
                            consoleInfo(envi, titre, info, valueur);

                            var rowNode = dataTable_classe_classMat
                                .row.add([values[0], values[1], '<a title="Delete Matière" class="btn-default btn-xs purple" href="#"><i class="fa fa-trash" style="font-size: x-large;"><input type="hidden" value="' + values[1] + '" name="classmat_' + event.target.id + '"></i></a> '])
                                .draw()
                                .node();


                            $(rowNode)
                                .css('color', 'green')
                                .animate({ color: 'black' });

                            break;

                        case "mod_emploitps_btn":

                            var result = $('#' + event.target.id + '').attr("value");
                            values = result.split("||");
                            var mod_input_ideTPs = (event.target.id).split("_");

                            $('#mod_grpe').html(values[0]);
                            $('#mod_mat').html(values[3]);

                            $('#mod_periode').append('<option value="" disabled selected >' + values[1] + '</option><option value="0" disabled>Veuillez choisir la période</option>');
                            $('#mod_dated').val(values[2]);

                            $('#mod_salle').append('<option value="" disabled selected >' + values[4] + '</option><option value="0" disabled>Choisissez une salle</option>');
                            $('#mod_Hd').val(values[5]);
                            $('#mod_Hf').val(values[6]);
                            $('#mod_input_ideTPs').val(mod_input_ideTPs[1]);



                            titre = "click mod_emploitps_btn";
                            info = "event sur = " + event.target;
                            valueur = "id =" + event.target.id + " && valeur =" + values + " Id emploi du temps=" + mod_input_ideTPs;
                            consoleInfo(envi, titre, info, valueur);

                            break;

                        case "mod_emploitps_btn_sup":

                            var result = (event.target.id).split("_");
                            //alert("mod_emploitps_btn_sup");
                            values = 'action=mod_sup_eTps&mod_tableChps=groupe_emploitps@@emploitps_id&mod_id_eTps=' + result[1] + '&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();

                            $.ajax({
                                    url: liens + 'App/Models/info.model.php',
                                    type: 'POST',
                                    dataType: 'text',
                                    data: values,
                                })
                                .done(function(data) {
                                    if (typeof(data) != undefined) {
                                        data = JSON.parse(data);
                                        fct_attrib_eploiTps_groupe_NEW();
                                    }
                                })
                                .fail(function() {});


                            break;


                        default:
                            break;
                    }

                }

                if ($('#' + event.target.id + '').attr("value") == "sup_ElevGroup") {

                    ajoutSupElveClasse(0, values[1], values[0]);
                } else if ($('#' + event.target.id + '').attr("value") == "add_ElevGroup") {

                    ajoutSupElveClasse(1, values[1], values[0]);

                }




            } else {
                tabval = (event.target.id).split("_");
                if (tabval[0] == "btdelabs") {
                    idelemt = event.target.id;
                    $('#' + idelemt).parents("tr").css("background", "red");
                    $('#' + idelemt).hide();


                    consoleInfo(envi, "click .btnsupelevabs tabval=" + tabval, "Value parent= ", $('#' + idelemt).parent().tagName);
                    url_info = liens + 'App/Models/info.model.php';
                    values_info = 'action=ajax_del_abs_elev&fk_emploitps=' + tabval[1] + '&global_admin=' + global_admin + '&global_univ=' + global_univ;
                    root_AjaxMethod(url_info, values_info, ajax_result_toast, idelemt);
                }

            }


        }

    });

    function fct_abs_view_infos(data) {

        if (typeof(data) != undefined) {
            data = JSON.parse(data);
            $('#elv_all_absc').html('');
            eleve_absence_tbe.clear().draw();
            for (var x in data.get_all_absence_elev) {

                if (data.get_all_absence_elev[x].abs_justif == 1) {
                    justif = '<div class="alert alert-success">Justifier !</div>';
                } else {
                    justif = '<div class="alert alert-danger">Non Justifier !</div>';
                }


                var rowNode = eleve_absence_tbe
                    .row.add([data.get_all_absence_elev[x].libele_partie, data.get_all_absence_elev[x].emploitps_date + ' | ' + data.get_all_absence_elev[x].emploitps_h_debut + ' - ' + data.get_all_absence_elev[x].emploitps_h_fin, data.get_all_absence_elev[x].mat_libelle, data.get_all_absence_elev[x].nom_prenom, data.get_all_absence_elev[x].abs_motifs, justif, '<button type="button" class="btn btn-outline-danger btnsupelevabs" id="btdelabs_' + data.get_all_absence_elev[x].fk_emploitps + '"><i class="fa fa-trash "></i> </button>'])
                    .draw()
                    .draw()
                    .node();
            }
        }
    }
    //btn_mod_maj_clse: Mise a jour table emploi du tps apres modif
    $('#btn_mod_maj_clse').on('click', function(event) {
        //fct_attrib_eploiTps_groupe();
        fct_attrib_eploiTps_groupe_NEW();
        //alert('click btnok');
    });


    //Envoi de mail chaque 3 second
    var myVar_smsauto = setInterval(fct_sendSMS_auto, 4000);
    var var_send_i = 0;
    //Fonction envoi sms
    function fct_sendSMS_auto() {
        var_send_i++;
        if (var_send_i == 2) { stopsendSMS(); }
        if (typeof($('#global_admin').val()) != undefined && typeof($('#global_univ').val()) != undefined) {
            titre = ":::::::::Envoi de sms automatique:::::::::";
            info = "  // sms --> ";
            valueur = "admin = " + $('#global_admin').val() + " univ=" + $('#global_univ').val();
            consoleInfo(envi, titre, info, valueur);
            url_info = liens + 'App/Models/info.model.php';
            values_info = 'action=envoi_sms_auto&&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();
            idelemt = '#';
            root_AjaxMethod(url_info, values_info, ajax_result_sms, idelemt);
        }

    }

    function ajax_result_sms(params) {


    }

    function stopsendSMS() {
        clearInterval(myVar_smsauto);
    }



});