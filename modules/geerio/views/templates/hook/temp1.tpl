<h1>Xin chao moi nguoi</h1>
<p>{$INFO['id']}</p>
<div>
gcontact_id:'{$INFO['id']}', //user's unique id, integer, mandatory
gender:'{$INFO['title']}', //numeric, user's gender, 1 for male, 2 for female, 3 for other, optional
first_name:'{$INFO['firstname']}', //user's firstname, optional
last_name:'{$INFO['lastname']}', //user's lastname, optional
company:'{$INFO['company']}', //user's company name, optional
birth_date:{$INFO['birthday']}, //user's date of birth, timestamp since the epoch, optional
email:'{$INFO['email']}', //user's email, mandatory
landline_number:'{$INFO['phone']}', //numeric, user's landline number, optional
mobile_phone_number:'{$INFO['mobile']}', //numeric, user's mobile phone number, optional
address_line_1:'{$INFO['address1']}', //user's 1st address line, optional
addresse_line_2: '{$INFO['address2']}', //user's 2nd address line, optional
city:'{$INFO['city']}', //user's city, optional
postal_code:'{$INFO['postcode']}', //user's postal code, optional
country:'{$INFO['country']}', //user's country, ISO 3166-1 alpha-2, optional
language:'{$INFO['language']}', //user's language, ISO 639-1, optional
optin_email:{$INFO['optin']}, //user's email optin, integer, only 1 (user optin) or 0 (user non optin), mandatory
optin_sms:1, //user's sms optin, integer, only 1 (user optin) or 0 (user non optin), mandatory
date_creation:{$INFO['datecreated']}, //user's account date creation,timestamp since the epoch, mandatory
date_update:{$INFO['dateupdated']}, //user's account last date update,timestamp since the epoch, mandatory
add_group:[
    {foreach from=$INFO['group'] item=name name=group}
            '{$name}' {if $smarty.foreach.group.last} {else} , {/if}
    {/foreach}
],
del_group:['AZERTY'] //withdrawal of the user from mentioned group(s), optional
contact_details_1:'azerty', //contact detail_1, may be used for user's further detail, optional
contact_details_2:'azerty', //contact detail_2, may be used for user's further detail, optional
contact_details_3:'azerty', //contact detail_3, may be used for user's further detail, optional
contact_details_4:'azerty', //contact detail_4, may be used for user's further detail, optional
contact_details_5:'azerty', //contact detail_5, may be user for user's further detail, optional