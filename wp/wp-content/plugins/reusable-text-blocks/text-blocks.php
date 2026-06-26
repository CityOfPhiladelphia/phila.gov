<?php
/*
Plugin Name: Text Blocks
Plugin URI: http://halgatewood.com/text-blocks
Description: Blocks of content that can be used throughout the site in theme templates and widgets.
Author: Hal Gatewood
Author URI: http://www.halgatewood.com
Text Domain: text-blocks
Domain Path: /languages
Version: 1.5.3
*/

/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// ADDS
add_action( 'plugins_loaded', 'text_block_setup' );
function text_block_setup()
{
	add_action( 'init', 'create_text_block_type' );
	add_action( 'admin_head', 'textblocks_css' );
	add_filter( 'manage_edit-text-blocks_columns', 'textblocks_columns' );
	add_action( 'manage_text-blocks_posts_custom_column', 'textblocks_add_columns' );
	add_action( 'widgets_init', 'text_block_register_widget' );
	add_shortcode( 'text-blocks', 'text_blocks_shortcode');
	add_action( 'add_meta_boxes', 'text_blocks_create_metaboxes' );
	
	if( is_admin() )
	{
		add_action( 'media_buttons', 'text_blocks_media_button', 11 );
		add_action( 'admin_footer', 'text_blocks_admin_footer_for_thickbox' );
	}
	
}

function text_block_register_widget()
{
	register_widget("TextBlocksWidget");
}

