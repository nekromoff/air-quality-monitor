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
            @foreach ($averages as $sensor_id=>$item)
                <div class="col-sm-3">
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">{{$item->day}}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">Senzor {{$sensors[$sensor_id]->number}}</h6>
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
  </body>
</html>
