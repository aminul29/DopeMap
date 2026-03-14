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
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
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

		$this->add_control(
			'marker_icon_size',
			array(
				'label'      => esc_html__( 'Marker Icon Size', 'dope-map' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 96,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_popup_box_style',
			array(
				'label' => esc_html__( 'Popup Box', 'dope-map' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'popup_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#0F1621',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'popup_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ECF4FF',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-text: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'popup_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#2B3950',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'popup_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dope-map' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
					'%'  => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 12,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'popup_content_padding',
			array(
				'label'      => esc_html__( 'Content Padding', 'dope-map' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 14,
					'right'    => 14,
					'bottom'   => 16,
					'left'     => 14,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .dope-map-popup__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'popup_close_button_heading',
			array(
				'label'     => esc_html__( 'Close Button', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'popup_close_color',
			array(
				'label'     => esc_html__( 'Close Icon Color', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-close-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'popup_close_background',
			array(
				'label'     => esc_html__( 'Close Background', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.40)',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-close-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'popup_close_focus_color',
			array(
				'label'     => esc_html__( 'Close Focus Outline', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#9CC8FF',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-close-focus: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_popup_title_style',
			array(
				'label' => esc_html__( 'Popup Title', 'dope-map' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'popup_title_color',
			array(
				'label'     => esc_html__( 'Color', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#F6F9FF',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-title-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'popup_title_typography',
				'selector' => '{{WRAPPER}} .dope-map-popup__title',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_popup_subtitle_style',
			array(
				'label' => esc_html__( 'Popup Subtitle', 'dope-map' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'popup_subtitle_color',
			array(
				'label'     => esc_html__( 'Color', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#C9D8EB',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-subtitle-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'popup_subtitle_typography',
				'selector' => '{{WRAPPER}} .dope-map-popup__subtitle',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_popup_link_style',
			array(
				'label' => esc_html__( 'Popup Link', 'dope-map' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'popup_link_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#EEF5FF',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-link-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'popup_link_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1A3659',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-link-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'popup_link_hover_text_color',
			array(
				'label'     => esc_html__( 'Hover Text Color', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-link-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'popup_link_hover_background_color',
			array(
				'label'     => esc_html__( 'Hover Background Color', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#274A75',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-link-hover-bg: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'popup_link_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dope-map' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
					'%'  => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 8,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-link-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'popup_link_typography',
				'selector' => '{{WRAPPER}} .dope-map-popup__button',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_popup_image_style',
			array(
				'label' => esc_html__( 'Popup Image', 'dope-map' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'popup_image_max_height',
			array(
				'label'      => esc_html__( 'Max Height', 'dope-map' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 60,
						'max' => 360,
					),
					'%'  => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 140,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-image-max-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'popup_image_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'dope-map' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
					'%'  => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-popup-image-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_bottom_links_style',
			array(
				'label' => esc_html__( 'Bottom Left Links', 'dope-map' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bottom_links_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-info-links-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bottom_links_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#B3D4FF',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-info-links-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bottom_links_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-info-links-bg: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'bottom_links_border',
				'selector' => '{{WRAPPER}} .dope-map-info-table',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'bottom_links_typography',
				'selector' => '{{WRAPPER}} .dope-map-info-table__trigger',
			)
		);

		$this->add_responsive_control(
			'bottom_links_gap',
			array(
				'label'      => esc_html__( 'Link Gap', 'dope-map' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 48,
					),
				),
				'default'    => array(
					'size' => 12,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dope-map-info-table__links' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bottom_links_padding',
			array(
				'label'      => esc_html__( 'Padding', 'dope-map' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'      => 14,
					'right'    => 16,
					'bottom'   => 14,
					'left'     => 16,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .dope-map-info-table__links' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bottom_links_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'dope-map' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'default'   => 'flex-start',
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'dope-map' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'dope-map' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'End', 'dope-map' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .dope-map-info-table__links' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'bottom_links_left',
			array(
				'label'      => esc_html__( 'Left', 'dope-map' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 240,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-info-links-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bottom_links_right',
			array(
				'label'      => esc_html__( 'Right', 'dope-map' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 240,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-info-links-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bottom_links_top',
			array(
				'label'      => esc_html__( 'Top', 'dope-map' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 240,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-info-links-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'bottom_links_bottom',
			array(
				'label'      => esc_html__( 'Bottom', 'dope-map' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 240,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .dope-map-widget' => '--dope-info-links-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_info_table',
			array(
				'label' => esc_html__( 'Bottom Left Links', 'dope-map' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$info_table_repeater = new \Elementor\Repeater();

		$info_table_repeater->add_control(
			'row_title',
			array(
				'label'       => esc_html__( 'Title', 'dope-map' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Resources', 'dope-map' ),
				'label_block' => true,
			)
		);

		$info_table_repeater->add_control(
			'popup_rows',
			array(
				'label'       => esc_html__( 'Popup Links', 'dope-map' ),
				'type'        => 'dopemap_nested_repeater',
				'description' => esc_html__( 'Each popup row adds one link to the popup list.', 'dope-map' ),
			)
		);

		$this->add_control(
			'info_table_rows',
			array(
				'label'       => esc_html__( 'Bottom Left Links', 'dope-map' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $info_table_repeater->get_controls(),
				'title_field' => '{{{ row_title || "Link Trigger" }}}',
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
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'default'     => '',
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
	 * Normalize popup rows from the custom nested repeater control.
	 *
	 * @param mixed $value Raw control value.
	 * @return array
	 */
	protected function get_popup_rows_value( $value ) {
		if ( is_string( $value ) ) {
			$raw_value = wp_unslash( $value );
			$decoded   = json_decode( $raw_value, true );

			if ( ! is_array( $decoded ) ) {
				$decoded = json_decode( html_entity_decode( $raw_value, ENT_QUOTES, 'UTF-8' ), true );
			}

			if ( ! is_array( $decoded ) ) {
				$maybe_value = maybe_unserialize( $raw_value );
				$decoded     = is_array( $maybe_value ) ? $maybe_value : array();
			}

			$value = $decoded;
		}

		if ( ! is_array( $value ) ) {
			return array();
		}

		$popup_rows = array();

		foreach ( $value as $row ) {
			if ( is_object( $row ) ) {
				$row = (array) $row;
			}

			if ( is_string( $row ) ) {
				$decoded_row = json_decode( html_entity_decode( wp_unslash( $row ), ENT_QUOTES, 'UTF-8' ), true );
				$row         = is_array( $decoded_row ) ? $decoded_row : array();
			}

			if ( ! is_array( $row ) ) {
				continue;
			}

			$title = isset( $row['popup_title'] ) ? sanitize_text_field( $row['popup_title'] ) : '';
			$link  = isset( $row['popup_link'] ) ? $row['popup_link'] : array();

			if ( is_object( $link ) ) {
				$link = (array) $link;
			}

			if ( is_string( $link ) ) {
				$decoded_link = json_decode( html_entity_decode( wp_unslash( $link ), ENT_QUOTES, 'UTF-8' ), true );
				$link         = is_array( $decoded_link ) ? $decoded_link : array();
			}

			if ( ! is_array( $link ) ) {
				$link = array();
			}

			$url = '';

			if ( ! empty( $link['url'] ) ) {
				$url = esc_url_raw( $link['url'] );
			} elseif ( ! empty( $row['popup_link_url'] ) ) {
				$url = esc_url_raw( $row['popup_link_url'] );
			}

			$is_external = ! empty( $link['is_external'] ) || ! empty( $row['popup_link_external'] );
			$nofollow    = ! empty( $link['nofollow'] ) || ! empty( $row['popup_link_nofollow'] );

			if ( '' === $title && '' === $url ) {
				continue;
			}

			$popup_rows[] = array(
				'title'      => $title,
				'linkUrl'    => $url,
				'isExternal' => $is_external,
				'nofollow'   => $nofollow,
			);
		}

		return $popup_rows;
	}

	/**
	 * Prepare bottom-left link triggers for the frontend.
	 *
	 * @param mixed $rows Raw info table rows.
	 * @return array
	 */
	protected function get_info_table_rows_value( $rows ) {
		if ( ! is_array( $rows ) ) {
			return array();
		}

		$prepared_rows = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}

			$title      = isset( $row['row_title'] ) ? sanitize_text_field( $row['row_title'] ) : '';
			$popup_rows = $this->get_popup_rows_value( $row['popup_rows'] ?? array() );

			if ( '' === $title ) {
				continue;
			}

			$prepared_rows[] = array(
				'title'     => $title,
				'popupRows' => $popup_rows,
			);
		}

		return $prepared_rows;
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
			'markerIconSize'   => isset( $settings['marker_icon_size']['size'] ) ? max( 12, min( 96, (int) $settings['marker_icon_size']['size'] ) ) : 16,
		);

		$markers = array();
		$info_table_rows = $this->get_info_table_rows_value( $settings['info_table_rows'] ?? array() );

		if ( ! empty( $settings['locations'] ) && is_array( $settings['locations'] ) ) {
			foreach ( $settings['locations'] as $location ) {
				$country_code = isset( $location['country_code'] ) ? strtoupper( sanitize_text_field( $location['country_code'] ) ) : '';
				$country_code = preg_replace( '/[^A-Z]/', '', $country_code );
				$has_country   = strlen( $country_code ) === 2;

				$marker_label = isset( $location['marker_label'] ) ? sanitize_text_field( $location['marker_label'] ) : '';
				$title        = isset( $location['title'] ) ? sanitize_text_field( $location['title'] ) : '';
				$subtitle     = isset( $location['subtitle'] ) ? wp_kses_post( $location['subtitle'] ) : '';
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
			'styles'    => $styles,
			'markers'   => $markers,
			'infoTable' => $info_table_rows,
		);
		?>
		<div class="dope-map-widget" id="<?php echo esc_attr( $map_id ); ?>" data-map-config="<?php echo esc_attr( wp_json_encode( $map_config ) ); ?>">
			<div class="dope-map-stage">
				<div class="dope-map-canvas" aria-label="<?php echo esc_attr__( 'Interactive world map', 'dope-map' ); ?>"></div>
				<div class="dope-map-popup-backdrop" hidden></div>
				<div class="dope-map-popup" hidden>
					<button type="button" class="dope-map-popup__close" aria-label="<?php echo esc_attr__( 'Close map popup', 'dope-map' ); ?>">&times;</button>
					<div class="dope-map-popup__image-wrap">
						<img class="dope-map-popup__image" src="" alt="" loading="lazy" />
					</div>
					<div class="dope-map-popup__content">
						<h4 class="dope-map-popup__title"></h4>
						<div class="dope-map-popup__subtitle"></div>
						<div class="dope-map-popup__link-list-wrap">
							<div class="dope-map-popup__link-list"></div>
						</div>
						<div class="dope-map-popup__empty"><?php echo esc_html__( 'No links available.', 'dope-map' ); ?></div>
						<a class="dope-map-popup__button" href="#"></a>
					</div>
				</div>
				<?php if ( ! empty( $info_table_rows ) ) : ?>
					<div class="dope-map-info-table" aria-label="<?php echo esc_attr__( 'Map links', 'dope-map' ); ?>">
						<div class="dope-map-info-table__links">
							<?php foreach ( $info_table_rows as $index => $row ) : ?>
								<a class="dope-map-info-table__trigger" href="#" data-info-index="<?php echo esc_attr( $index ); ?>">
									<?php echo esc_html( $row['title'] ); ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
