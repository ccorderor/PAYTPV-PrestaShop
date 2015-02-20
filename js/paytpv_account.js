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
        
        $(".remove_card").on("click", function(e){   
            e.preventDefault();
            $("#paytpv_iduser").val($(this).attr("id"));
            cc_iduser = $("#cc_"+$(this).attr("id")).val()
            confirm(msg_removecard + ": " + cc_iduser, true, function(resp) {
                if (resp)   removeCard();
            });
        });

        $(".cancel_suscription").on("click", function(e){   
            e.preventDefault();
            $("#id_suscription").val($(this).attr("id"));
            confirm(msg_cancelsuscripton, true, function(resp) {
                if (resp)   cancelSuscription();
            });
        });

    });

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

    function alert(msg) {
        $.fancybox("#alert",{
            beforeShow: function() {
                $(".title").html(msg);
            },
            modal: false,
        });
    }

    function vincularTarjeta(){
        if ($("#savecard").is(':checked')){
            $('#savecard').attr("disabled", true);
            $('#close_vincular').show();
            $('#nueva_tarjeta').show();
        }else{
            alert(msg_accept);
        }

    }

    function close_vincularTarjeta(){
        $('#savecard').attr("disabled", false);
        $('#nueva_tarjeta').hide();
        $('#close_vincular').hide();
    }

    function confirmationRemove(paytpv_cc){
        $("#cc").html(paytpv_cc);
        $("#paytpv_cc").val(paytpv_cc);
        $("#deltecard").open();
    }

    function removeCard()
    {
        paytpv_iduser = $("#paytpv_iduser").val();
        $.ajax({
            url: url_removecard,
            type: "POST",
            data: {
                'paytpv_iduser': paytpv_iduser,
                'ajax': true
            },
            success: function(result)
            {
                if (result == '0')
                {
                   $("#card_"+paytpv_iduser).fadeOut(1000);
                }
            }
        });
        
    };


    function cancelSuscription()
    {
        id_suscription = $("#id_suscription").val();
        $.ajax({
            url: url_cancelsuscription,
            type: "POST",
            data: {
                'id_suscription': id_suscription,
                'ajax': true
            },
            success: function(result)
            {
                if (result == '0')
                {
                    $("#suscription_"+id_suscription).find(".button_del").html("<span class=\"canceled_suscription\">{l s='CANCELED' mod='paytpv'}</span>");
                }
            }
        });
        
    };