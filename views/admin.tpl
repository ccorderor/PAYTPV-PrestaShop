    {$errorMessage}

    <img src="{$base_dir}modules/paytpv/paytpv.png" style="float:left; margin-right:15px;"><b>{l s='This module allows you to accept card payments.' mod='paytpv'}</b><br /><br />
            {l s='If the customer chooses this payment method, the customer could pay automatically.' mod='paytpv'}<br /><br /><br />
    <form action="{$serverRequestUri|strip_tags}" method="post">
    	<fieldset>
    		<legend>{l s='Product Configuration paytpv.com' mod='paytpv'}</legend>
    		<p>{l s='Please complete the information requested. You can get the data through client platform PayTPV.' mod='paytpv'}</p>
            <label for="operativa" id="lbloperativa">{l s='Operation method' mod='paytpv'}</label>
            <div class="margin-form">
                <select name="operativa" onchange="checkoperativa();" id="operativa">
                    <option value="1" {if $operativa==1}selected="1"{/if}>TPV WEB</option>
                    <option value="0" {if $operativa==0}selected="1"{/if}>BANKSTORE</option>
                </select>
            </div>

            <div id="iframe_container">
                <label>{l s='Integration method' mod='paytpv'}</label>
                <div class="margin-form">
                    <select name="iframe">
                            <option value="0" {if $iframe==0}selected="1"{/if}>Full screen</option>
                            <option value="1" {if $iframe==1}selected="1"{/if}>Iframe</option>
                    </select>
                </div>
            </div>

            <div id="terminales_container">
                <label for="terminales" id="lblterminales">{l s='Terminals available' mod='paytpv'}</label>
                <div class="margin-form">
                    <select name="terminales" onchange="checkterminales();" id="terminales" >
                        <option value="0" {if $terminales==0} selected="1"{/if}>{l s='Secure' mod='paytpv'}</option>
                        <option value="1" {if $terminales==1} selected="1"{/if}>{l s='No Secure' mod='paytpv'}</option>
                        <option value="2" {if $terminales==2} selected="1"{/if}>{l s='Both' mod='paytpv'}</option>
                    </select>
                </div>
            </div>

            <div id="tdfirst_container">
                <label for="tdfirst" id="lbltdfirst">{l s='Use 3D Secure' mod='paytpv'}</label>
                <div class="margin-form">
                    <select name="tdfirst" onchange="checktdfirst();" id="tdfirst">
                        <option value="0" {if $tdfirst==0}selected="1"{/if}>{l s='No' mod='paytpv'}</option>
                        <option value="1" {if $tdfirst==1}selected="1"{/if}>{l s='Yes' mod='paytpv'}</option>
                    </select>
                </div>
            </div>

            <div id="tdmin_container">
                <label for="tdmin" id="lbltdmin">{l s='Use 3D Secure in purchases above' mod='paytpv'}</label>
                <div class="margin-form"><input type="number" step="0.01" size="60" name="tdmin" id="tdmin" value="{$tdmin}" style="width:120px;text-align:right"/>&euro;</div>
            </div>

            <br/>
            <div id="commerce_password_container">
                <label for="commerce_password_container" id="lblcommerce_password_container">{l s='Commerce password required in saved cards purchase' mod='paytpv'}</label>
                <div class="margin-form">
                    <select name="commerce_password" id="commerce_password">
                        <option value="0" {if $commerce_password==0}selected="1"{/if}>{l s='No' mod='paytpv'}</option>
                        <option value="1" {if $commerce_password==1}selected="1"{/if}>{l s='Yes' mod='paytpv'}</option>
                    </select>
                </div>
            </div>

            <br/>
            <div id="usercode_container">
                <label for="usercode">{l s='User Name' mod='paytpv'}</label>
                <div class="margin-form"><input type="text" size="60" name="usercode" id="usercode" value="{$usercode}" /></div>
            </div>

    		<label>{l s='User Password' mod='paytpv'}</label>
    		<div class="margin-form"><input type="text" size="60" name="pass" value="{$pass}" /></div>

    		<label>{l s='Terminal number' mod='paytpv'}</label>
    		<div class="margin-form"><input type="text" size="60" name="term" value="{$term}" /></div>

    		<label>{l s='Client Code' mod='paytpv'}</label>
    		<div class="margin-form"><input type="text" size="60" name="clientcode" value="{$clientcode}" /></div>

            <div id="suscriptions_container" style="display:none">
                <label>{l s='Active Subscriptions' mod='paytpv'}</label>
                <div class="margin-form">
                    <select name="suscriptions" id="suscriptions">
                        <option value="0" {if $suscriptions==0}selected="1"{/if}>{l s='No' mod='paytpv'}</option>
                        <option value="1" {if $suscriptions==1}selected="1"{/if}>{l s='Yes' mod='paytpv'}</option>
                    </select>
                </div>	
            </div>
    	</fieldset>	

        <br/>

        <fieldset style="display:none">
            <legend><img src="../img/t/AdminPreferences.gif" />{l s='Customization' mod='paytpv'}</legend>  
            {l s='Please complete the additional data.' mod='paytpv'}
            <div class="margin-form"><p class="clear"></p></div>
            <label>{l s='Enable logging of failed / incomplete transactions' mod='paytpv'}</label>
            <div class="margin-form">
                <input type="radio" name="reg_estado" id="reg_estado_si" value="1" {if $reg_estado==1}checked="checked"{/if}/>
                <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='paytpv'}" title="{l s='Enabled' mod='paytpv'}" />
                <input type="radio" name="reg_estado" id="reg_estado_no" value="0" {if $reg_estado!=1}checked="checked"{/if}/>
                <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='paytpv'}" title="{l s='Disabled' mod='paytpv'}" />
                <p class="clear"></p>
            </div>
        </fieldset>

        <br/>

    	<center><input type="submit" id="btnSubmit" class="button" name="btnSubmit" value="{l s='Save Settings' mod='paytpv' mod='paytpv'}" /></center>

    	<div>
            <p><H1>{l s='IMPORTANT' mod='paytpv'}</H1></p>
    		<p><strong>{l s='Finally you need to configure your account <a href="https://www.paytpv.com"> PayTPV </a> the following URLs:' mod='paytpv'}</strong>
    			<ul>
    				<li><strong>URLOK:</strong> {$OK}</li>
    				<li><strong>URLKO:</strong> {$KO}</li>
    				<li><strong>URL NOTIFICACION:</strong> {$NOTIFICACION}</li>				
    			</ul>
    		</p>
    	</div>

    </form>

    {if $reg_estado==1}

    <br /><br /><br />

    <form action="'.$_SERVER['REQUEST_URI'].'" method="post">
        <fieldset>
            <legend><img src="../img/admin/contact.gif" alt="" title="" />{l s='Transactions with errors' mod='paytpv'}</legend>
            <table class="table">
                <thead>
                    <tr>
                        <th class="item" style="text-align:center;width:150px;">{l s='Date' mod='paytpv'}</th>
                        <th class="item" style="width:325px;">{l s='Client' mod='paytpv'}</th>
                        <th class="item" style="text-align:center;width:75px;">{l s='Amount' mod='paytpv'}</th>
                        <th class="item" style="text-align:center;width:300px;">{l s='Error type' mod='paytpv'}</th>
                        <th class="item" style="text-align:center;width:50px;">{l s='Actions' mod='paytpv'}</th>
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
                            <img onClick="document.location ={$currentindex}&configure={$name}&token={$token}&amount={$registro['amount']}&id_cart={$registro['id_cart']}&id_registro={$registro['id_registro']}';" src="../img/admin/add.gif" style="cursor:pointer" alt="{l s='Create Order' mod='paytpv'}" title="{l s='Create Order' mod='paytpv'}" />
                            
                            <img onClick='if (confirm("{l s='Delete this payment error?' mod='paytpv'}")) document.location = {$currentindex}&configure={$name}&token={$token}&id_registro={$registro['id_registro']}';' style="cursor:pointer; margin-left:10px;" src="../img/admin/disabled.gif" alt="{l s='Remove record' mod='paytpv'}" title="{l s='Remove record' mod='paytpv'}" />
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
                jQuery("#tdfirst_container").show();
                if(jQuery("#terminales").val() == 2)
                    jQuery("#tdmin_container").show();
                else
                    jQuery("#tdmin_container").hide();
                
                jQuery("#terminales_container").show();
                jQuery("#suscriptions_container").show();
                jQuery("#commerce_password_container").show();
                
            }else{
                jQuery("#usercode_container").show();
                jQuery("#iframe_container").show();
                jQuery("#tdfirst_container").hide();
                jQuery("#tdmin_container").hide();
                jQuery("#terminales_container").hide(); 
                jQuery("#suscriptions_container").hide(); 
                jQuery("#commerce_password_container").hide();
              }
        }

        function checkterminales(){
            // Si solo tiene terminal seguro o tiene los dos la primera compra va por seguro
            // Seguro
            switch (jQuery("#terminales").val()){
                case "0": // SEGURO
                    jQuery("#tdfirst").val(1);
                    jQuery("#tdmin_container").hide();
                    break;
                case "1": // NO SEGURO
                    jQuery("#tdfirst").val(0);
                    jQuery("#tdmin_container").hide();
                    break;
                case "2": // AMBOS
                    if (jQuery("#tdfirst").val()==0)
                        jQuery("#tdmin_container").show();
                    else
                        jQuery("#tdmin_container").hide();
                    break;
                
            }
        }

        function checktdfirst(){
            // Si solo tiene terminal seguro la primera compra va por seguro
            if(jQuery("#terminales").val() == 0 && jQuery("#tdfirst").val()==0){
                alert("{l s='If you only have a secure terminal payments are always Secure' mod='paytpv'}");
                jQuery("#tdfirst").val(1);
            }
            // Si solo tiene terminal no seguro la primera compra va por seguro
            if(jQuery("#terminales").val() == 1 && jQuery("#tdfirst").val()==1){
                alert("{l s='If you have only a No Secure terminal payments are always Unsure' mod='paytpv'}");
                jQuery("#tdfirst").val(0);
            }

            if(jQuery("#terminales").val() == 2){
                if (jQuery("#tdfirst").val()==0)
                    jQuery("#tdmin_container").show();
                else
                    jQuery("#tdmin_container").hide();
            }

        }

        checkoperativa();
        checkterminales();

    </script>

