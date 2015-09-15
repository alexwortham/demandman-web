@extends('layouts.master')

@section('content')
<div class="page-header">
	<h1>{{ $curve->name }}</h1>
</div>
<div id="chartdiv" style="width: 100%; height: 400px;"></div>
<a href="{{ action('LoadCurveController@edit', $curve->id) }}" class="btn btn-lg btn-primary">Edit</a>
@stop

@section('body_post')
{!! Html::script('js/amcharts/amcharts.js') !!}
{!! Html::script('js/amcharts/xy.js') !!}
<script>
            var chart;

            var chartData = [
		@foreach($curve->parse_data() as $point)
                {
                    "ax": {{ $point[0] }},
                    "ay": {{ $point[1] }},
                },
		@endforeach
            ];

            AmCharts.ready(function () {
                // XY CHART
                chart = new AmCharts.AmXYChart();

                chart.dataProvider = chartData;
                chart.startDuration = 1;

                // AXES
                // X
                var xAxis = new AmCharts.ValueAxis();
                xAxis.title = "Time (Minutes)";
                xAxis.position = "bottom";
                xAxis.dashLength = 1;
                xAxis.axisAlpha = 0;
                xAxis.autoGridCount = true;
                chart.addValueAxis(xAxis);

                // Y
                var yAxis = new AmCharts.ValueAxis();
                yAxis.position = "left";
                yAxis.title = "Demand (Watts)";
                yAxis.dashLength = 1;
                yAxis.axisAlpha = 0;
                yAxis.autoGridCount = true;
                chart.addValueAxis(yAxis);

                // GRAPHS
                // triangles up
                var graph1 = new AmCharts.AmGraph();
                graph1.lineColor = "#FF6600";
                graph1.balloonText = "x:[[x]] y:[[y]]";
                graph1.xField = "ax";
                graph1.yField = "ay";
                graph1.lineAlpha = 0;
                graph1.bullet = "circle";
                chart.addGraph(graph1);

                // CURSOR
                var chartCursor = new AmCharts.ChartCursor();
                chart.addChartCursor(chartCursor);

                // SCROLLBAR

                var chartScrollbar = new AmCharts.ChartScrollbar();
                chartScrollbar.scrollbarHeight = 5;
                chartScrollbar.offset = 15
                chart.addChartScrollbar(chartScrollbar);

                // WRITE
                chart.write("chartdiv");
            });
        </script>
@stop
