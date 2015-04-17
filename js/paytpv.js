/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author     Jose Ramon Garcia <jrgarcia@paytpv.com>
*  @copyright  2015 PAYTPV ON LINE S.L.
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/


$(document).ready(function() {
    $("#open_conditions").fancybox({
            autoSize:false,
            'width':parseInt($(window).width() * 0.7)
        });

    $("#open_directpay").fancybox({
            'beforeShow': onOpenDirectPay
        });

    $("body").on("click",".paytpv #exec_directpay",function() {
       var suscripcion="&suscripcion="+($("#suscripcion").is(':checked')?1:0)+"&periodicity="+$("#susc_periodicity").val()+"&cycles="+$("#susc_cycles").val();
       window.location.href = $("#card").val()+suscripcion;
    });

    $("body").on("click",".paytpv #suscripcion",function() {
       checkSuscription();
    });

    checkSuscription();

});

function checkSuscription(){
    if ($("#suscripcion").is(':checked')){
        $("#div_periodicity").show();
        $("#saved_cards").hide();
        $("#storingStep").hide();
        $(".paytpv_iframe").hide();
    }else{
        $("#div_periodicity").hide();
        $("#saved_cards").show();
        checkCard();
    }
   
}

function confirm(msg, modal, callback) {
    $.fancybox("#confirm",{
        modal: modal,
        beforeShow: function() {
            $(".title").html(msg);
        },
        afterShow: function() {
            $(".confirm").on("click", function(event){
                if($(event.target).is(".yes")){
                    ret = true;
                } else if ($(event.target).is(".no")){
                    ret = false;
                }
                $.fancybox.close();
            });
        },
        afterClose: function() {
            callback.call(this, ret);
        }
    });
}

function checkCard(){
    if ($("#card").val()=="0"){
        $("#storingStep").removeClass("hidden").show();
        $("#open_directpay").hide();
        $("#exec_directpay").hide();
    }else{
        $("#storingStep").hide();
        $("#open_directpay").show();
        $("#exec_directpay").show();
    }
    $(".paytpv_iframe").hide();

}


function onOpenDirectPay(){
    $("#datos_tarjeta").html($("#card :selected").text());
    var suscripcion="&suscripcion="+($("#suscripcion").is(':checked')?1:0)+"&periodicity="+$("#susc_periodicity").val()+"&cycles="+$("#susc_cycles").val();
    
    $("#pago_directo").attr("action",$("#card").val()+suscripcion);
}

function useCard(){
    if (confirm("{l s='Accept to pay and complete your order.' mod='paytpv'}")){
       window.location.href = $("#card").val()+suscripcion;
    }
}


function addCard(){
    $("#paytpv_agree").val($("#savecard").is(':checked')?1:0);
    $("#action_paytpv").val("add");

    $("#form_paytpv").submit();
}


function addCardJQ(url){
    $("#paytpv_iframe").attr("src","");
    paytpv_agree = $("#savecard").is(':checked')?1:0;
    $.ajax({
        url: url,
        type: "POST",
        data: {
            'paytpv_agree': paytpv_agree,
            'id_cart' : $("#id_cart").val(),
            'ajax': true
        },
        success: function(result)
        {   
            if (result.error=='0')
            {
                
                $("#storingStep").hide();
                $("#paytpv_iframe").attr("src",result.url);
                $(".paytpv_iframe").slideDown(500);
            }
        },
        dataType:"json"
    });
}

function suscribe(){
    $("#paytpv_agree").val(0);
    $("#action_paytpv").val("add");

    $("#paytpv_suscripcion").val(1);
    $("#paytpv_periodicity").val($("#susc_periodicity").val());
    $("#paytpv_cycles").val($("#susc_cycles").val());

    $("#form_paytpv").submit();
}

function suscribeJQ(url){
    $("#paytpv_iframe").attr("src","");
    $.ajax({
        url: url,
        type: "POST",
        data: {
            'paytpv_agree': 0,
            'paytpv_suscripcion': 1,
            'paytpv_periodicity': $("#susc_periodicity").val(),
            'paytpv_cycles': $("#susc_cycles").val(),
            'id_cart' : $("#id_cart").val(),
            'ajax': true
        },
        success: function(result)
        {
                       
            if (result.error=='0')
            {
                $("#div_periodicity").hide();
                $("#storingStep").hide();
                $("#paytpv_iframe").attr("src",result.url);
                $(".paytpv_iframe").slideDown(500);
            }
        },
        dataType:"json"
    });
}
