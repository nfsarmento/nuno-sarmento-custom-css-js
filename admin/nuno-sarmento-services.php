<?php defined('ABSPATH') or die();



class NUNO_SARMENTO_CCJ_Custom_Css_Js {


  private $rn = "\r\n";
  private $break = "[biw_br]";


    function __construct() {

        // admin functions
        add_action('add_meta_boxes', array($this, 'nuno_sarmento_ccj_add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_box_data'));
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

      add_submenu_page(
        'tend-custom-css-js',
        'Global Scripts',
        'Global Scripts',
        'manage_options',
        'tend-custom-css-js',
        array($this, 'nuno_sarmento_ccj_dashboard')
      );
    }

    function nuno_sarmento_ccj_dashboard() {
      if (isset($_POST['tend_ccj_save_button'])) {
        $this->save_meta_box_data();
        echo '<div class="updated notice">' . __('Settings saved.', 'tend-custom-css-js') . '</div>';
      }
      echo "<h1>" . __('Nuno Sarmento Custom CSS - JS', 'tend-custom-css-js') . "</h1>";
      echo "<p>" . __('Enter your custom Javascript and CSS that will be used on each page and post on your site.', 'tend-custom-css-js') . "</p>";
      echo '<form method="POST">';
      $this->meta_box_css();
      $this->meta_box_js_external();
      $this->meta_box_js();
      echo '<input type="submit" class="button action clearfix" name="tend_ccj_save_button" value="' . __('Save', 'tend-custom-css-js'). '"/>';
      echo '</form>';
    }



    function nuno_sarmento_ccj_add_meta_boxes() {
      $screens = array( 'post', 'page' );
      foreach ( $screens as $screen ) {
        add_meta_box(
          'tend_ccj_custom_css',
          __( 'Custom CSS', 'tend-custom-css-js' ),
          array($this, 'meta_box_css'),
          $screen
        );
        add_meta_box(
          'tend_ccj_custom_js_external',
          __( 'External Javascripts', 'tend-custom-css-js' ),
          array($this, 'meta_box_js_external'),
          $screen
        );
        add_meta_box(
          'tend_ccj_custom_js',
          __( 'Custom Javascript', 'tend-custom-css-js' ),
          array($this, 'meta_box_js'),
          $screen
        );
      }
    }

    function add_meta_box($type, $post, $text) {
      wp_nonce_field( 'tend_ccj_meta_box_'.$type, 'tend_ccj_meta_box_nonce_'.$type);
      if ($post) {
        $value = get_post_meta( $post->ID, '_tend_ccj_custom_'.$type, true );
      } else {
        $value = get_option('_tend_ccj_custom_'.$type, '');
      }
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

      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
      }

      // Check the user's permissions.
      if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
          return;
        }
      } else {
        if ( ! current_user_can( 'edit_post', $post_id ) && !is_admin()) {
          return;
        }
      }

      $exts = array('css','js', 'js_external');

      foreach ($exts as $ext) {
        if (isset( $_POST['tend_ccj_meta_box_nonce_'.$ext])) {
          if (wp_verify_nonce($_POST['tend_ccj_meta_box_nonce_'.$ext], 'tend_ccj_meta_box_'.$ext)) {
            if (isset($_POST['tend_ccj_custom_'.$ext])) {
              $value = $_POST['tend_ccj_custom_'.$ext];
              //$value = str_replace($this->rn, $this->break, $value);
              //$value = sanitize_text_field( $value );
              $value = str_replace($this->break, $this->rn, $value);
              if ($post_id) {
                update_post_meta( $post_id, '_tend_ccj_custom_'.$ext, $value );
              } else {
                update_option('_tend_ccj_custom_'.$ext, $value, false);
              }
            }
          }
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
      global $post;
      // global first
      foreach ($exts as $ext) {
        if ($value = get_option('_tend_ccj_custom_'.$ext, '')) {
          $this->nuno_sarmento_ccj_insert_script($value);
        }
      }
      // then page/post
      if (isset($post)) {
        foreach ($exts as $ext) {
          if ($value = get_post_meta( $post->ID, '_tend_ccj_custom_'.$ext, true )) {
            $this->nuno_sarmento_ccj_insert_script($value);
          }
        }
      }
    }

    function nuno_sarmento_ccj_add_css() {
      global $post;
      // global first
      if ($value = get_option('_tend_ccj_custom_css', '' )) {
        echo "<style>" . $value . "</style>";
      }
      // then page/post
      if (isset($post)) {
        if ($value = get_post_meta( $post->ID, '_tend_ccj_custom_css', true )) {
          echo "<style>" . $value . "</style>";
        }
      }
    }

}

if (!defined('DOING_AJAX') || !DOING_AJAX) {
  $nuno_sarmento_ccj_custom_css_js = new NUNO_SARMENTO_CCJ_Custom_Css_Js();
}
