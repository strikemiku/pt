/**************************************************************************
 * jquery.bubbleScroll.js
 * @version: 1.0 (11.28.2013)
 * @requires jQuery v1.8 or later
 * @author Axel Hardy
**************************************************************************/

(function ( $ ) {

	
	//////////////////////////////////////////
	// THE BUBBLESCROLL PLUGIN STARTS HERE //
	/////////////////////////////////////////
	$.fn.bubbleScroll = function( options ) {
		if($.cookie("bubbleScroll_close") == null) {
			var el = $(this); 
			
			// Plugin Param
			var settings = $.extend({
				borderColor: "#000",
				backgroundColor: "#FFF",
				textColor: "#000",
				bubbleStyle: "circle",
				position: "right",
				alwaysVisible: false,
				useCookie: false
			}, options );
			
			// Change the BubbleScroll Style
			el.find(".bubbleScroll_inner").css({
				borderColor: settings.borderColor,
				background: settings.backgroundColor,
				color: settings.textColor
			});
			
			// Change the style to a square
			if(settings.bubbleStyle == "square") {
				el.find(".bubbleScroll_inner").css({
					borderRadius: 0
				});
			}
			
			// Change the position (left or right)
			if(settings.position == "left" && settings.bubbleStyle == "circle")
			{
				el.css({
					left: 0,
					right: "inherit"
				});
				
				el.find(".bubbleScroll_inner").css({
					borderRadius: "150px 150px 150px 0",
					borderLeft: "none",
					borderRight: "2px solid " + settings.borderColor
				});
			} else if(settings.position == "left" && settings.bubbleStyle == "square") {
				el.find(".bubbleScroll_inner").css({
					borderLeft: "none",
					borderRight: "2px solid " + settings.borderColor
				});
				
				el.css({
					left:"-300px",
					right: "inherit"
				});
			}
			
			if(settings.alwaysVisible) {
				if(settings.position == "right")
					el.stop(true).animate({bottom:'0px', right:'0px'},450);
				else
					el.stop(true).animate({bottom:'0px', left:'0px'},450);
			} else {
				// Scroll Event
				$(window).scroll(function()
				{
					var clicked = false;
					var scrollPosition = window.pageYOffset;
					var windowSize     = window.innerHeight;
					var bodyHeight     = document.body.offsetHeight;
					var bottomDistance = Math.max(bodyHeight - (scrollPosition + windowSize), 0);
					
					if(bottomDistance < 300)
					{
						if(settings.position == "right")
							el.stop(true).animate({bottom:'0px', right:'0px'},450);
						else
							el.stop(true).animate({bottom:'0px', left:'0px'},450);
					} else if(bottomDistance > 300) {
						if(settings.position == "right")
							el.animate({bottom:'-300px', right:'-300px'},650);
						else
							el.animate({bottom:'-300px', left:'-300px'},650);
					}
				});
			}
			
			// Click to close the bubble
			$(document).on("click", ".bubbleScroll_close_button", function(e) {
		    	e.preventDefault();
			    $(el).animate({bottom:'-300px', right:'-300px'},650);
			    $(el).remove();
			    
			    		
				// Check the cookie option
				if(settings.useCookie) {
					$.cookie("bubbleScroll_close", 1);
				}
			});
		}
		
	};
}( jQuery ));