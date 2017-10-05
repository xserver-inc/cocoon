require('./jquery-2.1.4.min');
require('./jquery.clingify');

$(function() {
	'use strict';

	var $navBar = $('nav'),
		$navParent = $('.primary-content'),
		$sidebar = $('.some-sidebar'),
		$sidebarParent = $('.secondary-content'),	
		matchWidths = function($elem, $elemParent) {
			$elem.width($elemParent.width());
		},
		matchOffset = function($elem, $elemParent) {
			$elem.css({'padding-left' : $elemParent.offset().left});
		},
		resetOffset = function($elem) {
			$elem.css({'padding-left' : 0});
		};

	$navBar.clingify({
		extraClass : 'navClingy',
		locked : function() {
			matchOffset($navBar, $navParent);
		},
		detached : function() {
			resetOffset($navBar);
		},
		resized : function() {
			matchOffset($navBar, $navParent);
		}
	});
	$sidebar.clingify({
		breakpointWidth : 768,
		extraClass : 'sidebarClingy',
		locked : function() {
			matchWidths($sidebar, $sidebarParent);
		},
		resized : function() {
			matchWidths($sidebar, $sidebarParent);
		}
	});
});