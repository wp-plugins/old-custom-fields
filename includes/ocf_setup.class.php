<?php

/*	Copyright 2011 Akifumi Nishikawa

	License   : Licensed under the MIT License
	Author    : Akifumi Nishikawa( http://www.oldoffice.com/ )
	Version   : 1.1.2
	Update    : 2011-05-27
	
*/

class ocf_add_setup_class {

	private $ocf_page_key; // current_page ( post/page:id[0]/page:id[1]/page:id[2] )
	private $static_pages_id_arr;
	private $ocf_setup_data;
	
	### constructor
	function __construct() {
	
		$this->ocf_page_key = $ocf_page_key;
		$this->static_pages_id_arr = $static_pages_id_arr;
		$this->ocf_setup_data = $ocf_setup_data;
	}
	
	### core
	function ocf_setup_page() {
	
		$this->get_current_data();
		$this->ocf_get_option();
		
		$tag = '';
		$tb = "\t";
		
		// save message
		if( $_POST['posted'] == 'oldoffice' ) {
			$tag .= $tb."".'<div class="updated"><p><strong>設定を保存しました</strong></p></div>'."\n";
		}
		
		$tag .= $tb."".'<div class="wrap">'."\n";
		$tag .= $tb."\t".'<h2> Old Custom Fields 設定</h2>'."\n";
		$tag .= $tb."\t".'<ul class="subsubsub">'."\n";
		$tag .= $this->res_ocf_subsubsub();
		$tag .= $tb."\t".'</ul>'."\n";
		$tag .= $tb."\t".'<ul id="ocf_add_btn_box">'."\n";
		$tag .= $tb."\t\t".'<li><a href="#ocf_text_set" class="button-secondary">text</a></li>'."\n";
		$tag .= $tb."\t\t".'<li><a href="#ocf_text_set" class="button-secondary">textarea</a></li>'."\n";
		$tag .= $tb."\t\t".'<li><a href="#ocf_image_set" class="button-secondary">image</a></li>'."\n";
		$tag .= $tb."\t\t".'<li><a href="#ocf_file_set" class="button-secondary">file</a></li>'."\n";
		$tag .= $tb."\t\t".'<li><a href="#ocf_checkbox_set" class="button-secondary">checkbox</a></li>'."\n";
		$tag .= $tb."\t\t".'<li><a href="#ocf_radio_set" class="button-secondary">radio</a></li>'."\n";
		$tag .= $tb."\t\t".'<li><a href="#ocf_select_set" class="button-secondary">select</a></li>'."\n";
		$tag .= $tb."\t\t".'<li><a href="#ocf_select_set" class="button-secondary">見出し</a></li>'."\n";
		$tag .= $tb."\t".'</ul>'."\n";
		$tag .= $tb."\t".'<div id="ocf_sample_cont">'."\n";
		
		$tag .= $this->res_ocf_text_set();
		$tag .= $this->res_ocf_textarea_set();
		$tag .= $this->res_ocf_image_set();
		$tag .= $this->res_ocf_file_set();
		$tag .= $this->res_ocf_checkbox_set();
		$tag .= $this->res_ocf_radio_set();
		$tag .= $this->res_ocf_select_set();
		$tag .= $this->res_ocf_h5_set();
		
		$tag .= $tb."\t".'</div>'."\n";
		$tag .= $tb."\t".'<ul id="ocf_sample_list">'."\n";
		
		$tag .= $this->res_ocf_image_list( 'sample', array(), '' );
		$tag .= $this->res_ocf_checkbox_list( 'sample', array(), '' );
		$tag .= $this->res_ocf_radio_list( 'sample', array(), '' );
		$tag .= $this->res_ocf_select_list( 'sample', array(), '' );
		
		$tag .= $tb."\t".'</ul>'."\n";
		
		$tag .= $tb."\t".'<form id="ocf_setup_form" action="'.admin_url( 'options-general.php?page=old-custom-fields/old-custom-fields.php&ocf_type='.$this->ocf_page_key ).'" method="post">'."\n";
		$tag .= $tb."\t\t".'<div id="ocf_main_cont">'."\n";
		$tag .= $tb."\t\t\t".'<div id="ocf_no_setting" class="ocf_noset_box">'."\n";
		$tag .= $tb."\t\t\t\t".'<p class="ocf_comment"><span class="ocf_text_bold">設定なし</span> ※フィールド追加は、上記のタイプボタンをクリック。</p>'."\n";
		$tag .= $tb."\t\t\t".'</div>'."\n";
		$tag .= $this->disp_main_cont_setup_box();
		$tag .= $tb."\t\t".'</div>'."\n";
		$tag .= $tb."\t\t".'<div id="ocf_side_cont">'."\n";
		$tag .= $tb."\t\t\t".'<input type="hidden" name="posted" value="oldoffice" />'."\n";
		$tag .= $tb."\t\t\t".'<p><input type="submit" id="ocf_save_btn" class="button-primary" value="設定の保存"></p>'."\n";
		$tag .= $tb."\t\t\t".'<p>設定内容 ： <span class="ocf_text_bold">'.$this->ocf_current_page_name( 'page-id:', $this->ocf_page_key ).'</span></p>'."\n";
		$tag .= $tb."\t\t".'</div>'."\n";
		$tag .= $tb."\t".'</form>'."\n";
		$tag .= $tb."".'</div>'."\n";
		
		echo $tag;
	}
	
	
	### GET DATA
	
