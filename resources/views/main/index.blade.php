@extends('layouts.master')

@section('content')
<h2>Usage History</h2>
<div class="row">
    <div class="col-md-4"></div>
	<div class="col-md-4">
		<div id="gaugediv" style="width: 100%; height: 300px;"></div>
	</div>
    <div class="col-md-4"></div>
</div>
<div class="row">
    <div class="col-md-12">
        <h1>Energy Consumption</h1>
    </div>
</div>
<div class="row">
    <div class="col-md-4" style="font-size: 20px;">
        <ul id="appliance-usage-list" class="list-unstyled">
            @foreach($appliances as $appliance)
            <li>
                <span class="glyphicon glyphicon-stop"></span>
                <span id="appliance{{$appliance->id}}-legend">{{$appliance->name}}: {{$usages[$appliance->id]}} kWh</span>
            </li>
            @endforeach
        </ul>
    </div>
    <div class="col-md-8">
        <div id="piediv" style="width: 100%; height: 400px;"></div>
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
                        "color": "#84b761",
                        "endValue": 2,
                        "startValue": 0
                    }, {
                        "color": "#fdd400",
                        "endValue": 3,
                        "startValue": 2
                    }, {
                        "color": "#cc4748",
                        "endValue": 4,
                        "innerRadius": "95%",
                        "startValue": 3
                    }, {
                        "color": "#990000",
                        "endValue": 8,
                        "innerRadius": "92.5%",
                        "startValue": 4
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
                                gaugeChart.axes[0].setBottomText(value + " kWh");
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

            $('ul#appliance-usage-list li span.glyphicon').each(function(i, el){
                    $(this).css('color', pieChart.colors[i]);
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
