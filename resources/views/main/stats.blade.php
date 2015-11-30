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