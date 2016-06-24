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

    {capture name=path}
    <a href="{$link->getPageLink('order', true)|escape:'html'}">{l s='Cart' mod='paytpv'}</a>
    <span class="navigation-pipe">{$navigationPipe}</span>
    {l s='Pay with Card' mod='paytpv'}</a>
    {/capture}

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="paytpv">
                {if ($newpage_payment==2)}
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <p style="padding-top: 10px;">
                            <input type="radio" title="{l s='Pay with Card' mod='paytpv'}" 
                            name="payment_mode" 
                            class="payment_mode" 
                            id="payment_mode_paytpv" 
                            data-payment="paytpv"
                            data-payment-link="{$paytpv_iframe}">
                            <label class="upletter" style="padding-left: 20px;" for="payment_mode_paytpv">{l s='Pay with Card' mod='paytpv'}</label>
                        </p>
                        <form action="{$link->getModuleLink('paytpv', 'validation')}" method="post" id="paytpv_form" class="hidden"></form>
                    </div>
                </div>
                {/if}   
                <a href="http://www.paytpv.com" target="_blank"><img src="{$this_path}views/img/paytpv.png"></a>
                <img src="{$this_path}views/img/tarjetas.png">
                <br>
                {if ($msg_paytpv!="")}
                <p>
                    <span class="message">{$msg_paytpv}</span>
                </p>
                {/if}
                {if ($active_suscriptions)}
                <div id="tipo-pago">
                    <p class="checkbox">

                        <span class="checked"><input type="checkbox" name="suscripcion" id="suscripcion" onclick="check_suscription();" value="1"></span>
                        <label for="suscripcion">{l s='Would you like to subscribe to this order?' mod='paytpv'}</label>

                    </p>

                    <div id="div_periodicity" class="suscription_period" style="display:none">
                        <div class="nota">{l s='The first purchase will be made when placing the order and the following as defined as the frequency of the subscription' mod='paytpv'}.
                            {l s='By subscribing you agree to the ' mod='paytpv'} <a id="open_conditions" href="#conditions">{l s='terms and conditions of the service' mod='paytpv'}</a>.
                        </div>

                        <form class="form-inline">
                            <div class="form-group">    
                                <label for="susc_periodicity" class="control-label">{l s='Frequency:' mod='paytpv'} </label>
                                <select name="susc_periodicity" id="susc_periodicity" onChange="saveOrderInfoJQ(1)" class="form-control" style="min-width:200px;">
                                    <option value="7">{l s='7 days (weekly)' mod='paytpv'}</option>
                                    <option value="30" selected>{l s='30 days (monthly)' mod='paytpv'}</option>
                                    <option value="90">{l s='90 days (quarterly)' mod='paytpv'}</option>
                                    <option value="180">{l s='180 days (biannual)' mod='paytpv'}</option>
                                    <option value="365">{l s='365 days (annual)' mod='paytpv'}</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="susc_cycles">{l s='Payments:' mod='paytpv'}</label>
                                <select name="susc_cycles" id="susc_cycles" class="form-control" onChange="saveOrderInfoJQ(1)" style="min-width:200px;">
                                    <option value="0" selected>{l s='Permanent' mod='paytpv'}</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                            </div>
                        </form>

                    <!--<a href="javascript:void(0);" onclick="suscribeJQ();" title="{l s='Subscribe' mod='paytpv'}" class="button button-small btn btn-default">
                        <span>{l s='Subscribe' mod='paytpv'}<i class="icon-chevron-right right"></i></span>
                    </a>-->

                    
                </div>
            </div>
            
            {/if}
            

            <div id="saved_cards" style="display:none">
                <form class="form-inline">
                    <div class="form-group">
                        <label for="card">{l s='Card' mod='paytpv'}:</label>
                        <select name="card" id="card" onChange="checkCard()" class="form-control">
                            {section name=card loop=$saved_card }
                            {if ($saved_card[card].url=="0")}
                            {if ($newpage_payment==2)}
                            <option value='{$paytpv_iframe}'>{l s='NEW CARD' mod='paytpv'}</option>
                            {else}
                            <option value='0'>{l s='NEW CARD' mod='paytpv'}</option>
                            {/if}
                            {else}
                            <option value='{$saved_card[card].url}'>{$saved_card[card].CC} ({$saved_card[card].BRAND}){if ($saved_card[card].CARD_DESC!="")} - {$saved_card[card].CARD_DESC}{/if}</option>
                            {/if}
                            {/section}
                        </select>
                    </div>
                </form>

                {if (sizeof($saved_card)>1)}
                <div id="button_directpay">
                    {if ($commerce_password)}
                    <a id="open_directpay" href="#directpay" class="paytpv_pay button button-small btn btn-default">          
                        <span>{l s='Pay' mod='paytpv'}<i class="icon-chevron-right right"></i></span>
                    </a>
                    {else}
                    <a id="exec_directpay" href="#" class="exec_directpay paytpv_pay button button-small btn btn-default">          
                        <span>{l s='Pay' mod='paytpv'}<i class="icon-chevron-right right"></i></span>
                    </a>
                    {/if}
                    <img id='clockwait' style="display:none" src="{$this_path}views/img/clockpayblue.gif"></img>
                </div>
                {/if}
                
                <div id="confirm" style="display:none">
                    <p class="title"></p>
                    <input type="button" class="confirm yes button" value="{l s='Accept' mod='paytpv'}" />
                    <input type="button" class="confirm no button" value="{l s='Cancel' mod='paytpv'}" />
                </div>
            </div>


            <div id="storingStep" class="alert alert-info {if (sizeof($saved_card))>1}hidden{/if}">

                <h6>{l s='STREAMLINE YOUR FUTURE PURCHASES!' mod='paytpv'}</h4>
                    <label class="checkbox"><input type="checkbox" name="savecard" id="savecard" onChange="saveOrderInfoJQ(0)" checked>{l s='Yes, remember my card accepting the ' mod='paytpv'}<a id="open_conditions" href="#conditions">{l s='terms and conditions of the service' mod='paytpv'}</a>.</label>

                </div>
                

                <br class="clear"/>


                <div class="payment_module paytpv_iframe" style="display:none">       

                {if ($newpage_payment<2)}
                    {if ($paytpv_integration==0)}
                        <p id='ajax_loader' style="display:none">
                            <img id='ajax_loader' src="{$this_path}views/img/clockpayblue.gif"></img>
                            {l s='Loading payment form...' mod='paytpv'}
                        </p>
                        <iframe id="paytpv_iframe" src="{$paytpv_iframe}" name="paytpv" style="width: 670px; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-style: initial; border-color: initial; border-image: initial; height: 322px; " marginheight="0" marginwidth="0" scrolling="no"></iframe>
                    {else}
                        <form action="{$paytpv_jetid_url}" method="POST" class="paytpv_jet" id="paytpvPaymentForm" onsubmit="return takingOff();">
                        <ul>
                            <li>
                                <label for="MERCHANT_PAN">{l s='Credit Card Number' mod='paytpv'}:</label>
                                <input type="text" data-paytpv="paNumber" width="360" maxlength="16" value="" required="required" placeholder="1234 5678 9012 3456" pattern="{literal}[0-9]{15,16}{/literal}" onclick="this.value='';">
                            </li>
                            <li class="vertical">
                                <ul>
                                    <li>
                                        <label for="expiry_date">{l s='Expiration' mod='paytpv'}</label>
                                        <input maxlength="5" placeholder="{l s='mm/yy' mod='paytpv'}" id="expiry_date" required="required" pattern="{literal}[0-9]{2}/+[0-9]{2}{/literal}" type="text" onChange="buildED();">
                                        <input type="hidden" data-paytpv="dateMonth" maxlength="2"  value="">
                                        <input type="hidden" data-paytpv="dateYear" maxlength="2" value="">
                                    </li>
                                    <li>
                                        <label for="MERCHANT_CVC2">CVV</label>
                                        <input type="text" data-paytpv="cvc2" maxlength="4" value="" required="required" placeholder="123" pattern="{literal}[0-9]{3,4}{/literal}" onclick="this.value='';">
                                    </li>
                                    <small class="help">{l s='The CVV is a numerical code, usually 3 digits behind the card' mod='paytpv'}.</small>
                                </ul>
                            </li>
                            <li>
                                <label for="Nombre">{l s='Cardholder name' mod='paytpv'}</label>
                                <input type="text" class="paytpv_cardholdername" data-paytpv="cardHolderName" width="360" maxlength="50" value="" required="required" placeholder="{l s='Name surname' mod='paytpv'}" onclick="this.value='';">
                            </li>
                            <li>
                                
                                <input type="submit" class="button" value="{l s='Make Payment' mod='paytpv'}" id="btnforg" style="display: inline-block;font-size: 21px;font-weight: 300;line-height: 46px;height:46px;padding: 0 0px;text-align: center;width: 100%;" onclick="buildED();">

                                <span style="color:red;font-weight:bold;" id="paymentErrorMsg"></span>
                            </li>
                        </ul>
                        </form>

                        <script type="text/javascript" src="https://secure.paytpv.com/gateway/jet_paytpv_js.php?id={$jet_id}&language={$jet_lang}"></script>
                        <script type="text/javascript">
                        {literal}function buildED(){var t=document.getElementById("expiry_date").value,n=t.substr(0,2),a=t.substr(3,2);$('[data-paytpv="dateMonth"]').val(n),$('[data-paytpv="dateYear"]').val(a)}(function(){(function(){var t,n=[].indexOf||function(t){for(var n=0,a=this.length;a>n;n++)if(n in this&&this[n]===t)return n;return-1};t=jQuery,t.fn.validateCreditCard=function(a,e){var r,l,i,u,c,v,o,d,s,h,f,p,m;for(u=[{name:"amex",pattern:/^3[47]/,valid_length:[15]},{name:"diners_club_carte_blanche",pattern:/^30[0-5]/,valid_length:[14]},{name:"diners_club_international",pattern:/^36/,valid_length:[14]},{name:"jcb",pattern:/^35(2[89]|[3-8][0-9])/,valid_length:[16]},{name:"laser",pattern:/^(6304|670[69]|6771)/,valid_length:[16,17,18,19]},{name:"visa_electron",pattern:/^(4026|417500|4508|4844|491(3|7))/,valid_length:[16]},{name:"visa",pattern:/^4/,valid_length:[16]},{name:"mastercard",pattern:/^(5[1-5]|222|2[3-6]|27[0-1]|2720)/,valid_length:[16]},{name:"maestro",pattern:/^(5018|5020|5038|6304|6759|676[1-3])/,valid_length:[12,13,14,15,16,17,18,19]},{name:"discover",pattern:/^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)/,valid_length:[16]}],r=!1,a&&("object"==typeof a?(e=a,r=!1,a=null):"function"==typeof a&&(r=!0)),null===e&&(e={}),null===e.accept&&(e.accept=function(){var t,n,a;for(a=[],t=0,n=u.length;n>t;t++)l=u[t],a.push(l.name);return a}()),m=e.accept,f=0,p=m.length;p>f;f++)if(i=m[f],n.call(function(){var t,n,a;for(a=[],t=0,n=u.length;n>t;t++)l=u[t],a.push(l.name);return a}(),i)<0)throw"";return c=function(t){var a,r,c;for(c=function(){var t,a,r,i;for(i=[],t=0,a=u.length;a>t;t++)l=u[t],r=l.name,n.call(e.accept,r)>=0&&i.push(l);return i}(),a=0,r=c.length;r>a;a++)if(i=c[a],t.match(i.pattern))return i;return null},o=function(t){var n,a,e,r,l,i;for(e=0,i=t.split("").reverse(),a=r=0,l=i.length;l>r;a=++r)n=i[a],n=+n,a%2?(n*=2,e+=10>n?n:n-9):e+=n;return e%10===0},v=function(t,a){var e;return e=t.length,n.call(a.valid_length,e)>=0},h=function(t){return function(t){var n,a;return i=c(t),a=!1,n=!1,null!==i&&(a=o(t),n=v(t,i)),{card_type:i,valid:a&&n,luhn_valid:a,length_valid:n}}}(this),s=function(n){return function(){var a;return a=d(t(n).val()),h(a)}}(this),d=function(t){return t.replace(/[ -]/g,"")},r?(this.on("input.jccv",function(n){return function(){return t(n).off("keyup.jccv"),a.call(n,s())}}(this)),this.on("keyup.jccv",function(t){return function(){return a.call(t,s())}}(this)),a.call(this,s()),this):s()}}).call(this),$(function(){return $('[data-paytpv="paNumber"]').validateCreditCard(function(t){return $(this).removeClass().addClass("paytpv_merchant_pan"),null!==t.card_type?($(this).addClass(t.card_type.name),t.valid?$(this).addClass("valid"):$(this).removeClass("valid")):void 0},{accept:["visa","visa_electron","mastercard","maestro","discover","amex"]})})}).call(this),$(document).ready(function(){$("#expiry_date").on("input",function(){var t=$(this).val().length;if(2===t){var n=$(this).val();n+="/",$(this).val(n)}})});{/literal}
                        </script>
                    {/if}
                {/if}
            </div>



        </div>
    </div>


    <div style="display: none;">
        <div id="directpay" style="overflow:auto;">
            <form name="pago_directo" id="pago_directo" action="" method="post">
                <h1 class="estilo-tit1">{l s='Use Card' mod='paytpv'}</h1>
                <p>
                    {l s='Card' mod='paytpv'}:&nbsp;
                    <strong><span id="datos_tarjeta"></span></strong>
                </p>
                <p>
                    {l s='For security, enter your store user password' mod='paytpv'}
                </p>
                <p>
                    {l s='Password' mod='paytpv'}: <input type="password" name="password" id="password" class="password">
                </p>
                <p class="button_left">
                    <a id="pago_directo" href="#" class="exec_directpay paytpv_pay button button-small btn btn-default">          
                        <span>{l s='Pay' mod='paytpv'}<i class="icon-chevron-right right"></i></span>
                    </a>
                </p>
            </form>
        </div>
    </div>

    <div style="display: none;">
        <div id="conditions" style="overflow:auto;">
            <h2 class="estilo-tit1">1.- {l s='Subscriptions' mod='paytpv'}</h2>
            <p>
                {l s='This business does not store or transmit credit card or debit card data. Data is sent over an encrypted and secure channel to the PAYTPV platform.' mod='paytpv'}
            </p>
            <p>
                {l s='The user may cancel their subscription to the product at any time from the Section "My Cards and Subscriptions".  The user subscriptions will be displayed and they can cancel them if they wish.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1">2.- {l s='Linked cards' mod='paytpv'}</h2>
            <p>
                {l s='This business does not store or transmit credit card or debit card data. Data is sent over an encrypted and secure channel to the PAYTPV platform.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Security Policy' mod='paytpv'}</h2>
            <p>
                {l s='All transaction information transmitted between this site and PAYTPV systems is encrypted using 256-bit SSL certificates. All cardholder information is transmitted encrypted and all messages sent to your servers from PAYTPV are signed using SHA hashing to prevent tampering. The information that is transmitted to PAYTPV servers cannot be examined, scanned, used or modified by any external party that gains access to confidential information.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Data encryption and storage' mod='paytpv'}</h2>
            <p>
                {l s='Once in the PAYTPV systems, confidential information is protected using standard 1024-bit encryption. Encryption keys are kept in volatile high security systems with double authentication, which makes their extraction impossible. Banks, security agents and banking institutions perform regular audits to ensure data protection.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='System Safety' mod='paytpv'}</h2>
            <p>
                {l s='PAYTPV systems are reviewed quarterly by specific ISO tools, an independent Qualified Security Assessor (QSA) and a scanning vendor (ASV) approved by the payment card brands.' mod='paytpv'}
            </p>
            <p>
                {l s='PAYTPV is also subject to an annual audit according to the standards of data security of the Payment Card Industry (PCI DSS) and is a fully approved Level 1 provider of payment services, which is the highest level of compliance.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Links to banks' mod='paytpv'}</h2>
            <p>
                {l s='PAYTPV has multiple private links to banking networks that are completely independent of the Internet and which do not cross any public access network. All the information of the holder sent to banks and all the authorization messages sent in response are protected and cannot be manipulated.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Internal security' mod='paytpv'}</h2>
            <p>
                {l s='PAYTPV is audited in access controls to production environments. The CPD where systems are hosted operate according to the requirements for Tier III centers. This ensures that safety is not put at risk at any time. It has sophisticated alarm systems, surveillance by means of closed circuit TV and security guards 24 hours a day, 7 days a week on site, as well as rigorous monitoring and maintenance. All the information about transactions and customer cards is protected even from our own employees.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Disaster Recovery' mod='paytpv'}</h2>
            <p>
                {l s='PAYTPV has Backup systems hosted in different countries to ensure optimal safety of the systems and high availability. It also has a complete business continuity and disaster recovery policy.' mod='paytpv'}
            </p>
            <p>&nbsp;</p>
        </div>
    </div>

    <input type="hidden" name="paytpv_module" id="paytpv_module" value="{$link->getModuleLink('paytpv', 'actions',[], true)|escape:'htmlall':'UTF-8'}">

    <form id="form_paytpv" action="{$base_dir}index.php?controller=order" method="post">
        <input type="hidden" name="step" value="3">
        <input type="hidden" name="paytpv_cc" id="paytpv_cc" value="">

        <input type="hidden" name="paytpv_agree" id="paytpv_agree"  value="0">
        <input type="hidden" name="action_paytpv" id="action_paytpv"  value="">

        <!--SUSCRIPCIONES-->
        <input type="hidden" name="paytpv_suscripcion" id="paytpv_suscripcion"  value="0">
        <input type="hidden" name="paytpv_periodicity" id="paytpv_periodicity"  value="0">
        <input type="hidden" name="paytpv_cycles" id="paytpv_cycles"  value="0">

        <input type="hidden" name="id_cart" id="id_cart"  value="{$id_cart}">

    </form>

    <script type="text/javascript">
    paytpv_initialize();
    </script>
</div>





