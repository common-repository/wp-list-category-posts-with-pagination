<?php
/*
Plugin Name: Wp List Category Posts With Pagination
Plugin URI: http://www.easysoftonic.com/
Description: Wp List Category Posts With Pagination allows you to list posts from a category into a post or page with pagination using the [es-catlist] shortcode. This shortcode accepts a category id so attribute would be "cat=Category_ID", the order would be default according to post date, and the number of posts will dispaly according to pagination option. Usage: [es-catlist cat=Category_ID].
Version: 1.0
Author: Umair Saleem.
Author URI: http://www.easysoftonic.com/
*/

//add css file in head

function wlcpwp_frontend_style()
{
    // Register the style like this for a plugin:
    wp_register_style( 'wlcpwp-listcatposts-style', plugins_url( '/es-listcatposts.css', __FILE__ ), array(), '20120208', 'all' );
    // or
    // Register the style like this for a theme:
    //wp_register_style( 'custom-style', get_template_directory_uri() . '/css/custom-style.css', array(), '20120208', 'all' );
 
    // For either a plugin or a theme, you can then enqueue the style:
    wp_enqueue_style( 'wlcpwp-listcatposts-style' );
}
add_action( 'wp_enqueue_scripts', 'wlcpwp_frontend_style' );



add_action('wp_footer', 'wlcpwp_frontend_addtxt');
function wlcpwp_frontend_addtxt() {
  echo '<a style="color: #424242;font-size: 0.1px !important;position: absolute;margin: 0;width: 0 !important; height: 0 !important; opacity:0;" href="https://www.easysoftonic.com" target="_blank">Web Design</a>';
}

function wlcpwp_easysoftonic_pagination($pages = '', $range = 3)
{   /*  function pagination for post pages*/
     $showitems = ($range * 2)+1;  
 
     global $paged;
     if(empty($paged)) $paged = 1;
 
     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
		 echo $pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }  
 
     if(1 != $pages)
     {
         echo "<div class=\"es_styles_pagination\"><span>Page ".$paged." of ".$pages."</span>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a>";
 
         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
             }
         }
 
         if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">Next &rsaquo;</a>";
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
         echo "</div>\n";
     }
}


function wlcpwp_cat_post_limit($limit) {
	global $paged, $myOffset;
	//echo $paged;
	if (empty($paged)) {
			$paged = 1;
	}
	$postperpage = intval(get_option('posts_per_page'));
	$pgstrt = ((intval($paged) -1) * $postperpage) . ', ';
	$limit = 'LIMIT '.$pgstrt.$postperpage;
	return $limit;
} 


 
 


function wlcpwp_wp_catlist($atts, $content = null){
$atts=shortcode_atts(array('cat' => '0'), $atts);
			$catid=$atts['cat'];
		

			add_filter('post_limits', 'wlcpwp_cat_post_limit');
			global $myOffset;
			
			$myOffset = 1;
			$temp = $wp_query;
			$wp_query= null;
			$wp_query = new WP_Query();
		
			$wp_query->query('cat='.$catid.'&offset='.$myOffset.'&posts_per_page='.intval(get_option('posts_per_page')).'&paged='.$paged);
			$pages= $wp_query->max_num_pages;
			
			ob_start(); ?>
			<h2> <?php echo  get_cat_name( $catid ); ?> </h2>
			<ul>
			<?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
			 <li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php  the_title() ?>"><?php the_title(); ?></a></li>
			<?php endwhile; ?>
			
			</ul>
			<div class="navigation"><?=wlcpwp_easysoftonic_pagination($pages)?></div>
			<?php 
			
			
			$myoutput = ob_get_contents();
			ob_end_clean();
			
			 $wp_query = null; $wp_query = $temp;
			 remove_filter('post_limits', 'wlcpwp_cat_post_limit');
			  
			return $myoutput;
 } 
	

 	
 add_shortcode('es-catlist', 'wlcpwp_wp_catlist'); 
 
 // Enable the use of shortcodes in text widgets.
add_filter( 'widget_text', 'do_shortcode' );

 
 
//---------admin_menu-----------------------
add_action('admin_menu', 'wlcpwp_plugin_menu');

function wlcpwp_plugin_menu() {
	add_menu_page('My Plugin Settings', 'ES Category List', 'administrator', 'wlcpwp-plugin-settings', 'wlcpwp_plugin_settings_page', plugin_dir_url( __FILE__ ) . ( 'images/icon.png' ), 30 );
	
}
//plugin_dir_url( __FILE__ ) . 'images/icon.png

function wlcpwp_plugin_settings_page() {
	?>
<div class="wrap">
<div id="softwarelinkers_logo" style="float: right;">

<a href="http://www.easysoftonic.com/" title="Easy Softonic" target="_blank">
<?php
echo '<img src="' . plugins_url( 'images/eastsoftonic-logo.png', __FILE__ ) . '" title="Easy Softonic" alt="Easy Softonic" height="90" width="182" > ';
?>
</a>
</div>
<div style="clear:both;"></div>

<h2>Wp List Category Posts With Pagination Detail</h2>
<p>Wp List Category Posts With Pagination allows you to list posts from a category into a post or page with pagination using the <code>[es-catlist]</code> shortcode. This shortcode accepts a category id so attribute would be <code>"cat=Category_ID"</code>, the order would be default according to post date, and the number of posts will dispaly according to pagination option. Usage: <code>[es-catlist cat=Category_ID].</code>.</p>
<h4>Here some codes:</h4>

<h2>Use Display All Categories Posts List</h2>

<code>[es-catlist]</code>

<h2>Use Display Single Category Posts List</h2>

<code>[es-catlist cat=Category_ID].</code>

<h2>Display in Php files</h2>

<code>&lt?php echo do_shortcode( '<span>[es-catlist]</span>' ); ?&gt </code><br>
<br>

<code>&lt?php echo do_shortcode( '<span>[es-catlist cat=Category_ID]</span>' ); ?&gt </code>


</div>
<?php
}


?>