	/* FUNC. get current data */
	private function get_current_data() {
		
		global $wpdb;
		
		// get page key
		$this->ocf_page_key = ( $_GET["ocf_type"] && $_GET["ocf_type"] != 'post' ) ? $_GET["ocf_type"] : 'post';
		
		// get static_page_ID
		$sql = '
			SELECT
				ID
			FROM
				'.$wpdb->posts.'
				WHERE
					post_type = "page"
					AND
					post_status = "publish"
		';
		$this->static_pages_id_arr = $wpdb->get_results( $sql, ARRAY_A );
	}
	
	/* FUNC. get wp_option */
	private function ocf_get_option() {
		
		// db_setup_data( wp_option )
		$this->ocf_setup_data = get_option( 'ocf_setup_data' );
		
		// post_data( over write wp_option )
		if ( $_POST['posted'] == 'oldoffice' ) {
			$this->ocf_setup_data[ $this->ocf_page_key ] = $_POST['ocf_setup'];
			update_option( 'ocf_setup_data', $this->ocf_setup_data );
		}
	}
	
	
	### COMMON TAG
	
	/* UNC_TAG_PART. subsubsub */
	private function res_ocf_subsubsub() {
	
		$tag = 	'';
		$tb = "\t\t\t\t";
		
		$class_current = ( $this->ocf_page_key == 'post' ) ? ' class="current"' : '';
		$tag .= $tb."".'<li><a href="?page=old-custom-fields/old-custom-fields.php&ocf_type=post"'.$class_current.'>post </a> |</li>'."\n";
		
		for( $i = 0; $i < count( $this->static_pages_id_arr ); $i++ ) {
			$class_current = ( $this->ocf_page_key == $this->static_pages_id_arr[$i]['ID'] ) ? ' class="current"' : '';
			$tag .= $tb."".'<li><a href="?page=old-custom-fields/old-custom-fields.php&ocf_type='.$this->static_pages_id_arr[$i]['ID'].'"'.$class_current.'>page-id:'.$this->static_pages_id_arr[$i]['ID'].' </a> |</li>'."\n";
		}
		return $tag;
	}
	
	/* FUNC_TAG_PART. current_type */
	private function ocf_current_page_name( $str, $current_page_key ) {
	
		if( $current_page_key == 'post' ) {
			return $current_page_key;
		} else {
			return $str.$current_page_key;
		}
	}
	
	/* FUNC. disp main cont setup box */
	private function disp_main_cont_setup_box() {

		$tag = '';
	
		for( $i = 0; $i < count( $this->ocf_setup_data[$this->ocf_page_key] ); $i++ ) {
		
			switch ( $this->ocf_setup_data[$this->ocf_page_key][$i]['type'] ) {
				case 'text':
					$tag .= $this->res_ocf_text_set( $i, 'disp_db_option', $this->ocf_setup_data[$this->ocf_page_key][$i] );
					break;
				case 'textarea':
					$tag .= $this->res_ocf_textarea_set( $i, 'disp_db_option', $this->ocf_setup_data[$this->ocf_page_key][$i] );
					break;
				case 'image':
					$tag .= $this->res_ocf_image_set( $i, 'disp_db_option', $this->ocf_setup_data[$this->ocf_page_key][$i] );
					break;
				case 'file':
					$tag .= $this->res_ocf_file_set( $i, 'disp_db_option', $this->ocf_setup_data[$this->ocf_page_key][$i] );
					break;
				case 'checkbox':
					$tag .= $this->res_ocf_checkbox_set( $i, 'disp_db_option', $this->ocf_setup_data[$this->ocf_page_key][$i] );
					break;
				case 'radio':
					$tag .= $this->res_ocf_radio_set( $i, 'disp_db_option', $this->ocf_setup_data[$this->ocf_page_key][$i] );
					break;
				case 'select':
					$tag .= $this->res_ocf_select_set( $i, 'disp_db_option', $this->ocf_setup_data[$this->ocf_page_key][$i] );
					break;
				case 'h5':
					$tag .= $this->res_ocf_h5_set( $i, 'disp_db_option', $this->ocf_setup_data[$this->ocf_page_key][$i] );
					break;
				default:
					'';
			}
		}
		return $tag;
	}
	
