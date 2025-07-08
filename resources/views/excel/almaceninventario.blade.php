<table>
  <tr>
    <td colspan="6" style="font-weight: bold; text-align : center; color:#0675ca">CONSERFLOW S.A. DE C.V.</td>
  </tr>
  <tr>
    <td rowspan="3"></td>
    <td rowspan="3" colspan="3" style="text-align : center;">
      <b>REPORTE DE INVENTARIO</b>
    </td>
    <td style="font-weight: bold;text-align : center;">CÓDIGO</td>
    <td style="text-align : center;">PAL-01/F-06</td>
  </tr>
  <tr>
    <td style="font-weight: bold;text-align : center;">REVISIÓN</td>
    <td style="text-align : center;">00</td>
  </tr>
  <tr>
    <td style="font-weight: bold;text-align : center;">EMISIÓN</td>
    <td style="text-align : center;">29.MAR.21</td>
  </tr>
  <tr>
    <td colspan="6"></td>
  </tr>
  <tr>
    <td colspan="2"></td>
    <td style="background-color : #bababa; text-align : center;">Fecha del Reporte</td>
    <td colspan="3" style="text-align : center;"> <b>{{date("Y-m-d")}}</b>
    </td>
  </tr>
  <thead>
    <tr>
      <th colspan="2" style="color:white;background-color : #0675ca; text-align : center;"><b>Artículo</b></th>
      <th style="color:white; background-color : #0675ca; text-align : center;"><b>Existencia</b></th>
      <th style="color:white; background-color : #0675ca; text-align : center;"><b>Real</b></th>
      <th style="color:white; background-color : #0675ca; text-align : center;"><b>Diferencia</b></th>
      <th style="color:white; background-color : #0675ca; text-align : center;"><b>Observaciones</b></th>
    </tr>
  </thead>
  <tbody>
    @foreach($articulos as $articulo)
    <tr>
      <td colspan="2">{{$articulo->nombre}}</td>
      <td>{{$articulo->existecia_sistema}}</td>
      <td>{{$articulo->existencia_real}}</td>
      <td>{{$articulo->existecia_sistema - $articulo->existencia_real}}</td>
      <td>{{$articulo->observaciones}}</td>
    </tr>
    @endforeach
  </tbody>


</table>