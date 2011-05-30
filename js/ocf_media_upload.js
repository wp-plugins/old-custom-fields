/*	Copyright 2011 Akifumi Nishikawa

	License   : Licensed under the MIT License
	Author    : Akifumi Nishikawa(http://www.duluck.net/)
	Version   : 1.0.1　(for WordPress3.12ja)
	Update    : 2011-04-30
	
	jQuery 1.4.4
	cookie.js
*/

/*-- main --*/

(function($) {
		
	var $uploadMediaItems;
	var $uploadMediaItem;
	var $insideMediaItems;
	var $closeBtn;
		
	// アップローダー内各パーツ
	var $headerBtnTypeUrl;
	var $librarySaveBtn;
	var $gallerySaveBtn;
	var $createSaveBtn;
	
	var $addFileToFieldBtn;
		
	// cookie ID
	var ocfUploadFieldID;
	
	function init() {
		
		create();
		
		// カスタムフィールドからファイルをアップした時のmedia_uploadsイベント
		if( ocfUploadFieldID != '') {
			eventify();
			setup();
		}
	}
	
	/*- createObject -*/
	
	function create() {
		
		$uploadMediaItems   = $('#media-items');
		$uploadMediaItem    = $('div.media-item', $uploadMediaItems);
		$insideMediaItems   = $uploadMediaItems.children();
		$closeBtn           = $('#TB_closeWindowButton');
		
		$headerBtnTypeUrl   = $('#media-upload-header #tab-type_url');
		$librarySaveBtn     = $('#library-form > p.ml-submit');
		$gallerySaveBtn     = $('#gallery-form > p.ml-submit');
		$createSaveBtn      = $('#file-form > p.savebutton > input');

		$addFileToFieldBtn  = $('#media-items input.ocf_add_file_to_field');
		
		ocfUploadFieldID     = $.cookie('ocfUploadFieldID');
	}
	
	/*- eventify -*/
	
	function eventify() {
		
		// 外部URLタブの削除
		$headerBtnTypeUrl.remove();
		
		// ｢フィールドに挿入｣ボタンイベント
		$addFileToFieldBtn.live('click', function() {
			
			var $self               = $(this);
			var $currentImgBox      = $self.parents('div.media-item');
			var $btnWithAddFileName = $currentImgBox.find('tr.url > td.field > button.urlfile');
			var currentFileUrl      = $btnWithAddFileName.attr('title');
			var currentFileID       = $currentImgBox.attr('id').match(/[0-9]+$/)[0];
			
			$.cookie('currentFileUrl', currentFileUrl);
			$.cookie('currentFileID', currentFileID);
		});
	}
	
	/*- setup -*/
	function setup() {
		
		// 不要なアップローダーの項目削除
		$uploadMediaItem.each(function() {
															 
			var $self = $(this);
			var id = $self.attr('id');
			
			$self
				.find('tr.align, tr.post_content, tr.post_excerpt, tr.image_alt, tr.url, tr.image-size, tr.submit a:first, tr.submit input, input#insertonlybutton')
				.hide()
			.end()
				.find('tr.submit td:eq(1)')
					.prepend('<input type="submit" title="#' + id + '" class="button ocf_add_file_to_field" value="ファイル決定">')
					.append('<p>｢ファイル決定｣し、画面を閉じてください。</p>');
				
			$librarySaveBtn.hide();
			$gallerySaveBtn.hide();
		});
		
		// ｢全てを保存｣ボタンの削除
		$createSaveBtn.hide();
	}
	
	$(function() {
		init();
	});

})(jQuery);