	### OCF BOX
	
	/* FUNC_TAG. text set */
	private function res_ocf_text_set( $num = 'sample', $disp = 'sample', $arr = array() ) {
		
		if( $disp == 'disp_db_option' ) {
			$field_name = $arr['field_name'];
			$input_name = $arr['input_name'];
			$caption    = $arr['caption'];
			$default    = $arr['default'];
		}
		
		if( $arr['must'] == 'no' ) {
			$must_yes = '';
			$must_no  = ' checked="checked"';
		} else {
			$must_yes = ' checked="checked"';
			$must_no  = '';
		}
		
		$tag = '';
		$tb = "\t\t\t";
		
		$tag .= $tb."".'<div class="ocf_setup_box ocf_text_set" title="text">'."\n";
		$tag .= $tb."\t".'<h4>'.( $num + 1 ).' : text</h4>'."\n";
		$tag .= $tb."\t".'<input type="hidden" name="ocf_setup['.$num.'][type]" value="text" />'."\n";
		$tag .= $tb."\t".'<table class="ocf_table text_table" title="text">'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">フィールド名</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><input type="text" name="ocf_setup['.$num.'][field_name]" value="'.$field_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row"> input_name</th>'."\n";
		$tag .= $tb."\t\t\t".'<td class="input_name_field"><input type="text" name="ocf_setup['.$num.'][input_name]" value="'.$input_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">補足説明文</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><textarea name="ocf_setup['.$num.'][caption]">'.$caption.'</textarea></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">必須項目</th>'."\n";
		$tag .= $tb."\t\t\t".'<td>'."\n";
		$tag .= $tb."\t\t\t\t".'<label><input type="radio" name="ocf_setup['.$num.'][must]" value="must"'.$must_yes.' /> yes</label>'."\n";
		$tag .= $tb."\t\t\t\t".'<label><input type="radio" name="ocf_setup['.$num.'][must]" value="no"'.$must_no.' /> no</label>'."\n";
		$tag .= $tb."\t\t\t".'</td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t".'</table>'."\n";
		$tag .= $this->res_ocf_box_button();
		$tag .= $tb."".'</div>'."\n";
		
		return $tag;
	}
	
	/* FUNC_TAG. textarea set */
	private function res_ocf_textarea_set( $num = 'sample', $disp = 'sample', $arr = array() ) {
		
		if( $disp == 'disp_db_option' ) {
			$field_name = $arr['field_name'];
			$input_name = $arr['input_name'];
			$caption    = $arr['caption'];
			
			$row_size[$arr['row_size']] = ' selected="selected"';
		}
			
		if( $arr['must'] == 'no' ) {
			$must_yes = '';
			$must_no  = ' checked="checked"';
		} else {
			$must_yes = ' checked="checked"';
			$must_no  = '';
		}
		
		$tag = '';
		$tb = "\t\t\t";
		
		$tag .= $tb."".'<div class="ocf_setup_box ocf_textarea_set" title="textarea">'."\n";
		$tag .= $tb."\t".'<h4>'.( $num + 1 ).' : textarea</h4>'."\n";
		$tag .= $tb."\t".'<input type="hidden" name="ocf_setup['.$num.'][type]" value="textarea" />'."\n";
		$tag .= $tb."\t".'<table class="ocf_table textarea_table" title="text">'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">フィールド名</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><input type="text" name="ocf_setup['.$num.'][field_name]" value="'.$field_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row"> input_name</th>'."\n";
		$tag .= $tb."\t\t\t".'<td class="input_name_field"><input type="text" name="ocf_setup['.$num.'][input_name]" value="'.$input_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">補足説明文</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><textarea name="ocf_setup['.$num.'][caption]">'.$caption.'</textarea></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">必須項目</th>'."\n";
		$tag .= $tb."\t\t\t".'<td>'."\n";
		$tag .= $tb."\t\t\t\t".'<label><input type="radio" name="ocf_setup['.$num.'][must]" value="must"'.$must_yes.' /> yes</label>'."\n";
		$tag .= $tb."\t\t\t\t".'<label><input type="radio" name="ocf_setup['.$num.'][must]" value="no"'.$must_no.' /> no</label>'."\n";
		$tag .= $tb."\t\t\t".'</td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">サイズ</th>'."\n";
		$tag .= $tb."\t\t\t".'<td>'."\n";
		$tag .= $tb."\t\t\t\t".'<select name="ocf_setup['.$num.'][row_size]">'."\n";
		$tag .= $tb."\t\t\t\t\t".'<option value="2"'.$row_size[2].'>row=2　</option>'."\n";
		$tag .= $tb."\t\t\t\t\t".'<option value="3"'.$row_size[3].'>row=3　</option>'."\n";
		$tag .= $tb."\t\t\t\t\t".'<option value="4"'.$row_size[4].'>row=4　</option>'."\n";
		$tag .= $tb."\t\t\t\t\t".'<option value="5"'.$row_size[5].'>row=5　</option>'."\n";
		$tag .= $tb."\t\t\t\t".'</select>'."\n";
		$tag .= $tb."\t\t\t".'</td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t".'</table>'."\n";
		$tag .= $this->res_ocf_box_button();
		$tag .= $tb."".'</div>'."\n";
		
		return $tag;
	}
	
