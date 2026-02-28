<?php
/**
 * World Map widget for Elementor.
 *
 * @package DopeMap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DopeMap_World_Map_Widget extends \Elementor\Widget_Base {
	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'dope_world_map';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Dope World Map', 'dope-map' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-google-maps';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'dope-category' );
	}

	/**
	 * Get style dependencies.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'dopemap-style' );
	}

	/**
	 * Get script dependencies.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'dopemap-script' );
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_map_style',
			array(
				'label' => esc_html__( 'Map Style', 'dope-map' ),
			)
		);

		$this->add_responsive_control(
			'map_height',
			array(
				'label'      => esc_html__( 'Map Height', 'dope-map' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 300,
						'max' => 900,
					),
					'vh' => array(
						'min' => 40,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 520,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dope-map-canvas' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'map_border_radius',
			array(
				'label'      => esc_html__( 'Map Border Radius', 'dope-map' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
					'%'  => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 18,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dope-map-widget' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'background_color',
			array(
				'label'   => esc_html__( 'Ocean Color', 'dope-map' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#0653A6',
			)
		);

		$this->add_control(
			'region_default_color',
			array(
				'label'   => esc_html__( 'Land Color', 'dope-map' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#040608',
			)
		);

		$this->add_control(
			'region_hover_color',
			array(
				'label'   => esc_html__( 'Land Hover Color', 'dope-map' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#13263A',
			)
		);

		$this->add_control(
			'marker_color',
			array(
				'label'   => esc_html__( 'Marker Color', 'dope-map' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#FFFFFF',
			)
		);

		$this->add_control(
			'marker_hover_color',
			array(
				'label'   => esc_html__( 'Marker Hover Color', 'dope-map' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '#B3D4FF',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_locations',
			array(
				'label' => esc_html__( 'Locations', 'dope-map' ),
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'country_code',
			array(
				'label'       => esc_html__( 'Country Code (ISO-2)', 'dope-map' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'US',
				'description' => esc_html__( 'Use ISO-2 country code, e.g. US, BD, DE.', 'dope-map' ),
			)
		);

		$repeater->add_control(
			'marker_label',
			array(
				'label'       => esc_html__( 'Marker Label', 'dope-map' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'New York', 'dope-map' ),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Popup Title', 'dope-map' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Office Title', 'dope-map' ),
			)
		);

		$repeater->add_control(
			'subtitle',
			array(
				'label'       => esc_html__( 'Popup Subtitle', 'dope-map' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Short description for this location.', 'dope-map' ),
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label'   => esc_html__( 'Popup Image', 'dope-map' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array(
					'url' => '',
				),
			)
		);

		$repeater->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button Text', 'dope-map' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Learn More', 'dope-map' ),
			)
		);

		$repeater->add_control(
			'button_url',
			array(
				'label'       => esc_html__( 'Button URL', 'dope-map' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => 'https://example.com',
			)
		);

		$repeater->add_control(
			'custom_lat',
			array(
				'label'       => esc_html__( 'Custom Latitude (Optional)', 'dope-map' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'step'        => 0.0001,
				'placeholder' => '40.7128',
			)
		);

		$repeater->add_control(
			'custom_lng',
			array(
				'label'       => esc_html__( 'Custom Longitude (Optional)', 'dope-map' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'step'        => 0.0001,
				'placeholder' => '-74.0060',
			)
		);

		$this->add_control(
			'locations',
			array(
				'label'       => esc_html__( 'Map Locations', 'dope-map' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ marker_label || country_code }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$map_id   = 'dope-map-' . $this->get_id();

		$styles = array(
			'backgroundColor'  => sanitize_hex_color( $settings['background_color'] ),
			'regionDefault'    => sanitize_hex_color( $settings['region_default_color'] ),
			'regionHover'      => sanitize_hex_color( $settings['region_hover_color'] ),
			'markerColor'      => sanitize_hex_color( $settings['marker_color'] ),
			'markerHoverColor' => sanitize_hex_color( $settings['marker_hover_color'] ),
		);

		$markers = array();

		if ( ! empty( $settings['locations'] ) && is_array( $settings['locations'] ) ) {
			foreach ( $settings['locations'] as $location ) {
				$country_code = isset( $location['country_code'] ) ? strtoupper( sanitize_text_field( $location['country_code'] ) ) : '';
				$country_code = preg_replace( '/[^A-Z]/', '', $country_code );
				$has_country   = strlen( $country_code ) === 2;

				$marker_label = isset( $location['marker_label'] ) ? sanitize_text_field( $location['marker_label'] ) : '';
				$title        = isset( $location['title'] ) ? sanitize_text_field( $location['title'] ) : '';
				$subtitle     = isset( $location['subtitle'] ) ? sanitize_textarea_field( $location['subtitle'] ) : '';
				$button_text  = isset( $location['button_text'] ) ? sanitize_text_field( $location['button_text'] ) : '';

				$image_url = '';
				if ( ! empty( $location['image']['url'] ) ) {
					$image_url = esc_url_raw( $location['image']['url'] );
				}

				$button_url  = '';
				$is_external = false;
				if ( ! empty( $location['button_url']['url'] ) ) {
					$button_url  = esc_url_raw( $location['button_url']['url'] );
					$is_external = ! empty( $location['button_url']['is_external'] );
				}

				$has_custom_coords = isset( $location['custom_lat'] ) && isset( $location['custom_lng'] ) && is_numeric( $location['custom_lat'] ) && is_numeric( $location['custom_lng'] );
				$lat_lng           = null;

				if ( $has_custom_coords ) {
					$lat_lng = array(
						(float) $location['custom_lat'],
						(float) $location['custom_lng'],
					);
				}

				// At least one locator is required: valid country code or custom coordinates.
				if ( ! $has_country && ! $has_custom_coords ) {
					continue;
				}

				$markers[] = array(
					'countryCode' => $country_code,
					'name'        => $marker_label ? $marker_label : ( $has_country ? $country_code : esc_html__( 'Location', 'dope-map' ) ),
					'latLng'      => $lat_lng,
					'title'       => $title,
					'subtitle'    => $subtitle,
					'imageUrl'    => $image_url,
					'buttonText'  => $button_text,
					'buttonUrl'   => $button_url,
					'isExternal'  => $is_external,
				);
			}
		}

		$map_config = array(
			'styles'  => $styles,
			'markers' => $markers,
		);
		?>
		<div class="dope-map-widget" id="<?php echo esc_attr( $map_id ); ?>" data-map-config="<?php echo esc_attr( wp_json_encode( $map_config ) ); ?>">
			<div class="dope-map-canvas" aria-label="<?php echo esc_attr__( 'Interactive world map', 'dope-map' ); ?>"></div>
			<div class="dope-map-popup" hidden>
				<button type="button" class="dope-map-popup__close" aria-label="<?php echo esc_attr__( 'Close location popup', 'dope-map' ); ?>">&times;</button>
				<div class="dope-map-popup__image-wrap">
					<img class="dope-map-popup__image" src="" alt="" loading="lazy" />
				</div>
				<div class="dope-map-popup__content">
					<h4 class="dope-map-popup__title"></h4>
					<p class="dope-map-popup__subtitle"></p>
					<a class="dope-map-popup__button" href="#"></a>
				</div>
			</div>
		</div>
		<?php
	}
}
