/*	Copyright 2011 Akifumi Nishikawa

	License   : Licensed under the MIT License
	Author    : Akifumi Nishikawa(http://www.duluck.net/)
	Original  : Tomohiro Okuwaki(http://www.tinybeans.net/blog/)
	Version   : 1.0.4　(for WordPress3.12ja)
	Update    : 2011-05-11
	
	jQuery 1.4.4
	cookie.js
*/

/* main */

(function($) {
	
	var $ocfBox;
	var $ocfAddMediaBtn;
	var $imageCancelBtn;
	var $publishBtn;
	var $textFieldInput;
	var $imageField;
	var $uploadField;
	
	// media uploader
	var $TBset;
	
	// img_pass
	var adminUrl  = location.href;
	var imagesUrl = adminUrl.replace(/(http.+)(wp-admin)(.+)/,'$1') + 'wp-content/plugins/old-custom-fields/images/';
	var cancelPng = imagesUrl + 'cancel.png';
	var mustPng   = imagesUrl + 'must.png';
	
	function init() {
		create();
		eventify();
		setup();
	}
	
	function create() {
		
		$ocfBox          = $('#old_custom_fields');
		$ocfAddMediaBtn  = $('.ocf_add_media a', $ocfBox);
		$cancelBtn       = $('p.ocf_image_cancel a, p.ocf_file_cancel a', $ocfBox);
		$publishBtn      = $('#publishing-action #original_publish, #publishing-action #publish');
		$textFieldInput  = $('.postbox.text', $ocfBox).find('input.data');
		$imageField      = $('.postbox.image', $ocfBox);
		$uploadField     = $('.postbox.image, .postbox.file', $ocfBox);
		
		// media uploader
		$TBset          = $('#TB_overlay').add('#TB_window');
	}
	
	function eventify() {
		
		// イメージ・ファイルアップのliveイベント
		$ocfAddMediaBtn.live('click', function() {
											   
			var $self            = $(this);
			var $ocfUploadBox    = $self.parents('div.image, div.file');
			var ocfUploadFieldID = $ocfUploadBox.attr('id');
			
			$.cookie('ocfUploadFieldID', ocfUploadFieldID); // 該当イメージフィールドのidをcookieに保存
			
			if($('#media-buttons #add_media')) {
				$('#media-buttons #add_media').click(); // WordPressアップローダー起動 ※
			} else {
				$('#media-buttons a').click();
			}
			
			var $TBcloseSet = $('#TB_window #TB_closeWindowButton img, #TB_overlay');
		
			// アップローダーを閉じるときにcookie 削除
			$TBcloseSet.click(function(){
				
				// cookieからidと値を取得して変数に代入後にリセット
				
				var currentFileUrl = $.cookie('currentFileUrl');
				var currentFileID  = $.cookie('currentFileID');
				var decodeFileUrl  = decodeURI(currentFileUrl);
				var encodeFileUrl  = encodeURI(currentFileUrl);
				
				if(currentFileUrl) {
					alert('ファイルを保存しました。');
				} else {
					alert('ファイルは保存されていません。');
				}
				
				ocfUploadFieldID = '#' + ocfUploadFieldID;
				
				$.cookie('ocfUploadFieldID', '');
				$.cookie('currentFileUrl','');
				$.cookie('currentFileID','');
				
				// カスタムフィールドに値を入れる
				if(currentFileUrl) {
					
					$(ocfUploadFieldID).find('p.up_file_value_box').text(decodeFileUrl);
					$(ocfUploadFieldID).find('input.data').attr('value', encodeFileUrl);
					$(ocfUploadFieldID).find('input.thumdata').each(function() {
						var $self = $(this);
						var selfRel = $self.attr('rel');
						var thumUrl;
						
						thumUrl = encodeFileUrl.match(/(.+)(\.[a-z]+)$/)[1] + '_' + selfRel + encodeFileUrl.match(/(.+)(\.[a-z]+)$/)[2];
						$self.attr('value', thumUrl);
					});
					
					// ファイルの種類のアイコンとキャンセルボタンを表示
					var mediaType = getMediaType(currentFileUrl);
					
					if(mediaType) {
						$(ocfUploadFieldID).find('p.up_file_value_box').css('background','url(' + imagesUrl + mediaType + '.png) no-repeat 3px center').css('padding-left','25px');
						$(ocfUploadFieldID).find('span.ocf_imagefield_thumb').html('<img src="' + decodeFileUrl + '" width="150" />');
					} else {
						$(ocfUploadFieldID).find('p.up_file_value_box').removeAttr('style');          
					}
					
					// キャンセルボタン表示
					$(ocfUploadFieldID).find('p.ocf_image_cancel, p.ocf_file_cancel').show().children().children('img').attr('src', cancelPng);
					
					// 画像(ファイル)を[追加 > 変更]
					var fileTypeName;
					var fileTypeImg;
				
					if($ocfUploadBox.hasClass('ocf_image_cancel')) {
						fileTypeName = '画像';
					fileTypeImg = 'image';
					} else {
						fileTypeName = 'ファイル';
					fileTypeImg = 'other';
					}
					$(ocfUploadFieldID).find('p.ocf_add_media a').html(fileTypeName + 'を変更：<img src="images/media-button-' + fileTypeImg +'.gif" alt="' + fileTypeName + 'を変更">');
				}
			});
		});

		// 「キャンセル」ボタンのイベント
		$cancelBtn.live('click', function() {
		
			$self = $(this).parent();
			
			$self.prevAll('span').find('img').fadeOut('slow',function(){
				$(this).remove();
			});
			$self.prevAll('p.ocf_input').children('input').val('');
			$self.prevAll('p.up_file_value_box').text('').removeAttr('style');
			if($self.hasClass('ocf_image_cancel')) {
				fileTypeName = '画像';
			} else {
				fileTypeName = 'ファイル';
			}
			$self.nextAll('p.ocf_add_media').children('a').html(fileTypeName + 'を追加：<img src="images/media-button-image.gif" alt="' + fileTypeName + 'を追加">');
			$self.hide();
		});
		
		// 「公開」ボタンの必須チェックイベント
		$publishBtn.live('click', function() {
																									  
			var $mustBox    = $('div.postbox.must:visible');						  
			var slug        = $('#edit-slug-box #sample-permalink').text();
			var check       = 0;
			
			if(slug) {
				
				$mustBox.each(function() {
					
					var $self         = $(this);
					var $selfInput    = $self.find('input.data, textarea');
					var boxName       = $self.find('h4').text();
					var inputCheck    = ($self.hasClass('text') || $self.hasClass('image') || $self.hasClass('file') || $self.hasClass('textarea'));
					
					if(inputCheck && !$selfInput.val()) {
						alert(boxName + 'は必須項目です。');
						$self.css({'background-color':'#FFDDDD'});
						check = 1;
					}
				});
			
				if(check) {
					return false;
				} else {
					return true;
				}
			} else {
				return true;
			}
		});
		
/*- eventify functions -*/

	}
	
/*- main functions -*/
		
	//MAIN_FUNC : ファイルタイプ判定
	function getMediaType(str) {
		
		var mediaType = str.match(/[a-z]{2,5}$/i);
		
		if((mediaType == 'pdf')||(mediaType == 'PDF')) {
			mediaType = 'pdf';
		} else if((mediaType == 'jpg')||(mediaType == 'JPG')||(mediaType == 'gif')||(mediaType == 'GIF')||(mediaType == 'png')||(mediaType == 'PNG')) {
			mediaType = 'image';
		} else if((mediaType == 'csv')||(mediaType == 'CSV')) {
			mediaType = 'csv';
		} else {
			mediaType = 'file';
		}
		
		return mediaType;
	}
	
	
	// 初期設定
	function setup() {
	
		// 写真の表示
		$uploadField.each(function() {
		
			var $self          = $(this);
			var $selfTextBox   = $self.find('p.up_file_value_box');
			var $selfInputBox  = $self.find('input.data');
			var currentFileUrl = $selfTextBox.text();
			var decodeFileUrl  = decodeURI(currentFileUrl);
			var encodeFileUrl  = encodeURI(currentFileUrl);
			
			// カスタムフィールドに値を入れる
			if(currentFileUrl) {
				
				$selfTextBox.text(decodeFileUrl);
				$selfInputBox.attr('value', encodeFileUrl);
				
				// ファイルの種類のアイコンとキャンセルボタンを表示
				var mediaType = getMediaType(encodeFileUrl);
				
				if(mediaType) {
					$selfTextBox.css('background','url(' + imagesUrl + mediaType + '.png) no-repeat 3px center').css('padding-left','25px');
					$self.find('span.ocf_imagefield_thumb').html('<img src="' + encodeFileUrl + '" width="150" />');
				} else {
					$selfTextBox.removeAttr('style');          
				}
				
				$self.find('p.ocf_image_cancel, p.ocf_file_cancel').show().children().children('img').attr('src', cancelPng);
				
				var fileTypeName;
				var fileTypeImg;
				
				if($self.hasClass('image')) {
					fileTypeName = '画像';
					fileTypeImg = 'image';
				} else {
					fileTypeName = 'ファイル';
					fileTypeImg = 'other';
				}
				$self.find('p.ocf_add_media a').html(fileTypeName + 'を変更：<img src="images/media-button-' + fileTypeImg +'.gif" alt="' + fileTypeName + 'を変更">');
			}
		});
				
		// 必須項目の表示
		$('div.postbox.must').find('h4').css({
			'padding-left': '20px',
			'background': 'url(' + mustPng + ') no-repeat left top'
		});
	}
	
	$(function() {
		init();
	});

})(jQuery);