	/* FUNC_TAG. image set */
	private function res_ocf_image_set( $num = 'sample', $disp = 'sample', $arr = array() ) {
		
		if( $disp == 'disp_db_option' ) {
			$field_name = $arr['field_name'];
			$input_name = $arr['input_name'];
			$caption    = $arr['caption'];
			$list_arr   = $arr['list'];
		}
			
		if( $arr['must'] == 'no' ) {
			$must_yes = '';
			$must_no  = ' checked="checked"';
		} else {
			$must_yes = ' checked="checked"';
			$must_no  = '';
		}
		
		$tag = '';
		$tb = "\t\t\t";
		
		$tag .= $tb."".'<div class="ocf_setup_box ocf_image_set" title="image">'."\n";
		$tag .= $tb."\t".'<h4>'.( $num + 1 ).' : image</h4>'."\n";
		$tag .= $tb."\t".'<input type="hidden" name="ocf_setup['.$num.'][type]" value="image" />'."\n";
		$tag .= $tb."\t".'<table class="ocf_table image_table" title="text">'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">フィールド名</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><input type="text" name="ocf_setup['.$num.'][field_name]" value="'.$field_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row"> input_name</th>'."\n";
		$tag .= $tb."\t\t\t".'<td class="input_name_field"><input type="text" name="ocf_setup['.$num.'][input_name]" value="'.$input_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">補足説明文</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><textarea name="ocf_setup['.$num.'][caption]">'.$caption.'</textarea></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">必須項目</th>'."\n";
		$tag .= $tb."\t\t\t".'<td>'."\n";
		$tag .= $tb."\t\t\t\t".'<label><input type="radio" name="ocf_setup['.$num.'][must]" value="must"'.$must_yes.' /> yes</label>'."\n";
		$tag .= $tb."\t\t\t\t".'<label><input type="radio" name="ocf_setup['.$num.'][must]" value="no"'.$must_no.' /> no</label>'."\n";
		$tag .= $tb."\t\t\t".'</td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">サムネイル</th>'."\n";
		$tag .= $tb."\t\t\t".'<td>'."\n";
		$tag .= $tb."\t\t\t\t".'<ul class="ocf_list_box">'."\n";
		$tag .= $this->res_ocf_image_list( 'disp_db_option', $list_arr, $num );
		$tag .= $tb."\t\t\t\t".'</ul>'."\n";
		$tag .= $tb."\t\t\t".'</td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t".'</table>'."\n";
		$tag .= $this->res_ocf_box_button();
		$tag .= $tb."".'</div>'."\n";
		
		return $tag;
	}
	
	/* FUNC_TAG. file set */
	private function res_ocf_file_set( $num = 'sample', $disp = 'sample', $arr = array() ) {
		
		if( $disp == 'disp_db_option' ) {
			$field_name = $arr['field_name'];
			$input_name = $arr['input_name'];
			$caption    = $arr['caption'];
		}
			
		if( $arr['must'] == 'no' ) {
			$must_yes = '';
			$must_no  = ' checked="checked"';
		} else {
			$must_yes = ' checked="checked"';
			$must_no  = '';
		}
		
		$tag = '';
		$tb = "\t\t\t";
		
		$tag .= $tb."".'<div class="ocf_setup_box ocf_file_set" title="file">'."\n";
		$tag .= $tb."\t".'<h4>'.( $num + 1 ).' : file</h4>'."\n";
		$tag .= $tb."\t".'<input type="hidden" name="ocf_setup['.$num.'][type]" value="file" />'."\n";
		$tag .= $tb."\t".'<table class="ocf_table text_table" title="text">'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">フィールド名</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><input type="text" name="ocf_setup['.$num.'][field_name]" value="'.$field_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row"> input_name</th>'."\n";
		$tag .= $tb."\t\t\t".'<td class="input_name_field"><input type="text" name="ocf_setup['.$num.'][input_name]" value="'.$input_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">補足説明文</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><textarea name="ocf_setup['.$num.'][caption]">'.$caption.'</textarea></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">必須項目</th>'."\n";
		$tag .= $tb."\t\t\t".'<td>'."\n";
		$tag .= $tb."\t\t\t\t".'<label><input type="radio" name="ocf_setup['.$num.'][must]" value="must"'.$must_yes.' /> yes</label>'."\n";
		$tag .= $tb."\t\t\t\t".'<label><input type="radio" name="ocf_setup['.$num.'][must]" value="no"'.$must_no.' /> no</label>'."\n";
		$tag .= $tb."\t\t\t".'</td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t".'</table>'."\n";
		$tag .= $this->res_ocf_box_button();
		$tag .= $tb."".'</div>'."\n";
		
		return $tag;
	}
	
