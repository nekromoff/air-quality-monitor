<!doctype html>
<html lang="sk">
  <head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-5937S6Z');</script>
    <!-- End Google Tag Manager -->
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Monitoring kvality vzduchu, Bratislava, Slovensko</title>
  </head>
  <body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5937S6Z"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <div class="container">
        <div class="row">
            <h1>Monitoring kvality vzduchu v Bratislave</h1>
            <p>Senzory sú rozmiestnené v rôznych lokalitách v meste a poskytujú každých 5 minút meranie prachových častíc PM 2,5 a PM 10, teploty, vlhkosti a tlaku vzduchu.</p>
            <div class="alert alert-warning" role="alert">❗ Znečistenie prachovými časticami dlhodobo negatívne vplýva na zdravie obyvateľov a spôsobuje dýchacie problémy a alergie. <strong>Hlavným znečisťovateľom je doprava, konkrétne osobné a nákladné autá</strong>. Najviac ohrozené sú vaše deti, starí rodičia, alergici atď. ❗</div>
        </div>
        <div class="row">
            <div class="col-sm">
                <div class="card @if ($averages['yearly_2_5']>10) bg-danger text-white @else bg-light @endif">
                  <div class="card-body">
                    <h5 class="card-title">Ročný priemer PM 2,5</h5>
                    <h6 class="card-subtitle mb-2">Nesmie prekročiť limit 10 μg/m3</h6>
                    <p class="card-text display-1">{{number_format($averages['yearly_2_5'],2,',',' ')}}</p>
                  </div>
                </div>
            </div>
            <div class="col-sm">
                <div class="card @if ($averages['yearly_10']>20) bg-danger text-white @else bg-light @endif">
                  <div class="card-body">
                    <h5 class="card-title">Ročný priemer PM 10</h5>
                    <h6 class="card-subtitle mb-2">Nesmie prekročiť limit 20 μg/m3</h6>
                    <p class="card-text display-1">{{number_format($averages['yearly_10'],2,',',' ')}}</p>
                  </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm">
                <div class="card @if ($averages['24h_2_5']>25) bg-danger text-white @else bg-light @endif">
                  <div class="card-body">
                    <h5 class="card-title">24 hodinový priemer PM 2,5</h5>
                    <h6 class="card-subtitle mb-2">Nesmie prekročiť limit 25 μg/m3</h6>
                    <p class="card-text display-1">{{number_format($averages['24h_2_5'],2,',',' ')}}</p>
                  </div>
                </div>
            </div>
            <div class="col-sm">
                <div class="card @if ($averages['24h_10']>50) bg-danger text-white @else bg-light @endif">
                  <div class="card-body">
                    <h5 class="card-title">24 hodinový priemer PM 10</h5>
                    <h6 class="card-subtitle mb-2">Nesmie prekročiť limit 50 μg/m3</h6>
                    <p class="card-text display-1">{{number_format($averages['24h_10'],2,',',' ')}}</p>
                  </div>
                </div>
            </div>
        </div>
        <div class="row my-3">
            <p>Podľa štandardov Svetovej zdravotníckej organizácie (WHO) nesmie:</p>
            <ul>
                <li><strong>PM 2,5</strong> prekročiť ročný priemer 10 μg/m3 alebo 24 hodinový priemer 25 μg/m3</li>
                <li><strong>PM 10</strong> prekročiť ročný priemer 20 μg/m3 alebo 24 hodinový priemer 50 μg/m3</li>
            </ul>
        </div>
        <div class="row">
            <div id="chart-pm2_5" class="chart"></div>
            <div id="chart-pm10" class="chart"></div>
        </div>
        <div class="row">
            <h2>Detailné údaje za posledných 14 dní</h2>
        </div>
        <div class="row">
            @foreach ($averages['daily'] as $item)
                <div class="col-sm-3">
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{$item->day}}</h5>
                        <h6 class="card-subtitle mb-2 text-info">{{$sensors[$item->sensor_id]->location}}</h6>
                        <h6 class="card-subtitle mb-2 text-muted">Senzor {{$sensors[$item->sensor_id]->number}}</h6>
                        <p class="card-text">
                            Denné priemerné hodnoty:
                            <ul>
                                @if ($item->pm10!=0)
                                    <li>PM 10: <span class="badge @if ($item->pm10>50) badge-danger text-white @else badge-secondary @endif">{{number_format($item->pm10,2,',',' ')}} μg/m3</span></li>
                                @endif
                                @if ($item->pm2_5!=0)
                                    <li>PM 2,5: <span class="badge @if ($item->pm2_5>25) badge-danger text-white @else badge-secondary @endif">{{number_format($item->pm2_5,2,',',' ')}} μg/m3</span></li>
                                @endif
                                @if ($item->temperature!=0)
                                    <li>Teplota: <span class="badge badge-secondary">{{number_format($item->temperature,2,',',' ')}} °C</span></li>
                                @endif
                                @if ($item->humidity!=0)
                                    <li>Vlhkosť: <span class="badge badge-secondary">{{number_format($item->humidity,2,',',' ')}}%</span></li>
                                @endif
                                @if ($item->pressure!=0)
                                    <li>Tlak: <span class="badge badge-secondary">{{$item->pressure}}</span></li>
                                @endif
                            </ul>
                        </p>
                      </div>
                    </div>
                </div>
            @endforeach
        </div>
        <footer class="row mt-5 bg-light">
            <div class="col-sm">
                Službu prevádzkuje: <a href="https://cyklokoalicia.sk"><img src="https://cyklokoalicia.sk/wp-content/themes/cyklokoalicia-2/img/cyklokoalicia_logo.svg" alt="Logo Cyklokoalícia"></a><br>Podporené z grantu Clean Air Initiative.
            </div>
        </footer>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
    google.charts.load('current', {'packages':['line', 'corechart']});
      google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var chart_div_pm2_5 = document.getElementById('chart-pm2_5');
      var chart_div_pm10 = document.getElementById('chart-pm10');

      var data_pm2_5 = new google.visualization.DataTable();
      data_pm2_5.addColumn('string', 'Deň');
      data_pm2_5.addColumn('number', "PM 2,5");

      data_pm2_5.addRows([
        @foreach ($averages['chart'] as $item)
            ['{{$item->day}}',  {{round($item->pm2_5,2)}}]@if (!$loop->last),@endif
        @endforeach
      ]);

      var data_pm10 = new google.visualization.DataTable();
      data_pm10.addColumn('string', 'Deň');
      data_pm10.addColumn('number', "PM 10");

      data_pm10.addRows([
        @foreach ($averages['chart'] as $item)
            ['{{$item->day}}',  {{round($item->pm10,2)}}]@if (!$loop->last),@endif
        @endforeach
      ]);

      var options_pm2_5 = {
        chart: {
          title: 'Prachové častice PM 2,5 (znečistenie vzduchu po dňoch)'
        },
        width: 1200,
        height: 300,
        series: {
          // Gives each series an axis name that matches the Y-axis below.
          0: {axis: 'PM2_5'},
          1: { axis: 'threshold',
                type: "steppedArea",
                color: '#FF0000',
                visibleInLegend: false,
                areaOpacity: 0,
                enableInteractivity: false
            }
        },
        axes: {
          // Adds labels to each axis; they don't have to match the axis names.
          y: {
            PM2_5: {label: 'µg/m³'},
            threshold: {
                ticks: [250], //use this if you want to show the target value
                baseline: 7 //this shifts the base line to 250
             }
          }
        }
      };

      var options_pm10 = {
        chart: {
          title: 'Prachové častice PM 10 (znečistenie vzduchu po dňoch)'
        },
        width: 1200,
        height: 300,
        series: {
          // Gives each series an axis name that matches the Y-axis below.
          0: {axis: 'PM10'}
        },
        axes: {
          // Adds labels to each axis; they don't have to match the axis names.
          y: {
            PM10: {label: 'µg/m³'},
          }
        }
      };

      function drawMaterialChart() {
        var chart_pm2_5 = new google.charts.Line(chart_div_pm2_5);
        chart_pm2_5.draw(data_pm2_5, options_pm2_5);
        var chart_pm10 = new google.charts.Line(chart_div_pm10);
        chart_pm10.draw(data_pm10, options_pm10);
      }

      drawMaterialChart();

    }
    </script>
    <style>
        .chart {
          width: 100%;
          min-height: 300px;
        }
    </style>
<?php /*In the air quality directive (2008/EC/50), the EU has set two limit values for particulate matter (PM10) for the protection of human health: the PM10 daily mean value may not exceed 50 micrograms per cubic metre (µg/m3) more than 35 times in a year and the PM10 annual mean value may not exceed 40 micrograms per cubic metre (µg/m3 */
;?>
  </body>
</html>
