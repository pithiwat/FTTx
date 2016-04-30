<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
/*
$posts = Yii::$app->db->createCommand("SELECT * FROM year_node_summary where Date = '2016-03'")
            ->queryAll();
foreach ($posts as $data) {
	//echo $data['Village'];
}
*/

$this->title = 'Monitoring Screen UPC';
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
	    	echo Html::a("<span data-toggle='tooltip' title='Hooray!' style='color:".checkStatus($valueJson).";' class='glyphicon glyphicon-stop' aria-hidden='true'></span>", './iframe.html', ['rel' => 'fancybox_iframe']);
	?>
		<?php 
		/*
		<a href="index.php?r=site%2Foverviewupc"><span style="color:<?php echo checkStatus($valueJson); ?>" class="glyphicon glyphicon-stop" aria-hidden="true"></span></a>
		*/
		
		?>
	<?php
		}elseif ($groupJson == 'Other') {
			echo Html::a("<span style='color:".checkStatus($valueJson).";' class='glyphicon glyphicon-stop' aria-hidden='true'></span>", './iframe.html', ['rel' => 'fancybox_iframe']);
			?>
			<?php
			/*
			<a href="index.php?r=site%2Foverviewupc"><span style="color:<?php echo checkStatus($valueJson); ?>" class="glyphicon glyphicon-stop" aria-hidden="true"></span></a>
			*/
			?>
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
//readJson('C:\xampp\htdocs\gpon\views\site\tu_upc\node_level_cn.json');

?>
	

<?php
/*
$jsonGetFile = file_get_contents('C:\xampp\htdocs\gpon\views\site\tu_upc\node_level_cn.json');
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

	<?php
/*
echo newerton\fancybox\FancyBox::widget([
    'target' => 'a[rel=fancybox]',
    'helpers' => true,
    'mouse' => true,
    'config' => [
        'maxWidth' => '90%',
        'maxHeight' => '90%',
        'playSpeed' => 7000,
        'padding' => 0,
        'fitToView' => false,
        'width' => '70%',
        'height' => '70%',
        'autoSize' => false,
        'closeClick' => false,
        'openEffect' => 'elastic',
        'closeEffect' => 'elastic',
        'prevEffect' => 'elastic',
        'nextEffect' => 'elastic',
        'closeBtn' => false,
        'openOpacity' => true,
        'helpers' => [
            'title' => ['type' => 'float'],
            'buttons' => [],
            'thumbs' => ['width' => 68, 'height' => 50],
            'overlay' => [
                'css' => [
                    'background' => 'rgba(0, 0, 0, 0.8)'
                ]
            ]
        ],
    ]
]);
*/
echo newerton\fancybox\FancyBox::widget([
    'target' => 'a[rel=fancybox_iframe]', //id fancybox
    'mouse' => false,
    'config' => [
    	'type' => 'iframe', //Edit FancyBox Type 
        'maxWidth' => '100%',
        'maxHeight' => '100%',
        'padding' => 0,
        'fitToView' => true,
        'width' => '70%',
        'height' => '70%',
        'autoSize' => false,
        'closeBtn' => true, //Close Button On Top Left
        'scrolling' => 'auto', //Iframe Settings
		'preload'   => true, //Iframe Settings
		'arrows' => false //Close Nav Arrows
    ]
]);

/*
echo Html::a('<span style="color:<?php echo checkStatus($valueJson); ?>" class="glyphicon glyphicon-stop" aria-hidden="true"></span>', './iframe.html', ['rel' => 'fancybox_iframe']);*/

//echo Html::a('Show Graph', '@web/img/1_b.jpg', ['rel' => 'fancybox']);
//echo Html::a('Show Graph', './iframe.html', ['rel' => 'fancybox_iframe']);
//echo Html::a('Show Graph',['/site/overviewupc'], ['rel' => 'fancybox_iframe']);
//echo Html::a(Html::img('@web/img/1_b.jpg'), '@web/img/1_b.jpg', ['rel' => 'fancybox']);

//Amcharts
$chartConfiguration = [
    'type'         => 'serial',
    'dataProvider' => [['year'  => 2005, 'income' => 23.5],
                       ['year' => 2006, 'income' => 26.2],
                       ['year' => 2007, 'income' => 30.1]
                      ],
   'categoryField' =>  'year',
   'rotate'        => true,
 
   'categoryAxis' => ['gridPosition' => 'start', 'axisColor' => '#DADADA'],
   'valueAxes'    => [['axisAlpha' => 0.2]],
   'graphs'       => [['type' => 'column',
                       'title' => 'Income',
                       'valueField' => 'income',
                       'lineAlpha' => 0,
                       'fillColors' => '#ADD981',
                       'fillAlphas' => 0.8,
                       'balloonText' => '[[title]] in [[category]]:<b>[[value]]</b>'
                     ]]
];
echo speixoto\amcharts\Widget::widget(['chartConfiguration' => $chartConfiguration]);

?>

		<h3>FTTx Overview UPC</h3>
		<div class="row">
		  <div class="col-xs-6 col-md-6">
		  <h4>1. CN MTG: (<?php echo checkGroupNum(readJson('./tu_upc/node_level_cn.json'),"MTG"); ?>)</h4>
			<?php
			//$jsonDecode = readJson('./tu_upc/node_level_cn.json')
			checkGroup(readJson('./tu_upc/node_level_cn.json'),"MTG");
			?>

		  </div>
		  <div class="col-xs-6 col-md-6">
		  	<h4>1. CN TYN: (<?php echo checkGroupNum(readJson('./tu_upc/node_level_cn.json'),"TYN"); ?>)</h4>
			<?php
			checkGroup(readJson('./tu_upc/node_level_cn.json'),"TYN");
			?>

		  </div>
		</div>


		<div class="row">
		  <div class="col-xs-12 col-md-12">
		  <h4>2. RN: (<?php echo checkGroupNum(readJson('./tu_upc/node_level_rn.json'),"Other"); ?>)</h4>
			<?php
			//$jsonDecode = readJson('./tu_upc/node_level_rn.json')
			checkGroup(readJson('./tu_upc/node_level_rn.json'),"Other");
			?>

		  </div>
		</div>	

		<div class="row">
		  <div class="col-xs-12 col-md-12">
		  <h4>3. PN: (<?php echo checkGroupNum(readJson('./tu_upc/node_level_pn.json'),"Other"); ?>)</h4>
			<?php
			//$jsonDecode = readJson('./tu_upc/node_level_pn.json')
			checkGroup(readJson('./tu_upc/node_level_pn.json'),"Other");
			?>

		  </div>
		</div>

		<div class="row">
		  <div class="col-xs-12 col-md-12">
		  <h4>4. DN: (<?php echo checkGroupNum(readJson('./tu_upc/node_level_dn.json'),"Other"); ?>)</h4>
			<?php
			//$jsonDecode = readJson('./tu_upc/node_level_dn.json')
			checkGroup(readJson('./tu_upc/node_level_dn.json'),"Other");
			?>

		  </div>
		</div>	

		<div class="row">
		  <div class="col-xs-12 col-md-12">
		  <h4>5. CPE: (<?php echo checkGroupNum(readJson('./tu_upc/node_level_cpe.json'),"Other"); ?>)</h4>
			<?php
			//$jsonDecode = readJson('./tu_upc/node_level_cpe.json')
			checkGroup(readJson('./tu_upc/node_level_cpe.json'),"Other");
			?>

		  </div>
		</div>	

	</div>

</div>

