
(function($) {
	$.fn.jQuerySimpleCounter = function( options ) {
	    let settings = $.extend({
	        start:  0,
	        end:    100,
	        easing: 'swing',
	        duration: 400,
	        complete: ''
	    }, options );

	    const thisElement = $(this);

	    $({count: settings.start}).animate({count: settings.end}, {
			duration: settings.duration,
			easing: settings.easing,
			step: function() {
				let mathCount = Math.ceil(this.count);
				thisElement.text(mathCount);
			},
			complete: settings.complete
		});
	};

}(jQuery));

    let sideBar = document.querySelector('.side-bar');
    let arrowCollapse = document.querySelector('#logo-name__icon');
    let main_page = document.querySelector('.main_page');
    sideBar.onclick = () => {
		sideBar.classList.toggle('sidebar_collapse');
		arrowCollapse.classList.toggle('sidebar_collapse');
		if (arrowCollapse.classList.contains('sidebar_collapse')) {
			arrowCollapse.classList = 'bx bx-arrow-from-left logo-name__icon sidebar_collapse';
			main_page.classList = 'main_page_collapse1';
		} else {
			main_page.classList = 'main_page_collapse';
			arrowCollapse.classList = 'bx bx-arrow-from-right logo-name__icon';
		}
    };

$(function () {
    $('[data-toggle="tooltip"]').tooltip({html: true});
})
