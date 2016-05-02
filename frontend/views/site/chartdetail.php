<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

//$id = isset($_GET['id']) ? $_GET['id'] : null;
$id = Yii::$app->request->get('id');
echo $id;

$this->title = 'Chart Details';
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
	    if($groupJson == $valueJson['group']){
	    	echo Html::a("<span data-toggle='tooltip' title=".$valueJson['node_name']." style='color:".checkStatus($valueJson).";' class='glyphicon glyphicon-stop' aria-hidden='true'></span>", './iframe.html', ['rel' => 'fancybox_iframe']);
	?>
	<?php
		}elseif ($groupJson == 'Other') {
			echo Html::a("<span data-toggle='tooltip' title=".$valueJson['node_name']." style='color:".checkStatus($valueJson).";' class='glyphicon glyphicon-stop' aria-hidden='true'></span>", './iframe.html', ['rel' => 'fancybox_iframe']);
			?>
			<?php
		}
	}
}

function checkGroupNum($jsonFile,$groupJson){
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

function getGraphResult($jsonFileName){
	$jsonGetFile = file_get_contents($jsonFileName);
	$jsonDecode = json_decode($jsonGetFile, true);
	$jsonGraphFile = [];
	$chartTitle = [];
	$chartYAxisTitle = [];
	$chartColor = [];

	//results index interface
	for($i=0;$i<count($jsonDecode['results'][0]['graph']);$i++){

		//Interface
		array_push($jsonGraphFile,renderGraph("./tu_upc/data/".$jsonDecode['results'][0]['name'].'_'.$jsonDecode['node_id'].'_'.$jsonDecode['results'][0]['graph'][$i]['index'].".json",'no'));
		array_push($chartTitle, 'Interface Bandwidth Utilization');
		array_push($chartYAxisTitle, 'bits per second');
		array_push($chartColor, '#00ff00');

	}

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

	return array($jsonGraphFile,$chartTitle,$chartYAxisTitle,$chartColor);

}

function renderGraph($jsonFileName,$checkPercent){
	$chartData = [];
	$jsonGetFile = file_get_contents($jsonFileName);
	$jsonDecode = json_decode($jsonGetFile, true);
	//Tx
	for($countTx=0;$countTx<count($jsonDecode['queries'][0]['results'][0]['values']);$countTx++){
		if($checkPercent == 'no') {
			$TxTime = gmdate("Y-m-d H:i:s",$jsonDecode['queries'][0]['results'][0]['values'][$countTx][0]/1000); //Tx Time
			$TxValue = number_format($jsonDecode['queries'][0]['results'][0]['values'][$countTx][1]*8/1000/1000/1000,2); //Tx
			array_push($chartData, array('date' => $TxTime,'visits' => $TxValue));
		}else{
			$TxTime = gmdate("Y-m-d H:i:s",$jsonDecode['queries'][0]['results'][0]['values'][$countTx][0]/1000); //Tx Time
			$TxValue = number_format($jsonDecode['queries'][0]['results'][0]['values'][$countTx][1],2); //Tx
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

function readJson($jsonFileName){
	$jsonGetFile = file_get_contents($jsonFileName);
	$jsonDecode = json_decode($jsonGetFile, true);
	return $jsonDecode;
}

?>
	
<?php

?>
<div class="site-index">

	<div class="body-content">

	<?php

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

?>

		<h3>FTTx Overview UPC</h3>
		<?php

			//Amcharts
			$graphResult = getGraphResult('./tu_upc/data/graph_result.json');
			$graphFile = $graphResult[0];
			$graphTitle = $graphResult[1];
			$graphYAxisTitle = $graphResult[2];
			$graphColor = $graphResult[3];

			echo "<div class='row'>";
			for($i=0;$i<count($graphFile);$i++){
				$chartConfiguration = [
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
				echo speixoto\amcharts\Widget::widget(['chartConfiguration' => $chartConfiguration]);	
				echo "</div>";
			}
			echo "</div>";
		?>

</div>

