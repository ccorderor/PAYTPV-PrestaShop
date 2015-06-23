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


<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.min.js"></script>

<script>

function CheckForm(){
	$("#clockwait").show();
	// Check card Test values
	$.post('{$CHECK_CARD}', $( "#formulario" ).serialize(), function( data ) {
		if (data.checked==0){
			$("#resp_error").show();
			$("#form_pago").hide();
			$("#clockwait").hide();
		}else{
			if (data.dsecure==1 && "107_TEST"!="{$TRANSACTION_TYPE}"){
				parent.location='{$URL_DSECURE}'+'&MERCHAN_PAN='+$("#merchan_pan").val();
			}else{
				// Throw notification Test
				$.post( '{$URL_NOT}', $( "#formulario" ).serialize(), function( data ) {
					// Go to Url OK
					parent.location=data.urlok;
				}, "json");
			}
		}
	}, "json");

	return false;
}


</script>

<body style="width:450px;">
<form name="formulario" id="formulario"  action="" method="post" onSubmit="return CheckForm();">
<div id="form_pago" style="background-color:#fff;width:400px !important;">
	<div>
		<span style="font-family: arial;font-size: 12px;color: #333;">Titular de la tarjeta:</span>
	</div>
	<div>
		<input type="text" style="border: 1px solid #ccc; border-radius: 4px; padding: 5px;color: #333; font-size: 12px; margin: 0px 6px 10px 0;" maxlength="50" id="nombre" name="nombre" value="" onClick="this.value='';" />
	</div>
	<div>
		<span style="font-family: arial;font-size: 12px;color: #333;">Número de tarjeta:</span>
	</div>
	<div>
		<input type="text" style="border: 1px solid #ccc; border-radius: 4px; padding: 5px;color: #333; font-size: 12px; margin: 0px 6px 10px 0;" maxlength="16" id="merchan_pan" name="merchan_pan" value="" onClick="this.value='';" />
	</div>
	<div>
		<span style="font-family: arial;font-size: 12px;color: #333;">Fecha de caducidad:</span>
	</div>

	<div>
		<div style="display:inline">
			<select id="mm" name="mm" style="border: 1px solid #ccc;border-radius: 4px;padding: 5px;color: #333;font-size: 12px;margin: 0px 6px 10px 0; ">
			<option value="01">01</option>
			<option value="02">02</option>
			<option value="03">03</option>
			<option value="04">04</option>
			<option value="05">05</option>
			<option value="06">06</option>
			<option value="07">07</option>
			<option value="08">08</option>
			<option value="09">09</option>
			<option value="10">10</option>
			<option value="11">11</option>
			<option value="12">12</option>
			</select>        
		</div>
		<div style="display:inline">
			<select name="yy" id="yy" style="border: 1px solid #ccc;border-radius: 4px;padding: 5px;color: #333;font-size: 12px;margin: 0px 6px 10px 0; ">
			<option value=15>2015</option><option value=16>2016</option><option value=17>2017</option><option value=18>2018</option><option value=19>2019</option><option value=20>2020</option><option value=21>2021</option><option value=22>2022</option><option value=23>2023</option>
			</select>        
		</div>
	</div>
  
	<div><span style="font-family: arial;font-size: 12px;color: #333;">Código CVC2:</span></div>
  	<div style="width:14%;"><input type="text" style="width:90% !important;border: 1px solid #ccc; border-radius: 4px; padding: 5px;color: #333; font-size: 12px; margin: 0px 6px 10px 0;" maxlength="4" id="merchan_cvc2" name="merchan_cvc2" value="" onClick="this.value='';" /></div>
	
	<div style="width:14%;">
		<img src="{$this_path}/views/img/cvv2.png" alt="CVC2" width="34" height="21" title="CVC2" style="margin-left:5px; float:left;" />
	</div>
  	<div style="width:70%;">
  		<span style="font-family: arial;font-size: 12px;color: #333;">(Los 3 últimos dígitos)</span>
  	</div>
	
  	<br>
  
	<input type="submit" style="background-color: #0099e6;border: 1px solid #0099e6;color: #fff;font-weight: bold;border-radius: 3px;padding: 5px 10px;" value="Aceptar pago" class="boton" id="btnforg" />

	<div><img src="{$this_path}/views/img/clockpayblue.gif" alt="Espere" width="41" height="30" id="clockwait" style="display:none; margin-top:5px;" /></div>

	<input type="hidden" name="TransactionType" value="{$TRANSACTION_TYPE}">
	<input type="hidden" name="Order" value="{$MERCHANT_ORDER}">
	<input type="hidden" name="Amount" value="{$MERCHANT_AMOUNT}">
	<input type="hidden" name="Response" value="OK">
	<input type="hidden" name="ExtendedSignature" value="{$MERCHANT_MERCHANTSIGNATURE}">
	<input type="hidden" name="IdUser" value="{$ID_USER}">
	<input type="hidden" name="TokenUser" value="{$TOKEN_USER}">
	<input type="hidden" name="Currency" value="{$MERCHANT_CURRENCY}">
	<input type="hidden" name="AuthCode" value="Test_mode">

</div>



<div id="resp_error" style="display:none;width:100%;" marginwidth="0" marginheight="0">
	<div style="margin-top:15px !important;background-color:#fff;width:400px !important;">
	<div style="float:left;width:98%;margin-top:13px;"><span style="font-family: arial;font-size: 12px;color: #333;">No se ha podido realizar correctamente la operación por el siguiente motivo:</span></div>
	<div style="float:left;width:98%;margin-top:13px;"><b><span style="font-family: arial;font-size: 12px;color: #333;">Error durante el proceso<br><span class="bigger">Error Inesperado. Verifique los datos de la tarjeta y pruebe de nuevo.</span></span></b></div>
	</div>
	<div style="margin-top:20px !important;background-color:#fff;width:400px !important;">
	<div style="float:left;width:30%;"><input type="button" value="Volver" style="background-color: #0099e6;border: 1px solid #0099e6;color: #fff;font-weight: bold;border-radius: 3px;padding: 5px 10px;" onclick="document.location=document.location"></div>
	 <div style="float:left;width:30%;"><input type="button" value="Finalizar" style="background-color: #0099e6;border: 1px solid #0099e6;color: #fff;font-weight: bold;border-radius: 3px;padding: 5px 10px;" onclick="parent.location='{$URL_KO}'"></div>
</div>
</form>




