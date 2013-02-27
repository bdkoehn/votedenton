<?php

/*
*  Field Functions
*
*  @description: The API for all fields
*  @since: 3.6
*  @created: 23/01/13
*/

class acf_field_functions
{
	
	/*
	*  __construct
	*
	*  @description: 
	*  @since 3.1.8
	*  @created: 23/06/12
	*/
	
	function __construct()
	{
		//value
		add_filter('acf/load_value', array($this, 'load_value'), 5, 3);
		add_action('acf/update_value', array($this, 'update_value'), 5, 3);
		add_action('acf/delete_value', array($this, 'delete_value'), 5, 2);
		add_action('acf/format_value', array($this, 'format_value'), 5, 2);
		add_action('acf/format_value_for_api', array($this, 'format_value_for_api'), 5, 2);
		
		
		// field
		add_filter('acf/load_field', array($this, 'load_field'), 5, 3);
		add_action('acf/update_field', array($this, 'update_field'), 5, 2);
		add_action('acf/delete_field', array($this, 'delete_field'), 5, 2);
		add_action('acf/create_field', array($this, 'create_field'), 5, 1);
		add_action('acf/create_field_options', array($this, 'create_field_options'), 5, 1);
		
		
		// extra
		add_filter('acf/load_field_defaults', array($this, 'load_field_defaults'), 5, 1);
	}
	
	
	/*
	*  load_value
	*
	*  @description: loads basic value from the db
	*  @since: 3.6
	*  @created: 23/01/13
	*/
	
	function load_value($value, $post_id, $field)
	{
		$cache = wp_cache_get( 'value-' . $post_id . '-' . $field['name'], 'acf' );
		if( $cache )
		{
			return $cache;
		}
		
		
		// if $post_id is a string, then it is used in the everything fields and can be found in the options table
		if( is_numeric($post_id) )
		{
			$value = get_post_meta( $post_id, $field['name'], false );
			
			// value is an array, check and assign the real value / default value
			if( !isset($value[0]) )
			{
				if( isset($field['default_value']) )
				{
					$value = $field['default_value'];
				}
				else
				{
					$value = false;
				}
		 	}
		 	else
		 	{
			 	$value = $value[0];
		 	}
		}
		elseif( strpos($post_id, 'user_') !== false )
		{
			$post_id = str_replace('user_', '', $post_id);
			
			$value = get_user_meta( $post_id, $field['name'], false );
			
			// value is an array, check and assign the real value / default value
			if( !isset($value[0]) )
			{
				if( isset($field['default_value']) )
				{
					$value = $field['default_value'];
				}
				else
				{
					$value = false;
				}
		 	}
		 	else
		 	{
			 	$value = $value[0];
		 	}
		}
		else
		{
			$value = get_option( $post_id . '_' . $field['name'], null );
			
			if( is_null($value) )
			{
				if( isset($field['default_value']) )
				{
					$value = $field['default_value'];
				}
				else
				{
					$value = false;
				}
		 	}

		}
		
		
		// if value was duplicated, it may now be a serialized string!
		$value = maybe_unserialize($value);
		
		
		// apply filters
		$value = apply_filters('acf_load_value', $value, $field, $post_id );
		
		$keys = array('type', 'name', 'key');
		$called = array(); // field[type] && field[name] may be the same! Don't run the same filter twice!
		foreach( $keys as $key )
		{
			// validate
			if( !isset($field[ $key ]) ){ continue; }
			if( in_array($field[ $key ], $called) ){ continue; }
			
			
			// add to $called
			$called[] = $field[ $key ];
			
			
			// run filters
			$value = apply_filters('acf_load_value-' . $field[ $key ], $value, $post_id, $field); // old filter
			$value = apply_filters('acf/load_value-' . $field[ $key ], $value, $post_id, $field); // new filter
			
		}
		
		
		//update cache
		wp_cache_set( 'value-' . $post_id . '-' . $field['name'], $value, 'acf' );
		
		return $value;
	}
	
	
	/*
	*  format_value
	*
	*  @description: uses the basic value and allows the field type to format it
	*  @since: 3.6
	*  @created: 26/01/13
	*/
	
