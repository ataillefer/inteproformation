<?php
	// Loading child theme textdomain
	load_child_theme_textdomain( CURRENT_THEME, CHILD_DIR . '/languages' );
	
	require_once('custom-js.php');

	add_filter( 'cherry_stickmenu_selector', 'cherry_change_selector' );
	function cherry_change_selector($selector) {
		$selector = '.header .tail-top';
		return $selector;
	}

	add_filter( 'cherry_slider_params', 'child_slider_params' );
    function child_slider_params( $params ) {
        $params['minHeight'] = '"90px"';
        $params['height'] = '"28.1025641025641%"';
    return $params;
    }

    add_action( 'after_setup_theme', 'after_cherry_child_setup' );
	function after_cherry_child_setup() {
		$nfu_options = get_option( 'nsu_form' );
		if ( !$nfu_options ) {
			$nfu_options_array = array();
			$nfu_options_array['email_label']         = 'Subscribe to my newsletter';
			$nfu_options_array['email_default_value'] = 'your email address';
			$nfu_options_array['submit_button']       = 'submit';
			update_option( 'nsu_form', $nfu_options_array );
		}
	}

    /**
	 * Service Box
	 *
	 */
	if (!function_exists('service_box_shortcode')) {

		function service_box_shortcode($atts, $content = null) { 
			extract(shortcode_atts(
				array(
					'title'        => '',
					'subtitle'     => '',
					'icon'         => '',
					'text'         => '',
					'btn_text'     => __('Read more', CHERRY_PLUGIN_DOMAIN),
					'btn_link'     => '',
					'btn_size'     => '',
					'target'       => '',
					'custom_class' => ''
			), $atts));
			
			$output =  '<div class="service-box '.$custom_class.'">';
		
			if($icon != 'no'){
				if ($btn_link!="") {
					$output .=  '<a href="'.$btn_link.'" title="'.$btn_text.'" target="'.$target.'">';
				}
				$icon_url = CHERRY_PLUGIN_URL . 'includes/images/' . strtolower($icon) . '.png' ;
				if( defined ('CHILD_DIR') ) {
					if(file_exists(CHILD_DIR.'/images/'.strtolower($icon).'.png')){
						$icon_url = CHILD_URL.'/images/'.strtolower($icon).'.png';
					}
				}
				if ($icon == 'icon1') {
					$output .= '<figure class="icon"><i class="icon-edit"></i></figure>';	
				} elseif ($icon == 'icon2') {
					$output .= '<figure class="icon"><i class="icon-book"></i></figure>';
				} elseif ($icon == 'icon3') {
					$output .= '<figure class="icon"><i class="icon-group"></i></figure>';
				} elseif ($icon == 'icon4') {
					$output .= '<figure class="icon"><i class="icon-star-empty"></i></figure>';
				} elseif ($icon == 'icon5') {
					$output .= '<figure class="icon"><i class="icon-trophy"></i></figure>';
				} elseif ($icon == 'icon6') {
					$output .= '<figure class="icon"><i class="icon-gift"></i></figure>';
				} else {
					$output .= '<figure class="icon"><img src="'.$icon_url.'" alt="" /></figure>';
				}
				if ($btn_link!="") {
					$output .=  '</a>';
				}				
			}

			$output .= '<div class="service-box_body">';

			if ($title!="") {
				$output .= '<h2 class="title">';
				$output .= $title;
				$output .= '</h2>';
			}
			if ($subtitle!="") {
				$output .= '<h5 class="sub-title">';
				$output .= $subtitle;
				$output .= '</h5>';
			}
			if ($text!="") {
				$output .= '<div class="service-box_txt">';
				$output .= $text;
				$output .= '</div>';
			}
			if ($btn_link!="") {
				$output .=  '<div class="btn-align"><a href="'.$btn_link.'" title="'.$btn_text.'" class="btn btn-inverse btn-'.$btn_size.' btn-primary " target="'.$target.'">';
				$output .= $btn_text;
				$output .= '</a></div>';
			}
			$output .= '</div>';
			$output .= '</div><!-- /Service Box -->';
			return $output;
		}
		add_shortcode('service_box', 'service_box_shortcode');

	}

	//Recent Posts
	if (!function_exists('shortcode_recent_posts')) {

		function shortcode_recent_posts($atts, $content = null) {
			extract(shortcode_atts(array(
					'type'             => 'post',
					'category'         => '',
					'custom_category'  => '',
					'tag'              => '',
					'post_format'      => 'standard',
					'num'              => '5',
					'meta'             => 'true',
					'thumb'            => 'true',
					'thumb_width'      => '120',
					'thumb_height'     => '120',
					'more_text_single' => '',
					'excerpt_count'    => '0',
					'custom_class'     => ''
			), $atts));

			$output = '<ul class="recent-posts '.$custom_class.' unstyled">';

			global $post;
			global $my_string_limit_words;
			$item_counter = 0;
			// WPML filter
			$suppress_filters = get_option('suppress_filters');

			if($post_format == 'standard') {

				$args = array(
							'post_type'         => $type,
							'category_name'     => $category,
							'tag'               => $tag,
							$type . '_category' => $custom_category,
							'numberposts'       => $num,
							'orderby'           => 'post_date',
							'order'             => 'DESC',
							'tax_query'         => array(
							'relation'          => 'AND',
								array(
									'taxonomy' => 'post_format',
									'field'    => 'slug',
									'terms'    => array('post-format-aside', 'post-format-gallery', 'post-format-link', 'post-format-image', 'post-format-quote', 'post-format-audio', 'post-format-video'),
									'operator' => 'NOT IN'
								)
							),
							'suppress_filters' => $suppress_filters
						);

			} else {

				$args = array(
					'post_type'         => $type,
					'category_name'     => $category,
					'tag'               => $tag,
					$type . '_category' => $custom_category,
					'numberposts'       => $num,
					'orderby'           => 'post_date',
					'order'             => 'DESC',
					'tax_query'         => array(
					'relation'          => 'AND',
						array(
							'taxonomy' => 'post_format',
							'field'    => 'slug',
							'terms'    => array('post-format-' . $post_format)
						)
					),
					'suppress_filters' => $suppress_filters
				);
			}

			$latest = get_posts($args);

			foreach($latest as $k => $post) {
					//Check if WPML is activated
					if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
						global $sitepress;

						$post_lang = $sitepress->get_language_for_element($post->ID, 'post_' . $type);
						$curr_lang = $sitepress->get_current_language();
						// Unset not translated posts
						if ( $post_lang != $curr_lang ) {
							unset( $latest[$k] );
						}
						// Post ID is different in a second language Solution
						if ( function_exists( 'icl_object_id' ) ) {
							$post = get_post( icl_object_id( $post->ID, $type, true ) );
						}
					}
					setup_postdata($post);
					$excerpt        = get_the_excerpt();
					$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
					$url            = $attachment_url['0'];
					$image          = aq_resize($url, $thumb_width, $thumb_height, true);
					$subtitle  		= get_post_meta($post->ID, 'subtitle', true);

					$post_classes = get_post_class();
					foreach ($post_classes as $key => $value) {
						$pos = strripos($value, 'tag-');
						if ($pos !== false) {
							unset($post_classes[$key]);
						}
					}
					$post_classes = implode(' ', $post_classes);

					$output .= '<li class="recent-posts_li ' . $post_classes . '  list-item-' . $item_counter . '">';

					//Aside
					if($post_format == "aside") {

						$output .= the_content($post->ID);

					} elseif ($post_format == "link") {

						$url =  get_post_meta(get_the_ID(), 'tz_link_url', true);

						$output .= '<a target="_blank" href="'. $url . '">';
						$output .= get_the_title($post->ID);
						$output .= '</a>';

					//Quote
					} elseif ($post_format == "quote") {

						$quote =  get_post_meta(get_the_ID(), 'tz_quote', true);

						$output .= '<div class="quote-wrap clearfix">';

								$output .= '<blockquote>';
									$output .= $quote;
								$output .= '</blockquote>';

						$output .= '</div>';

					//Image
					} elseif ($post_format == "image") {

					if (has_post_thumbnail() ) :

						// $lightbox = get_post_meta(get_the_ID(), 'tz_image_lightbox', TRUE);

						$src      = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), array( '9999','9999' ), false, '' );

						$thumb    = get_post_thumbnail_id();
						$img_url  = wp_get_attachment_url( $thumb,'full'); //get img URL
						$image    = aq_resize( $img_url, 200, 120, true ); //resize & crop img


						$output .= '<figure class="thumbnail featured-thumbnail large">';
							$output .= '<a class="image-wrap" rel="prettyPhoto" title="' . get_the_title($post->ID) . '" href="' . $src[0] . '">';
							$output .= '<img src="' . $image . '" alt="' . get_the_title($post->ID) .'" />';
							$output .= '<span class="zoom-icon"></span></a>';
						$output .= '</figure>';

					endif;


					//Audio
					} elseif ($post_format == "audio") {

						$template_url = get_template_directory_uri();
						$id           = $post->ID;

						// get audio attribute
						$audio_title  = get_post_meta(get_the_ID(), 'tz_audio_title', true);
						$audio_artist = get_post_meta(get_the_ID(), 'tz_audio_artist', true);
						$audio_format = get_post_meta(get_the_ID(), 'tz_audio_format', true);
						$audio_url    = get_post_meta(get_the_ID(), 'tz_audio_url', true);

						$content_url = content_url();
						$content_str = 'wp-content';

						$pos    = strpos($audio_url, $content_str);
						if ($pos === false) {
							$file = $audio_url;
						} else {
							$audio_new = substr($audio_url, $pos+strlen($content_str), strlen($audio_url) - $pos);
							$file      = $content_url.$audio_new;
						}

						$output .= '<script type="text/javascript">
							jQuery(document).ready(function(){
								var myPlaylist_'. $id.'  = new jPlayerPlaylist({
								jPlayer: "#jquery_jplayer_'. $id .'",
								cssSelectorAncestor: "#jp_container_'. $id .'"
								}, [
								{
									title:"'. $audio_title .'",
									artist:"'. $audio_artist .'",
									'. $audio_format .' : "'. stripslashes(htmlspecialchars_decode($file)) .'"}
								], {
									playlistOptions: {enableRemoveControls: false},
									ready: function () {jQuery(this).jPlayer("setMedia", {'. $audio_format .' : "'. stripslashes(htmlspecialchars_decode($file)) .'", poster: "'. $image .'"});
								},
								swfPath: "'. $template_url .'/flash",
								supplied: "'. $audio_format .', all",
								wmode:"window"
								});
							});
							</script>';

						$output .= '<div id="jquery_jplayer_'.$id.'" class="jp-jplayer"></div>
									<div id="jp_container_'.$id.'" class="jp-audio">
										<div class="jp-type-single">
											<div class="jp-gui">
												<div class="jp-interface">
													<div class="jp-progress">
														<div class="jp-seek-bar">
															<div class="jp-play-bar"></div>
														</div>
													</div>
													<div class="jp-duration"></div>
													<div class="jp-time-sep"></div>
													<div class="jp-current-time"></div>
													<div class="jp-controls-holder">
														<ul class="jp-controls">
															<li><a href="javascript:;" class="jp-previous" tabindex="1" title="'.__('Previous', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Previous', CHERRY_PLUGIN_DOMAIN).'</span></a></li>
															<li><a href="javascript:;" class="jp-play" tabindex="1" title="'.__('Play', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Play', CHERRY_PLUGIN_DOMAIN).'</span></a></li>
															<li><a href="javascript:;" class="jp-pause" tabindex="1" title="'.__('Pause', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Pause', CHERRY_PLUGIN_DOMAIN).'</span></a></li>
															<li><a href="javascript:;" class="jp-next" tabindex="1" title="'.__('Next', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Next', CHERRY_PLUGIN_DOMAIN).'</span></a></li>
															<li><a href="javascript:;" class="jp-stop" tabindex="1" title="'.__('Stop', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Stop', CHERRY_PLUGIN_DOMAIN).'</span></a></li>
														</ul>
														<div class="jp-volume-bar">
															<div class="jp-volume-bar-value"></div>
														</div>
														<ul class="jp-toggles">
															<li><a href="javascript:;" class="jp-mute" tabindex="1" title="'.__('Mute', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Mute', CHERRY_PLUGIN_DOMAIN).'</span></a></li>
															<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="'.__('Unmute', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Unmute', CHERRY_PLUGIN_DOMAIN).'</span></a></li>
														</ul>
													</div>
												</div>
												<div class="jp-no-solution">
													<span>'.__('Update Required.', CHERRY_PLUGIN_DOMAIN).'</span>'.__('To play the media you will need to either update your browser to a recent version or update your ', CHERRY_PLUGIN_DOMAIN).'<a href="http://get.adobe.com/flashplayer/" target="_blank">'.__('Flash plugin', CHERRY_PLUGIN_DOMAIN).'</a>
												</div>
											</div>
										</div>
										<div class="jp-playlist">
											<ul>
												<li></li>
											</ul>
										</div>
									</div>';


					$output .= '<div class="entry-content">';
						$output .= get_the_content($post->ID);
					$output .= '</div>';

					//Video
					} elseif ($post_format == "video") {

						$template_url = get_template_directory_uri();
						$id           = $post->ID;

						// get video attribute
						$video_title  = get_post_meta(get_the_ID(), 'tz_video_title', true);
						$video_artist = get_post_meta(get_the_ID(), 'tz_video_artist', true);
						$embed        = get_post_meta(get_the_ID(), 'tz_video_embed', true);
						$m4v_url      = get_post_meta(get_the_ID(), 'tz_m4v_url', true);
						$ogv_url      = get_post_meta(get_the_ID(), 'tz_ogv_url', true);

						$content_url = content_url();
						$content_str = 'wp-content';

						$pos1 = strpos($m4v_url, $content_str);
						if ($pos1 === false) {
							$file1 = $m4v_url;
						} else {
							$m4v_new  = substr($m4v_url, $pos1+strlen($content_str), strlen($m4v_url) - $pos1);
							$file1    = $content_url.$m4v_new;
						}

						$pos2 = strpos($ogv_url, $content_str);
						if ($pos2 === false) {
							$file2 = $ogv_url;
						} else {
							$ogv_new  = substr($ogv_url, $pos2+strlen($content_str), strlen($ogv_url) - $pos2);
							$file2    = $content_url.$ogv_new;
						}

						// get thumb
						if(has_post_thumbnail()) {
							$thumb   = get_post_thumbnail_id();
							$img_url = wp_get_attachment_url( $thumb,'full'); //get img URL
							$image   = aq_resize( $img_url, 770, 380, true ); //resize & crop img
						}

						if ($embed == '') {
							$output .= '<script type="text/javascript">
								jQuery(document).ready(function(){
									jQuery("#jquery_jplayer_'. $id.'").jPlayer({
										ready: function () {
											jQuery(this).jPlayer("setMedia", {
												m4v: "'. stripslashes(htmlspecialchars_decode($file1)) .'",
												ogv: "'. stripslashes(htmlspecialchars_decode($file2)) .'",
												poster: "'. $image .'"
											});
										},
										swfPath: "'. $template_url .'/flash",
										solution: "flash, html",
										supplied: "ogv, m4v, all",
										cssSelectorAncestor: "#jp_container_'. $id.'",
										size: {
											width: "100%",
											height: "100%"
										}
									});
								});
								</script>';
								$output .= '<div id="jp_container_'. $id .'" class="jp-video fullwidth">';
								$output .= '<div class="jp-type-list-parent">';
								$output .= '<div class="jp-type-single">';
								$output .= '<div id="jquery_jplayer_'. $id .'" class="jp-jplayer"></div>';
								$output .= '<div class="jp-gui">';
								$output .= '<div class="jp-video-play">';
								$output .= '<a href="javascript:;" class="jp-video-play-icon" tabindex="1" title="'.__('Play', CHERRY_PLUGIN_DOMAIN).'">'.__('Play', CHERRY_PLUGIN_DOMAIN).'</a></div>';
								$output .= '<div class="jp-interface">';
								$output .= '<div class="jp-progress">';
								$output .= '<div class="jp-seek-bar">';
								$output .= '<div class="jp-play-bar">';
								$output .= '</div></div></div>';
								$output .= '<div class="jp-duration"></div>';
								$output .= '<div class="jp-time-sep">/</div>';
								$output .= '<div class="jp-current-time"></div>';
								$output .= '<div class="jp-controls-holder">';
								$output .= '<ul class="jp-controls">';
								$output .= '<li><a href="javascript:;" class="jp-play" tabindex="1" title="'.__('Play', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Play', CHERRY_PLUGIN_DOMAIN).'</span></a></li>';
								$output .= '<li><a href="javascript:;" class="jp-pause" tabindex="1" title="'.__('Pause', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Pause', CHERRY_PLUGIN_DOMAIN).'</span></a></li>';
								$output .= '<li class="li-jp-stop"><a href="javascript:;" class="jp-stop" tabindex="1" title="'.__('Stop', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Stop', CHERRY_PLUGIN_DOMAIN).'</span></a></li>';
								$output .= '</ul>';
								$output .= '<div class="jp-volume-bar">';
								$output .= '<div class="jp-volume-bar-value">';
								$output .= '</div></div>';
								$output .= '<ul class="jp-toggles">';
								$output .= '<li><a href="javascript:;" class="jp-mute" tabindex="1" title="'.__('Mute', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Mute', CHERRY_PLUGIN_DOMAIN).'</span></a></li>';
								$output .= '<li><a href="javascript:;" class="jp-unmute" tabindex="1" title="'.__('Unmute', CHERRY_PLUGIN_DOMAIN).'"><span>'.__('Unmute', CHERRY_PLUGIN_DOMAIN).'</span></a></li>';
								$output .= '</ul>';
								$output .= '</div></div>';
								$output .= '<div class="jp-no-solution">';
								$output .= '<span>'.__('Update Required.', CHERRY_PLUGIN_DOMAIN).'</span>'.__('To play the media you will need to either update your browser to a recent version or update your ', CHERRY_PLUGIN_DOMAIN).'<a href="http://get.adobe.com/flashplayer/" target="_blank">'.__('Flash plugin', CHERRY_PLUGIN_DOMAIN).'</a>';
								$output .= '</div></div></div></div>';
								$output .= '</div>';
						} else {
							$output .= '<div class="video-wrap">' . stripslashes(htmlspecialchars_decode($embed)) . '</div>';
						}

						if($excerpt_count >= 1){
							$output .= '<div class="excerpt">';
								$output .= my_string_limit_words($excerpt,$excerpt_count);
							$output .= '</div>';
					}

					//Standard
					} else {

						if ($thumb == 'true') {
							if ( has_post_thumbnail($post->ID) ){
								$output .= '<figure class="thumbnail featured-thumbnail"><a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
								$output .= '<img src="'.$image.'" alt="' . get_the_title($post->ID) .'"/>';
								$output .= '</a></figure>';
							}
						}

						$output .= '<div class="inner">';

							$output .= '<h5><a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
									$output .= get_the_title($post->ID);
							$output .= '</a></h5>';

							if ($subtitle != "") $output .= '<h6>' . $subtitle . '</h6>';

							if ($meta == 'true') {
									$output .= '<span class="meta">';
											$output .= '<span class="post-date">';
												$output .= '<span class="day">' . get_the_time('j') . '</span>' . get_the_time('M');
											$output .= '</span>';
									$output .= '</span>';
							}
							$output .= cherry_get_post_networks(array('post_id' => $post->ID, 'display_title' => false, 'output_type' => 'return'));
							if ($excerpt_count >= 1) {
								$output .= '<div class="excerpt">';
									$output .= my_string_limit_words($excerpt,$excerpt_count);
								$output .= '</div>';
							}
							if ($more_text_single!="") {
								$output .= '<a href="'.get_permalink($post->ID).'" class="btn btn-primary" title="'.get_the_title($post->ID).'">';
								$output .= $more_text_single;
								$output .= '</a>';
							}

						$output .= '</div>';
					}
				$output .= '<div class="clear"></div>';
				$item_counter ++;
				$output .= '</li><!-- .entry (end) -->';
			}
			wp_reset_postdata(); // restore the global $post variable
			$output .= '</ul><!-- .recent-posts (end) -->';
			return $output;
		}
		add_shortcode('recent_posts', 'shortcode_recent_posts');
	}

	/**
	 * Post Cycle
	 *
	 */
	if (!function_exists('shortcode_post_cycle')) {

		function shortcode_post_cycle($atts, $content = null) {
			extract(shortcode_atts(array(
					'num'              => '5',
					'type'             => 'post',
					'meta'             => '',
					'effect'           => 'slide',
					'thumb'            => 'true',
					'thumb_width'      => '200',
					'thumb_height'     => '180',
					'more_text_single' => '',
					'category'         => '',
					'custom_category'  => '',
					'excerpt_count'    => '15',
					'pagination'       => 'true',
					'navigation'       => 'true',
					'custom_class'     => ''
			), $atts));

			$type_post         = $type;
			$slider_pagination = $pagination;
			$slider_navigation = $navigation;
			$random            = gener_random(10);
			$i                 = 0;
			$rand              = rand();
			$count             = 0;
			if ( is_rtl() ) {
				$is_rtl = 'true';
			} else {
				$is_rtl = 'false';
			}

			$output = '<script type="text/javascript">
							jQuery(window).load(function() {
								jQuery("#flexslider_'.$random.'").flexslider({
									animation: "'.$effect.'",
									smoothHeight : true,
									directionNav: '.$slider_navigation.',
									controlNav: '.$slider_pagination.',
									rtl: '.$is_rtl.',
									slideshow: false
								});
							});';
			$output .= '</script>';
			$output .= '<div id="flexslider_'.$random.'" class="flexslider no-bg '.$custom_class.'">';
				$output .= '<ul class="slides">';

				global $post;
				global $my_string_limit_words;

				// WPML filter
				$suppress_filters = get_option('suppress_filters');

				$args = array(
					'post_type'              => $type_post,
					'category_name'          => $category,
					$type_post . '_category' => $custom_category,
					'numberposts'            => $num,
					'orderby'                => 'post_date',
					'order'                  => 'DESC',
					'suppress_filters'       => $suppress_filters
				);

				$latest = get_posts($args);

				foreach($latest as $key => $post) {
					//Check if WPML is activated
					if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
						global $sitepress;

						$post_lang = $sitepress->get_language_for_element($post->ID, 'post_' . $type_post);
						$curr_lang = $sitepress->get_current_language();
						// Unset not translated posts
						if ( $post_lang != $curr_lang ) {
							unset( $latest[$key] );
						}
						// Post ID is different in a second language Solution
						if ( function_exists( 'icl_object_id' ) ) {
							$post = get_post( icl_object_id( $post->ID, $type_post, true ) );
						}
					}
					setup_postdata($post);
					$excerpt        = get_the_excerpt();
					$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
					$url            = $attachment_url['0'];
					$image          = aq_resize($url, $thumb_width, $thumb_height, true);

					$output .= '<li class="list-item-'.$count.'">';

						if ($thumb == 'true') {

							if ( has_post_thumbnail($post->ID) ){
								$output .= '<figure class="thumbnail featured-thumbnail"><a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
								$output .= '<img  src="'.$image.'" alt="'.get_the_title($post->ID).'" />';
								$output .= '</a></figure>';
							} else {

								$thumbid = 0;
								$thumbid = get_post_thumbnail_id($post->ID);

								$images = get_children( array(
									'orderby'        => 'menu_order',
									'order'          => 'ASC',
									'post_type'      => 'attachment',
									'post_parent'    => $post->ID,
									'post_mime_type' => 'image',
									'post_status'    => null,
									'numberposts'    => -1
								) );

								if ( $images ) {

									$k = 0;
									//looping through the images
									foreach ( $images as $attachment_id => $attachment ) {
										// $prettyType = "prettyPhoto-".$rand ."[gallery".$i."]";
										//if( $attachment->ID == $thumbid ) continue;

										$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' ); // returns an array
										$img = aq_resize( $image_attributes[0], $thumb_width, $thumb_height, true ); //resize & crop img
										$alt = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
										$image_title = $attachment->post_title;

										if ( $k == 0 ) {
											$output .= '<figure class="featured-thumbnail">';
											$output .= '<a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
											$output .= '<img  src="'.$img.'" alt="'.get_the_title($post->ID).'" />';
											$output .= '</a></figure>';
										} break;
										$k++;
									}
								}
							}
						}

						$output .= '<h5><a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
						$output .= get_the_title($post->ID);
						$output .= '</a></h5>';

						if($meta == 'true'){
							$output .= '<span class="meta">';
							$output .= '<span class="post-date">';
							$output .= get_the_date();
							$output .= '</span>';
							$output .= '<span class="post-comments">'.__('Comments', CHERRY_PLUGIN_DOMAIN).": ";
							$output .= '<a href="'.get_comments_link($post->ID).'">';
							$output .= get_comments_number($post->ID);
							$output .= '</a>';
							$output .= '</span>';
							$output .= '</span>';
						}
						

						if($excerpt_count >= 1){
							$output .= '<p class="excerpt">';
							if ($type == "testi") {
								$output .= '“ ' . my_string_limit_words($excerpt,$excerpt_count) . '”'; 	
							} else {
								$output .= my_string_limit_words($excerpt,$excerpt_count);	
							}							
							$output .= '</p>';
						}

						//display post options
						$output .= '<div class="post_options">';
						switch($type_post) {
							case "team":
								$teampos  = (get_post_meta($post->ID, 'my_team_pos', true)) ? get_post_meta($post->ID, 'my_team_pos', true) : "";
								$teaminfo = (get_post_meta($post->ID, 'my_team_info', true)) ? get_post_meta($post->ID, 'my_team_info', true) : "";
								$output .= "<span class='page-desc'>".$teampos."</span><br><span class='team-content post-content'>".$teaminfo."</span>";
								$output .= cherry_get_post_networks(array('post_id' => $post->ID, 'display_title' => false, 'output_type' => 'return'));
								break;
							case "testi":
								$testiname = (get_post_meta($post->ID, 'my_testi_caption', true)) ? get_post_meta($post->ID, 'my_testi_caption', true) : "";
								$testiurl  = (get_post_meta($post->ID, 'my_testi_url', true)) ? get_post_meta($post->ID, 'my_testi_url', true) : "";
								$testiinfo = (get_post_meta($post->ID, 'my_testi_info', true)) ? get_post_meta($post->ID, 'my_testi_info', true) : "";

								if ($testiname != "") $output .="<span class='user'>".$testiname."</span>";

								if ($testiinfo != "") $output .="<br><span class='info'>".$testiinfo."</span>";

								if ($testiurl != "")  $output .="<br><a href='".$testiurl."'>".$testiurl."</a>";

								break;
							case "portfolio":
								$portfolioClient = (get_post_meta($post->ID, 'tz_portfolio_client', true)) ? get_post_meta($post->ID, 'tz_portfolio_client', true) : "";
								$portfolioDate = (get_post_meta($post->ID, 'tz_portfolio_date', true)) ? get_post_meta($post->ID, 'tz_portfolio_date', true) : "";
								$portfolioInfo = (get_post_meta($post->ID, 'tz_portfolio_info', true)) ? get_post_meta($post->ID, 'tz_portfolio_info', true) : "";
								$portfolioURL = (get_post_meta($post->ID, 'tz_portfolio_url', true)) ? get_post_meta($post->ID, 'tz_portfolio_url', true) : "";
								$output .="<strong class='portfolio-meta-key'>".__('Client', CHERRY_PLUGIN_DOMAIN).": </strong><span> ".$portfolioClient."</span><br>";
								$output .="<strong class='portfolio-meta-key'>".__('Date', CHERRY_PLUGIN_DOMAIN).": </strong><span> ".$portfolioDate."</span><br>";
								$output .="<strong class='portfolio-meta-key'>".__('Info', CHERRY_PLUGIN_DOMAIN).": </strong><span> ".$portfolioInfo."</span><br>";
								$output .="<a href='".$portfolioURL."'>".__('Launch Project', CHERRY_PLUGIN_DOMAIN)."</a><br>";
								break;
							default:
								$output .="";
						};
						$output .= '</div>';

						if($more_text_single!=""){
							$output .= '<a href="'.get_permalink($post->ID).'" class="btn btn-primary" title="'.get_the_title($post->ID).'">';
							$output .= $more_text_single;
							$output .= '</a>';
						}

					$output .= '</li>';
					$count++;
				}
				wp_reset_postdata(); // restore the global $post variable
				$output .= '</ul>';
			$output .= '</div>';
			return $output;
		}
		add_shortcode('post_cycle', 'shortcode_post_cycle');

	}

	//------------------------------------------------------
	//  Related Posts
	//------------------------------------------------------
	if(!function_exists('cherry_related_posts')){
		function cherry_related_posts($args = array()){
			global $post;
			$default = array(
				'post_type' => get_post_type($post),
				'class' => 'related-posts',
				'class_list' => 'related-posts_list',
				'class_list_item' => 'related-posts_item',
				'display_title' => true,
				'display_link' => true,
				'display_thumbnail' => true,
				'width_thumbnail' => 170,
				'height_thumbnail' => 120,
				'before_title' => '<h3 class="related-posts_h">',
				'after_title' => '</h3>',
				'posts_count' => 4
			);
			extract(array_merge($default, $args));

			$post_tags = wp_get_post_terms($post->ID, $post_type.'_tag', array("fields" => "slugs"));
			$tags_type = $post_type=='post' ? 'tag' : $post_type.'_tag' ;
			$suppress_filters = get_option('suppress_filters');// WPML filter
			$blog_related = apply_filters( 'cherry_text_translate', of_get_option('blog_related'), 'blog_related' );
			if ($post_tags && !is_wp_error($post_tags)) {
				$args = array(
					"$tags_type" => implode(',', $post_tags),
					'post_status' => 'publish',
					'posts_per_page' => $posts_count,
					'ignore_sticky_posts' => 1,
					'post__not_in' => array($post->ID),
					'post_type' => $post_type,
					'suppress_filters' => $suppress_filters
					);
				query_posts($args);
				if ( have_posts() ) {
					$output = '<div class="'.$class.'">';
					$output .= $display_title ? $before_title.$blog_related.$after_title : '' ;
					$output .= '<ul class="'.$class_list.' clearfix">';
					while( have_posts() ) {
						the_post();
						$thumb   = has_post_thumbnail() ? get_post_thumbnail_id() : PARENT_URL.'/images/empty_thumb.gif';
						$blank_img = stripos($thumb, 'empty_thumb.gif');
						$img_url = $blank_img ? $thumb : wp_get_attachment_url( $thumb,'full');
						$image   = $blank_img ? $thumb : aq_resize($img_url, $width_thumbnail, $height_thumbnail, true) or $img_url;

						$output .= '<li class="'.$class_list_item.'">';
						$output .= $display_thumbnail ? '<figure class="thumbnail featured-thumbnail"><a href="'.get_permalink().'" title="'.get_the_title().'"><img data-src="'.$image.'" alt="'.get_the_title().'" /></a></figure>': '' ;
						$output .= $display_link ? '<a href="'.get_permalink().'" >'.get_the_title().'</a>': '' ;
						$output .= '</li>';
					}
					$output .= '</ul></div>';
					echo $output;
				}
				wp_reset_query();
			}
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/* Custom Comments Structure
	/*-----------------------------------------------------------------------------------*/
	if ( !function_exists( 'mytheme_comment' ) ) {
		function mytheme_comment($comment, $args, $depth) {
			$GLOBALS['comment'] = $comment;
		?>
		<li <?php comment_class('clearfix'); ?> id="li-comment-<?php comment_ID() ?>">
			<div id="comment-<?php comment_ID(); ?>" class="comment-body clearfix">
				<div class="wrapper">
					<div class="comment-author vcard">
						<?php echo get_avatar( $comment->comment_author_email, 80 ); ?>
						<?php printf('<span class="author">%1$s</span>', get_comment_author_link()) ?>
					</div>
					<?php if ($comment->comment_approved == '0') : ?>
						<em><?php echo theme_locals("your_comment") ?></em>
					<?php endif; ?>
					<div class="extra-wrap">
						<?php comment_text(); ?>

						<div class="comment-meta commentmetadata"><?php printf('%1$s', get_comment_date()) ?></div>

						<div class="extra-wrap">
							<div class="reply">
								<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
							</div>							
						</div>
					</div>
				</div>				
			</div>
	<?php }
	}

?>