	/* FUNC_TAG. checkbox set */
	private function res_ocf_checkbox_set( $num = 'sample', $disp = 'sample', $arr = array() ) {
		
		if( $disp == 'disp_db_option' ) {
			$field_name     = $arr['field_name'];
			$input_name     = $arr['input_name'];
			$caption        = $arr['caption'];
			
			$list_arr       = $arr['list'];
			
			$arrange_inline = ( $arr['arrange'] == 'inline' )? ' checked="checked"': '';
			$arrange_block  = ( $arr['arrange'] == 'inline' )? '': ' checked="checked"';
		}
		else
		{
			$arrange_block  = ' checked="checked"';
		}
		
		$tag = '';
		$tb = "\t\t\t";
		
		$tag .= $tb."".'<div class="ocf_setup_box ocf_checkbox_set" title="checkbox">'."\n";
		$tag .= $tb."\t".'<h4>'.( $num + 1 ).' : checkbox</h4>'."\n";
		$tag .= $tb."\t".'<input type="hidden" name="ocf_setup['.$num.'][type]" value="checkbox" />'."\n";
		$tag .= $tb."\t".'<table class="ocf_table checkbox_table" title="text">'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">フィールド名</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><input type="text" name="ocf_setup['.$num.'][field_name]" value="'.$field_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row"> input_name</th>'."\n";
		$tag .= $tb."\t\t\t".'<td class="input_name_field"><input type="text" name="ocf_setup['.$num.'][input_name]" value="'.$input_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">補足説明文</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><textarea name="ocf_setup['.$num.'][caption]">'.$caption.'</textarea></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">選択項目</th>'."\n";
		$tag .= $tb."\t\t\t".'<td>'."\n";
		$tag .= $tb."\t\t\t\t".'<ul class="ocf_list_box">'."\n";
		$tag .= $this->res_ocf_checkbox_list( 'disp_db_option', $list_arr, $num );
		$tag .= $tb."\t\t\t\t".'</ul>'."\n";
		$tag .= $tb."\t\t\t".'</td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">並べ方</th>'."\n";
		$tag .= $tb."\t\t\t".'<td>'."\n";
		$tag .= $tb."\t\t\t\t".'<label><input type="radio" name="ocf_setup['.$num.'][arrange]" value="inline"'.$arrange_inline.' /> 横並び</label>'."\n";
		$tag .= $tb."\t\t\t\t".'<label><input type="radio" name="ocf_setup['.$num.'][arrange]" value="block"'.$arrange_block.' /> 縦並び</label>'."\n";
		$tag .= $tb."\t\t\t".'</td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t".'</table>'."\n";
		$tag .= $this->res_ocf_box_button();
		$tag .= $tb."".'</div>'."\n";
		
		return $tag;
	}
	
