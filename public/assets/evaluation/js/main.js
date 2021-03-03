/**
 * Created by jordan on 16/10/2015.
 */



$(document).ready(function() {


    $("#join-team" ).mouseover(function() {
        var el = $(".bird");
        el.addClass("bird-hover");
        el.removeClass("bird");
    });

    $("#join-team" ).mouseout(function() {
        var el = $(".bird-hover");
        el.addClass("bird");
        el.removeClass("bird-hover");
    });


    $("#project-talk" ).mouseover(function() {
        var el = $(".ampoule");
        el.addClass("ampoule-hover");
        el.removeClass("ampoule");
    });

    $("#project-talk" ).mouseout(function() {
        var el = $(".ampoule-hover");
        el.addClass("ampoule");
        el.removeClass("ampoule-hover");
    });



    $("#join-customer" ).mouseover(function() {
        var el = $(".hand");
        el.addClass("hand-hover");
        el.removeClass("hand");
    });

    $("#join-customer" ).mouseout(function() {
        var el = $(".hand-hover");
        el.addClass("hand");
        el.removeClass("hand-hover");
    });

    /*
    * OLD QCM
    * */


    var total = $('.questionnaire form>.block_100.qcm').length-1;

    $(' form>.block_100.qcm').each(function(index){
        var parent=$(this);
        parent.find('label').on('click',function(){

            if(index != total){

                parent.find('p.suivante').fadeIn();
            }

            else{

                $('div.mail_qcm').fadeIn();
            }






        })
        parent.find('p.suivante').on('click',function(){
            next=index+1;
            $('.qcm_retour').show();
            parent.removeClass('active');
            $('.questionnaire form>.block_100.qcm.question_'+next).addClass('active');
            if(next==9){
                /* $('div.mail_qcm').fadeIn(); */
            }
        })
        parent.find('p.qcm_retour').on('click',function(){
            prev=index-1;

            parent.removeClass('active');
            $('.questionnaire form>.block_100.qcm.question_'+prev).addClass('active');
            if(!prev){
                $('.qcm_retour').hide();
            }
            if(prev==8){
                $('div.mail_qcm').hide();
            }

        })
    });


  /*  $(document).mousemove(function(e){
        $("#image").css({left:e.pageX+20, top:e.pageY+30});
    });*/

    // Portfolio filter
    var listlink = $('#tri li');
    var listitems = $('#portfolio_items > div');
    $(listlink).first().addClass("active-link");
    $(listlink).each(function() {
        $(this).on("click",function(){
            if(!$(this).hasClass("active-link")){
                $(listlink).removeClass("active-link");
                $(this).addClass("active-link");
                var textli = $(this).attr("data-filter");
                var textreset = $(listlink).first().attr("data-filter");
                $(listitems).hide();
                $(listitems).each(function () {
                    if ($(this).hasClass(textli)) {
                        $(this).fadeIn(1000);
                    }
                });
                if(textli==textreset){
                    $(listitems).fadeIn();
                }
            }
        })
    });

});