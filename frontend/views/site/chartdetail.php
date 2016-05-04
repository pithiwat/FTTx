<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

//Receive parameter $id
$id = Yii::$app->request->get('id'); //$id = isset($_GET['id']) ? $_GET['id'] : null;
echo $id;

// $this->title = 'Chart Details';
// $this->params['breadcrumbs'][] = $this->title;

/*
//Generate Chart Data.
*/
function getGraphResult($jsonFileName){

	$jsonGetFile = file_get_contents($jsonFileName);
	$jsonDecode = json_decode($jsonGetFile, true);
	$jsonGraphFile = [];
	$chartTitle = [];
	$chartXAxisTitle = [];
	$chartYAxisTitle = [];
	$chartColor = [];

	//Results index interface
	for($i=0; $i<count($jsonDecode['results'][0]['graph']); $i++){

		//Interface
		array_push($jsonGraphFile, renderGraph("./tu_upc/data/".$jsonDecode['results'][0]['name'].'_'.$jsonDecode['node_id'].'_'.$jsonDecode['results'][0]['graph'][$i]['index'].".json", 'no', 'yes')); //Tx

		array_push($chartTitle, $jsonDecode['results'][0]['graph'][$i]['name']);
		array_push($chartXAxisTitle, ['Tx', 'Rx']);
		array_push($chartYAxisTitle, 'bits per second');
		array_push($chartColor, array('#00ff00', '#ffff00'));

	}

	//CPU Utilization
	$cpuutilIndex = (count($jsonDecode['results'][0]['graph'])-1);
	array_push($jsonGraphFile,renderGraph("./tu_upc/data/".$jsonDecode['results'][1]['name'].'_'.$jsonDecode['node_id'].'_'.$jsonDecode['results'][$cpuutilIndex]['graph'][0]['index'].".json", 'yes', 'no'));
	array_push($chartTitle, 'CPU Utilization');
	array_push($chartXAxisTitle, 'CPU');
	array_push($chartYAxisTitle, 'percent');
	array_push($chartColor, array('#ff0000'));

	//Memory Utilization
	$memutilIndex = (count($jsonDecode['results'][0]['graph']));
	array_push($jsonGraphFile,renderGraph("./tu_upc/data/".$jsonDecode['results'][2]['name'].'_'.$jsonDecode['node_id'].'_'.$jsonDecode['results'][$memutilIndex]['graph'][0]['index'].".json", 'no', 'no'));
	array_push($chartTitle, 'Memory Utilization');
	array_push($chartXAxisTitle, 'Memory');
	array_push($chartYAxisTitle, 'bits per second');
	array_push($chartColor, array('#0000ff'));

	return array($jsonGraphFile, $chartTitle, $chartXAxisTitle, $chartYAxisTitle, $chartColor, $cpuutilIndex);
}

function renderGraph($jsonFileName, $checkIsPercent, $checkIsBW){

	$chartData = [];
	$jsonGetFile = file_get_contents($jsonFileName);
	$jsonDecode = json_decode($jsonGetFile, true);

	if($checkIsBW == 'no') { //CPU AND Memory
		for($countTx=0; $countTx<count($jsonDecode['queries'][0]['results'][0]['values']); $countTx++){ //Count Tx
			if($checkIsPercent == 'no') { //Memory
				$TxTime = gmdate("Y-m-d H:i:s", $jsonDecode['queries'][0]['results'][0]['values'][$countTx][0]/1000); //Tx Time
				$TxValue = number_format($jsonDecode['queries'][0]['results'][0]['values'][$countTx][1]*8/1000/1000/1000,2); //Tx
				array_push($chartData, array('date' => $TxTime, 'Memory' => $TxValue));
			}else{ //CPU
				$TxTime = gmdate("Y-m-d H:i:s", $jsonDecode['queries'][0]['results'][0]['values'][$countTx][0]/1000); //Tx Time
				$TxValue = number_format($jsonDecode['queries'][0]['results'][0]['values'][$countTx][1],2); //Tx

				array_push($chartData, array('date' => $TxTime, 'CPU' => $TxValue));
			}
		}
	}else{ //BW
		//for($countRx=0; $countRx<count($jsonDecode['queries'][1]['results'][0]['values']); $countRx++) //Count Rx
		for($countTx=0; $countTx<count($jsonDecode['queries'][0]['results'][0]['values']); $countTx++){
				$TxTime = gmdate("Y-m-d H:i:s", $jsonDecode['queries'][0]['results'][0]['values'][$countTx][0]/1000); //Tx Time
				$TxValue = number_format($jsonDecode['queries'][0]['results'][0]['values'][$countTx][1]*8/1000/1000/1000,2); //Tx

				//$RxTime = gmdate("Y-m-d H:i:s", $jsonDecode['queries'][1]['results'][0]['values'][$countRx][0]/1000); //Rx Time
				$RxValue = number_format($jsonDecode['queries'][1]['results'][0]['values'][$countTx][1]*8/1000/1000/1000,2); //Rx

				array_push($chartData, array('date' => $TxTime, 'Tx' => $TxValue, 'Rx' => $RxValue));
		}
	}

	return $chartData;
}

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

