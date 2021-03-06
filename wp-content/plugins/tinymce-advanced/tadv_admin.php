<?php

if ( ! defined( 'TADV_ADMIN_PAGE' ) ) {
	exit;
}
	
// TODO
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die('Access denied');
}

$message = '';

// TODO admin || SA
if ( isset( $_POST['tadv_uninstall'] ) ) {
	check_admin_referer( 'tadv-uninstall' );
	$this->remove_settings(true);

	?>
	<div class="updated" style="margin-top:30px;">
	<p><?php _e( 'All options have been removed from the database. You can', 'tadv'); ?> <a href="plugins.php"><?php _e('deactivate TinyMCE Advanced', 'tadv'); ?></a> <?php _e('or', 'tadv'); ?> <a href=""> <?php _e('reload this page', 'tadv'); ?></a> <?php _e('to reset them to the default values.', 'tadv'); ?></p>
	</div>
	<?php

	return;
}

if ( ! $this->check_minimum_supported_version() ) {
	?>
	<div class="error" style="margin-top:30px;">
	<p><?php _e( 'This plugin requires WordPress version 3.9 or newer. Please upgrade your WordPress installation or download an', 'tadv'); ?> <a href="http://wordpress.org/extend/plugins/tinymce-advanced/download/"><?php _e('older version of the plugin.', 'tadv'); ?></a></p>
	</div>
	<?php

	return;
}

$imgpath = TADV_URL . 'images/';
$tadv_options_updated = false;
$settings = $admin_settings = array();

