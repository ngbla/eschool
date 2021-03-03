if (window.location.hostname == "localhost.eschool") {

    liens_serv = 'http://' + window.location.hostname + '/';

} else {

    liens_serv = 'https://' + window.location.hostname + '/';

}

//alert

var Appconfigs = {

    envi: "debug",

    liens_serv: liens_serv

};




function consoleInfo(envi, titre, info, valueur) {

    if (envi == "debug") {

        console.log(titre + ' : ', info + '  ', "( " + valueur + " )");

        console.log('**********LOGS INFOS************');

    }

}

//ajax fction

function root_AjaxMethod(url, params, successCallback, idelemt) {

    //show loading... image
    var fct_successCallback = successCallback;
    var gbl_idelemt = idelemt;
    $.ajax({

        url: url,
        type: 'POST',
        dataType: 'text',
        data: params,
        success: fct_successCallback,

        error: function(xhr, textStatus, errorThrown) {
            consoleInfo(Appconfigs.envi, 'Fct root_AjaxMethod', 'Erreur = ' + textStatus, 'xhr =' + xhr + '  errorThrown=' + errorThrown);
            console.log('Erreur! Veuillez Réessayer');
        }


    });


}


function root_fct_showinfos_toast(infos_stat, classtype) {
    //classtype alert-danger , alert-success  , alert-warning

    $('.toast').toast({ delay: 50000 });

    $('#toast_body').removeClass("alert-danger");
    $('#toast_body').removeClass("alert-success");
    $('#toast_body').removeClass("alert-warning");

    $('#toast_header').removeClass("alert-danger");
    $('#toast_header').removeClass("alert-success");
    $('#toast_header').removeClass("alert-warning");

    $("#toast_body").text(infos_stat);

    $('#toast_body').addClass(classtype);
    $('#toast_header').addClass(classtype);

    //show hide
    $('.toast').toast('show');

}

function root_fct_erreur_notif() {
    $(".spinner_load").hide();
    $(".badge_success").hide();
    $(".badge_erreur").show();
}


function root_fct_load_notif() {
    $(".spinner_load").show();
    $(".badge_success").hide();
    $(".badge_erreur").hide();
}


function root_fct_succes_notif() {
    $(".spinner_load").hide();
    $(".badge_success").show();

}





$(document).ready(function() {
    var userip, usercountry, usercity, userregion, userlatitude, userlongitude, usertimezone;


    //Desactive utilisateur connectez
    //Envoi de mail chaque 3 second 11H17
    var_etat_i = 0;
    var setinter_fct = setInterval(fct_verif_conex, 6000);

    function fct_verif_conex() {
        //console.log('var_etatinit_i = ' + var_etat_i);
        if ((var_etat_i == 6000 || var_etat_i == 0) && typeof($('#global_admin').val()) != undefined && typeof($('#global_univ').val()) != undefined) {

            url_info = liens + 'App/Models/info.model.php';
            values_info = 'action=set_user_etatconect&global_admin=' + $('#global_admin').val() + '&global_univ=' + $('#global_univ').val();
            idelemt = '#';
            root_AjaxMethod(url_info, values_info, ajax_fct_verif_conex, idelemt);
            var_etat_i = 0;
            //console.log('Initialisation var_etat_i = ' + var_etat_i);

        }
        var_etat_i++;
    }

    function ajax_fct_verif_conex(params) {}

    //var ip_local = getNetworkIP();
    //ip_local.then((value) => { console.log(value); });

    async function getNetworkIP() {
        let found = false;
        let resolve;
        const promise = new Promise((res) => {
            resolve = res;
        });
        const pc = new RTCPeerConnection({ iceServers: [] });

        pc.addEventListener("icecandidate", (e) => {
            if (!e.candidate || found) return;
            resolve(e.candidate.address);
            found = true;
        });

        pc.createDataChannel("");
        pc.createOffer().then((desc) => pc.setLocalDescription(desc));


        return promise;

    }




});