<?php

/*	Copyright 2011 Akifumi Nishikawa

	License   : Licensed under the MIT License
	Author    : Akifumi Nishikawa(http://www.duluck.net/)
	Version   : 1.1.6
	Update    : 2011-09-15
	
*/

include_once('resizeimg.class.php');

class ocf_post_class extends resize_image {

	private $ocf_setup_data;
	private $page_key;
	private $postID;
	
	### constructor
	function __construct() {
		//継承元 resize_image のコンストラクタを明示的に呼び出し。do
		parent::__construct();
		
		$this->page_key = $page_key;
		$this->postID = $postID;
	}

	### core
	public function ocf_post_page() {
	
		$this->ocf_data();
		
		$tb = "";
		$tag = $tb.'<input type="hidden" name="ocf_verify_key" id="ocf_verify_key" value="'.wp_create_nonce( 'old_custom_fields' ).'" />'."\n\n";

		for( $i = 0; $i < count( $this->ocf_setup_data ); $i++ ){
		
			$value = $this->get_ocf_value_data( $this->ocf_setup_data[ $i ][ 'input_name' ] );
			
			switch( $this->ocf_setup_data[ $i ][ 'type' ] ) {
			
				case 'text':
					$tag .= $this->make_textfield( $i, $this->ocf_setup_data[ $i ], $value );
					break;
				case 'textarea':
					$tag .= $this->make_textarea( $i, $this->ocf_setup_data[ $i ], $value );
					break;
				case 'image':
					$tag .= $this->make_imagefield( $i, $this->ocf_setup_data[ $i ], $value );
					break;
				case 'file':
					$tag .= $this->make_filefield( $i, $this->ocf_setup_data[ $i ], $value );
					break;
				case 'checkbox':
					$tag .= $this->make_checkbox( $i, $this->ocf_setup_data[ $i ], $value );
					break;
				case 'radio':
					$tag .= $this->make_radio( $i, $this->ocf_setup_data[ $i ], $value );
					break;
				case 'select':
					$tag .= $this->make_select( $i, $this->ocf_setup_data[ $i ], $value );
					break;
				case 'h5':
					$tag .= $this->make_h5( $this->ocf_setup_data[ $i ][ 'h5_name' ] );
					break;
				default:
					$tag .= '';
			}
		}
		echo $tag;
	}
	
	
	### GET DATA
	
	/* FUNC. current ocf data */
	private function ocf_data() {
	
		global $post;
		
		if( isset( $post->post_type ) && $post->post_type == 'post' ) {
			$this->page_key = $post->post_type; // if post:"post"
		} elseif( isset( $post->post_type ) && $post->post_type == 'page' ) {
			$this->page_key = $post->ID; // if page:postID
		}
		
		$all_setup_data = get_option( 'ocf_setup_data' );
		$this->ocf_setup_data = $all_setup_data[ $this->page_key ];
		
		$this->postID = ( isset( $post->ID ) ) ? $post->ID : '';
	}
	
	
	### UTILITY
	
	/* FUNC. sanitize */
	private function sanitize_name( $str ) {
		$str = sanitize_title( $str ); // src:wp-includes/functions-formatting.php
		return $str;
	}
	
	/* FUNC. get custom field data */
	private function get_ocf_value_data( $input_name ) {
		$value = ( $this->postID ) ? get_post_meta( $this->postID, $input_name ) : '';
		return $value[ 0 ];
	}
	
	
	### CUSTOM FIELDS TAG
	
	/* FUNC_TAG_INPUT. text */
	private function make_textfield( $num, $setup, $value ) {
	
		$inside = '';
		$tb = "\t\t";
		
		$inside .= $tb."\t".'<p class="ocf_input"><input type="text" name="'.$setup[ 'input_name' ].'" value="'.$value.'" size="40" class="data" /></p>'."\n";
		$inside .= ( $setup[ 'caption' ] ) ? $tb."\t".'<p class="ocf_caption">'.$setup[ 'caption' ].'</p>'."\n" : '' ;
		
		$tag = $this->disp_postbox( $num, $inside, $setup );
		return $tag;
	}
	
	/* FUNC_TAG_INPUT. textarea */
	private function make_textarea( $num, $setup, $value ) {
	
		$inside = '';
		$tb = "\t\t";
		
		$inside .= $tb."\t".'<p class="ocf_input">'."\n";
		$inside .= $tb."\t\t".'<textarea class="data" name="'.$setup[ 'input_name' ].'" type="textfield" rows="'.$setup[ 'row_size' ].'" cols="60">'.$value.'</textarea></p>'."\n";
		$inside .= $tb."\t".'</p>'."\n";
		$inside .= ( $setup[ 'caption' ] ) ? $tb."\t".'<p class="ocf_caption">'.$setup[ 'caption' ].'</p>'."\n" : '' ;
		
		$tag = $this->disp_postbox( $num, $inside, $setup );
		return $tag;
	}

