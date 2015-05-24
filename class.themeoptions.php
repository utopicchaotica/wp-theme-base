<?php
/**
 * Master theme class
 * 
 * @package Bolts
 * @since 1.0
 */
class My_Theme_Options {
	
	private $sections;
	private $checkboxes;
	private $settings;
	// private $file_dir;
	
	/**
	 * Construct
	 *
	 * @since 1.0
	 */
	public function __construct() {
		// $this->file_dir = get_bloginfo('template_directory');
		// This will keep track of the checkbox options for the validate_settings function.
		$this->checkboxes = array();
		$this->settings = array();
		$this->get_settings();
		
		$this->sections['general']      = __( 'General Settings' );
		$this->sections['socialmedia']   = __( 'Social Media' );
		$this->sections['album']   = __( 'Album Details' );
		$this->sections['reset']        = __( 'Reset to Defaults' );
		// $this->sections['about']        = __( 'About' );
		
		add_action( 'admin_menu', array( &$this, 'add_pages' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		
		if ( ! get_option( 'mytheme_options' ) )
			$this->initialize_settings();
	}
	
	/**
	 * Add options page
	 *
	 * @since 1.0
	 */
	public function add_pages() {
		// $admin_page = add_theme_page( __( 'Theme Options' ), __( 'Theme Options' ), 'manage_options', 'mytheme-options', array( &$this, 'display_page' ) );
		$admin_page = add_menu_page( __( 'Theme Options' ), __( 'Theme Options' ), 'manage_options', 'mytheme-options', array( &$this, 'display_page' ) );
		
		add_action( 'admin_print_scripts-' . $admin_page, array( &$this, 'scripts' ) );
		add_action( 'admin_print_styles-' . $admin_page, array( &$this, 'styles' ) );
	}
	
	/**
	 * Create settings field
	 *
	 * @since 1.0
	 */
	public function create_setting( $args = array() ) {
		$defaults = array(
			'id'      => 'default_field',
			'title'   => __( 'Default Field' ),
			'desc'    => __( 'This is a default description.' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general',
			'choices' => array(),
			'class'   => ''
		);
		
		extract( wp_parse_args( $args, $defaults ) );
		
		$field_args = array(
			'type'      => $type,
			'id'        => $id,
			'desc'      => $desc,
			'std'       => $std,
			'choices'   => $choices,
			'label_for' => $id,
			'class'     => $class
		);
		
		if ( $type == 'checkbox' )
			$this->checkboxes[] = $id;
		
		add_settings_field( $id, $title, array( $this, 'display_setting' ), 'mytheme-options', $section, $field_args );
	}
	
	/**
	 * Display options page
	 *
	 * @since 1.0
	 */
	public function display_page() {
		echo <<<HTML
<div class="wrap">
	<div class="icon32" id="icon-options-general"></div>
	<h2>Theme Options</h2>
HTML;
	
		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true ) {
			echo <<<HTML
	<div class="updated fade"><p>Theme options updated.</p></div>
HTML;
		}

		
		echo '<form action="options.php" method="post">';
	
		settings_fields( 'mytheme_options' );
		echo <<<HTML
	<div class="ui-tabs">
		<ul class="ui-tabs-nav">
HTML;
		
		foreach ( $this->sections as $section_slug => $section )
			echo '<li><a href="#' . $section_slug . '">' . $section . '</a></li>';
		
		echo '</ul>';
		do_settings_sections( $_GET['page'] );
		
		echo <<<HTML
	</div>
	<p class="submit"><input name="Submit" type="submit" class="button-primary" value="Save Changes" /></p>
	</form>
HTML;
	
	echo <<<JS
<script type="text/javascript">
		jQuery(document).ready(function($) {
			var sections = [];
JS;
			
		foreach ( $this->sections as $section_slug => $section )
			echo "sections['$section'] = '$section_slug';";
			
		echo <<<JS
			var wrapped = $('.wrap h3').wrap('<div class="ui-tabs-panel">');
			wrapped.each(function() {
				$(this).parent().append($(this).parent().nextUntil('div.ui-tabs-panel'));
			});
			$('.ui-tabs-panel').each(function(index) {
				$(this).attr('id', sections[$(this).children('h3').text()]);
				if (index > 0)
					$(this).addClass('ui-tabs-hide');
			});
			$('.ui-tabs').tabs({
				fx: { opacity: 'toggle', duration: 'fast' }
			});
			
			$('input[type=text], textarea').each(function() {
				if ($(this).val() == $(this).attr('placeholder') || $(this).val() == '')
					$(this).css('color', '#999');
			});
			
			$('input[type=text], textarea').focus(function() {
				if ($(this).val() == $(this).attr('placeholder') || $(this).val() == '') {
					$(this).val('');
					$(this).css('color', '#000');
				}
			}).blur(function() {
				if ($(this).val() == '' || $(this).val() == $(this).attr('placeholder')) {
					$(this).val($(this).attr('placeholder'));
					$(this).css('color', '#999');
				}
			});
			
			$('.wrap h3, .wrap table').show();
			
			// This will make the 'warning' checkbox class really stand out when checked.
			// I use it here for the Reset checkbox.
			$('.warning').change(function() {
				if ($(this).is(':checked'))
					$(this).parent().css('background', '#c00').css('color', '#fff').css('fontWeight', 'bold');
				else
					$(this).parent().css('background', 'none').css('color', 'inherit').css('fontWeight', 'normal');
			});
			
			// Browser compatibility
			if ($.browser.mozilla) $('form').attr('autocomplete', 'off');
		});
	</script>
</div>
JS;
	}
	
	/**
	 * Description for section
	 *
	 * @since 1.0
	 */
	public function display_section() {
		// code
	}
	
	/**
	 * Description for About section
	 *
	 * @since 1.0
	 */
	public function display_about_section() {
		// This displays on the "About" tab. Echo regular HTML here, like so:
		// echo '<p>Copyright 2011 me@example.com</p>';
	}
	
	/**
	 * HTML output for text field
	 *
	 * @since 1.0
	 */
	public function display_setting( $args = array() ) {
		extract( $args );
		
		$options = get_option( 'mytheme_options' );
		
		if ( ! isset( $options[$id] ) && $type != 'checkbox' )
			$options[$id] = $std;
		elseif ( ! isset( $options[$id] ) )
			$options[$id] = 0;
		
		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . $class;
		
		switch ( $type ) {
			case 'heading':
				echo '</td></tr><tr valign="top"><td colspan="2"><h4>' . $desc . '</h4>';
				break;
			
			case 'checkbox':
				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="mytheme_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' /> <label for="' . $id . '">' . $desc . '</label>';
				break;
			
			case 'select':
				echo '<select class="select' . $field_class . '" name="mytheme_options[' . $id . ']">';
				
				foreach ( $choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[$id], $value, false ) . '>' . $label . '</option>';
				
				echo '</select>';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;
			
			case 'radio':
				$i = 0;
				foreach ( $choices as $value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="mytheme_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';
					if ( $i < count( $options ) - 1 )
						echo '<br />';
					$i++;
				}
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;
			
			case 'textarea':
				echo '<textarea class="' . $field_class . '" id="' . $id . '" name="mytheme_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="30">' . wp_htmledit_pre( $options[$id] ) . '</textarea>';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;
			
			case 'password':
				echo '<input class="regular-text' . $field_class . '" type="password" id="' . $id . '" name="mytheme_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" />';
				
				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';
				break;
			
			case 'upload':
				echo '<input id="' . $id . '" class="upload-url' . $field_class . '" type="text" name="mytheme_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" />';
				echo '<input id="st_upload_button" class="st_upload_button" type="button" name="upload_button" value="Upload" />';
				if ( $desc != '' )
				echo '<br />
				<span class="description">' . $desc . '</span>';
				break;

			case 'text':
			default:
		 		echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="mytheme_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';
		 		
		 		if ( $desc != '' )
		 			echo '<br /><span class="description">' . $desc . '</span>';
		 		break;
		}
	}
	
	/**
	 * Settings and defaults
	 * 
	 * @since 1.0
	 */
	public function get_settings() {
		/* General Settings
		===========================================*/
		$this->settings['mainimage'] = array(
			'section' => 'general',
			'title'   => __( 'Main image' ),
			'desc'    => __( 'Image in the header.' ),
			'type'    => 'upload',
			'std'     => __(get_bloginfo('template_directory') . '/img/mainImg.jpg')
		);
		$this->settings['newsfeed_num_news'] = array(
			'section' => 'general',
			'title'   => __('News feed posts'),
			'desc'    => __('Enter the number of latest news summaries you want to show on the home page.'),
			'type'    => 'text',
			'std'     => __('2')
		);
		$this->settings['newsfeed_order'] = array(
			'section' => 'general',
			'title'   => __('News feed order'),
			'desc'    => __('Choose the chronological order of the news feed posts.'),
			'type'    => 'select',
			'std'     => 'Newest to oldest',
			'choices' => array(
				'choice1' => 'Newest to oldest',
				'choice2' => 'Oldest to newst'
			)
		);
		$this->settings['homevid_url'] = array(
			'section' => 'general',
			'title'   => __('Featured video'),
			'desc'    => __('Put the URL of the video you want to show on the home page here.'),
			'type'    => 'text',
			'std'     => ''
		);
		$this->settings['show_mailinglist_sidebar'] = array(
			'section' => 'general',
			'title'   => __('Show Mailing List (Sidebar)'),
			'desc'    => __('Check the box if you want to show the "Newsletter" section in the sidebar.'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->settings['mailinglist_html'] = array(
			'section' => 'general',
			'title'   => __('Mailing List HTML Code'),
			'desc'    => __('Copy and paste the HTML code into the textbox above.'),
			'type'    => 'textarea',
			'std'     => ''
		);
		$this->settings['mailinglist_css'] = array(
			'section' => 'general',
			'title'   => __('Mailing List CSS Code'),
			'desc'    => __('Cut any mention of CSS from the previous section and paste the code into the textbox above.'),
			'type'    => 'textarea',
			'std'     => ''
		);
		
		/* Social Media
		===========================================*/
		$this->settings['fb_url'] = array(
			'section' => 'socialmedia',
			'title'   => __( 'Facebook' ),
			'desc'    => __( 'Link to your Facebook page.' ),
			'type'    => 'text',
			'std'     => 'http://www.facebook.com/'
		);
		$this->settings['tw_url'] = array(
			'section' => 'socialmedia',
			'title'   => __( 'Twitter' ),
			'desc'    => __( 'Link to your Twitter page.' ),
			'type'    => 'text',
			'std'     => 'http://www.twitter.com/'
		);
		$this->settings['yt_url'] = array(
			'section' => 'socialmedia',
			'title'   => __( 'Youtube' ),
			'desc'    => __( 'Link to your Youtube page.' ),
			'type'    => 'text',
			'std'     => 'http://www.youtube.com/'
		);
		$this->settings['ms_url'] = array(
			'section' => 'socialmedia',
			'title'   => __( 'MySpace' ),
			'desc'    => __( 'Link to your MySpace page.' ),
			'type'    => 'text',
			'std'     => 'http://www.myspace.com/'
		);
		$this->settings['show_twitter_sidebar'] = array(
			'section' => 'socialmedia',
			'title'   => __('Show Twitter Widget (Sidebar)'),
			'desc'    => __('Check the box if you want to show the "Twitter" section in the sidebar.'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->settings['twwdgt'] = array(
			'section' => 'socialmedia',
			'title'   => __( 'Twitter Widget HTML Code' ),
			'desc'    => __( 'Go <a href="https://twitter.com/about/resources/widgets/widget_profile" target="_blank">here</a> to create and customize your widget, and then copy and paste the resulting code into the textbox above.' ),
			'type'    => 'textarea',
			'std'     => ''
		);
				
		/* Album Details
		===========================================*/
		$this->settings['show_albumdetails_sidebar'] = array(
			'section' => 'album',
			'title'   => __('Show Album Details (Sidebar)'),
			'desc'    => __('Check the box if you want to show the "Buy the Album" section in the sidebar.'),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->settings['album_cover'] = array(
			'section' => 'album',
			'title'   => __( 'Album cover' ),
			'desc'    => __( 'The image of the album cover.' ),
			'type'    => 'upload',
			'std'     => ''
		);
		$this->settings['album_name'] = array(
			'section' => 'album',
			'title'   => __( 'Album name' ),
			'desc'    => __( 'The name of the album.' ),
			'type'    => 'text',
			'std'     => ''
		);
		$this->settings['album_link'] = array(
			'section' => 'album',
			'title'   => __( 'iTunes link' ),
			'desc'    => __( 'iTunes link for the album.' ),
			'type'    => 'text',
			'std'     => ''
		);
		$this->settings['album_descrip'] = array(
			'section' => 'album',
			'title'   => __( 'Album description' ),
			'desc'    => __( 'Description of the album.' ),
			'type'    => 'textarea',
			'std'     => ''
		);
				
		/* Reset
		===========================================*/
		$this->settings['reset_theme'] = array(
			'section' => 'reset',
			'title'   => __( 'Reset theme' ),
			'type'    => 'checkbox',
			'std'     => 0,
			'class'   => 'warning', // Custom class for CSS
			'desc'    => __( 'Check this box and click "Save Changes" below to reset theme options to their defaults.' )
		);
		
	}
	
	/**
	 * Initialize settings to their default values
	 * 
	 * @since 1.0
	 */
	public function initialize_settings() {
		$default_settings = array();
		foreach ( $this->settings as $id => $setting ) {
			if ( $setting['type'] != 'heading' )
				$default_settings[$id] = $setting['std'];
		}
		
		update_option( 'mytheme_options', $default_settings );
	}
	
	/**
	* Register settings
	*
	* @since 1.0
	*/
	public function register_settings() {
		register_setting( 'mytheme_options', 'mytheme_options', array ( &$this, 'validate_settings' ) );
		
		foreach ( $this->sections as $slug => $title ) {
			if ( $slug == 'about' )
				add_settings_section( $slug, $title, array( &$this, 'display_about_section' ), 'mytheme-options' );
			else
				add_settings_section( $slug, $title, array( &$this, 'display_section' ), 'mytheme-options' );
		}
		
		$this->get_settings();
		
		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$this->create_setting( $setting );
		}
	}
	
	/**
	* jQuery Tabs
	*
	* @since 1.0
	*/
	public function scripts() {
		wp_print_scripts( 'jquery-ui-tabs' );
		//Media Uploader Scripts
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		// wp_register_script('my-upload', get_bloginfo( 'stylesheet_directory' ) . '/js/uploader.js', array('jquery','media-upload','thickbox'));
		wp_register_script('my-upload', get_bloginfo('template_directory') . '/functions/my-script.js', array('jquery','media-upload','thickbox'));
		wp_enqueue_script('my-upload');
	}
	
	/**
	* Styling for the theme options page
	*
	* @since 1.0
	*/
	public function styles() {
		// wp_register_style( 'mytheme-admin', get_bloginfo( 'stylesheet_directory' ) . '/mytheme-options.css' );
		wp_register_style( 'mytheme-admin', 
		get_bloginfo('template_directory') . '/functions/functions.css' );
		wp_enqueue_style( 'mytheme-admin' );
		//Media Uploader Style
		wp_enqueue_style('thickbox');
	}
	
	/**
	* Validate settings
	*
	* @since 1.0
	*/
	public function validate_settings( $input ) {
		if ( ! isset( $input['reset_theme'] ) ) {
			$options = get_option( 'mytheme_options' );
			
			foreach ( $this->checkboxes as $id ) {
				if ( isset( $options[$id] ) && ! isset( $input[$id] ) )
					unset( $options[$id] );
			}
			
			return $input;
		}
		return false;
	}
}

$theme_options = new My_Theme_Options();

function mytheme_option( $option ) {
	$options = get_option( 'mytheme_options' );
	if (isset($options[$option])) return $options[$option];
	else return false;
}
?>