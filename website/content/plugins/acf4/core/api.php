<?php

// vars
$GLOBALS['acf_field'] = array();


/*
*  acf_filter_post_id()
*
*  A helper function to filter the post_id variable.
*
*  @type	function
*  @since	3.6
*  @date	29/01/13
*
*  @param	$post_id
*
*  @return	$post_id
*/

function acf_filter_post_id( $post_id )
{
	// global
	global $post; 
	 
	
	// set post_id to global
	if( !$post_id )
	{
		$post_id = $post->ID;
	}
	
	
	// allow for option == options
	if( $post_id == "option" )
	{
		$post_id = "options";
	}
	
	
	/*
	*  Override for preview
	*  
	*  If the $_GET['preview_id'] is set, then the user wants to see the preview data.
	*  There is also the case of previewing a page with post_id = 1, but using get_field
	*  to load data from another post_id.
	*  In this case, we need to make sure that the autosave revision is actually related
	*  to the $post_id variable. If they match, then the autosave data will be used, otherwise, 
	*  the user wants to load data from a completely different post_id
	*/
	
	if( isset($_GET['preview_id']) )
	{
		$autosave = wp_get_post_autosave( $_GET['preview_id'] );
		if( $autosave->post_parent == $post_id )
		{
			$post_id = $autosave->ID;
		}
	}
	
	
	// return
	return $post_id;
}


/*
*  get_field_reference()
*
*  This function will find the $field_key that is related to the $field_name.
*  This is know as the field value reference
*
*  @type	function
*  @since	3.6
*  @date	29/01/13
*
*  @param	$field_name - the name of the field - 'sub_heading'
*  @param	$post_id - the post_id of which the value is saved against
*
*  @return	$return - a string containing the field_key
*/

function get_field_reference( $field_name, $post_id )
{
	// cache
	$cache = wp_cache_get( 'field_reference-' . $post_id . '-' . $field_name, 'acf' );
	if( $cache )
	{
		return $cache;
	}
	
	
	// vars
	$return = '';

	
	// get field key
	if( is_numeric($post_id) )
	{
		$return = get_post_meta($post_id, '_' . $field_name, true); 
	}
	elseif( strpos($post_id, 'user_') !== false )
	{
		$temp_post_id = str_replace('user_', '', $post_id);
		$return = get_user_meta($temp_post_id, '_' . $field_name, true); 
	}
	else
	{
		$return = get_option('_' . $post_id . '_' . $field_name); 
	}
	
	
	// set cache
	wp_cache_set( 'field_reference-' . $post_id . '-' . $field_name, $return, 'acf' );
		
	
	// return	
	return $return;
}


/*
*  get_field_objects()
*
*  This function will return an array containing all the custom field objects for a specific post_id.
*  The function is not very elegant and wastes a lot of PHP memory / SQL queries if you are not using all the fields / values.
*
*  @type	function
*  @since	3.6
*  @date	29/01/13
*
*  @param	$post_id - the post_id of which the value is saved against
*
*  @return	$return - an array containin the field groups
*/

function get_field_objects( $post_id = false )
{
	// global
	global $wpdb;
	
	
	// filter post_id
	$post_id = acf_filter_post_id( $post_id );


	// vars
	$field_key = '';
	$value = array();
	
	
	// get field_names
	if( is_numeric($post_id) )
	{
		$keys = $wpdb->get_col($wpdb->prepare(
			"SELECT meta_value FROM $wpdb->postmeta WHERE post_id = %d and meta_key LIKE %s AND meta_value LIKE %s",
			$post_id,
			'\_%',
			'field\_%'
		));
	}
	elseif( strpos($post_id, 'user_') !== false )
	{
		$user_id = str_replace('user_', '', $post_id);
		
		$keys = $wpdb->get_col($wpdb->prepare(
			"SELECT meta_value FROM $wpdb->usermeta WHERE user_id = %d and meta_key LIKE %s AND meta_value LIKE %s",
			$user_id,
			'\_%',
			'field\_%'
		));
	}
	else
	{
		$keys = $wpdb->get_col($wpdb->prepare(
			"SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s",
			'\_' . $post_id . '\_%' 
		));
	}


	if( is_array($keys) )
	{
		foreach( $keys as $key )
		{
			$field = get_field_object( $key, $post_id );
			
			if( !is_array($field) )
			{
				continue;
			}
			
			$value[ $field['name'] ] = $field;
		}
 	}
 	
 	
	// no value
	if( empty($value) )
	{
		return false;
	}
	
	
	// return
	return $value;
}