	/* FUNC_TAG_INPUT. image */
	private function make_imagefield( $num, $setup, $value ) {
	
		$inside = '';
		$tb = "\t\t";
		
		$inside .= $tb."\t".'<div class="inside">'."\n";
		$inside .= $tb."\t\t".'<p class="ocf_input">'."\n";
		$inside .= $tb."\t\t\t".'<input type="hidden" name="'.$setup[ 'input_name' ].'" value="'.$value.'" class="data" />'."\n";
		
		$img_size_arr = $setup[ 'list' ];
		
		// thumbnail image size
		for($i = 0; $i < count($img_size_arr); $i++) {
		
			$check_arr = array( 'fit', 'resize', 'wfit', 'hfit' );
			
			if( !in_array($img_size_arr[ $i ][ "thum_type" ], $check_arr ) ) {
				continue;
			} else {
				$width = isset( $img_size_arr[ $i ][ "thum_size_w" ] ) ? $img_size_arr[ $i ][ "thum_size_w" ] : 'auto';
				$height = isset( $img_size_arr[ $i ][ "thum_size_h" ] ) ? $img_size_arr[ $i ][ "thum_size_h" ] : 'auto';
				
				if( !empty( $value ) ) {
					$thum_value = $this->rename_image_fpath( $width, $height, $img_size_arr[ $i ][ "thum_type" ], $value );
				} else {
					$thum_value = '';
				}
				
				$imgattr = $img_size_arr[ $i ][ "thum_type" ].'_'.$width.'_'.$height;
				$name    = $setup[ 'input_name' ].'_'.$imgattr;
				$inside .= $tb."\t\t\t".'<input type="hidden" name="'.$name.'" value="'.$thum_value.'" class="thumdata" rel="'.$imgattr.'" />'."\n";
			}
		}
		
		$inside .= $tb."\t\t".'</p>'."\n";
		$inside .= $tb."\t\t".'<span class="ocf_imagefield_thumb"></span>'."\n";
		$inside .= $tb."\t\t".'<p class="up_file_value_box">'.urldecode( $value ).'</p>'."\n";
		$inside .= $tb."\t\t".'<p class="ocf_image_cancel" style="display:none;"><a>画像を削除：<img alt="delete image" src="" /></a></p>'."\n";
		$inside .= $tb."\t\t".'<p class="ocf_add_media"><a>画像を追加：<img alt="add image" src="images/media-button-image.gif" /></p></a>'."\n";
		$inside .= ( $setup[ 'caption' ] ) ? $tb."\t\t".'<p class="ocf_caption">'.$setup[ 'caption' ].'</p>'."\n" : '' ;
		$inside .= $tb."\t".'</div>'."\n";
		
		$tag = $this->disp_postbox( $num, $inside, $setup );
		return $tag;
	}
	
	/* FUNC. rename_image_fpath */
	private function rename_image_fpath( $width, $height, $thum_type, $value ) {
	
		// multisite mode - wp
		if ( is_multisite() ) {
			global $blog_id;
			$site_path = get_blog_status ( $blog_id, 'path' );
			$site_num = get_blog_status ( $blog_id, 'blog_id' );
			$src_path = str_replace( $site_path, '/wp-content/blogs.dir/'.$site_num.'/', $value );
			$thum_value = resize_image::disp_resize_img_path( $src_path, $width, $height, $thum_type );
			$thum_value = str_replace( '/wp-content/blogs.dir/'.$site_num.'/', $site_path, $thum_value );
		// nomal mode - wp
		} elseif ( !is_multisite() ) {
			$src_path = $value;
			$thum_value = resize_image::disp_resize_img_path( $src_path, $width, $height, $thum_type );
		}
	}

	/* FUNC_TAG_INPUT. file */
	private function make_filefield( $num, $setup, $value ) {
	
		$inside = '';
		$tb = "\t\t";
		
		$inside .= $tb."\t".'<p class="ocf_input">'."\n";
		$inside .= $tb."\t\t\t".'<p class="up_file_value_box">'.urldecode( $value ).'</p>'."\n";
		$inside .= $tb."\t\t".'<input type="hidden" name="'.$setup[ 'input_name' ].'" value="'.$value.'" class="data" />'."\n";
		$inside .= $tb."\t\t".'<img src="images/cancel.png" width="16" height="16" class="cancel" style="display:none;" />'."\n";
		$inside .= $tb."\t".'</p>'."\n";
		$inside .= $tb."\t".'<p class="ocf_file_cancel" style="display:none;"><a>ファイルを削除：<img alt="delete file" src="" /></a></p>'."\n";
		$inside .= $tb."\t".'<p class="ocf_add_media"><a>ファイルを追加：<img alt="add file" src="images/media-button-other.gif" /></a></p>'."\n";
		$inside .= ( $setup[ 'caption' ] ) ? $tb."\t".'<p class="ocf_caption">'.$setup[ 'caption' ].'</p>'."\n" : '' ;
		
		$tag = $this->disp_postbox( $num, $inside, $setup );
		return $tag;
	}

