<?php
/*
Plugin Name: Usersnap
Plugin URI: http://www.usersnap.com
Description: With usersnap your users can give you a visual feedback of your page.
Version: 0.6
Author: Usersnap
Author URI: http://usersnap.com
License: GPL v2
*/

define('USERSNAP_VERSION', '0.6');
define('USERSNAP_PLUGIN_URL', plugin_dir_url( __FILE__ ));

if ( is_admin() ){ // admin actions
  add_action( 'admin_init', 'us_register_settings' );
  add_action( 'admin_menu', 'us_plugin_menu' );
} else {
	add_action('wp_head', 'us_add_js');
}

function us_add_js() {
	$options = get_option('usersnap_options');
	if ($options['button-valign']==null) {
		$options['button-valign'] = "bottom";
	}
	if ($options['button-halign']==null) {
		$options['button-halign'] = "right";
	}
	if ($options['api-key']!=="") {
		?>
		<script type="text/javascript">
			var _usersnapconfig = {
				apiKey: '<?php echo $options['api-key']; ?>',
				valign: '<?php echo $options['button-valign']; ?>',
			    halign: '<?php echo $options['button-halign']; ?>'
			}; 
			(function() {
			    var s = document.createElement('script');
			    s.type = 'text/javascript';
			    s.async = true;
			    s.src = '//api.usersnap.com/usersnap.js';
			    var x = document.getElementsByTagName('head')[0];
			    x.appendChild(s);
			})();
		</script>
		<?php
	}
} 

function us_plugin_menu() {
	add_menu_page('Usersnap Settings', 'Usersnap', 'administrator', __FILE__, 'us_option_page' /*,plugins_url('/images/icon.png', __FILE__)*/);
}

function us_register_settings() {
	register_setting( 'usersnap_options', 'usersnap_options', 'usersnap_options_validate');
	add_settings_section('usersnap_main', 'Main Settings', 'usersnap_section_text', 'usersnap');
	add_settings_field('us-api-key', 'API-Key', 'usersnap_input_text', 'usersnap', 'usersnap_main');
	add_settings_field('us-button-valign', 'Button Vertical Alignment', 'usersnap_input_vbutton', 'usersnap', 'usersnap_main');
	add_settings_field('us-button-halign', 'Button Horizontal Alignment', 'usersnap_input_hbutton', 'usersnap', 'usersnap_main');
}

function usersnap_input_text() {
	$options = get_option('usersnap_options');
	?><input id="us-api-key" name="usersnap_options[api-key]" size="40" type="text" value="<?php echo $options['api-key']; ?>" /><?php
}

function usersnap_input_vbutton() {
	$options = get_option('usersnap_options');
	if ($options['button-valign']==null) {
		$options['button-valign'] = "bottom";
	}
	?><select id="us-button-valign" name="usersnap_options[button-valign]">
		<option value="top" <?php echo ($options['button-valign']=="top"?"selected":"")?>>top</option>
		<option value="middle" <?php echo ($options['button-valign']=="middle"?"selected":"")?>>middle</option>
		<option value="bottom" <?php echo ($options['button-valign']=="bottom"?"selected":"")?>>bottom</option>
	</select><?php
}

function usersnap_input_hbutton() {
	$options = get_option('usersnap_options');
	if ($options['button-halign']==null) {
		$options['button-halign'] = "right";
	}
	?><select id="us-button-halign" name="usersnap_options[button-halign]">
		<option value="left" <?php echo ($options['button-halign']=="left"?"selected":"")?>>left</option>
		<option value="right" <?php echo ($options['button-halign']=="right"?"selected":"")?>>right</option>
	</select><?php
}

function usersnap_section_text() {
}

function usersnap_options_validate($input) {
	return $input;
}


function us_option_page() {
	if (!current_user_can('administrator'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	?>
	<div class="wrap">
	<form method="post" action="options.php">
	<p><small>Optain an API-Key at <a href="http://www.usersnap.com" target="_blank">http://www.usersnap.com</a></small></p>
	<?php settings_fields( 'usersnap_options' ); ?>
    <?php do_settings_sections('usersnap'); ?>
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	</form>
	</div>
	<?php
}