if ( isset( $_POST['tadv-save'] ) ) {
	check_admin_referer( 'tadv-save-buttons-order' );
	$options_array = $admin_settings_array = $disabled_plugins = $plugins_array = array();

	// User settings
	for ( $i = 1; $i < 5; $i++ ) {
		$buttons = $this->parse_buttons( 'tb' . $i );
		// Layer plugin buttons??
		$buttons = str_replace( 'insertlayer', 'insertlayer,moveforward,movebackward,absolute', $buttons );
		$settings['toolbar_' . $i] = $buttons;
	}

	if ( ! empty( $_POST['advlist'] ) ) {
		$options_array[] = 'advlist';
	}

	if ( ! empty( $_POST['contextmenu'] ) ) {
		$options_array[] = 'contextmenu';
	}

	if ( ! empty( $_POST['menubar'] ) ) {
		$options_array[] = 'menubar';
		$plugins_array = array( 'anchor', 'code', 'insertdatetime', 'nonbreaking', 'print', 'searchreplace', 'table', 'visualblocks', 'visualchars' );
	}

	// Admin settings, TODO
	if ( ! empty( $_POST['importcss'] ) ) {
		$admin_settings_array[] = 'importcss';
	}

	if ( ! empty( $_POST['no_autop'] ) ) {
		$admin_settings_array[] = 'no_autop';
	}

	if ( ! empty( $_POST['editorstyle'] ) ) {
		$admin_settings_array[] = 'editorstyle';
	}

	if ( ! empty( $_POST['disabled_plugins'] ) && is_array( $_POST['disabled_plugins'] ) ) {
		foreach( $_POST['disabled_plugins'] as $plugin ) {
			if ( in_array( $this->all_plugins, $plugin, true ) ) {
				$disabled_plugins[] = $plugin;
			}
		}
	}

	// Admin options
	$admin_settings['options'] = implode( ',', $admin_settings_array );
	$admin_settings['disabled_plugins'] = implode( ',', $disabled_plugins );

	$this->admin_settings = $admin_settings;
	update_option( 'tadv_admin_settings', $admin_settings );

	// User options
	// TODO allow editors, authors and contributors some access
	$settings['options'] = implode( ',', $options_array );
	$this->settings = $settings;
	$this->load_settings();

	// Merge the submitted plugins and from the buttons
	$settings['plugins'] = implode( ',', $this->get_plugins( $plugins_array ) );
	$this->plugins = $settings['plugins'];

	// Save the new settings
	update_option( 'tadv_settings', $settings );

} elseif ( isset( $_POST['tadv-restore-defaults'] ) ) {
	// TODO admin || SA
	$this->admin_settings = $this->default_admin_settings;
	update_option( 'tadv_admin_settings', $this->default_admin_settings );

	// can 'save_posts' ?
	$this->settings = $this->default_settings;
	update_option( 'tadv_settings', $this->default_settings );

	$message = '<div class="updated"><p>' .  __('Default settings restored.', 'tadv') . '</p></div>';
} elseif ( isset( $_POST['tadv-export-settings'] ) ) {
	$this->load_settings();
	$output = array( 'settings' => $this->settings );
	// TODO admin || SA
	$output['admin_settings'] = $this->admin_settings;

	?>
	<div class="wrap">
	<h2><?php _e('TinyMCE Advanced Settings Export', 'tadv'); ?></h2>

	<div class="tadv-import-export">
	<p>
	<?php

	_e( 'The settings are exported as a JSON encoded string. ', 'tadv' );
	_e( 'Please copy the content and save it in a <b>text</b> (.txt) file, using a plain text editor like Notepad. ', 'tadv' );
	_e( 'It is important that the export is not changed in any way, no spaces, line breaks, etc.', 'tadv' );

	?>
	</p>

	<form action="">
		<p><textarea readonly="readonly" id="tadv-export"><?php echo json_encode( $output ); ?></textarea></p>
		<p><button type="button" class="button" id="tadv-export-select"><?php _e( 'Select All', 'tadv' ); ?></button></p>
	</form>
	<p><a href=""><?php _e('Back to Editor Settings', 'tadv'); ?></a></p>
	</div>
	</div>
	<?php

	return;
} elseif ( isset( $_POST['tadv-import-settings'] ) ) {
	// TODO ! admin && ! SA
	?>
	<div class="wrap">
	<h2><?php _e('TinyMCE Advanced Settings Import', 'tadv'); ?></h2>

	<div class="tadv-import-export">
	<p><?php

	_e( 'The settings are imported from a JSON encoded string. Please paste the exported string in the textarea below.', 'tadv' );

	?></p>

	<form action="" method="post">
		<p><textarea id="tadv-import" name="tadv-import"></textarea></p>
		<p>
			<button type="button" class="button" id="tadv-import-verify"><?php _e( 'Verify', 'tadv' ); ?></button>
			<input type="submit" class="button button-primary alignright" name="tadv-import-submit" value="<?php _e( 'Import', 'tadv' ); ?>" />
		</p>
		<?php wp_nonce_field('tadv-import'); ?>
		<p id="tadv-import-error"></p>
	</form>
	<p><a href=""><?php _e('Back to Editor Settings', 'tadv'); ?></a></p>
	</div>
	</div>
	<?php
	
	return;
} elseif ( isset( $_POST['tadv-import-submit'] ) && ! empty( $_POST['tadv-import'] ) && is_string( $_POST['tadv-import'] ) ) {
	check_admin_referer( 'tadv-import' );
	$import = json_decode( trim( wp_unslash( $_POST['tadv-import'] ) ), true );
	$settings = $admin_settings = array();

	if ( is_array( $import ) ) {
		if ( ! empty( $import['settings'] ) ) {
			$settings = $this->sanitize_settings( $import['settings'] );
		}

		if ( ! empty( $import['admin_settings'] ) ) {
			$admin_settings = $this->sanitize_settings( $import['admin_settings'] );
		}
	}

	if ( empty( $settings ) ) {
		$message = '<div class="error"><p>' .  __('Importing of settings failed.', 'tadv') . '</p></div>';
	} else {
		$this->admin_settings = $admin_settings;
		update_option( 'tadv_admin_settings', $admin_settings );
	
		// User options
		// TODO allow editors, authors and contributors some access
		$this->settings = $settings;
		$this->load_settings();
		
		// Merge the submitted plugins and from the buttons
		if ( ! empty( $settings['plugins'] ) ) {
			$settings['plugins'] = implode( ',', $this->get_plugins( explode( ',', $settings['plugins'] ) ) );
		}

		$this->plugins = $settings['plugins'];
		
		// Save the new settings
		update_option( 'tadv_settings', $settings );
	}
}