	function format_value( $value, $field )
	{
		$value = apply_filters('acf/format_value-' . $field['type'] , $value, $field);
		
		return $value;
	}
	
	
	/*
	*  format_value_for_api
	*
	*  @description: uses the basic value and allows the field type to format it or the api functions
	*  @since: 3.6
	*  @created: 26/01/13
	*/
	
	function format_value_for_api( $value, $field )
	{
		$value = apply_filters('acf/format_value_for_api-' . $field['type'] , $value, $field);
		
		return $value;
	}
	
	
	/*
	*  update_value
	*
	*  @description: updates a value into the db
	*  @since: 3.6
	*  @created: 23/01/13
	*/
	
	function update_value( $value, $field, $post_id )
	{
		// strip slashes
		$value = stripslashes_deep($value);
		
		
		
		// apply filters
		$value = apply_filters('acf_update_value', $value, $field, $post_id );
		
		$keys = array('type', 'name', 'key');
		$called = array(); // field[type] && field[name] may be the same! Don't run the same filter twice!
		foreach( $keys as $key )
		{
			// validate
			if( !isset($field[ $key ]) ){ continue; }
			if( in_array($field[ $key ], $called) ){ continue; }
			
			
			// add to $called
			$called[] = $field[ $key ];
			
			
			// run filters
			$value = apply_filters('acf_update_value-' . $field[ $key ], $value, $field, $post_id); // old filter
			$value = apply_filters('acf/update_value-' . $field[ $key ], $value, $field, $post_id); // new filter
			//echo 'acf/update_value-' . $field[ $key ] . '<br />';
		}

				
		
		// if $post_id is a string, then it is used in the everything fields and can be found in the options table
		if( is_numeric($post_id) )
		{
			// allow ACF to save to revision!
			update_metadata('post', $post_id, $field['name'], $value );
			update_metadata('post', $post_id, '_' . $field['name'], $field['key']);
			
			//update_post_meta( $post_id, $field['name'], $value );
			//update_post_meta( $post_id, '_' . $field['name'], $field['key'] );
		}
		elseif( strpos($post_id, 'user_') !== false )
		{
			$post_id = str_replace('user_', '', $post_id);
			update_user_meta( $post_id, $field['name'], $value );
			update_user_meta( $post_id, '_' . $field['name'], $field['key'] );
		}
		else
		{
			update_option( $post_id . '_' . $field['name'], $value );
			update_option( '_' . $post_id . '_' . $field['name'], $field['key'] );
		}
		
		
		//clear the cache for this field
		wp_cache_delete( 'value-' . $post_id . '-' . $field['name'], 'acf' );
	}
	
	
	/*
	*  delete_value
	*
	*  @description: deletes a value from the database
	*  @since: 3.6
	*  @created: 23/01/13
	*/
	
	function delete_value( $post_id, $key )
	{
		// if $post_id is a string, then it is used in the everything fields and can be found in the options table
		if( is_numeric($post_id) )
		{
			delete_post_meta( $post_id, $key );
			delete_post_meta( $post_id, '_' . $key );
		}
		elseif( strpos($post_id, 'user_') !== false )
		{
			$post_id = str_replace('user_', '', $post_id);
			delete_user_meta( $post_id, $key );
			delete_user_meta( $post_id, '_' . $key );
		}
		else
		{
			delete_option( $post_id . '_' . $key );
			delete_option( '_' . $post_id . '_' . $key );
		}
		
		wp_cache_delete( 'value-' . $post_id . '-' . $key, 'acf' );
	}
	
	
	/*
	*  load_field
	*
	*  @description: loads a field from the database
	*  @since 3.5.1
	*  @created: 14/10/12
	*/
	