	/* FUNC_TAG. redio set */
	private function res_ocf_radio_set( $num = 'sample', $disp = 'sample', $arr = array() ) {
		
		if( $disp == 'disp_db_option' ) {
			$field_name     = $arr['field_name'];
			$input_name     = $arr['input_name'];
			$caption        = $arr['caption'];
			
			$list_arr       = $arr['list'];
			
			$list_selected  = ( $arr['selected'] ) ? $arr['selected'] : 'list_0';
			
			$arrange_inline = ( $arr['arrange'] == 'inline' )? ' checked="checked"': '';
			$arrange_block  = ( $arr['arrange'] == 'block' )? ' checked="checked"': '';
		}
		else {
			$arrange_block  = ' checked="checked"';
		}
		
		$tag = '';
		$tb = "\t\t\t";
		
		$tag .= $tb."\t".'<div class="ocf_setup_box ocf_radio_set" title="radio">'."\n";
		$tag .= $tb."\t".'<h4>'.( $num + 1 ).' : radio</h4>'."\n";
		$tag .= $tb."\t".'<input type="hidden" name="ocf_setup['.$num.'][type]" value="radio" />'."\n";
		$tag .= $tb."\t".'<table class="ocf_table radio_table" title="text">'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">フィールド名</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><input type="text" name="ocf_setup['.$num.'][field_name]" value="'.$field_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row"> input_name</th>'."\n";
		$tag .= $tb."\t\t\t".'<td class="input_name_field"><input type="text" name="ocf_setup['.$num.'][input_name]" value="'.$input_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">補足説明文</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><textarea name="ocf_setup['.$num.'][caption]">'.$caption.'</textarea></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">選択項目</th>'."\n";
		$tag .= $tb."\t\t\t".'<td>'."\n";
		$tag .= $tb."\t\t\t\t".'<ul class="ocf_list_box">'."\n";
		$tag .= $this->res_ocf_radio_list( 'disp_db_option', $list_arr, $num, $list_selected );
		$tag .= $tb."\t\t\t\t".'</ul>'."\n";
		$tag .= $tb."\t\t\t".'</td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">並べ方</th>'."\n";
		$tag .= $tb."\t\t\t".'<td>'."\n";
		$tag .= $tb."\t\t\t\t".'<label><input type="radio" name="ocf_setup['.$num.'][arrange]" value="inline"'.$arrange_inline.' /> 横並び</label>'."\n";
		$tag .= $tb."\t\t\t\t".'<label><input type="radio" name="ocf_setup['.$num.'][arrange]" value="block"'.$arrange_block.' /> 縦並び</label>'."\n";
		$tag .= $tb."\t\t\t".'</td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t".'</table>'."\n";
		$tag .= $this->res_ocf_box_button();
		$tag .= $tb."".'</div>'."\n";
		
		return $tag;
	}
	
	/* FUNC_TAG. select set */
	private function res_ocf_select_set( $num = 'sample', $disp = 'sample', $arr = array() ) {
		
		if( $disp == 'disp_db_option' ) {
			$field_name     = $arr['field_name'];
			$input_name     = $arr['input_name'];
			$caption        = $arr['caption'];
			
			$list_arr       = $arr['list'];
			
			$list_selected  = ( $arr['selected'] ) ? $arr['selected'] : 'list_0';
		}
		
		$tag = '';
		$tb = "\t\t\t";
		
		$tag .= $tb."".'<div class="ocf_setup_box ocf_select_set" title="select">'."\n";
		$tag .= $tb."\t".'<h4>'.( $num + 1 ).' : select</h4>'."\n";
		$tag .= $tb."\t".'<input type="hidden" name="ocf_setup['.$num.'][type]" value="select" />'."\n";
		$tag .= $tb."\t".'<table class="ocf_table select_table" title="text">'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">フィールド名</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><input type="text" name="ocf_setup['.$num.'][field_name]" value="'.$field_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row"> input_name</th>'."\n";
		$tag .= $tb."\t\t\t".'<td class="input_name_field"><input type="text" name="ocf_setup['.$num.'][input_name]" value="'.$input_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">補足説明文</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><textarea name="ocf_setup['.$num.'][caption]">'.$caption.'</textarea></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t\t".'<tr>'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">選択項目</th>'."\n";
		$tag .= $tb."\t\t\t".'<td>'."\n";
		$tag .= $tb."\t\t\t\t".'<ul class="ocf_list_box">'."\n";
		$tag .= $this->res_ocf_select_list( 'disp_db_option', $list_arr, $num, $list_selected );
		$tag .= $tb."\t\t\t\t".'</ul>'."\n";
		$tag .= $tb."\t\t\t".'</td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t".'</table>'."\n";
		$tag .= $this->res_ocf_box_button();
		$tag .= $tb."".'</div>'."\n";
		
		return $tag;
	}
	
	/* FUNC_TAG. h5 set */
	private function res_ocf_h5_set( $num = 'sample', $disp = 'sample',$arr = array() ) {
		
		if( $disp == 'disp_db_option' ) {
			$h5_name = $arr['h5_name'];
		}
		
		$tag = '';
		$tb = "\t\t\t";
		
		$tag .= $tb."".'<div class="ocf_setup_box ocf_h5_set" title="h5">'."\n";
		$tag .= $tb."\t".'<h4>'.( $num + 1 ).' : h5</h4>'."\n";
		$tag .= $tb."\t".'<input type="hidden" name="ocf_setup['.$num.'][type]" value="h5" />'."\n";
		$tag .= $tb."\t".'<table class="ocf_table h5_table" title="text">'."\n";
		$tag .= $tb."\t\t\t".'<th scope="row">見出し名</th>'."\n";
		$tag .= $tb."\t\t\t".'<td><input type="text" name="ocf_setup['.$num.'][h5_name]" value="'.$h5_name.'" class="regular-text" /></td>'."\n";
		$tag .= $tb."\t\t".'</tr>'."\n";
		$tag .= $tb."\t".'</table>'."\n";
		$tag .= $this->res_ocf_box_button();
		$tag .= $tb."".'</div>'."\n";
		
		return $tag;
	}
	
