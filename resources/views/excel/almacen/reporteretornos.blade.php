<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Retornos</title>
</head>
<body>
  <table>
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr>
      <td></td>
      <td></td>
      <td></td>
      <td style="font-size: 14px; font-weight: bold; text-align: center">Retornos a Almacén</td></tr>
    <tr><td></td></tr>
    <tr><td></td></tr>
    <tr>
      <td style="font-weight: bold; background-color: #0070c0; color: white;">PROYECTO</td>
      <td style="font-weight: bold; background-color: #0070c0; color: white;">TIPO</td>
      <td style="font-weight: bold; background-color: #0070c0; color: white;">FECHA</td>
      <td style="font-weight: bold; background-color: #0070c0; color: white;">ARTÍCULO</td>
      <td style="font-weight: bold; background-color: #0070c0; color: white;">PRECIO UNITARIO</td>
      <td style="font-weight: bold; background-color: #0070c0; color: white;">CANTIDAD RETORNADO</td>
      <td style="font-weight: bold; background-color: #0070c0; color: white;">PENDIENTE</td>
      <td style="font-weight: bold; background-color: #0070c0; color: white;">ALMACÉN</td>
    </tr>
    @foreach ($salidas as $s)   
    <tr>
      <td>{{$s->proyecto}}</td>
      <td>{{$s->tipo}}</td>
      <td>{{$s->fecha}}</td>
      <td>{{$s->articulo}}</td>
      <td>{{$s->precio_unitario}}</td>
      <td>{{$s->cantidad_retornado}}</td>
      <td>{{$s->pendiente}}</td>
      <td>{{$s->almacen}}</td>
    </tr>
    @endforeach
  </table>
  
</body>
</html>