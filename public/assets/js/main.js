$(document).ready(function() {
    envi = Appconfigs.envi;

    liens = Appconfigs.liens_serv;

    //DETECT TOUS LES EVENEMENTS SOURIS
    document.addEventListener("click", function(event) {
        //event.preventDefault();

        if (typeof(event.target.id) != undefined && event.target.id != "") {

            titre = "evenement sur: " + event.target;
            info = "id =" + event.target.id;
            if (typeof(event.target.class) != undefined && event.target.class != "") {
                info = info + "  Class=" + event.target.class;
            }
            valueur = event.target.value;
            consoleInfo(envi, titre, info, valueur);



            switch (event.target.id) {
                case "attribution_emlploiTps_mat_Lundi":
                    select_jour = "Lundi";
                    getProfListMajSelect(select_jour);
                    break;

                default:

                    break;
            }


            if (typeof($('#' + event.target.id).attr('option')) != undefined) {
                info = info + "  Option =" + $('#' + event.target.id).attr('option');
            }
        }

    });


    //id="dataTable_attrib_mat"

    //$('select').selectize({ sortField: 'text' });
    $("select").select2();


    var allowedTypes = ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'tif'];
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



    function root_dateDiff(date1, date2) {
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


});