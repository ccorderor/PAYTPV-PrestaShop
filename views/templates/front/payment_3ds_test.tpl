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

<head>
	<meta content="text/html; charset=windows-1252" http-equiv="Content-Type">
	<title>SAS Servidor Autenticación PAYTPV</title>
	<meta http-equiv="Expires" content="-1" />
	<meta http-equiv="Expires" content="Monday, 01-Jan-90 00:00:00 GMT" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Cache-Control" content="no-cache" />
	<link media="screen" href="{$this_path}/css/2100.css" type="text/css" rel="StyleSheet" />
	<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.min.js"></script>

	<script type="text/javascript">
	function checkform() {
		if ($("#demopin").val() == "1234") {
			// Throw notification Test
			$.post( '{$URL_NOT}', $( "#formulario" ).serialize(), function( data ) {
				parent.location=data.urlok;
			}, "json");
		} else {
			document.getElementById("showerror").innerHTML = "El PIN introducido es erróneo";
		}
		return false;
	}
	</script>
</head>
<body>
	<table width="370" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<td align="center" colspan="2">
					<h3>Autenticación Comercio Electrónico Seguro</h3>
				</td>
			</tr>
			<tr>
				<td width="185px" align="center">
					<img border="0" alt="VISA" src="https://secure.paytpv.com/gateway/3dsas/VerifiedByVisa.jpg"></td>
				<td width="185px" align="center"></td>
			</tr>
			<tr>
				<td colspan="2">
					<hr></td>
			</tr>
		</tbody>
	</table>
	<div id="capaPantalla1" style="position: absolute; visibility: visible; left: 5px;">
		<div style="position:relative;margin:0px auto;display:block;top: -10px;">
			<h4 class="uno">Compruebe los datos de su operación</h4>
		</div>
		<form onsubmit="javascript:checkform();" id="formulario" name="formulario" method="formulario">
		<table width="374" cellspacing="0" cellpadding="0" border="0" style="BORDER-COLLAPSE: collapse">
			<tbody>
				<tr>
					<td style="BORDER-TOP: 0px ; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM: 0px ; BORDER-RIGHT-WIDTH: 0px"> <font class="titulotabla">Importe:</font>
					</td>
					<td style="BORDER-TOP: 0px ; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM: 0px ; BORDER-RIGHT-WIDTH: 0px"> <font class="detalletabla">{$MERCHANT_AMOUNT_DECIMAL} {$CURRENCY_SIGN}</font>
					</td>
				</tr>
				<tr>
					<td style="BORDER-TOP: 0px ; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM: 0px ; BORDER-RIGHT-WIDTH: 0px">
						<font class="titulotabla">Comercio:</font>
					</td>
					<td style="BORDER-TOP: 0px ; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM: 0px ; BORDER-RIGHT-WIDTH: 0px">
						<font class="detalletabla">{$SHOP_NAME}</font>
					</td>
				</tr>
				<tr>
					<td style="BORDER-TOP: 0px ; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM: 0px ; BORDER-RIGHT-WIDTH: 0px">
						<font class="titulotabla">Fecha:</font>
					</td>
					<td style="BORDER-TOP: 0px ; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM: 0px ; BORDER-RIGHT-WIDTH: 0px">
						<font class="detalletabla">{$FECHA}</font>
					</td>
				</tr>
				<tr>
					<td style="BORDER-TOP: 0px ; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM: 0px ; BORDER-RIGHT-WIDTH: 0px">
						<font class="titulotabla">Hora:</font>
					</td>
					<td style="BORDER-TOP: 0px ; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM: 0px ; BORDER-RIGHT-WIDTH: 0px">
						<font class="detalletabla">{$HORA}</font>
					</td>
				</tr>
				
				<tr>
					<td width="362" height="1" colspan="2">
						<div style="margin:0px auto;padding:0px auto;">
							<hr></div>
						<div style="margin:0px auto;display:block;top: -10px;">
							<h4 class="dos">
								Introduzca el PIN de 4 dígitos de su tarjeta de crédito/débito:
							</h4>
						</div>
					</td>
				</tr>
				<tr>
					<td width="363" align="center" class="letra" colspan="2">
						<table align="center">
							<tbody>
								<tr>
									<td width="40%" align="right">
										<font style="font-family: Arial;font-size: 11px;font-weight:bold;color:#858585;">PIN:</font>
										&nbsp;
									</td>
									<td width="60%" align="left">
										<input type="text" size="6" maxlength="6" id="demopin" name="pin" class="formulario">
										&nbsp;
										<img border="0" align="middle" alt="CaixaProtect" src="https://secure.paytpv.com/gateway/3dsas/2100candau256.png"></td>
								</tr>
								<tr>
									<td width="100%" align="center" colspan="2">
										<div id="showerror" class="error_text">
										</div>
										<br></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>

				<tr>
					<td width="362" height="7" style="BORDER-TOP: 0px ; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM: 0px ; BORDER-RIGHT-WIDTH: 0px" colspan="2"></td>
				</tr>
				<tr>
					<td width="362" align="right" height="30" style="valign:center;BORDER-TOP: 0px ; BORDER-LEFT-WIDTH: 0px; BORDER-BOTTOM: 0px ; BORDER-RIGHT-WIDTH: 0px" colspan="2">
						<div style="align: right;">
							<a class="boton aceptar" href="#" onclick="checkform();">
								<span>Confirmar compra</span>
							</a>
							<a class="boton cancelar" href="#" onclick="window.location.href='{$URL_KO}'">
								<span>Cancelar</span>
							</a>
						</div>
					</td>
				</tr>
				<input type="hidden" name="TransactionType" value="{$TRANSACTION_TYPE}">
				<input type="hidden" name="Order" value="{$MERCHANT_ORDER}">
				<input type="hidden" name="Amount" value="{$MERCHANT_AMOUNT}">
				<input type="hidden" name="Response" value="OK">
				<input type="hidden" name="ExtendedSignature" value="{$MERCHANT_MERCHANTSIGNATURE}">
				<input type="hidden" name="IdUser" value="{$ID_USER}">
				<input type="hidden" name="TokenUser" value="{$TOKEN_USER}">
				<input type="hidden" name="Currency" value="{$CURRENCY}">
				<input type="hidden" name="merchan_pan" value="{$MERCHAN_PAN}">
			</tbody>
		</table>
		</form>
	</div>
</body>
