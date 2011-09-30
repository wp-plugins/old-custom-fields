/*	Copyright 2011 Akifumi Nishikawa

	License   : Licensed under the MIT License
	Author    : Akifumi Nishikawa(http://www.oldoffice.net/)
	Thanks    : Genta Takamura
	Version   : 1.1.6　(for WordPress3.12ja)
	Update    : 2011-9-17
	
	jQuery 1.4.4
*/


/*-- plugins --*/

// target disp flash
(function($) {

	$.fn.flash = function(options) {
		
		var opts = $.extend({}, $.fn.flash.defaults, options);
		
		return this.each(function() {
			var $self = $(this);
			$self
				.stop(true, true) // 連打実行を防ぐ
				.css({
					opacity: 0
				})
				.animate({opacity: 1}, {duration: opts.time});
		});
	};
	
	$.fn.flash.defaults = {
		time: 500
	};

})(jQuery);

// check for code
(function($) {

	$.fn.jQalert = function(text, options) {
		
		var opts = $.extend({}, $.fn.jQalert.defaults, options);
		
		return this.each(function() {
			var $self = $(this);
			alert(text);
		});
	};
	
	$.fn.jQalert.defaults = {
	};

})(jQuery);


/*-- main --*/

(function($) {
	
	var	$ocfTextSet,
		$ocfTextareaSet,
		$ocfImageSet,
		$ocfFileSet,
		$ocfCheckboxSet,
		$ocfRadioSet,
		$ocfSelectSet,
		$ocfH5Set,
		$ocfNoSetting,
		
		$ocfAddBtnBox,
		$ocfSampleCont,
		$ocfSampleList,
		$ocfMainCont,
		
		$ocfBoxDeleteBtn,
		$ocfBoxUpBtn,
		$ocfBoxDownBtn,
		
		$ocfListBox,
		$ocfListDeleteBtn,
		$ocfListUpBtn,
		$ocfListDownBtn,
		$ocfListAddBtn,
		
		$ocfSetupForm,
		$ocfSaveBtn;
	
	function init() {
		create();
		eventify();
		setup();
	}
	
	/*- Create Object -*/
	
	function create() {
		
		// ocf_set
		$ocfTextSet       = $('.ocf_text_set');
		$ocfTextareaSet   = $('.ocf_textarea_set');
		$ocfImageSet      = $('.ocf_image_set');
		$ocfFileSet       = $('.ocf_file_set');
		$ocfCheckboxSet   = $('.ocf_checkbox_set');
		$ocfRadioSet      = $('.ocf_radio_set');
		$ocfSelectSet     = $('.ocf_select_set');
		$ocfH5Set         = $('.ocf_h5_set');
		$ocfNoSetting     = $('#ocf_no_setting');
		
		// cont
		$ocfAddBtnBox     = $('#ocf_add_btn_box');
		$ocfSampleCont    = $('#ocf_sample_cont');
		$ocfSampleList    = $('#ocf_sample_list');
		$ocfMainCont      = $('#ocf_main_cont');
		
		// box_btn
		$ocfBoxDeleteBtn  = $('.ocf_box_delete_btn', '#ocf_main_cont');
		$ocfBoxUpBtn      = $('.ocf_box_up_btn:not(.ocf_noaction)', '#ocf_main_cont');
		$ocfBoxDownBtn    = $('.ocf_box_down_btn:not(.ocf_noaction)', '#ocf_main_cont');
		
		// list_btn
		$ocfListBox       = $('.ocf_list_box', '#ocf_main_cont');
		$ocfListDeleteBtn = $('.ocf_list_delete_btn:not(.ocf_noaction)', '#ocf_main_cont');
		$ocfListAddBtn    = $('.ocf_list_add_btn:not(.ocf_noaction)', '#ocf_main_cont');
		$ocfListUpBtn     = $('.ocf_list_up_btn:not(.ocf_noaction)', '#ocf_main_cont');
		$ocfListDownBtn   = $('.ocf_list_down_btn:not(.ocf_noaction)', '#ocf_main_cont');
		
		// form
		$ocfSetupForm     = $('#ocf_setup_form');
		$ocfSaveButton    = $('#ocf_save_btn');
		
	}
	
	/*- イベント -*/
	
	function eventify() {
	
		// OCF_SETの追加
		$('a', $ocfAddBtnBox).live('click', function(e) {
			
			var	$selfList  = $(this).parent(),
				$sibList   = $selfList.siblings(),
				index      = $selfList.add($sibList).index($selfList),
				$box       = $('.ocf_setup_box', $ocfSampleCont),
				$targetBox = $box.eq(index),
				radioArr   = new Array();
			
			// チェック状態の一時保存
			if($('.ocf_setup_box:eq(0)', $ocfMainCont).length > 0) {
				saveRadioChecked(radioArr);
			}
			
			// hide no_setting
			$ocfNoSetting
				.hide();
				
			// add ocf_set
			$targetBox
				.clone(true)
				.prependTo($ocfMainCont)
				.flash();
			
			resetBoxButton();               // Boxボタンリセット
			resetBoxTitle();                // Boxタイトルリセット
			resetBoxInputAttr();            // Box内 Input Name/Value リセット
			radioCheckedOfAddBox(radioArr); // 追加BOXのCheckedArr追加
			dispRadioChecked(radioArr);     // checked復元
			
		});
		
		// OCF_SETの削除
		$ocfBoxDeleteBtn.live('click',function() {
			
			var	$currentDivBox = $(this).parent().parent().parent(),
				boxIndex       = $('.ocf_setup_box', $ocfMainCont).index($currentDivBox),
				radioArr       = new Array();
		
			conf = confirm('削除しますか?');
			
			// チェック状態の一時保存
			saveRadioChecked(radioArr);
			radioArr.splice(boxIndex, 1);
			
			// DIV削除
			if(conf) {
				$currentDivBox
					.flash()
					.remove();
			}
			
			if($('.ocf_setup_box',$ocfMainCont).length == 0) {
				$ocfNoSetting
					.show()
					.flash();
			}
			
			resetBoxButton();               // Boxボタンリセット
			resetBoxTitle();                // Boxタイトルリセット
			resetBoxInputAttr();            // Box内 Input Name/Value リセット
			dispRadioChecked(radioArr);     // checked復元
		});
		
		// form divのup
		$ocfBoxUpBtn.live('click',function() {
			
			var	$currentDivBox      = $(this).parent().parent().parent(),
				$prevDivBox         = $currentDivBox.prev(),
				$cloneCurrentDivBox = $currentDivBox.clone(true),
				currentCaptionVal   = $('textarea', $currentDivBox).val(),
				boxIndex            = $('.ocf_setup_box', $ocfMainCont).index($currentDivBox),
				radioArr            = new Array(),
				tempArr01           = new Array(),
				tempArr02           = new Array();
			
			// チェック状態の一時保存
			saveRadioChecked(radioArr);
			tempArr01 = radioArr[boxIndex];
			tempArr02 = radioArr[boxIndex - 1];
			radioArr.splice((boxIndex - 1), 2, tempArr01, tempArr02);
			
			$('textarea', $cloneCurrentDivBox)
				.val(currentCaptionVal);
			
			$cloneCurrentDivBox
				.insertBefore($prevDivBox).flash();
				
			$currentDivBox
				.remove();
			
			resetBoxButton();               // Boxボタンリセット
			resetBoxTitle();                // Boxタイトルリセット
			resetBoxInputAttr();            // Box内 Input Name/Value リセット
			dispRadioChecked(radioArr);     // checked復元
		});
		
		// form divのdown
		$ocfBoxDownBtn.live('click',function() {
			
			var	$currentDivBox      = $(this).parent().parent().parent(),
				$nextDivBox         = $currentDivBox.next(),
				$cloneCurrentDivBox = $currentDivBox.clone(true),
				currentCaptionVal   = $('textarea', $currentDivBox).val(),
				boxIndex            = $('.ocf_setup_box', $ocfMainCont).index($currentDivBox),
				radioArr            = new Array(),
				tempArr01           = new Array(),
				tempArr02           = new Array();
			
			// チェック状態の一時保存
			saveRadioChecked(radioArr);
			tempArr01 = radioArr[boxIndex + 1];
			tempArr02 = radioArr[boxIndex];
			radioArr.splice((boxIndex), 2, tempArr01, tempArr02);
			
			$('textarea', $cloneCurrentDivBox)
				.val(currentCaptionVal);
			
			$cloneCurrentDivBox
				.insertAfter($nextDivBox).flash();
				
			$currentDivBox
				.remove();
			
			resetBoxButton();               // Boxボタンリセット
			resetBoxTitle();                // Boxタイトルリセット
			resetBoxInputAttr();            // Box内 Input Name/Value リセット
			dispRadioChecked(radioArr);     // checked復元
		});
		
		// image,checkbox,radio,select のadd list
		$ocfListAddBtn.live('click',function() {
			
			var	$currentUlBox = $(this).parent().parent().parent(),
				$currentLiBox = $(this).parent().parent();
		
			// list追加
			if($currentLiBox.hasClass('ocf_image_list')) {
				$ocfSampleList.children('li.ocf_image_list')
					.clone()
					.appendTo($currentUlBox).flash();
			} else if ($currentLiBox.hasClass('ocf_checkbox_list')) {
				$ocfSampleList.children('li.ocf_checkbox_list')
					.clone()
					.appendTo($currentUlBox).flash();
			} else if ($currentLiBox.hasClass('ocf_radio_list')) {
				$ocfSampleList.children('li.ocf_radio_list')
					.clone()
					.appendTo($currentUlBox).flash();
			} else if ($currentLiBox.hasClass('ocf_select_list')) {
				$ocfSampleList.children('li.ocf_select_list')
					.clone()
					.appendTo($currentUlBox).flash();
			}
			
			resetListButton($currentUlBox); 　　　// List内 Buttonリセット
			resetInputNameOfList($currentUlBox);  // List内 Input Name リセット
			resetInputValueOfList($currentUlBox); // List内 Input Value リセット
		});
		
		// image,checkbox,radio,select のdelete list
		$ocfListDeleteBtn.live('click',function() {
			
			var	$currentUlBox = $(this).parent().parent().parent(),
				currentListNum = $currentUlBox.children('li').length;
			
			// list削除
			$(this).parent().parent()
				.remove();
			
			resetListButton($currentUlBox); 　　　// List内 Buttonリセット
			resetInputNameOfList($currentUlBox);  // List内 Input Name リセット
			resetInputValueOfList($currentUlBox); // List内 Input Value リセット
			
			if(currentListNum == 2) {
				restListDeleteBtn($currentUlBox); // 1個になったときの削除ボタン
			}
		});

		// image,checkbox,radio,select のup list
		$ocfListUpBtn.live('click',function() {
			
			var	$currentUlBox = $(this).parent().parent().parent(),
				$current      = $(this).parent().parent(),
				$prev         = $current.prev(),
				$cloneCurrent  = $current.clone(),
				$clonePrev     = $prev.clone();

			$cloneCurrent
				.insertBefore($prev).flash();
				
			$current
				.remove();
			
			resetListButton($currentUlBox); 　　　// List内 Buttonリセット
			resetInputNameOfList($currentUlBox);  // List内 Input Name リセット
			resetInputValueOfList($currentUlBox); // List内 Input Value リセット
		});
		
		// image,checkbox,radio,select のdown list
		$ocfListDownBtn.live('click',function() {
			
			var	$currentUlBox = $(this).parent().parent().parent(),
				$current      = $(this).parent().parent(),
				$next         = $current.next(),
				$cloneCurrent = $current.clone(),
				$cloneNext    = $next.clone();
			
			$cloneCurrent
				.insertAfter($next).flash();
				
			$current
				.remove();
			
			resetListButton($currentUlBox); 　　　// List内 Buttonリセット
			resetInputNameOfList($currentUlBox);  // List内 Input Name リセット
			resetInputValueOfList($currentUlBox); // List内 Input Value リセット
		});
		
		// image(setup_box)でサイズタイプ選択したときのイベント
		$('select', $ocfImageSet).change(function() {
												  
			var $self               = $(this);
			var $currentLiBox       = $self.parent('li.ocf_image_list');
			var $optionSelectedWfit = $self.children('option.wfit');
			var $inputHsize         = $self.parent().children('p').children('input').eq(0);
			var $inputWsize         = $self.parent().children('p').children('input').eq(1);
			
			if($optionSelectedWfit.attr('selected') == true) {
				$inputHsize.attr('disabled', false);
				$inputWsize.attr('disabled', true).attr('value', 'auto');
			} else {
				$inputHsize.attr('disabled', false);
				$inputWsize.attr('disabled', false);
			}
		})
		.change();
		
		// checkbox(setup_box)で出力値=配列を選択したときのイベント
		$('td.callSetupTd label', $ocfCheckboxSet).live('click', function() {
																	  
			var	$self        = $(this),
				$parentTd    = $self.parent(),
				labelIndex   = $parentTd.children('label').index($self),
				$targetInput = $parentTd.children('input');
			
			if(labelIndex == 0) {
				$targetInput.attr('disabled', true);
			} else {
				$targetInput.attr('disabled', false);
			}
		});
		
		
		// formのサブミットイベント
		$ocfSaveButton.click('submit', function() {
			
			var inputNameArr = new Array();
			var checkSameInputName = 1;
			var $inputNameField = $('.input_name_field > input', '#ocf_main_cont');
			
			$inputNameField.each( function() {
				
				var $self = $(this);
				inputNameArr.push( $self.attr('value') );
			});
			
			for( i=0; i < inputNameArr.length; i++ ) {
				
				var check1Bite = inputNameArr[ i ].match(/^[a-zA-Z][a-zA-Z0-9]*$/);
				
				if( ! inputNameArr[ i ] ) {
					checkSameInputName = 'noData';
					break;
				} else if( ! check1Bite ) {
					checkSameInputName = 'oneBite';
					break;
				}else {
					for( j = i + 1; j < inputNameArr.length; j++ ) {
						if( inputNameArr[ i ] == inputNameArr[ j ] ) {
							checkSameInputName = 'sameData';
							break;
						}
					}
				}
			}
			
			if( checkSameInputName == 'noData' ) {
				alert(" [ input_name ] に空欄が存在します");
				return false;
			} else if( checkSameInputName == 'oneBite' ) {
				alert(" [ input_name ] は数字で始まらない英数半角で");
				return false;
			} else if( checkSameInputName == 'sameData' ) {
				alert("同じ [ input_name ] が存在します");
				return false;
			} else {
				return true;
			}
		});


		/* eventify function */
		
		//FUNC. 並び替え時Boxタイトルリセット
		function resetBoxTitle() {
			
			// タイトルの振り直し
			$('.ocf_setup_box', $ocfMainCont).each(function(i) {
				
				var str = (i + 1) + ' : ' + $(this).attr('title'); // 番号 + タイトル
				
				$(this).children('h4').text(str);
				
			});
		}
		
		//FUNC. 並び替え時 各Box内Input Name/Valueリセット
		function resetBoxInputAttr() {
											  
			var $div = $('div.ocf_setup_box', $ocfMainCont);
			
			$div.each(function(i) {
							   
				var	$target           = $(':input', this),
					boxIndex          = i,
					$listInCurrentBox = $('.ocf_list_box > li', this);
					
				$target.each(function(j) {
									  
					var $self = $(this),
						fixedName = '',
						fixedValue = '',
						preFix = '',
						postFix = '',
						$currnetList = $self.parents('.ocf_list_box > li'),
						listIndex = $listInCurrentBox.index($currnetList);
						
					preFix = $self.attr('name').match(/^.+?\[/)[0];
					postFix = $self.attr('name').match(/\]\[[^[]*\]$/)[0];
					
					
					fixedName = preFix + boxIndex + postFix;
					
					if($self.hasClass('list_input_each_val')) {
						fixedName = preFix + boxIndex + '][list][' + listIndex + postFix;
					} else {
						fixedName = preFix + boxIndex + postFix;
					}
					
					$self.attr('name', fixedName);
					
					if($self.hasClass('list_input_common_val')) {
						fixedValue = 'list_' + listIndex;
						$self.attr('value', fixedValue);
					}
					
				});
			});
		}
		
		//FUNC. Listのinput_name リセット
		function resetInputNameOfList($ul) {
		
			var	$currentSetupBox  = $ul.parents('.ocf_setup_box'),
				$list             = $('.ocf_list_box > li', $currentSetupBox),
				boxIndex          = $('.ocf_setup_box', $ocfMainCont).index($currentSetupBox);
			
			$list.each(function(i) {
				
				var	$currentList = $(this),
					$input = $(':input', $currentList);
					
				$input.each(function() {
						
					var	$self        = $(this),
						preFix       = 'ocf_setup[',
						postFix      = '',
						fixedName;
						
					postFix   = $self.attr('name').match(/\]\[[^[]*\]$/)[0];
					
					if($self.hasClass('list_input_each_val')) {
						fixedName = preFix + boxIndex + '][list][' + i + postFix;
					} else {
						fixedName = preFix + boxIndex + postFix;
					}
					
					$self.attr('name', fixedName);
				});
			});
		}
		
		//FUNC. Listのinput_value リセット
		function resetInputValueOfList($ul, $list) {
		
			var	$input            = $('input.list_input_common_val', $list),
				$currentSetupBox  = $ul.parents('.ocf_setup_box'),
				$listInCurrentBox = $('.ocf_list_box > li', $currentSetupBox);
				
			$input.each(function() {
					
				var	$self         = $(this),
					$currnetList  = $self.parents('.ocf_list_box > li'),
					listIndex     = $listInCurrentBox.index($currnetList),
					fixedValue;
				
				fixedValue = 'list_' + listIndex;
				$self.attr('value', fixedValue);
			});
		}
		
		//FUNC. List 削除ボタンリセット
		function restListDeleteBtn($currentUlBox) {
			
			$('.ocf_list_delete_btn',$currentUlBox)
				.addClass('ocf_noaction');
		}
	}
	
	
	/*- 初期設定 -*/
	
	function setup() {
		
		var	$setupBoxOfMain = $('.ocf_setup_box', $ocfMainCont),
			setupBoxNum = $setupBoxOfMain.length;
		
		// no Settingの表示
		if(setupBoxNum > 0) {
			$ocfNoSetting.hide();
		} else {
			$ocfNoSetting.show();
		}
		
		// Box Button リセット
		if(setupBoxNum > 0) {
		
			resetBoxButton();
			
			$setupBoxOfMain.each(function() {
										  
				var	$self = $(this),
					$listBox = $('.ocf_list_box', $self);
				
				if($self.hasClass('ocf_image_set') || $self.hasClass('ocf_checkbox_set') || $self.hasClass('ocf_radio_set') || $self.hasClass('ocf_select_set')) {
					resetListButton($listBox);
				}
			});
		}
	}
	
	
	/*- Global Function -*/
		
	//G_FUNC. Boxボタンリセット
	function resetBoxButton() {
			
		// 上下ボタン終端のイベント調整
		$('.ocf_box_up_btn',$ocfMainCont)
			.removeClass('ocf_noaction')
			.first()
			.addClass('ocf_noaction');
			
		$('.ocf_box_down_btn',$ocfMainCont)
			.removeClass('ocf_noaction')
			.last()
			.addClass('ocf_noaction');
	}
		
	//G_FUNC. Listボタンリセット
	function resetListButton($ul) {
		
		$('a:not(.ocf_list_add_btn)' ,$ul)
			.removeClass('ocf_noaction');
			
		$('a.ocf_list_add_btn' ,$ul)
			.addClass('ocf_noaction');
			
		// 上下ボタン終端のイベント調整
		$('a.ocf_list_up_btn:first',$ul)
			.addClass('ocf_noaction');
			
		$('a.ocf_list_down_btn:last',$ul)
			.addClass('ocf_noaction');
		
		// 追加ボタンの最後を除く
		$('a.ocf_list_add_btn:last',$ul)
			.removeClass('ocf_noaction');
	}
		
	//G_FUNC. BOX追加時のラジオボタンのチェック状態追加
	function radioCheckedOfAddBox(arr) {
		
		var	$addSetUpBox = $('.ocf_setup_box', $ocfMainCont).eq(0);
		
		if($addSetUpBox.hasClass('ocf_text_set') || $addSetUpBox.hasClass('ocf_textarea_set') || $addSetUpBox.hasClass('ocf_image_set') || $addSetUpBox.hasClass('ocf_file_set')) {
			addArr = [false,true];
		} else if($addSetUpBox.hasClass('ocf_checkbox_set')) {
			addArr = [false,true,false,true];
		} else if($addSetUpBox.hasClass('ocf_radio_set')) {
			addArr = [true,false,true];
		} else if($addSetUpBox.hasClass('ocf_select_set')) {
			addArr = [true];
		}
		
		arr.unshift(addArr);
		return arr;
	}
	
	//G_FUNC. BOXのラジオチェック状態の保存
	function saveRadioChecked(arr) {
		
		var	$box = $('div.ocf_setup_box', $ocfMainCont);
		
		$box.each(function(i) {
						   
			var	$selfBox = $(this),
			$radio = $(':radio', $selfBox);
			
			arr[i] = [];
						   
			$radio.each(function(j) {
				
				var	$selfRadio = $(this);
				
				if($selfRadio.attr('checked') === true) {
					arr[i][j] = true;
				} else {
					arr[i][j] = false;
				}
			});
		});
		
		return arr;
	}
	
	//G_FUNC. BOXのラジオチェック状態の復元
	function dispRadioChecked(arr) {
		
		var	$box = $('div.ocf_setup_box', $ocfMainCont);
		
		$box.each(function(i) {
						   
			var	$selfBox = $(this),
			$radio = $(':radio', $selfBox);
						   
			$radio.each(function(j) {
				
				var	$selfRadio = $(this);
				
				if(arr[i][j]) {
					$selfRadio.attr('checked', true);
				}
			});
		});
	}
	
	//G_FUNC. BOXのラジオチェック状態の復元
	
	/*- READY() -*/
	
	$(function() {
		init();
	});
	

})(jQuery);