/*
*  get_fields()
*
*  This function will return an array containing all the custom field values for a specific post_id.
*  The function is not very elegant and wastes a lot of PHP memory / SQL queries if you are not using all the values.
*
*  @type	function
*  @since	3.6
*  @date	29/01/13
*
*  @param	$post_id - the post_id of which the value is saved against
*
*  @return	$return - an array containin the field values
*/

function get_fields( $post_id = false )
{
	$fields = get_field_objects( $post_id );
	
	if( is_array($fields) )
	{
		foreach( $fields as $k => $field )
		{
			$fields[ $k ] = $field['value'];
		}
	}
	
	return $fields;	
}


/*
*  get_field()
*
*  This function will return a custom field value for a specific field name/key + post_id.
*  There is a 3rd parameter to turn on/off formating. This means that an Image field will not use 
*  its 'return option' to format the value but return only what was saved in the database
*
*  @type	function
*  @since	3.6
*  @date	29/01/13
*
*  @param	$field_key - string containing the name of teh field name / key ('sub_field' / 'field_1')
*  @param	$post_id - the post_id of which the value is saved against
*  @param	$format_value - whether or not to format the value as described above
*
*  @return	$value - the value found
*/
 
function get_field( $field_key, $post_id = false, $format_value = true ) 
{
	// filter post_id
	$post_id = acf_filter_post_id( $post_id );
	
	
	// vars
	$return = false;
	$options = array(
		'load_value' => true
	);
	
	
	// format value
	if( $format_value )
	{
		$options['format_value'] = true;
	}
	
	$field = get_field_object( $field_key, $post_id, $options);
	
	
	if( is_array($field) )
	{
		$return = $field['value'];
	}
	
	
	return $return;
	 
}


/*
*  get_field_object()
*
*  This function will return an array containing all the field data for a given field_name
*
*  @type	function
*  @since	3.6
*  @date	3/02/13
*
*  @param	$field_key - string containing the name of teh field name / key ('sub_field' / 'field_1')
*  @param	$post_id - the post_id of which the value is saved against
*  @param	$options - an array containing options
*				+ load_value - true | false
*				+ format_value - true | false
*
*  @return	$return - an array containin the field groups
*/

function get_field_object( $field_key, $post_id = false, $options = array() )
{
	// filter post_id
	$post_id = acf_filter_post_id( $post_id );
	$field = false;
	
	
	// defaults for options
	$defaults = array(
		'load_value'	=>	true,
		'format_value'	=>	true,
	);
	
	$options = array_merge($defaults, $options);
	
	
	// is $field_name a name? pre 3.4.0
	if( strpos($field_key, "field_") === false )
	{
		// get field key
		$field_key = get_field_reference( $field_key, $post_id );
	}
	
	
	// get field
	if( strpos($field_key, "field_") !== false )
	{
		$field = apply_filters('acf/load_field', false, $field_key );
	}
	
	
	// validate field
	if( !$field )
	{
		// treat as text field
		$field = array(
			'type' => 'text',
			'name' => $field_key
		);
	}
	
	
	// load value
	if( $options['load_value'] )
	{
		$field['value'] = apply_filters('acf/load_value', false, $post_id, $field);
		
		
		// format value
		if( $options['format_value'] )
		{
			$field['value'] = apply_filters('acf/format_value_for_api', $field['value'], $field);
		}
	}


	return $field;

}


