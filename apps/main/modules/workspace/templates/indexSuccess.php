<?php echo stylesheet_tag('../js/extjs/ux/css/bubble.css') ?>
<?php echo stylesheet_tag('../js/extjs/ux/css/GroupTab.css') ?>
<?php echo stylesheet_tag('../js/extjs/ux/css/ux-all.css') ?>
<?php echo javascript_include_tag( 'extjs/ux/BubblePanel.js') ?>
<?php echo javascript_include_tag( 'extjs/ux/GroupTab.js') ?>
<?php echo javascript_include_tag( 'extjs/ux/GroupTabPanel.js') ?>
<?php echo javascript_include_tag( 'extjs/ux/RowEditor.js') ?>
<?php echo javascript_include_tag( 'workspace/workspace.js') ?>
<?php echo javascript_include_tag( 'workspace/register.js') ?>
<?php echo javascript_include_tag( 'workspace/contacts.js') ?>
<?php echo javascript_include_tag( 'workspace/loans.js') ?>
<script type="text/javascript">
Ext.onReady(workspace);
</script>
<style type="text/css">
body {
	background-color: #4E79B2 !important;
}
</style>

<div id="floating_window"	class="x-hidden"></div>
<div id="details_floating_window"	class="x-hidden"></div>
<div id="titulo">
<div style="float: left; text-align: center;"><font face="arial"
	size="6" color="#4E79B2">Bienvenido a Zahler</font></div>

<div class="nomostrar" style="float: right; padding: 0px 10px 0px 10px;">
<form>
<div>
<div id="logout_button"></div>
<!--<button type="button" name="boton_salir"--> <!--	onClick="cerrarSession( function(){Ext.getCmp('panel_servicios').setActiveGroup(0);});"-->
<!--	style='padding: 5px 0px 0px 0px;'>Logout</button>--></div>
</form>
</div>
</div>
<div></div>
