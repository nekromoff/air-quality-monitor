<!doctype html>
<html lang="sk">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Monitoring kvality vzduchu, Bratislava, Slovensko</title>
  </head>
  <body>
    <div class="container">
        <div class="row">
            <h1>Monitoring kvality vzduchu v Bratislave</h1>
            <p>Senzory sú rozmiestnené v rôznych lokalitách v meste a poskytujú každých 5 minút meranie prachových častíc PM 2,5 a PM 10, teploty, vlhkosti a tlaku vzduchu.</p>
            <div id="chart"></div>
            @foreach ($averages as $item)
                <div class="col-sm-3">
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{$item->day}}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Senzor {{$sensors[$item->sensor_id]->number}}</h6>
                        <p class="card-text">
                            Denné priemerné hodnoty:
                            <ul>
                                <li>PM 10: <span class="badge badge-secondary">{{round($item->pm10,2)}}</span></li>
                                <li>PM 2,5: <span class="badge badge-secondary">{{round($item->pm2_5,2)}}</span></li>
                                <li>Teplota: <span class="badge badge-secondary">{{round($item->temperature,2)}} °C</span></li>
                                <li>Vlhkosť: <span class="badge badge-secondary">{{round($item->humidity,2)}}%</span></li>
                                @if ($item->pressure)
                                    <li>Tlak: <span class="badge badge-secondary">{{$item->pressure}}</span></li>
                                @endif
                            </ul>
                        </p>
                      </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
    google.charts.load('current', {'packages':['line', 'corechart']});
      google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

      var chartDiv = document.getElementById('chart');

      var data = new google.visualization.DataTable();
      data.addColumn('string', 'Day');
      data.addColumn('number', "PM 2,5");
      data.addColumn('number', "PM 10");

      data.addRows([
        @foreach ($averages_chart as $item)
            ['{{$item->day}}',  {{round($item->pm2_5,2)}},  {{round($item->pm10,2)}}]@if (!$loop->last),@endif
        @endforeach
      ]);

      var materialOptions = {
        chart: {
          title: 'Kvalita vzduchu po dňoch'
        },
        width: 900,
        height: 500,
        series: {
          // Gives each series an axis name that matches the Y-axis below.
          0: {axis: 'PM'},
        },
        axes: {
          // Adds labels to each axis; they don't have to match the axis names.
          y: {
            PM: {label: 'Prachové častice'},
          }
        }
      };

      var classicOptions = {
        title: 'Average Temperatures and Daylight in Iceland Throughout the Year',
        width: 900,
        height: 500,
        // Gives each series an axis that matches the vAxes number below.
        series: {
          0: {targetAxisIndex: 0},
          1: {targetAxisIndex: 1}
        },
        vAxes: {
          // Adds titles to each axis.
          0: {title: 'Temps (Celsius)'},
          1: {title: 'Daylight'}
        },
        hAxis: {
          ticks: [new Date(2014, 0), new Date(2014, 1), new Date(2014, 2), new Date(2014, 3),
                  new Date(2014, 4),  new Date(2014, 5), new Date(2014, 6), new Date(2014, 7),
                  new Date(2014, 8), new Date(2014, 9), new Date(2014, 10), new Date(2014, 11)
                 ]
        },
        vAxis: {
          viewWindow: {
            max: 30
          }
        }
      };

      function drawMaterialChart() {
        var materialChart = new google.charts.Line(chartDiv);
        materialChart.draw(data, materialOptions);
        button.innerText = 'Change to Classic';
        button.onclick = drawClassicChart;
      }

      function drawClassicChart() {
        var classicChart = new google.visualization.LineChart(chartDiv);
        classicChart.draw(data, classicOptions);
        button.innerText = 'Change to Material';
        button.onclick = drawMaterialChart;
      }

      drawMaterialChart();

    }
    </script>
  </body>
</html>
