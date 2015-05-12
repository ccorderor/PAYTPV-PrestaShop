{*
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
*}

    {$errorMessage}

    <img src="{$base_dir}modules/paytpv/views/img/paytpv.png" style="float:left; margin-right:15px;"><b>{l s='This module allows you to accept card payments via paytpv.com.' mod='paytpv'}</b><br /><br />
            {l s='If the customer chooses this payment method, they will be able to make payments automatically.' mod='paytpv'}<br /><br /><br />
    <div>
        <p><H1>{l s='PRERREQUISTES' mod='paytpv'}</H1></p>
            <ul>
                <li>{l s='The store must be installed on-line, NOT in Local in order to use this module' mod='paytpv'}</li>
                <li>{l s='The PayTPV server must be accessible. (check that there are no problems when there is a firewall)' mod='paytpv'}</li>
            </ul>
        </p>
    </div>
    <form action="{$serverRequestUri|strip_tags}" method="post">
    	<fieldset>
    		<legend>{l s='Paytpv.com Product Configuration' mod='paytpv'}</legend>
    		<p>{l s='Please complete the information requested. You can obtain information on the PayTPV product.' mod='paytpv'}</p>

            <label for="environment" id="lblenvironment">{l s='Environment' mod='paytpv'}</label>
            <div class="margin-form">
                <select name="environment" id="environment" onchange="checkenvironment();">
                    <option value="0" {if $environment==0}selected="1"{/if}>{l s='Live Mode' mod='paytpv'}</option>
                    <option value="1" {if $environment==1}selected="1"{/if}>{l s='Test Mode' mod='paytpv'}</option>
                </select>
                <div id="test_mode">
                    {l s='Test PayTPV module without PayTPV account.' mod='paytpv'}<br/>
                    {l s='Test Cards: 5325298401138208 / 5540568785541245 / 5407696658785988.' mod='paytpv'}<br/>
                    {l s='Expiration Date: Month: 5 / Year: 2020' mod='paytpv'}<br/>
                    {l s='CVC2: 123 / 3DSecure: 1234' mod='paytpv'}
                </div>
            </div>

            <div id="terminales_container">
                <label for="terminales" id="lblterminales">{l s='Terminals available' mod='paytpv'}</label>
                <div class="margin-form">
                    <select name="terminales" onchange="checkterminales();" id="terminales" >
                        <option value="0" {if $terminales==0} selected="1"{/if}>{l s='Secure' mod='paytpv'}</option>
                        <option value="1" {if $terminales==1} selected="1"{/if}>{l s='Non-Secure' mod='paytpv'}</option>
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
                <label for="tdmin" id="lbltdmin">{l s='Use 3D Secure on purchases over' mod='paytpv'}</label>
                <div class="margin-form"><input type="number" step="0.01" size="60" name="tdmin" id="tdmin" value="{$tdmin}" style="width:120px;text-align:right"/>&euro;</div>
            </div>

            <br/>
            <div id="commerce_password_container">
                <label for="commerce_password_container" id="lblcommerce_password_container">{l s='Request business password on purchases with stored cards' mod='paytpv'}</label>
                <div class="margin-form">
                    <select name="commerce_password" id="commerce_password">
                        <option value="0" {if $commerce_password==0}selected="1"{/if}>{l s='No' mod='paytpv'}</option>
                        <option value="1" {if $commerce_password==1}selected="1"{/if}>{l s='Yes' mod='paytpv'}</option>
                    </select>
                </div>
            </div>

            <br/>
            
            <div id="real_mode">
        		<label>{l s='User Password' mod='paytpv'}</label>
        		<div class="margin-form"><input type="text" size="60" name="pass" value="{$pass}" /></div>

        		<label>{l s='Terminal Number' mod='paytpv'}</label>
        		<div class="margin-form"><input type="text" size="60" name="term" value="{$term}" /></div>

        		<label>{l s='Client Code' mod='paytpv'}</label>
        		<div class="margin-form"><input type="text" size="60" name="clientcode" value="{$clientcode}" /></div>
            </div>

            <div id="suscriptions_container">
                <label>{l s='Activate Subscriptions' mod='paytpv'}</label>
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

    	<center><input type="submit" id="btnSubmit" class="button" name="btnSubmit" value="{l s='Save Configuration' mod='paytpv' mod='paytpv'}" /></center>

    	<div>
            <p class="important">{l s='IMPORTANT' mod='paytpv'}</p>
    		<p><strong>{l s='Finally you need to configure in your account' mod='paytpv'} <a class='link' target="_blank" href="https://www.paytpv.com/clientes.php"> PayTPV </a>{l s='the following URLs for the payment module to work properly' mod='paytpv'}:</strong>
            </p>
			<ul class="paytpv">
				<li><strong>URL OK:</strong> {$OK}</li>
				<li><strong>URL KO:</strong> {$KO}</li>
                <li><strong>{l s='Type of Notification (IMPORTANT)' mod='paytpv'}:</strong> {l s='Notification via URL or Notification via URL and email' mod='paytpv'}
                    <ul class="paytpv">
                        <li><strong>URL NOTIFICACION:</strong> {$NOTIFICACION}</li>
                    </ul>
                </li>		
			</ul>

    	</div>

        <div>
            <p class="important">{l s='USER DOCUMENTATION' mod='paytpv'}</p>
            <p><strong>{l s='Link to documentation by clicking the following link' mod='paytpv'} <a class='link' target="_blank"  href="https://github.com/PayTpv/PAYTPV-PrestaShop/blob/master/PAYTPV_MODULO_PRESTASHOP.pdf?raw=true">{l s='USER DOCUMENTATION'  mod='paytpv'}</a></strong>
        </div>

    </form>

    {if $reg_estado==1}

    <br /><br /><br />

    <form action="'.$_SERVER['REQUEST_URI'].'" method="post">
        <fieldset>
            <legend><img src="../img/admin/contact.gif" alt="" title="" />{l s='Failed Transactions' mod='paytpv'}</legend>
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
                    jQuery("#tdmin_container").show();
                    break;
            }
        }

        function checktdfirst(){
            // Si solo tiene terminal seguro la primera compra va por seguro
            if(jQuery("#terminales").val() == 0 && jQuery("#tdfirst").val()==0){
                alert("{l s='If you only have a Secure terminal, payments always go via Secure' mod='paytpv'}");
                jQuery("#tdfirst").val(1);
            }
            // Si solo tiene terminal no seguro la primera compra va por seguro
            if(jQuery("#terminales").val() == 1 && jQuery("#tdfirst").val()==1){
                alert("{l s='If you only have a Non-Secure terminal, payments always go via Non-Secure' mod='paytpv'}");
                jQuery("#tdfirst").val(0);
            }
        }

        function checkenvironment(){
            if (jQuery("#environment").val()==1){
                jQuery("#test_mode").show();
                jQuery("#real_mode").hide();
            }else{
                jQuery("#test_mode").hide();
                jQuery("#real_mode").show();
            }
        }
        
        checkenvironment();
        checkterminales();

    </script>