	### OCF LIST
	
	/* FUNC_TAG_LIST for image */
	private function res_ocf_image_list( $disp, $list_arr, $num = ''  ) {
		
		$tag = '';
		$tb = "\t\t\t\t\t\t\t\t";
		$count = ( $disp == 'disp_db_option' && count( $list_arr ) > 0 ) ? count( $list_arr ) : 1 ;
		
		for( $i = 0; $i < $count; $i++ ) {
			
			$thum_type_name = $list_arr[ $i ]['thum_type'];
			$selected[ $thum_type_name ] = ' selected="selected"';
			
			$tag .= $tb."".'<li class="ocf_image_list clearfix">'."\n";
			$tag .= $tb."\t".'<p>W:<input type="text" name="ocf_setup['.$num.'][list]['.$i.'][thum_size_w]" value="'.$list_arr[$i]['thum_size_w'].'" class="small-text list_input_each_val" />px　</p>'."\n";
			$tag .= $tb."\t".'<p>H:<input type="text" name="ocf_setup['.$num.'][list]['.$i.'][thum_size_h]" value="'.$list_arr[$i]['thum_size_h'].'" class="small-text list_input_each_val" />px　</p>'."\n";
			$tag .= $tb."\t".'<select name="ocf_setup['.$num.'][list]['.$i.'][thum_type]" class="list_input_each_val">'."\n";
			$tag .= $tb."\t\t".'<option value="fit"'.$selected['fit'].' class="fit">サイズにフィット</option>'."\n";
			$tag .= $tb."\t\t".'<option value="resize"'.$selected['resize'].' class="resize">サイズ内に縮小</option>'."\n";
			$tag .= $tb."\t\t".'<option value="wfit"'.$selected['wfit'].' class="wfit">横幅にあわせて縮小</option>'."\n";
			$tag .= $tb."\t".'</select>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_btn"><a href="#add" class="ocf_list_add_btn"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_list_btn_add.png" alt="add" width="26" height="20" /></a></p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_btn"><a href="#delete" class="ocf_list_delete_btn"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_list_btn_delete.png" alt="delete" width="26" height="20" /></a></p>'."\n";
			$tag .= $tb."".'</li>'."\n";
		}
		
		return $tag;
	}
	
	/* FUNC_TAG_LIST for checkbox */
	private function res_ocf_checkbox_list( $disp, $list_arr, $num = ''  ) {
		
		$tag = '';
		$tb = "\t\t\t\t\t\t\t\t";
		$count = ( $disp == 'disp_db_option' && count( $list_arr ) > 0 ) ? count( $list_arr ) : 1 ;
								
		for( $i = 0; $i < $count; $i++ ) {
			
			$checked = ( $list_arr[$i]['checked'] == 'checked' )? ' checked="checked"': '';
			
			$tag .= $tb."".'<li class="ocf_checkbox_list clearfix">'."\n";
			$tag .= $tb."\t".'<p>値:<input type="text" name="ocf_setup['.$num.'][list]['.$i.'][value]" value="'.$list_arr[$i]['value'].'" class="middle_text list_input_each_val" />　</p>'."\n";
			$tag .= $tb."\t".'<p>ラベル:<input type="text" name="ocf_setup['.$num.'][list]['.$i.'][label]" value="'.$list_arr[$i]['label'].'" class="middle_text list_input_each_val" />　</p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_check"><label><input type="checkbox" name="ocf_setup['.$num.'][list]['.$i.'][checked]" value="checked"'.$checked.' class="list_input_each_val" /> checked　</label></p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_btn"><a href="#add" class="ocf_list_add_btn"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_list_btn_add.png" alt="add" width="26" height="20" /></a></p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_btn"><a href="#delete" class="ocf_list_delete_btn ocf_noaction"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_list_btn_delete.png" alt="delete" width="26" height="20" /></a></p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_btn"><a href="#up" class="ocf_list_up_btn ocf_noaction"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_list_btn_uo.png" alt="up" width="26" height="20" /></a></p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_btn"><a href="#down" class="ocf_list_down_btn ocf_noaction"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_list_btn_down.png" alt="down" width="26" height="20" /></a></p>'."\n";
			$tag .= $tb."".'</li>'."\n";
		}
		
		return $tag;
	}
	
