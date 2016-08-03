<?php 

function set_session(){
    if (isset($_GET['key'])) {
        session_id($_GET['key']);
    }
}

set_session();

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
include('weblib.php');

define('Functions_list', serialize(array('record_file_save', 'record_file_fetch')));


function set_header(){
    header("access-control-allow-origin: *");
}
	
function exit_if_request_is_options() {
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit;
    }
}

function received_get_data(){
    return (isset($_GET)) ? $_GET : false;
}

function received_post_data(){
    return (isset($_POST)) ? $_POST : false;
}
   
function validate_request(){
    $cmid = required_param('cmid', PARAM_INT);
    $userid = required_param('user', PARAM_INT);
    $filenum = required_param('cn', PARAM_INT);
    $vmsession = required_param('sesseionkey', PARAM_FILE);
    $data = required_param('record_data', PARAM_RAW);
    return array($cmid, $userid, $filenum, $vmsession, $data);
}

function execute_action($valid_parameters, $DB){
    $getdata = received_get_data();
    if($getdata && isset($getdata['methodname'])){
        $postdata = received_post_data();
        if($postdata){
            $function_list = unserialize(Functions_list);
            if(in_array($getdata['methodname'], $function_list)){
                //call_user_func($getdata['methodname'], $getdata, $postdata, $valid_parameters, $DB);
                $getdata['methodname']($getdata, $postdata, $valid_parameters, $DB);
            } else {
                throw new Exception('There is no any method of name ' .$getdata['methodname']);
            }
        }
    } else {
        throw new Exception('There is no any method for execution');
    }
}
    
set_header();
exit_if_request_is_options();
$validparams = validate_request();
execute_action($validparams, $DB);
?>