/*
*  the_field()
*
*  This function is the same as echo get_field().
*
*  @type	function
*  @since	1.0.3
*  @date	29/01/13
*
*  @param	$field_name - the name of the field - 'sub_heading'
*  @param	$post_id - the post_id of which the value is saved against
*
*  @return	
*/

function the_field( $field_name, $post_id = false )
{
	$value = get_field($field_name, $post_id);
	
	if( is_array($value) )
	{
		$value = @implode(', ',$value);
	}
	
	echo $value;
}


/*
*  the_field()
*
*  This function is used inside a while loop to return either true or false (loop again or stop).
*  When using a repeater or flexible content field, it will loop through the rows until 
*  there are none left or a break is detected
*
*  @type	function
*  @since	1.0.3
*  @date	29/01/13
*
*  @param	$field_name - the name of the field - 'sub_heading'
*  @param	$post_id - the post_id of which the value is saved against
*
*  @return	bool
*/

function has_sub_field( $field_name, $post_id = false )
{
	// filter post_id
	$post_id = acf_filter_post_id( $post_id );
	
	
	// empty?
	if( empty($GLOBALS['acf_field']) )
	{
		// vars
		$f = get_field_object( $field_name, $post_id );
		$v = $f['value'];
		unset( $f['value'] );
		
		
		$GLOBALS['acf_field'][] = array(
			'name'	=>	$field_name,
			'value'	=>	$v,
			'field'	=>	$f,
			'row'	=>	-1,
			'post_id' => $post_id,
		);
	}
	

	// vars
	$depth = count( $GLOBALS['acf_field'] ) - 1;
	$name = $GLOBALS['acf_field'][$depth]['name'];
	$value = $GLOBALS['acf_field'][$depth]['value'];
	$field = $GLOBALS['acf_field'][$depth]['field'];
	$row = $GLOBALS['acf_field'][$depth]['row'];
	$id = $GLOBALS['acf_field'][$depth]['post_id'];
	
	
	// if ID has changed, this is a new repeater / flexible field!
	if( $post_id != $id )
	{
		// reset
		$GLOBALS['acf_field'] = array();
		return has_sub_field($field_name, $post_id);
	}

	
	// does the given $field_name match the current field?
	if( $field_name != $name )
	{
		// is this a "new" while loop refering to a sub field?
		if( isset($value[ $row ][ $field_name ]) )
		{
			$GLOBALS['acf_field'][] = array(
				'name'	=>	$field_name,
				'value'	=>	$value[ $row ][ $field_name ],
				'field' => acf_get_child_field_from_parent_field( $field_name, $field ),
				'row'	=>	-1,
				'post_id' => $post_id,
			);
		}
		elseif( isset($GLOBALS['acf_field'][$depth-1]) && $GLOBALS['acf_field'][$depth-1]['name'] == $field_name )
		{
			// if someone used break; We should see if the parent value has this field_name as a value.
			unset( $GLOBALS['acf_field'][$depth] );
			$GLOBALS['acf_field'] = array_values($GLOBALS['acf_field']);
		}
		else
		{
			// this was a break; (probably to get the first row only). Clear the repeater
			$GLOBALS['acf_field'] = array();
			return has_sub_field($field_name, $post_id);
		}
		
	}
	
	
	// update vars
	$depth = count( $GLOBALS['acf_field'] ) - 1;
	$value = $GLOBALS['acf_field'][$depth]['value'];
	$field = $GLOBALS['acf_field'][$depth]['field'];
	$row = $GLOBALS['acf_field'][$depth]['row'];

		
	// increase row number
	$GLOBALS['acf_field'][$depth]['row']++;
	$row++;
	
	
	if( isset($value[$row]) )
	{
		// next row exists
		return true;
	}
	
	
	// no next row! Unset this array and return false to stop while loop
	unset( $GLOBALS['acf_field'][$depth] );
	$GLOBALS['acf_field'] = array_values($GLOBALS['acf_field']);

	return false;
	
}


