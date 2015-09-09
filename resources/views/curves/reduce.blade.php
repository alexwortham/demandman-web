@extends('layouts.master')

@section('content')
<div class="page-header">
	<h1>{{ $curve->name }}</h1>
</div>
<h2>Load Chart</h2>
<div id="loadChartdiv" style="width: 100%; height: 400px;"></div>
<h2>Demand Chart</h2>
<div id="demandChartdiv" style="width: 100%; height: 400px;"></div>
@stop

@section('body_post')
{!! Html::script('js/amcharts/amcharts.js') !!}
{!! Html::script('js/amcharts/xy.js') !!}
{!! Html::script('js/amcharts/serial.js') !!}
<script>
            var reducedChart;

            var demandData = [
		@foreach($demand as $key => $val)
                {
                    "ax": {{ $key }},
                    "ay": {{ $val }},
                },
		@endforeach
            ];

	    var loadChart;

            var loadData = [
		@foreach($curve->parse_data() as $point)
                {
                    "ax": {{ $point[0] }},
                    "ay": {{ $point[1] }},
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

                // XY CHART
                loadChart = new AmCharts.AmXYChart();

                loadChart.dataProvider = loadData;
                loadChart.startDuration = 1;

                // AXES
                // X
                var xAxis = new AmCharts.ValueAxis();
                xAxis.title = "Time (Minutes)";
                xAxis.position = "bottom";
                xAxis.dashLength = 1;
                xAxis.axisAlpha = 0;
                xAxis.autoGridCount = true;
                loadChart.addValueAxis(xAxis);

                // Y
                var yAxis = new AmCharts.ValueAxis();
                yAxis.position = "left";
                yAxis.title = "Load (Watts)";
                yAxis.dashLength = 1;
                yAxis.axisAlpha = 0;
                yAxis.autoGridCount = true;
                loadChart.addValueAxis(yAxis);

                // GRAPHS
                // triangles up
                var graph1 = new AmCharts.AmGraph();
                graph1.lineColor = "#FF6600";
                graph1.balloonText = "x:[[x]] y:[[y]]";
                graph1.xField = "ax";
                graph1.yField = "ay";
                graph1.lineAlpha = 0;
                graph1.bullet = "circle";
                loadChart.addGraph(graph1);

                // CURSOR
                var loadChartCursor = new AmCharts.ChartCursor();
                loadChart.addChartCursor(loadChartCursor);

                // SCROLLBAR

                var loadChartScrollbar = new AmCharts.ChartScrollbar();
                loadChartScrollbar.scrollbarHeight = 5;
                loadChartScrollbar.offset = 15
                loadChart.addChartScrollbar(loadChartScrollbar);

                // WRITE
                loadChart.write("loadChartdiv");
            });
        </script>
@stop
