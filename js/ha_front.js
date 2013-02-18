  (function($) {
       $().ready(function() {
       	
       	var HAHNAIR = {

				cookie_name: 'hahnair-redirect',
				airlines_http: 'http://hahnair.aero',
				agencies_http: 'http://149.62.57.33/hahnair/node/',

				rememberChoice : function() {
					$('input[name="redirect-to"]').click(function() {

						$.cookie(HAHNAIR.cookie_name, null);

						if ($(this).attr('checked')) {
							switch ($(this).val()) {
								case 'airlines':
									$('#agencies .redirect-div input')[0].checked = false;
									$.cookie(HAHNAIR.cookie_name, HAHNAIR.airlines_http, { expires: 7, path: '/' });
									break;
								case 'agencies':
									$('#airlines .redirect-div input')[0].checked = false;
									$.cookie(HAHNAIR.cookie_name, HAHNAIR.agencies_http, { expires: 7, path: '/' });
									break;
								default:
									break;
							}
						}

					});
				},
				leaveBox:function(){
				  
				  
				},

				animateBoxes : function() {
					var handleAnimation = function(divIn, divOut) {
					  $('#'+divIn+' .ha-window-left .image >img').css({'marginLeft':'0px'});
						$('#'+divOut+' .ha-window-left .image > img').css({'marginLeft':'-142px'});
						$('.ha-infobar').css({'opacity':'1'});
					
						$('.splash-box div').stop();
						$('#'+divOut+' .ha-infobar').hide();
						$('#'+divOut+' .ha-window-left .image .image-text').hide();
						
						$('#'+divIn).animate({width: '708px'});

						$('#'+divIn+' .ha-window-left').animate({width: '700px'});
						$('#'+divIn+' .ha-window-left .image').animate({width: '677px'}, function() {
							$('#'+divIn+' .ha-window-left .image .image-text').show();
							$('#' + divIn+' .ha-infobar').show();
						});

						$('#'+divOut).animate({width: '230px'});
						$('#'+divOut+' .ha-window-left').animate({width: '222px'});
						$('#'+divOut+' .ha-window-left .image').animate({width: '200px'});
					}

					var airlinesHandler = function(e) {
						
						handleAnimation('airlines', 'agencies');
					}

					var agenciesHandler = function(e) {
						handleAnimation('agencies', 'airlines');
					}

					$('#airlines').mouseenter(airlinesHandler);
					$('#agencies').mouseenter(agenciesHandler);
				},

				init : function() {

					HAHNAIR.rememberChoice();
					HAHNAIR.animateBoxes();
					//HAHNAIR.leaveBox();
					
				}
			};

			$("body").ready(function() {
				var bgImg = new Image();
				bgImg.src = 'images/body_bg.png';
			});
			$(function() {
				HAHNAIR.init();
			});

 });
})(jQuery);

			