/*
*  get_sub_field()
*
*  This function is used inside a 'has_sub_field' while loop to return a sub field value
*
*  @type	function
*  @since	1.0.3
*  @date	29/01/13
*
*  @param	$field_name - the name of the field - 'sub_heading'
*
*  @return	mixed
*/

function get_sub_field( $field_name )
{

	// no field?
	if( empty($GLOBALS['acf_field']) )
	{
		return false;
	}
	
	
	// vars
	$depth = count( $GLOBALS['acf_field'] ) - 1;
	$value = $GLOBALS['acf_field'][$depth]['value'];
	$field = $GLOBALS['acf_field'][$depth]['field'];
	$row = $GLOBALS['acf_field'][$depth]['row'];


	// no value at i
	if( !isset($value[ $row ][ $field_name ]) )
	{
		return false;
	}

	
	return $value[ $row ][ $field_name ];
}


/*
*  get_sub_field()
*
*  This function is the same as echo get_sub_field
*
*  @type	function
*  @since	1.0.3
*  @date	29/01/13
*
*  @param	$field_name - the name of the field - 'sub_heading'
*
*  @return	
*/

function the_sub_field($field_name)
{
	$value = get_sub_field($field_name);
	
	if(is_array($value))
	{
		$value = implode(', ',$value);
	}
	
	echo $value;
}


/*
*  get_sub_field_object()
*
*  This function is used inside a 'has_sub_field' while loop to return a sub field object
*
*  @type	function
*  @since	3.5.8.1
*  @date	29/01/13
*
*  @param	$field_name - the name of the field - 'sub_heading'
*
*  @return	mixed
*/

function get_sub_field_object( $child_name )
{
	// no field?
	if( empty($GLOBALS['acf_field']) )
	{
		return false;
	}


	// vars
	$depth = count( $GLOBALS['acf_field'] ) - 1;
	$parent = $GLOBALS['acf_field'][$depth]['field'];


	// return
	return acf_get_child_field_from_parent_field( $child_name, $parent );
	
}


/*
*  acf_get_sub_field_from_parent_field
*
*  @description: 
*  @since: 3.6
*  @created: 23/02/13
*/

function acf_get_child_field_from_parent_field( $child_name, $parent )
{
	// vars
	$return = false;
	
	
	// find child
	if( isset($parent['sub_fields']) && is_array($parent['sub_fields']) )
	{
		foreach( $parent['sub_fields'] as $child )
		{
			if( $child['name'] == $child_name || $child['key'] == $child_name )
			{
				$return = $child;
				break;
			}
		}
	}
	elseif( isset($parent['layouts']) && is_array($parent['layouts']) )
	{
		foreach( $parent['layouts'] as $layout )
		{
			if( isset($layout['sub_fields']) && is_array($layout['sub_fields']) )
			{
				foreach( $layout['sub_fields'] as $child )
				{
					if( $child['name'] == $child_name || $child['key'] == $child_name )
					{
						$return = $child;
						break;
					}
				}
			}
		}
	}
	

	// return
	return $return;
	
}


/*
*  register_field_group()
*
*  This function is used to register a field group via code. It acceps 1 array containing
*  all the field group data. This data can be obtained by using teh export tool within ACF
*
*  @type	function
*  @since	3.0.6
*  @date	29/01/13
*
*  @param	$array - an array holding all the field group data
*
*  @return
*/

$GLOBALS['acf_register_field_group'] = array();

function register_field_group( $array )
{
	// add id
	if( !isset($array['id']) )
	{
		$array['id'] = uniqid();
	}
	

	// 3.2.5 - changed show_on_page option
	if( !isset($array['options']['hide_on_screen']) && isset($array['options']['show_on_page']) )
	{
		$show_all = array('the_content', 'discussion', 'custom_fields', 'comments', 'slug', 'author');
		$array['options']['hide_on_screen'] = array_diff($show_all, $array['options']['show_on_page']);
		unset( $array['options']['show_on_page'] );
	}


	$GLOBALS['acf_register_field_group'][] = $array;
}


