<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('memory_limit',-1);
ini_set('max_execution_time', 900000);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');

require_once('class.XRDB.php');

function resize_image_to_width($new_width, $image, $width, $height) {
    $resize_ratio = $new_width / $width;
    $new_height = $height * $resize_ratio;
    $new_image=imagecreate($new_width, $new_height);
    imagecopyresized($new_image, $image,0,0,0,0, $new_width, $new_height, $width, $height);
    return $new_image;
}

function save_image($new_image, $new_filename, $new_type='jpeg', $quality=90) {
    if( $new_type == 'jpeg' ) {
        imagejpeg($new_image, $new_filename, $quality);
     }
     elseif( $new_type == 'png' ) {
        imagepng($new_image, $new_filename);
     }
     elseif( $new_type == 'gif' ) {
        imagegif($new_image, $new_filename);
     }
}
$X=new XRDB();

$id=$_POST['user_id'];
$file_name=$_FILES["file"]["name"];
$file_type=$_FILES["file"]["type"];
if ($file_type!="image/jpeg"&&$file_type!="image/png") {
    $output=array();
    $output['error_code']=99;
    echo json_encode($output);
    die();
}
if ($file_type=="image/jpeg") $extension="jpg";
if ($file_type=="image/png") $extension="png";

$target_dir = "/var/www/html/images/";
$aid=rand(600000,1600000);
$target_file = $target_dir . $aid . "." . $extension;
$target_file2 = $target_dir . "r" . $id . "." . $extension;
$check = getimagesize($_FILES["file"]["tmp_name"]);
if($check !== false) {
    $uploadOk = 1;
  } else {
    $uploadOk = 0;
  }

if ($uploadOk==0) {
   $output=array();
   $output['error_code']=1;
   echo json_encode($output);
   die();
}

if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    if ($extension=="jpg") {
        $image = imagecreatefromjpeg($target_file);
    } else { 
        $image = imagecreatefrompng($target_file);
    }

list($width, $height, $type) = getimagesize($target_file);
$image_width_fixed = resize_image_to_width(128, $image, $width, $height);
save_image($image_width_fixed,$target_file2);
     $post=array();
     $post['table_name']="nua_user";
     $post['action']="insert";
     $post['id']=$id;
     $post['avatar']="https://deepgoat.com/images/" . $aid . "." . $extension;
     $X->post($post);
     $output=array();
     $output['error_code']=0;
   } else {
       $output=array();
       $output['error_code']=2;
   }
//print_r($_FILES);
echo json_encode($output);
?>