$this->load_settings();

if ( empty( $this->toolbar_1 ) && empty( $this->toolbar_2 ) && empty( $this->toolbar_3 ) && empty( $this->toolbar_4 ) ) {
	$message = '<div class="error"><p>' .  __('ERROR: All toolbars are empty. Default settings loaded.', 'tadv') . '</p></div>';

	$this->admin_settings = $this->default_admin_settings;
	$this->settings = $this->default_settings;
	$this->load_settings();
}

$used_buttons = array_merge( $this->toolbar_1, $this->toolbar_2, $this->toolbar_3, $this->toolbar_4 );
$all_buttons = $this->get_all_buttons();

?>
<div class="wrap" id="contain">
<h2><?php _e('Editor Settings', 'tadv'); ?></h2>
<?php

if ( isset( $_POST['tadv-save'] ) && empty( $message ) ) {
	?><div class="updated" id="message"><p><?php _e( 'Settings saved.', 'tadv' ); ?></p></div><?php
} else {
	echo $message;
}

?>
<form id="tadvadmin" method="post" action="">
<div id="tadvzones">

<p><?php _e( 'New in TinyMCE 4.0/WordPress 3.9 is the editor menu. When it is enabled, most buttons are also available as menu items.', 'tadv' ); ?></p>

<p><label>
<input type="checkbox" name="menubar" id="menubar" <?php if ( $this->check_setting( 'menubar' ) ) { echo ' checked="checked"'; } ?>>
<?php _e('Enable the editor menu.', 'tadv'); ?>
</label></p>

<p id="tadv-menu-img" <?php if ( $this->check_setting( 'menubar' ) ) { echo ' class="enabled"'; } ?>>&nbsp;</p>

<?php

for ( $i = 1; $i < 5; $i++ ) {
	$toolbar = "toolbar_$i";
	
	?>
	<div class="tadvdropzone">
	<ul id="tb<?php echo $i; ?>" class="container">
	<?php
	
	foreach( $this->$toolbar as $button ) {
		if ( strpos( $button, 'separator' ) !== false || in_array( $button, array( 'moveforward', 'movebackward', 'absolute' ) ) ) {
			continue;
		}

		if ( isset( $all_buttons[$button] ) ) {
			$name = $all_buttons[$button];
			unset( $all_buttons[$button] );
		} else {
			// error?..
			continue;
		}
	
		if ( strpos( $name, '<!' ) === 0 )
			$name = '';

		?>
		<li class="tadvmodule" id="<?php echo $button; ?>">
		<div class="tadvitem">
			<i class="mce-ico mce-i-<?php echo $button; ?>" title="<?php echo $name; ?>"></i>
			<span class="descr"> <?php echo $name; ?></span>
			<input type="hidden" class="tadv-button" name="tb<?php echo $i; ?>[]" value="<?php echo $button; ?>" />
		</div>
		</li>
		<?php

	}

	?>
	</ul></div>
	<?php
}

?>
</div>

<p><?php _e('Drag and drop buttons onto the toolbars above, or drag the buttons to rearrange them.', 'tadv'); ?></p>
<!--
<div id="length-error-message" class="tadv-error">
<?php _e('Adding too many buttons will make the toolbar too long and will not display correctly in TinyMCE!', 'tadv'); ?>
</div>
-->
<div id="unuseddiv">
<ul id="unused">
<?php

foreach( $all_buttons as $button => $name ) {
	if ( strpos( $button, 'separator' ) !== false )
		continue;

	if ( strpos( $name, '<!' ) === 0 )
		$name = '';

	?>
	<li class="tadvmodule" id="<?php echo $button; ?>">
	<div class="tadvitem">
		<i class="mce-ico mce-i-<?php echo $button; ?>" title="<?php echo $name; ?>"></i>
		<span class="descr"> <?php echo $name; ?></span>
		<input type="hidden" class="tadv-button" name="unused[]" value="<?php echo $button; ?>" />
	</div>
	</li>
	<?php

}

?>
</ul>
</div>