// INIT:
// LANGUAGES
// CUSTOM POST TYPE
function create_text_block_type()
{
	
	// LOAD TEXT DOMAIN
	load_plugin_textdomain( 'text-blocks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
  	$labels = array(
				    'name' 					=> __('Text Blocks', 'text-blocks'),
				    'singular_name' 		=> __('Text Block', 'text-blocks'),
				    'add_new' 				=> __('Add New', 'text-blocks'),
				    'add_new_item' 			=> __('Add New Block', 'text-blocks'),
				    'edit_item' 			=> __('Edit Text Block', 'text-blocks'),
				    'new_item' 				=> __('New Block', 'text-blocks'),
				    'all_items' 			=> __('All Text Blocks', 'text-blocks'),
				    'view_item' 			=> __('View Block', 'text-blocks'),
				    'search_items' 			=> __('Search Text Blocks', 'text-blocks'),
				    'not_found' 			=> __('No blocks found', 'text-blocks'),
				    'not_found_in_trash' 	=> __('No blocks found in Trash', 'text-blocks'),
				    'parent_item_colon' 	=> '',
				    'menu_name' 			=> __('Text Blocks', 'text-blocks')
  					);

	$args = array(
					'labels' 				=> $labels,
					'public' 				=> false,
					'publicly_queryable' 	=> true,
					'show_ui' 				=> true,
					'show_in_menu' 			=> true,
					'query_var' 			=> true,
					'rewrite' 				=> array('with_front' => false),
					'capability_type' 		=> 'post',
					'has_archive' 			=> false,
					'hierarchical' 			=> false,
					'menu_position' 		=> 26.4,
					'exclude_from_search' 	=> true,
					'supports' 				=> array( 'title', 'editor', 'thumbnail', 'revisions' )
					);

	register_post_type( 'text-blocks', apply_filters('text_blocks_post_type_args', $args ) );
}


// ADMIN: WIDGET ICONS
function textblocks_css()
{
	global $wp_version;

	if($wp_version >= 3.8)
	{

	echo '
		<style>
			#adminmenu #menu-posts-text-blocks div.wp-menu-image:before { content: "\f180"; }
		</style>
	';
	}
	else
	{


	$icon 		= plugins_url( 'reusable-text-blocks' ) . "/menu-icon.png";
	$icon_32 	= plugins_url( 'reusable-text-blocks' ) . "/icon-32.png";

	echo "
		<style>
			#menu-posts-text-blocks .wp-menu-image { background: url({$icon}) no-repeat 6px -26px !important; }
			#menu-posts-text-blocks.wp-has-current-submenu .wp-menu-image { background-position:6px 6px!important; }
			.icon32-posts-text-blocks { background: url({$icon_32}) no-repeat 0 0 !important; }
		</style>
	";
	}
}


// CUSTOM COLUMNS
function textblocks_columns( $columns )
{
	return array(
		'cb'       	=> '<input type="checkbox" />',
		'title'    	=> __( 'Title', 'text-blocks' ),
		'shortcode'	=> __( 'Shortcode', 'text-blocks' ),
		'text'     	=> __( 'Text', 'text-blocks' )
	);
}


// CUSTOM COLUMN DATA
function textblocks_add_columns( $column )
{
	global $post;
	$edit_link = get_edit_post_link( $post->ID );

	if ( $column == 'text' ) echo strip_tags($post->post_content);
 	if(	$column == "shortcode")
 	{
 		echo "
 				[text-blocks id=\"{$post->ID}\"]<br />
 				[text-blocks id=\"{$post->post_name}\"]<br /><hr />
 				[text-blocks id=\"{$post->ID}\" plain=\"1\"]<br />
 				[text-blocks id=\"{$post->post_name}\" plain=\"1\"]
 			";
 	}
}


// METABOXES
function text_blocks_create_metaboxes()
{
	// IF ON EDIT SHOW THE SHORTCODE
	if(isset($_GET['action']) AND $_GET['action'] == "edit")
	{
		add_meta_box( 'text_blocks_shortcode_metabox', __('Text Block Shortcode', 'text-blocks'), 'text_blocks_shortcode_metabox', 'text-blocks', 'normal', 'default' );
	}
}


// SHORTCODE DISPLAY HELPER
function text_blocks_shortcode_metabox()
{
	global $post;

	echo "<p><b>" . __('Like WordPress Content:', 'text-blocks') . "</b><br />[text-blocks id=\"{$post->ID}\"] &nbsp; or &nbsp; [text-blocks id=\"{$post->post_name}\"]</p>";

	echo "<p><b>" . __('No extra markup:', 'text-blocks') . "</b><br />[text-blocks id=\"{$post->ID}\" plain=\"1\"] &nbsp; or &nbsp; [text-blocks id=\"{$post->post_name}\" plain=\"1\"]</p>";

	echo "<p><b>" . __('In Theme Template:', 'text-blocks') . "</b><br />&lt;?php if(function_exists('show_text_block')) { echo show_text_block('{$post->post_name}', true); } ?&gt;</p>";

	echo '<span class="description">' . __('Put one of the above codes wherever you want the text block to appear', 'text-blocks') . '</span>';
}



// TEXT BLOCK WIDGET
class TextBlocksWidget extends WP_Widget
{
	function __construct() { parent::__construct(false, $name = 'Text Blocks Widget'); }

    function widget($args, $instance)
    {
        extract( $args );
        $title 		= isset($instance['title']) ? $instance['title'] : false;
        $id 		= (int) $instance['id'];
        $block 		= get_post( $id );
        $wpautop	= isset($instance['wpautop']) ? $instance['wpautop'] : false;
        $hide_title = isset($instance['hide_title']) ? $instance['hide_title'] : false;

        $block_content = $block->post_content;
        if($wpautop == "on") { $block_content = wpautop($block_content); }
        ?>
          <?php echo $before_widget; ?>
              <?php if ( $title && !$hide_title ) echo $before_title . $title . $after_title; ?>
				<div class="text-block <?php echo $block->post_name ?>"><?php echo apply_filters( 'text_blocks_widget_html', $block_content); ?></div>
          <?php echo $after_widget; ?>
        <?php
    }

    function update($new_instance, $old_instance)
    {
		$instance = $old_instance;
		$instance['title'] 			= strip_tags($new_instance['title']);
		$instance['id'] 			= strip_tags($new_instance['id']);
		$instance['wpautop'] 		= strip_tags($new_instance['wpautop']);
		$instance['hide_title'] 	= strip_tags($new_instance['hide_title']);
        return $instance;
    }

    function form($instance)
    {
        $title 				= isset($instance['title']) ? esc_attr($instance['title']) : "";
        $selected_block 	= isset($instance['id']) ? esc_attr($instance['id']) : 0;
        $wpautop 			= isset($instance['wpautop']) ? esc_attr($instance['wpautop']) : 0;
		$hide_title 		= isset($instance['hide_title']) ? esc_attr($instance['hide_title']) : 0;

        $blocks = get_posts( array('post_type' => 'text-blocks', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ));
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'text-blocks'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		<p>
          <label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('Text Block:', 'text-blocks'); ?></label>
          <select class="widefat" id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>">
          	<?php foreach($blocks as $block) { ?>
          	<option value="<?php echo $block->ID; ?>"<?php if($selected_block == $block->ID) echo " selected=\"selected\""; ?>><?php echo $block->post_title; ?></option>
          	<?php } ?>
          </select>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id('wpautop'); ?>" name="<?php echo $this->get_field_name('wpautop'); ?>" type="checkbox"<?php if($wpautop == "on") echo " checked='checked'"; ?>>&nbsp;
			<label for="<?php echo $this->get_field_id('wpautop'); ?>"><?php _e('Automatically add paragraphs', 'text-blocks'); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('hide_title'); ?>" name="<?php echo $this->get_field_name('hide_title'); ?>" type="checkbox"<?php if($hide_title == "on") echo " checked='checked'"; ?>>&nbsp;
			<label for="<?php echo $this->get_field_id('hide_title'); ?>"><?php _e('Hide widget title on front end', 'text-blocks'); ?></label>
		</p>
        <?php
    }
}


// SHOW TEXT BLOCK
function show_text_block($id, $plain = false, $atts = false)
{
	$id = apply_filters( 'text_blocks_show_text_block_id', $id );


	// IF ID IS NOT NUMERIC CHECK FOR SLUG
	if(!is_numeric($id))
	{
		$page = get_page_by_path( $id, null, 'text-blocks' );
		$id = apply_filters( 'wpml_object_id', $page->ID, 'text-blocks' );
	}

	if( !$id ) return false;
	if ( get_post_status( $id ) != 'publish' ) return false;
	
	// LOOK FOR TEMPLATE IN THEME
	$template = isset($atts['template']) ? $atts['template'] : $id;
	$template = locate_template( apply_filters( 'text_blocks_template_location', array( "text-blocks/text-blocks-{$template}.php", "text-blocks-{$template}.php" ) ) );
	
	
	// LOAD TEMPLATE IF FOUND
	if( $template )
	{
		$content = get_post_field( 'post_content', $id );
		if( !isset($atts['stop_detect'])) $content = text_blocks_att_swap( $content, $atts );
		
		ob_start();
		include( $template );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

	// LOAD PLAIN CONTENT
	if( $plain )
	{
		$content = get_post_field( 'post_content', $id );
		if( !isset($atts['stop_detect'])) $content = text_blocks_att_swap( $content, $atts );
		return apply_filters( 'text_blocks_shortcode_html', $content, $atts );
	}


	// APPLY 'the_content' FILTER TO BLEND WITH EVERYTHING ELSE
	$content = apply_filters( 'the_content', get_post_field( 'post_content', $id), $atts );
	if( !isset($atts['stop_detect'])) $content = text_blocks_att_swap( $content, $atts );
	return apply_filters( 'text_blocks_shortcode_html', $content, $atts, $id );	
}


// SHORT CODE
function text_blocks_shortcode($atts)
{
	$id = isset($atts['id']) ? $atts['id'] : false;
	$plain = isset($atts['plain']) ? 1 : 0;
	if($id) { return show_text_block( $id, $plain, $atts ); }
	else { return false; }
}


// DETECT ATTS FUNCTION
function text_blocks_att_swap( $content, $atts )
{
	if( !is_array($atts) ) $atts = (array) $atts;
	
	$dont_detect = apply_filters('text_blocks_dont_detect_words', array('template', 'id', 'detect') );
	
	$replace_front 	= apply_filters('text_blocks_replace_front', '{{');
	$replace_back 	= apply_filters('text_blocks_replace_back', '}}');

	foreach( $atts as $slug => $att )
	{
		if( in_array($att, $dont_detect) ) continue;
		$content = str_replace($replace_front . $slug . $replace_back, $atts[$slug], $content);
	}
	
	return $content;
}


// MEDIA BUTTON
function text_blocks_media_button() 
{
	global $pagenow, $typenow, $wp_version;
	$output = '';
	if ( version_compare( $wp_version, '3.5', '>=' ) AND in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) && $typenow != 'text-blocks' ) 
	{
		$img = '<style>#text-blocks-media-button::before { font: 400 18px/1 dashicons; content: \'\f180\'; }</style><span class="wp-media-buttons-icon" id="text-blocks-media-button"></span>';
		$output = '<a href="#TB_inline?width=640&inlineId=add-text-blocks" class="thickbox button text-blocks-thickbox" title="' .  __( 'Add Block', 'text-blocks'  ) . '" style="padding-left: .4em;"> ' . $img . __( 'Add Block', 'text-blocks'  ) . '</a>';
	}
	echo $output;
}


// MEDIA BUTTON FUNCTIONALITY
function text_blocks_admin_footer_for_thickbox() 
{
	global $pagenow, $typenow, $wp_version;

	// Only run in post/page creation and edit screens
	if ( version_compare( $wp_version, '3.5', '>=' ) AND in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) && $typenow != 'text-blocks' ) { ?>
		
		<script type="text/javascript">
            function insertReusableTextBlock() 
            {
            	var id = jQuery('#text-blocks-select-box').val();
                if ('' === id)
                {
                    alert('<?php _e( "You must choose a block", "text-blocks" ); ?>');
                    return;
                }
                
                var slug = jQuery('#text-blocks-select-box option:selected').data('slug');
                
                window.send_to_editor('[text-blocks id="' + id + '" slug="' + slug + '"]');
            }
		</script>

		<div id="add-text-blocks" style="display: none;">
			<div class="wrap" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
				<?php
				
				$blocks = get_posts( array('post_type' => 'text-blocks', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ));
				
				if( $blocks ) { ?>
					<select id="text-blocks-select-box" style="clear: both; display: block; margin-bottom: 1em;">
						<option value=""><?php _e('Choose a Text Block', 'text-blocks'); ?></option>
						<?php
							foreach ( $blocks as $block ) 
							{
								echo '<option value="' . $block->ID . '" data-slug="' . $block->post_name . '">' . $block->post_title . '</option>';
							}
						?>
					</select>
				<?php } else { echo __('No text blocks have been created yet. Please create one first and then you will be able to select it here.', 'text-blocks'); } ?>

				<p class="submit">
					<input type="button" id="text-blocks-insert-download" class="button-primary" value="<?php echo __( 'Insert Block', 'text-blocks' ); ?>" onclick="insertReusableTextBlock();" />
					<a id="text-blocks-cancel-add" class="button-secondary" onclick="tb_remove();" title="<?php _e( 'Cancel', 'text-blocks' ); ?>"><?php _e( 'Cancel', 'text-blocks' ); ?></a>
				</p>
			</div>
		</div>
	<?php
	}
}
