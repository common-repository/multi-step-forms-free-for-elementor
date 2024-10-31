<?php

namespace ElementorMultistepFree\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use ElementorPro\Core\Utils;
use ElementorPro\Modules\Forms\Classes\Ajax_Handler;
use ElementorPro\Modules\Forms\Classes\Form_Base;
use ElementorPro\Modules\Forms\Classes\Recaptcha_Handler;
use ElementorPro\Modules\Forms\Classes\Recaptcha_V3_Handler;
use ElementorPro\Modules\Forms\Module;
use ElementorPro\Plugin;
#use ElementorPro\Modules\Forms\Widgets;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * @since 1.1.0
 */
class multistep_free extends \ElementorPro\Modules\Forms\Widgets\Form {
  public function __construct($data = [], $args = null) {
    parent::__construct($data, $args);
    wp_register_script( 'elementor-multistep_free', multistep_plugin_url_free . 'assets/js/multistep_free_proper.js', ["elementor-frontend-modules", "elementor-pro-frontend"], '1.1.6', true ); #'elementor-frontend', , "ElementorProFrontendConfig" // BEST "elementor-pro-frontend", "elementor-sticky"
    global $wp;
    $is_edit = \Elementor\Plugin::instance()->preview->is_preview_mode();
    $locale_settings = array(
      'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'elementor-pro-frontend' ),
      "is_admin" => $is_edit,
    );
    wp_localize_script( 'elementor-multistep_free', 'php_vars', $locale_settings );
  }


  /**
   * Retrieve the widget name.
   *
   * @since 1.1.0
   *
   * @access public
   *
   * @return string Widget name.
   */
  public function get_name() {
    return 'multistep-free';
  }

  public function get_script_depends() {
    return [ 'elementor-multistep_free' ];
  }

  /**
   * Retrieve the widget title.
   *
   * @since 1.1.0
   *
   * @access public
   *
   * @return string Widget title.
   */
  public function get_title() {
    return __( 'Multistep Form Basic', 'elementor-multistep' );
  }

  protected function _register_controls() {
    $repeater = new Repeater();
    //require_once( __DIR__ . '/repeater-multistep.php' );
    //$repeater = new \Elementormultistep\Widgets\Control_Repeater_Multistep();

    $field_types = [
      'text' => __( 'Text', 'elementor-pro' ),
      'email' => __( 'Email', 'elementor-pro' ),
      'textarea' => __( 'Textarea', 'elementor-pro' ),
      'url' => __( 'URL', 'elementor-pro' ),
      'tel' => __( 'Tel', 'elementor-pro' ),
      'radio' => __( 'Radio', 'elementor-pro' ),
      'select' => __( 'Select', 'elementor-pro' ),
      'checkbox' => __( 'Checkbox', 'elementor-pro' ),
      'acceptance' => __( 'Acceptance', 'elementor-pro' ),
      'number' => __( 'Number', 'elementor-pro' ),
      'date' => __( 'Date', 'elementor-pro' ),
      'time' => __( 'Time', 'elementor-pro' ),
      'upload' => __( 'File Upload', 'elementor-pro' ),
      'password' => __( 'Password', 'elementor-pro' ),
      'html' => __( 'HTML', 'elementor-pro' ),
      'hidden' => __( 'Hidden', 'elementor-pro' ),
      'page_break' => __( 'Page Break', 'elementor-pro' ),
    ];

    /**
     * Forms field types.
     *
     * Filters the list of field types displayed in the form `field_type` control.
     *
     * @since 1.0.0
     *
     * @param array $field_types Field types.
     */
    $field_types = apply_filters( 'elementor_pro/forms/field_types', $field_types );

    $repeater->start_controls_tabs( 'form_fields_tabs' );

    $repeater->start_controls_tab( 'form_fields_content_tab', [
      'label' => __( 'Content', 'elementor-pro' ),
    ] );

    $repeater->add_control(
      'field_type',
      [
        'label' => __( 'Type', 'elementor-pro' ),
        'type' => Controls_Manager::SELECT,
        'options' => $field_types,
        'default' => 'text',


      ]
    );

    $repeater->add_control(
      'field_label',
      [
        'label' => __( 'Label', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'default' => '',
      ]
    );

    $repeater->add_control(
      'placeholder',
      [
        'label' => __( 'Placeholder', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'default' => '',
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'operator' => 'in',
              'value' => [
                'tel',
                'text',
                'email',
                'textarea',
                'number',
                'url',
                'password',
              ],
            ],
          ],
        ],
      ]
    );

