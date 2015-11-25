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
    <div id="chartdiv" style="width: 100%; height: 400px;"></div>
    <div id="demandChartdiv" style="width: 100%; height: 400px;"></div>
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
                "ax": "{{ $key }}",
                "ay": "{{ $demand->watts }}",
            },
            @endforeach
                ];

        var chartData = [
		@foreach($curve->load_data as $point)
                {
                    "date": new Date("{{ $point->time }}"),
                    "load": {{ $point->load }},
                },
		@endforeach
            ];

        AmCharts.ready(function () {
            // SERIAL CHART
                chart = new AmCharts.AmSerialChart();

                chart.dataProvider = chartData;
                chart.categoryField = "date";

                // data updated event will be fired when chart is first displayed,
                // also when data will be updated. We'll use it to set some
                // initial zoom
                chart.addListener("dataUpdated", function() {
                    //foo
                });

                // AXES
                // Category
                var categoryAxis = chart.categoryAxis;
                categoryAxis.parseDates = true; // in order char to understand dates, we should set parseDates to true
                categoryAxis.minPeriod = "ss"; // as we have data with minute interval, we have to set "mm" here.
                categoryAxis.gridAlpha = 0.07;
                categoryAxis.axisColor = "#DADADA";

                // Value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.gridAlpha = 0.07;
                valueAxis.title = "Load in Watts";
                chart.addValueAxis(valueAxis);

                // GRAPH
                var graph = new AmCharts.AmGraph();
                graph.type = "line"; // try to change it to "column"
                graph.title = "red line";
                graph.valueField = "load";
                graph.lineAlpha = 1;
                graph.lineColor = "#d1cf2a";
                graph.fillAlphas = 0.3; // setting fillAlphas to > 0 value makes it area graph
                chart.addGraph(graph);

                // CURSOR
                var chartCursor = new AmCharts.ChartCursor();
                chartCursor.cursorPosition = "mouse";
                chartCursor.categoryBalloonDateFormat = "JJ:NN, DD MMMM";
                chart.addChartCursor(chartCursor);

                // SCROLLBAR
                var chartScrollbar = new AmCharts.ChartScrollbar();

                chart.addChartScrollbar(chartScrollbar);

                // WRITE
                chart.write("chartdiv")








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
