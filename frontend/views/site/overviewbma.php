<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
/*
$posts = Yii::$app->db->createCommand("SELECT * FROM year_node_summary where Date = '2016-03'")
            //->queryAll();
foreach ($posts as $data) {
	//echo $data['Village'];
}
*/

$this->title = 'Monitoring Screen BMA';
$this->params['breadcrumbs'][] = $this->title;

$statusColor = "";
function checkStatus($varStatus){
	if($varStatus['status'] == "ERROR"){
    	$statusColor = "#ff0000";
    }
    elseif($varStatus['status'] == "WARNING"){
    	$statusColor = "#ffff00";
    }else{
    	$statusColor = "#00cc00";
    }
    return $statusColor;
}

function checkGroup($jsonFile,$groupJson){
	foreach ($jsonFile as $keyJson => $valueJson) {
	    //echo $person_a['id'];
	    //echo "<br>";
	    //echo $valueJson['group'];
	    //echo "<br>";
	    if($groupJson == $valueJson['group']){
	?>
		<a href="index.php?r=site%2Foverviewbma"><span style="color:<?php echo checkStatus($valueJson); ?>" class="glyphicon glyphicon-stop" aria-hidden="true"></span></a>
	<?php
		}elseif ($groupJson == 'Other') {
			?>
			<a href="index.php?r=site%2Foverviewbma"><span style="color:<?php echo checkStatus($valueJson); ?>" class="glyphicon glyphicon-stop" aria-hidden="true"></span></a>
			<?php
		}
	}
}

function checkGroupNum($jsonFile,$groupJson){
	$groupCount=0;
	foreach ($jsonFile as $keyJson => $valueJson) {
	    //echo $person_a['id'];
	    //echo "<br>";
	    if($valueJson['group'] == $groupJson){
	    	$groupCount +=1;
		}elseif ($groupJson == 'Other') {
			$groupCount +=1;
		}
	}
	return $groupCount;
}

function readJson($jsonFileName){
	$jsonGetFile = file_get_contents($jsonFileName);
	$jsonDecode = json_decode($jsonGetFile, true);
	return $jsonDecode;
}
//readJson('C:\xampp\htdocs\gpon\views\site\tu_bkk\node_level_cn.json');

?>
	

<?php
/*
$jsonGetFile = file_get_contents('C:\xampp\htdocs\gpon\views\site\tu_bkk\node_level_cn.json');
$jsonDecode = json_decode($jsonGetFile, true);


$jsonIterator = new RecursiveIteratorIterator(
    new RecursiveArrayIterator(json_decode($jsonGetFile, TRUE)),
    RecursiveIteratorIterator::SELF_FIRST);

foreach ($jsonIterator as $key => $val) {
    if(is_array($val)) {
        //echo "$key:\n";
    } else {
        //echo "$key => $val\n";
    }
}
*/

?>
<div class="site-index">

	<div class="body-content">
		<h3>FTTx Overview BMA</h3>
		<div class="row">
		  <div class="col-xs-6 col-md-6">
		  <h4>1. CN MTG: (<?php echo checkGroupNum(readJson('./tu_bkk/node_level_cn.json'),"MTG"); ?>)</h4>
			<?php
			//$jsonDecode = readJson('./tu_bkk/node_level_cn.json')
			checkGroup(readJson('./tu_bkk/node_level_cn.json'),"MTG");
			?>

		  </div>
		  <div class="col-xs-6 col-md-6">
		  	<h4>1. CN TYN: (<?php echo checkGroupNum(readJson('./tu_bkk/node_level_cn.json'),"TYN"); ?>)</h4>
			<?php
			checkGroup(readJson('./tu_bkk/node_level_cn.json'),"TYN");
			?>

		  </div>
		</div>


		<div class="row">
		  <div class="col-xs-12 col-md-12">
		  <h4>2. AGN: (<?php echo checkGroupNum(readJson('./tu_bkk/node_level_agn.json'),"Other"); ?>)</h4>
			<?php
			//$jsonDecode = readJson('./tu_bkk/node_level_agn.json')
			checkGroup(readJson('./tu_bkk/node_level_agn.json'),"Other");
			?>

		  </div>
		</div>	

		<div class="row">
		  <div class="col-xs-12 col-md-12">
		  <h4>3. AN: (<?php echo checkGroupNum(readJson('./tu_bkk/node_level_an.json'),"Other"); ?>)</h4>
			<?php
			//$jsonDecode = readJson('./tu_bkk/node_level_an.json')
			checkGroup(readJson('./tu_bkk/node_level_an.json'),"Other");
			?>

		  </div>
		</div>

	</div>

</div>

