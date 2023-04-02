<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('memory_limit',-1);
ini_set('max_execution_time', 900000);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
//header('Content-type: application/json');

require_once('class.XRDB.php');
$X=new XRDB();

function all_legs($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name from kss_bodypart where id in (558,568,569,570,571) or parent in (558,568,569,570,571)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function glutes($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (558,568) or parent in (568)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function hamstrings($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (558,570) or parent in (570)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function calves($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (558,571) or parent in (571)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function quads($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (558,569) or parent in (569)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function all_chest($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (559,565,566,567) or parent in (565,566,567)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function upper_chest($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (559,565) or parent in (565)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function mid_chest($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (559,566) or parent in (566)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function lower_chest($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (559,567) or parent in (567)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function all_back($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name from kss_bodypart where id in (560,592,572,573,594,595,596,597) or parent in (560,592,572,573)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id  from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function lower_back($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name from kss_bodypart where id in (573) or parent in (573)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id  from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function all_shoulders($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (561,577,578,579) or parent in (561,577,578,579)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function front_shoulders($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (561,577) or parent in (577)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function mid_shoulders($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (561,578) or parent in (578)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function rear_shoulders($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (561,579) or parent in (579)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function biceps($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (562,574) or parent in (574)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}
function triceps($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (562,575) or parent in (575)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}
function forearms($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (562,576) or parent in (576)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

function traps($id,$score=1) {
   $X=new XRDB();

   $sql="select * from kss_exercise where id = " . $id;
   $h=$X->sql($sql);
   $exercise_name=$h[0]['exercise_name'];

   $sql="select id, bodypart_name  from kss_bodypart where id in (563) or parent in (563)";
   $g=$X->sql($sql);
   foreach($g as $h) {
      $post=array();
	   $sql="select id from kss_exercise_detail where exercise_id = " . $id . " and bodypart_id = " . $h['id'];
	   $hh=$X->sql($sql);
	   if (sizeof($hh)>0) $post['id']=$hh[0]['id'];
      $post['table_name']="kss_exercise_detail";
      $post['action']="insert";
      $post['exercise_id']=$id;
      $post['exercise_name']=$exercise_name;
      $post['bodypart_id']=$h['id'];
      $post['bodypart_name']=$h['bodypart_name'];
      $post['score_multiplier']=$score;
      $post['display_order']=1;
      $X->post($post);
   }
}

$sql="delete from kss_exercise_detail";
$X->execute($sql);

glutes(558,1);
quads(558,1);
// Romanian Deadlift
glutes(559,1);
hamstrings(559,0.9);
// Stiff Leg Deadlift
glutes(560,0.9);
hamstrings(560,1);
// 45%
glutes(561,1);
quads(561,1);
// Reset Deadlift
glutes(562,1);
quads(562,1);
hamstrings(562,1);
lower_back(562,1);

glutes(563,1);
quads(563,1);
hamstrings(563,1);
lower_back(563,1);
// Standing Hamstring
hamstrings(564,1);
// Lying Hamstring
hamstrings(565,1);
//Leg Extension Machine
quads(566,0.5);
//Reverse Lunges - Smith
glutes(567,2);
quads(567,2);
//Hack
glutes(568,1);
quads(568,1.2);
//Barbell Squat
glutes(569,1.1);
quads(569,1.1);
// Hip Raises
glutes(570,1);
// Hyperextensions
glutes(571,0.4);
// Standing Calf
calves(572,1);
// Seated Calf
calves(573,1);

//-- CHEST
all_chest(574,1);
triceps(574,0.2);
all_chest(575,1.1);
triceps(575,0.3);
all_chest(576,2);
triceps(576,1);

upper_chest(577,1.1);
lower_chest(577,0.8);
mid_chest(577,1);
front_shoulders(577,0.8);
triceps(577,1);

upper_chest(578,1.2);
lower_chest(578,0.9);
mid_chest(578,1);
front_shoulders(578,0.9);
triceps(578,1);

upper_chest(579,2.2);
lower_chest(579,1.8);
mid_chest(579,2);
front_shoulders(579,1.8);
rear_shoulders(579,1.5);
triceps(579,2);

lower_chest(580,1.2);
mid_chest(580,1);
upper_chest(580,1);
triceps(580,0.5);

mid_chest(581,1);

mid_chest(582,3);

upper_chest(583,1);
mid_chest(583,1);

all_back(584);
all_back(585);

all_back(586,1);
biceps(586,0.6);

all_back(587,1);
biceps(587,0.4);

all_back(588,1);
biceps(588,0.4);

all_back(589,0.5);
biceps(589,0.2);

all_back(590,1);
biceps(590,0.4);

all_back(591,1);

all_shoulders(592);
all_shoulders(593,2);
all_shoulders(594,2);

rear_shoulders(595,2);
rear_shoulders(596,2);

mid_shoulders(597,2);
mid_shoulders(598,1.4);

mid_shoulders(599,3);

biceps(600,2);

biceps(601,1);

biceps(602,2);

triceps(603,2);

triceps(604,1);

biceps(605,1);

traps(606);

traps(607,2);

triceps(608);