	/* FUNC_TAG_INPUT. checkbox */
	private function make_checkbox( $num, $setup, $value ) {
	
		$inside = '';
		$tb = "\t\t";
		
		$value_arr = explode( ',', $value );
		
		for( $i = 0; $i < count( $setup[ 'list' ] ); $i++ ) {
			if( count( $value_arr ) > 0 && in_array( $setup[ 'list' ][ $i ][ 'value' ], $value_arr ) ) {
				$checked = ' checked="checked"';
			} elseif( !is_array( $value ) && $setup[ 'list' ][ $i ][ 'checked' ] == 'checked' ) {
				$checked = ' checked="checked"';
			} else {
				$checked = '';
			}
			
			$inside .= $tb."\t".'<p class="ocf_input"><label for="'.$setup[ 'input_name' ].'_'.( $i+1 ).'"><input type="checkbox" id="'.$setup[ 'input_name' ].'_'.( $i+1 ).'" name="'.$setup[ 'input_name' ].'[ ]" value="'.$setup[ 'list' ][ $i ][ 'value' ].'"'.$checked.' /> '.$setup[ 'list' ][ $i ][ 'label' ].'</label></p>'."\n";
		}
		
		$inside .= ( $setup[ 'caption' ] ) ? $tb."\t".'<p class="ocf_caption">'.$setup[ 'caption' ].'</p>'."\n" : '' ;
		
		$tag = $this->disp_postbox( $num, $inside, $setup );
		return $tag;
	}

	/* FUNC_TAG_INPUT. redio */
	private function make_radio( $num, $setup, $value ) {
	
		$inside = '';
		$tb = "\t\t";
		
		$setup[ 'selected' ] = ( $setup[ 'selected' ] ) ? $setup[ 'selected' ] : 'list_0';
		
		for( $i = 0; $i < count( $setup[ 'list' ] ); $i++ ) {
			if( !empty( $value ) && $setup[ 'list' ][ $i ][ 'value' ] == $value ) {
				$checked = ' checked="checked"';
			} elseif( empty( $value ) && $setup[ 'selected' ] == 'list_'.$i ) {
				$checked = ' checked="checked"';
			} else {
				$checked = '';
			}
			
			$inside .= $tb."\t".'<p class="ocf_input"><label for="'.$setup[ 'input_name' ].'_'.( $i+1 ).'"><input type="radio" id="'.$setup[ 'input_name' ].'_'.( $i+1 ).'" name="'.$setup[ 'input_name' ].'" value="'.$setup[ 'list' ][ $i ][ 'value' ].'"'.$checked.' /> '.$setup[ 'list' ][ $i ][ 'label' ].'</label></p>'."\n";
		}
		
		$inside .= ( $setup[ 'caption' ] ) ? $tb."\t".'<p class="ocf_caption">'.$setup[ 'caption' ].'</p>'."\n" : '' ;
		
		$tag = $this->disp_postbox( $num, $inside, $setup );
		return $tag;
	}

	/* FUNC_TAG_INPUT. select */
	private function make_select( $num, $setup, $value ) {
		
		$inside = '';
		$tb = "\t\t";
		
		$setup[ 'selected' ] = ( $setup[ 'selected' ] ) ? $setup[ 'selected' ] : 'list_0';
		
		$inside .= $tb."\t".'<p class="ocf_input">'."\n";
		$inside .= $tb."\t\t".'<select name="'.$setup[ 'input_name' ].'">'."\n";
		
		for( $i = 0; $i < count( $setup[ 'list' ] ); $i++ ) {
			if( !empty( $value ) && $setup[ 'list' ][ $i ][ 'value' ] == $value ) {
				$selected = ' selected="selected"';
			} elseif( empty( $value ) && $setup[ 'selected' ] == 'list_'.$i ) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			$inside .= $tb."\t\t\t".'<option value="'.$setup[ 'list' ][ $i ][ 'value' ].'" class="data"'.$selected.'>&nbsp;'.$setup[ 'list' ][ $i ][ 'label' ].'&nbsp;</option>'."\n";
		}
		
		$inside .= $tb."\t\t".'</select>'."\n";
		$inside .= $tb."\t".'</p>'."\n";
		$inside .= ( $setup[ 'caption' ] ) ? $tb."\t".'<p class="ocf_caption">'.$setup[ 'caption' ].'</p>'."\n" : '' ;
		
		$tag = $this->disp_postbox( $num, $inside, $setup );
		return $tag;
	}
	
