<?php
class avia_wp_import extends WP_Import
{
	var $preStringOption; 
	var $results;
	var $getOptions;
	var $saveOptions;
	var $termNames;
	
	function saveOptions($option_file, $import_only = false)
	{	

		if($option_file) @include_once($option_file);
		
		switch($import_only)
		{
			case 'options': $dynamic_pages = $dynamic_elements = false; break;
			case 'dynamic_pages': $options = $dynamic_elements = false; break;
			case 'dynamic_elements': $options = $dynamic_pages = false; break;
		}
		
		
		if(!isset($options) && !isset($dynamic_pages) && !isset($dynamic_elements)  ) { return false; }
		
		$options = unserialize(base64_decode($options));
		$dynamic_pages = unserialize(base64_decode($dynamic_pages));
		$dynamic_elements = unserialize(base64_decode($dynamic_elements));
		
		global $avia;
		
		if(is_array($options))
		{
			foreach($avia->option_pages as $page)
			{
				$database_option[$page['parent']] = $this->extract_default_values($options[$page['parent']], $page, $avia->subpages);
			}
		}
		
		if(!empty($database_option))
		{
			update_option($avia->option_prefix, $database_option);
		}
		
		if(!empty($dynamic_pages))
		{
			update_option($avia->option_prefix.'_dynamic_pages', $dynamic_pages);
		}
		
		if(!empty($dynamic_elements))
		{
			update_option($avia->option_prefix.'_dynamic_elements', $dynamic_elements);
		}
		
		if(!empty($widget_settings))
		{
			$widget_settings = unserialize(base64_decode($widget_settings));
			if(!empty($widget_settings))
			{
				foreach($widget_settings as $key => $setting)
				{
					update_option( $key, $setting );
				}
			}
		}
		
		
		
	}
	
	/**
	 *  Extracts the default values from the option_page_data array in case no database savings were done yet
	 *  The functions calls itself recursive with a subset of elements if groups are encountered within that array
	 */
	public function extract_default_values($elements, $page, $subpages)
	{
	
		$values = array();
		foreach($elements as $element)
		{
				if($element['type'] == 'group')
				{	
					$iterations =  count($element['std']);
					
					for($i = 0; $i<$iterations; $i++)
					{
						$values[$element['id']][$i] = $this->extract_default_values($element['std'][$i], $page, $subpages);
					}
				}
				else if(isset($element['id']))
				{
					if(!isset($element['std'])) $element['std'] = "";
					
					if($element['type'] == 'select' && !is_array($element['subtype']))
					{	
						if(!isset($element['taxonomy'])) $element['taxonomy'] = 'category';
						$values[$element['id']] = $this->getSelectValues($element['subtype'], $element['std'], $element['taxonomy']);
					}
					else
					{
						$values[$element['id']] = $element['std'];
					}
				}
			
		}
		
		return $values;
	}
	
	function getSelectValues($type, $name, $taxonomy)
	{
		switch ($type)
		{
			case 'page':
			case 'post':	
				$the_post = get_page_by_title( $name, 'OBJECT', $type );
				if(isset($the_post->ID)) return $the_post->ID;
			break;
			
			case 'cat':	
			
				if(!empty($name))
				{
					$return = array();
					
					foreach($name as $cat_name)
					{	
						if($cat_name)
						{	
							if(!$taxonomy) $taxonomy = 'category';
							$the_category = get_term_by('name', $cat_name, $taxonomy);
						
							if($the_category) $return[] = $the_category->term_id;
						}
					}
				
				if(!empty($return))
				{
					if(!isset($return[1]))
					{
						 $return = $return[0];
					}
					else
					{
						$return = implode(',',$return);
					}
				}
				return $return;
			}
			
		break;
		}
	}
	
