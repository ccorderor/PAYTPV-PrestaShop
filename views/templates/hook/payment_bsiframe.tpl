<script type="text/javascript">
    $(document).ready(function() {
        $("#open_conditions").fancybox({
                autoSize:false,
                'width':parseInt($(window).width() * 0.7)
            });

        $("#open_directpay").fancybox({
                'beforeShow': onOpenDirectPay
            });

        $("#exec_directpay").click(function() {
           var suscripcion="&suscripcion="+($("#suscripcion").is(':checked')?1:0)+"&periodicity="+$("#susc_periodicity").val()+"&cycles="+$("#susc_cycles").val();
           window.location.href = $("#card").val()+suscripcion;
        });

        $("#suscripcion").click(function() {
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


    function addCardJQ(){
        $("#paytpv_iframe").attr("src","");
        paytpv_agree = $("#savecard").is(':checked')?1:0;
        $.ajax({
            url: "{$link->getModuleLink('paytpv', 'actions', ['process' => 'addCard'], true)|addslashes}",
            type: "POST",
            data: {
                'paytpv_agree': paytpv_agree,
                'id_cart' : {$id_cart},
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

    function suscribeJQ(){
        $("#paytpv_iframe").attr("src","");
        $.ajax({
            url: "{$link->getModuleLink('paytpv', 'actions', ['process' => 'suscribe'], true)|addslashes}",
            type: "POST",
            data: {
                'paytpv_agree': 0,
                'paytpv_suscripcion': 1,
                'paytpv_periodicity': $("#susc_periodicity").val(),
                'paytpv_cycles': $("#susc_cycles").val(),
                'id_cart' : {$id_cart},
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

</script>

<style>

    .paytpv
    {
        padding-left:17px;
        display: block;
        border: 1px solid #d6d4d4;
        border-radius: 4px;
    }
    .paytpv_tarjetas{
        padding: 5px 15px 15px 15px;
        font-size: 90%;
    }

    .alert {
        padding: 0px 35px 8px 14px;
        margin: 10px 20px 0px 0px;
        /* text-shadow: 0 1px 0 rgba(255,255,255,0.5); */
        background-color: #fcf8e3;
        border: 1px solid #fbeed5;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
       }

    .alert-info {
        color: #3a87ad;
        background-color: #d9edf7;
        border-color: #bce8f1;
    }

    #directpay{
        font-size: 12px;
         padding: 8px 35px 8px 14px;
        margin: 10px 20px 0px 0px;
        /* text-shadow: 0 1px 0 rgba(255,255,255,0.5); */
        background-color: #fcf8e3;
        border: 1px solid #fbeed5;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;

    }

    #directpay p{
        padding_top: 10px;
    }

     #directpay .password{
        border: 1px solid #3a87ad;
    }

    .button_left {
        float: left; 
    }

    .button_right {
        float: right;
        height: 50px;
    }

    .nota {
        color: #3a87ad;
        font-size: 90%;
        background-color: #d9edf7;
        border-color: #bce8f1;
        margin: 5px 0px;
    }

    .suscripcion{
        list-style:none;
    }

    .suscription_period{
        padding: 5px 0px;
    }
    .suscripcion li{
        color: rgb(3, 173, 212);
    }

    #tipo-pago p{
        padding-bottom: 10px;
    }

    .hidden{
        display:none;
    }

</style>
<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="paytpv">            
            <a href="http://www.paytpv.com" target="_blank"><img src="{$this_path}paytpv.png"></a>
            <br>
            {if ($msg_paytpv!="")}
                <p>
                    <span class="message">{$msg_paytpv}</span>
                </p>
            {/if}
            {if (!$showcard && $active_suscriptions)}
            <div id="tipo-pago">
                <p class="checkbox">
                   
                    <span class="checked"><input type="checkbox" name="suscripcion" id="suscripcion" value="1"></span>
                    <label for="suscripcion">{l s='Subscribe to this order?' mod='paytpv'}</label>
                    
                </p>
                
                <div id="div_periodicity" class="suscription_period" style="display:none">
                    <div class="nota">{l s='The first purchase when ordering shall carry out, and the following is defined as the frequency subscription' mod='paytpv'}.
                    {l s='By subscribing you agree to the ' mod='paytpv'} <a id="open_conditions" href="#conditions">{l s='terms and conditions of service' mod='paytpv'}</a>.
                    </div>
                    {l s='Periodicity:' mod='paytpv'} 
                    <select name="susc_periodicity" id="susc_periodicity">
                        <option value="7">{l s='7 days (weekly)' mod='paytpv'}</option>
                        <option value="30" selected>{l s='30 days (monthly)' mod='paytpv'}</option>
                        <option value="90">{l s='90 days (quarterly)' mod='paytpv'}</option>
                        <option value="180">{l s='180 days (biannual)' mod='paytpv'}</option>
                        <option value="365">{l s='365 days (anual)' mod='paytpv'}</option>
                    </select>
                    &nbsp;&nbsp;
                    {l s='Payments:' mod='paytpv'} 
                    <select name="susc_cycles" id="susc_cycles">
                        <option value="0" selected>{l s='Permanent' mod='paytpv'}</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">6</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                    </select>
                    &nbsp;&nbsp;
                    <span class="">
                    <a href="javascript:void(0);" onclick="suscribeJQ();" title="{l s='Subscribe' mod='paytpv'}" class="button button-small btn btn-default">
                        {l s='Subscribe' mod='paytpv'}
                    </a>
                    </span>             
                </div>
            </div>
            
            {/if}
            
           
            <div id="saved_cards">
                {l s='Card' mod='paytpv'}:
                <select name="card" id="card" onChange="checkCard()">
                    {section name=card loop=$saved_card}
                        {if ($saved_card[card].url=="0")}
                            <option value='0'>{l s='NEW CARD' mod='paytpv'}</option>
                        {else}
                            <option value='{$saved_card[card].url}'>{$saved_card[card].CC} ({$saved_card[card].BRAND})</option>
                        {/if}
                    {/section}
                </select>
                &nbsp;&nbsp;
                {if ($commerce_password)}
                    <a id="open_directpay" href="#directpay" class="button button-small btn btn-default">          
                        {l s='Pay' mod='paytpv'}
                    </a>
                {else}
                    <a id="exec_directpay" href="#directpay" class="button button-small btn btn-default">          
                        {l s='Pay' mod='paytpv'}
                    </a>
                {/if}
                
                <div id="confirm" style="display:none">
                    <p class="title"></p>
                    <input type="button" class="confirm yes button" value="{l s='Accept' mod='paytpv'}" />
                    <input type="button" class="confirm no button" value="{l s='Cancel' mod='paytpv'}" />
                </div>
            </div>


            <div id="storingStep" class="alert alert-info {if (sizeof($saved_card))>=1}hidden{/if}">
               
                <h4>{l s='STREAMLLINE YOUR FUTURE PURCHASES!' mod='paytpv'}</h4>
                {l s='Link a card to your account to make all procedures easily and quickly.' mod='paytpv'}
                <br>
                <label class="checkbox"><input type="checkbox" name="savecard" id="savecard" checked>{l s='Yes, remember my card accepting ' mod='paytpv'}<a id="open_conditions" href="#conditions">{l s='terms and conditions of service' mod='paytpv'}</a>.</label>


                <a href="javascript:void(0);" onclick="addCardJQ();" title="{l s='NEW CARD' mod='paytpv'}" class="button button-small btn btn-default">
                    {l s='Next' mod='paytpv'}
                </a>
            </div>
                
            
            <br class="clear"/>
            
            <p class="payment_module paytpv_iframe" style="display:none">
                <iframe id="paytpv_iframe" src="" name="paytpv" style="width: 670px; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-style: initial; border-color: initial; border-image: initial; height: 322px; " marginheight="0" marginwidth="0" scrolling="no"></iframe>
            </p>
           
        </div>
    </div>


    <div style="display: none;">
        <div id="directpay" style="overflow:auto;">
            <form name="pago_directo" id="pago_directo" action="" method="post">
                <h1 class="estilo-tit1">{l s='Use Card' mod='paytpv'}</h1>
                <p>
                {l s='Card' mod='paytpv'}:&nbsp
                <strong><span id="datos_tarjeta"></span></strong>
                </p>
                <p>
                    {l s='For security, enter your user password Store' mod='paytpv'}
                </p>
                <p>
                {l s='Password' mod='paytpv'}: <input type="password" name="password" id="password" class="password">
                </p>
                <p class="button_left">
                    <input type="submit" title="{l s='Pay' mod='paytpv'}" class="button button-small btn btn-default" value="{l s='Pay' mod='paytpv'}">
                    </a>
                </p>
            </form>
        </div>
    </div>

    <div style="display: none;">
        <div id="conditions" style="overflow:auto;">
            <h2 class="estilo-tit1">1.- {l s='Subscriptions' mod='paytpv'}</h2>
            <p>
            {l s='This trade does not store or transmit data credit card or debit card. The data are sent via a secure, encrypted channel platform PayTPV.' mod='paytpv'}
            </p>
            <p>
            {l s='At any time, the user can unsubscribe from the product from the "My cards and subscriptions". User subscriptions are displayed and may be canceled if desired.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1">2.- {l s='Related Cards' mod='paytpv'}</h2>
            <p>
            {l s='This trade does not store or transmit data credit card or debit card. The data are sent via a secure, encrypted channel platform PayTPV.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Security Policy' mod='paytpv'}</h2>
            <p>
            {l s='All transaction information is transmitted between this site and PayTPV systems is encrypted using 256-bit SSL certificates. All cardholder information is transmitted encrypted and all messages sent to your servers from PayTPV signed using SHA hashing to prevent tampering. The information is transmitted to servers PayTPV can not probe, scan, used or modified by any external to gain access to confidential information.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Encryption and Data Storage' mod='paytpv'}</h2>
            <p>
            {l s='Once PayTPV systems, confidential information is protected using standard 1024-bit encryption. Encryption keys are kept in high security systems with double authentication volatile and which precludes their extraction. Banks, security agents and banks perform regular audits to ensure data protection.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='System Safety' mod='paytpv'}</h2>
            <p>
            {l s='PayTPV systems are reviewed quarterly by specific tools ISO, a Qualified Qualified Security Assessor (QSA) and an independent approved scanning vendor (ASV) for the payment card brands.
            PayTPV also subject to an annual audit by the standards of data security (PCI DSS) and Payment Card Industry is a provider of payment services fully approved Level 1, which is the highest level of compliance.' mod='paytpv'}
            </p>
            <p>
            {l s='PayTPV also subject to an annual audit by the standards of data security (PCI DSS) and Payment Card Industry is a provider of payment services fully approved Level 1, which is the highest level of compliance.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Links to banks' mod='paytpv'}</h2>
            <p>
            {l s='PayTPV has multiple private links to banking networks that are completely independent of the Internet and which do not cross any public access network. Any cardholder information sent to the banks and all messages sent in response authorization are protected and can not be manipulated.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Internal security' mod='paytpv'}</h2>
            <p>
            {l s='PayTPV is audited access controls production environments. The CPD stay where systems operate according to the requirements for Tier III centers. This ensures that safety is not put at risk at any time. It has sophisticated alarm systems, surveillance and closed circuit TV security guards present 24 hours a day, 7 days a week on site, as well as monitoring and a rigorous maintenance. All transaction information and customer card is protected even our own employees.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Disaster Recovery' mod='paytpv'}</h2>
            <p>
            {l s='PayTPV has hosted Backup systems in different countries to ensure optimal safety systems and high availability. It also has a policy of commercial below and complete disaster recovery.' mod='paytpv'}
            </p>
            <p>&nbsp;</p>
        </div>
    </div>


    <form id="form_paytpv" action="{$base_dir}index.php?controller=order" method="post">
        <input type="hidden" name="step" value="3">
        <input type="hidden" name="paytpv_cc" id="paytpv_cc" value="">

        <input type="hidden" name="paytpv_agree" id="paytpv_agree"  value="0">
        <input type="hidden" name="action_paytpv" id="action_paytpv"  value="">

        <!--SUSCRIPCIONES-->
        <input type="hidden" name="paytpv_suscripcion" id="paytpv_suscripcion"  value="0">
        <input type="hidden" name="paytpv_periodicity" id="paytpv_periodicity"  value="0">
        <input type="hidden" name="paytpv_cycles" id="paytpv_cycles"  value="0">

    </form>
</div>