add_filter('acf/get_field_groups', 'acf_register_field_group', 10, 1);
function acf_register_field_group( $return )
{

	// validate
	if( empty($GLOBALS['acf_register_field_group']) )
	{
		return $return;
	}
	
	
	// merge in custom
	$return = array_merge($return, $GLOBALS['acf_register_field_group']);
	
	
	
	// order field groups based on menu_order, title
	// Obtain a list of columns
	foreach( $return as $key => $row )
	{
	    $menu_order[ $key ] = $row['menu_order'];
	    $title[ $key ] = $row['title'];
	}
	
	// Sort the array with menu_order ascending
	// Add $array as the last parameter, to sort by the common key
	if(isset($menu_order))
	{
		array_multisort($menu_order, SORT_ASC, $title, SORT_ASC, $return);
	}
	
	return $return;
}


/*
*  get_row_layout()
*
*  This function will return a string representation of the current row layout within a 'has_sub_field' loop
*
*  @type	function
*  @since	3.0.6
*  @date	29/01/13
*
*  @param	$array - an array holding all the field group data
*
*  @return	$value - string containing the layout
*/

function get_row_layout()
{
	// vars
	$value = get_sub_field('acf_fc_layout');
	
	
	return $value;
}


/*
*  acf_shortcode()
*
*  This function is used to add basic shortcode support for the ACF plugin
*
*  @type	function
*  @since	1.1.1
*  @date	29/01/13
*
*  @param	$array - an array holding all the field group data
*
*  @return	$value - the value found by get_field
*/

function acf_shortcode( $atts )
{
	// extract attributs
	extract( shortcode_atts( array(
		'field' => "",
		'post_id' => false,
	), $atts ) );
	
	
	// $field is requird
	if( !$field || $field == "" )
	{
		return "";
	}
	
	
	// get value and return it
	$value = get_field( $field, $post_id );
	
	
	if( is_array($value) )
	{
		$value = @implode( ', ',$value );
	}
	
	return $value;
}
add_shortcode( 'acf', 'acf_shortcode' );


/*--------------------------------------------------------------------------------------
*
*	Front end form Head
*
*	@author Elliot Condon
*	@since 1.1.4
* 
*-------------------------------------------------------------------------------------*/

function acf_form_head()
{
	// global vars
	global $post_id;
	
	
	
	// run database save first
	if( isset($_POST['acf_save']) )
	{
		// $post_id to save against
		$post_id = $_POST['post_id'];
		
		
		// allow for custom save
		$post_id = apply_filters('acf_form_pre_save_post', $post_id);
		
		
		// save the data
		do_action('acf_save_post', $post_id);	
				
				
		// redirect
		if(isset($_POST['return']))
		{
			wp_redirect($_POST['return']);
			exit;
		}
		
	}
	
	
	// need wp styling
	wp_enqueue_style(array(
		'colors-fresh'
	));
	
		
	// actions
	do_action('acf/input/admin_enqueue_scripts');

	add_action('wp_head', 'acf_form_wp_head');
	
}

function acf_form_wp_head()
{
	do_action('acf/input/admin_head');
}


/*--------------------------------------------------------------------------------------
*
*	Front end form
*
*	@author Elliot Condon
*	@since 1.1.4
* 
*-------------------------------------------------------------------------------------*/

