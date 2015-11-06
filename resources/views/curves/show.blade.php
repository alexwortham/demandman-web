@extends('layouts.master')

@section('content')
<div class="page-header">
	<h1>{{ $curve->name }}</h1>
</div>
<div id="chart1div" style="width: 100%; height: 400px;"></div>
<a href="{{ action('LoadCurveController@edit', $curve->id) }}" class="btn btn-lg btn-primary">Edit</a>
@stop

@section('body_post')
{!! Html::script('js/amchart1s/amchart1s.js') !!}
{!! Html::script('js/amchart1s/xy.js') !!}
<script>
            var chart1;

            var chart1Data = [
		@foreach($curve->parse_data() as $point)
                {
                    "ax": {{ $point[0] }},
                    "ay": {{ $point[1] }},
                },
		@endforeach
            ];

            AmCharts.ready(function () {
                // XY CHART
                chart1 = new AmCharts.AmXYChart();

                chart1.dataProvider = chart1Data;
                chart1.startDuration = 1;

                // AXES
                // X
                var xAxis = new AmCharts.ValueAxis();
                xAxis.title = "Time (Minutes)";
                xAxis.position = "bottom";
                xAxis.dashLength = 1;
                xAxis.axisAlpha = 0;
                xAxis.autoGridCount = true;
                chart1.addValueAxis(xAxis);

                // Y
                var yAxis = new AmCharts.ValueAxis();
                yAxis.position = "left";
                yAxis.title = "Demand (Watts)";
                yAxis.dashLength = 1;
                yAxis.axisAlpha = 0;
                yAxis.autoGridCount = true;
                chart1.addValueAxis(yAxis);

                // GRAPHS
                // triangles up
                var graph1 = new AmCharts.AmGraph();
                graph1.lineColor = "#FF6600";
                graph1.balloonText = "x:[[x]] y:[[y]]";
                graph1.xField = "ax";
                graph1.yField = "ay";
                graph1.lineAlpha = 0;
                graph1.bullet = "circle";
                chart1.addGraph(graph1);

                // CURSOR
                var chart1Cursor = new AmCharts.ChartCursor();
                chart1.addChartCursor(chart1Cursor);

                // SCROLLBAR

                var chart1Scrollbar = new AmCharts.ChartScrollbar();
                chart1Scrollbar.scrollbarHeight = 5;
                chart1Scrollbar.offset = 15
                chart1.addChartScrollbar(chart1Scrollbar);

                // WRITE
                chart1.write("chart1div");
            });
        </script>
@stop
