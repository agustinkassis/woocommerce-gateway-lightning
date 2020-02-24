<?

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once(WC_LND_PLUGIN_PATH . '/admin/includes/LND_Settings_Page_Generator.php');

if (!class_exists('LND_WC_Settings_LND')) {

class LND_WC_Settings_LND extends LND_Settings_Page_Generator {
    public static $prefix = WC_LND_NAME . '_lnd_config';
    protected static $instance = null;

    public function __construct() {
        $this->title = __('LND Settings', 'lnd-woocommerce');

        self::set_structure();
        parent::__construct();

        $this->lndCon = LndWrapper::instance();

        if (!empty($_FILES[static::$prefix]['name']['tls'])) {
          $this->upload_tls();
        }

        if (!empty($_FILES[static::$prefix]['name']['macaroon'])) {
          $this->upload_macaroon();
        }
    }


    /**
     * Get settings structure
     *
     * @access public
     * @return array
     */
    public static function set_structure() {
        // Define main settings
        self::$structure = array(
            'settings' => array(
                'title' => __('Config', 'lnd-woocommerce'),
                'children' => array(
                    'general_settings' => array(
                        'title' => __('Server Config', 'lnd-woocommerce'),
                        'children' => [
                            'host' => array(
                                'title'     => __('Host', 'lnd-woocommerce'),
                                'type'      => 'text',
                                'default'   => __('localhost', 'lnd-woocommerce'),
                                'required'  => true,
                                'hint'      => __('LND host address, you can use <b>localhost</b>.', 'lnd-woocommerce'),
                            ),
                            'port' => array(
                                'title'     => __('Port', 'lnd-woocommerce'),
                                'type'      => 'text',
                                'default'   => 8080,
                                'required'  => true,
                                'hint'      => __('LND port, must be the same as <b>restlisten</b> value from lnd.conf. Please type just the port number.', 'lnd-woocommerce'),
                            ),
                            'ssl' => array(
                                'title'     => __('SSL Enabled', 'lnd-woocommerce'),
                                'type'      => 'checkbox',
                                'default'   => '1',
                                'hint'      => __('You need to upload tls.cert below.', 'lnd-woocommerce'),
                            ),
                        ],
                    ),
                    'credentials' => array(
                        'title' => __('Credentials', 'lnd-woocommerce'),
                        'children' => array(
                          'tls' => array(
                              'title'     => __('TLS File', 'lnd-woocommerce'),
                              'type'      => 'file',
                              'required'  => false,
                              'hint'      => __('tls.cert file generated by LND.', 'lnd-woocommerce'),
                          ),
                          'macaroon' => array(
                              'title'     => __('Macaroon File', 'lnd-woocommerce'),
                              'type'      => 'file',
                              'required'  => false,
                              'hint'      => __( 'Macaroon file, must have invoice permissions at least', WC_LND_NAME ),
                          ),
                        ),
                    ),
                ),
            ),
            'info' => array(
                'title' => __('Server Info', 'lnd-woocommerce'),
                'template' => 'info',
                'children' => [
                  'general_settings' => [
                      'title' => __('General', 'lnd-woocommerce'),
                      'children' => [
                          'empty' => array(
                              'title'     => __('Test', 'lnd-woocommerce'),
                              'type'      => 'template',
                              'view'      => 'footer',
                          ),
                      ],
                  ],
                ],
            ),
            'debug' => array(
                'title' => __('Debug', 'lnd-woocommerce'),
                'template' => 'debug',
                'children' => [
                  'general_settings' => [
                      'title' => __('General', 'lnd-woocommerce'),
                      'children' => [
                          'empty' => array(
                              'title'     => __('Test', 'lnd-woocommerce'),
                              'type'      => 'template',
                              'view'      => 'footer',
                          ),
                      ],
                  ],
                ],
            ),
        );
        return self::$structure;
    }

    /**
     * Upload Macaroon file
     *
     * @access public
     * @return void
     */
    public function upload_macaroon() {
        $this->upload_file('macaroon', WC_LND_MACAROON_FILE, ['macaroon']);
    }

    /**
     * Upload TLS file
     *
     * @access public
     * @return void
     */
    public function upload_tls() {
        $this->upload_file('tls', WC_LND_TLS_FILE, ['cert']);
    }

    public function print_template_debug() {
      // Print settings page content
      include WC_LND_ADMIN_PATH . '/views/lnd/debug.php';
    }

    public function print_template_info() {
      try {
        $info = $this->lndCon->getInfo();
      } catch (\Exception $e) {
        $message = $e->getMessage();
        // Print settings error content
        include WC_LND_ADMIN_PATH . '/views/error.php';
        return;
      }

      include WC_LND_ADMIN_PATH . '/views/lnd/info.php';
    }

}
LND_WC_Settings_LND::instance();
}
