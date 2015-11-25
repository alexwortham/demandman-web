@extends('layouts.master')

@section('content')

    @foreach($demands as $demand)
    <h3>{{ $demand->start_time }} -&gt; {{ $demand->end_time }}</h3>
        <ul>
            <li>Watts: {{ $demand->watts }}</li>
            <li>Watt Hours: {{ $demand->watt_hours }}</li>
            <li>Demand Charge: {{ $demand->demand_charge }}</li>
            <li>Usage Charge: {{ $demand->usage_charge }}</li>
        </ul>
    @endforeach
@stop

@section('body_post')
    {!! Html::script('js/amcharts/amcharts.js') !!}
    {!! Html::script('js/amcharts/xy.js') !!}
    {!! Html::script('js/amcharts/serial.js') !!}
    <script>
        var demandChart;

        var demandData = [
                @foreach($demands as $key => $demand)
                        {
                "ax": {{ $key }},
                "ay": {{ $demand->watts }},
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
        });
    </script>
@stop
