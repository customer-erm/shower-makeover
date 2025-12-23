<?php
/**
 * Plugin Name: Shower Makeover Pro
 * Plugin URI: https://eliteresultsmarketing.com/shower-makeover
 * Description: AI-powered bathroom visualization with professional recommendations. See your dream shower before you buy.
 * Version: 2.0.0
 * Author: Elite Results Marketing
 * Author URI: https://eliteresultsmarketing.com
 * License: GPL v2 or later
 * Text Domain: shower-makeover
 */

if (!defined('ABSPATH')) exit;

define('SM_VERSION', '2.0.0');
define('SM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SM_PLUGIN_URL', plugin_dir_url(__FILE__));

class ShowerMakeoverPro {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_shortcode('shower_makeover', [$this, 'render_shortcode']);
        add_action('wp_ajax_sm_transform_image', [$this, 'handle_transform']);
        add_action('wp_ajax_nopriv_sm_transform_image', [$this, 'handle_transform']);
        add_action('wp_ajax_sm_test_connection', [$this, 'handle_test_connection']);
        register_activation_hook(__FILE__, [$this, 'activate']);
    }
    
    public function add_admin_menu() {
        add_menu_page('Shower Makeover Pro', 'Shower Makeover', 'manage_options', 'shower-makeover', [$this, 'render_admin_page'], 'dashicons-admin-home', 30);
    }
    
    public function register_settings() {
        register_setting('sm_settings', 'sm_settings', ['sanitize_callback' => [$this, 'sanitize_settings']]);
    }
    
    public function sanitize_settings($input) {
        return [
            'gemini_api_key' => sanitize_text_field($input['gemini_api_key'] ?? ''),
            'gemini_model' => sanitize_text_field($input['gemini_model'] ?? 'gemini-2.5-flash-image'),
            'company_name' => sanitize_text_field($input['company_name'] ?? ''),
            'company_phone' => sanitize_text_field($input['company_phone'] ?? ''),
            'cta_url' => esc_url_raw($input['cta_url'] ?? ''),
        ];
    }
    
    public function render_admin_page() {
        $settings = get_option('sm_settings', []);
        ?>
        <div class="wrap">
            <h1>Shower Makeover Pro Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields('sm_settings'); ?>
                <h2>API Configuration</h2>
                <table class="form-table">
                    <tr>
                        <th>Google Gemini API Key</th>
                        <td>
                            <input type="password" name="sm_settings[gemini_api_key]" value="<?php echo esc_attr($settings['gemini_api_key'] ?? ''); ?>" class="regular-text" />
                            <p class="description">Get from <a href="https://aistudio.google.com/apikey" target="_blank">Google AI Studio</a></p>
                        </td>
                    </tr>
                    <tr>
                        <th>Gemini Model</th>
                        <td>
                            <select name="sm_settings[gemini_model]">
                                <option value="gemini-2.5-flash-image" <?php selected($settings['gemini_model'] ?? '', 'gemini-2.5-flash-image'); ?>>Nano Banana (Fast)</option>
                                <option value="gemini-3-pro-image-preview" <?php selected($settings['gemini_model'] ?? '', 'gemini-3-pro-image-preview'); ?>>Nano Banana Pro (Best)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Test Connection</th>
                        <td><button type="button" class="button" id="sm-test-connection">Test API</button> <span id="sm-connection-status"></span></td>
                    </tr>
                </table>
                <h2>Company Info</h2>
                <table class="form-table">
                    <tr><th>Company Name</th><td><input type="text" name="sm_settings[company_name]" value="<?php echo esc_attr($settings['company_name'] ?? ''); ?>" class="regular-text" /></td></tr>
                    <tr><th>Phone Number</th><td><input type="text" name="sm_settings[company_phone]" value="<?php echo esc_attr($settings['company_phone'] ?? ''); ?>" class="regular-text" /></td></tr>
                    <tr><th>CTA Button URL</th><td><input type="url" name="sm_settings[cta_url]" value="<?php echo esc_attr($settings['cta_url'] ?? ''); ?>" class="regular-text" /></td></tr>
                </table>
                <?php submit_button(); ?>
            </form>
            <hr><h2>Usage</h2><code>[shower_makeover]</code>
        </div>
        <script>
        jQuery('#sm-test-connection').on('click', function() {
            var $btn = jQuery(this), $status = jQuery('#sm-connection-status');
            $btn.prop('disabled', true).text('Testing...');
            jQuery.post(ajaxurl, {action: 'sm_test_connection', nonce: '<?php echo wp_create_nonce('sm_admin_nonce'); ?>'}, function(r) {
                $btn.prop('disabled', false).text('Test API');
                $status.html(r.success ? '<span style="color:green">✓ Connected</span>' : '<span style="color:red">✗ ' + r.data.message + '</span>');
            });
        });
        </script>
        <?php
    }
    
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'shower-makeover') !== false) {
            wp_enqueue_style('sm-admin', SM_PLUGIN_URL . 'assets/css/admin.css', [], SM_VERSION);
        }
    }
    
    public function enqueue_frontend_assets() {
        if (!is_admin()) {
            wp_enqueue_style('sm-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap', [], null);
            wp_enqueue_style('sm-styles', SM_PLUGIN_URL . 'assets/css/shower-makeover.css', [], SM_VERSION);
            wp_enqueue_script('sm-script', SM_PLUGIN_URL . 'assets/js/shower-makeover.js', ['jquery'], SM_VERSION, true);
            
            $settings = get_option('sm_settings', []);
            wp_localize_script('sm-script', 'smConfig', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('sm_nonce'),
                'companyName' => $settings['company_name'] ?? '',
                'companyPhone' => $settings['company_phone'] ?? '',
                'ctaUrl' => $settings['cta_url'] ?? '',
            ]);
        }
    }
    
    public function render_shortcode($atts) {
        ob_start();
        include SM_PLUGIN_DIR . 'templates/designer-form.php';
        return ob_get_clean();
    }
    
    // Data Methods
    public function get_layouts() {
        return [
            ['id' => 'inline', 'name' => 'Single Door', 'desc' => 'Classic alcove with hinged door', 'icon' => 'door', 'best_for' => 'alcove', 'price' => 0],
            ['id' => 'doorPanel', 'name' => 'Door + Panel', 'desc' => 'Wide opening, modern look', 'icon' => 'door-panel', 'best_for' => 'wide', 'price' => 200],
            ['id' => 'corner', 'name' => '90° Corner', 'desc' => 'L-shaped corner enclosure', 'icon' => 'corner', 'best_for' => 'corner', 'price' => 350],
            ['id' => 'neoAngle', 'name' => 'Neo Angle', 'desc' => 'Diamond corner design', 'icon' => 'neo', 'best_for' => 'corner-small', 'price' => 450],
            ['id' => 'walkIn', 'name' => 'Walk-In', 'desc' => 'Doorless open entry', 'icon' => 'walkin', 'best_for' => 'large', 'price' => 300],
            ['id' => 'slider', 'name' => 'Sliding Door', 'desc' => 'Space-saving barn style', 'icon' => 'slider', 'best_for' => 'tub', 'price' => 250],
        ];
    }
    
    public function get_glass_types() {
        return [
            ['id' => 'clear', 'name' => 'Clear', 'desc' => 'Crystal clarity', 'price' => 0],
            ['id' => 'starphire', 'name' => 'Starphire', 'desc' => 'Ultra-clear premium', 'price' => 150],
            ['id' => 'frosted', 'name' => 'Frosted', 'desc' => 'Maximum privacy', 'price' => 100],
            ['id' => 'rain', 'name' => 'Rain Glass', 'desc' => 'Textured elegance', 'price' => 125],
            ['id' => 'gray', 'name' => 'Gray Tint', 'desc' => 'Modern sophistication', 'price' => 75],
        ];
    }
    
    public function get_hardware_finishes() {
        return [
            ['id' => 'matteBlack', 'name' => 'Matte Black', 'color' => '#1a1a1a', 'price' => 0],
            ['id' => 'brushedNickel', 'name' => 'Brushed Nickel', 'color' => '#b5b5a8', 'price' => 0],
            ['id' => 'polishedChrome', 'name' => 'Polished Chrome', 'color' => '#e8e8e8', 'price' => 0],
            ['id' => 'brushedGold', 'name' => 'Brushed Gold', 'color' => '#d4af37', 'price' => 100],
            ['id' => 'satinBrass', 'name' => 'Satin Brass', 'color' => '#dbb968', 'price' => 100],
            ['id' => 'oilRubbedBronze', 'name' => 'Oil Rubbed Bronze', 'color' => '#4a3428', 'price' => 75],
        ];
    }
    
    public function get_handle_styles() {
        return [
            ['id' => 'ladder', 'name' => 'Ladder Pull', 'price' => 0],
            ['id' => 'squareLadder', 'name' => 'Square Ladder', 'price' => 25],
            ['id' => 'cPull', 'name' => 'C-Pull', 'price' => 35],
            ['id' => 'towelBar', 'name' => 'Towel Bar', 'price' => 50],
        ];
    }
    
    public function get_upgrades() {
        return [
            ['id' => 'armorCoating', 'name' => 'ShieldGuard™ Coating', 'desc' => 'Lifetime easy-clean protection'],
            ['id' => 'steamShower', 'name' => 'Steam Ready', 'desc' => 'Fully sealed enclosure'],
            ['id' => 'gridPattern', 'name' => 'Grid Pattern', 'desc' => 'Black or brass grid lines on glass'],
        ];
    }
    
    // AJAX Handlers
    public function handle_test_connection() {
        check_ajax_referer('sm_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'Unauthorized']); return; }
        
        $settings = get_option('sm_settings', []);
        $api_key = $settings['gemini_api_key'] ?? '';
        if (empty($api_key)) { wp_send_json_error(['message' => 'No API key']); return; }
        
        $response = wp_remote_post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-image:generateContent', [
            'timeout' => 30,
            'headers' => ['Content-Type' => 'application/json', 'x-goog-api-key' => $api_key],
            'body' => json_encode(['contents' => [['parts' => [['text' => 'Say OK']]]]])
        ]);
        
        if (is_wp_error($response)) { wp_send_json_error(['message' => $response->get_error_message()]); return; }
        wp_remote_retrieve_response_code($response) === 200 ? wp_send_json_success() : wp_send_json_error(['message' => 'API Error']);
    }
    
    public function handle_transform() {
        check_ajax_referer('sm_nonce', 'nonce');
        
        if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(['message' => 'Please upload a valid image']);
            return;
        }
        
        $file = $_FILES['image'];
        if (!in_array($file['type'], ['image/jpeg', 'image/png', 'image/webp'])) {
            wp_send_json_error(['message' => 'Please upload JPEG, PNG, or WebP']);
            return;
        }
        
        // Get all form data
        $base_type = sanitize_text_field($_POST['base_type'] ?? 'tub');
        $layout = sanitize_text_field($_POST['layout'] ?? 'corner');
        $glass = sanitize_text_field($_POST['glass'] ?? 'clear');
        $hardware = sanitize_text_field($_POST['hardware'] ?? 'matteBlack');
        $handle = sanitize_text_field($_POST['handle'] ?? 'ladder');
        $upgrades = isset($_POST['upgrades']) ? array_map('sanitize_text_field', $_POST['upgrades']) : [];
        $img_width = intval($_POST['image_width'] ?? 0);
        $img_height = intval($_POST['image_height'] ?? 0);
        
        // Get human-readable names
        $base_type_name = $base_type === 'tub' ? 'Glass on Tub' : 'Glass to Floor';
        $layout_data = $this->find_option($this->get_layouts(), $layout);
        $glass_data = $this->find_option($this->get_glass_types(), $glass);
        $hardware_data = $this->find_option($this->get_hardware_finishes(), $hardware);
        $handle_data = $this->find_option($this->get_handle_styles(), $handle);
        
        // Build upgrade descriptions
        $upgrade_names = [];
        foreach ($upgrades as $u) {
            $ud = $this->find_option($this->get_upgrades(), $u);
            if ($ud) $upgrade_names[] = $ud['name'];
        }
        
        // Calculate aspect ratio
        $aspect_ratio = $this->calculate_aspect_ratio($img_width, $img_height);
        
        // Build the prompt
        $prompt = $this->build_transformation_prompt(
            $base_type_name,
            $layout_data['name'] ?? $layout,
            $glass_data['name'] ?? $glass,
            $hardware_data['name'] ?? $hardware,
            $handle_data['name'] ?? $handle,
            $upgrade_names,
            $layout_data['best_for'] ?? '',
            $img_width,
            $img_height
        );
        
        // Prepare image
        $image_data = file_get_contents($file['tmp_name']);
        $base64_image = base64_encode($image_data);
        
        // Get settings
        $settings = get_option('sm_settings', []);
        $api_key = $settings['gemini_api_key'] ?? '';
        $model = $settings['gemini_model'] ?? 'gemini-2.5-flash-image';
        
        if (empty($api_key)) {
            wp_send_json_error(['message' => 'API not configured']);
            return;
        }
        
        // Call Gemini API
        $request_body = [
            'contents' => [[
                'parts' => [
                    ['text' => $prompt],
                    ['inline_data' => ['mime_type' => $file['type'], 'data' => $base64_image]]
                ]
            ]],
            'generationConfig' => ['responseModalities' => ['TEXT', 'IMAGE']]
        ];
        
        if ($aspect_ratio) {
            $request_body['generationConfig']['imageConfig'] = ['aspectRatio' => $aspect_ratio];
        }
        
        $response = wp_remote_post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent",
            [
                'timeout' => 120,
                'headers' => ['Content-Type' => 'application/json', 'x-goog-api-key' => $api_key],
                'body' => json_encode($request_body)
            ]
        );
        
        if (is_wp_error($response)) {
            wp_send_json_error(['message' => $response->get_error_message()]);
            return;
        }
        
        $code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if ($code !== 200) {
            wp_send_json_error(['message' => $body['error']['message'] ?? 'API Error']);
            return;
        }
        
        // Extract response
        $generated_image = null;
        $ai_response = '';
        
        if (isset($body['candidates'][0]['content']['parts'])) {
            foreach ($body['candidates'][0]['content']['parts'] as $part) {
                if (isset($part['inlineData'])) $generated_image = $part['inlineData']['data'];
                if (isset($part['text'])) $ai_response = $part['text'];
            }
        }
        
        if (!$generated_image) {
            wp_send_json_error(['message' => 'No image generated. Try a different photo.']);
            return;
        }
        
        wp_send_json_success([
            'image' => 'data:image/png;base64,' . $generated_image,
            'aiRecommendation' => $ai_response,
            'selections' => [
                'baseType' => $base_type_name,
                'layout' => $layout_data['name'] ?? $layout,
                'glass' => $glass_data['name'] ?? $glass,
                'hardware' => $hardware_data['name'] ?? $hardware,
                'handle' => $handle_data['name'] ?? $handle,
                'upgrades' => $upgrade_names
            ]
        ]);
    }
    
    private function find_option($options, $id) {
        foreach ($options as $opt) {
            if ($opt['id'] === $id) return $opt;
        }
        return null;
    }
    
    private function calculate_aspect_ratio($w, $h) {
        if ($w <= 0 || $h <= 0) return null;
        $r = $w / $h;
        if ($r >= 1.7) return '16:9';
        if ($r >= 1.4) return '3:2';
        if ($r >= 1.2) return '4:3';
        if ($r >= 0.9) return '1:1';
        if ($r >= 0.7) return '3:4';
        return '9:16';
    }
    
    private function build_transformation_prompt($base_type, $layout, $glass, $hardware, $handle, $upgrades, $best_for, $w, $h) {
        $upgrade_text = !empty($upgrades) ? "\n- Premium Features: " . implode(', ', $upgrades) : '';
        $dim_text = ($w > 0 && $h > 0) ? "Original image: {$w}x{$h}px. Match this resolution exactly." : '';
        
        $base_instruction = $base_type === 'Glass on Tub' 
            ? "KEEP THE EXISTING TUB - the glass enclosure sits ON TOP of the bathtub. Do NOT remove the tub."
            : "REMOVE THE TUB - replace with a tile shower floor/pan. Glass goes all the way to the floor.";
        
        return "You are a friendly, expert shower glass consultant helping a homeowner visualize their bathroom upgrade. Analyze this photo and create a photorealistic visualization with their selected frameless glass shower.

{$dim_text}

INSTALLATION TYPE: {$base_type}
{$base_instruction}

MANDATORY: YOUR TEXT RESPONSE MUST BEGIN WITH DIMENSIONS

You MUST estimate the shower opening dimensions by analyzing reference objects visible in the photo. Use these known standard sizes:

REFERENCE OBJECTS FOR MEASUREMENT:
- Standard toilet: 15\" wide, 28-30\" deep, 15\" seat height
- Standard bathtub: 60\" long, 30-32\" wide, 14-16\" tall
- Bathroom door: 28-32\" wide, 80\" tall
- Light switch/outlet: 4.5\" tall
- Standard floor tiles: 12\"x12\" or 12\"x24\" (count them!)
- Wall tiles: Often 3\"x6\", 4\"x4\", or 6\"x6\"
- Shower valve/faucet: Typically mounted 48\" from floor
- Showerhead: Typically mounted 72-80\" from floor
- Vanity counter height: 32-36\" from floor
- Towel bar: Usually 48\" from floor

REQUIRED FORMAT - Start your response with this EXACT format:
---
DIMENSIONS: [WIDTH]\" W x [DEPTH]\" D x [HEIGHT]\" H

Example: DIMENSIONS: 36\" W x 32\" D x 84\" H
---

If this is a tub enclosure or alcove with no separate depth, use:
DIMENSIONS: [WIDTH]\" W x [HEIGHT]\" H

AFTER THE DIMENSIONS, provide:

1. **What You're Working With** (2-3 sentences):
   Describe the current space - alcove, corner, or open? What's there now?

2. **Your Design Recommendation**:
   The customer selected \"{$layout}\" - confirm it works OR suggest a better fit.

3. **Installation Notes** (1-2 sentences):
   Mention where the door will swing/slide and any relevant details.

VISUALIZATION SPECS:
- Base: {$base_type}
- Layout: {$layout} frameless glass enclosure
- Glass: {$glass} (3/8\" tempered)
- Hardware: {$hardware} finish
- Handle: {$handle} style{$upgrade_text}

CRITICAL INSTALLATION ACCURACY:
You must render the shower with CORRECT hardware placement for a real installation:

HINGE PLACEMENT:
- Door hinges mount on the wall side OR on a fixed panel, NEVER floating
- For wall-mount: 2-3 hinges directly into wall studs/blocking
- For glass-to-glass: hinges connect door to fixed panel
- Hinges should be {$hardware} finish, visible and substantial

CLAMP/BRACKET PLACEMENT:
- U-channel or wall clamps at top and bottom of fixed panels
- Wall clamps anchor into studs, not just drywall
- Support bars/headers for panels over 36\" wide
- All clamps/brackets in matching {$hardware} finish

DOOR PLACEMENT LOGIC:
- Door should swing OUTWARD (into bathroom, not into shower)
- Door should be positioned for easy entry based on bathroom layout
- Consider toilet, vanity, and traffic flow when placing door
- Hinges on the side closest to the nearest wall for stability

GLASS PANEL RULES:
- Fixed panels attach to walls with U-channel or clamps
- 90° corners need a corner clamp or post
- Notches around tub ledges must be shown if keeping tub
- Panels must show proper glass thickness (3/8\")

VISUALIZATION REQUIREMENTS:
- Show realistic {$hardware} hardware with proper metallic reflections
- Glass should show subtle reflections of the bathroom
- Match the exact lighting and perspective of the original photo
- Keep ALL other bathroom elements unchanged
- Result must look like a professional installation photo

Generate the visualization image after your consultation text.";
    }
    
    public function activate() {
        add_option('sm_settings', [
            'gemini_api_key' => '',
            'gemini_model' => 'gemini-2.5-flash-image',
            'company_name' => get_bloginfo('name'),
            'company_phone' => '',
            'cta_url' => '',
        ]);
    }
}

function sm_init() { return ShowerMakeoverPro::get_instance(); }
add_action('plugins_loaded', 'sm_init');
