<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
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
	    	echo Html::a("<span data-toggle='tooltip' title=".$valueJson['node_name']." style='color:".checkStatus($valueJson).";' class='glyphicon glyphicon-stop' aria-hidden='true'></span>", Url::toRoute(['site/chartdetail', 'id' => $valueJson['id']]), ['rel' => 'fancybox_iframe']);
	?>
		<?php 
		/*
		echo Html::a("<span data-toggle='tooltip' title=".$valueJson['node_name']." style='color:".checkStatus($valueJson).";' class='glyphicon glyphicon-stop' aria-hidden='true'></span>", './iframe.html', ['rel' => 'fancybox_iframe']);

		<a href="index.php?r=site%2Foverviewupc"><span style="color:<?php echo checkStatus($valueJson); ?>" class="glyphicon glyphicon-stop" aria-hidden="true"></span></a>
		*/
		
		?>
	<?php
		}elseif ($groupJson == 'Other') {
			echo Html::a("<span data-toggle='tooltip' title=".$valueJson['node_name']." style='color:".checkStatus($valueJson).";' class='glyphicon glyphicon-stop' aria-hidden='true'></span>", Url::toRoute(['site/chartdetail', 'id' => $valueJson['id']]), ['rel' => 'fancybox_iframe']);
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

function getGraphResult($jsonFileName){
	$jsonGetFile = file_get_contents($jsonFileName);
	$jsonDecode = json_decode($jsonGetFile, true);
	$jsonGraphFile = [];
	$chartTitle = [];
	$chartYAxisTitle = [];
	$chartColor = [];
	/****
	print_r($jsonDecode);
	echo "<br>"; 
	echo $jsonDecode['name'];
	echo "<br>"; 
	print_r($jsonDecode['results']);
	echo "<br>"; 

	//intutil
	echo $jsonDecode['results'][0]['name'];
	echo "<br>"; 

	//echo $jsonDecode['results'][0]['graph'][0]['name'];
	//echo "<br>"; 
	****/

	//results index interface
	for($i=0;$i<count($jsonDecode['results'][0]['graph']);$i++){
		/****
		echo $jsonDecode['results'][0]['name'].'_'.$jsonDecode['node_id'].'_'.$jsonDecode['results'][0]['graph'][$i]['index'];
		echo "<br>"; 
		echo $jsonDecode['results'][0]['graph'][$i]['name'];
		echo "<br>"; 
		****/
		//echo $jsonDecode['results'][0]['graph'][$i]['name'];
		//echo "<br>"; 

		//Interface
		array_push($jsonGraphFile,renderGraph("./tu_upc/data/".$jsonDecode['results'][0]['name'].'_'.$jsonDecode['node_id'].'_'.$jsonDecode['results'][0]['graph'][$i]['index'].".json",'no'));
		array_push($chartTitle, 'Interface Bandwidth Utilization');
		array_push($chartYAxisTitle, 'bits per second');
		array_push($chartColor, '#00ff00');

	}
	
	/***
	//cpuutil
	$cpuutilIndex = (count($jsonDecode['results'][0]['graph'])-1);
	echo $jsonDecode['results'][1]['name'].'_'.$jsonDecode['node_id'].'_'.$jsonDecode['results'][$cpuutilIndex]['graph'][0]['index'];
	echo "<br>"; 
	//memutil
	$memutilIndex = (count($jsonDecode['results'][0]['graph']));
	echo $jsonDecode['results'][2]['name'].'_'.$jsonDecode['node_id'].'_'.$jsonDecode['results'][$memutilIndex]['graph'][0]['index'];
	echo "<br>"; 
	***/
	

	//cpuutil
	$cpuutilIndex = (count($jsonDecode['results'][0]['graph'])-1);
	array_push($jsonGraphFile,renderGraph("./tu_upc/data/".$jsonDecode['results'][1]['name'].'_'.$jsonDecode['node_id'].'_'.$jsonDecode['results'][$cpuutilIndex]['graph'][0]['index'].".json",'yes'));
	array_push($chartTitle, 'CPU Utilization');
	array_push($chartYAxisTitle, 'percent');
	array_push($chartColor, '#ff0000');

	//memutil
	$memutilIndex = (count($jsonDecode['results'][0]['graph']));
	array_push($jsonGraphFile,renderGraph("./tu_upc/data/".$jsonDecode['results'][2]['name'].'_'.$jsonDecode['node_id'].'_'.$jsonDecode['results'][$memutilIndex]['graph'][0]['index'].".json",'no'));
	array_push($chartTitle, 'Memory Utilization');
	array_push($chartYAxisTitle, 'bits per second');
	array_push($chartColor, '#0000ff');
	/*
	foreach ($jsonFile as $keyJson => $valueJson) {
		echo $valueJson['results'];
	    echo "<br>"; 
	}
	*/
	return array($jsonGraphFile,$chartTitle,$chartYAxisTitle,$chartColor);
	//return renderGraph("./tu_upc/data/".$jsonDecode['results'][0]['name'].'_'.$jsonDecode['node_id'].'_'.$jsonDecode['results'][0]['graph'][0]['index'].".json");
}

function renderGraph($jsonFileName,$checkPercent){
	$chartData = [];
	$jsonGetFile = file_get_contents($jsonFileName);
	$jsonDecode = json_decode($jsonGetFile, true);
	//echo $jsonFileName;
	//echo $checkPercent;
	//Tx
	//print_r($jsonDecode['queries'][0]['results'][0]['values']);
	//print_r($jsonDecode['queries'][0]['results'][0]['values'][0]);
	for($countTx=0;$countTx<count($jsonDecode['queries'][0]['results'][0]['values']);$countTx++){
		if($checkPercent == 'no') {
			$TxTime = gmdate("Y-m-d H:i:s",$jsonDecode['queries'][0]['results'][0]['values'][$countTx][0]/1000); //Tx Time
			$TxValue = number_format($jsonDecode['queries'][0]['results'][0]['values'][$countTx][1]*8/1000/1000/1000,2); //Tx
			//echo number_format($jsonDecode['queries'][0]['results'][0]['values'][$countTx][1],2);
			//echo "<br>"; 
			array_push($chartData, array('date' => $TxTime,'visits' => $TxValue));
		}else{
			$TxTime = gmdate("Y-m-d H:i:s",$jsonDecode['queries'][0]['results'][0]['values'][$countTx][0]/1000); //Tx Time
			$TxValue = number_format($jsonDecode['queries'][0]['results'][0]['values'][$countTx][1],2); //Tx
			//echo number_format($jsonDecode['queries'][0]['results'][0]['values'][$countTx][1],2);
			//echo "<br>"; 
			array_push($chartData, array('date' => $TxTime,'visits' => $TxValue));
		}
	}
	//Rx
	//print_r($jsonDecode['queries'][1]['results'][0]['values'][0]);
	//echo "<br>"; 
	//for($countRx=0;$countRx<count($jsonDecode['queries'][1]['results'][0]['values']);$countRx++){
		//echo gmdate("Y-m-d H:i:s",$jsonDecode['queries'][1]['results'][0]['values'][$countRx][0]/1000); //Rx Time
		//echo number_format($jsonDecode['queries'][1]['results'][0]['values'][$countRx][1]*8/1000/1000/1000,2); //Rx
		//echo "<br>"; 
	//}
	return $chartData;
}
//print_r($graphYAxisTitle);
//getGraphResult('./tu_upc/data/graph_result.json');
//print_r(renderGraph('./tu_upc/data/cpuutil_18002_0.json'));

/*Time
$timestamp=1461810011000/1000;
echo gmdate("Y-m-d H:i:s", $timestamp);
echo "<br>"; 
echo date('Y-m-d H:i:s', 1222431569); 
*/

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
        'width' => '100%',
        'height' => '70%',
        'autoSize' => true,
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
function generateChartData(){
	$jsonGetFile = file_get_contents('./tu_bkk/test.json');
	$jsonDecode = json_decode($jsonGetFile, true);
	$chartData = [];
	foreach ($jsonDecode as $keyJson => $valueJson) {
		//echo $valueJson['year'];
		//echo "<br>";
		array_push($chartData, array('year' => $valueJson['year'],'income' => $valueJson['income']));
	}
	//print_r($chartData);
	return $chartData;
}
//generateChartData();

//Amcharts
$chartConfiguration = [
	//$chartData  => generateChartData(),
    'type'         => 'serial',
    'theme'        => 'light',
    /*
    'dataProvider' => [['year'  => 2005, 'income' => 23.5],
                       ['year' => 2006, 'income' => 26.2],
                       ['year' => 2007, 'income' => 30.1]
                      ],
    */
    'dataProvider' => generateChartData(),
                      

   'categoryField' =>  'year',
   'rotate'        => true,
   "chartCursor" => [
        "categoryBalloonDateFormat" => "JJ:NN, DD MMMM",
        "cursorPosition" => "mouse"
    ],
 
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

// generate some random data, quite different range
function generateChartData2() {
    $chartData = [];
    // current date
    $firstDate = date("Y-m-d H:i:s");
	$time = strtotime($firstDate);
    // now set 500 minutes back
    $time = $time - (500 * 60);
    $firstDate = date("Y-m-d H:i:s", $time);

    // and generate 500 data items
    for ($i = 0; $i < 500; $i++) {
        $newDate = $firstDate;
        $addtime = strtotime($newDate);
        // each time we add one minute
        $addtime = $addtime + ($i * 60);
        $newDate = date("Y-m-d H:i:s", $addtime);
        // some random number
        $visits = rand(250,600);
        // add data item to the array
        array_push($chartData, array('date' => $newDate,'visits' => $visits));
        //$chartData.push({
            //date: $newDate,
            //visits: $visits
        //});
    }
    return $chartData;
}
//print_r(generateChartData2());

$graphResult = getGraphResult('./tu_upc/data/graph_result.json');
$graphFile = $graphResult[0];
$graphTitle = $graphResult[1];
$graphYAxisTitle = $graphResult[2];
$graphColor = $graphResult[3];

echo "<div class='row'>";
for($i=0;$i<count($graphFile);$i++){
	$chartConfiguration2 = [
		//$chartData  => generateChartData(),
	    'type'         => 'serial',
	    'theme'        => 'light',
	    "marginRight"  => 80,
	    "titles" => [
			[
				"text" => $graphTitle[$i],
				"size" => 15
			]
		],
	    'dataProvider' => $graphFile[$i],
	    'valueAxes'    => [["position" => "left",
					        "title" => $graphYAxisTitle[$i]]],
	   "graphs" => [[
	        "id" => "g1",
	        "fillAlphas" => 0.4,
	        "valueField" => "visits",
	        "lineColor" => $graphColor[$i], //line color
	        "fillColors" => $graphColor[$i], //area color
	         "balloonText" => "<div style='margin:5px; font-size:19px;'>Visits:<b>[[value]]</b></div>"
	    ]],
		"chartScrollbar" => [
		        "graph" => "g1",
		        "scrollbarHeight" => 80,
		        "backgroundAlpha" => 0,
		        "selectedBackgroundAlpha" => 0.1,
		        "selectedBackgroundColor" => "#888888",
		        "graphFillAlpha" => 0,
		        "graphLineAlpha" => 0.5,
		        "selectedGraphFillAlpha" => 0,
		        "selectedGraphLineAlpha" => 1,
		        "autoGridCount" => true,
		        "color" => "#AAAAAA"
		    ],
		    "chartCursor" => [
		        "categoryBalloonDateFormat" => "JJ:NN, DD MMMM",
		        "cursorPosition" => "mouse"
		    ],
		    "categoryField" => "date",
		    "categoryAxis" => [
		        "minPeriod" => "mm",
		        "parseDates" => true
		    ],
		    "export" => [
		        "enabled" => true,
		         "dateFormat" => "YYYY-MM-DD HH:NN:SS"
		    ]
		];
	echo  "<div class='col-xs-6 col-md-6'>";
	echo speixoto\amcharts\Widget::widget(['chartConfiguration' => $chartConfiguration2]);	
	echo "</div>";
}
echo "</div>";
/*
echo "<div class='row'>";
	echo  "<div class='col-xs-6 col-md-6'>";
	echo speixoto\amcharts\Widget::widget(['chartConfiguration' => $chartConfiguration2]);
	echo "</div>";
	echo  "<div class='col-xs-6 col-md-6'>";
	echo speixoto\amcharts\Widget::widget(['chartConfiguration' => $chartConfiguration2]);
	echo "</div>";
echo "</div>";
*/
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

