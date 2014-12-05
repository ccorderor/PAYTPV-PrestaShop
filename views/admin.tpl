    {$errorMessage}

    <img src="{$base_dir}modules/paytpv/paytpv.png" style="float:left; margin-right:15px;"><b>{l s='Este módulo te permite aceptar pagos con tarjeta.' mod='paytpv'}</b><br /><br />
            {l s='Si el cliente elije este modo de pago, podrá pagar de forma automática.' mod='paytpv'}<br /><br /><br />
    <form action="{$serverRequestUri|strip_tags}" method="post">
    	<fieldset>
    		<legend>{l s='Configuración del producto paytpv.com' mod='paytpv'}</legend>
    		<p>{l s='Por favor complete la información requerida. Puede obtener los datos a través de la plataforma de cliente de PayTPV.' mod='paytpv'}</p>
            <label for="operativa" id="lbloperativa">{l s='Tipo de operativa' mod='paytpv'}</label>
            <div class="margin-form">
                <select name="operativa" onchange="checkoperativa();" id="operativa">
                    <option value="1" {if $operativa==1}selected="1"{/if}>TPV WEB</option>
                    <option value="0" {if $operativa==0}selected="1"{/if}>BANKSTORE</option>
                </select>
            </div>

            <div id="iframe_container">
                <label>{l s='Tipo de integración' mod='paytpv'}</label>
                <div class="margin-form">
                    <select name="iframe">
                            <option value="0" {if $iframe==0}selected="1"{/if}>Full screen</option>
                            <option value="1" {if $iframe==1}selected="1"{/if}>Iframe</option>
                    </select>
                </div>
            </div>

            <div id="terminales_container">
                <label for="terminales" id="lblterminales">{l s='Terminales disponibles' mod='paytpv'}</label>
                <div class="margin-form">
                    <select name="terminales" onchange="checkterminales();" id="terminales" >
                        <option value="0" {if $terminales==0} selected="1"{/if}>{l s='Seguro' mod='paytpv'}</option>
                        <option value="1" {if $terminales==1} selected="1"{/if}>{l s='No Seguro' mod='paytpv'}</option>
                        <option value="2" {if $terminales==2} selected="1"{/if}>{l s='Ambos' mod='paytpv'}</option>
                    </select>
                </div>
            </div>

            <div id="3dfirst_container">
                <label for="3dfirst" id="lbl3dfirst">{l s='3D Secure en la primera compra' mod='paytpv'}</label>
                <div class="margin-form">
                    <select name="3dfirst" onchange="check3dfirst();" id="3dfirst">
                        <option value="0" {if $3dfirst==0}selected="1"{/if}>No</option>
                        <option value="1" {if $3dfirst==1}selected="1"{/if}>Si</option>
                    </select>
                </div>
            </div>

            <div id="usercode_container">
                <label for="usercode">{l s='Nombre de usuario' mod='paytpv'}</label>
                <div class="margin-form"><input type="text" size="60" name="usercode" id="usercode" value="{$usercode}" /></div>
            </div>

    		<label>{l s='Contraseña' mod='paytpv'}</label>
    		<div class="margin-form"><input type="text" size="60" name="pass" value="{$pass}" /></div>

    		<label>{l s='Número de terminal' mod='paytpv'}</label>
    		<div class="margin-form"><input type="text" size="60" name="term" value="{$term}" /></div>

    		<label>{l s='Código de cliente' mod='paytpv'}</label>
    		<div class="margin-form"><input type="text" size="60" name="clientcode" value="{$clientcode}" /></div>

            <div id="suscriptions_container" style="display:none">
                <label>{l s='Activar Suscripciones' mod='paytpv'}</label>
                <div class="margin-form">
                    <select name="suscriptions" id="suscriptions">
                        <option value="0" {if $suscriptions==0}selected="1"{/if}>No</option>
                        <option value="1" {if $suscriptions==1}selected="1"{/if}>Si</option>
                    </select>
                </div>	
            </div>
    	</fieldset>	

        <br/>

        <fieldset style="display:none">
            <legend><img src="../img/t/AdminPreferences.gif" />{l s='Personalización' mod='paytpv'}</legend>  
            {l s='Por favor completa los datos adicionales.' mod='paytpv'}
            <div class="margin-form"><p class="clear"></p></div>
            <label>{l s='Activar registro de transacciones fallidas/incompletas' mod='paytpv'}</label>
            <div class="margin-form">
                <input type="radio" name="reg_estado" id="reg_estado_si" value="1" {if $reg_estado==1}checked="checked"{/if}/>
                <img src="../img/admin/enabled.gif" alt="{l s='Activado' mod='paytpv'}" title="{l s='Activado' mod='paytpv'}" />
                <input type="radio" name="reg_estado" id="reg_estado_no" value="0" {if $reg_estado!=1}checked="checked"{/if}/>
                <img src="../img/admin/disabled.gif" alt="{l s='Desactivado' mod='paytpv'}" title="{l s='Desactivado' mod='paytpv'}" />
                <p class="clear"></p>
            </div>
        </fieldset>

        <br/>

    	<center><input type="submit" id="btnSubmit" class="button" name="btnSubmit" value="{l s='Save Settings' mod='paytpv' mod='paytpv'}" /></center>

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
            <legend><img src="../img/admin/contact.gif" alt="" title="" />{l s='Operaciones con errores' mod='paytpv'}</legend>
            <table class="table">
                <thead>
                    <tr>
                        <th class="item" style="text-align:center;width:150px;">{l s='Fecha' mod='paytpv'}</th>
                        <th class="item" style="width:325px;">{l s='Cliente' mod='paytpv'}</th>
                        <th class="item" style="text-align:center;width:75px;">{l s='Importe' mod='paytpv'}</th>
                        <th class="item" style="text-align:center;width:300px;">{l s='Tipo error' mod='paytpv'}</th>
                        <th class="item" style="text-align:center;width:50px;">{l s='Acciones' mod='paytpv'}</th>
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
                            <img onClick="document.location ={$currentindex}&configure={$name}&token={$token}&amount={$registro['amount']}&id_cart={$registro['id_cart']}&id_registro={$registro['id_registro']}';" src="../img/admin/add.gif" style="cursor:pointer" alt="{l s='Crear Pedido' mod='paytpv'}" title="{l s='Crear Pedido' mod='paytpv'}" />
                            
                            <img onClick='if (confirm("{l s='Desea eliminar este error en el pago?' mod='paytpv'}")) document.location = {$currentindex}&configure={$name}&token={$token}&id_registro={$registro['id_registro']}';' style="cursor:pointer; margin-left:10px;" src="../img/admin/disabled.gif" alt="{l s='Eliminar registro' mod='paytpv'}" title="{l s='Eliminar registro' mod='paytpv'}" />
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
                jQuery("#iframe_container").hide();
                jQuery("#3dfirst_container").show();
                jQuery("#terminales_container").show();
                jQuery("#suscriptions_container").show();    
            }else{
                jQuery("#usercode_container").show();
                jQuery("#iframe_container").show();
                jQuery("#3dfirst_container").hide();
                jQuery("#terminales_container").hide(); 
                jQuery("#suscriptions_container").hide(); 
              }
        }

        function checkterminales(){
            // Si solo tiene terminal seguro o tiene los dos la primera compra va por seguro
            // Seguro
            switch (jQuery("#terminales").val()){
                case "0": // SEGURO
                case "2": // AMBOS
                    jQuery("#3dfirst").val(1);
                    break;
                case "1": // NO SEGURO
                    jQuery("#3dfirst").val(0);
                    break;
            }
        }

        function check3dfirst(){
            // Si solo tiene terminal seguro la primera compra va por seguro
            if(jQuery("#terminales").val() == 0 && jQuery("#3dfirst").val()==0){
                alert("{l s='Si solo tiene un terminal seguro los pagos van siempre por seguro' mod='paytpv'}");
                jQuery("#3dfirst").val(1);
            }
            // Si solo tiene terminal seguro la primera compra va por seguro
            if(jQuery("#terminales").val() == 1 && jQuery("#3dfirst").val()==1){
                alert("{l s='Si solo tiene un terminal NO seguro los pagos van siempre por NO seguro' mod='paytpv'}");
                jQuery("#3dfirst").val(0);
            }

            // Si solo tiene terminal seguro la primera compra va por seguro
            if(jQuery("#terminales").val() == 2 && jQuery("#3dfirst").val()==0){
                alert("{l s='Si tiene un terminal Seguro y otro No Seguro la primera compra va por seguro' mod='paytpv'}");
                jQuery("#3dfirst").val(1);
            }
        }

        checkoperativa();
        checkterminales();

    </script>

