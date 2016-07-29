<?php /* Smarty version Smarty-3.1.19, created on 2016-07-29 05:12:46
         compiled from "C:\xampp\htdocs\ps-v3\modules\blockspecials\views\templates\hook\blockspecials-home.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2012579b1e0eab9fb8-22355354%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd4914abe58e0a9610464f1fc3560e2f5bc16a2e9' => 
    array (
      0 => 'C:\\xampp\\htdocs\\ps-v3\\modules\\blockspecials\\views\\templates\\hook\\blockspecials-home.tpl',
      1 => 1465977676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2012579b1e0eab9fb8-22355354',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'specials' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_579b1e0eac5b39_71398761',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_579b1e0eac5b39_71398761')) {function content_579b1e0eac5b39_71398761($_smarty_tpl) {?>
<?php if (isset($_smarty_tpl->tpl_vars['specials']->value)&&$_smarty_tpl->tpl_vars['specials']->value) {?>
	<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./product-list.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('products'=>$_smarty_tpl->tpl_vars['specials']->value,'class'=>'blockspecials tab-pane','id'=>'blockspecials'), 0);?>

<?php } else { ?>
<ul id="blockspecials" class="blockspecials tab-pane">
	<li class="alert alert-info"><?php echo smartyTranslate(array('s'=>'No special products at this time.','mod'=>'blockspecials'),$_smarty_tpl);?>
</li>
</ul>
<?php }?>
<?php }} ?>
