@extends('layouts.master')

@section('content')
<div class="page-header">
	<h1>Compose {{ $curve1->name }} and {{ $curve2->name }}.</h1>
</div>
<h2>{{ $curve1->name }} Load Chart</h2>
<div id="load1Chartdiv" style="width: 100%; height: 400px;"></div>
<h2>{{ $curve2->name }} Load Chart</h2>
<div id="load2Chartdiv" style="width: 100%; height: 400px;"></div>
<h2>Composite Load Chart</h2>
<div id="loadCompChartdiv" style="width: 100%; height: 400px;"></div>
<h2>Demand Charges</h2>
<div id="demandChartdiv" style="width: 100%; height: 400px;"></div>
@stop

@section('body_post')
{!! Html::script('js/amcharts/amcharts.js') !!}
{!! Html::script('js/amcharts/xy.js') !!}
{!! Html::script('js/amcharts/serial.js') !!}
<script>
            var demandChart;

            var demandData = [
		@foreach($demand as $key => $val)
                {
                    "ax": {{ $val[0] }},
                    "ay": {{ $val[1] }},
                },
		@endforeach
            ];

	    var load1Chart;

            var load1Data = [
		@foreach($curve1->parse_data() as $point)
                {
                    "ax": {{ $point[0] }},
                    "ay": {{ $point[1] }},
                },
		@endforeach
            ];

	    var load2Chart;

            var load2Data = [
		@foreach($curve2->parse_data() as $point)
                {
                    "ax": {{ $point[0] }},
                    "ay": {{ $point[1] }},
                },
		@endforeach
            ];

	    var loadCompChart;

            var loadCompData = [
		@foreach($combined as $key => $val)
                {
                    "ax": {{ $key }},
                    "ay": {{ $val[1] }},
                    "bx": {{ $key }},
                    "by": {{ $val[2] }},
                    "cx": {{ $key }},
                    "cy": {{ $val[0] }},
                },
		@endforeach
            ];

	AmCharts.ready(function () {
                // SERIAL CHART
                demandChart = new AmCharts.AmSerialChart();
                demandChart.dataProvider = demandData;
                demandChart.categoryField = "ax";
                demandChart.startDuration = 1;

                // AXES
                // category
                var categoryAxis = demandChart.categoryAxis;
                categoryAxis.labelRotation = 90;
                categoryAxis.gridPosition = "start";

                // value
                // in case you don't want to change default settings of value axis,
                // you don't need to create it, as one value axis is created automatically.

                // GRAPH
                var graph = new AmCharts.AmGraph();
                graph.valueField = "ay";
                graph.balloonText = "[[category]]: <b>[[value]]</b>";
                graph.type = "column";
                graph.lineAlpha = 0;
                graph.fillAlphas = 0.8;
                demandChart.addGraph(graph);

                // CURSOR
                var demandChartCursor = new AmCharts.ChartCursor();
                demandChartCursor.cursorAlpha = 0;
                demandChartCursor.zoomable = false;
                demandChartCursor.categoryBalloonEnabled = false;
                demandChart.addChartCursor(demandChartCursor);

                demandChart.creditsPosition = "top-right";

                demandChart.write("demandChartdiv");

                // LOAD 1 CHART
                load1Chart = new AmCharts.AmXYChart();

                load1Chart.dataProvider = load1Data;
                load1Chart.startDuration = 1;

                // AXES
                // X
                var xAxis = new AmCharts.ValueAxis();
                xAxis.title = "Time (Minutes)";
                xAxis.position = "bottom";
                xAxis.dashLength = 1;
                xAxis.axisAlpha = 0;
                xAxis.autoGridCount = true;
                load1Chart.addValueAxis(xAxis);

                // Y
                var yAxis = new AmCharts.ValueAxis();
                yAxis.position = "left";
                yAxis.title = "Load (Watts)";
                yAxis.dashLength = 1;
                yAxis.axisAlpha = 0;
                yAxis.autoGridCount = true;
                load1Chart.addValueAxis(yAxis);

                // GRAPHS
                // triangles up
                var graph1 = new AmCharts.AmGraph();
                graph1.lineColor = "#FF6600";
                graph1.balloonText = "x:[[x]] y:[[y]]";
                graph1.xField = "ax";
                graph1.yField = "ay";
                graph1.lineAlpha = 0;
                graph1.bullet = "circle";
                load1Chart.addGraph(graph1);

                // CURSOR
                var load1ChartCursor = new AmCharts.ChartCursor();
                load1Chart.addChartCursor(load1ChartCursor);

                // SCROLLBAR

                var load1ChartScrollbar = new AmCharts.ChartScrollbar();
                load1ChartScrollbar.scrollbarHeight = 5;
                load1ChartScrollbar.offset = 15
                load1Chart.addChartScrollbar(load1ChartScrollbar);

                // WRITE
                load1Chart.write("load1Chartdiv");

		// LOAD 2 CHART
                load2Chart = new AmCharts.AmXYChart();

                load2Chart.dataProvider = load2Data;
                load2Chart.startDuration = 1;

                // AXES
                // X
                var xAxis = new AmCharts.ValueAxis();
                xAxis.title = "Time (Minutes)";
                xAxis.position = "bottom";
                xAxis.dashLength = 1;
                xAxis.axisAlpha = 0;
                xAxis.autoGridCount = true;
                load2Chart.addValueAxis(xAxis);

                // Y
                var yAxis = new AmCharts.ValueAxis();
                yAxis.position = "left";
                yAxis.title = "Load (Watts)";
                yAxis.dashLength = 1;
                yAxis.axisAlpha = 0;
                yAxis.autoGridCount = true;
                load2Chart.addValueAxis(yAxis);

                // GRAPHS
                // triangles up
                var graph1 = new AmCharts.AmGraph();
                graph1.lineColor = "#FF6600";
                graph1.balloonText = "x:[[x]] y:[[y]]";
                graph1.xField = "ax";
                graph1.yField = "ay";
                graph1.lineAlpha = 0;
                graph1.bullet = "circle";
                load2Chart.addGraph(graph1);

                // CURSOR
                var load2ChartCursor = new AmCharts.ChartCursor();
                load2Chart.addChartCursor(load2ChartCursor);

                // SCROLLBAR

                var load2ChartScrollbar = new AmCharts.ChartScrollbar();
                load2ChartScrollbar.scrollbarHeight = 5;
                load2ChartScrollbar.offset = 15
                load2Chart.addChartScrollbar(load2ChartScrollbar);

                // WRITE
                load2Chart.write("load2Chartdiv");

		// LOAD COMP CHART

		loadCompChart = new AmCharts.AmSerialChart();
                loadCompChart.dataProvider = loadCompData;

                //chart.categoryField = "date";
                //chart.dataDateFormat = "YYYY-MM-DD";

                // sometimes we need to set margins manually
                // autoMargins should be set to false in order chart to use custom margin values
                loadCompChart.autoMargins = false;
                loadCompChart.marginRight = 0;
                loadCompChart.marginLeft = 0;
                loadCompChart.marginBottom = 0;
                loadCompChart.marginTop = 0;
                loadCompChart.categoryField = "ax";
                loadCompChart.startDuration = 1;

                // AXES
                // category
                var categoryAxis = loadCompChart.categoryAxis;
                //categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
                //categoryAxis.minPeriod = "DD"; // our data is daily, so we set minPeriod to DD
                categoryAxis.inside = true;
                categoryAxis.gridAlpha = 0;
                categoryAxis.tickLength = 0;
                categoryAxis.axisAlpha = 0;

                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.dashLength = 4;
                valueAxis.axisAlpha = 0;
                loadCompChart.addValueAxis(valueAxis);

                // GRAPH
                var graph = new AmCharts.AmGraph();
                graph.type = "line";
                graph.valueField = "ay";
                graph.lineColor = "#FF0000";
                loadCompChart.addGraph(graph);

                var graph1 = new AmCharts.AmGraph();
                graph1.type = "line";
                graph1.valueField = "by";
                graph1.lineColor = "#00FF00";
                loadCompChart.addGraph(graph1);

                var graph2 = new AmCharts.AmGraph();
                graph2.type = "line";
                graph2.valueField = "cy";
                graph2.lineColor = "#0000FF";
                loadCompChart.addGraph(graph2);
                //graph.customBullet = "images/star.png"; // bullet for all data points
                //graph.bulletSize = 14; // bullet image should be a rectangle (width = height)
                //graph.customBulletField = "customBullet"; // this will make the graph to display custom bullet (red star)

                // CURSOR
                var loadCompChartCursor = new AmCharts.ChartCursor();
                loadCompChart.addChartCursor(loadCompChartCursor);

                // WRITE
                loadCompChart.write("loadCompChartdiv");

//                loadCompChart = new AmCharts.AmXYChart();
//
//                loadCompChart.dataProvider = loadCompData;
//                loadCompChart.startDuration = 1;
//
//                // AXES
//                // X
//                var xAxis = new AmCharts.ValueAxis();
//                xAxis.title = "Time (Minutes)";
//                xAxis.position = "bottom";
//                xAxis.dashLength = 1;
//                xAxis.axisAlpha = 0;
//                xAxis.autoGridCount = true;
//                loadCompChart.addValueAxis(xAxis);
//
//                // Y
//                var yAxis = new AmCharts.ValueAxis();
//                yAxis.position = "left";
//                yAxis.title = "Load (Watts)";
//                yAxis.dashLength = 1;
//                yAxis.axisAlpha = 0;
//                yAxis.autoGridCount = true;
//                loadCompChart.addValueAxis(yAxis);
//
//                // GRAPHS
//                // triangles up
//                var graph1 = new AmCharts.AmGraph();
//                graph1.lineColor = "#0000FF";
//                graph1.balloonText = "x:[[x]] y:[[y]]";
//                graph1.xField = "ax";
//                graph1.yField = "ay";
//                graph1.lineAlpha = 0;
//                graph1.bullet = "circle";
//                loadCompChart.addGraph(graph1);
//
//                var graph2 = new AmCharts.AmGraph();
//                graph2.lineColor = "#00FF00";
//                graph2.balloonText = "x:[[x]] y:[[y]]";
//                graph2.xField = "bx";
//                graph2.yField = "by";
//                graph2.lineAlpha = 0;
//                graph2.bullet = "circle";
//                loadCompChart.addGraph(graph2);
//
//		var graph3 = new AmCharts.AmGraph();
//                graph3.lineColor = "#FF0000";
//                graph3.balloonText = "x:[[x]] y:[[y]]";
//                graph3.xField = "cx";
//                graph3.yField = "cy";
//                graph3.lineAlpha = 0;
//                graph3.bullet = "circle";
//                loadCompChart.addGraph(graph3);
//
//                // CURSOR
//                var loadCompChartCursor = new AmCharts.ChartCursor();
//                loadCompChart.addChartCursor(loadCompChartCursor);
//
//                // SCROLLBAR
//
//                var loadCompChartScrollbar = new AmCharts.ChartScrollbar();
//                loadCompChartScrollbar.scrollbarHeight = 5;
//                loadCompChartScrollbar.offset = 15
//                loadCompChart.addChartScrollbar(loadCompChartScrollbar);
//
//                // WRITE
//                loadCompChart.write("loadCompChartdiv");

            });
        </script>
@stop