	function load_field( $field, $field_key, $post_id = false )
	{
		// return cache
		$cache = wp_cache_get( 'load_field-' . $field_key, 'acf' );
		if($cache != false)
		{
			return $cache;
		}
		
		
		// vars
		global $wpdb;
		
		
		// get field from postmeta
		$sql = $wpdb->prepare("SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = %s", $field_key);
		
		if( $post_id )
		{
			$sql .= $wpdb->prepare("AND post_id = %d", $post_id);
		}

		$rows = $wpdb->get_results( $sql, ARRAY_A );
		

		if( is_array($rows) )
		{
			// potentialy, get_field_objects has picked up sub fields! These can't be found via sql so feturn false.
			if( !isset($rows[0]) )
			{
				return false;
			}
			
			
			$row = $rows[0];
			
			
			/*
			*  WPML compatibility
			*
			*  If WPML is active, and the $post_id (Field group ID) was not defined,
			*  it is assumed that the load_field functio has been called from the API (front end).
			*  In this case, the field group ID is never known and we can check for the correct translated field group
			*/
			
			if( defined('ICL_LANGUAGE_CODE') && !$post_id )
			{
				$wpml_post_id = icl_object_id($row['post_id'], 'acf', true, ICL_LANGUAGE_CODE);
				
				foreach( $rows as $r )
				{
					if( $r['post_id'] == $wpml_post_id )
					{
						// this row is a field from the translated field group
						$row = $r;
					}
				}
			}
			
			
			// return field if it is not in a trashed field group
			if( get_post_status( $row['post_id'] ) != "trash" )
			{
				$row['meta_value'] = maybe_unserialize( $row['meta_value'] );
				$row['meta_value'] = maybe_unserialize( $row['meta_value'] ); // run again for WPML
				
				
				// store field
				$field = $row['meta_value'];
				
				
				// apply filters
				$field = apply_filters('acf/load_field_defaults', $field);
				
				
				$keys = array('type', 'name', 'key');
				$called = array(); // field[type] && field[name] may be the same! Don't run the same filter twice!
				foreach( $keys as $key )
				{
					// validate
					if( in_array($field[ $key ], $called) ){ continue; }
					
					
					// add to $called
					$called[] = $field[ $key ];
					
					
					// run filters
					$field = apply_filters('acf_load_field-' . $field[ $key ], $field); // old filter
					$field = apply_filters('acf/load_field-' . $field[ $key ], $field); // new filter
					
				}
				
				
				// apply filters
				$field = apply_filters('acf_load_field', $field);
				
			
				// set cache
				wp_cache_set( 'load_field-' . $field_key, $field, 'acf' );
				
				return $field;
			}
		}
		


		// hook to load in registered field groups
		$acfs = apply_filters('acf/get_field_groups', false);
		
		if($acfs)
		{
			// loop through acfs
			foreach($acfs as $acf)
			{
				// loop through fields
				if($acf['fields'])
				{
					foreach($acf['fields'] as $field)
					{
						if($field['key'] == $field_key)
						{
							// apply filters
							$field = apply_filters('acf_load_field', $field);
							
							
							$keys = array('type', 'name', 'key');
							$called = array(); // field[type] && field[name] may be the same! Don't run the same filter twice!
							foreach( $keys as $key )
							{
								// validate
								if( !isset($field[ $key ]) ){ continue; }
								if( in_array($field[ $key ], $called) ){ continue; }
								
								
								// add to $called
								$called[] = $field[ $key ];
								
								
								// run filters
								$field = apply_filters('acf_load_field-' . $field[ $key ], $field); // old filter
								$field = apply_filters('acf/load_field-' . $field[ $key ], $field); // new filter
								
							}
							
							
							// set cache
							wp_cache_set( 'load_field-' . $field_key, $field, 'acf' );
							
							return $field;
						}
					}
				}
				// if($acf['fields'])
			}
			// foreach($acfs as $acf)
		}
		// if($acfs)

 		
 		return null;
	}
	
	
	/*
	*  load_field_defaults
	*
	*  @description: applies default values to the field after it has been loaded
	*  @since 3.5.1
	*  @created: 14/10/12
	*/
	
