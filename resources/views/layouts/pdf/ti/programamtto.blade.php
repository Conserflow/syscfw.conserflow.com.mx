<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PTI-01_F-04 - PROGRAMA ANUAL DE MANTENIMIENTO PREVENTIVO DE EQUIPO DE TI</title>
</head>

<style type="text/css">
  @page {
    margin-top: 3cm;
    margin-left: 1cm;
    margin-right: 1cm;
    margin-bottom: 2cm;
  }

  header {
    position: fixed;
    top: -90px;
    left: 0px;
    right: 0px;
  }

  footer {
    position: fixed;
    bottom: -40px;
    left: 0px;
    right: 0px;
    height: 30px;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
  }

  table {
    border-collapse: collapse;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
    text-align: center;
  }

  .border-all tr td {
    border: 1px solid black;
  }

  .text-blue {
    color: #0070c0;
  }

  .text-white {
    color: white;
  }

  .h-2 {
    padding-top: .5rem;
    padding-bottom: .5rem;
  }

  .h-4 {
    padding-top: 1rem;
    padding-bottom: 1rem;
  }

  .h-1 {
    padding-top: .3rem;
    padding-bottom: .3rem;
  }

  .text-bold {
    font-weight: bold;
  }

  .text-left {
    text-align: left;
  }

  .text-end {
    text-align: right;
  }

  .text-sm {
    font-size: 8;
  }

  .bg-blue {
    background-color: #0070c0;
    color: white;
    font-weight: bold;
  }

  .bg-gray {
    color: black;
    font-weight: bold;
    background-color: #BFBFBF;
  }

  body {
    font-family: Arial, Helvetica, sans-serif;
  }

  table tr .border-bottom-none {
    border-bottom: none
  }

  table tr .border-top-none {
    border-top: none;
  }
</style>

<body>
  <header>
    <table width="100%" style="border-collapse: collapse; border: 1px solid; font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            text-align: center;">
      <tr>
        <th colspan="4" style="border: 1px solid; ">
          <div style="color: #4472C4;"> CONSERFLOW S.A. DE C.V.</div>
        </th>
      </tr>
      <tr>
        <td rowspan="3" width="20%"><img src="img/conserflow.png" width="120"></td>
        <td rowspan="3" style="border: 1px solid; text-align: center;" width="55%"><b>PROGRAMA ANUAL DE MANTENIMIENTO PREVENTIVO DE EQUIPO DE TI</b> </td>
        <td style="border: 1px solid; text-align: center;" width="10%">CÓDIGO</td>
        <td style="border: 1px solid; text-align: center;" width="15%">PTI-01/F-04</td>
      </tr>
      <tr>
        <td style="border: 1px solid; text-align: center;">REVISIÓN</td>
        <td style="border: 1px solid; text-align: center;">00</td>
      </tr>
      <tr>
        <td style="border: 1px solid; text-align: center;">EMISIÓN</td>
        <td style="border: 1px solid; text-align: center;">01.ABR.20</td>
      </tr>
    </table>
  </header>

  <footer>
    <div>
      <p style="color: #0070c0;font-weight: bold;font-size: 10;">CONSERFLOW S.A. DE C.V.</p>
    </div>
  </footer>

  <div>
    <table width="100%" style="border-collapse: collapse; border: 1px solid; font-family: Arial, Helvetica, sans-serif;
                    font-size: 12px;">
      <tr>
        <td style="border-top: 1px solid white; border-left: 1 px solid white;">
        <b>Año: {{$anio}}</b> <br>
      </td>
      </tr>
      <tr>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>Tipo de Equipo</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>Marca</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>Modelo</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>No. Serie</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>Ene.</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>Feb.</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>Mar.</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>Abr.</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>May.</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>Jun.</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>Jul.</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>Ago.</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>Sep.</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>Oct.</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>Nov.</b> </div>
        </td>
        <td style="background-color: #0070C0; border: 1px solid; text-align:center;">
          <div style="color:white;"><b>Dic.</b> </div>
        </td>
      </tr>
      @foreach ($arreglo as $key => $value)
      <tr>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->tipo == 1 ? 'Computo' : 'Otros'}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->marca}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->modelo}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->num_serie}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->mes == 1 ? 'X' : ''}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->mes == 2 ? 'X' : ''}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->mes == 3 ? 'X' : ''}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->mes == 4 ? 'X' : ''}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->mes == 5 ? 'X' : ''}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->mes == 6 ? 'X' : ''}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->mes == 7 ? 'X' : ''}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->mes == 8 ? 'X' : ''}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->mes == 9 ? 'X' : ''}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->mes == 10 ? 'X' : ''}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->mes == 11 ? 'X' : ''}}</td>
        <td style="border: 1px solid; text-align: center;">&nbsp;{{$value->mes == 12 ? 'X' : ''}}</td>

      </tr>
      @endforeach


    </table>
  </div>

  <script type="text/php">
    if (isset($pdf)) {
        $text = "PAGINA {PAGE_NUM} DE {PAGE_COUNT}";
        $size = 9;
        $color = #0070c0;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 1;
        $y = $pdf->get_height() - 35;
        $pdf->page_text($x, $y, $text, $font, $size,$color);
    }
</script>
</body>

</html>