	/* FUNC_TAG_LIST for radio */
	private function res_ocf_radio_list( $disp, $list_arr, $num = '',  $list_selected = '' ) {
		
		$tag = '';
		$tb = "\t\t\t\t\t\t\t\t";
		$count = ( $disp == 'disp_db_option' && count( $list_arr ) > 0 ) ? count( $list_arr ) : 1 ;
								
		for( $i = 0; $i < $count; $i++ ) {
			
			$selected_num = str_replace( 'list_', '', $list_selected );
			$selected[$selected_num] = ' checked="checked"';
		
			$tag .= $tb."".'<li class="ocf_radio_list clearfix">'."\n";
			$tag .= $tb."\t".'<p>値:<input type="text" name="ocf_setup['.$num.'][list]['.$i.'][value]" value="'.$list_arr[$i]['value'].'" class="middle_text list_input_each_val" />　</p>'."\n";
			$tag .= $tb."\t".'<p>ラベル:<input type="text" name="ocf_setup['.$num.'][list]['.$i.'][label]" value="'.$list_arr[$i]['label'].'" class="middle_text list_input_each_val" />　</p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_check"><label><input type="radio" name="ocf_setup['.$num.'][selected]" value="list_'.$i.'"'.$selected[$i].' class="list_input_common_val" /> checked　</label></p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_btn"><a href="#add" class="ocf_list_add_btn"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_list_btn_add.png" alt="add" width="26" height="20" /></a></p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_btn"><a href="#delete" class="ocf_list_delete_btn ocf_noaction"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_list_btn_delete.png" alt="delete" width="26" height="20" /></a></p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_btn"><a href="#up" class="ocf_list_up_btn ocf_noaction"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_list_btn_uo.png" alt="up" width="26" height="20" /></a></p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_btn"><a href="#down" class="ocf_list_down_btn ocf_noaction"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_list_btn_down.png" alt="down" width="26" height="20" /></a></p>'."\n";
			$tag .= $tb."".'</li>'."\n";
		}
		
		return $tag;
	}
	
	/* FUNC_TAG_LIST for select */
	private function res_ocf_select_list( $disp, $list_arr, $num = '',  $list_selected = '' ) {
		
		$tag = '';
		$tb = "\t\t\t\t\t\t\t\t";
		$count = ( $disp == 'disp_db_option' && count( $list_arr ) > 0 ) ? count( $list_arr ) : 1 ;
								
		for( $i = 0; $i < $count; $i++ ) {
			
			$selected_num = str_replace( 'list_', '', $list_selected );
			$selected[$selected_num] = ' checked="checked"';
			
			$tag .= $tb."".'<li class="ocf_select_list clearfix">'."\n";
			$tag .= $tb."\t".'<p>値:<input type="text" name="ocf_setup['.$num.'][list]['.$i.'][value]" value="'.$list_arr[$i]['value'].'" class="middle_text list_input_each_val" />　</p>'."\n";
			$tag .= $tb."\t".'<p>ラベル:<input type="text" name="ocf_setup['.$num.'][list]['.$i.'][label]" value="'.$list_arr[$i]['label'].'" class="middle_text list_input_each_val" />　</p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_check"><label><input type="radio" name="ocf_setup['.$num.'][selected]" value="list_'.$i.'"'.$selected[$i].' class="list_input_common_val" /> selected　</label></p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_btn"><a href="#add" class="ocf_list_add_btn"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_list_btn_add.png" alt="add" width="26" height="20" /></a></p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_btn"><a href="#delete" class="ocf_list_delete_btn ocf_noaction"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_list_btn_delete.png" alt="delete" width="26" height="20" /></a></p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_btn"><a href="#up" class="ocf_list_up_btn ocf_noaction"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_list_btn_uo.png" alt="up" width="26" height="20" /></a></p>'."\n";
			$tag .= $tb."\t".'<p class="ocf_list_btn"><a href="#down" class="ocf_list_down_btn ocf_noaction"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_list_btn_down.png" alt="down" width="26" height="20" /></a></p>'."\n";
			$tag .= $tb."".'</li>'."\n";
		}
		return $tag;
	}
	
	### OCF OTHER PARTS
	
	/* FUNC_TAG_PART. box button */
	private function res_ocf_box_button() {
	
		$tag = '';
		$tb = "\t\t\t";
	
		$tag .= $tb."\t".'<ul class="ocf_box_btn_box clearfix">'."\n";
		$tag .= $tb."\t\t".'<li><a href="#delete" class="ocf_box_delete_btn"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_box_btn_delete.png" alt="delete" width="50" height="20"></a></li>'."\n";
		$tag .= $tb."\t\t".'<li><a href="#up" class="ocf_box_up_btn"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_box_btn_up.png" alt="up" width="50" height="20"></a></li>'."\n";
		$tag .= $tb."\t\t".'<li><a href="#down" class="ocf_box_down_btn"><img src="'.OCF_PLUGIN_URL.'/images/setup/ocf_box_btn_down.png" alt="down" width="50" height="20"></a></li>'."\n";
		$tag .= $tb."\t".'</ul>'."\n";
		
		return $tag;
	}
}

?>
