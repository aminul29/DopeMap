<?php
/**
 * Custom nested popup rows control for Elementor.
 *
 * @package DopeMap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DopeMap_Control_Popup_Rows extends \Elementor\Base_Data_Control {
	/**
	 * Control type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'dopemap_nested_repeater';
	}

	/**
	 * Default value.
	 *
	 * @return string
	 */
	public function get_default_value() {
		return '[]';
	}

	/**
	 * Default settings.
	 *
	 * @return array
	 */
	protected function get_default_settings() {
		return array(
			'label_block' => true,
		);
	}

	/**
	 * Enqueue editor assets.
	 *
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_style( 'dopemap-editor-control-style' );
		wp_enqueue_script( 'dopemap-editor-control-script' );
	}

	/**
	 * Render control template in the editor.
	 *
	 * @return void
	 */
	public function content_template() {
		?>
		<div class="elementor-control-field">
			<# if ( data.label ) { #>
				<label class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>
			<div class="elementor-control-input-wrapper">
				<div class="dopemap-nested-repeater">
					<input type="hidden" class="dopemap-nested-repeater__input" data-setting="{{ data.name }}" />
					<div class="dopemap-nested-repeater__rows"></div>
					<button type="button" class="elementor-button elementor-button-default dopemap-nested-repeater__add">
						<?php echo esc_html__( 'Add Popup Row', 'dope-map' ); ?>
					</button>
				</div>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}
