{*
* 2007-2013 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">

    $(document).ready(function() {
        $("#open_conditions").fancybox({
                autoSize:false,
                'width':parseInt($(window).width() * 0.7)
            });
        
        $(".remove_card").on("click", function(e){   
            e.preventDefault();
            $("#paytpv_cc").val($(this).attr("id"));
            confirm("{l s='Eliminar tarjeta' mod='paytpv'}" + ": " + $(this).attr("id"), true, function(resp) {
                if (resp)   removeCard();
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
            alert("{l s='Debe aceptar los términos y condiciones del servicio' mod='paytpv'}");
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
		paytpv_cc = $("#paytpv_cc").val();
		$.ajax({
			url: "{$link->getModuleLink('paytpv', 'actions', ['process' => 'remove'], true)|addslashes}",
			type: "POST",
			data: {
				'paytpv_cc': paytpv_cc,
				'ajax': true
			},
			success: function(result)
			{
				if (result == '0')
				{
					$("#card_"+paytpv_cc).fadeOut(1000);
				}
 		 	}
		});
		
	};

</script>

<style>
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
    .terminos
    {
        color:#ff6000;
    }

    .button_del{
    	float:right;
    }

    .bankstoreCard {
		border: 1px solid #e5e5e5;
		border-radius: 4px;
		margin-bottom: 10px;
		padding: 20px;
		color: #a6a6a6;
		}

	.remove_card{
		color: #ff6000!important;
	}
</style>

{capture name=path}
	<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">
		{l s='Mis tarjetas vinculadas' mod='paytpv'}</a>
		<span class="navigation-pipe">{$navigationPipe}</span>{l s='Mis tarjetas vinculadas.' mod='paytpv'}
{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<div id="paytpv_block_account">
	<h2>{l s='Mis tarjetas vinculadas.' mod='paytpv'}</h2>
	{if isset($saved_card[0])}
		<div class="span6" id="div_tarjetas">
            {l s='Tarjetas disponibles' mod='paytpv'}:
            {section name=card loop=$saved_card}   
                <div class="bankstoreCard" id="card_{$saved_card[card].CC}">  
    	            {$saved_card[card].CC} ({$saved_card[card].BRAND})
    	            <label class="button_del">
    		            <a href="#" id="{$saved_card[card].CC}" class="remove_card">
    			         {l s='Eliminar tarjeta' mod='paytpv'}
    			        </a>
    		    	</label>
            	</div>
            {/section}
    	</div>

        <div id="confirm" style="display:none">
            <p class="title"></p>
            <input type="button" class="confirm yes button" value="{l s='Aceptar' mod='paytpv'}" />
            <input type="button" class="confirm no button" value="{l s='Cancelar' mod='paytpv'}" />
            <input type="hidden" name="patypv_cc" id="paytpv_cc">
        </div>
    </div>
	{else}
		<p class="warning">{l s='No tiene asociado todavía ninguna tarjeta. ' mod='paytpv'}</p>
	{/if}


    <div id="alert">
            <p class="title"></p>
    </div>

	<div id="storingStep" class="alert alert-info" style="display: block;">
        <h4>{l s='¡Agilice sus futuras compras!' mod='paytpv'}</h4>
        {l s='Vincule una tarjeta a su cuenta para poder hacer todos los trámites de forma ágil y rápida.' mod='paytpv'}
        <br>
        <label class="checkbox"><input type="checkbox" name="savecard" id="savecard"> {l s='Al vincular una tarjet acepta los' mod='paytpv'} <a id="open_conditions" href="#conditions"><span class="terminos">{l s='términos y condiciones del servicio' mod='paytpv'}</span></a>.</label>


        <a href="javascript:void(0);" onclick="vincularTarjeta();" title="{l s='Vincular Tarjeta de crédito' mod='paytpv'}" class="button button-small btn btn-default">
            {l s='Vincular tarjeta' mod='paytpv'}
        </a>
        <a href="javascript:void(0);" onclick="close_vincularTarjeta();" title="{l s='Cancelar' mod='paytpv'}" class="button button-small btn btn-default" id="close_vincular" style="display:none">
	            {l s='Cancelar' mod='paytpv'}
	    </a>

        <p class="payment_module paytpv_iframe" id="nueva_tarjeta" style="display:none">
            <iframe src="https://secure.paytpv.com/gateway/bnkgateway.php?{$query}" name="paytpv" style="width: 670px; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-style: initial; border-color: initial; border-image: initial; height: 322px; " marginheight="0" marginwidth="0" scrolling="no"></iframe>
        </p>
    </div>

    <div style="display: none;">
        <div id="conditions" style="overflow:auto;">
            <h1 class="estilo-tit1">Tarjetas vinculadas</h1>
            <p>
            <abbr title="">Este comercio</abbr> no almacena ni transmite los datos tarjetas
            de crédito o débito. Los datos son enviados a través de un canal cifrado y seguro a la
            plataforma <abbr title="PayTPV On Line S.L.">PAYTPV</abbr>.
            </p>
            <p>
           En cualquier momento, el usuario puede añadir ó eliminar los datos de sus tarjetas vinculadas. En el apartado Mi cuenta, verá un apartado "Mis tarjetas vinculadas" donde se mostrarán las tarjetas almacenadas y podrán ser eliminadas.
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">Política de seguridad</h2>
            <p>
            Toda la información de transacciones que se transmite entre este sitio y los sistemas de PAYTPV se
            cifra mediante certificados SSL de 256 bits. Toda la información del titular se transmite cifrada y todos
            los mensajes enviados a sus servidores desde PAYTPV se firman mediante hashing SHA para evitar su manipulación.
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


	<ul class="footer_links">
		<li class="fleft">
			<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}"><img src="{$img_dir}icon/my-account.gif" alt="" class="icon" /></a>
			<a href="{$link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8'}">{l s='Mi cuenta.' mod='paytpv'}</a></li>
	</ul>
</div>