	function load_field_defaults( $field )
	{
		// validate $field
		if( ! $field )
		{
			$field = array();
		}
		
		
		// defaults
		$defaults = array(
			'key' => '',
			'label' => '',
			'name' => '',
			'type' => 'text',
			'order_no' => 1,
			'instructions' => '',
			'required' => 0,
			'id' => '',
			'class' => '',
			'conditional_logic' => array(
				'status' => 0,
				'allorany' => 'all',
				'rules' => 0
			),
		);
		$field = array_merge($defaults, $field);
		
		
		// Parse Values
		$field = apply_filters( 'acf/parse_types', $field );
		
		
		// class
		if( !$field['class'] )
		{
			$field['class'] = $field['type'];
		}
		
		
		// id
		if( !$field['id'] )
		{
			$id = $field['name'];
			$id = str_replace('][', '_', $id);
			$id = str_replace('fields[', '', $id);
			$id = str_replace('[', '-', $id); // location rules (select) does'nt have "fields[" in it
			$id = str_replace(']', '', $id);
			
			$field['id'] = 'acf-' . $id;
		}
		
		
		// return
		return $field;
	}
	
	
	/*
	*  update_field
	*
	*  @description: updates a field in the database
	*  @since: 3.6
	*  @created: 24/01/13
	*/
	
	function update_field( $field, $post_id )
	{
		// filters
		$field = apply_filters('acf/update_field-' . $field['type'], $field, $post_id ); // new filter
		
		
		// save
		update_post_meta( $post_id, $field['key'], $field );
	}
	
	
	/*
	*  delete_field
	*
	*  @description: deletes a field in the database
	*  @since: 3.6
	*  @created: 24/01/13
	*/
	
	function delete_field( $post_id, $field_key )
	{
		// delete
		delete_post_meta($post_id, $field_key);
	}
	
	
	/*
	*  create_field
	*
	*  @description: renders a field into a HTML interface
	*  @since: 3.6
	*  @created: 23/01/13
	*/
	
	function create_field( $field )
	{
		// load defaults
		// if field was loaded from db, these default will already be appield
		// if field was written by hand, it may be missing keys
		$field = apply_filters('acf/load_field_defaults', $field);
		
		
		// create field specific html
		do_action('acf/create_field-' . $field['type'], $field);
		
		
		// conditional logic
		// - isset is needed for the edit field group page where fields are created without many parameters
		if( $field['conditional_logic']['status'] ):
			
			$join = ' && ';
			if( $field['conditional_logic']['allorany'] == "any" )
			{
				$join = ' || ';
			}
			
			?>
<script type="text/javascript">
(function($){
	
	// create the conditional function
	$(document).live('acf/conditional_logic/<?php echo $field['key']; ?>', function(){
		
		var field = $('[data-field_key="<?php echo $field['key']; ?>"]');

<?php

		$if = array();
		foreach( $field['conditional_logic']['rules'] as $rule )
		{
			$if[] = 'acf.conditional_logic.calculate({ field : "'. $field['key'] .'", toggle : "' . $rule['field'] . '", operator : "' . $rule['operator'] .'", value : "' . $rule['value'] . '"})' ;
		}
		
?>
		if(<?php echo implode( $join, $if ); ?>)
		{
			field.removeClass('acf-conditional_logic-hide').addClass('acf-conditional_logic-show');
		}
		else
		{
			field.removeClass('acf-conditional_logic-show').addClass('acf-conditional_logic-hide');
		}
		
	});
	
	
	// add change events to all fields
<?php 

$already_added = array();

foreach( $field['conditional_logic']['rules'] as $rule ): 

	if( in_array( $rule['field'], $already_added) )
	{
		continue;
	}
	else
	{
		$already_added[] = $rule['field'];
	}
	
	?>
	$('.field-<?php echo $rule['field']; ?> *[name]').live('change', function(){
		$(document).trigger('acf/conditional_logic/<?php echo $field['key']; ?>');
	});
<?php endforeach; ?>
	
	$(document).live('acf/setup_fields', function(e, postbox){
		$(document).trigger('acf/conditional_logic/<?php echo $field['key']; ?>');
	});
		
})(jQuery);
</script>
			<?php
		endif;
	}
	
	
	/*
	*  create_field_options
	*
	*  @description: renders a field into a HTML interface
	*  @since: 3.6
	*  @created: 23/01/13
	*/
	
	function create_field_options($field)
	{
		do_action('acf/create_field_options-' . $field['type'], $field);
	}

	
	
}

new acf_field_functions();

?>