    $repeater->add_control(
      'required',
      [
        'label' => __( 'Required', 'elementor-pro' ),
        'type' => Controls_Manager::SWITCHER,
        'return_value' => 'true',
        'default' => '',
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'operator' => '!in',
              'value' => [
                'recaptcha',
                'recaptcha_v3',
                'hidden',
                'html',
                'page_break',
              ],
            ],
          ],
        ],
      ]
    );

    $repeater->add_control(
      'field_options',
      [
        'label' => __( 'Options', 'elementor-pro' ),
        'type' => Controls_Manager::TEXTAREA,
        'default' => '',
        'description' => __( 'Enter each option in a separate line. To differentiate between label and value, separate them with a pipe char ("|"). For example: First Name|f_name', 'elementor-pro' ),
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'operator' => 'in',
              'value' => [
                'select',
                'checkbox',
                'radio',
              ],
            ],
          ],
        ],
      ]
    );

    $repeater->add_control(
      'allow_multiple',
      [
        'label' => __( 'Multiple Selection', 'elementor-pro' ),
        'type' => Controls_Manager::SWITCHER,
        'return_value' => 'true',
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'value' => 'select',
            ],
          ],
        ],
      ]
    );

    $repeater->add_control(
      'default_placeholder',
      [
        'label' => __( 'Add Placeholder', 'elementor-pro' ),
        'type' => Controls_Manager::SWITCHER,
        'return_value' => 'true',
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'value' => 'select',
            ],
            [
              'name' => 'required',
              'value' => 'true',
            ],
            [
              'name' => 'allow_multiple',
              "operator" => "!==",
              'value' => 'true',
            ],
          ],
        ],
      ]
    );

    $repeater->add_control(
      'placeholder_select',
      [
        'label' => __( 'Custom Placeholder', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'default' => 'Please select an option',
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'value' => 'select',
            ],
            [
              'name' => 'default_placeholder',
              'value' => 'true',
            ],
          ],
        ],
      ]
    );

    $repeater->add_control(
      'select_size',
      [
        'label' => __( 'Rows', 'elementor-pro' ),
        'type' => Controls_Manager::NUMBER,
        'min' => 2,
        'step' => 1,
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'value' => 'select',
            ],
            [
              'name' => 'allow_multiple',
              'value' => 'true',
            ],
          ],
        ],
      ]
    );

    $repeater->add_control(
      'inline_list',
      [
        'label' => __( 'Inline List', 'elementor-pro' ),
        'type' => Controls_Manager::SWITCHER,
        'return_value' => 'elementor-subgroup-inline',
        'default' => '',
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'operator' => 'in',
              'value' => [
                'checkbox',
                'radio',
              ],
            ],
          ],
        ],
      ]
    );

    $repeater->add_control(
      'field_html',
      [
        'label' => __( 'HTML', 'elementor-pro' ),
        'type' => Controls_Manager::TEXTAREA,
        'dynamic' => [
          'active' => true,
        ],
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'value' => 'html',
            ],
          ],
        ],
      ]
    );

    $repeater->add_responsive_control(
      'width',
      [
        'label' => __( 'Column Width', 'elementor-pro' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '' => __( 'Default', 'elementor-pro' ),
          '100' => '100%',
          '80' => '80%',
          '75' => '75%',
          '66' => '66%',
          '60' => '60%',
          '50' => '50%',
          '40' => '40%',
          '33' => '33%',
          '25' => '25%',
          '20' => '20%',
        ],
        'default' => '100',
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'operator' => '!in',
              'value' => [
                'hidden',
                'recaptcha',
                'recaptcha_v3',
                'page_break',
              ],
            ],
          ],
        ],
      ]
    );

    $repeater->add_control(
      'rows',
      [
        'label' => __( 'Rows', 'elementor-pro' ),
        'type' => Controls_Manager::NUMBER,
        'default' => 4,
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'value' => 'textarea',
            ],
          ],
        ],
      ]
    );

    $repeater->add_control(
      'recaptcha_size', [
        'label' => __( 'Size', 'elementor-pro' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'normal',
        'options' => [
          'normal' => __( 'Normal', 'elementor-pro' ),
          'compact' => __( 'Compact', 'elementor-pro' ),
        ],
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'value' => 'recaptcha',
            ],
          ],
        ],
      ]
    );

    $repeater->add_control(
      'recaptcha_style',
      [
        'label' => __( 'Style', 'elementor-pro' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'light',
        'options' => [
          'light' => __( 'Light', 'elementor-pro' ),
          'dark' => __( 'Dark', 'elementor-pro' ),
        ],
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'value' => 'recaptcha',
            ],
          ],
        ],
      ]
    );

    $repeater->add_control(
      'recaptcha_badge', [
        'label' => __( 'Badge', 'elementor-pro' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'bottomright',
        'options' => [
          'bottomright' => __( 'Bottom Right', 'elementor-pro' ),
          'bottomleft' => __( 'Bottom Left', 'elementor-pro' ),
          'inline' => __( 'Inline', 'elementor-pro' ),
        ],
        'description' => __( 'To view the validation badge, switch to preview mode', 'elementor-pro' ),
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'value' => 'recaptcha_v3',
            ],
          ],
        ],
      ]
    );

    $repeater->add_control(
      'css_classes',
      [
        'label' => __( 'CSS Classes', 'elementor-pro' ),
        'type' => Controls_Manager::HIDDEN,
        'default' => '',
        'title' => __( 'Add your custom class WITHOUT the dot. e.g: my-class', 'elementor-pro' ),
      ]
    );

    $repeater->end_controls_tab();

    $repeater->start_controls_tab(
      'form_fields_advanced_tab',
      [
        'label' => __( 'Advanced', 'elementor-pro' ),
        'condition' => [
          'field_type!' => 'html',
        ],
      ]
    );

    $repeater->add_control(
      'field_value',
      [
        'label' => __( 'Default Value', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'default' => '',
        'dynamic' => [
          'active' => true,
        ],
        'conditions' => [
          'terms' => [
            [
              'name' => 'field_type',
              'operator' => 'in',
              'value' => [
                'text',
                'email',
                'textarea',
                'url',
                'tel',
                'radio',
                'select',
                'number',
                'date',
                'time',
                'hidden',
              ],
            ],
          ],
        ],
      ]
    );

    $repeater->add_control(
      'custom_id',
      [
        'label' => __( 'ID', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Please make sure the ID is unique and not used elsewhere in this form. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'elementor-pro' ),
        'render_type' => 'none',
        //"default" => $repeater->get_control_uid(),
      ]
    );
    $shortcode_template = '{{ view.container.settings.get( \'custom_id\' ) }}';
    $repeater->add_control(
      'shortcode',
      [
        'label' => __( 'Shortcode', 'elementor-pro' ),
        'type' => Controls_Manager::RAW_HTML,
        'classes' => 'forms-field-shortcode',
        'raw' => '<input class="elementor-form-field-shortcode" value=\'[field id="' . $shortcode_template . '"]\' readonly />',
      ]
    );

    $repeater->end_controls_tab();

    $repeater->end_controls_tabs();

    $this->start_controls_section(
      'section_form_fields',
      [
        'label' => __( 'Form Fields', 'elementor-pro' ),
      ]
    );

    $this->add_control(
			'important_note',
			[
				'label' => __( '', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( '<h3 style="line-height:16px;">Not finding everything you need? Check out <a href="'."https://teklovers.com".'/membersip" target="_blank" style="color:cornflowerblue;"><b>Multistep Forms PRO</b></a></h3>', 'plugin-name' ),
				'content_classes' => 'your-class',
			]
		);

    $this->add_control(
      'form_name',
      [
        'label' => __( 'Form Name', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'default' => __( 'New Form', 'elementor-pro' ),
        'placeholder' => __( 'Form Name', 'elementor-pro' ),
      ]
    );

    $this->add_control(
      'form_fields',
      [

        'type' => Controls_Manager::REPEATER,//
        'fields' => $repeater->get_controls(),
        'default' => [
          [
            'custom_id' => 'header',
            'field_type' => 'html',
            'field_label' => __( 'Header', 'elementor-pro' ),
            'width' => '100',
            'default' => "<h3>Contact Form</h3>",
          ],
          [
            'custom_id' => 'page_1',
            'field_type' => 'page_break',
            'field_label' => __( 'Page 1', 'elementor-pro' ),
            'width' => '100',
          ],
          [
            'custom_id' => 'name',
            'field_type' => 'text',
            'field_label' => __( 'Name', 'elementor-pro' ),
            'placeholder' => __( 'Name', 'elementor-pro' ),
            'width' => '100',
          ],
          [
            'custom_id' => 'page_2',
            'field_type' => 'page_break',
            'field_label' => __( 'Page 2', 'elementor-pro' ),
            'width' => '100',
          ],
          [
            'custom_id' => 'email',
            'field_type' => 'email',
            'required' => 'true',
            'field_label' => __( 'Email', 'elementor-pro' ),
            'placeholder' => __( 'Email', 'elementor-pro' ),
            'width' => '100',
          ],
          [
            'custom_id' => 'phone',
            'field_type' => 'tel',
            'required' => 'true',
            'field_label' => __( 'Phone', 'elementor-pro' ),
            'placeholder' => __( 'Phone', 'elementor-pro' ),
            'width' => '100',
          ],
          [
            'custom_id' => 'page_3',
            'field_type' => 'page_break',
            'field_label' => __( 'Page 3', 'elementor-pro' ),
            'width' => '100',
          ],
          [
            'custom_id' => 'message',
            'field_type' => 'textarea',
            'field_label' => __( 'Message', 'elementor-pro' ),
            'placeholder' => __( 'Message', 'elementor-pro' ),
            'width' => '100',
          ],
        ],
        'title_field' => '{{{ field_label }}}',
        "event" => "test_event_multi"
      ]
    );

    $this->add_control(
      'input_size',
      [
        'label' => __( 'Input Size', 'elementor-pro' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'xs' => __( 'Extra Small', 'elementor-pro' ),
          'sm' => __( 'Small', 'elementor-pro' ),
          'md' => __( 'Medium', 'elementor-pro' ),
          'lg' => __( 'Large', 'elementor-pro' ),
          'xl' => __( 'Extra Large', 'elementor-pro' ),
        ],
        'default' => 'sm',
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'show_labels',
      [
        'label' => __( 'Label', 'elementor-pro' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', 'elementor-pro' ),
        'label_off' => __( 'Hide', 'elementor-pro' ),
        'return_value' => 'true',
        'default' => 'true',
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'mark_required',
      [
        'label' => __( 'Required Mark', 'elementor-pro' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', 'elementor-pro' ),
        'label_off' => __( 'Hide', 'elementor-pro' ),
        'default' => '',
        'condition' => [
          'show_labels!' => '',
        ],
      ]
    );


    $this->add_control(
      'field_required_msg',
      [
        'label' => __( 'Required', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Display Message for when a field is required', 'elementor-pro' ),
        "default" => "This field is required.",
      ]
    );
    $this->add_control(
      'phone_length_msg',
      [
        'label' => __( 'Phone Length', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Display Message for when a phone number is over the length limit', 'elementor-pro' ),
        "default" => "Max length is 15 digits.",
      ]
    );
    $this->add_control(
      'not_phone_format_msg',
      [
        'label' => __( 'Phone Format', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Display Message for when a field is not in phone format', 'elementor-pro' ),
        "default" => "This field doesn't follow phone format.",
      ]
    );
    $this->add_control(
      'not_email_format_msg',
      [
        'label' => __( 'Email Format', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'description' => __( 'Display Message for when a field is not in email format', 'elementor-pro' ),
        "default" => "This field doesn't follow email format.",
      ]
    );


    $this->add_control(
      'label_position',
      [
        'label' => __( 'Label Position', 'elementor-pro' ),
        'type' => Controls_Manager::HIDDEN,
        'options' => [
          'above' => __( 'Above', 'elementor-pro' ),
          'inline' => __( 'Inline', 'elementor-pro' ),
        ],
        'default' => 'above',
        'condition' => [
          'show_labels!' => '',
        ],
      ]
    );

    $this->end_controls_section();



    $this->start_controls_section(
      'section_integration',
      [
        'label' => __( 'Actions After Submit', 'elementor-pro' ),
      ]
    );

    $actions = Module::instance()->get_form_actions();

    $actions_options = [];

    foreach ( $actions as $action ) {
      $actions_options[ $action->get_name() ] = $action->get_label();
    }

    $this->add_control(
      'submit_actions',
      [
        'label' => __( 'Add Action', 'elementor-pro' ),
        'type' => Controls_Manager::SELECT2,
        'multiple' => true,
        'options' => $actions_options,
        'render_type' => 'none',
        'label_block' => true,
        'default' => [
          'email',
        ],
        'description' => __( 'Add actions that will be performed after a visitor submits the form (e.g. send an email notification). Choosing an action will add its setting below.', 'elementor-pro' ),
      ]
    );

    $this->end_controls_section();

    foreach ( $actions as $action ) {
      $action->register_settings_section( $this );
    }

    $this->start_controls_section(
      'section_form_options',
      [
        'label' => __( 'Additional Options', 'elementor-pro' ),
        'tab' => Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'form_id',
      [
        'label' => __( 'Form ID', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'placeholder' => 'new_form_id',
        "default" => $this->get_id(),
        'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'elementor-pro' ),
        'separator' => 'after',
      ]
    );

    $this->add_control(
      'custom_messages',
      [
        'label' => __( 'Custom Messages', 'elementor-pro' ),
        'type' => Controls_Manager::SWITCHER,
        'default' => '',
        'separator' => 'before',
        'render_type' => 'none',
      ]
    );

    $default_messages = Ajax_Handler::get_default_messages();

    $this->add_control(
      'success_message',
      [
        'label' => __( 'Success Message', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'default' => $default_messages[ Ajax_Handler::SUCCESS ],
        'placeholder' => $default_messages[ Ajax_Handler::SUCCESS ],
        'label_block' => true,
        'condition' => [
          'custom_messages!' => '',
        ],
        'render_type' => 'none',
      ]
    );

    $this->add_control(
      'error_message',
      [
        'label' => __( 'Error Message', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'default' => $default_messages[ Ajax_Handler::ERROR ],
        'placeholder' => $default_messages[ Ajax_Handler::ERROR ],
        'label_block' => true,
        'condition' => [
          'custom_messages!' => '',
        ],
        'render_type' => 'none',
      ]
    );

    $this->add_control(
      'required_field_message',
      [
        'label' => __( 'Required Message', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'default' => $default_messages[ Ajax_Handler::FIELD_REQUIRED ],
        'placeholder' => $default_messages[ Ajax_Handler::FIELD_REQUIRED ],
        'label_block' => true,
        'condition' => [
          'custom_messages!' => '',
        ],
        'render_type' => 'none',
      ]
    );

    $this->add_control(
      'invalid_message',
      [
        'label' => __( 'Invalid Message', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'default' => $default_messages[ Ajax_Handler::INVALID_FORM ],
        'placeholder' => $default_messages[ Ajax_Handler::INVALID_FORM ],
        'label_block' => true,
        'condition' => [
          'custom_messages!' => '',
        ],
        'render_type' => 'none',
      ]
    );

    $this->end_controls_section();



    $this->start_controls_section(
      'section_form_style',
      [
        'label' => __( 'Form', 'elementor-pro' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'column_gap',
      [
        'label' => __( 'Columns Gap', 'elementor-pro' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 10,
        ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 60,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-field-group' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
          '{{WRAPPER}} .elementor-multistep-fields-wrapper' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
        ],
      ]
    );

    $this->add_control(
      'row_gap',
      [
        'label' => __( 'Rows Gap', 'elementor-pro' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 10,
        ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 60,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-field-group' => 'margin-bottom: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .elementor-field-group.recaptcha_v3-bottomleft, {{WRAPPER}} .elementor-field-group.recaptcha_v3-bottomright' => 'margin-bottom: 0;',
          '{{WRAPPER}} .elementor-multistep-fields-wrapper' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'heading_label',
      [
        'label' => __( 'Label', 'elementor-pro' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'label_spacing',
      [
        'label' => __( 'Spacing', 'elementor-pro' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 0,
        ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 60,
          ],
        ],
        'selectors' => [
          'body.rtl {{WRAPPER}} .elementor-labels-inline .elementor-field-group > label' => 'padding-left: {{SIZE}}{{UNIT}};',
          // for the label position = inline option
          'body:not(.rtl) {{WRAPPER}} .elementor-labels-inline .elementor-field-group > label' => 'padding-right: {{SIZE}}{{UNIT}};',
          // for the label position = inline option
          'body {{WRAPPER}} .elementor-labels-above .elementor-field-group > label' => 'padding-bottom: {{SIZE}}{{UNIT}};',
          // for the label position = above option
        ],
      ]
    );

    $this->add_control(
      'label_color',
      [
        'label' => __( 'Text Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-field-group > label, {{WRAPPER}} .elementor-field-subgroup label' => 'color: {{VALUE}};',
        ],
        'scheme' => [
          'type' => Scheme_Color::get_type(),
          'value' => Scheme_Color::COLOR_3,
        ],
      ]
    );

    $this->add_control(
      'mark_required_color',
      [
        'label' => __( 'Mark Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-mark-required .elementor-field-label:after' => 'color: {{COLOR}};',
        ],
        'condition' => [
          'mark_required' => 'yes',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'label_typography',
        'selector' => '{{WRAPPER}} .elementor-field-group > label',
        'scheme' => Scheme_Typography::TYPOGRAPHY_3,
      ]
    );

    $this->add_control(
      'heading_html',
      [
        'label' => __( 'HTML Field', 'elementor-pro' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'html_spacing',
      [
        'label' => __( 'Spacing', 'elementor-pro' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 0,
        ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 60,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-field-type-html' => 'padding-bottom: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'html_color',
      [
        'label' => __( 'Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-field-type-html' => 'color: {{VALUE}};',
        ],
        'scheme' => [
          'type' => Scheme_Color::get_type(),
          'value' => Scheme_Color::COLOR_3,
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'html_typography',
        'selector' => '{{WRAPPER}} .elementor-field-type-html',
        'scheme' => Scheme_Typography::TYPOGRAPHY_3,
      ]
    );





    ////////////////////////// FORWARD
    $this->add_control(
      'multistep_paging_buttons_forward',
      [
        'label' => __( 'Forward Button', 'elementor-pro' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );




    $this->add_control(
      'multistep_paging_buttons_text_forward',
      [
        'label' => __( 'Button Text', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'default' => __( 'Forward', 'elementor-pro' ),
        'placeholder' => __( 'Forward', 'elementor-pro' ),
      ]
    );




    ////////////////////////// BACKWARD
    $this->add_control(
      'multistep_paging_buttons_backwards',
      [
        'label' => __( 'Backwards Button', 'elementor-pro' ),
        'type' => Controls_Manager::HEADING,
        'separator' => 'before',
      ]
    );



    $this->add_control(
      'multistep_paging_buttons_text_backwards',
      [
        'label' => __( 'Button Text', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'default' => __( 'Back', 'elementor-pro' ),
        'placeholder' => __( 'Back', 'elementor-pro' ),
      ]
    );


    $this->end_controls_section();

    $this->start_controls_section(
      'section_field_style',
      [
        'label' => __( 'Field', 'elementor-pro' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'field_text_color',
      [
        'label' => __( 'Text Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-field-group .elementor-field' => 'color: {{VALUE}};',
        ],
        'scheme' => [
          'type' => Scheme_Color::get_type(),
          'value' => Scheme_Color::COLOR_3,
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'field_typography',
        'selector' => '{{WRAPPER}} .elementor-field-group .elementor-field, {{WRAPPER}} .elementor-field-subgroup label',
        'scheme' => Scheme_Typography::TYPOGRAPHY_3,
      ]
    );

    $this->add_control(
      'field_background_color',
      [
        'label' => __( 'Background Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'default' => '#ffffff',
        'selectors' => [
          '{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'background-color: {{VALUE}};',
          '{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'background-color: {{VALUE}};',
        ],
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'field_border_color',
      [
        'label' => __( 'Border Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'border-color: {{VALUE}};',
          '{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'border-color: {{VALUE}};',
          '{{WRAPPER}} .elementor-field-group .elementor-select-wrapper::before' => 'color: {{VALUE}};',
        ],
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'field_border_width',
      [
        'label' => __( 'Border Width', 'elementor-pro' ),
        'type' => Controls_Manager::DIMENSIONS,
        'placeholder' => '1',
        'size_units' => [ 'px' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'field_border_radius',
      [
        'label' => __( 'Border Radius', 'elementor-pro' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
          '{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->end_controls_section();



    $this->start_controls_section(
      'section_button_style',
      [
        'label' => __( 'Submit Button', 'elementor-pro' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'button_text',
      [
        'label' => __( 'Text', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'default' => __( 'Send', 'elementor-pro' ),
        'placeholder' => __( 'Send', 'elementor-pro' ),

      ]
    );

    $this->add_control(
      'button_size',
      [
        'label' => __( 'Size', 'elementor-pro' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'sm',
        'options' => self::get_button_sizes(),
      ]
    );

    $this->add_responsive_control(
      'button_align',
      [
        'label' => __( 'Alignment', 'elementor-pro' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'start' => [
            'title' => __( 'Left', 'elementor-pro' ),
            'icon' => 'eicon-text-align-left',
          ],
          'center' => [
            'title' => __( 'Center', 'elementor-pro' ),
            'icon' => 'eicon-text-align-center',
          ],
          'end' => [
            'title' => __( 'Right', 'elementor-pro' ),
            'icon' => 'eicon-text-align-right',
          ],
          'stretch' => [
            'title' => __( 'Justified', 'elementor-pro' ),
            'icon' => 'eicon-text-align-justify',
          ],
        ],
        'default' => 'stretch',
        'prefix_class' => 'elementor%s-button-align-',
      ]
    );



    $this->add_control(
      'button_icon_align',
      [
        'label' => __( 'Icon Position', 'elementor-pro' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'left',
        'options' => [
          'left' => __( 'Before', 'elementor-pro' ),
          'right' => __( 'After', 'elementor-pro' ),
        ],
        'condition' => [
          'selected_button_icon[value]!' => '',
        ],
      ]
    );

    $this->add_control(
      'button_icon_indent',
      [
        'label' => __( 'Icon Spacing', 'elementor-pro' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'max' => 50,
          ],
        ],
        'condition' => [
          'selected_button_icon[value]!' => '',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
          '{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'button_css_id',
      [
        'label' => __( 'Button ID', 'elementor-pro' ),
        'type' => Controls_Manager::TEXT,
        'default' => '',
        'title' => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'elementor-pro' ),
        'label_block' => false,
        'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'elementor-pro' ),
        'separator' => 'before',

      ]
    );

    $this->start_controls_tabs( 'tabs_button_style' );

    $this->start_controls_tab(
      'tab_button_normal',
      [
        'label' => __( 'Normal', 'elementor-pro' ),
      ]
    );

    $this->add_control(
      'button_background_color',
      [
        'label' => __( 'Background Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'scheme' => [
          'type' => Scheme_Color::get_type(),
          'value' => Scheme_Color::COLOR_4,
        ],
        'default' => '#f2a163',
        'selectors' => [
          '{{wrapper}} .multistep-submit' => 'background-color: {{VALUE}} !important;',
        ],
      ]
    );

    $this->add_control(
      'button_text_color',
      [
        'label' => __( 'Text Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .multistep-submit' => 'color: {{VALUE}} !important;',
          '{{WRAPPER}} .multistep-submit svg' => 'fill: {{VALUE}} !important;',
        ],

      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'button_typography',
        'scheme' => Scheme_Typography::TYPOGRAPHY_4,
        'selector' => '{{WRAPPER}} .multistep-submit',
      ]
    );

    $this->add_group_control(
      Group_Control_Border::get_type(), [
        'name' => 'button_border',
        'selector' => '{{WRAPPER}} .multistep-submit',
      ]
    );

    $this->add_control(
      'button_border_radius',
      [
        'label' => __( 'Border Radius', 'elementor-pro' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .multistep-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
        ],
      ]
    );

    $this->add_control(
      'button_text_padding',
      [
        'label' => __( 'Text Padding', 'elementor-pro' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em', '%' ],
        'selectors' => [
          '{{WRAPPER}} .multistep-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab(
      'tab_button_hover',
      [
        'label' => __( 'Hover', 'elementor-pro' ),
      ]
    );

    $this->add_control(
      'button_background_hover_color',
      [
        'label' => __( 'Background Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .multistep-submit:hover' => 'background-color: {{VALUE}} !important;',
        ],
      ]
    );

    $this->add_control(
      'button_hover_color',
      [
        'label' => __( 'Text Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .multistep-submit:hover' => 'color: {{VALUE}} !important;',
        ],
      ]
    );

    $this->add_control(
      'button_hover_border_color',
      [
        'label' => __( 'Border Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .multistep-submit:hover' => 'border-color: {{VALUE}} !important;',
        ],
        'condition' => [
          'button_border_border!' => '',
        ],
      ]
    );

    $this->add_control(
      'button_hover_animation',
      [
        'label' => __( 'Animation', 'elementor-pro' ),
        'type' => Controls_Manager::HOVER_ANIMATION,
      ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

    $this->end_controls_section();

    $this->start_controls_section(
      'section_messages_style',
      [
        'label' => __( 'Messages', 'elementor-pro' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'message_typography',
        'scheme' => Scheme_Typography::TYPOGRAPHY_3,
        'selector' => '{{WRAPPER}} .elementor-message',
      ]
    );

    $this->add_control(
      'success_message_color',
      [
        'label' => __( 'Success Message Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-message.elementor-message-success' => 'color: {{COLOR}};',
        ],
      ]
    );

    $this->add_control(
      'error_message_color',
      [
        'label' => __( 'Error Message Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-message.elementor-message-danger' => 'color: {{COLOR}};',
        ],
      ]
    );

    $this->add_control(
      'inline_message_color',
      [
        'label' => __( 'Inline Message Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-message.elementor-help-inline' => 'color: {{COLOR}};',
        ],
      ]
    );

    $this->end_controls_section();

  }

  private function render_icon_with_fallback( $settings ) {
    $migrated = isset( $settings['__fa4_migrated']['selected_button_icon'] );
    $is_new = empty( $settings['button_icon'] ) && Icons_Manager::is_migration_allowed();

    if ( $is_new || $migrated ) {
      Icons_Manager::render_icon( $settings['selected_button_icon'], [ 'aria-hidden' => 'true' ] );
    } else {
      ?><i class="<?php echo esc_attr( $settings['button_icon'] ); ?>" aria-hidden="true"></i><?php
    }
  }

  protected function render() {
    $instance = $this->get_settings_for_display();

    if ( ! Plugin::elementor()->editor->is_edit_mode() ) {
      /**
       * Elementor form Pre render.
       *
       * Fires before the from is rendered in the frontend
       *
       * @since 2.4.0
       *
       * @param array $instance current form settings
       * @param Form $this current form widget instance
       */
      do_action( 'elementor-pro/forms/pre_render', $instance, $this );
    }

    $this->add_render_attribute(
      [
        'wrapper' => [
          'class' => [
            'elementor-multistep-fields-wrapper',
            'elementor-labels-' . $instance['label_position'],
          ],
        ],
        'submit-group' => [
          'class' => [
            'elementor-field-group',
            'elementor-column',
            'elementor-field-type-submit',
          ],
        ],
        'button' => [
          'class' => ['elementor-button','multistep-submit'],
        ],
        'icon-align' => [
          'class' => [
            empty( $instance['button_icon_align'] ) ? '' :
              'elementor-align-icon-' . $instance['button_icon_align'],
            'elementor-button-icon',
          ],
        ],
      ]
    );


    #$instance['button_width'] = $instance['multistep_buttons_width']['size'] . $instance['multistep_buttons_width']['unit'];



    if ( ! empty( $instance['multistep_buttons_width'] ) ) {
      $this->add_render_attribute( 'submit-group', 'class', 'elementor-col-' . $instance['multistep_buttons_width']['size'] . $instance['multistep_buttons_width']['unit'] );
    } else {
      $this->add_render_attribute( 'submit-group', 'class', 'elementor-col-' . "45" . "px" );
    }


    if ( ! empty( $instance['button_width_tablet'] ) ) {
      $this->add_render_attribute( 'submit-group', 'class', 'elementor-md-' . $instance['button_width_tablet'] );
    }

    if ( ! empty( $instance['button_width_mobile'] ) ) {
      $this->add_render_attribute( 'submit-group', 'class', 'elementor-sm-' . $instance['button_width_mobile'] );
    }

    if ( ! empty( $instance['button_size'] ) ) {
      $this->add_render_attribute( 'button', 'class', 'elementor-size-' . $instance['button_size'] );
    }

    if ( ! empty( $instance['button_type'] ) ) {
      $this->add_render_attribute( 'button', 'class', 'elementor-button-' . $instance['button_type'] );
    }

    if ( $instance['button_hover_animation'] ) {
      $this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $instance['button_hover_animation'] );
    }

    if ( ! empty( $instance['form_id'] ) ) {
      $this->add_render_attribute( 'form', 'id', $instance['form_id'] );
    }

    if ( ! empty( $instance['form_name'] ) ) {
      $this->add_render_attribute( 'form', 'name', $instance['form_name'] );
    }

    if ( ! empty( $instance['button_css_id'] ) ) {
      $this->add_render_attribute( 'button', 'id', $instance['button_css_id'] );
    }



    if (isset($instance['multistep_buttons_width']) == True) {
      $button_width = $instance['multistep_buttons_width']['size'] . $instance['multistep_buttons_width']['unit'];
    } else {
      $button_width = '105px';
    }


    if (isset($instance['multistep_buttons_height']) == True) {
      $button_height = $instance['multistep_buttons_height']['size'] . $instance['multistep_buttons_height']['unit'];
    } else {
      $button_height = '40px';
    }






    //echo "<!--FIND ME: ".$splaceholder."|"."-->";
    ?>
    <style id="template_style_test">
      .testClass {
        color: red;
      }
    </style>
    <style id="template_style">
      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .multistep-warning {
        margin-bottom: 3px;
        color:red;
        margin-left:10px;
        opacity: 0;
      }

      <?php echo "form[sid='" . $this->get_id() . "']";?> .elementor-field-type-checkbox input[type=checkbox] {
        padding-left: 19px;
      }

      <?php echo "form[sid='" . $this->get_id() . "']";?> .elementor-field-type-checkbox input[type=checkbox]:checked {
        margin-left: -19px;
      }

      <?php echo "form[sid='" . $this->get_id() . "']";?> .elementor-field-type-checkbox input[type=checkbox]:before {
        margin-top: 7px;
        z-index: 100;
        position: relative;
        margin-left: 3px;
      }


      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .page_break_class {
        min-height:200px;
      }
      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .multistep-button-controls-spacer{
        height:60px;
        background-color: none;
      }
      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .multistep-button-controls{
        margin:auto;
        /*width:450px;*/
        /*height: calc(100px + 15px);*/
        /*position: relative;
        bottom: 5px;
        left: 25%;
        right: 25%;*/
      }
      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .multistep-button-controls button {
        width: <?php echo $button_width; ?>;
        /*height:40px;*/
        display:inline-block;
        height: <?php echo $button_height; ?>;
        /*85px*/
      }
      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .multistep-button-controls-forward {
        float:right;
      }
      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .multistep-button-controls-backwards {
        float:left;
      }

      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .multistep-button-controls button[type=submit] {
        float:right;

        width: <?php echo $button_width; ?>;
        max-width: <?php echo $button_width; ?>;
      }

      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .page_break_class .elementor-field-type-submit {
        /*bottom:-60px;*/
      }
      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .invisible {
        display:none !important;
      }
      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .fade-in {
        -webkit-transition: opacity 0.5s ease-in;
        -moz-transition: opacity 0.5s ease-in;
        -o-transition: opacity 0.5s ease-in;
        -ms-transition: opacity 0.5s ease-in;
        transition: opacity 0.5s ease-in;
        opacity: 1;
      }
      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .current-page-tracker-wrapper {
        display:inline-block;
        width:auto;
        width: calc(100% - <?php echo $button_width; ?> -  <?php echo $button_width; ?>);
        align-items: center;
        justify-content: center;
        height:100%;
      }

      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .button-control-placeholder {
        height:40px;
        float:left;
        width:<?php echo $button_width; ?>;
      }


      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .current-page-tracker {
        text-align:center;
        /*margin-top:calc(25% - (33px/4));*/
        height: 33px;
        position: relative;
        /*top: calc(50%);
        transform: translateY(-50%);*/


        top: 0;
        transform: translateY(25%);
      }

      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .circle {
        display:inline-block;
        width: 20px;
        height: 20px;
        background: green;
        -moz-border-radius: 50px;
        -webkit-border-radius: 50px;
        border-radius: 50px;
      }
      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .step {

        height: 20px;
        width: 20px;
        margin: 0 2px;
        margin-top:5px;
        background-color: #bbbbbb;
        border: none;
        border-radius: 50%;
        display: inline-block;
        opacity: 0.5;
        margin-bottom: 7.5px;
      }
      @media (max-width: 800px) {
        <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .step {
          display:inline-block;
          width: 17px;
          height: 17px;
          background: green;
          -moz-border-radius: 50px;
          -webkit-border-radius: 50px;
          border-radius: 50px;
          margin-bottom: 7.5px;
        }
      }
      @media (max-width: 400px) {
        <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .step {
          display:inline-block;
          width: 14px;
          height: 14px;
          background: green;
          -moz-border-radius: 50px;
          -webkit-border-radius: 50px;
          border-radius: 50px;
          margin-bottom: 7.5px;
        }
      }


      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep input[type="checkbox"]:checked::before{
        content: "\f00c";
        font-family: FontAwesome;
      }

      /* Mark the active step: */
      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .step.active {
        margin-top:0px;
        opacity: 1;
        height:25px;
        width:25px;
        -webkit-transition: width 0.5s, height 0.5s, margin-top 0.5s; /* For Safari 3.1 to 6.0 */
        transition: width 0.5s, height 0.5s, margin-top 0.5s;
        margin-bottom: 5px;
      }

      @media (max-width: 800px) {
        <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .step.active {
          margin-top:0px;
          opacity: 1;
          height:22px;
          width:22px;
          -webkit-transition: width 0.5s, height 0.5s, margin-top 0.5s; /* For Safari 3.1 to 6.0 */
          transition: width 0.5s, height 0.5s, margin-top 0.5s;
          margin-bottom: 6px;
        }
      }
      @media (max-width: 400px) {
        <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep .step.active {
          margin-top:0px;
          opacity: 1;
          height:19px;
          width:19px;
          -webkit-transition: width 0.5s, height 0.5s, margin-top 0.5s; /* For Safari 3.1 to 6.0 */
          transition: width 0.5s, height 0.5s, margin-top 0.5s;
          margin-bottom: 6px;
        }
      }


      <?php echo "form[sid='" . $this->get_id() . "']";?>.elementor-multistep div.elementor-field-subgroup {
        padding-left:5px !important;
      }
      .form_messages {
        display: none;
      }
    </style>


    <form class="elementor-multistep elementor_multistep_free" method="post" sid="<?php echo $this->get_id(); ?>" <?php echo $this->get_render_attribute_string( 'form' ); ?>>
      <input type="hidden" name="post_id" value="<?php echo Utils::get_current_post_id(); ?>"/>
			<input type="hidden" name="form_id" value="<?php echo $this->get_id(); ?>"/>
      <div class="form_messages" id="field_required_msg"><?php echo (isset($instance['field_required_msg'])) ? ($instance['field_required_msg'] != "") ? $instance['field_required_msg'] : "This field is required." : "This field is required." ; ?></div>
      <div class="form_messages" id="phone_length_msg"><?php echo (isset($instance['phone_length_msg'])) ? ($instance['phone_length_msg'] != "") ? $instance['phone_length_msg'] : "Max length is 15 digits." : "Max length is 15 digits." ; ?></div>
      <div class="form_messages" id="not_phone_format_msg"><?php echo (isset($instance['not_phone_format_msg'])) ? ($instance['not_phone_format_msg'] != "") ? $instance['not_phone_format_msg'] : "This field doesn't follow phone format." : "This field doesn't follow phone format." ; ?></div>
      <div class="form_messages" id="not_email_format_msg"><?php echo (isset($instance['not_email_format_msg'])) ? ($instance['not_email_format_msg'] != "") ? $instance['not_email_format_msg'] : "This field doesn't follow email format." : "This field doesn't follow email format." ; ?></div>
			<?php if ( is_singular() ) {
				// `queried_id` may be different from `post_id` on Single theme builder templates.
				?>
				<input type="hidden" name="queried_id" value="<?php echo get_the_ID(); ?>"/>
			<?php } ?>


      <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
        <?php
        $page_breaks = array();
        foreach ( $instance['form_fields'] as $item_index => $item ) {
          $item['input_size'] = $instance['input_size'];
          $this->form_fields_render_attributes( $item_index, $instance, $item );


          $field_type = $item['field_type'];

          /**
           * Render form field.
           *
           * Filters the field rendered by Elementor Forms.
           *
           * @since 1.0.0
           *
           * @param array $item       The field value.
           * @param int   $item_index The field index.
           * @param Form  $this       An instance of the form.
           */
          $item = apply_filters( 'elementor_pro/forms/render/item', $item, $item_index, $this );

          /**
           * Render form field.
           *
           * Filters the field rendered by Elementor Forms.
           *
           * The dynamic portion of the hook name, `$field_type`, refers to the field type.
           *
           * @since 1.0.0
           *
           * @param array $item       The field value.
           * @param int   $item_index The field index.
           * @param Form  $this       An instance of the form.
           */
          $item = apply_filters( "elementor_pro/forms/render/item/{$field_type}", $item, $item_index, $this );



          if ( 'hidden' === $item['field_type'] ) {
            $item['field_label'] = false;
          }

          if ( 'page_break' === $item['field_type'] ) {
            array_push($page_breaks, $item);
          } else {
            ?>
          <div <?php echo $this->get_render_attribute_string( 'field-group' . $item_index ); ?>>
            <?php
          }
          if ( $item['field_label'] && 'html' !== $item['field_type'] && 'page_break' !== $item['field_type'] ) {
            echo '<label ' . $this->get_render_attribute_string( 'label' . $item_index ) . '>' . $item['field_label'] . '</label><span class="multistep-warning" id="warning-'.$item['custom_id'].'"></span>';
          }

          switch ( $item['field_type'] ) {
            case 'page_break':

              //echo "<!-- count($page_breaks)" . count($page_breaks) . " -->";
              if (count($page_breaks) > 1) {
                echo '<div class="multistep-button-controls-spacer">
                </div></div>';
              }
              echo "<div class='page_break_class page_break_class_style invisible'>";
            case 'html':
              echo do_shortcode( $item['field_html'] );
              break;
            case 'textarea':
              echo $this->make_textarea_field( $item, $item_index );
              break;

            case 'select':
              $splaceholder = isset($item['placeholder_select']) === true && $item['placeholder_select'] !== null ? $item['placeholder_select'] : "";
              #echo "<!-- FIND ME:".print_r($item,1)."-->";
              $required = ($item['required'] == true) ? " required" : "";
              $multiple = ($item['allow_multiple'] == true) ? " multiple" : "";
              if ($splaceholder !== "") {
                $echo_text = "<select name='form_fields[".$item['custom_id']."][]' id='form-field-".$item['custom_id']."' class='elementor-field-textual elementor-size-sm'".$multiple.$required.">";

                $echo_text .= "<option value=''>".$splaceholder."</option>";
                foreach(explode("\n", $item['field_options']) as $key => $option) {
                  $value = (strpos($option, "|")) ? explode("|", $option)[1] : $option;
                  $pretty_value = (strpos($option, "|")) ? explode("|", $option)[0] : $option;
                  $echo_text .= "<option value='".$value."'>".$pretty_value."</option>";
                }
                echo $echo_text. "</select>";
              } else {
                echo $this->make_select_field( $item, $item_index );
              }

              break;

            case 'radio':
            case 'checkbox':
              echo $this->make_radio_checkbox_field( $item, $item_index, $item['field_type'] );
              break;
            case 'text':
            case 'email':
            case 'url':
            case 'password':
            case 'hidden':
            case 'search':
              $this->add_render_attribute( 'input' . $item_index, 'class', 'elementor-field-textual' );
              echo '<input size="1" ' . $this->get_render_attribute_string( 'input' . $item_index ) . '>';
              break;
            default:
              $field_type = $item['field_type'];

              /**
               * Elementor form field render.
               *
               * Fires when a field is rendered.
               *
               * The dynamic portion of the hook name, `$field_type`, refers to the field type.
               *
               * @since 1.0.0
               *
               * @param array $item       The field value.
               * @param int   $item_index The field index.
               * @param Form  $this       An instance of the form.
               */
              do_action( "elementor_pro/forms/render_field/{$field_type}", $item, $item_index, $this );
          }

          if ( 'page_break' !== $item['field_type'] ) {
            ?>
          </div>
          <?php
          }

          }
          ?>

        </div>







      <div class="multistep-button-controls-wrapper">
        <div class="multistep-button-controls page_break_class_style">
          <!--<div <?php echo $this->get_render_attribute_string( 'submit-group' ); ?>>
          </div>-->
          <button type="submit" <?php echo $this->get_render_attribute_string( 'button' ); ?>>
            <span <?php echo $this->get_render_attribute_string( 'content-wrapper' ); ?>>
              <?php if ( ! empty( $instance['button_icon'] ) || ! empty( $instance['selected_button_icon'] ) ) : ?>
                <span <?php echo $this->get_render_attribute_string( 'icon-align' ); ?>>
                  <?php $this->render_icon_with_fallback( $instance ); ?>
                  <?php if ( empty( $instance['button_text'] ) ) : ?>
                    <span class="elementor-screen-only"><?php _e( 'Submit', 'elementor-pro' ); ?></span>
                  <?php endif; ?>
                </span>
              <?php endif; ?>
              <?php if ( ! empty( $instance['button_text'] ) ) : ?>
                <span class="elementor-button-text"><?php echo $instance['button_text']; ?></span>
              <?php endif; ?>
            </span>
          </button>
          <button type="button" class="multistep-button-controls-forward"><!--  onclick="showTab(1)"-->
            <?php echo isset($instance['multistep_paging_buttons_text_forward']) ? $instance['multistep_paging_buttons_text_forward'] : "Forward" ;?>
          </button>



          <button type="button" class="multistep-button-controls-backwards"><!--  onclick="showTab(-1)"-->
            <?php echo isset($instance['multistep_paging_buttons_text_backwards']) ? $instance['multistep_paging_buttons_text_backwards'] : "Back" ;?>
          </button>
          <div class="button-control-placeholder"></div>


          <div class="current-page-tracker-wrapper">
            <div class="current-page-tracker">


            </div>
          </div>
        </div>
      </div>
    </form>

    <?php

    ?>


    </div>
    <?php
  }

  protected function _content_template() {
    $submit_text = esc_html__( 'Submit', 'elementor-pro' );
    #$settings = $this->get_settings_for_display();
    #$back_text = $settings['multistep_paging_buttons_text_backwards'];
    #$forward_text = $settings['multistep_paging_buttons_text_forward'];

    ?>

    <style id="template_style">

      form[sid="{{settings.form_id}}"] .multistep-warning {
        margin-bottom: 3px;
        color:red;
        margin-left:10px;
        opacity: 0;
      }
      form[sid="{{settings.form_id}}"] .fade-in {
        -webkit-transition: opacity 0.5s ease-in;
        -moz-transition: opacity 0.5s ease-in;
        -o-transition: opacity 0.5s ease-in;
        -ms-transition: opacity 0.5s ease-in;
        transition: opacity 0.5s ease-in;
        opacity: 1;
      }
      form[sid="{{settings.form_id}}"] .multistep-button-controls button[type=submit] {
        float:right;

      }
      form[sid="{{settings.form_id}}"] .button-control-placeholder {
        float:left;
        width:105px/*original*/;
        height:40px;
      }
      form[sid="{{settings.form_id}}"] .page_break_class {
        min-height:200px;
      }
      form[sid="{{settings.form_id}}"] .multistep-button-controls-spacer{
        height:60px;
        background-color: none;
      }
      form[sid="{{settings.form_id}}"] .multistep-button-controls{
        margin:auto;
        width:450px;
        height: calc(100px + 15px);
        /*position: relative;
        bottom: 5px;
        left: 25%;
        right: 25%;*/
      }
      form[sid="{{settings.form_id}}"] .multistep-button-controls button {
        width: 105px;
        height:40px;
        display:inline-block;
        /*85px*/
      }
      form[sid="{{settings.form_id}}"] .multistep-button-controls-forward {
        float:right;
      }
      form[sid="{{settings.form_id}}"] .multistep-button-controls-backwards {
        float:left;
      }
      form[sid="{{settings.form_id}}"] .page_break_class .elementor-field-type-submit {
        /*bottom:-60px;*/
      }
      form[sid="{{settings.form_id}}"] .invisible {
        display:none !important;
      }
      form[sid="{{settings.form_id}}"] .current-page-tracker-wrapper {
        display:inline-block;
        width:auto;
        /*width: calc(100% - 85px - 85px);*/
        width:calc(100% - 45px - 45px);
        align-items: center;
        justify-content: center;
        height:calc(100% - 40px);;
        width: 100%;
      }

      form[sid="{{settings.form_id}}"] .current-page-tracker {
        /*text-align:center;
        margin-top:calc(25% - (33px/4));
        height: 33px;*/
        text-align:center;
        /*margin-top:calc(25% - (33px/4));*/
        height: 33px;
        position: relative;
        top: calc(50%);
        transform: translateY(-50%);
      }




      form[sid="{{settings.form_id}}"] .circle {
        display:inline-block;
        width: 20px;
        height: 20px;
        background: green;
        -moz-border-radius: 50px;
        -webkit-border-radius: 50px;
        border-radius: 50px;
      }
      form[sid="{{settings.form_id}}"] .step {
        height: 20px;
        width: 20px;
        margin: 0 2px;
        margin-top:5px;
        background-color: #bbbbbb;
        border: none;
        border-radius: 50%;
        display: inline-block;
        opacity: 0.5;
        margin-bottom: 7.5px;
      }
      @media (max-width: 800px) {
        form[sid="{{settings.form_id}}"] .step {
          display:inline-block;
          width: 17px;
          height: 17px;
          background: green;
          -moz-border-radius: 50px;
          -webkit-border-radius: 50px;
          border-radius: 50px;
          margin-bottom: 7.5px;
        }
      }
      @media (max-width: 400px) {
        form[sid="{{settings.form_id}}"] .step {
          display:inline-block;
          width: 14px;
          height: 14px;
          background: green;
          -moz-border-radius: 50px;
          -webkit-border-radius: 50px;
          border-radius: 50px;
          margin-bottom: 7.5px;
        }
      }

      /* Mark the active step: */
      form[sid="{{settings.form_id}}"] .step.active {
        margin-top:0px;
        opacity: 1;
        height:25px;
        width:25px;
        -webkit-transition: width 0.5s, height 0.5s, margin-top 0.5s; /* For Safari 3.1 to 6.0 */
        transition: width 0.5s, height 0.5s, margin-top 0.5s;
        margin-bottom: 5px;
      }
      @media (max-width: 800px) {
        form[sid="{{settings.form_id}}"] .step.active {
          margin-top:0px;
          opacity: 1;
          height:22px;
          width:22px;
          -webkit-transition: width 0.5s, height 0.5s, margin-top 0.5s; /* For Safari 3.1 to 6.0 */
          transition: width 0.5s, height 0.5s, margin-top 0.5s;
          margin-bottom: 6px;
        }
      }
      @media (max-width: 400px) {
        form[sid="{{settings.form_id}}"] .step.active {
          margin-top:0px;
          opacity: 1;
          height:19px;
          width:19px;
          -webkit-transition: width 0.5s, height 0.5s, margin-top 0.5s; /* For Safari 3.1 to 6.0 */
          transition: width 0.5s, height 0.5s, margin-top 0.5s;
          margin-bottom: 6px;
        }
      }

      /* Mark the steps that are finished and valid: */
      /*
      {{settings.form_id}} .step.finish {
        background-color: #4CAF50;
      }*/
      .form_messages {
        display:none;
      }
    </style>
    <form class="elementor-multistep elementor_multistep_free" sid="{{settings.form_id}}" name="{{settings.form_name}}">
      <div class="form_messages" id="field_required_msg">"{{settings.field_required_msg}}"</div>
      <div class="form_messages" id="phone_length_msg">"{{settings.phone_length_msg}}"</div>
      <div class="form_messages" id="not_phone_format_msg">"{{settings.not_phone_format_msg}}"</div>
      <div class="form_messages" id="not_email_format_msg">"{{settings.not_email_format_msg}}"</div>
      <div class="elementor-multistep-fields-wrapper elementor-labels-{{settings.label_position}}">
        <#
          


          var page_breaks = []
          for ( var i in settings.form_fields ) {
            var item = settings.form_fields[ i ];
            item = elementor.hooks.applyFilters( 'elementor_pro/forms/content_template/item', item, i, settings );

            var options = item.field_options ? item.field_options.split( '\n' ) : [],
              itemClasses = _.escape( item.css_classes ),
              labelVisibility = '',
              placeholder = '',
              required = '',
              inputField = '',
              multiple = '',
              fieldGroupClasses = 'elementor-field-group elementor-column elementor-field-type-' + item.field_type;

            fieldGroupClasses += ' elementor-col-' + ( ( '' !== item.width ) ? item.width : '100' );

            if ( item.width_tablet ) {
              fieldGroupClasses += ' elementor-md-' + item.width_tablet;
            }

            if ( item.width_mobile ) {
              fieldGroupClasses += ' elementor-sm-' + item.width_mobile;
            }

            if ( ! settings.show_labels ) {
              item.field_label = false;
            }

            if ( item.required ) {
              required = 'required';
              fieldGroupClasses += ' elementor-field-required';

              if ( settings.mark_required ) {
                fieldGroupClasses += ' elementor-mark-required';
              }
            }

            if ( item.placeholder ) {
              placeholder = 'placeholder="' + _.escape( item.placeholder ) + '"';
            }

            if ( item.allow_multiple ) {
              multiple = ' multiple';
              fieldGroupClasses += ' elementor-field-type-' + item.field_type + '-multiple';
            }

            switch ( item.field_type ) {
              case 'page_break':
                item.field_label = false;

                page_breaks.push(item);


                inputField = '';
                console.log("page_breaks.length > 1",page_breaks.length, page_breaks.length > 1)
                if (page_breaks.length > 1) {
                  inputField += '<div class="multistep-button-controls-spacer"></div></div>';
                }
                inputField += '<div class="page_break_class page_break_class_style invisible">';
                break;
              case 'html':
                item.field_label = false;
                inputField = item.field_html;
                break;

              case 'textarea':
                inputField = '<textarea class="elementor-field elementor-field-textual elementor-size-' + settings.input_size + ' ' + itemClasses + '" name="form_field_' + i + '" id="form_field_' + i + '" rows="' + item.rows + '" ' + required + ' ' + placeholder + '>' + item.field_value + '</textarea>';
                break;

              case 'select':
                if ( options ) {
                  var size = '';
                  if ( item.allow_multiple && item.select_size ) {
                    size = ' size="' + item.select_size + '"';
                  }
                  inputField = '<div class="elementor-field elementor-select-wrapper ' + itemClasses + '">';
                  inputField += '<select class="elementor-field-textual elementor-size-' + settings.input_size + '" name="form_field_' + i + '" id="form_field_' + i + '" ' + required + multiple + size + ' >';
                  for ( var x in options ) {
                    var option_value = options[ x ];
                    var option_label = options[ x ];
                    var option_id = 'form_field_option' + i + x;

                    if ( options[ x ].indexOf( '|' ) > -1 ) {
                      var label_value = options[ x ].split( '|' );
                      option_label = label_value[0];
                      option_value = label_value[1];
                    }

                    view.addRenderAttribute( option_id, 'value', option_value );
                    if ( option_value ===  item.field_value ) {
                      view.addRenderAttribute( option_id, 'selected', 'selected' );
                    }
                    inputField += '<option ' + view.getRenderAttributeString( option_id ) + '>' + option_label + '</option>';
                  }
                  inputField += '</select></div>';
                }
                break;

              case 'radio':
              case 'checkbox':
                if ( options ) {
                  var multiple = '';

                  if ( 'checkbox' === item.field_type && options.length > 1 ) {
                    multiple = '[]';
                  }

                  inputField = '<div style="padding-left:5px;" class="elementor-field-subgroup ' + itemClasses + ' ' + item.inline_list + '">';

                  for ( var x in options ) {
                    var option_value = options[ x ];
                    var option_label = options[ x ];
                    var option_id = 'form_field_' + item.field_type + i + x;
                    if ( options[x].indexOf( '|' ) > -1 ) {
                      var label_value = options[x].split( '|' );
                      option_label = label_value[0];
                      option_value = label_value[1];
                    }

                    view.addRenderAttribute( option_id, {
                      value: option_value,
                      type: item.field_type,
                      id: 'form_field_' + i + '-' + x,
                      name: 'form_field_' + i + multiple
                    } );

                    if ( option_value ===  item.field_value ) {
                      view.addRenderAttribute( option_id, 'checked', 'checked' );
                    }

                    inputField += '<span class="elementor-field-option"><input ' + view.getRenderAttributeString( option_id ) + ' ' + required + '> ';
                    inputField += '<label for="form_field_' + i + '-' + x + '">' + option_label + '</label></span>';

                  }

                  inputField += '</div>';
                }
                break;

              case 'text':
              case 'email':
              case 'url':
              case 'password':
              case 'number':
              case 'search':
                itemClasses = 'elementor-field-textual ' + itemClasses;
                inputField = '<input size="1" type="' + item.field_type + '" value="' + item.field_value + '" class="elementor-field elementor-size-' + settings.input_size + ' ' + itemClasses + '" name="form_field_' + i + '" id="form_field_' + i + '" ' + required + ' ' + placeholder + ' >';
                break;
              default:
                inputField = elementor.hooks.applyFilters( 'elementor_pro/forms/content_template/field/' + item.field_type, '', item, i, settings );
            }

            if ( inputField ) {
              if (item.field_type === 'page_break'){
                #>
                {{{ inputField }}}
                <#
              } else {
                #>
                <div class="{{ fieldGroupClasses }}">

                  <# if ( item.field_label ) { #>
                    <label class="elementor-field-label" for="form_field_{{ i }}" {{{ labelVisibility }}}>{{{ item.field_label }}}</label>
                    <span class="multistep-warning" id="warning-{{{ item.custom_id }}}"></span>
                  <# } #>

                  {{{ inputField }}}
                </div>
                <#
              }
            }
          }


          var buttonClasses = 'elementor-field-group elementor-column elementor-field-type-submit';

          buttonClasses += ' elementor-col-' + ( '100' );

          if ( settings.button_width_tablet ) {
            buttonClasses += ' elementor-md-' + settings.button_width_tablet;
          }

          if ( settings.button_width_mobile ) {
            buttonClasses += ' elementor-sm-' + settings.button_width_mobile;
          }

          var iconHTML = elementor.helpers.renderIcon( view, settings.selected_button_icon, { 'aria-hidden': true }, 'i' , 'object' ),
            migrated = elementor.helpers.isIconMigrated( settings, 'selected_button_icon' );

          #>


      </div>

      <div class="multistep-button-controls-wrapper">
        <div class="multistep-button-controls page_break_class_style">
          <button id="{{ settings.button_css_id }}" type="submit" class="elementor-button multistep-submit elementor-size-{{ settings.button_size }} elementor-button-{{ settings.button_type }} elementor-animation-{{ settings.button_hover_animation }}">
            <span>
              <# if ( settings.button_icon || settings.selected_button_icon ) { #>
                <span class="elementor-button-icon elementor-align-icon-{{ settings.button_icon_align }}">
                  <# if ( iconHTML && iconHTML.rendered && ( ! settings.button_icon || migrated ) ) { #>
                    {{{ iconHTML.value }}}
                  <# } else { #>
                    <i class="{{ settings.button_icon }}" aria-hidden="true"></i>
                  <# } #>
                  <span class="elementor-screen-only"><?php echo $submit_text; ?></span>
                </span>
              <# } #>

              <# if ( settings.button_text ) { #>
                <span class="elementor-button-text">{{{ settings.button_text }}}</span>
              <# } #>
            </span>
          </button>


          <button class="multistep-button-controls-forward" onclick="showTab(1)">
            {{{ settings.multistep_paging_buttons_text_forward }}}
          </button>



          <button class="multistep-button-controls-backwards" onclick="showTab(-1)">
            {{{ settings.multistep_paging_buttons_text_backwards }}}
          </button>

          <div class="button-control-placeholder"></div>

          <div class="current-page-tracker-wrapper">
            <div class="current-page-tracker">


            </div>
          </div>
        </div>
      </div>
    </form>




    <?php
  }


}# ElementorProFrontendConfig
