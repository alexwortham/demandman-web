@extends('layouts.master')

@section('content')

    <h1>{{$startTime}}</h1>
    <h2>{{$rel}}</h2>
    @foreach($demands as $demand)
    <h3>{{ $demand->start_time }} -&gt; {{ $demand->end_time }}</h3>
        <ul>
            <li>Average Watts: {{ $demand->watts }}</li>
            <li>Watt Hours: {{ $demand->watt_hours }}</li>
            <li>Demand Charge: {{ $demand->demand_charge }}</li>
            <li>Usage Charge: {{ $demand->usage_charge }}</li>
        </ul>
    @endforeach
    @foreach($curves as $key => $curve)
    <div id="chart{{$key}}div" style="width: 100%; height: 400px;"></div>
    @endforeach
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
        @foreach($curves as $key => $curve)
            var chart{{$key}}Data = [
            @foreach($curve->load_data as $point)
                    {
                        "date": new Date("{{ $point->time }}"),
                        "load": {{ $point->load }},
                    },
            @endforeach
                ];
        @endforeach

        AmCharts.ready(function () {
            @foreach($curves as $key => $curve)
            // SERIAL CHART
                chart{{$key}} = new AmCharts.AmSerialChart();

                chart{{$key}}.dataProvider = chart{{$key}}Data;
                chart{{$key}}.categoryField = "date";

                // data updated event will be fired when chart is first displayed,
                // also when data will be updated. We'll use it to set some
                // initial zoom
                chart{{$key}}.addListener("dataUpdated", function() {
                    //foo
                });

                // AXES
                // Category
                var categoryAxis = chart{{$key}}.categoryAxis;
                categoryAxis.parseDates = true; // in order char to understand dates, we should set parseDates to true
                categoryAxis.minPeriod = "ss"; // as we have data with minute interval, we have to set "mm" here.
                categoryAxis.gridAlpha = 0.07;
                categoryAxis.axisColor = "#DADADA";

                // Value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.gridAlpha = 0.07;
                valueAxis.title = "Load in Watts";
                chart{{$key}}.addValueAxis(valueAxis);

                // GRAPH
                var graph = new AmCharts.AmGraph();
                graph.type = "line"; // try to change it to "column"
                graph.title = "red line";
                graph.valueField = "load";
                graph.lineAlpha = 1;
                graph.lineColor = "#d1cf2a";
                graph.fillAlphas = 0.3; // setting fillAlphas to > 0 value makes it area graph
                chart{{$key}}.addGraph(graph);

                // CURSOR
                var chartCursor = new AmCharts.ChartCursor();
                chartCursor.cursorPosition = "mouse";
                chartCursor.categoryBalloonDateFormat = "JJ:NN, DD MMMM";
                chart{{$key}}.addChartCursor(chartCursor);

                // SCROLLBAR
                var chartScrollbar = new AmCharts.ChartScrollbar();

                chart{{$key}}.addChartScrollbar(chartScrollbar);

                // WRITE
                chart{{$key}}.write("chart{{$key}}div");

            @endforeach







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
