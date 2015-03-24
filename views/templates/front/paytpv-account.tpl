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
    <a href="{$link->getPageLink('my-account', true)|escape:'html'}">{l s='My account' mod='paytpv'}</a>
    <span class="navigation-pipe">{$navigationPipe}</span>
        {l s='My Cards and Subscriptions' mod='paytpv'}</a>
        
{/capture}


<script type="text/javascript">
    var url_removecard = "{$url_removecard}";
    var url_cancelsuscription = "{$url_cancelsuscription}";
    var url_savedesc = "{$url_savedesc}";
    var msg_cancelsuscription = "{l s='Cancel Subscription' mod='paytpv'}"
    var msg_removecard = "{l s='Remove Card' mod='paytpv'}";
    var msg_accept = "{l s='You must accept the terms and conditions of service' mod='paytpv'}";
    var msg_savedesc = "{l s='Save description' mod='paytpv'}";
    var msg_descriptionsaved = "{l s='Description saved' mod='paytpv'}";
    var status_canceled = "{$status_canceled}";
    
</script>

<div id="paytpv_block_account">
    <h2>{l s='My Cards' mod='paytpv'}</h2>
    {if isset($saved_card[0])}
        <div class="span6" id="div_tarjetas">
            {l s='Available Cards' mod='paytpv'}:
            {section name=card loop=$saved_card}   
                <div class="bankstoreCard" id="card_{$saved_card[card].IDUSER}">  
                    {$saved_card[card].CC} ({$saved_card[card].BRAND})
                    <input type="text" maxlength="32" style="width:300px" id="card_desc_{$saved_card[card].IDUSER}" name="card_desc_{$saved_card[card].IDUSER}" value="{$saved_card[card].CARD_DESC}" placeholder="{l s='add description' mod='paytpv'}">
                    <label class="button_del">
                        <a href="#" id="{$saved_card[card].IDUSER}" class="save_desc">
                         {l s='Save description' mod='paytpv'}
                        </a>
                         | 
                        <a href="#" id="{$saved_card[card].IDUSER}" class="remove_card">
                         {l s='Remove Card' mod='paytpv'}
                        </a>
                       
                        <input type="hidden" name="cc_{$saved_card[card].IDUSER}" id="cc_{$saved_card[card].IDUSER}" value="{$saved_card[card].CC}">
                    </label>
                </div>
            {/section}
        </div>
   
    {else}
        <p class="warning">{l s='There is still no card associated.' mod='paytpv'}</p>
    {/if}

    <div id="storingStep" class="box">
        <h4>{l s='STREAMLLINE YOUR FUTURE PURCHASES!' mod='paytpv'}</h4>
        <p>{l s='Link a card to your account to make all procedures easily and quickly.' mod='paytpv'}</p>

        <p class="checkbox">
            <span class="checked"><input type="checkbox" name="savecard" id="savecard"></span>
            <label for="savecard">{l s='When you link a Tarjet accepts ' mod='paytpv'}<a id="open_conditions" href="#conditions" class="link"><strong>{l s='terms and conditions of service' mod='paytpv'}</strong></a></label>
        </p>
        <p>
            <a href="javascript:void(0);" onclick="vincularTarjeta();" title="{l s='Link card' mod='paytpv'}" class="button button-small btn btn-default">
                <span>{l s='Link card' mod='paytpv'}<i class="icon-chevron-right right"></i></span>
            </a>
            <a href="javascript:void(0);" onclick="close_vincularTarjeta();" title="{l s='Cancel' mod='paytpv'}" class="button button-small btn btn-default" id="close_vincular" style="display:none">
                <span>{l s='Cancel' mod='paytpv'}<i class="icon-chevron-right right"></i></span>
            </a>
        </p>

        <p class="payment_module paytpv_iframe" id="nueva_tarjeta" style="display:none">
            <iframe src="https://secure.paytpv.com/gateway/bnkgateway.php?{$query}" name="paytpv" style="width: 670px; border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-style: initial; border-color: initial; border-image: initial; height: 322px; " marginheight="0" marginwidth="0" scrolling="no"></iframe>
        </p>
    </div>
    <hr>
    <h2>{l s='My Subscriptions' mod='paytpv'}</h2>
    {if isset($suscriptions[0])}
        <div class="span6" id="div_suscripciones">
            {l s='Subscriptions' mod='paytpv'}:
            <ul>
                {section name=suscription loop=$suscriptions} 
                    <li class="suscriptionCard" id="suscription_{$suscriptions[suscription].ID_SUSCRIPTION}">  
                        <a href="{$link->getPageLink('order-detail',true,null,"id_order={$suscriptions[suscription].ID_ORDER}")|escape:'html'}">{l s='Order' mod='paytpv'}: {$suscriptions[suscription].ORDER_REFERENCE}</a>
                        <br>
                        {l s='Every' mod='paytpv'} {$suscriptions[suscription].PERIODICITY} {l s='days' mod='paytpv'} - {l s='repeat' mod='paytpv'} {$suscriptions[suscription].CYCLES} {l s='times' mod='paytpv'} - {l s='Amount' mod='paytpv'}: {$suscriptions[suscription].PRICE} - {l s='Start' mod='paytpv'}: {$suscriptions[suscription].DATE_YYYYMMDD}
                        <label class="button_del">
                            {if $suscriptions[suscription].STATUS==0}
                                <a href="#" id="{$suscriptions[suscription].ID_SUSCRIPTION}" class="cancel_suscription">
                                 {l s='Cancel Subscription' mod='paytpv'}
                                </a>
                            {else if $suscriptions[suscription].STATUS==1}
                                <span class="canceled_suscription">
                                    {l s='CANCELED' mod='paytpv'}
                                </span>
                            {else if $suscriptions[suscription].STATUS==2}
                                <span class="finised_suscription">
                                    {l s='FINISHED' mod='paytpv'}
                                </span>
                            {/if}
                        </label>
                        <div class="span6" id="div_suscripciones_pay">
                            {$suscription_pay = $suscriptions[suscription].SUSCRIPTION_PAY}
                            <ul >
                                {section name=suscription_pay loop=$suscription_pay}
                                <li class="suscription_pay" id="suscription_pay{$suscription_pay[suscription_pay].ID_SUSCRIPTION}">
                                     <a href="{$link->getPageLink('order-detail',true,null,"id_order={$suscription_pay[suscription_pay].ID_ORDER}")|escape:'html'}">{l s='Order' mod='paytpv'}: {$suscription_pay[suscription_pay].ORDER_REFERENCE}</a>
                                     {l s='Amount' mod='paytpv'}: {$suscription_pay[suscription_pay].PRICE} - {l s='Date' mod='paytpv'}: {$suscription_pay[suscription_pay].DATE_YYYYMMDD}

                                </li>
                                {/section}
                            </ul>

                        </div>
                    </li>
                {/section}
            </ul>
        </div>
   
    {else}
        <p class="warning">{l s='There is no subscription.' mod='paytpv'}</p>
    {/if}

    <div id="alert" style="display:none">
        <p class="title"></p>
    </div>

    <div id="confirm" style="display:none">
        <p class="title"></p>
        <input type="button" class="confirm yes button" value="{l s='Accept' mod='paytpv'}" />
        <input type="button" class="confirm no button" value="{l s='Cancel' mod='paytpv'}" />
        <input type="hidden" name="paytpv_cc" id="paytpv_cc">
        <input type="hidden" name="paytpv_iduser" id="paytpv_iduser">
        <input type="hidden" name="id_suscription" id="id_suscription">
    </div>

    <div style="display: none;">
        <div id="conditions" style="overflow:auto;">
            <h1 class="estilo-tit1">{l s='Related Cards' mod='paytpv'}</h1>
            <p>
            {l s='This trade does not store or transmit data credit card or debit card. The data are sent via a secure, encrypted channel platform PayTPV.' mod='paytpv'}
            </p>
            <p>
            {l s='At any time, the user can add or remove data from your linked card. In the My Account section, you will see a section "My cards" where the cards will be stored and may be removed.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Security Policy' mod='paytpv'}</h2>
            <p>
            {l s='All transaction information is transmitted between this site and PayTPV systems is encrypted using 256-bit SSL certificates. All cardholder information is transmitted encrypted and all messages sent to your servers from PayTPV signed using SHA hashing to prevent tampering. The information is transmitted to servers PayTPV can not probe, scan, used or modified by any external to gain access to confidential information.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Encryption and Data Storage' mod='paytpv'}</h2>
            <p>
            {l s='Once PayTPV systems, confidential information is protected using standard 1024-bit encryption. Encryption keys are kept in high security systems with double authentication volatile and which precludes their extraction. Banks, security agents and banks perform regular audits to ensure data protection.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='System Safety' mod='paytpv'}</h2>
            <p>
            {l s='PayTPV systems are reviewed quarterly by specific tools ISO, a Qualified Qualified Security Assessor (QSA) and an independent approved scanning vendor (ASV) for the payment card brands.' mod='paytpv'}
            </p>
            <p>
            {l s='PayTPV also subject to an annual audit by the standards of data security (PCI DSS) and Payment Card Industry is a provider of payment services fully approved Level 1, which is the highest level of compliance.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Links to banks' mod='paytpv'}</h2>
            <p>
            {l s='PayTPV has multiple private links to banking networks that are completely independent of the Internet and which do not cross any public access network. Any cardholder information sent to the banks and all messages sent in response authorization are protected and can not be manipulated.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Internal security' mod='paytpv'}</h2>
            <p>
            {l s='PayTPV is audited access controls production environments. The CPD stay where systems operate according to the requirements for Tier III centers. This ensures that safety is not put at risk at any time. It has sophisticated alarm systems, surveillance and closed circuit TV security guards present 24 hours a day, 7 days a week on site, as well as monitoring and a rigorous maintenance. All transaction information and customer card is protected even our own employees.' mod='paytpv'}
            </p>
            <h2 class="estilo-tit1" id="politica_seguridad">{l s='Disaster Recovery' mod='paytpv'}</h2>
            <p>
            {l s='PayTPV has hosted Backup systems in different countries to ensure optimal safety systems and high availability. It also has a policy of commercial below and complete disaster recovery.' mod='paytpv'}
            </p>
            <p>&nbsp;</p>
        </div>
    </div>


    
</div>