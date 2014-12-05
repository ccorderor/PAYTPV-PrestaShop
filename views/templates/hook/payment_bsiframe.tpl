<script type="text/javascript">
    $(document).ready(function() {
        $("#open_conditions").fancybox({
                autoSize:false,
                'width':parseInt($(window).width() * 0.7)
            });

        $("#open_directpay").fancybox({
                'beforeShow': onOpenDirectPay
            });

        $(".remove_card").on("click", function(e){   
            e.preventDefault();
            cc = $("#card :selected").text();
            confirm("{l s='Eliminar tarjeta' mod='paytpv'}" + ": " + cc, true, function(resp) {
                if (resp)   removeCard();
            });
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
        }else{
            $("#div_periodicity").hide();
            $("#saved_cards").show();
            $("#storingStep").show();
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


    function onOpenDirectPay(){
        $("#datos_tarjeta").html($("#card :selected").text());
        var suscripcion="&suscripcion="+($("#suscripcion").is(':checked')?1:0)+"&periodicity="+$("#susc_periodicity").val()+"&cycles="+$("#susc_cycles").val();
        
        $("#pago_directo").attr("action",$("#card").val()+suscripcion);
    }

    function useCard(){
        if (confirm("{l s='Pulse Aceptar para pagar y finalizar el pedido.' mod='paytpv'}")){
           window.location.href = $("#card").val()+suscripcion;
        }
    }
    function removeCard(){
        cc = $("#card :selected").text();
        
        cc = cc.split("(")[0];
        $("#paytpv_cc").val(cc);
        $("#action_paytpv").val("remove");
        $("#form_paytpv").submit();

    }
    function addCard(){
        $("#paytpv_agree").val($("#savecard").is(':checked')?1:0);
        $("#action_paytpv").val("add");

        $("#form_paytpv").submit();
    }

    function suscribe(){
        $("#paytpv_agree").val(0);
        $("#action_paytpv").val("add");

        $("#paytpv_suscripcion").val(1);
        $("#paytpv_periodicity").val($("#susc_periodicity").val());
        $("#paytpv_cycles").val($("#susc_cycles").val());

        $("#form_paytpv").submit();
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
        padding: 8px 35px 8px 14px;
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
                    <laber for="suscripcion">{l s='¿Desea suscribirse a este producto?' mod='paytpv'}</label>
                    
                </p>
                
                <div id="div_periodicity" class="suscription_period" style="display:none">
                    <div class="nota">{l s='La primera compra se efecturá al realizar el pedido y las siguientes según se haya definido la periodicidad de la suscripción' mod='paytpv'}.
                    {l s='Al suscribirse está aceptando los' mod='paytpv'} <a id="open_conditions" href="#conditions">{l s='términos y condiciones del servicio' mod='paytpv'}</a>.
                    </div>
                    {l s='Periodicidad:' mod='paytpv'} 
                    <select name="susc_periodicity" id="susc_periodicity">
                        <option value="7">{l s='7 dias (Semanal)' mod='paytpv'}</option>
                        <option value="30" selected>{l s='30 dias (Mensual)' mod='paytpv'}</option>
                        <option value="90">{l s='90 dias (Trimestral)' mod='paytpv'}</option>
                        <option value="180">{l s='180 dias (Semestral)' mod='paytpv'}</option>
                        <option value="365">{l s='365 dias (Anual)' mod='paytpv'}</option>
                    </select>
                    &nbsp;&nbsp;
                    {l s='Nº de pagos:' mod='paytpv'} 
                    <select name="susc_cycles" id="susc_cycles">
                        <option value="0" selected>{l s='Indefinido' mod='paytpv'}</option>
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
                    <a href="javascript:void(0);" onclick="suscribe();" title="{l s='Suscribirse' mod='paytpv'}" class="button button-small btn btn-default">
                        {l s='Suscribirse' mod='paytpv'}
                    </a>
                    </span>             
                </div>
            </div>
            <br/>
            {/if}
            
            {if !$showcard && isset($saved_card[0])}
                <div id="saved_cards">
                    {l s='Tarjetas disponibles' mod='paytpv'}:
                    <select name="card" id="card">
                    {section name=card loop=$saved_card}       
                        <option value='{$saved_card[card].url}'>{$saved_card[card].CC} ({$saved_card[card].BRAND})</option>
                    {/section}
                    </select>
                    &nbsp;&nbsp;
                    <a id="open_directpay" href="#directpay" class="button button-small btn btn-default">           
                        {l s='Pagar' mod='paytpv'}
                    </a>
                    <a href="#" class="remove_card button button-small btn btn-default">
                     {l s='Eliminar' mod='paytpv'}
                    </a>

                    <div id="confirm" style="display:none">
                        <p class="title"></p>
                        <input type="button" class="confirm yes button" value="Aceptar" />
                        <input type="button" class="confirm no button" value="Cancelar" />
                    </div>
                </div>

            {/if}

            {if (!$showcard)}
                <div id="storingStep" class="alert alert-info" style="display: block;">
                    <h4>{l s='¡Agilice sus futuras compras!' mod='paytpv'}</h4>
                    {l s='Vincule la tarjeta a su cuenta para poder hacer todos los trámites de forma ágil y rápida.' mod='paytpv'}
                    <br>
                    <label class="checkbox"><input type="checkbox" name="savecard" id="savecard"> Si, recordar mi tarjeta aceptando los <a id="open_conditions" href="#conditions">{l s='términos y condiciones del servicio' mod='paytpv'}</a>.</label>


                    <a href="javascript:void(0);" onclick="addCard();" title="{l s='Nueva Tarjeta de crédito' mod='paytpv'}" class="button button-small btn btn-default">
                        {l s='Nueva tarjeta' mod='paytpv'}
                    </a>
                </div>
                
            {/if}
            <br class="clear"/>

            {if ($showcard)}
                <p class="payment_module paytpv_iframe">
                    <iframe src="https://secure.paytpv.com/gateway/bnkgateway.php?{$query}" name="paytpv" style="width: 670px; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-style: initial; border-color: initial; border-image: initial; height: 322px; " marginheight="0" marginwidth="0" scrolling="no"></iframe>
                </p>
            {/if}
        </div>
    </div>


    <div style="display: none;">
        <div id="directpay" style="overflow:auto;">
            <form name="pago_directo" id="pago_directo" action="" method="post">
                <h1 class="estilo-tit1">{l s='Usar Tarjeta' mod='paytpv'}</h1>
                <p>
                {l s='Tarjeta' mod='paytpv'}:&nbsp
                <strong><span id="datos_tarjeta"></span></strong>
                </p>
                <p>
                    {l s='Para mayor seguridad, deberá introducir la contraseña de la tienda' mod='paytpv'}
                </p>
                <p>
                {l s='Contraseña' mod='paytpv'}: <input type="password" name="password" id="password" class="password">
                </p>
                <p class="button_left">
                    <input type="submit" title="{l s='Pagar' mod='paytpv'}" class="button button-small btn btn-default" value="{l s='Pagar' mod='paytpv'}">
                    </a>
                </p>
            </form>
        </div>
    </div>

    <div style="display: none;">
        <div id="conditions" style="overflow:auto;">
            <h2 class="estilo-tit1">1.- Suscripciones</h2>
            <p>
            <abbr title="">Este comercio</abbr> no almacena ni transmite los datos tarjetas
            de crédito o débito. Los datos son enviados a través de un canal cifrado y seguro a la
            plataforma <abbr title="PayTPV On Line S.L.">PAYTPV</abbr>.
            </p>
            <p>
            En cualquier momento, el usuario podrá cancelar la suscripción al producto desde el apartado "Mis tarjetas y suscripciones". Se mostrarán las suscripciones del usuario y las podrá cancelar si lo desea.
            </p>
            <h2 class="estilo-tit1">2.- Tarjetas vinculadas</h2>
            <p>
            <abbr title="">Este comercio</abbr> no almacena ni transmite los datos tarjetas
            de crédito o débito. Los datos son enviados a través de un canal cifrado y seguro a la
            plataforma <abbr title="PayTPV On Line S.L.">PAYTPV</abbr>.
            </p>
            <p>
            En cualquier momento, el usuario puede añadir ó eliminar los datos de sus tarjetas vinculadas. En el apartado Mi cuenta, verá un apartado "Mis tarjetas y suscripciones" donde se mostrarán las tarjetas almacenadas y podrán ser eliminadas.
            </p>
            <h3 class="estilo-tit1" id="politica_seguridad">Política de seguridad</h3>
            <p>
            Toda la información de transacciones que se transmite entre este sitio y los sistemas de PAYTPV se
            cifra mediante certificados SSL de 256 bits. Toda la información del titular se transmite cifrada y todos
            los mensajes enviados a sus servidores desde PAYTPV se firman mediante hashing SHA para evitar su manipulación.
            La información que se transmite a los servidores de PAYTPV no se puede examinar, escanear, utilizar o
            modificar por cualquier externo que obtenga acceso a información confidencial.
            </p>
            <h3 class="estilo-tit1" id="politica_seguridad">Cifrado y almacenamiento de datos</h3>
            <p>
            Una vez en los sistemas de PAYTPV, la información confidencial se protege utilizando estándares de
            cifrado de 1024 bits. Las claves de cifrado se mantienen en sistemas de alta seguridad volátiles y con
            doble autenticación lo que imposibilita su extracción. Bancos, agentes de seguridad y entidades
            bancarias realizan auditorías con regularidad para garantizar la protección de los datos.
            </p>
            <h3 class="estilo-tit1" id="politica_seguridad">Seguridad en los sistemas</h3>
            <p>
            Los sistemas de PAYTPV se examinan trimestralmente mediante herramientas específicas ISO, un Asesor de
            Seguridad Cualificado (QSA) independiente y un proveedor de exploración aprobado (ASV) por las marcas de
            tarjetas de pago.
            </p>
            <p>
            PAYTPV también se somete a una auditoría anual según los estándares de seguridad de
            datos del sector de tarjetas de pago (PCI DSS) y es un proveedor de servicios de pago de Nivel 1 totalmente
            aprobado, que es el mayor nivel de cumplimiento.
            </p>
            <h3 class="estilo-tit1" id="politica_seguridad">Enlaces con entidades bancarias</h3>
            <p>
            PAYTPV tiene varios enlaces privados con redes bancarias que son completamente independientes de Internet y que
            no atraviesan ninguna red de acceso público. Toda la información del titular enviada a los bancos
            y todos los mensajes de autorización enviados como respuesta están protegidos y no se pueden
            manipular.
            </p>
            <h3 class="estilo-tit1" id="politica_seguridad">Seguridad interna</h3>
            <p>
            PAYTPV está auditado en controles de acceso a los entornos de producción. Los CPD donde se alojan
            los sistemas operan conforme a los requisitos para los centros Tier III. De esta forma se garantiza que la
            seguridad no se ponga en riesgo en ningún momento. Se dispone de sofisticados sistemas de alarma,
            vigilancia mediante circuitos cerrados de TV y vigilantes de seguridad, presentes 24 horas al día y 7 días
            a la semana en las instalaciones, así como de una monitorización y un mantenimiento rigurosos.
            Toda la información de transacciones y tarjetas de clientes está protegida incluso de nuestros
            propios empleados.
            </p>
            <h3 class="estilo-tit1" id="politica_seguridad">Recuperación de desastres</h3>
            <p>
            PAYTPV dispone de sistemas de Backup alojados en diferentes países para garantizar una seguridad de
            sistemas óptima y una alta disponibilidad. Asimismo, cuenta con una política de continuación
            comercial y recuperación de desastres completa.
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