function acf_form($options = null)
{
	global $post;
	
	
	// defaults
	$defaults = array(
		'post_id' => $post->ID, // post id to get field groups from and save data to
		'field_groups' => array(), // this will find the field groups for this post
		'form_attributes' => array( // attributes will be added to the form element
			'class' => ''
		),
		'return' => add_query_arg( 'updated', 'true', get_permalink() ), // return url
		'html_before_fields' => '', // html inside form before fields
		'html_after_fields' => '', // html inside form after fields
		'submit_value' => 'Update', // vale for submit field
		'updated_message' => 'Post updated.', // default updated message. Can be false
	);
	
	
	// merge defaults with options
	if($options && is_array($options))
	{
		$options = array_merge($defaults, $options);
	}
	else
	{
		$options = $defaults;
	}
	
	
	
	// register post box
	if( !$options['field_groups'] )
	{
		// get field groups
		$filter = array(
			'post_id' => $options['post_id']
		);
		
		if( strpos($options['post_id'], 'user_') !== false )
		{
			$user_id = str_replace('user_', '', $options['post_id']);
			$filter['ef_user'] = $user_id;
		}
		elseif( strpos($options['post_id'], 'taxonomy_') !== false )
		{
			$taxonomy_id = str_replace('taxonomy_', '', $options['post_id']);
			$filter['ef_taxonomy'] = $taxonomy_id;
		}
		
		
		$options['field_groups'] = array();
		$options['field_groups'] = apply_filters( 'acf/location/match_field_groups', $options['field_groups'], $filter );
	}


	// updated message
	if(isset($_GET['updated']) && $_GET['updated'] == 'true' && $options['updated_message'])
	{
		echo '<div id="message" class="updated"><p>' . $options['updated_message'] . '</p></div>';
	}
	
	
	// Javascript
	$script_post_id = is_numeric($options['post_id']) ? $options['post_id'] : 0;
	echo '<script type="text/javascript">acf.post_id = ' . $script_post_id . '; </script>';
	
	
	// display form
	?>
	<form action="" id="post" method="post" <?php if($options['form_attributes']){foreach($options['form_attributes'] as $k => $v){echo $k . '="' . $v .'" '; }} ?>>
	<div style="display:none">
		<input type="hidden" name="acf_save" value="true" />
		<input type="hidden" name="post_id" value="<?php echo $options['post_id']; ?>" />
		<input type="hidden" name="return" value="<?php echo $options['return']; ?>" />
		<?php wp_editor('', 'acf_settings'); ?>
	</div>
	
	<div id="poststuff">
	<?php
	
	// html before fields
	echo $options['html_before_fields'];
	
	
	$acfs = apply_filters('acf/get_field_groups', array());
	
	if( is_array($acfs) ){ foreach( $acfs as $acf ){
		
		// only add the chosen field groups
		if( !in_array( $acf['id'], $options['field_groups'] ) )
		{
			continue;
		}
		
		
		// load options
		$acf['options'] = apply_filters('acf/field_group/get_options', $acf['id']);
		
		
		// load fields
		$fields = apply_filters('acf/field_group/get_fields', $acf['id']);
		
		
		echo '<div id="acf_' . $acf['id'] . '" class="postbox acf_postbox">';
		echo '<h3 class="hndle"><span>' . $acf['title'] . '</span></h3>';
		echo '<div class="inside">';
		echo '<div class="options" data-layout="' . $acf['options']['layout'] . '" data-show="1"></div>';
							
		do_action('acf/create_fields', $fields, $options['post_id']);
		
		echo '</div></div>';
		
	}}
	
	
	// html after fields
	echo $options['html_after_fields'];
	
	?>
	<!-- Submit -->
	<div class="field">
		<input type="submit" value="<?php echo $options['submit_value']; ?>" />
	</div>
	<!-- / Submit -->

	</div><!-- <div id="poststuff"> -->
	</form>
	<?php
	
}


/*
*  update_field()
*
*  This function will update a value in the database
*
*  @type	function
*  @since	3.1.9
*  @date	29/01/13
*
*  @param	$field_name - the name of the field - 'sub_heading'
*  @param	$value - the value to save in the database
*  @param	$post_id - the post_id of which the value is saved against
*
*  @return
*/

