<?php

class FORMS {

	
    function __construct() {

    }
    
    function getSimpleFormData($data) {
       $output='{
         "formData": {
             "DATE_TIME": "10/03/2021",
             "SIMPLE_INPUT": "Existing Data",
             "EXAMPLE_PASSWORD": "",
             "FORM_CONTROL_TEXT_INPUT": "",
             "FORM_CONTROL_SELECT": "2",
             "FORM_CONTROL_MULTI_SELECT": "3",
             "FORM_CONTROL_TEXTAREA": "This is Text Area",
             "FORM_CONTROL_RANGE": "80",
             "CUSTOM_RANGE": "50",
             "CUSTOM_FILE": "",
             "EXAMPLE_READONLY": "Readonly Value",
             "EXAMPLE_DISABLED": "Disabled Value",
             "EXAMPLE_STATIC_TEXT": "Static Text",
             "EXAMPLE_HELPING_TEXT": "Helping Text",
             "EXAMPLE_EMAIL" : "ed@artfin.com",
             "EXAMPLE_PASSWORD2": "",
             "EXAMPLE_CHECKBOX": "on"
         },
         "TABLE_NAME":"EXAMPLE",
         "KEY":"ID",
         "SEQUENCE":"EXAMPLE_SEQ",
         "select": [
            {"value":"1", "name": "1"},
            {"value":"2", "name": "2"},
            {"value":"3", "name": "3"},
            {"value":"4", "name": "4"},
            {"value":"5", "name": "5"}
         ]
       }';
       return json_decode($output,true);
    }

    function postForm($data) {
         $this->post($data['formData']);
         $results=array();
         $results['error_code']=0;
         $results['error_message']="Save Complete";
         return $results;
    }

    function post($data) {

    } 

}