<p class="tadv-more-plugins"><?php _e( 'Also enable:' ); ?>
	<label>
	<input type="checkbox" name="advlist" id="advlist" <?php if ( $this->check_setting('advlist') ) echo ' checked="checked"'; ?> />
	<?php _e('List Style Options', 'tadv'); ?>
	</label>

	<label>
	<input type="checkbox"  name="contextmenu" id="contextmenu" <?php if ( $this->check_setting('contextmenu') ) echo ' checked="checked"'; ?> />
	<?php _e('Context Menu', 'tadv'); ?>
	</label>
</p>

<?php

if ( ! is_multisite() && current_user_can( 'manage_options' ) ) {

	?>
	<div class="advanced-options">
	<h3><?php _e('Advanced Options', 'tadv'); ?></h3>
	<?php
	
	if ( ! current_theme_supports( 'editor-style' ) ) {
	
		?>
		<p><?php
		_e('It seems your theme doesn\'t support customised styles for the editor. ', 'tadv');
		_e('You can create a CSS file named <code>editor-style.css</code> and upload it to your theme\'s directory. ', 'tadv');
		_e('After that, enable this setting.', 'tadv');
		?></p>

		<p>
			<label><input type="checkbox" name="editorstyle" id="editorstyle" <?php if ( $this->check_setting( 'editorstyle', true ) ) echo ' checked="checked"'; ?> />
			<?php _e('Import editor-style.css.', 'tadv'); ?></label>
		</p>
		<?php
	}

	?>
	<p>
		<label><input type="checkbox" name="importcss" id="importcss" <?php if ( $this->check_setting( 'importcss', true ) ) echo ' checked="checked"'; ?> />
		<?php _e('Load the CSS classes used in editor-style.css and replace the Styles sub-menu.', 'tadv'); ?></label>
	</p>

	<p>
		<label><input type="checkbox" name="no_autop" id="no_autop" <?php if ( $this->check_setting( 'no_autop', true ) ) echo ' checked="checked"'; ?> />
		<?php _e('Stop removing the &lt;p&gt; and &lt;br /&gt; tags when saving and show them in the Text editor', 'tadv'); ?></label>
		<br>
		<?php
		_e('This will make it possible to use more advanced coding in the HTML editor without the back-end filtering affecting it much. ', 'tadv');
		_e('However it may behave unexpectedly in rare cases, so test it thoroughly before enabling it permanently. ', 'tadv');
		_e('Line breaks in the HTML editor would still affect the output, in particular do not use empty lines, line breaks inside HTML tags or multiple &lt;br /&gt; tags.', 'tadv');
		?>
	</p>

	<p>
		<button class="button" type="button" id="tadv-remove-settings"><?php _e('Remove Settings', 'tadv'); ?></button>
		<input type="submit" class="button" name="tadv-export-settings" value="<?php _e( 'Export Settings', 'tadv' ); ?>" />
		<input type="submit" class="button" name="tadv-import-settings" value="<?php _e( 'Import Settings', 'tadv' ); ?>" />
	</p>
	</div>
	<?php

}
?>

<p class="tadv-submit">
	<?php wp_nonce_field( 'tadv-save-buttons-order' ); ?>
	<input class="button" type="submit" name="tadv-restore-defaults" value="<?php _e('Restore Default Settings', 'tadv'); ?>" />
	<input class="button-primary button-large" type="submit" name="tadv-save" value="<?php _e('Save Changes', 'tadv'); ?>" />
</p>
</form>

<div id="wp-adv-error-message" class="tadv-error">
<?php _e('The "Toolbar toggle" button shows/hides the second, third, and forth button rows. It will only work when it is in the first row and there are buttons in the second row.', 'tadv'); ?>
</div>

<div id="tadv-confirm-uninstall" style="">
<form method="post" action="">
<?php wp_nonce_field('tadv-uninstall'); ?>
<div><?php _e('Remove all settings from the database?', 'tadv'); ?>
	<input class="button" type="button" id="tadv-cancel" value="<?php _e('Cancel', 'tadv'); ?>" />
	<input class="button" type="submit" name="tadv_uninstall" value="<?php _e('Continue', 'tadv'); ?>" />
</div>
</form>
</div>
</div>
