<?php defined('ABSPATH') or die();

class NUNO_SARMENTO_CCJ_Custom_Css_Js {

  private $rn = "\r\n";
  private $break = "[biw_br]";

    function __construct() {

        // admin functions
        add_action('admin_menu', array($this, 'nuno_sarmento_ccj_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'nuno_sarmento_ccj_enqueue_scripts'));
        // front-end functions
        add_action('wp_print_footer_scripts', array($this, 'nuno_sarmento_ccj_add_css'), PHP_INT_MAX);
        add_action('wp_print_footer_scripts', array($this, 'nuno_sarmento_ccj_add_js'), PHP_INT_MAX);

    }

    public function nuno_sarmento_ccj_enqueue_scripts() {
      wp_register_style('TEND_CCJ_CSS_JS', plugins_url('../assets/css/style.css', __FILE__), null, '1.0', 'screen');
      wp_enqueue_style('TEND_CCJ_CSS_JS');
    }

    public function nuno_sarmento_ccj_admin_menu() {
      // Add a page to manage this plugin's settings
      add_menu_page(
        'NS  CSS & JS',
        'NS  CSS & JS',
        'manage_options',
        'tend-custom-css-js',
        array($this, 'nuno_sarmento_ccj_dashboard'),
        'dashicons-media-code', // icon_url
  			22 // position
      );

    }

    function nuno_sarmento_ccj_dashboard() {
      if (isset($_POST['tend_ccj_save_button'])) {
        $this->save_meta_box_data();
        echo '<div class="updated notice">' . __('Settings saved.', 'tend-custom-css-js') . '</div>';
      }

      $ns_dashboard_css ='

      <style media="screen">
      .header__ns_nsss:after { content: " "; display: block; height: 29px; width: 15%; position: absolute;
      	top: 3%; right: 25px; background-image: url(//ps.w.org/nuno-sarmento-social-icons/assets/icon-128x128.png?rev=1588574); background-size:128px 128px; height: 128px; width: 128px;
      }
      .header__ns_nsss{ background: white; height: 150px; width: 100%; float: left;}
      .header__ns_nsss h2 {padding: 35px;font-size: 27px;}
      @media only screen and (max-width: 480px) {
      	.header__ns_nsss:after { content: " "; display: block; height: 29px; width: 15%; position: absolute;
      		top: 6%; right: 25px; background-image: url(//ps.w.org/nuno-sarmento-social-icons/assets/icon-128x128.png?rev=1588574); background-size:50px 50px; height: 50px; width: 50px;
      	}
        .header__ns_nsss h2 {padding: 30px;font-size: 30px;line-height: 34px;}
      }
      .sub_header__ns_nsss { float: left; width: 100%; margin-bottom: 20px; position: relative; }
      </style>

      <div class="wrap">
    		<div class="header__ns_nsss">
    			<h2>Nuno Sarmento Custom CSS - JS</h2>
    		</div>
        <div class="sub_header__ns_nsss">
           <br></br>
          <p><strong>Enter your custom Javascript and CSS that will be used on each page and post of your site.</strong></p>
        </div>
      </div>

      ';

      echo $ns_dashboard_css;
      echo '<form method="POST">';
      $this->meta_box_css();
      $this->meta_box_js_external();
      $this->meta_box_js();
      echo '<input type="submit" class="button action clearfix" name="tend_ccj_save_button" value="' . __('Save', 'tend-custom-css-js'). '"/>';
      echo '</form>';
    }


    public function custom_scripts_1_callback() {
  		printf(
  			'<textarea class="large-text" rows="5" name="nuno_sarmento_custom_css_js_option_name[custom_scripts_1]" id="custom_scripts_1">%s</textarea>',
  			isset( $this->nuno_sarmento_custom_css_js_options['custom_scripts_1'] ) ? esc_attr( $this->nuno_sarmento_custom_css_js_options['custom_scripts_1']) : ''
  		);
  	}


    function add_meta_box($type, $post, $text) {

      $value = get_option('_tend_ccj_custom_'.$type, '');

      if ($text) {
        echo "<p>{$text}</p>";
      }
      echo '<textarea class="biw_textarea" name="tend_ccj_custom_'.$type.'">' . esc_textarea(stripslashes($value)) . '</textarea>';
    }

    function meta_box_css($post = null) {
      $this->add_meta_box('css', $post, __('Enter your custom CSS here (no need to add &lt;style&gt;&lt;/style&gt; )', 'tend-custom-css-js'));
    }

    function meta_box_js($post = null) {
      $this->add_meta_box('js', $post, __('Enter your custom javascript here (no need to add &lt;script&gt;&lt;/script&gt;)', 'tend-custom-css-js'));
    }

    function meta_box_js_external($post = null) {
      $this->add_meta_box('js_external', $post, __('Add your external javascript url, one entry per line.', 'tend-custom-css-js'));
    }


    function save_meta_box_data($post_id = 0) {

      $exts = array('css','js', 'js_external');

      foreach ($exts as $ext) {

        if (isset($_POST['tend_ccj_custom_'.$ext])) {
          $value = $_POST['tend_ccj_custom_'.$ext];
          //$value = str_replace($this->rn, $this->break, $value);
          //$value = sanitize_text_field( $value );
          $value = str_replace($this->break, $this->rn, $value);
          update_option('_tend_ccj_custom_'.$ext, $value, false);

        }
      }

    }

    function nuno_sarmento_ccj_insert_script($script = null) {
      if ($script === null) return;
      $script = trim(stripslashes($script));
      if (!$script || $script == '') return;
      echo '<script type="text/javascript"';
      if (stripos($script, '://') == 4 || stripos($script, '://') == 5) {
        echo " src=\"{$script}\">";
      } else {
        echo ">\r\n{$script}\r\n";
      }
      echo "</script>\r\n";
    }

    function nuno_sarmento_ccj_add_js() {
      $exts = array('js_external', 'js');
      foreach ($exts as $ext) {
        if ($value = get_option('_tend_ccj_custom_'.$ext, '')) {
          $this->nuno_sarmento_ccj_insert_script($value);
        }
      }
    }

    function nuno_sarmento_ccj_add_css() {

      if ($value = get_option('_tend_ccj_custom_css', '' )) {
        echo "<style>" . $value . "</style>";
      }
    }

}

if (!defined('DOING_AJAX') || !DOING_AJAX) {
  $nuno_sarmento_ccj_custom_css_js = new NUNO_SARMENTO_CCJ_Custom_Css_Js();
}
