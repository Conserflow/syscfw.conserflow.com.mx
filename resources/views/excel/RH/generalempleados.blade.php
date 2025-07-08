<table>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr><tr>
        <td></td>
    </tr><tr>
        <td></td>
    </tr>
    <tr>
        <td style="background-color: #0070c0; color: white;">Nombre</td>
        <td style="background-color: #0070c0; color: white;">Fecha Ingreso</td>
        <td style="background-color: #0070c0; color: white;">CURP</td>
        <td style="background-color: #0070c0; color: white;">RFC</td>
        <td style="background-color: #0070c0; color: white;">Fecha Alta IMSS</td>
        <td style="background-color: #0070c0; color: white;">NSS</td>
        <td style="background-color: #0070c0; color: white;">Lugar Nacimiento</td>
        <td style="background-color: #0070c0; color: white;">Fecha Nacimiento</td>
        <td style="background-color: #0070c0; color: white;">Sexo</td>
        <td style="background-color: #0070c0; color: white;">Edo. Civil</td>
        <td style="background-color: #0070c0; color: white;">Tipo Sangre</td>
        <td style="background-color: #0070c0; color: white;">Talla Overol</td>
        <td style="background-color: #0070c0; color: white;">Talla Botas</td>
        <td style="background-color: #0070c0; color: white;">Amortizacion</td>
        <td style="background-color: #0070c0; color: white;">No. Crédito</td>
        <td style="background-color: #0070c0; color: white;">Ubicacion</td>
        <td style="background-color: #0070c0; color: white;">Empresa</td>
        <td style="background-color: #0070c0; color: white;">Correo Electrónico</td>
        <td style="background-color: #0070c0; color: white;">Tel.</td>
        <td style="background-color: #0070c0; color: white;">Tel. Casa</td>
        <td style="background-color: #0070c0; color: white;">Tel. Emergencia</td>
        <td style="background-color: #0070c0; color: white;">Contacto Emergencia</td>
        <td style="background-color: #0070c0; color: white;">Banco</td>
        <td style="background-color: #0070c0; color: white;">No. Tarjeta</td>
        <td style="background-color: #0070c0; color: white;">No. Cuenta</td>
        <td style="background-color: #0070c0; color: white;">Clabe</td>
        <td style="background-color: #0070c0; color: white;">Familiares</td>
        <td style="background-color: #0070c0; color: white;">Conocidos</td>
        <td style="background-color: #0070c0; color: white;">Referencias</td>
        <td style="background-color: #0070c0; color: white;">Solicitud</td>
        <td style="background-color: #0070c0; color: white;">Evaluacion Psicolaboral</td>
        <td style="background-color: #0070c0; color: white;">Evaluacion Hab. Técnicas</td>
        <td style="background-color: #0070c0; color: white;">Foto</td>
        <td style="background-color: #0070c0; color: white;">Acta Nac.</td>
        <td style="background-color: #0070c0; color: white;">INE</td>
        <td style="background-color: #0070c0; color: white;">CURP</td>
        <td style="background-color: #0070c0; color: white;">RFC</td>
        <td style="background-color: #0070c0; color: white;">NSS</td>
        <td style="background-color: #0070c0; color: white;">Comprobante Domicilio</td>
        <td style="background-color: #0070c0; color: white;">Cartilla Servicio Militar</td>
        <td style="background-color: #0070c0; color: white;">Licencia Manejo</td>
        <td style="background-color: #0070c0; color: white;">Acta Matrimonio</td>
        <td style="background-color: #0070c0; color: white;">Carta Infonavit</td>
        <td style="background-color: #0070c0; color: white;">Certificado Medico</td>
        <td style="background-color: #0070c0; color: white;">Carta No Enfermedades</td>
        <td style="background-color: #0070c0; color: white;">Vales Resguardo</td>
        <td style="background-color: #0070c0; color: white;">Comprobante Estudios</td>
        <td style="background-color: #0070c0; color: white;">Carta Recomendacion</td>
        <td style="background-color: #0070c0; color: white;">Retencion Infonavit</td>
        <td style="background-color: #0070c0; color: white;">Dirección</td>
        <td style="background-color: #0070c0; color: white;">Teléfono</td>
        <td style="background-color: #0070c0; color: white;">Correo</td>
    </tr>
    @foreach($empleados as $e)
    <tr>
        <td>{{$e["empleado"]}}</td>
        <td>{{$e["fech_ing"]}}</td>
        <td>{{$e["curp"]}}</td>
        <td>{{$e["rfc"]}}</td>
        <td>{{$e["fech_alta_imss"]}}</td>
        <td>{{$e["nss_imss"]}}</td>
        <td>{{$e["lug_nac"]}}</td>
        <td>{{$e["fech_nac"]}}</td>
        <td>{{$e["sexo"]==1?"Masculino":"Femenino"}}</td>
        <td>{{$e["estado_civil"]}}</td>
        <td>{{$e["tipo_sangre"]}}</td>
        <td>{{$e["talla_overol"]}}</td>
        <td>{{$e["talla_botas"]}}</td>
        <td>{{$e["amortizacion"]}}</td>
        <td>{{$e["numero_credito"]}}</td>
        <td>
            @if($e["ubicacion_id"]==1) Tehuacán, Puebla
            @elseif ($e["ubicacion_id"]==2) Coatzacoalcos, Veracruz
            @else Córdoba, Veracruz
            @endif
        </td>
        <td>
            @if($e["id_checador"]==1) Conserflow Semanal
            @elseif ($e["id_checador"]==2) Conserflow Quincenal
            @elseif ($e["id_checador"]==3) CSCT Semanal
            @else CSCT Quincenal
            @endif
        </td>

        <td>{{$e["contacto"]["correo_electronico"]}}</td>
        <td>{{$e["contacto"]["tel_celular"]}}</td>
        <td>{{$e["contacto"]["tel_casa"]}}</td>
        <td>{{$e["contacto"]["tel_emergencia"]}}</td>
        <td>{{$e["contacto"]["contacto_emergencia"]}}</td>

        <td>
            @if($e["banco"]["banco_id"]==1) SANTANDER
            @elseif ($e["banco"]["banco_id"]==2) BANCOMER
            @elseif ($e["banco"]["banco_id"]==3) BANORTE
            @else BANCOPPEL
            @endif
        </td>
        <td>{{$e["banco"]["numero_tarjeta"]}}</td>
        <td>{{$e["banco"]["numero_cuenta"]}}</td>
        <td>{{"".$e["banco"]["clabe"]. " "}}</td>

        <td>{{$e["familiares"]}}</td>
        <td></td>
        <td>{{$e["referencias"]}}</td>

        <td>{{$e["expedientes"]["solicitud"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["evaluacion_psicolaboral"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["evaluacion_hab_tecnicas"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["foto"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["acta_nacimiento"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["credencial_elector"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["curp"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["rfc"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["nss_imss"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["comprobante_domicilio"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["cartilla_servicio_militar"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["licencia_manejo"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["acta_matrimonio"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["carta_infonavit"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["certificado_medico"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["carta_no_enfermedades"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["vales_resguardo"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["comprobante_estudios"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["carta_recomendacion"]==1?'Sí':'-'}}</td>
        <td>{{$e["expedientes"]["retencion_credito_infonavit"]==1?'Sí':'-'}}</td>

        <td>{{$e["direccion"]}}</td>
        <td>{{$e["telefono"]["nombre"]}}</td>
        <td>{{$e["correo"]["nombre"]}}</td>
    </tr>
    @endforeach

</table>