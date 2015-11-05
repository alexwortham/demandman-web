@extends('layouts.master')

@section('content')
<div class="page-header">
	<h1>{{ $run->appliance->name }}</h1>
</div>
<div id="chartdiv" style="width: 100%; height: 400px;"></div>
<a href="{{ route('run.show', $run->id) }}" class="btn btn-lg btn-primary">Edit</a>
@stop

@section('body_post')
{!! Html::script('js/amcharts/amcharts.js') !!}
{!! Html::script('js/amcharts/serial.js') !!}
<script>
            var chart;

            var chartData = [
		@foreach($run->loadCurve->loadData as $point)
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
                categoryAxis.minPeriod = "mm"; // as we have data with minute interval, we have to set "mm" here.
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
            });

            @if ($live)
            (function($){
                var refreshTo;
                var latest;
                var chartTimeout = 0;

                function padZero(number) {
                    if (number < 10) {
                        return '0' + number;
                    } else {
                        return '' + number;
                    }
                }

                function dateString(date) {
                    return '' + date.getFullYear() + '-'
                            + (padZero((date.getMonth() + 1) % 12)) + '-'
                        + padZero(date.getDate()) + ' ' + padZero(date.getHours()) + ':'
                        + padZero(date.getMinutes()) + ':' + padZero(date.getSeconds());
                }

                function refreshChartData(since) {
                    $.ajax(window.location.href + '/' + dateString(since), {
                        success: function (data, status, jqXHR) {
                            if (data.length > 0) {
                                for (var i = 0, len = data.length; i < len; i++) {
                                    chartData.push({date: new Date(data[i].time),
                                        load: data[i].load});
                                    chart.validateData();
                                }
                                latest = new Date(data[len - 1].time);
                                chartTimeout = 0;
                            } else {
                                chartTimeout++;
                            }
                            if (chartTimeout < 60) {
                                refreshTo = setTimeout(function () {
                                    refreshChartData(latest);
                                }, 1000);
                            }
                        }
                    });
                }

                $(function() {
                    refreshChartData(new Date('{{ $since }}'));
                });
            })(jQuery);
            @endif
</script>
@stop
