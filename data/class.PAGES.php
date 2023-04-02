<?php

class MENUS {

	
    function __construct() {

    }

    function getVerticalMenu() {

$output='[{
	"title": "Menu",
	"routerLink": "",
	"icon": "",
	"id": 0,
	"badge": "title",
	"submenu": []
}, {
	"title": "Vertical Dashboard",
	"routerLink": "/v",
	"icon": "mdi mdi-view-dashboard",
	"id": 0,
	"badge": "",
	"submenu": []
}, {
	"title": "Horizontal Dashboard",
	"routerLink": "/h",
	"icon": "mdi mdi-view-dashboard",
	"id": 0,
	"badge": "",
	"submenu": []
}, {
	"title": "Sample Page",
	"routerLink": "",
	"icon": "mdi mdi-book-open-page-variant",
	"id": 1,
	"badge": "",
	"submenu": [{
		"title": "Invoice",
		"routerLink": "/invoice"
	}, {
		"title": "Starter Page",
		"routerLink": "/starter"
	}, {
		"title": "Maintenance",
		"routerLink": "/maintenance"
	}, {
		"title": "FAQs",
		"routerLink": "/faqs"
	}, {
		"title": "Pricing",
		"routerLink": "/pricing"
	}, {
		"title": "Team",
		"routerLink": "/team"
	}, {
		"title": "Login",
		"routerLink": "/login"
	}, {
		"title": "Register",
		"routerLink": "/register"
	}, {
		"title": "Recover Password",
		"routerLink": "/recover-password"
	}, {
		"title": "Lock Screen",
		"routerLink": "/lock-screen"
	}, {
		"title": "Error 404",
		"routerLink": "/404"
	}, {
		"title": "Error 500",
		"routerLink": "/500"
	}]
}, {
	"title": "Components",
	"routerLink": "",
	"icon": "",
	"id": 0,
	"badge": "title",
	"submenu": []
}, {
	"title": "UI Elements",
	"routerLink": "",
	"icon": "mdi mdi-briefcase-check",
	"id": 2,
	"badge": "",
	"submenu": [{
		"title": "Buttons",
		"routerLink": "/ui-buttons"
	}, {
		"title": "Cards",
		"routerLink": "/ui-cards"
	}, {
		"title": "Carousel",
		"routerLink": "/ui-carousel"
	}, {
		"title": "Embeds",
		"routerLink": "/ui-embeds"
	}, {
		"title": "General",
		"routerLink": "/ui-general"
	}, {
		"title": "Grid",
		"routerLink": "/ui-grid"
	}, {
		"title": "Media Objects",
		"routerLink": "/ui-media-objects"
	}, {
		"title": "Modals",
		"routerLink": "/ui-modals"
	}, {
		"title": "Progress Bars",
		"routerLink": "/ui-progressbars"
	}, {
		"title": "Tabs",
		"routerLink": "/ui-tabs"
	}, {
		"title": "Typography",
		"routerLink": "/ui-typography"
	}, {
		"title": "Toasts",
		"routerLink": "/ui-toasts"
	}, {
		"title": "Tooltips & Popovers",
		"routerLink": "/ui-tooltips-popovers"
	}, {
		"title": "Scrollspy",
		"routerLink": "/ui-scrollspy"
	}, {
		"title": "Spinners",
		"routerLink": "/ui-spinners"
	}, {
		"title": "Sweet Alerts",
		"routerLink": "/ui-sweetalerts"
	}]
}, {
	"title": "Forms",
	"routerLink": "",
	"icon": "mdi mdi-format-page-break",
	"id": 3,
	"badge": "badge badge-pill-badge-danger float-right",
	"submenu": [{
		"title": "Elements",
		"routerLink": "/forms-elements"
	}, {
		"title": "Plugins",
		"routerLink": "/forms-plugins"
	}, {
		"title": "Validation",
		"routerLink": "/forms-validation"
	}, {
		"title": "Masks",
		"routerLink": "/forms-masks"
	}, {
		"title": "Quilljs",
		"routerLink": "/forms-quilljs"
	}, {
		"title": "File Uploads",
		"routerLink": "/file-uploads"
	}]
}, {
	"title": "Tables",
	"routerLink": "",
	"icon": "mdi mdi-table-merge-cells",
	"id": 4,
	"badge": "",
	"submenu": [{
		"title": "Basic Tables",
		"routerLink": "/tables-basic"
	}, {
		"title": "Data Tables",
		"routerLink": "/tables-datatables"
	}]
}, {
	"title": "Charts",
	"routerLink": "",
	"icon": "mdi mdi-chart-donut",
	"id": 5,
	"badge": "",
	"submenu": [{
		"title": "Charts Morris",
		"routerLink": "/chars-morris"
	}, {
		"title": "Charts Google",
		"routerLink": "/chars-google"
	}, {
		"title": "Charts Chartjs",
		"routerLink": "/chars-chartjs"
	}, {
		"title": "Charts Sparklline",
		"routerLink": "/chars-sparkline"
	}, {
		"title": "Jquery Knob",
		"routerLink": "/chars-knob"
	}]
}, {
	"title": "Icons",
	"routerLink": "",
	"icon": "mdi mdi-black-mesa",
	"id": 6,
	"badge": "",
	"submenu": [{
		"title": "Feather Icons",
		"routerLink": "/icons-feather"
	}, {
		"title": "Material Design",
		"routerLink": "/icons-materialdesign"
	}, {
		"title": "Drip Icons",
		"routerLink": "/icons-dripicons"
	}, {
		"title": "Font Awesome",
		"routerLink": "/icons-fontawesome"
	}]
}, {
	"title": "more",
	"routerLink": "",
	"icon": "",
	"id": 0,
	"badge": "title",
	"submenu": []
}, {
	"title": "Calendar",
	"routerLink": "/calendar",
	"icon": "mdi mdi-calendar",
	"id": 0,
	"badge": "",
	"submenu": []
}, {
	"title": "Maps",
	"routerLink": "",
	"icon": "mdi mdi-map-marker-multiple",
	"id": 7,
	"badge": "",
	"submenu": [{
		"title": "Google Maps",
		"routerLink": "/maps-google"
	}, {
		"title": "Vector Maps",
		"routerLink": "/maps-vector"
	}]
}, {
	"title": "Multi Level",
	"routerLink": "",
	"icon": "mdi mdi-share-variant",
	"id": 8,
	"badge": "",
	"submenu": [{
		"title": "Level 1.1",
		"routerLink": "/y"
	}, {
		"title": "Level 1.2",
		"routerLink": "",
		"id": 9,
		"submenu": [{
			"title": "Level 2.1",
			"routerLink": "/y"
		}, {
			"title": "Level 2.2",
			"routerLink": "/y"
		}]
	}]
}]';

           return json_decode($output,true);
    }

}

