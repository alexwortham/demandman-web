@extends('layouts.master')

@section('content')
    <style>
        .stat-panel {
            border-left: 1px solid rgba(0,0,0,0.1);
            display: inline-block;
            padding-left: 1em;
            margin-left: 1em;
        }
        .stat-panel h4 {
            margin: 0;
        }
        .stat-panel h6 {
            margin: 0;
            color: #ababab;
        }
        .stat-panel p {
            font-size: 46pt;
            margin: 0;
        }
        .bill-amount {
            position: relative;
            top: 50%;
            transform: translateY(-50%);
            text-align: center;
        }
        .stat-panel h1.bill {
            font-size: 86px;
            color: #57A259;
            margin: 0;
        }
        .demand-legend {
            text-align: center;
            margin: 0;
        }
        .title-row {
            border-bottom: 2px solid #ddd;
        }
        .title-row h2 {
            margin: 0;
            text-align: center;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <h2>Billing Breakdown</h2>
        </div>
    </div>
    <div class="panel panel-default">
            <div class="panel-body stats">
                <div class="row">
                    <div class="stat-panel">
                        <h4>Amount Due</h4>
                        <h1 class="bill">${{$totalBill}}</h1>
                    </div>
                @foreach($stats as $stat)
                <div class="stat-panel">
                    <h4>{{$stat['title']}}</h4>
                    <h6>{{$stat['subtitle']}}&nbsp;</h6>
                    <p>{{$stat['value']}}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="row">
                <div class="col-md-4">
                </div>
    </div>
<div class="row">
    <div class="col-md-12">
        <h2>Demand Management</h2>
    </div>
</div>
<div class="panel panel-default smoothness">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4" style="height: 300px; margin-bottom: 50px">
                <div id="slider-vertical" style="height:270px; display: inline-block; margin-bottom: 10px;"></div>
                <div id="threshdiv" style="width: 90%; height: 100%; display: inline-block;"></div>
                <h3 class="demand-legend">Demand Threshold</h3>
            </div>
            <div class="col-md-4">
                <div id="gaugediv" style="width: 100%; height: 300px;"></div>
                <h3 class="demand-legend">Current Demand</h3>
            </div>
            <div class="col-md-4" style="margin-bottom: 50px;">
                <div id="peakdiv" style="height: 300px;"></div>
                <h3 class="demand-legend">Peak Demand</h3>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <h2>Energy Consumption</h2>
    </div>
</div>
<div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
    <div class="col-md-4" style="font-size: 18px; padding-bottom: 50px;">
        <table id="appliance-usage-list" class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Appliance</th>
                    <th>Usage</th>
                    <th>Cost</th>
                </tr>
            </thead>
            <tbody>
            @foreach($appliances as $appliance)
                <tr>
                    <td><span class="glyphicon glyphicon-stop"></span></td>
                    <td><span id="appliance{{$appliance->id}}-legend">{{$appliance->name}}</span></td>
                    <td>{{$usages[$appliance->id]}} kWh</td>
                    <td>{{$costs[$appliance->id]}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-md-8">
        <div id="piediv" style="width: 100%; height: 400px; margin-top: -90px"></div>
    </div>
            </div>
    </div>
</div>

<h2>Control Panel</h2>
<div class="row">
@foreach ($appliances as $appliance)
	<div class="col-md-6">
	@include('main/applianceControl')
	</div>
@endforeach
</div>
@stop

@section('body_post')
	{!! Html::script('js/amcharts/amcharts.js') !!}
	{!! Html::script('js/amcharts/serial.js') !!}
	{!! Html::script('js/amcharts/xy.js') !!}
	{!! Html::script('js/amcharts/gauge.js') !!}
	{!! Html::script('js/amcharts/pie.js') !!}
    {!! Html::script('js/amcharts/themes/light.js') !!}
	<script>



		(function($){
			$(function() {
				        var pieChart;

                var chartOptions = {
                    "levels" : [{
                        "gaugeStart": 0,
                        "gaugeEnd": 4,
                        "threshStart": 0,
                        "threshEnd": 4000,
                        "gaugeColor": "#84b761",
                        "threshColor": "#84b761"
                    }, {
                        "gaugeStart": 4,
                        "gaugeEnd": 6,
                        "threshStart": 4000,
                        "threshEnd": 6000,
                        "gaugeColor": "#fdd400",
                        "threshColor": "#fdd400"
                    }, {
                        "gaugeStart": 6,
                        "gaugeEnd": 8,
                        "threshStart": 6000,
                        "threshEnd": 8000,
                        "gaugeColor": "#cc4748",
                        "threshColor": "#cc4748"
                    }, {
                        "gaugeStart": 8,
                        "gaugeEnd": 10,
                        "threshStart": 8000,
                        "threshEnd": 10000,
                        "gaugeColor": "#990000",
                        "threshColor": "#990000"
                    }]
                };

        AmCharts.ready(function () {
            var gaugeChart = AmCharts.makeChart("gaugediv", {
                "type": "gauge",
                "theme": "light",
                "fontSize": 20,
                "axes": [{
                    "axisThickness": 2,
                    "axisAlpha": 0.4,
                    "tickAlpha": 0.4,
                    "valueInterval": 1,
                    "bands": [{
                        "color": chartOptions.levels[0].gaugeColor,
                        "endValue": chartOptions.levels[0].gaugeEnd,
                        "startValue": chartOptions.levels[0].gaugeStart
                    }, {
                        "color": chartOptions.levels[1].gaugeColor,
                        "endValue": chartOptions.levels[1].gaugeEnd,
                        "startValue": chartOptions.levels[1].gaugeStart
                    }, {
                        "color": chartOptions.levels[2].gaugeColor,
                        "endValue": chartOptions.levels[2].gaugeEnd,
                        "startValue": chartOptions.levels[2].gaugeStart,
                        "innerRadius": "95%"
                    }, {
                        "color": chartOptions.levels[3].gaugeColor,
                        "endValue": chartOptions.levels[3].gaugeEnd,
                        "startValue": chartOptions.levels[3].gaugeStart,
                        "innerRadius": "92.5%"
                    }],
                    "bottomText": "0 kWh",
                    "bottomTextYOffset": -20,
                    "endValue": 12
                }],
                "arrows": [ {
                    "radius": "70%",
                    "nailRadius": "25",
                    "nailAlpha": 1,
                    "innerRadius": 0,
                    "startWidth": 22,
                    "color": "#cc4748"
                }, {
                    "radius": "70%",
                    "nailRadius": "25",
                    "nailAlpha": 1,
                    "innerRadius": 0,
                    "startWidth": 22,
                    "color": "#000000"
                }],
                "export": {
                    "enabled": true
                }
            });

            setInterval(randomValue, 2000);

            // set random value
            function randomValue() {
                var value = Math.round({{$demand->watts / 1000}} * 100) / 100;
                var randvalue = Math.round( Math.random() * 12 );
                if (gaugeChart) {
                    if (gaugeChart.arrows) {
                        if (gaugeChart.arrows[0]) {
                            if (gaugeChart.arrows[0].setValue) {
                                gaugeChart.arrows[0].setValue(value);
                                gaugeChart.axes[0].setBottomText(randvalue + " kW");
                            }
                        }
                        if (gaugeChart.arrows[1]) {
                            if (gaugeChart.arrows[1].setValue) {
                                gaugeChart.arrows[1].setValue(randvalue);
                            }
                        }
                    }
                }
            }





            pieChart = AmCharts.makeChart("piediv", {
                "type": "pie",
                "startDuration": 0,
                "theme": "light",
                "addClassNames": true,
//                "legend":{
//                    "position":"right",
//                    "marginRight":100,
//                    "fontSize": 20,
//                    "autoMargins":false
//                },
                "innerRadius": "30%",
                "defs": {
                    "filter": [{
                        "id": "shadow",
                        "width": "200%",
                        "height": "200%",
                        "feOffset": {
                            "result": "offOut",
                            "in": "SourceAlpha",
                            "dx": 0,
                            "dy": 0
                        },
                        "feGaussianBlur": {
                            "result": "blurOut",
                            "in": "offOut",
                            "stdDeviation": 5
                        },
                        "feBlend": {
                            "in": "SourceGraphic",
                            "in2": "blurOut",
                            "mode": "normal"
                        }
                    }]
                },
                "dataProvider": [
                    @foreach($appliances as $appliance)
                    {
                        "appliance": "{{$appliance->name}}",
                        "kWh": (Math.round("{{$usages[$appliance->id]}}" * 100) / 100)
                    },
                    @endforeach
                ],
                "valueField": "kWh",
                "titleField": "appliance",
                "export": {
                    "enabled": true
                },
                "fontSize": "18pt",
            });

            //pieChart.addTitle("Energy Consumption", 28, "#000000", 1, true);
            pieChart.addListener("init", handleInit);

            pieChart.addListener("rollOverSlice", function(e) {
                handleRollOver(e);
            });

            function handleInit(){
                pieChart.legend.addListener("rollOverItem", handleRollOver);
            }

            function handleRollOver(e){
                var wedge = e.dataItem.wedge.node;
                wedge.parentNode.appendChild(wedge);
            }





            var initialTreshColor;
            var initialThreshValue = "{{ $threshold }}";

            var threshChartData = [ {
                "category": "Demand Threshold",
                "value1": initialThreshValue,
                "value2": 10000,
                "value": 60
            } ];

            $.each(chartOptions.levels, function(idx, level) {
                if (initialThreshValue >= level.threshStart &&
                        initialThreshValue < level.threshEnd) {
                    initialTreshColor = level.threshColor;
                }
            });

            var threshChart = AmCharts.makeChart( "threshdiv", {
                "theme": "light",
                "type": "serial",
//                "autoMargins": false,
//                "marginBottom": 100,
//                "marginLeft": 350,
//                "marginRight": 300,
                "dataProvider": threshChartData,
                "fontSize": 18,

                "valueAxes": [ {
                    "id": "v1",
                    "title": "Watts",
                    "stackType": "3d",
                    "gridAlpha": 0,
                    "maximum": 10000,
                    "minimum": 2000,
                    "usePrefixes": true,
                    "unit": "W"
                }, {
                    "id": "v2",
                    "title": "Cost",
                    "position": "right",
                    "maximum": 10.00,
                    "minimum": 2.00,
                    "precision": 2} ],
                "graphs": [ {
                    "type": "column",
                    "topRadius": 1,
                    "columnWidth": 1,
                    "showOnAxis": true,
                    "lineThickness": 0,
                    "lineAlpha": 0.5,
                    "lineColor": "#FFFFFF",
                    "fillColors": initialTreshColor,
                    "fillAlphas": 0.8,
                    "valueField": "value1",
                    "valueAxis": "v1"
                }, {
                    "type": "column",
                    "topRadius": 1,
                    "columnWidth": 1,
                    "showOnAxis": true,
                    "lineThickness": 2,
                    "lineAlpha": 0.5,
                    "lineColor": "#cdcdcd",
                    "fillColors": "#cdcdcd",
                    "fillAlphas": 0.5,
                    "valueField": "value2",
                    "valueAxis": "v1"
                }, {
                    "type": "column",
                    "topRadius": 1,
                    "columnWidth": 1,
                    "showOnAxis": true,
                    "lineThickness": 2,
                    "lineAlpha": 0,
                    "lineColor": "#cdcdcd",
                    "fillColors": "#cdcd00",
                    "fillAlphas": 0,
                    "valueField": "value",
                    "valueAxis": "v2"
                } ],

                "categoryField": "category",
                "categoryAxis": {
                    "axisAlpha": 0,
                    "labelOffset": 0,
                    "gridAlpha": 0,
                    "labelsEnabled": false
                },
                "export": {
                    "enabled": true
                }
            } );

            threshChart.hideGraphsBalloon(threshChart.graphs[1]);



            var initialPeakColor;
            var initialPeakValue = "{{ $demand->watts }}";

            var peakChartData = [ {
                "category": "Demand Peak",
                "peak": initialPeakValue,
                "charge": (initialPeakValue / 1000)
            } ];

            $.each(chartOptions.levels, function(idx, level) {
                if (initialPeakValue >= level.threshStart &&
                        initialPeakValue < level.threshEnd) {
                    initialPeakColor = level.threshColor;
                }
            });

            var peakChart = AmCharts.makeChart( "peakdiv", {
                "theme": "light",
                "type": "serial",
//                "autoMargins": false,
//                "marginBottom": 100,
//                "marginLeft": 350,
//                "marginRight": 300,
                "dataProvider": peakChartData,
                "fontSize": 18,

                "valueAxes": [ {
                    "id": "v1",
                    "title": "Watts",
                    "stackType": "3d",
                    "gridAlpha": 0,
                    "minimum": 0,
                    "usePrefixes": true,
                    "unit": "W"
                }, {
                    "id": "v2",
                    "title": "Cost",
                    "position": "right",
                    //"maximum": 10.00,
                    "minimum": 0.00,
                    "precision": 2,
                    "synchronizeWith": "v1",
                    "synchronizeMultiplier": 0.01} ],
                "graphs": [ {
                    "type": "column",
                    "topRadius": 1,
                    "columnWidth": 1,
                    "showOnAxis": true,
                    "lineThickness": 0,
                    "lineAlpha": 0.5,
                    "lineColor": "#FFFFFF",
                    "fillColors": initialPeakColor,
                    "fillAlphas": 0.8,
                    "valueField": "peak",
                    "valueAxis": "v1"
                },  {
                    "type": "column",
                    "topRadius": 1,
                    "columnWidth": 1,
                    "showOnAxis": true,
                    "lineThickness": 2,
                    "lineAlpha": 0,
                    "lineColor": "#cdcdcd",
                    "fillColors": "#cdcd00",
                    "fillAlphas": 0,
                    "valueField": "charge",
                    "valueAxis": "v2"
                } ],

                "categoryField": "category",
                "categoryAxis": {
                    "axisAlpha": 0,
                    "labelOffset": 0,
                    "gridAlpha": 0,
                    "labelsEnabled": false
                },
                "export": {
                    "enabled": true
                }
            } );

//            peakChart.validateData();


            $('td span.glyphicon').each(function(i, el){
                    $(this).css('color', pieChart.colors[i]);
                });

            $( "#slider-vertical" ).slider({
                    orientation: "vertical",
                    range: "min",
                    min: 2000,
                    max: 10000,
                    value: ((threshChartData[0]).value1),
                    slide: function( event, ui ) {
                        (threshChartData[0]).value1 = ui.value;
                        $.each(chartOptions.levels, function(idx, level) {
                            if (ui.value >= level.threshStart &&
                                    ui.value < level.threshEnd) {
                                threshChart.graphs[0].fillColors = level.threshColor;
                            }
                        });
                        threshChart.validateData();
                    },
                    stop: function( event, ui ) {
                        //ajax set threshold.
                    }
                });

        });

				$('a.btn-success').each(function(i,el){
					$(this).click(function(event) {
						event.preventDefault();
						$.get($(this).attr('href'));
					});
				});



			});
		})(jQuery);

	</script>
@stop
