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

    .button_right {
        float: right;
        
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
            {if !$showcard && isset($saved_card[0])}
                {l s='Tarjetas disponibles' mod='paytpv'}:
                <select name="card" id="card">
                {section name=card loop=$saved_card}       
                    <option value='{$saved_card[card].url}'>{$saved_card[card].CC} ({$saved_card[card].BRAND})</option>
                {/section}
                </select>
                &nbsp;&nbsp;
                <a id="open_directpay" href="#directpay" class="button button-small btn btn-default">           
                    {l s='Usar tarjeta' mod='paytpv'}
                </a>
                <!--<a href="javascript:void(0);" onclick="jQuery('p.paytpv_iframe').show();" title="{l s='Usar los datos de otra tarjeta de crédito' mod='paytpv'}" class="button button-small btn btn-default">
                    {l s='Nueva tarjeta' mod='paytpv'}
                </a>-->
                <a href="javascript:void(0);" onclick="removeCard();" class="button button-small btn btn-default">
                 {l s='Eliminar tarjeta' mod='paytpv'}
                </a>
              
                <p class="paytpv_tarjetas">{l s='Usar tarjeta: Seleccione la tarjeta almacenada que desea usar para realizar el pago.' mod='paytpv'}<br>{l s='Eliminar tarjeta: Seleccione la tarjeta almacenada que desea eliminar.' mod='paytpv'}</p>
            {/if}

            {if (!$showcard)}
                <div id="storingStep" class="alert alert-info" style="display: block;">
                    <h4>¡Agilice sus futuras compras!</h4>
                    Vincula la tarjeta a tu cuenta para poder hacer todos los trámites de forma ágil y rápida.
                    <br>
                    <label class="checkbox"><input type="checkbox" name="savecard" id="savecard"> Si, recordar mi tarjeta aceptando los <a id="open_conditions" href="#conditions">términos y condiciones del servicio</a>.</label>


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
        <div id="directpay" style="width:450px;height:200px;overflow:auto;">
            <form name="pago_directo" id="pago_directo" action="" method="post">
                <h1 class="estilo-tit1">{l s='Usar Tarjeta' mod='paytpv'}</h1>
                <p>
                {l s='Pago con tarjeta' mod='paytpv'}:&nbsp
                <strong><span id="datos_tarjeta"></span></strong>
                </p>
                <p>
                    {l s='Para mayor seguridad, deberá introducir la contraseña de la tienda' mod='paytpv'}
                </p>
                <p>
                {l s='Contraseña' mod='paytpv'}: <input type="password" name="password" id="password" class="password">
                </p>
                <p class="button_right">
                    <input type="submit" title="{l s='Pagar' mod='paytpv'}" class="button button-small btn btn-default" value="{l s='Pagar' mod='paytpv'}">
                        
                    </a>
                </p>
            </form>
        </div>
    </div>


    <div style="display: none;">
        <div id="conditions" style="width:600px;height:400px;overflow:auto;">
            <h1 class="estilo-tit1">Tarjetas vinculadas</h1>
            <p>
            <abbr title="">Este comercio</abbr> no almacena ni transmite los datos tarjetas
            de crédito o débito. Los datos son enviados a través de un canal cifrado y seguro a la
            plataforma <abbr title="PayTPV On Line S.L.">PAYTPV</abbr>.
            </p>
            <p>
            En cualquier momento, el usuario puede añadir, eliminar los datos de sus tarjetas vinculadas. Haciendo un nuevo pedido se mostrarán las tarjetas almacenadas y tendrá un botón para poder eliminarlas.
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">Política de seguridad</h2>
            <p>
            Toda la información de transacciones que se transmite entre este sitio y los sistemas de PAYTPV se
            cifra mediante certificados SSL de 256 bits. Toda la información del titular se transmite cifrada y todos
            los mensajes enviados a sus servidores desde PAYTPV se firman mediante hashing SHA para evitar su manipulaci´n.
            La información que se transmite a los servidores de PAYTPV no se puede examinar, escanear, utilizar o
            modificar por cualquier externo que obtenga acceso a información confidencial.
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">Cifrado y almacenamiento de datos</h2>
            <p>
            Una vez en los sistemas de PAYTPV, la información confidencial se protege utilizando estándares de
            cifrado de 1024 bits. Las claves de cifrado se mantienen en sistemas de alta seguridad volátiles y con
            doble autenticación lo que imposibilita su extracción. Bancos, agentes de seguridad y entidades
            bancarias realizan auditorías con regularidad para garantizar la protección de los datos.
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">Seguridad en los sistemas</h2>
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
            <h2 class="estilo-tit1" id="politica_seguridad">Enlaces con entidades bancarias</h2>
            <p>
            PAYTPV tiene varios enlaces privados con redes bancarias que son completamente independientes de Internet y que
            no atraviesan ninguna red de acceso público. Toda la información del titular enviada a los bancos
            y todos los mensajes de autorización enviados como respuesta están protegidos y no se pueden
            manipular.
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">Seguridad interna</h2>
            <p>
            PAYTPV está auditado en controles de acceso a los entornos de producción. Los CPD donde se alojan
            los sistemas operan conforme a los requisitos para los centros Tier III. De esta forma se garantiza que la
            seguridad no se ponga en riesgo en ningún momento. Se dispone de sofisticados sistemas de alarma,
            vigilancia mediante circuitos cerrados de TV y vigilantes de seguridad, presentes 24 horas al día y 7 días
            a la semana en las instalaciones, así como de una monitorización y un mantenimiento rigurosos.
            Toda la información de transacciones y tarjetas de clientes está protegida incluso de nuestros
            propios empleados.
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">Recuperación de desastres</h2>
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

        <input type="hidden" name="paytpv_agree" id="paytpv_agree"  value="">
        <input type="hidden" name="action_paytpv" id="action_paytpv"  value="">
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#open_conditions").fancybox();
        $("#open_directpay").fancybox({
                'beforeShow': onOpenDirectPay
            });
    });

    function onOpenDirectPay(){
        $("#datos_tarjeta").html($("#card :selected").text());
        $("#pago_directo").attr("action",$("#card").val());
    }

    function useCard(){
        if (confirm("{l s='Pulse Aceptar para pagar y finalizar el pedido.' mod='paytpv'}"))
            window.location.href = $("#card").val();
    }
    function removeCard(){
        cc = $("#card :selected").text();
        if (confirm("{l s='Esta seguro que desea eliminar la tarjeta ' mod='paytpv'}" + cc)){
            cc = cc.split("(")[0];
            $("#paytpv_cc").val(cc);
            $("#action_paytpv").val("remove");
            $("#form_paytpv").submit();
        }

    }
    function addCard(){
        $("#paytpv_agree").val($("#savecard").attr("checked") ? 1 : 0);
        $("#action_paytpv").val("add");
        $("#form_paytpv").submit();
    }

</script>



