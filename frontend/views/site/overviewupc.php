<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

//Title
$this->title = 'Monitoring Screen UPC';
$this->params['breadcrumbs'][] = $this->title;

/*
//Function check status color
*ERROR = RED
*WARNING = YELLOW
*OK = GREEN
*/
$statusColor = "";
function checkStatus($varStatus){
	if($varStatus['status'] == "ERROR"){
    	$statusColor = "#ff0000"; //Red
    }
    elseif($varStatus['status'] == "WARNING"){
    	$statusColor = "#ffff00"; //Yellow
    }else{
    	$statusColor = "#00cc00"; //Green
    }
    return $statusColor;
}

/*
//Function check node group
*Split MTG AND TYN
*Other means other site except (MTG AND TYN).
*/
function checkGroup($jsonFile, $groupJson){
	foreach ($jsonFile as $keyJson => $valueJson) {
	    if($groupJson == $valueJson['group']){ //MTG AND TYN
	    	echo Html::a("<span data-toggle='tooltip' title=".$valueJson['node_name']." style='color:".checkStatus($valueJson).";' class='glyphicon glyphicon-stop' aria-hidden='true'></span>", Url::toRoute(['site/chartdetail', 'id' => $valueJson['id']]), ['rel' => 'fancybox_iframe']);
		}elseif ($groupJson == 'Other') {
			echo Html::a("<span data-toggle='tooltip' title=".$valueJson['node_name']." style='color:".checkStatus($valueJson).";' class='glyphicon glyphicon-stop' aria-hidden='true'></span>", Url::toRoute(['site/chartdetail', 'id' => $valueJson['id']]), ['rel' => 'fancybox_iframe']);
		}
	}
}

/*
//Function count number node in group.
*/
function checkGroupNum($jsonFile, $groupJson){
	$groupCount=0;
	foreach ($jsonFile as $keyJson => $valueJson) {
	    if($valueJson['group'] == $groupJson){
	    	$groupCount +=1;
		}elseif ($groupJson == 'Other') {
			$groupCount +=1;
		}
	}
	return $groupCount;
}

/*
//Function read JSON File.
*/
function readJson($jsonFileName){
	$jsonGetFile = file_get_contents($jsonFileName);
	$jsonDecode = json_decode($jsonGetFile, true);
	return $jsonDecode;
}

?>

<div class="site-index">

	<div class="body-content">

<?php
	
	//Fancy Box plugin properties.
	echo newerton\fancybox\FancyBox::widget([
	    'target' => 'a[rel=fancybox_iframe]', //id fancybox
	    'mouse' => false,
	    'config' => [
	    	'type' => 'iframe', //Edit FancyBox Type 
	        'maxWidth' => '100%',
	        'maxHeight' => '100%',
	        'padding' => 0,
	        'fitToView' => true,
	        'width' => '100%',
	        'height' => '70%',
	        'autoSize' => true,
	        'closeBtn' => true, //Close Button On Top Left
	        'scrolling' => 'auto', //Iframe Settings
			'preload'   => true, //Iframe Settings
			'arrows' => false //Close Nav Arrows
	    ]
	]);

?>

		<h3>FTTx Overview UPC</h3>
		<div class="row">
		  <div class="col-xs-6 col-md-6">
		  <h4>1. CN MTG: (<?php echo checkGroupNum(readJson('./tu_upc/node_level_cn.json'), "MTG"); ?>)</h4>

			<?php checkGroup(readJson('./tu_upc/node_level_cn.json'), "MTG"); ?>

		  </div>
		  <div class="col-xs-6 col-md-6">
		  	<h4>1. CN TYN: (<?php echo checkGroupNum(readJson('./tu_upc/node_level_cn.json'), "TYN"); ?>)</h4>

			<?php checkGroup(readJson('./tu_upc/node_level_cn.json'), "TYN"); ?>

		  </div>
		</div>


		<div class="row">
		  <div class="col-xs-12 col-md-12">
		  <h4>2. RN: (<?php echo checkGroupNum(readJson('./tu_upc/node_level_rn.json'), "Other"); ?>)</h4>
			
			<?php checkGroup(readJson('./tu_upc/node_level_rn.json'), "Other"); ?>

		  </div>
		</div>	

		<div class="row">
		  <div class="col-xs-12 col-md-12">
		  <h4>3. PN: (<?php echo checkGroupNum(readJson('./tu_upc/node_level_pn.json'), "Other"); ?>)</h4>

			<?php checkGroup(readJson('./tu_upc/node_level_pn.json'), "Other"); ?>

		  </div>
		</div>

		<div class="row">
		  <div class="col-xs-12 col-md-12">
		  <h4>4. DN: (<?php echo checkGroupNum(readJson('./tu_upc/node_level_dn.json'), "Other"); ?>)</h4>
			
			<?php checkGroup(readJson('./tu_upc/node_level_dn.json'), "Other"); ?>

		  </div>
		</div>	

		<div class="row">
		  <div class="col-xs-12 col-md-12">
		  <h4>5. CPE: (<?php echo checkGroupNum(readJson('./tu_upc/node_level_cpe.json'), "Other"); ?>)</h4>
			
			<?php checkGroup(readJson('./tu_upc/node_level_cpe.json'), "Other"); ?>

		  </div>
		</div>	

	</div>

</div>