	function set_menus()
	{
		global $avia_config;
		//get all registered menu locations
		$locations   = get_theme_mod('nav_menu_locations');

		//get all created menus
		$avia_menus  = wp_get_nav_menus();
		
		
		if(!empty($avia_menus) && !empty($avia_config['nav_menus']))
		{
			$avia_navs = array();
			foreach($avia_config['nav_menus'] as $key => $nav_menu)
			{
				if(isset($nav_menu['html']))
				{
					$avia_navs[$key] = $nav_menu['html'];
				}
				else
				{
					$avia_navs[$key] = $nav_menu;
				}
			}
		
			foreach($avia_menus as $avia_menu)
			{
				//check if we got a menu that corresponds to the Menu name array ($avia_config['nav_menus']) we have set in functions.php
				if(is_object($avia_menu) && in_array($avia_menu->name, $avia_navs) )
				{
					$key = array_search($avia_menu->name, $avia_navs);
					if($key)
					{
						//if we have found a menu with the correct menu name apply the id to the menu location
						$locations[$key] = $avia_menu->term_id;
					}
				}
			}
		}
		
		
		
		//update the theme
		set_theme_mod( 'nav_menu_locations', $locations);
	}
	
	
	function process_menu_item( $item ) {

		// skip draft, orphaned menu items
		if ( 'draft' == $item['status'] )
			return;

		$menu_slug = false;
		if ( isset($item['terms']) ) {
			// loop through terms, assume first nav_menu term is correct menu
			foreach ( $item['terms'] as $term ) {
				if ( 'nav_menu' == $term['domain'] ) {
					$menu_slug = $term['slug'];
					break;
				}
			}
		}

		// no nav_menu term associated with this menu item
		if ( ! $menu_slug ) {
			_e( 'Menu item skipped due to missing menu slug', 'wordpress-importer' );
			echo '<br />';
			return;
		}

		$menu_id = term_exists( $menu_slug, 'nav_menu' );
		if ( ! $menu_id ) {
			printf( __( 'Menu item skipped due to invalid menu slug: %s', 'wordpress-importer' ), esc_html( $menu_slug ) );
			echo '<br />';
			return;
		} else {
			$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
		}

		foreach ( $item['postmeta'] as $meta )
			$$meta['key'] = $meta['value'];

		if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[intval($_menu_item_object_id)] ) ) {
			$_menu_item_object_id = $this->processed_terms[intval($_menu_item_object_id)];
		} else if ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[intval($_menu_item_object_id)] ) ) {
			$_menu_item_object_id = $this->processed_posts[intval($_menu_item_object_id)];
		} else if ( 'custom' != $_menu_item_type ) {
			// associated object is missing or not imported yet, we'll retry later
			$this->missing_menu_items[] = $item;
			return;
		}

		if ( isset( $this->processed_menu_items[intval($_menu_item_menu_item_parent)] ) ) {
			$_menu_item_menu_item_parent = $this->processed_menu_items[intval($_menu_item_menu_item_parent)];
		} else if ( $_menu_item_menu_item_parent ) {
			$this->menu_item_orphans[intval($item['post_id'])] = (int) $_menu_item_menu_item_parent;
			$_menu_item_menu_item_parent = 0;
		}

		// wp_update_nav_menu_item expects CSS classes as a space separated string
		$_menu_item_classes = maybe_unserialize( $_menu_item_classes );
		if ( is_array( $_menu_item_classes ) )
			$_menu_item_classes = implode( ' ', $_menu_item_classes );

		$args = array(
			'menu-item-object-id' => $_menu_item_object_id,
			'menu-item-object' => $_menu_item_object,
			'menu-item-parent-id' => $_menu_item_menu_item_parent,
			'menu-item-position' => intval( $item['menu_order'] ),
			'menu-item-type' => $_menu_item_type,
			'menu-item-title' => $item['post_title'],
			'menu-item-url' => $_menu_item_url,
			'menu-item-description' => $item['post_content'],
			'menu-item-attr-title' => $item['post_excerpt'],
			'menu-item-target' => $_menu_item_target,
			'menu-item-classes' => $_menu_item_classes,
			'menu-item-xfn' => $_menu_item_xfn,
			'menu-item-status' => $item['status']
		);
		
		
		

		$id = wp_update_nav_menu_item( $menu_id, 0, $args );
		if ( $id && ! is_wp_error( $id ) )
			$this->processed_menu_items[intval($item['post_id'])] = (int) $id;
		
		/*kriesi mod: necessary to add custom post meta to the import*/
		if ( $id && ! is_wp_error( $id ) )
		{
			foreach($item['postmeta'] as $itemkey => $meta)
			{
				$key = str_replace('_', '-', ltrim($meta['key'], "_"));
				
				/*do a check: only add keys that to not exist - parent menu item is a special case that must be checked as well*/
				if( !array_key_exists($key, $args) && $key != "menu-item-menu-item-parent")
				{
					if(!empty($meta['value']))
					{
						update_post_meta($id, $meta['key'], $meta['value']);
					}
				}
			}
		}
		/*end mod*/
		
		
	}
	
	
	
}




