<?php

class MENUS {

	
    function __construct() {

    }

    function getVerticalMenu($data) {

$output='[{
	"title": "Menu",
	"routerLink": "",
	"icon": "",
	"id": 0,
	"badge": "title",
	"submenu": []
}, {
	"title": "Active Claims",
	"routerLink": "/active-claims",
	"icon": "mdi mdi-view-dashboard",
	"id": 0,
	"badge": "",
	"submenu": []
}
]';
$data['role']='sadmin';    
if ($data['role']=='sadmin') {
$output='[
   {"title": "Menu", "routerLink": "", "icon": "", "id": 0, "badge": "title", "submenu": [] }, 
   {"title": "Claims", "routerLink": "/active-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Add Claim", "routerLink": "/new-claim", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Logout", "routerLink": "/login", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }
   ]';
} 

if ($data['role']=='badmin') {
$output='[
   {"title": "Menu", "routerLink": "", "icon": "", "id": 0, "badge": "title", "submenu": [] }, 
   {"title": "Active Policys", "routerLink": "/active-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Pending Quotes", "routerLink": "/closed-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "New Quotes", "routerLink": "/closed-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Companies", "routerLink": "/closed-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Users", "routerLink": "/new-claim", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Logout", "routerLink": "/login", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }
   ]';
} 

if ($data['role']=='broker') {
$output='[
   {"title": "Menu", "routerLink": "", "icon": "", "id": 0, "badge": "title", "submenu": [] }, 
   {"title": "Active Policys", "routerLink": "/active-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Pending Quotes", "routerLink": "/closed-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "New Quotes", "routerLink": "/closed-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Companies", "routerLink": "/closed-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Users", "routerLink": "/new-claim", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Logout", "routerLink": "/login", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }
   ]';
} 

if ($data['role']=='eadmin') {
$output='[
   {"title": "Menu", "routerLink": "", "icon": "", "id": 0, "badge": "title", "submenu": [] }, 
   {"title": "Active Policys", "routerLink": "/active-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Pending Quotes", "routerLink": "/closed-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Employees", "routerLink": "/closed-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Companies", "routerLink": "/closed-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Users", "routerLink": "/new-claim", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Logout", "routerLink": "/login", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }
   ]';
} 

if ($data['role']=='employee') {
$output='[
   {"title": "Menu", "routerLink": "", "icon": "", "id": 0, "badge": "title", "submenu": [] }, 
   {"title": "Existing Coverage", "routerLink": "/active-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Coverage Options", "routerLink": "/closed-claims", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }, 
   {"title": "Logout", "routerLink": "/login", "icon": "mdi mdi-view-dashboard", "id": 0, "badge": "", "submenu": [] }
   ]';
} 

           return json_decode($output,true);
    }

}

