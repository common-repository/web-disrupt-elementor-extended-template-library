<?php
/**
 * Plugin Name: Elementor Library Unlimited
 * Description: Save more time with this extended library of templates. Users can submit ones they have made to grow the library. Lets make elementor better together.
 * Plugin URI: https://www.webdisrupt.com/elementor-extended-library/
 * Version: 1.0.4
 * Author: Web Disrupt
 * Author URI: https://webdisrupt.com
 * Text Domain: elementor-library-unlimited
 * License: GNU General Public License v3.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists('WD_Elementor_Library_Unlimited')) {

class WD_Elementor_Library_Unlimited {

    
    /**
	 * Creates a single Instance of self
	 *
	 * @var Static data - Define menu main menu name
	 * @since 1.0.0
	 */
	private static $_instance = null;


    /**
	 * Settings plugin details
	 *
	 * @var Static data - Define all important magic strings
	 * @since 1.0.0
	 */
	static $plugin_data = null;
	
	/**
	 * Define All Actions
	 *
	 * @var Static data - Define all actions
	 * @since 1.0.0
	 */
	 static $element_pro_actions = null;


	/**
	 * Creates and returns the main object for this plugin
	 *
	 *
	 * @since  1.0.0
	 * @return Web_Disrupt_Funnelmentals
	 */
	static public function init() {
        
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

    }

    /**
	 * Main Constructor that sets up all static data associated with this plugin.
	 *
	 *
	 * @since  1.0.0
	 *
	 */
	private function __construct() {


        // Setup static plugin_data
		self::$plugin_data = array(
            "name"            => "Web Disrupt Elementor",
            "slug"            => "web-disrupt-elementor-library-unlimited",
            "version"         => "1.0.4",
            "author"          => "Web Disrupt",
            "description"     => "Save more time with this extended library of templates. Users can submit ones they have made to grow the library. Lets make elementor better together.",
            "logo"            => plugins_url( 'assets/images/logo.png', __FILE__ ),
			"style"           => plugins_url( 'templates/style.css', __FILE__  ),
			"images"          => plugins_url( 'assets/image/', __FILE__ ),
            "url-author"      => "https://www.webdisrupt.com/",
            "this-root"       => plugins_url( '', __FILE__ )."/",
			"this-dir"        => plugin_dir_path( __FILE__ ),
			"this-file"       =>  __FILE__,
			"settings-id"     		  => "wd-elementor-library-unlimited-settings-data",
			"plugin-menu"	  		  => "elementor",
			"config-library"	      => plugin_dir_path( __FILE__ )."config/library.json",
			"config-contributors"	  => plugin_dir_path( __FILE__ )."config/contributors.json",
			"temp-library"	          => plugin_dir_path( __FILE__ )."temp/",
			"temp-library-link"	          => plugin_dir_url( __FILE__ )."temp/"
			);

		add_action( 'elementor/editor/before_enqueue_scripts', array($this,'enqueue_elementor_editor_add_scripts'));
		add_action('wp_ajax_get_main_view_from_elementor_ultimate_template_library', array($this, 'main_view_template'));
		add_action('wp_ajax_get_ELU_thumbnails', array($this, 'ajax_get_ELU_thumbnails'));
		add_action('wp_ajax_get_ELU_preview', array($this, 'ajax_get_ELU_preview'));
		add_action('wp_ajax_check_if_required_plugin_is_installed', array($this, 'check_if_required_plugin_is_installed'));
		/* Set to 0.0.0 and call update on first use */
		if(get_option('ELU__current_version') == false){
			update_option('ELU__current_version', '0.0.0');
		}

	} //ctor

    /**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'elementor-pro' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'elementor-pro' ), '1.0.0' );
	}


	/**
	 * Add styles and scripts for Elementor Library Unlimited
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_elementor_editor_add_scripts(){

		wp_register_style( 'web-disrupt-library-unlimited-templates-css',  plugins_url( '/assets/css/style.css', self::$plugin_data['this-file'] ) );
		wp_enqueue_style( 'web-disrupt-library-unlimited-templates-css');
		wp_register_script( 'web-disrupt-library-unlimited-templates-js',  plugins_url( '/assets/js/elementor-manage-library.js', self::$plugin_data['this-file'] ) );
		wp_enqueue_script( 'web-disrupt-library-unlimited-templates-js');

	}

	/**
	 * Required Plugins for a template
	 *
	 * @return String with what is required to use this template
	 */
	public function check_if_required_plugin_is_installed(){
		$true_count = 0;
		$print_string = "";
		if(strpos(sanitize_text_field($_POST['required']), 'funnelmentals') !== false) {
			if(!file_exists(WP_PLUGIN_DIR."/web-disrupt-funnelmentals") && !file_exists(WP_PLUGIN_DIR."/web-disrupt-funnelmentals-premium")){ $print_string = "funnelmentals"; wp_die(); }
		}
		if (strpos(sanitize_text_field($_POST['required']), 'pro') !== false){
			if(!file_exists(WP_PLUGIN_DIR."/elementor-pro")){ $print_string = "pro"; wp_die(); }
		}
		if (strpos(sanitize_text_field($_POST['required']), 'funnelmentals-pro') !== false){
			if(!file_exists(WP_PLUGIN_DIR."/web-disrupt-funnelmentals-premium")){ $print_string = "funnelmentals-pro"; wp_die(); }
		} 
		if($print_string == ''){
			echo "true"; wp_die();	
		} else {
			echo $print_string;
		}
	}

	/**
	 * Main Template view
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function main_view_template(){
		$this->update_if_needed();
		$this->print_header();
		?>
		<div class="ELU__main-view"> 
			<?php $this->get_ELU_thumbnails(" ", 0); ?>
		</div>
		<?php
		wp_die();
	}

	/**
	 * AJAX Wrapper for ELU thumbnails
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_get_ELU_thumbnails(){
		$this->get_ELU_thumbnails(sanitize_text_field($_POST['data']['search']), sanitize_text_field($_POST['data']['page']));
		wp_die();
	}

	/**
	 * Print out Thumbnails for library
	 *
	 * @since 1.0.0
	 * @param [string] $search Fitler the library based on this string
	 * @param [number] $page is the current page
	 * @param [number] $paging is a max number per page
	 * @return void
	 */
	private function get_ELU_thumbnails($search, $page, $paging = 12){
		if(strlen($search) < 1){
			$search = " ";
		}
		$search = strtolower($search);
		/* Step 1 Filter Search with Step 2 Pagging */
		$library_data = json_decode(file_get_contents(( self::$plugin_data['config-library'] )), true);
		$searched_list = [];
		/* First filter list based ons earch */
		for ($i=0; $i < count($library_data); $i++) { 
			$this_search = strtolower($library_data[$i]['name'] . " " . $library_data[$i]['type']  . " " .  $library_data[$i]['series']  . " " .  $library_data[$i]['keywords']);
			if (strpos($this_search, $search) !== false) { // If contains search string
				array_push($searched_list, $library_data[$i]);
			}
		}
		$template_list = [];
		$current_page_start =  $page * $paging;
		$current_page_end =  (($page+1) * $paging)-1;
		$current_count = 0;
		/* Add paging to filtered list */
		for ($i=0; $i < count($searched_list); $i++) { 
			if($current_count >= $current_page_start && $current_count <= $current_page_end){ // If inside pagging zone
				array_push($template_list, $searched_list[$i]);
				$current_count++;
			} else { // Count but don't add it to the list
				$current_count++;
			}
		}
		$page_count = ceil($current_count/$paging);

		echo "<script> var ELU_Index = []; </script>";
		?> <div class="ELU__main-tiled-view"> <?php
		if(count($template_list) != 0){
			for($i=0; $i < count($template_list); $i++) { 
				$slug = strtolower(str_replace(" ", "-", $template_list[$i]['name']));
				?>
				<div class='ELU__item'>
					<div class="ELU__title"> 
					<?php 
						echo $template_list[$i]['name']; 
					?>
					</div>
					<?php 
					// Collect all thumbnails that exist
					$thumbList = [];
					$versionList = [];
					if(isset($template_list[$i]['steps'][0])){ // Add variation
						for ($t=0; $t < count($template_list[$i]['steps']); $t++) { 
							array_push($thumbList, $this->get_and_cache_image("Thumbnails", "png", $slug."-".strtolower(str_replace(" ", "-", $template_list[$i]['steps'][$t]))));
							array_push($versionList, $template_list[$i]['steps'][$t]);
						}						
					} else if(isset($template_list[$i]['variations'][0])){ // Add variation
						for ($t=0; $t < count($template_list[$i]['variations']); $t++) { 
							array_push($thumbList, $this->get_and_cache_image("Thumbnails", "png", $slug."-".strtolower(str_replace(" ", "-", $template_list[$i]['variations'][$t]))));
							array_push($versionList, $template_list[$i]['variations'][$t]);
						}						
					} else {
						array_push($thumbList, $this->get_and_cache_image("Thumbnails", "png", $slug));
					}
					for ($t=0; $t < count($thumbList); $t++) { 
						?><div class="ELU__thumb ELU__index-<?php echo $i;?> ELU__index-<?php echo $i; if(isset($versionList[$t])){ echo "-".strtolower(str_replace(" ", "-", $versionList[$t])); } ?><?php if($t != 0){ echo " ELU_thumb_hidden"; } ?>" data-index="<?php echo $i; ?>" style='background-image:url(<?php echo $thumbList[$t];?>);'></div><?php
					}
					echo "<script> ELU_Index[".$i."] = ".json_encode($template_list[$i])."; </script>";
					if($template_list[$i]['type'] == 'funnel'){
						$variation_data = [ "uindex" => $i,  "name" => "Funnel Step", "list" => $template_list[$i]['steps'] ];
						$this->template_multi_version($variation_data);
					} else {
						$variation_data = [ "uindex" => $i,  "name" => "Variation", "list" => $template_list[$i]['variations'] ];
						$this->template_multi_version($variation_data);
					} ?>
					<div class="ELU__dates"><div><?php echo "<b>Created</b>: ".$template_list[$i]['created']; ?></div><div><?php echo "<b>Updated</b>: ".$template_list[$i]['updated']; ?></div></div>
					<div class="ELU__action-bar">
						<div class="ELU__grow"> </div>
						<div data-version="ELU__version-<?php echo $i;?>" data-requirements='<?php for($r=0; $r < count($template_list[$i]['requires']);$r++){ echo $template_list[$i]['requires'][$r]."|*|"; } ?>' data-template-name='<?php echo $slug; ?>' class='ELU__btn-template-insert'> Insert Template </div>
					</div>
				</div>
			<?php
			}  /* Thumbnail Loop */
		} else {
			echo "<div style='padding:40px;font-size:20px;'> <i class='fa fa-frown-o'></i> No Results found!</div>";
		}
		?></div>
		
		<div class="ELU__pagging"> 
		<?php
		for ($i=0; $i < $page_count; $i++) { 
			if($i == $page){
				echo "<div class='ELU__page-current'>".($i+1)."</div>";
			} else {
				echo "<div class='ELU__page-goto'>".($i+1)."</div>";
			}
		}
		?>	
		</div>
		<input class="ELU__search-saved" type="hidden" value="<?php if(strlen($search) > 1){ echo $search; } else { echo " "; } ?>" />
		<div class="ELU__pagging_bottom_made_with_love">
			Templates made with <i class="fa fa-heart"></i>
	    </div>
		<?php
	}

	/**
	 * AJAX Wrapper for ELU preview
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_get_ELU_preview(){
		$this->print_preview_window($_POST['data']);
		wp_die();
	}

	

	/**
	 * Print the preview window and make callable through ajax
	 *
	 * @return void
	 */
	private function print_preview_window($data){
		
		$contributors = json_decode(file_get_contents( self::$plugin_data['config-contributors']), true);
		?>
		<div class="ELU__preview-window">
		<div class="ELU__preview-pane">
			<div class="ELU__inner-size">		
			<?php 
			if(isset($data['steps'][0])){
				$loop = $data['steps'];
			} else if(isset($data['variations'][0])){
				$loop = $data['variations'];
			}
			if(isset($loop)){
				for ($i=0; $i < count($loop); $i++) { 
					$slug = strtolower(str_replace(" ", "-", sanitize_text_field($data['name'])."-".sanitize_text_field($loop[$i])));
					echo "<div class='ELU__preview-caption'> <i class='fa fa-cube' style='color:#999'></i>".sanitize_text_field($data['name'])." <i class='fa fa-chevron-right'></i> ".sanitize_text_field($loop[$i])." Preview </div>";
					echo "<img src='".$this->get_and_cache_image("Previews", "jpg", $slug)."' />";
				}
			} else {
				$slug = strtolower(str_replace(" ", "-", sanitize_text_field($data['name'])));
				echo "<div class='ELU__preview-caption'> ".sanitize_text_field($data['name'])." Preview </div>";
				echo "<img src='".$this->get_and_cache_image("Previews", "jpg", $slug)."' />";
			}
			?>
		</div>
		</div>
		<div class="ELU__info-bar">
			<div class="ELU__preview-close"> <i class="fa fa-times"></i> </div>
			<div class="ELU__ib-header">
			Details
			<div class="ELU__line_divider"></div>
			</div>
			
				<div class="ELU__ib-details">
				<div>name: <span><?php echo sanitize_text_field($data['name']); ?></span></div>
				<div>type: <span><?php echo sanitize_text_field($data['type']); ?></span></div>
				<div>series: <span><?php echo sanitize_text_field($data['series']); ?></span></div>
				<div>created: <span><?php echo sanitize_text_field($data['created']); ?></span></div>
				<div>last updated: <span><?php echo sanitize_text_field($data['updated']); ?></span></div>
				<div>contributors: <span>
					<?php for ($i=0; $i < count($data['contributor']); $i++) { 
						echo "<a href='".$contributors[sanitize_text_field($data['contributor'][$i])]."' target='contributor_".sanitize_text_field($data['contributor'][$i])."'>".sanitize_text_field($data['contributor'][$i])."</a>";
						if(($i+1) != count($data['contributor'])) { echo ", "; }
					}  ?>
			</span></div>
				<div>requiremnets: <span>
					<?php for ($i=0; $i < count($data['requires']); $i++) { 
						echo sanitize_text_field($data['requires'][$i]);
						if(($i+1) != count($data['requires'])) { echo ", "; }
					}  ?>
			</span></div>
			<div>keywords: <span> <?php echo sanitize_text_field($data['keywords']); ?></span></div>
			</div>
		</div>
		<div>
		<?php
	}

	/**
	 * Print Header On EUL Tab
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function print_header(){
		?>
		<div style='display:flex;margin-top: -15px;padding: 0px 10px;'>
			<div style="flex-grow: 1;"> <h3 style='text-align:left;'>Elementor Library Unlimited</h3> </div>
			<div style='text-align:right;'> Library Version <?php echo get_option("ELU__current_version"); ?> </div>
		</div>	
		<div style='display:flex;justify-content:center;padding:10px;'>
		<div class='ELU__search-input'><input class="ELU__search-value" type="text" /></div>
		<div class='ELU__search'><i class="fa fa-search"></i> Search </div>
		<div class='ELU__more-power'><i class="fa fa-fire"></i> Unlock More Power Now!</div>
		</div>
		<div class='ELU__more-power-details' style="display:none;">
			<div class="ELU__more-power-header">
				<div class='ELU__more-power-title'> Take your system to the next Level! </div>  
				<div class='ELU__more-power-close'><i class="fa fa-remove"></i></div>
			</div>
			<div class="ELU__more-power-list">
				<div class="ELU__mp-item"> 
					<h4>Funnelmentals</h4>
					<div class="desc">
					Powerful funnel building software. Download it now from the WordPressOrg repository. 
					</div>
					<img src="<?php echo self::$plugin_data["images"]; ?>funnelmentals.png" />
					<a href="https://wordpress.org/plugins/web-disrupt-funnelmentals/" target="_funnelmentals"> Goto Plugin Page </a>
					<small>** Official Repo Manager </small>
					<div style="height:10px;"></div>
					<h4>Funnelmentals Pro</h4>
					<div class="desc">
					We also have a PRO version which is worth every penny. Video playlists, one-page WooCommerce checkouts, and more.
					</div>
					<img src="<?php echo self::$plugin_data["images"]; ?>funnelmentals-pro.png" />
					<a href="https://webdisrupt.com/funnelmentals/"  target="_funnelmentals_pro"> Get Funnelmentals </a>
					<small>** Official Plugin Vendor </small>
				</div>
				<div class="ELU__mp-item"> 
					<h4>Elementor Pro</h4>
					<div class="desc">
					Extends Elementor for the better. Get header/footers, global widgets, custom forms, per element css, menues, and more.
					</div>
					<img src="<?php echo self::$plugin_data["images"]; ?>elementor-pro.png" />
					<a href="https://elementor.com/pro/?ref=1544&campaign=webdisrupt"  target="_elementor_pro"> Get Elementor Pro </a>
					<small>** Affiliate Plugin Sponsor </small>
				</div>
				<div class="ELU__mp-item"> 
					<h4>Free Powerful Guide</h4>
					<div class="desc">
					Come visit Web Disrupt and get setup with the best information for WordPress, Funnelmentals, and Elementor. 
					We have a free $97 hyper-course available for everyone who signs up for our mailing list.
					</div>
					<img src="<?php echo self::$plugin_data["images"]; ?>flat-web-disrupt-animated-branding-super-charge-elementor.gif" />
					<a href="https://webdisrupt.com/"  target="_webdisrupt"> Goto WebDisrupt.com </a>
					<small>** Official Offer </small>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Print Versioning System
	 *
	 * @since 1.0.0
	 * @param [object] $data contains a unique index, name, and version array list
	 * @return void
	 */
	private function template_multi_version($data){
			?><label class="ELU__version-label"><?php echo sanitize_text_field($data['name']); ?>:</label><?php
			$itemCount = 0;	
			if(isset($data['list']) && count($data['list']) > 0 ){
				echo "<select id='ELU__version-".$data['uindex']."' data-thumb-class='ELU__index-".sanitize_text_field($data['uindex'])."' class='ELU__version'>";
				foreach($data['list'] as $item){
					$item_link = strtolower(str_replace(" ", "-", sanitize_text_field($item)));
					echo "<option value='".$item_link."'";
					if($itemCount == 0){ echo " selected "; }
					echo ">".sanitize_text_field($item)."</option>";
					$itemCount++;
				}
			} else {
				echo "<select id='ELU__version-".sanitize_text_field($data['uindex'])."' disabled class='ELU__variations'>";
				echo "<option value='none' selected='selected'> - None - </option>";
			}
			echo "</select>";
	}
	
	/**
	 * Updates config files based on if version is up-to-date - (v.txt)
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function update_if_needed(){
		/* Detect if version is up-to-date, if it isn't then update config (.json) files */
		if($aws_version = wp_remote_retrieve_body( wp_remote_get(("https://s3.us-east-2.amazonaws.com/web-disrupt-unlimited-library/v.txt")))){
			$local_version = get_option("ELU__current_version");
			if($aws_version != $local_version){
				$config_locations = [
					["local" => self::$plugin_data['config-library'], 'remote' => "https://s3.us-east-2.amazonaws.com/web-disrupt-unlimited-library/library.json"],
					["local" => self::$plugin_data['config-contributors'], 'remote' => "https://s3.us-east-2.amazonaws.com/web-disrupt-unlimited-library/contributors.json"]
				];
				foreach($config_locations as $location){
					$myfile = fopen($location['local'], "w");
					fwrite($myfile, wp_remote_retrieve_body( wp_remote_get(($location['remote']))));
					fclose($myfile);
				}
				update_option("ELU__current_version", sanitize_text_field($aws_version));
			}
		}
	}

	/**
	 * Get thumbnails/Previews & cache for a month (fallback to placeholder)
	 *
	 * @since 1.0.0
	 * @param [type] $folder - "Thumbnails" or "Previews"
	 * @param [type] $file_type - "png" or "jpg"
	 * @param [type] $file_name - The filename
	 * @return void
	 */
	private function get_and_cache_image($folder, $file_type, $file_name){
		$folder = sanitize_text_field($folder);
		$file_type = sanitize_text_field($file_type);
		$file_name = sanitize_title_with_dashes($file_name);
		$time = strtotime('now');
		$monthlyCache = "-".date("Y", $time)."-".date("m",$time);
		$prev_time = strtotime('-1 month -1 day');
		$previousCache = "-".date("Y", $prev_time)."-".date("m",$prev_time);
		$local_file = self::$plugin_data['temp-library'].$file_name.$monthlyCache.".".$file_type;
		$local_link = self::$plugin_data['temp-library-link'].$file_name.$monthlyCache.".".$file_type;
		$previous_local_file = self::$plugin_data['temp-library'].$file_name.$previousCache.".".$file_type;
		$remote_file = "https://s3.us-east-2.amazonaws.com/web-disrupt-unlimited-library/".$folder."/".$file_name.".".$file_type;
		if(file_exists($previous_local_file)){ // Delete cached file
			unlink($previous_local_file);
		}
		if(file_exists($local_file)){
			return $local_link;
		} else {
			if(wp_remote_head(($remote_file))) { // Get new file from remote if exists
				$myfile = fopen($local_file, "w");
				fwrite($myfile, wp_remote_retrieve_body( wp_remote_get(($remote_file))));
				fclose($myfile);
				return $local_link;
			} else { // If remote file doesn't exist then use placeholder
				return self::$plugin_data["images"]."empty.png";
			}
		}
	}

}

// Initialize the Web Disrupt Funnelmentals settings page
WD_Elementor_Library_Unlimited::init();

require __DIR__ . '/elementor-import-process.php';

} // Make sure class doesn't already exist