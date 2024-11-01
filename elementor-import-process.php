<?php
namespace Elementor\TemplateLibrary;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Library Unlimited
 *
 * Uses built-in Elementor commands to create a new source for template library.
 *
 * @since 1.0.0
 */
if ( did_action( 'elementor/loaded' ) ) {
	class Source_Web_Disrupt_Funnelmentals extends Source_Base {

		public function __construct() {
			parent::__construct();
			add_action( 'wp_ajax_get_content_from_elementor_export_file', array($this, 'get_finalized_data'));
		}

		public function get_id() {}
		public function get_title() {}
		public function register_data(){ }
		public function get_items( $args = [] ){}
		public function get_item( $template_id ){}
		public function get_data( array $args ){}
		public function delete_template( $template_id ){}
		public function save_item( $template_data ){}
		public function update_item( $new_data ){}
		public function export_template( $template_id ){}

		public function get_finalized_data() {

			$data = json_decode( wp_remote_retrieve_body( wp_remote_get( "https://s3.us-east-2.amazonaws.com/web-disrupt-unlimited-library/Templates/". $_POST['filename'] )), true );
			$content = $data['content'];
			$content = $this->process_export_import_content( $content, 'on_import' );
			$content = $this->replace_elements_ids( $content );
			echo json_encode($content);
			wp_die();
				
		}
	}
	new Source_Web_Disrupt_Funnelmentals();
}