	/* FUNC_TAG_INPUT. h5 */
	private function make_h5( $h5_name ) {
		
		$tag = '';
		$tb = "\t\t";
		
		$tag .= $tb."\t".'<h5 class="postbox_h5">'.$h5_name.'</h5>'."\n" ;
		return $tag;
	}
    
	/* FUNC_TAG. outside each box */
	private function disp_postbox( $num, $inside, $setup ) {
	
		$tag = '';
		$tb = "\t\t";
		
		$must = ( $setup[ 'must' ] == 'must' ) ? ' must' : '' ;
		$inline = ( $setup[ 'arrange' ] == 'inline' ) ? ' inline' : '' ;
		$num = ( $num < 10 ) ? '0'.$num : $num ;
		$setup[ 'type' ] = ( $setup[ 'type' ] == 'checkbox' ) ? 'check' : $setup[ 'type' ] ;
		
		$tag .= $tb.'<div class="postbox '.$setup[ 'type' ].$must.$inline.'" id="ocf_box'.$num.'_'.$setup[ 'input_name' ].'">'."\n";
		$tag .= $tb."\t".'<h4 class="ocf_title">'.$setup[ 'field_name' ].'</h4>'."\n";
		$tag .= $inside;
		$tag .= $tb.'</div>'."\n\n";
		
		return $tag;
	}
	
	/* FUNC. add meta value to post. */
	public function edit_meta_value( $id ) {
	
		$this->ocf_data();
	
		global $wpdb;
	
		if( !isset( $id ) ) {
			$id = $post->ID;
		}
		
		if( !current_user_can( 'edit_post', $id ) ) {
			return $id;
		}
		
		if( !wp_verify_nonce( $_REQUEST[ 'ocf_verify_key' ], 'old_custom_fields' ) ) {
			return $id;
		}
		
		for( $i = 0; $i < count( $this->ocf_setup_data ); $i++ ) {
		
			$input_name = $this->sanitize_name( $this->ocf_setup_data[ $i ][ 'input_name' ] );
			$input_name = $wpdb->escape( stripslashes( trim( $input_name ) ) );
			$val = ( is_array( $_REQUEST[ $input_name ] ) ) ? implode( ',', $_REQUEST[ $input_name ] ) : $_REQUEST[ $input_name ];
			$meta_value = stripslashes( trim( $val ) ); // value from each custom fields
			
			if( isset( $meta_value ) && !empty( $meta_value ) ) {
				delete_post_meta( $id, $input_name ); // remove custom field data from DB-WPMETA
				add_post_meta( $id, $input_name, $meta_value ); // add value to DB-WPMETA
			} else {
				delete_post_meta( $id, $input_name ); // if no data : not action
			}
			
			// add thumbnail data
			if( $this->ocf_setup_data[ $i ][ 'type' ] == 'image' ) {
			
				$img_size_arr = $this->ocf_setup_data[ $i ][ 'list' ];
				
				for( $j = 0; $j < count($img_size_arr); $j++ ) {
					
					$width = isset( $img_size_arr[ $j ][ "thum_size_w" ] ) ? $img_size_arr[ $j ][ "thum_size_w" ] : 'auto';
					$height = isset( $img_size_arr[ $j ][ "thum_size_h" ] ) ? $img_size_arr[ $j ][ "thum_size_h" ] : 'auto';
					
					// thumbnail image path
					$input_name_img_thum = $input_name.'_'.$img_size_arr[ $j ][ "thum_type" ].'_'.$width.'_'.$height;
					$input_name_img_thum = $wpdb->escape( stripslashes( trim( $input_name_img_thum ) ) );
					$val_img_thum = ( is_array( $_REQUEST[ $input_name_img_thum ] ) ) ? implode( ',', $_REQUEST[ $input_name_img_thum ] ) : $_REQUEST[ $input_name_img_thum ];
					$meta_value_img_thum = stripslashes( trim( $val_img_thum ) );
			
					if( isset( $meta_value_img_thum ) && !empty( $meta_value_img_thum ) ) {
						delete_post_meta( $id, $input_name_img_thum ); // remove custom field data from DB-WPMETA
						add_post_meta( $id, $input_name_img_thum, $meta_value_img_thum ); // add value to DB-WPMETA
					} else {
						delete_post_meta( $id, $input_name_img_thum ); // if no data : not action
					}
				}
			}
		}
	}
}
?>