<?php
/*
Plugin Name: Carto_hal
Plugin URI: 
Description: Cartography of HAL repo
Version: 1.0
Author: Guiot Anthony
*/

/**
 * Register the widget
 */
add_action('widgets_init', create_function('', 'return register_widget("Widget_Carto_Hal");'));

/**
 * Class Widget_Carto_Hal
 */
class Widget_Carto_Hal extends WP_Widget
{
	/** Basic Widget Settings */
	const WIDGET_NAME = "Widget_Carto_Hal";
	const WIDGET_DESCRIPTION = "Cartography of HAL repository";

	var $textdomain;
	var $fields;

	/**
	 * Construct the widget
	 */
	function __construct()
	{
		//We're going to use $this->textdomain as both the translation domain and the widget class name and ID
		$this->textdomain = strtolower(get_class($this));

		//Figure out your textdomain for translations via this handy debug print
		//var_dump($this->textdomain);

		//Add fields
		$this->add_field('title', 'Enter title', 'Cartographie HAL', 'text');
		$this->add_field('ApiURL', 'Enter ApiUrl', 'http://api.archives-ouvertes.fr', 'text');
		$this->add_field('DisplayMap', 'Display or not map', 'true', 'text');
		$this->add_field('DisplayDatatable', 'Display or not table', 'false', 'text');
		$this->add_field('query', 'Enter a collection', 'UL', 'text');


		//Init the widget
		parent::__construct($this->textdomain, __(self::WIDGET_NAME, $this->textdomain), array( 'description' => __(self::WIDGET_DESCRIPTION, $this->textdomain), 'classname' => $this->textdomain));
	}

	/**
	 * Widget frontend
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance)
	{
		$title = apply_filters('widget_title', $instance['title']);

		/* Before and after widget arguments are usually modified by themes */
		echo $args['before_widget'];
		wp_enqueue_style( 'semanticui', plugin_dir_url( __FILE__ ) . 'css/semantic/dist/semantic.min.css' );
		wp_enqueue_style( 'leafletcss', plugin_dir_url( __FILE__ ) . 'app/js/leaflet/leaflet.css' );
		wp_enqueue_style( 'css', plugin_dir_url( __FILE__ ) . 'css/style.css' );
		wp_enqueue_style( 'jqureyrangecss', plugin_dir_url( __FILE__ ) . 'css/jquery.range.css' );

		wp_enqueue_script( 'jquery');
		wp_enqueue_script( 'angular', plugin_dir_url( __FILE__ ) . 'app/js/angular.js' );
		wp_enqueue_script( 'printjs', plugin_dir_url( __FILE__ ) . 'app/js/print.js' );
		wp_enqueue_script( 'jquery-range', plugin_dir_url( __FILE__ ) . 'app/js/jquery.range-min.js' );
		wp_enqueue_script( 'leaflet', plugin_dir_url( __FILE__ ) . 'app/js/leaflet/leaflet.js' );
		wp_enqueue_script( 'app', plugin_dir_url( __FILE__ ) . 'app/app.min.js' );
		wp_enqueue_script( 'config', plugin_dir_url( __FILE__ ) . 'app/ConfigDefault.js' );
		wp_enqueue_script( 'dataTables', plugin_dir_url( __FILE__ ) . 'app/js/jquery.dataTables.min.js' );
		wp_enqueue_script( 'buttons', plugin_dir_url( __FILE__ ) . 'app/js/dataTables.buttons.min.js' );
		wp_enqueue_script( 'semanticjs', plugin_dir_url( __FILE__ ) . 'app/js/dataTables.semanticui.min.js' );
		wp_enqueue_script( 'buttonjs', plugin_dir_url( __FILE__ ) . 'app/js/buttons.html5.min.js' );

		if (!empty($title))
			echo $args['before_title'] . $title . $args['after_title'];

		/* Widget output here */
		$this->widget_output($args, $instance);

		/* After widget */
		echo $args['after_widget'];
	}
	
	/**
	 * This function will execute the widget frontend logic.
	 * Everything you want in the widget should be output here.
	 */
	private function widget_output($args, $instance)
	{
		

		extract($instance);

		/**
		 * This is where you write your custom code.
		 */
		?>
			<html ng-app="cartoHal">
    <head>
     
        <script type="text/javascript">
        $=jQuery.noConflict(); 
         var ConfigWidgetHal={
            ApiURL:"<?php echo esc_attr(isset($instance["ApiURL"]) ? $instance["ApiURL"] : $field_data["default_value"]); ?>",
            DisplayMap:<?php echo esc_attr(isset($instance["DisplayMap"]) ? $instance["DisplayMap"] : $field_data["default_value"]); ?>,
            DisplayDatatable:<?php echo esc_attr(isset($instance["DisplayDatatable"]) ? $instance["DisplayDatatable"] : $field_data["default_value"]); ?>,
            query:"<?php echo esc_attr(isset($instance["query"]) ? $instance["query"] : $field_data['default_value']); ?>"
          }

        </script>


          <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    </head>
    <body>
    <h1>Widget Hal</h1>

    <search></search>

    </body>
</html>
		<?php
	}

	/**
	 * Widget backend
	 *
	 * @param array $instance
	 * @return string|void
	 */
	public function form( $instance )
	{
		/* Generate admin for fields */
		foreach($this->fields as $field_name => $field_data)
		{
			if($field_data['type'] === 'text'):
				?>
				<p>
					<label for="<?php echo $this->get_field_id($field_name); ?>"><?php _e($field_data['description'], $this->textdomain ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id($field_name); ?>" name="<?php echo $this->get_field_name($field_name); ?>" type="text" value="<?php echo esc_attr(isset($instance[$field_name]) ? $instance[$field_name] : $field_data['default_value']); ?>" />
				</p>
			<?php
			//elseif($field_data['type'] == 'textarea'):
			//You can implement more field types like this.
			else:
				echo __('Error - Field type not supported', $this->textdomain) . ': ' . $field_data['type'];
			endif;
		}
	}

	/**
	 * Adds a text field to the widget
	 *
	 * @param $field_name
	 * @param string $field_description
	 * @param string $field_default_value
	 * @param string $field_type
	 */
	private function add_field($field_name, $field_description = '', $field_default_value = '', $field_type = 'text')
	{
		if(!is_array($this->fields))
			$this->fields = array();

		$this->fields[$field_name] = array('name' => $field_name, 'description' => $field_description, 'default_value' => $field_default_value, 'type' => $field_type);
	}

	/**
	 * Updating widget by replacing the old instance with new
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update($new_instance, $old_instance)
	{
		return $new_instance;
	}
}