<!-- <h3>FTTx Overview UPC</h3> -->
<?php

	//Amcharts
	$graphResult = getGraphResult('./tu_upc/data/graph_result.json');
	$graphFile = $graphResult[0];
	$graphTitle = $graphResult[1];
	$graphXAxisTitle = $graphResult[2];
	$graphYAxisTitle = $graphResult[3];
	$graphColor = $graphResult[4];
	$graphCpuutilIndex = $graphResult[5];

	//Plot BW Chart
	echo "<div class='row'>";
	echo "<h3 align='center'>Interface Bandwidth Utilization</h3>";
	for($i=0; $i<count($graphCpuutilIndex)+1; $i++){ //Index All Chart BW Interface 
		$chartConfiguration = [
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
		        "id" => "graph1",
		        "fillAlphas" => 0.4,
		        "valueField" => $graphXAxisTitle[$i][0], //Tx XAXIS Title
		        "lineColor" => $graphColor[$i][0], //line color
		        "fillColors" => $graphColor[$i][0], //area color
		         "balloonText" => "<div style='margin:5px; font-size:19px;'>".$graphXAxisTitle[$i][0].":<b>[[value]]</b></div>"
		    ],
			   [
				   "id" => "graph2",
				   "fillAlphas" => 0.4,
				   "valueField" => $graphXAxisTitle[$i][1], //Rx XAXIS Title
				   "lineColor" => $graphColor[$i][1], //line color
				   "fillColors" => $graphColor[$i][1], //area color
				   "balloonText" => "<div style='margin:5px; font-size:19px;'>".$graphXAxisTitle[$i][1].".:<b>[[value]]</b></div>"
			   ]],
			"chartScrollbar" => [
			        "graph" => "graph1",
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

	//Plot CPU and Memory Chart
	$cpu_mem_value_field = ['CPU','Memory'];
	echo "<div class='row'>";
	for($i=count($graphCpuutilIndex)+1; $i<count($graphCpuutilIndex)+3; $i++){ //Index CPU AND Memory Utilization
		$chartConfiguration = [
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
		        "id" => "graph1",
		        "fillAlphas" => 0.4,
		        "valueField" => $graphXAxisTitle[$i], //CPU AND Memory XAXIS Title
		        "lineColor" => $graphColor[$i][0], //line color
		        "fillColors" => $graphColor[$i][0], //area color
		         "balloonText" => "<div style='margin:5px; font-size:19px;'>".$graphXAxisTitle[$i].":<b>[[value]]</b></div>"
		    ]],
			"chartScrollbar" => [
			        "graph" => "graph1",
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
		//echo "<h3 align='center'>Interface Bandwidth Utilization</h3>";
		echo speixoto\amcharts\Widget::widget(['chartConfiguration' => $chartConfiguration]);	
		echo "</div>";
	}
	echo "</div>";
?>

	</div>

</div>

