{$errorMessage}
<img src="{$base_dir}modules/paytpv/paytpv.png" style="float:left; margin-right:15px;"><b>{l s='Este módulo te permite aceptar pagos con tarjeta.'}</b><br /><br />
        {l s='Si el cliente elije este modo de pago, podrá pagar de forma automática.'}<br /><br /><br />
<form action="{$serverRequestUri|strip_tags}" method="post">
	<fieldset>
		<legend>{l s='Configuración del producto paytpv.com'}</legend>
		<p>{l s='Por favor complete la información requerida. Puede obtener los datos a través de la plataforma de cliente de PayTPV.'}</p>
        <label for="operativa" id="lbloperativa">{l s='Tipo de operativa'}</label>
        <div class="margin-form">
            <select name="operativa" onchange="checkoperativa();" id="operativa">
                <option value="1" {if $operativa==1}selected="1"{/if}>TPV WEB</option>
                <option value="0" {if $operativa==0}selected="1"{/if}>BANKSTORE</option>
            </select>
        </div>
        <div id="usercode_container">
            <label for="usercode">{l s='Nombre de usuario'}</label>
            <div class="margin-form"><input type="text" size="60" name="usercode" id="usercode" value="{$usercode}" /></div>
        </div>
		<label>{l s='Contraseña'}</label>
		<div class="margin-form"><input type="text" size="60" name="pass" value="{$pass}" /></div>
		<label>{l s='Número de terminal'}</label>
		<div class="margin-form"><input type="text" size="60" name="term" value="{$term}" /></div>
		<label>{l s='Código de cliente'}</label>
		<div class="margin-form"><input type="text" size="60" name="clientcode" value="{$clientcode}" /></div>		
		<label>{l s='Tipo de integración'}</label>
        <div class="margin-form">
            <select name="iframe">
                    <option value="0" {if $iframe==0}selected="1"{/if}>Full screen</option>
                    <option value="1" {if $iframe==1}selected="1"{/if}>Iframe</option>
            </select>
        </div>
	</fieldset>	
    <br/>
    <fieldset style="display:none">
        <legend><img src="../img/t/AdminPreferences.gif" />{l s='Personalización'}</legend>  
        {l s='Por favor completa los datos adicionales.'}
        <div class="margin-form"><p class="clear"></p></div>
        <label>{l s='Activar registro de transacciones fallidas/incompletas'}</label>
        <div class="margin-form">
            <input type="radio" name="reg_estado" id="reg_estado_si" value="1" {if $reg_estado==1}checked="checked"{/if}/>
            <img src="../img/admin/enabled.gif" alt="{l s='Activado'}" title="{l s='Activado'}" />
            <input type="radio" name="reg_estado" id="reg_estado_no" value="0" {if $reg_estado!=1}checked="checked"{/if}/>
            <img src="../img/admin/disabled.gif" alt="{l s='Desactivado'}" title="{l s='Desactivado'}" />
            <p class="clear"></p>
        </div>
    </fieldset>
    <br/>
	<center><input type="submit" id="btnSubmit" class="button" name="btnSubmit" value="{l s='Save Settings' mod='paytpv'}" /></center>
	<div>
		<p>Por último tendrá que configurar en su cuenta de <a href="https://www.paytpv.com">PayTPV</a> la siguientes URLs:
			<ul>
				<li>URLOK: {$OK}</li>
				<li>URLKO: {$KO}</li>
				<li>URL Notificación: {$OK}</li>				
			</ul>
		</p>
	</div>
</form>
{if $reg_estado==1}
<br /><br /><br />
<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
    <fieldset>
        <legend><img src="../img/admin/contact.gif" alt="" title="" />{l s='Operaciones con errores'}</legend>
        <table class="table">
            <thead>
                <tr>
                    <th class="item" style="text-align:center;width:150px;">{l s='Fecha'}</th>
                    <th class="item" style="width:325px;">{l s='Cliente'}</th>
                    <th class="item" style="text-align:center;width:75px;">{l s='Importe'}</th>
                    <th class="item" style="text-align:center;width:300px;">{l s='Tipo error'}</th>
                    <th class="item" style="text-align:center;width:50px;">{l s='Acciones'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$carritos item=registro}
                <tr>
                    <td class="first_item" style="text-align:center;">{$registro['date_add']}</td>
                    <td class="item" style="text-align:left;"><span>{$registro['customer_firstname']} {$registro['customer_lastname']}</span></td>
                    <td class="item" style="text-align:center;">{$registro['amount']}</td>
                    <td class="item" style="text-align:left;">{$registro['error_code']}</td>
                    <td class="center">
                        <img onClick="document.location = {$currentindex}&configure={$name}&token={$token}&amount={$registro['amount']}&id_cart={$registro['id_cart']}&id_registro={$registro['id_registro']}';" src="../img/admin/add.gif" style="cursor:pointer" alt="{l s='Crear Pedido'}" title="{l s='Crear Pedido'}" />
                        <img onClick='if (confirm("{l s='Desea eliminar este error en el pago?'}")) document.location = {$currentindex}&configure={$name}&token={$token}&id_registro={$registro['id_registro']}';' style="cursor:pointer; margin-left:10px;" src="../img/admin/disabled.gif" alt="{l s='Eliminar registro'}" title="{l s='Eliminar registro'}" />
                    </td>
                </tr>
            {/foreach}
            </tbody>
            </table>
            </fieldset>    
    {/if}	
</form>
<script>
    function checkoperativa(){
        if(jQuery("#operativa").val() == 0){
            jQuery("#usercode_container").hide();
        }else{
            jQuery("#usercode_container").show();
        }
    }
    checkoperativa();
</script>
