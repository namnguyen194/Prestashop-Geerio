<?php /* Smarty version Smarty-3.1.19, created on 2016-07-29 03:45:55
         compiled from "C:\xampp\htdocs\ps-v3\admin484po0ipm\themes\default\template\helpers\list\list_action_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14081579b09b3ce5994-25721670%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '67f0354de7ca9574583bdfefbe8aeb091b9bb070' => 
    array (
      0 => 'C:\\xampp\\htdocs\\ps-v3\\admin484po0ipm\\themes\\default\\template\\helpers\\list\\list_action_edit.tpl',
      1 => 1465977674,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14081579b09b3ce5994-25721670',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_579b09b3cf1516_54979426',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_579b09b3cf1516_54979426')) {function content_579b09b3cf1516_54979426($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="edit">
	<i class="icon-pencil"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
