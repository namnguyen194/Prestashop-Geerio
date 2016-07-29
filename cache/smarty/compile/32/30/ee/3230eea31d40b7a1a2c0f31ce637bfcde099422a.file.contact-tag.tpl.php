<?php /* Smarty version Smarty-3.1.19, created on 2016-07-29 05:19:41
         compiled from "C:\xampp\htdocs\ps-v3\modules\geerio\views\templates\hook\contact-tag.tpl" */ ?>
<?php /*%%SmartyHeaderCode:29788579b1fad7e55f9-16539287%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3230eea31d40b7a1a2c0f31ce637bfcde099422a' => 
    array (
      0 => 'C:\\xampp\\htdocs\\ps-v3\\modules\\geerio\\views\\templates\\hook\\contact-tag.tpl',
      1 => 1469783678,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '29788579b1fad7e55f9-16539287',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'INFO' => 0,
    'name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_579b1fad818277_97697216',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_579b1fad818277_97697216')) {function content_579b1fad818277_97697216($_smarty_tpl) {?><script>        
var data ={
contact_id:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['id'];?>
', //user's unique id, integer, mandatory
gender:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['title'];?>
', //numeric, user's gender, 1 for male, 2 for female, 3 for other, optional
first_name:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['firstname'];?>
', //user's firstname, optional
last_name:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['lastname'];?>
', //user's lastname, optional
company:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['company'];?>
', //user's company name, optional
birth_date:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['birthday'];?>
', //user's date of birth, timestamp since the epoch, optional
email:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['email'];?>
', //user's email, mandatory
landline_number:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['phone'];?>
', //numeric, user's landline number, optional
mobile_phone_number:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['mobile'];?>
', //numeric, user's mobile phone number, optional
address_line_1:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['address1'];?>
', //user's 1st address line, optional
addresse_line_2: '<?php echo $_smarty_tpl->tpl_vars['INFO']->value['address2'];?>
', //user's 2nd address line, optional
city:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['city'];?>
', //user's city, optional
postal_code:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['postcode'];?>
', //user's postal code, optional
country:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['country'];?>
', //user's country, ISO 3166-1 alpha-2, optional
language:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['language'];?>
', //user's language, ISO 639-1, optional
optin_email:<?php echo $_smarty_tpl->tpl_vars['INFO']->value['optin'];?>
, //user's email optin, integer, only 1 (user optin) or 0 (user non optin), mandatory
optin_sms:1, //user's sms optin, integer, only 1 (user optin) or 0 (user non optin), mandatory
date_creation:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['datecreated'];?>
', //user's account date creation,timestamp since the epoch, mandatory
date_update:'<?php echo $_smarty_tpl->tpl_vars['INFO']->value['dateupdated'];?>
', //user's account last date update,timestamp since the epoch, mandatory
add_group:[<?php  $_smarty_tpl->tpl_vars['name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['name']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['INFO']->value['group']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['name']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['name']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['name']->key => $_smarty_tpl->tpl_vars['name']->value) {
$_smarty_tpl->tpl_vars['name']->_loop = true;
 $_smarty_tpl->tpl_vars['name']->iteration++;
 $_smarty_tpl->tpl_vars['name']->last = $_smarty_tpl->tpl_vars['name']->iteration === $_smarty_tpl->tpl_vars['name']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['group']['last'] = $_smarty_tpl->tpl_vars['name']->last;
?>'<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
'<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['group']['last']) {?> <?php } else { ?>,<?php }?><?php } ?>],
del_group:['AZERTY'], //withdrawal of the user from mentioned group(s), optional
contact_details_1:'azerty', //contact detail_1, may be used for user's further detail, optional
contact_details_2:'azerty', //contact detail_2, may be used for user's further detail, optional
contact_details_3:'azerty', //contact detail_3, may be used for user's further detail, optional
contact_details_4:'azerty', //contact detail_4, may be used for user's further detail, optional
contact_details_5:'azerty' //contact detail_5, may be user for user's further detail, optional
};
geerio.sendData(data, 'contact'); // action sur le bouton de création de compte.
</script><?php }} ?>
