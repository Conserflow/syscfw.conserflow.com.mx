<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Etiquetas</title>
  <style>
    @page {
      margin: 40px 15px 15px 15px;
      font-family: Arial, Helvetica, sans-serif;
    }

    img {
      max-width: 320px;
    }

    .img-container {
      margin-left: 1.5rem;
      text-align: center;
      vertical-align: middle;
      padding-top: 5px;
      height: 180px;
    }

    .text-container {
      margin-left: 3rem;
      margin-right: 3rem;
      font-size: 8;
      text-align: center;
    }

    .code {
      padding: 0;
      margin: -10;
    }
  </style>

<body>
  <div>

    <table width="100%">
      @foreach($codigos as $c)
      <tr>
        <td width="50%">
          <div class="img-container">
            <img src="data:image/png;base64,'.{{$c[0]['img']}}.'" />
            <p class="code">{{$c[0]['codigo']}}</p>
            <div class="text-container">
              <p>{{$c[0]["descripcion"]}}</p>
            </div>
          </div>
        </td>
        @if(isset($c[1]))
        <td width="50%">
          <div class="img-container">
            <img src="data:image/png;base64,'.{{$c[1]['img']}}.'" />
            <p class="code">{{$c[1]['codigo']}}</p>
            <div class="text-container">
              <p>{{$c[1]["descripcion"]}}</p>
            </div>
          </div>
        </td>
        @endif
      </tr>
      @endforeach
    </table>
  </div>
</body>

</html>