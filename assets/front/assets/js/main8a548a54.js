(function ($) {
	"use strict";

    jQuery(document).ready(function($){
       


 
/*****==================================
 * Menu Active Start
=====================================*****/
$('.stellarnav').stellarNav({
	theme: 'dark',
	breakpoint: 660,
	position: 'static',
	phoneBtn: false,
	locationBtn: false,
	sticky:false,
	showArrows:true,
	openingSpeed: 500,
	closingDelay: 500,
	
});
        
window.onscroll = function() {myFunction()};

var header = document.getElementById("myHeader");
var sticky = header.offsetTop;

function myFunction() {
	if (window.pageYOffset > sticky) {
	header.classList.add("sticky");
	} else {
	header.classList.remove("sticky");
	}
}       

/*****==================================
 * Menu Active End
=====================================*****/

	
/*****==================================
 * Slider Active Start
=====================================*****/
$(".slider-active").owlCarousel({
	loop:true,
	dots:true,
	nav:true,
	margin:15,
	autoplay:true,
	smartSpeed: 2000,
	autoplayTimeout:6000,
	animateOut: 'fadeOut',
	navText:["<i Class='las la-arrow-left'></i>", 
			  "<i class='las la-arrow-right'></i>"],
	responsiveClass: true,
	responsive: {
	  0: {
		items: 1,
	  },
	  480: {
		items: 1,
	  },
	  768: {
		items: 1,
	  },
	  1000: {
		items:1,
	  }
	}

});	

/*****==================================
 * Slider Active End
=====================================*****/	


/*****==================================
 * Speech Active Start
=====================================*****/	        
        
$(".speech-active").owlCarousel({
	loop:true,
	dots:true,
	nav:false,
	autoplayHoverPause:true,
	margin:0,
	autoplay:true,
	smartSpeed: 1000,
	autoplayTimeout:5000,
	navText:["<i Class='fas fa-angle-left'></i>", 
			"<i Class='fas fa-angle-right'></i>"],
	responsiveClass: true,
	responsive: {
		0: {
		items: 1,
		},
		480: {
		items: 1,
		},
		768: {
		items: 2,
		},
		1000: {
		items:2,
		}
	}

});

/*****==================================
 * Speech Active End
=====================================*****/	                   


/*****==================================
 * Coures Active Start
=====================================*****/		
$(".coures-active").owlCarousel({
	loop:true,
	dots:true,
	nav:false,
	margin:5,
	autoplay:true,
	smartSpeed: 1000,
	autoplayTimeout:5000,
	responsiveClass: true,
	responsive: {
	  0: {
		items: 1,
	  },
	  480: {
		items: 2,
	  },
	  768: {
		items: 2,
	  },
	  1000: {
		items: 3,
	  }
	}

});	

/*****==================================
 * Course Active End
=====================================*****/


/*****==================================
 * Coures Active Start
=====================================*****/		
$(".blog-active").owlCarousel({
	loop:true,
	dots:true,
	nav:false,
	margin:5,
	autoplay:true,
	smartSpeed: 1000,
	autoplayTimeout:5000,
	responsiveClass: true,
	responsive: {
	  0: {
		items: 1,
	  },
	  480: {
		items: 2,
	  },
	  768: {
		items: 2,
	  },
	  1000: {
		items: 3,
	  }
	}

});	

/*****==================================
 * Course Active End
=====================================*****/



/*****==================================
 * Video Active Start
=====================================*****/	     
$(".video-list").owlCarousel({
	loop:true,
	dots:true,
	nav:false,
	autoplayHoverPause:true,
	margin:20,
	autoplay:true,
	smartSpeed: 1000,
	autoplayTimeout:5000,
	navText:["<i Class='fas fa-angle-left'></i>", 
			"<i Class='fas fa-angle-right'></i>"],
	responsiveClass: true,
	responsive: {
		0: {
		items: 1,
		},
		480: {
		items: 2,
		},
		768: {
		items: 3,
		},
		1000: {
		items:4,
		}
	}

});

/*****==================================
 * Video Active End
=====================================*****/	                   


        
/*****==================================
 * Teacher Active Start
=====================================*****/		                   
$(".teacher_list").owlCarousel({
	loop:true,
	dots:true,
	nav:true,
	margin:0,
	autoplay:true,
	smartSpeed: 1000,
	autoplayTimeout:5000,
	navText:["<i Class='las la-angle-left'></i>", 
			"<i Class='las la-angle-right'></i>"],
	responsiveClass: true,
	responsive: {
		0: {
		items: 1,
		},
		480: {
		items: 2,
		},
		768: {
		items: 3,
		},
		1000: {
		items:5,
		}
	}

});

/*****==================================
 * Teacher Active End
=====================================*****/	
 

/*****==================================
 * Client Active Start
=====================================*****/
$(".clinet-active").owlCarousel({
	loop:true,
	dots:false,
	nav:false,
	margin:30,
	autoplay:true,
	smartSpeed: 1000,
	autoplayTimeout:5000,
	navText:["<i Class='las la-arrow-left'></i>", 
			  "<i class='las la-arrow-right'></i>"],
	responsiveClass: true,
	responsive: {
	  0: {
		items: 2,
	  },
	  480: {
		items: 2,
	  },
	  768: {
		items: 3,
	  },
	  1000: {
		items:6,
	  }
	}

});	

/*****==================================
 * Client Active End
=====================================*****/       




/*========== Video popup js Start =============*/ 
   
	$('.popup').magnificPopup({
		type: 'iframe'
	});
	
/*========== Video popup js End =============*/      

  
/*==========Gallery js Start =============*/  

$('.gallery').magnificPopup({
	type: 'image',
	  mainClass: 'mfp-with-zoom', 
	  gallery:{
		enabled:true
	},

	zoom: {
		enabled: true, 		
		duration: 500, // duration of the effect, in milliseconds
		easing: 'ease-in-out', // CSS transition easing function		
		opener: function(openerElement) {		
		return openerElement.is('img') ? openerElement : openerElement.find('img');
		}
	}

});

/*==========Gallery js End =============*/  



  
/*========== Scroll js Start =============*/ 
		
$(window).scroll(function(){
	if ($(this).scrollTop() > 100) {
		$('.elitesDesign_scroll').fadeIn();
	} else {
		$('.elitesDesign_scroll').fadeOut();
	}
});

//Click event to scroll to top
$('.elitesDesign_scroll').on('click', function(){
$('html, body').animate({scrollTop : 0},800);
return false;
});

/*========== Scroll js End =============*/        
//        

    });

//
//    jQuery(window).load(function(){
////        jQuery(".page-loader").fadeOut(1000);
//    });


}(jQuery));	