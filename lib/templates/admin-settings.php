<h1>LeadsNearby Tech Profiles Settings</h1>
<form method="post" action="options.php">
<?php
	settings_fields('lnb-tech-pro-group');
	do_settings_sections( 'lnb-tech-pro-settings' ); 
	submit_button();
?>
</form>