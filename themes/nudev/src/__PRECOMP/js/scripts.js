var windowSize = Array(0,0);
var rotators = null;
var iamnavbgs = null;
var contentAreaHeight = 0;
var cNav = null;
var debug = true;
var showSize = true;
var sizeBreak = 900;
var isSafari = /safari/i.test(navigator.userAgent);
var currentPanel = 0;
var panelCount = 3;
var windowWidth = null;
var offset = 0;


	// some of the vars above can be wrapped in a page class check to only load on the homepage



(function(root,$,undefined){
	"use strict";

	$(function(){


		/*Remove the lines below once you are done testing to set break points for various screen sizes*/
		if(showSize){
	    var wi = $(window).width();
	    $("p.testp").text('Screen width is currently: ' + wi + 'px.');

	    $(window).resize(function(){
	      var wi = $(window).width();
	      if (wi <= 576){
	        $("p.testp").text('Screen width is less than or equal to 576px. Width is currently: ' + wi + 'px.');
	      }else if (wi <= 680){
	        $("p.testp").text('Screen width is between 577px and 680px. Width is currently: ' + wi + 'px.');
	      }else if (wi <= 1024){
	        $("p.testp").text('Screen width is between 681px and 1024px. Width is currently: ' + wi + 'px.');
	      }else if (wi <= 1500){
	        $("p.testp").text('Screen width is between 1025px and 1499px. Width is currently: ' + wi + 'px.');
	      }else {
	        $("p.testp").text('Screen width is greater than 1500px. Width is currently: ' + wi + 'px.');
	    	}
	    });
		}
		/* ********************************************** */




		/* ***************************************************************************
		The following is a set of tools and features for all pages as soon as they
		are loaded
		*************************************************************************** */

		// we need to set the main content offset based on: utility nav height, alerts height, and main header height
		$("main").css({
			"margin-top":$("header").outerHeight()
		});





		// if this file has loaded, we want to append an option to let the page know JS is working
		$('body').addClass('nu-js');








		// listen for the user to focus on the search bar so that we can make some small design tweaks if JS is available
		if(parseInt($('form#nu__searchbar-form > div > label').css('left')) > 0){	// not already small
			$('#nu__searchbar').on('focus','form#nu__searchbar-form > div > input',function(e){	// focus
				$('form#nu__searchbar-form > div > label').addClass('focus');
				$('form#nu__searchbar-form > div > button.reset').css({'color':'rgba(255,255,255,1.0)','pointer-events':'auto'});
			});
			$('#nu__searchbar').on('blur','form#nu__searchbar-form > div > input',function(e){	// blur
				if($(this).val() == ''){
					$('form#nu__searchbar-form > div > label').removeClass('focus');
					$('form#nu__searchbar-form > div > button.reset').css({'color':'rgba(255,255,255,0.0)','pointer-events':'none'});
				}
			});
		}

		// need a listener on the search reset button to cover some other misc. functionality
		$('form#nu__searchbar-form').on('click','div > button[type=reset]',function(e){
			$('form#nu__searchbar-form > div > input').val('');
			$('form#nu__searchbar-form > div > input').attr('value','');
			$('form#nu__searchbar-form > div > label').removeClass('focus');
			$('form#nu__searchbar-form > div > button.reset').css({'color':'rgba(255,255,255,0.0)','pointer-events':'none'});
		});





		// get the height of the content area on the page
		function getContentAreaHeight(){
			// contentAreaHeight = (parseInt($(window).height()) - parseInt($('header').outerHeight()) - parseInt($('footer').outerHeight()));
			contentAreaHeight = (parseInt($(window).height()) - parseInt($('header').outerHeight()));
		}





		// set the height of menu panels if we made it this far, used to augment the CSS
		function setMenuPanels(){
			getContentAreaHeight();
			$('div.navigational > section > div.items').css({'height':contentAreaHeight,'min-height':contentAreaHeight});
			if($('body').hasClass('search')){
				$('#nu__search section').css({'min-height':contentAreaHeight});
			}
		}





		// we need to pass the browser window size to PHP so that we can better tailor responsive imagery all around
		function getWindowSize(){
			windowSize[0] = $(window).height();
			windowSize[1] = $(window).width();
			$.post("/wp-content/themes/nudev/src/windowsize.php",{"height":windowSize[0],"width":windowSize[1]},function(data){
					// console.log(data);
    	});
		}





		// call the page setup scripts to optimize some items
		//setMenuPanels();
		getWindowSize();





		// we need to perform some tweaks to the site if we are below the break size on load
		if(windowSize[1] < 740){
			navReset();
		}

		/* ************************************************************************ */






		// /* ***************************************************************************
		// The following is a set of tools and features for the homepage
		// *************************************************************************** */

		// these are stored within the separate homepage.js file

		// /* end of the stuff for the homepage */





		/* ***************************************************************************
		The following is a set of tools and features for the main navigation
		*************************************************************************** */

		// gather up the background images for the iamnav cats
		$.post("/wp-content/themes/nudev/src/iamnavbgs.php",function(data){
			// console.log(data);
			iamnavbgs = JSON.parse(data);
			console.log(iamnavbgs);
		});

		// this will handle some preventitive measures in the main nav regarding overlap of options
		$('nav').on('click','input#nu__supernav-toggle,input#nu__iamnav-toggle,input#nu__search-toggle',function(){

			// determine which nav we are looking at and whether it is the currently active one, in which case close it
			if(cNav == null){
				$(this).prop('checked',true);
				cNav = $(this).attr('id');
			}else if($(this).attr('id') == cNav){
				$(this).prop('checked',false);
				cNav = null;
			}else{
				$('nav input').prop('checked',false);
				$(this).prop('checked',true);
				cNav = $(this).attr('id');
			}



			// // if we are on a screensize below the break size, we need to autoscroll the nav element that was selected to the top just below the header
			// // for ease of use
			// if(windowSize[1] < sizeBreak){
			// 	console.log($(this));
			// }



			// need to reset the first item in the supernav and iamnav menu to be active
			navReset();

			// if we are on the search page, we need to restrict opening the search again on top of itself
			if($('body').hasClass('search')){
				$('input#nu__search-toggle').prop('checked',false);
			}

			// check to see if we need to collapse the footer if it is already open (homepage only)
			if($('body').hasClass('home') && !$('footer#nu__global-footer').hasClass('collapse')){
				$('footer#nu__global-footer').addClass('collapse');
			}

			if(!$('body').hasClass('home')){
				allowScrollOrNot();
			}

		});





		// this will handle resetting the nav panels
		function navReset(){

			// every state has to reset the first items from being active
			$('#nu__supernav > section > div > ul > li.active').removeClass('active');
			$('#nu__iamnav > section > div > ul > li.active').removeClass('active');

			if(windowSize[1] > 740){	// above break size, show first cat automagically
				$('#nu__supernav > section > div > ul > li:first-child').addClass('active');
				$('#nu__iamnav > section > div > ul > li:first-child').addClass('active');
			}
		}





		// if the user clicks outside the menu items and on the empty portion of the fullscreen sneezeguard, close the nav panels
		$('nav.nu__mainmenu').on('click','div#nu__supernav,div#nu__searchbar,div#nu__iamnav',function(e){
			if(['nu__supernav','nu__searchbar','nu__iamnav'].indexOf(e.target.id) >= 0){
				$('nav input').prop('checked',false);
				cNav = null;
			}
		});





		// this function will determine whether or not to allow the page to scroll, if the menu is open or not
		function allowScrollOrNot(){

			// prevent the main page from scrolling when the nav is open or allow it if we close the navs
			if($('input#nu__search-toggle').prop('checked') || $('input#nu__iamnav-toggle').prop('checked') || $('input#nu__supernav-toggle').prop('checked')){
				$('html').css({'overflow-y':'hidden'});
			}else{
				$('html').css({'overflow-y':'scroll'});
			}

		}





		// this will handle the accordion functionality for the main navigational elements
		$('div.navigational > section > div > ul').on('click','li:not(.featured)',function(e){




			// if we are on a screensize below the break size, we need to autoscroll the nav element that was selected to the top just below the header
			// for ease of use
			// if(windowSize[1] < sizeBreak){
			// 	console.log($(this));
			// 	$(window).scrollTop(0);
			// }


			// if we are clicking on cats in the iamnav, we may need to swap the background image
			if($(this).parent().parent().parent().parent().attr('id') == 'nu__iamnav' && iamnavbgs.length > 0){
				$('div#nu__iamnav').attr('style','background-image: url('+iamnavbgs[$(this).index()]+');');
			}




			$('div.navigational > section > div > ul li').removeClass('active');
			$(this).addClass('active');
		});

		/* end of the stuff for the main navigation */







		// let's listen for the page to resize and handle some events
		$(window).on("resize",function(){

			getWindowSize();

			setMenuPanels();

			navReset();

			// reset the offset to position content just below the header
			$("main").css({
				"margin-top":$("header").outerHeight()
			});


			// need to account for the alerts being open and shift the main menu overlays down to match!!
			if(parseInt($('#nu__alerts').height()) > 0){
				$('#nu__supernav,#nu__iamnav,#nu__searchbar').css({'top':parseInt($('header').outerHeight())});
			}


			if($('body').hasClass('home')){	// we need to make sure that we are resizing and keeping only 1 panel on the screen during resize

				// if alerts are showing we need to account for that in the content area of the homepage
				if(parseInt($('#nu__alerts').height()) > 0){

					var hpHeight = parseInt($(window).height()) - parseInt($('header').outerHeight()) - parseInt($('footer').height());
					$('main#nu__homepage').css({'height':hpHeight,'min-height':hpHeight});

				}


				// if we are below the size break, we will just stack
				if(windowSize[1] < sizeBreak){
					$('#nu__stories').css({'margin-left':'0'});
					currentPanel = 0;
					offset = 0;

					// hide the next and previous arrows for the slider on the homepage as the content has stacked
					$('#next,#prev').fadeOut(200);

				}else{	// we have gone above the break size, reset the next and previous arrows if not already visible
					if($('#next').css('display') == 'none'){
						$('#next').fadeIn(200);
					}
				}


				// we need to reset the panel sizes on resize to ensure that things slide back and forth correctly
				var newWidth = windowSize[1] * -1;

				var wDiff = (windowWidth - newWidth);

				if(currentPanel > 0){

					var newOffset = (offset - (wDiff * currentPanel));

					$('#nu__stories').css({"margin-left":newOffset});
					offset = newOffset;
				}

				windowWidth = newWidth;

			}

		});

	});
}(this,jQuery));
