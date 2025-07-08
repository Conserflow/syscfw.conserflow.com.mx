<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PRH-01/F-05 - LISTA DE ASISTENCIA</title>
</head>

<style type="text/css">
  @page {
    margin-top: 0cm;
    margin-left: 0cm;
    margin-right: 0cm;
    margin-bottom: 0cm;
  }


  header {
    position: fixed;
    top: -20px;
    width: 100%;
    height: 10rem;
  }

  footer {
    position: fixed;
    left: 0px;
    bottom: 0px;
  }

  .title {
    font-size: 48px;
    text-align: center;
    font-weight: bold;
    font-family: Arial, Helvetica, sans-serif;
    color: #005c93;
  }

  .mes {
    font-size: 32px;
    text-align: center;
    font-weight: bold;
    margin-top: -1rem;
    padding-bottom: 2rem;
    font-family: Arial, Helvetica, sans-serif;
    color: #005c93;
  }

  .title2 {
    font-size: 21px;
    font-family: Arial, Helvetica, sans-serif;
    color: #005c93;
  }

  .empleado {
    font-size: 12;
    font-weight: bold;
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

  .h-1 {
    padding-top: .2rem;
    padding-bottom: .2rem;
  }

  .text-left {
    text-align: left;
  }

  body {
    /* margin-top: 12rem;
    margin-bottom: 22rem; */
    padding-top: 10rem;
    padding-bottom: 22rem;
    background-image: url("img/cumple_fondo.jpg");
    /* background-position: 0 -15rem; */
    font-family: Arial, Helvetica, sans-serif;
  }

  .izq {
    top: -29px;
    position: absolute;
  }

  .der {
    top: -34px;
    right: 0px;
    position: absolute;
  }

  .fondo {
    position: absolute;
    top: 0;
  }
</style>

<body>
  <header>
    <div style="position: relative;">
      <p class="title">Felíz Cumpleaños</p>
      <p class="mes">{{$mes}}</p>
    </div>
  </header>

  <footer>
  </footer>

  <div>
    <div class="center" style="margin-left: 8.5rem;">
      <table width="80%" class="table border-all1">
        <tr>
          <th width="100%">
            <p class="title2">Nombre</p>
          </th>
          <th width="30%">
            <p class="title2">Día</p>
          </th>
        </tr>
        @foreach ($cumples as $c)
        <tr>
          <td class="h-1 empleado">
            <div class="text-left">{{ $c->empleado }}</div>
          </td>
          <td class="empleado"> {{$c->dia}}</td>
        </tr>
        @endforeach
      </table>
    </div>
  </div>
</body>

</html>