function update_field( $field_key, $value, $post_id = false )
{
	// filter post_id
	$post_id = acf_filter_post_id( $post_id );
	
	
	// vars
	$options = array(
		'load_value' => false,
		'format_value' => false
	);
	
	$field = get_field_object( $field_key, $post_id, $options);
	
	
	if( !is_array($field) )
	{
		$field = array(
			'type' => 'none',
			'name' => $field_key
		);
	}
	
	
	// sub fields? They need formatted data
	if( $field['type'] == 'repeater' )
	{
		$value = acf_convert_field_names_to_keys( $value, $field );
	}
	elseif( $field['type'] == 'flexible_content' )
	{
		if( $field['layouts'] )
		{
			foreach( $field['layouts'] as $layout )
			{
				$value = acf_convert_field_names_to_keys( $value, $layout );
			}
		}
	}
	
	
	// save
	do_action('acf/update_value', $value, $field, $post_id );
	
	
	return true;
	
}


/*
*  delete_field()
*
*  This function will remove a value from the database
*
*  @type	function
*  @since	3.1.9
*  @date	29/01/13
*
*  @param	$field_name - the name of the field - 'sub_heading'
*  @param	$post_id - the post_id of which the value is saved against
*
*  @return
*/

function delete_field( $field_name, $post_id )
{
	do_action('acf/delete_value', $post_id, $field_name );
}


/*--------------------------------------------------------------------------------------
*
*	acf_convert_field_names_to_keys
*
*	@description: Helper for the update_field function
*	@created: 30/09/12
*	@author Elliot Condon
*	@since 3.5.0
*
*-------------------------------------------------------------------------------------*/

function acf_convert_field_names_to_keys( $value, $field )
{
	// only if $field has sub fields
	if( !isset($field['sub_fields']) )
	{
		return $value;
	}
	

	// define sub field keys
	$sub_fields = array();
	if( $field['sub_fields'] )
	{
		foreach( $field['sub_fields'] as $sub_field )
		{
			$sub_fields[ $sub_field['name'] ] = $sub_field;
		}
	}
	
	
	// loop through the values and format the array to use sub field keys
	if( $value )
	{
		foreach( $value as $row_i => $row)
		{
			if( $row )
			{
				foreach( $row as $sub_field_name => $sub_field_value )
				{
					// sub field must exist!
					if( !isset($sub_fields[ $sub_field_name ]) )
					{
						continue;
					}
					
					
					// vars
					$sub_field = $sub_fields[ $sub_field_name ];
					$sub_field_value = acf_convert_field_names_to_keys( $sub_field_value, $sub_field );
					
					
					// set new value
					$value[$row_i][ $sub_field['key'] ] = $sub_field_value;
					
					
					// unset old value
					unset( $value[$row_i][$sub_field_name] );
						
					
				}
				// foreach( $row as $sub_field_name => $sub_field_value )
			}
			// if( $row )
		}
		// foreach( $value as $row_i => $row)
	}
	// if( $value )
	
	
	return $value;

}



/*
*  Depreceated Functions
*
*  @description: 
*  @created: 23/07/12
*/


/*--------------------------------------------------------------------------------------
*
*	reset_the_repeater_field
*
*	@author Elliot Condon
*	@depreciated: 3.3.4 - now use has_sub_field
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function reset_the_repeater_field()
{
	// do nothing
}


/*--------------------------------------------------------------------------------------
*
*	the_repeater_field
*
*	@author Elliot Condon
*	@depreciated: 3.3.4 - now use has_sub_field
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function the_repeater_field($field_name, $post_id = false)
{
	return has_sub_field($field_name, $post_id);
}


/*--------------------------------------------------------------------------------------
*
*	the_flexible_field
*
*	@author Elliot Condon
*	@depreciated: 3.3.4 - now use has_sub_field
*	@since 3.?.?
* 
*-------------------------------------------------------------------------------------*/

function the_flexible_field($field_name, $post_id = false)
{
	return has_sub_field($field_name, $post_id);
}


?>