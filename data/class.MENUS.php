<?php

class PAGES {

	
    function __construct() {

    }
    
    function getDashboardData($data) {
       $output='{
         "cost_per_unit": "$17.21",
         "cost_per_unit_pct": "12.5%".
         "market_revenue": "$1875.54",
         "market_revenue_pct": "-18.71%",
         "expenses": "$785.62",
         "expenses_pct": "57%",
         "daily_visits": "1,155,187",
         "daily_visits_pct": "17.8%",
         "total_sales": "5,459";
         "open_campaign": "18",
         "revenue": "1,875.54",
         "total_offers": "541",
         "total_revenue": "$7,841.12",
         "total_open_campaign": "17",
         "recent_customers": [
             {"customer": "Paul J. Friend",
              "phone": "937-330-1634",
              "email": "paul@example.com",
              "city": "New York",
              "date": "07/07/2018",
              "avatar": "assets/images/users/avatar-8.jpg"},
             {"customer": "Brian J. Luellen",
              "phone": "215-302-3376",
              "email": "brian@example.com",
              "city": "New York",
              "date": "07/27/2019",
              "avatar": "assets/images/users/avatar-8.jpg"},
             {"customer": "Kathryn S. Collier",
              "phone": "404-266-3542",
              "email": "kathryn@example.com",
              "city": "Canada",
              "date": "06/30/2019",
              "avatar": "assets/images/users/avatar-8.jpg"},
             {"customer": "Timothy Kauper",
              "phone": "202-454-3322",
              "email": "timothy@example.com",
              "city": "Denmark",
              "date": "07/07/2018",
              "avatar": "assets/images/users/avatar-8.jpg"},
             {"customer": "Zara Raws",
              "phone": "(02) 75 150 665",
              "email": "zara@example.com",
              "city": "Germany",
              "date": "07/15/2018",
              "avatar": "assets/images/users/avatar-8.jpg"}
         ],
         "transactions": [
             {"cc": "4257 **** **** 7852",
              "exp": "11 April 2020",
              "amount": "$79.49",
              "type": "fab fa-cc-visa",
              "name": "Helen Warren"},
             {"cc": "4265 **** **** 0025",
              "exp": "28 Jan 2020",
              "amount": "$1254.00",
              "type": "fab fa-cc-stripe",
              "name": "Kayla Lambie"},
             {"cc": "5570 **** **** 8547",
              "exp": "12 Dec 2021",
              "amount": "$784.25",
              "type": "fab fa-cc-amazon-pay",
              "name": "Hugo Lavarack"},
             {"cc": "4257 **** **** 7852",
              "exp": "11 April 2020",
              "amount": "$485.24",
              "type": "fab fa-cc-visa",
              "name": "Amber Scurry"},
             {"cc": "4257 **** **** 7852",
              "exp": "11 April 2020",
              "amount": "$79.49",
              "type": "fab fa-cc-visa",
              "name": "Caitlyn Gibney"}
         ]
       }';
       return json_decode($output,true);

    }

}

