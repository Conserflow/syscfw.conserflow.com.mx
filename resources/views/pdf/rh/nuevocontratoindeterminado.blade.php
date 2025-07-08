<!DOCTYPE html>
<html lang="en">

<style type="text/css">
    @page {
        margin-top: 2cm;
        margin-left: 2cm;
        margin-right: 2cm;
        margin-bottom: 1.9cm;
        text-align: justify;
        text-justify: inter-word;
    }

    .main {
        font-family: Calibri, 'Trebuchet MS', sans-serif;
        font-size: 11;
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

    .text-center {
        text-align: center;
    }

    .text-bold {
        font-weight: bold;
    }

    .text-underline {
        font-weight: bold;
        text-decoration: underline;
    }

    .firma {
        border-top: 1px solid black;
        margin-left: 1rem;
        margin-right: 1rem;
    }
</style>

<body>
    <footer>
        <div>
            <p style="color: #0070c0;font-weight: bold;font-size: 10;"></p>
        </div>
    </footer>

    <div class="main">
        <p class="text-center text-bold">CONTRATO INDIVIDUAL DE TRABAJO POR TIEMPO INDETERMINADO</p>
        <p>Que celebran por una parte la sociedad denominada CONSERFLOW, S.A. DE C.V. representada
            por el señor RAMON CRUZ MARTINEZ a quien lo sucesivo se denominará como la EMPRESA y/o
            EMPLEADOR, y por otra parte el (la) señor (a) {{ $contrato->empleado }} a quien en lo sucesivo se denominara
            como TRABAJADOR (A); quienes se obligan al tenor de las siguientes declaraciones y clausulas:
        </p>
        <p class="text-center text-bold">DECLARACIONES</p>

        <p class="text-bold">I.- DE LA EMPRESA</p>
        <p>1.- Ser una sociedad legalmente constituida conforme a la legislación mexicana, con plena
            capacidad para contratar y obligarse en términos del presente contrato.</p>
        <p>2.- Tener su domicilio en Calle: Del Mezquite Lote 5 Manzana 3, Col. Santa Clara, C.P. 75820,
            Santiago Miahuatlán Puebla, México.</p>
        <p>3.- Que su objeto social es lícito.</p>
        <p>4.- Que por así convenir a sus intereses ha decidido llevar a cabo la celebración del presente
            contrato.</p>

        <p class="text-bold">II.- DEL TRABAJADOR</p>

        <p>1.- Contar con plena capacidad para contratar y obligarse en términos del presente contrato, ser de
            nacionalidad mexicana, con edad de {{ $contrato->edad }} años, de sexo
            {{ $contrato->sexo == 0 ? 'Femenino' : 'Masculino' }} y que su
            estado civil es {{ $contrato->edo_civil }}. Con Clave Única de Registro de Población
            {{ $contrato->curp }} y Registro Federal de Contribuyentes {{ $contrato->rfc }}.
        </p>
        <p>2.- Que es su voluntad celebrar el presente contrato laboral por así convenir a sus intereses.</p>
        <p>3.- Tener su domicilio en {{ $contrato->direccion }}.</p>
        <p>4.- Estar físicamente apto, así como contar con los conocimientos, habilidades y demás requisitos
            necesarios para desempeñar el puesto a que se refiere la cláusula segunda de este contrato.</p>
        <p>5.- Designar como beneficiario a {{ $contrato->beneficiario }}, de acuerdo con la fracción X del artículo 24
            de la Ley Federal del Trabajo, para el pago de los salarios y prestaciones devengadas y no cobradas a la
            muerte del trabajador o las que se generen por su fallecimiento o desaparición derivada de un acto
            delincuencial.</p>

        <p class="text-bold text-center">CLAUSULAS</p>

        <p><span class="text-bold">PRIMERA.- RELACIÓN LABORAL.-</span>
            Las partes están de acuerdo en iniciar una relación laboral
            por lo que la EMPRESA contrata al TRABAJADOR reconociendo que la relación laboral inicio el
            día {{ $contrato->fecha_inicio }} y están de acuerdo en darle formalidad a la misma mediante el presente
            contrato.</p>

        <p>La relación laboral podrá modificarse, suspenderse, rescindirse o terminarse por voluntad de las
            partes o en términos de lo previsto por la Ley Federal del Trabajo.</p>

        <p><span class="text-bold">SEGUNDA.- PUESTO DE TRABAJO.-</span> El TRABAJADOR prestará sus servicios para la
            empresa como {{ $contrato->puesto }}, cuyas funciones, obligaciones y responsabilidades le han sido
            informadas; debiendo desempeñar dicho trabajo con la intensidad, cuidado y esmero apropiados, bajo la
            dirección y subordinación de los representantes de la empresa.</p>

        <p><span class="text-bold">TERCERA.- FUNCIONES DEL TRABAJADOR.-</span>
            Las partes convienen que las principales labores del TRABAJADOR consistirán de manera
            enunciativa más no limitativa, por lo que el TRABAJADOR acepta también realizar todas
            aquellas que le sean encomendadas por sus superiores siempre que sean relacionadas a
            la actividad de la EMPRESA de acuerdo con las necesidades de productividad y
            desarrollo de ésta. El TRABAJADOR ratifica desde este momento su conformidad con esta
            condición de trabajo.</p>

        <p><span class="text-bold">CUARTA.- CONFIDENCIALIDAD.-</span>
            El TRABAJADOR se obliga durante la relación laboral, así
            como después de terminada ésta, bajo su más estricta responsabilidad, a no revelar a ninguna
            persona datos sobre la tecnología, mercadotecnia, sistemas, documentos a que tenga acceso en
            virtud del desempeño de su trabajo dentro de la empresa, y por lo tanto a guardar los secretos
            técnicos, de venta, comerciales, administrativos y de fabricación.</p>
        <p>El TRABAJADOR dará igual tratamiento a toda la información que llegue a conocer en relación con
            la EMPRESA, no pudiendo divulgar la información con personas ajenas a la empresa ni entre sus
            compañeros de trabajo más allá de lo estrictamente necesario para el desempeño de sus labores.
        </p>
        <p>
            En caso de faltar a la confidencialidad aquí establecida, el trabajador asumirá su responsabilidad
            en los términos de las leyes aplicables; además de considerarse como causa especial de rescisión
            de este contrato sin responsabilidad para la EMPRESA.
        </p>
        <p><span class="text-bold">QUINTA.- CLAUSULA DE NO COMPETENCIA.-</span>
            El TRABAJADOR se obliga a no realizar por su cuenta ninguna labor adicional a la
            que desarrolla para la EMPRESA, en especial cualquiera que pudiera ser considerada
            como competencia, deslealtad o que conlleve un conflicto de intereses entre el TRABAJADOR
            y la EMPRESA. De igual manera se obliga a no negociar por su cuenta con los clientes de
            la empresa.</p>
        <p>El incumplimiento de esta obligación será considerado como causa especial de rescisión de este
            contrato, sin responsabilidad para la EMPRESA. Además, el TRABAJADOR asumirá la
            responsabilidad en la que incurra de conformidad con las leyes aplicables.</p>

        <p><span class="text-bold">SEXTA.- LUGAR DE TRABAJO.-</span> El TRABAJADOR se obliga a prestar
            sus servicios en el lugar que designe la EMPRESA que será el ubicado en
            {{ $contrato->ubicacion }}. El TRABAJADOR es conocedor de quela Empresa requiere que
            preste sus servicios en diferentes partes del país, por lo que otorga su consentimiento
            y autorización para que la empresa pueda en cualquier otro momento designar otro lugar
            para la prestación de servicios, dentro del territorio nacional de acuerdo
            con las necesidades de desarrollo y productividad de ésta, conforme a lo establecido por
            la Ley Federal del Trabajo.</p>
        <p>El TRABAJADOR se obliga a permanecer en el lugar de prestación de servicios que le sea
            señalado, por el tiempo que dure la jornada laboral.</p>

        <p><span class="text-bold">SÉPTIMA.- PRODUCTIVIDAD.-</span>
            El TRABAJADOR se obliga a laborar de manera exclusiva para la
            EMPRESA durante su jornada laboral, quedando estrictamente prohibido dedicarse a cualquier
            otra actividad distinta durante la misma ya sea remunerada o no, a fin de que le sea posible
            desarrollar sus labores de manera eficiente de acuerdo a la productividad esperada.</p>
        <p><span class="text-bold">OCTAVA.- SALARIO.-</span>
            El salario DIARIO del TRABAJADOR será de ${{ $contrato->sdi_letra}}, que será pagado
            por {{ $contrato->tipo_nomina }} vencidas, en moneda de curso legal en el domicilio
            designado para el desarrollo de las actividades laborales, dentro de la jornada de
            trabajo. El TRABAJADOR autoriza a la EMPRESA a realizar las deducciones y/o retenciones
            legales que correspondan, mismas que serán desglosadas en los recibos que la EMPRESA
            entregue al TRABAJADOR.</p>

        <p>El TRABAJADOR autoriza a la EMPRESA para que, por seguridad, se realice el pago de su
            salario a través del depósito bancario, conforme a lo establecido en el primer párrafo
            el artículo 101 de la Ley Federal del Trabajo.</p>

        <p>Los recibos de pago deberán entregarse al TRABAJADOR preferentemente en forma electrónica, y
            en documento impreso cuando el TRABAJADOR así lo requiera al departamento de Recursos
            Humanos.</p>

        <p><span class="text-bold">NOVENA.- JORNADA LABORAL.-</span> El TRABAJADOR laborará
            semanalmente 48 cuarenta y ocho horas.</p>
        <p>La jornada laboral de lunes a viernes iniciará a las 8:00 horas y se interrumpirá a
            las 13:00 horas a fin de que el TRABAJADOR pueda descansar y consumir sus alimentos,
            pudiendo para tal efecto salir del lugar de trabajo; la jornada se reanudará a las
            15:00 horas y finalizará a las 18:00 horas. Los días sábados la jornada laboral
            iniciará a las 8:00 horas y finalizará a las 13:00 horas.
        </p>
        <p>Lo anterior sin perjuicio de que la EMPRESA, de acuerdo con las necesidades de producción o de
            cualquier otra circunstancia pueda a su juicio cambiar dicho horario, pero respetando en todo casa
            el número máximo de horas ordinarias que semanalmente puedan laborarse de acuerdo con la Ley
            Federal del Trabajo. El número de horas de la jornada semanal, podrá repartirse en tal forma que
            el TRABAJADOR disfrute de mayor descanso los sábados o cualquier otro día, o goce de alguna
            modalidad equivalente.</p>
        <p>Será obligación del TRABAJADOR laborar semanalmente el número máximo de horas ordinarias
            permitidas por la Ley y en consecuencia, aun cuando la EMPRESA disminuya dicho máximo, el
            TRABAJADOR deberá volver a laborarlo en cuento se le ordenado, sin que por ello tenga derecho
            a salario adicional.</p>
        <p><span class="text-bold">DÉCIMA. - TIEMPO DE DESCANSO Y PARA ALIMENTOS. -</span>
            La jornada laboral permitirá que el TRABAJADOR disfrute de un periodo para descansar y
            tomar sus alimentos fuera del centro de trabajo. El tiempo para ingestión de alimentos y
            descansar NO se computará como tiempo efectivo de trabajo.</p>

        <p><span class="text-bold">DECIMA PRIMERA.- CONTROL DE ASISTENCIA.-</span>
            El TRABAJADOR se obliga diariamente a firmar o marcar la hora de entrada y salida a
            sus labores, bajo el sistema que la EMPRESA establezca o llegue a establecer en el
            futuro, a fin de tener un control de tiempo y asistencia. El incumplimiento a esta
            obligación hará presumir a la EMPRESA la inasistencia del TRABAJADOR a su jornada laboral,
            pudiendo efectuar los correspondientes descuentos sobre el salario.</p>

        <p><span class="text-bold">DÉCIMA SEGUNDA. - DESCANSO SEMANAL. -</span>
            Por cada seis días consecutivos de trabajo, el
            TRABAJADOR gozará de un día de descanso con goce de sueldo íntegro; en caso de que éste no
            labore los seis días, recibirá una sexta parte de su salario por cada día que hubiere trabajado. Se
            pacta que el día de descanso semanal será el <span class="text-underline">Domingo.</span></p>

        <p><span class="text-bold">DECIMA TERCERA.- DÍAS DE DESCANSO OBLIGATORIO.-</span>
            Los días de descanso obligatorio
            serán los establecidos en articulo 74 la Ley Federal del Trabajo: I.- Primero de enero, II.- Primer
            lunes de febrero en conmemoración al cinco de febrero, III.- Tercer lunes de marzo en
            conmemoración al veintiuno de marzo, IV.- Primero de mayo, V.- Dieciséis de septiembre, VI.-
            Tercer lunes de noviembre en conmemoración al veinte de noviembre, VII.- El primero de
            diciembre cada seis años cuando corresponda a la transmisión del Poder Ejecutivo Federal, VIII.-
            El veinticinco de diciembre, y IX.- El que determinen las leyes federales y locales electorales, en el
            caso de elecciones ordinarias, para efectuar la jornada electoral.</p>

        <p><span class="text-bold">DECIMA CUARTA.- CAPACITACIÓN Y ADIESTRAMIENTO.-</span>
            La EMPRESA se compromete a otorgar capacitación y adiestramiento al TRABAJADOR, para
            elevar su nivel de vida y productividad en los términos de los planes y programas que
            se establezcan en la EMPRESA, de acuerdo con lo dispuesto por la Ley Federal del Trabajo.</p>

        <p>Atendiendo a la naturaleza del trabajo, esta capacitación se realizará fuera de la jornada
            de trabajo y será obligatoria para el TRABAJADOR.</p>

        <p>La capacitación tendrá como objeto actualizar y perfeccionar los conocimientos y habilidades
            del TRABAJADOR, incrementar la productividad, proporcionarle información sobre nueva
            tecnología y métodos aplicables a sus funciones, prevenir accidentes de trabajo y
            preparar al TRABAJADOR para ocupar otro puesto cuando exista vacante.</p>

        <p><span class="text-bold">DECIMA QUINTA.- OBLIGACIONES DEL TRABAJADOR.-</span>
            Son obligaciones del TRABAJADOR
            las contenidas en el artículo 134 de la Ley Federal del Trabajo, incluidas las siguientes: I. Cumplir
            con el reglamento interno de trabajo y las normas que indique la EMPRESA para su seguridad y
            protección personal; II.- Desempeñar sus labores bajo la dirección de la EMPRESA o de su
            representante, a cuya autoridad estará subordinado en todo lo concerniente al trabajo; III.- Ejecutar
            el trabajo con la intensidad, cuidado y esmero apropiados y en la forma, tiempo y lugar convenidos;
            IV.- Dar aviso inmediato a la EMPRESA, salvo caso fortuito o de fuerza mayor, de las causas
            justificadas que le impidan concurrir a su trabajo; V.- Restituir a la EMPRESA los materiales no
            usados y conservar en buen estado los instrumentos y útiles que le hayan dado para el desarrollo
            del trabajo, no siendo responsable por el deterioro que origine el uso de estos objetos, ni del
            ocasionado por caso fortuito, fuerza mayor, o por mala calidad o defectuosa construcción; VI.-
            Observar buenas costumbres durante el servicio; VII.- Prestar auxilios en cualquier tiempo que se
            necesiten, cuando por siniestro o riesgo inminente peligren las personas o los intereses de la
            EMPRESA o de sus compañeros de trabajo; VIII.- Someterse a los reconocimientos médicos
            previstos en el reglamento interior de trabajo y demás normas vigentes de la EMPRESA, para
            comprobar que no padecen alguna incapacidad o enfermedad de trabajo, contagiosa o incurable;
            IX. Poner en conocimiento de la EMPRESA las enfermedades contagiosas que padezcan, tan
            pronto como tenga conocimiento de las mismas; X.- Comunicar a la EMPRESA o a su
            representante las deficiencias que advierta, a fin de evitar daños o perjuicios a los intereses y vidas
            de sus compañeros de trabajo o de la EMPRESA; y XI.- Guardar escrupulosamente los secretos
            técnicos, comerciales y de fabricación de los productos a cuya elaboración concurran directa o
            indirectamente, o de los cuales tengan conocimiento por razón del trabajo que desempeñen, así
            como de los asuntos administrativos reservados, cuya divulgación pueda causar perjuicios a la
            empresa.</p>

        <p>Queda prohibido al trabajador: I. Ejecutar cualquier acto que pueda poner en peligro su propia
            seguridad, la de sus compañeros de trabajo o la de terceras personas, así como la del lugar en que
            el trabajo se desempeñe; II. Faltar al trabajo sin causa justificada o sin permiso de la EMPRESA;
            III. Substraer de la EMPRESA útiles de trabajo o materia prima o elaborada; IV. Presentarse al
            trabajo en estado de embriaguez; V. Presentarse al trabajo bajo la influencia de algún narcótico o
            droga enervante, salvo que exista prescripción médica. Antes de iniciar su servicio, el trabajador
            deberá poner el hecho en conocimiento de la EMPRESA y presentarle la prescripción suscrita por
            el médico; VI. Portar armas de cualquier clase durante las horas de trabajo; VII. Suspender las
            labores sin autorización de la EMPRESA; VIII. Hacer colectas en el lugar de trabajo; IX. Usar los
            útiles y herramientas suministrados por la EMPRESA, para objeto distinto de aquél a que están
            destinados; X. Hacer cualquier clase de propaganda en las horas de trabajo, dentro del
            establecimiento; y XI. Acosar sexualmente a cualquier persona o realizar actos inmorales en los
            lugares de trabajo.</p>

        <p><span class="text-bold">DÉCIMA SEXTA.- SANCIONES.-</span>
            Las partes están de acuerdo en que la EMPRESA podrá
            suspender al TRABAJADOR sin goce de sueldo hasta por un término de ocho días cuando éste
            incumpla con las obligaciones descritas en la cláusula que antecede.</p>

        <p>Cuando el TRABAJADOR deje de asistir justificadamente a laborar por enfermedad, deberá de
            presentar el certificado de incapacidad otorgado por la institución de Seguridad Social a más tardar
            el primer día que se presente de nuevo a laborar, ya que de lo contrario de considerará como falta
            injustificada. Las sanciones por faltas injustificadas al trabajo, serán las siguientes: I.- Por la
            primera falta de asistencia injustificada se aplicara una suspensión de un día sin goce de sueldo.
            II.- Por la segunda falta de asistencia injustificada se aplicara una suspensión de dos días sin goce
            de sueldo. III.- Por la tercera falta de asistencia injustificada se aplicara una suspensión de tres
            días sin goce de sueldo. IV.- Por más de tres faltas de asistencia injustificada en un plazo de treinta
            días, la EMPRESA podrá rescindir el contrato de trabajo, sin responsabilidad para ésta.</p>

        <p><span class="text-bold">DECIMA SÉPTIMA.- SEGURIDAD SOCIAL.-</span>
            La EMPRESA inscribirá o dará de alta oportunamente al TRABAJADOR ante el Instituto
            Mexicano del Seguro Social, obligándose el TRABAJADOR a permitir que la EMPRESA haga los
            descuentos en su salario que sean necesarios y tengan por objeto cubrir la cuota obrera
            ante el Instituto Mexicano del Seguro Social. Ambas partes se comprometen a cumplir con
            todo lo relatico a la Ley del Seguro Social y sus reglamentos.</p>

        <p><span class="text-bold">DÉCIMA OCTAVA.- RIESGOS DE TRABAJO.-</span>
            La EMPRESA inscribirá al TRABAJADOR dentro del régimen de Seguridad Social, por lo que
            todo lo relacionado con riesgos de trabajo y enfermedades o accidentes, se estará a lo
            dispuesto por la Ley Federal del Trabajo en la materia y sus reglamentos, sin
            responsabilidad alguna para la EMPRESA de tales enfermedades y accidentes.
            La EMPRESA Y el TRABAJADOR pagarán las cuotas que les correspondan.</p>

        <p>Es obligación del TRABAJADOR dar aviso inmediato a la EMPRESA cuando se encuentre en
            estado de incapacidad, presentando el Certificado que para tal efecto emita el Instituto Mexicano
            del Seguro Social, pudiendo hacer uso de los medios electrónicos de comunicación que para tal
            efecto disponga la empresa.</p>

        <p>Cuando una TRABAJADORA se encuentre embarazada deberá informar a la empresa de su
            estado cuando el mismo sea de su conocimiento, a fin de que la EMPRESA tome las medidas
            necesarias para preservar la salud de la TRABAJADORA en la medida de sus posibilidades.</p>

        <p><span class="text-bold">DÉCIMA NOVENA.- VACACIONES.-</span>
            La EMPRESA después de que el trabajador cumpla con un
            año de servicios concederá a éste un periodo vacacional con goce de sueldo, así como la prima
            vacacional correspondiente, las cuales nunca serán menos de las señaladas por la Ley Federal del
            Trabajo. Si la relación de trabajo termina antes de que se cumpla el año de servicios, el trabajador
            tendrá derecho a una remuneración proporcional por el tiempo que hubiere laborado.</p>

        <p><span class="text-bold">VIGÉSIMA.- PRIMA VACACIONAL.-</span>
            El TRABAJADOR tendrá derecho a una prima no menor de
            veinticinco por ciento sobre los salarios que le correspondan durante el periodo vacacional.</p>

        <p><span class="text-bold">VIGÉSIMA PRIMERA.- AGUINALDO.-</span>
            La EMPRESA pagará al TRABAJADOR un aguinaldo o
            gratificación anual equivalente a quince días de salario y lo cubrirá a más tardar el día veinte de
            diciembre del año correspondiente. Cuando el TRABAJADOR no haya laborado el año completo,
            tendrá derecho a recibir dicha prestación en la parte proporcional por el tiempo que hubiere
            laborado.</p>

        <p><span class="text-bold">VIGÉSIMA SEGUNDA.- INSTRUMENTOS DE TRABAJO.-</span>
            La EMPRESA se obliga a proporcionar
            al TRABAJADOR los equipos, artículos, procedimientos, manuales, catálogos, automóviles,
            información, herramientas, equipos de instrumentos y en general todas las facilidades necesarias
            para realizar su trabajo y a su vez el TRABAJADOR reconoce que son de la EMPRESA y se obliga
            a no usarlos para fines personales y a cuidarlos como propios, devolviéndolos a la EMPRESA
            cuando esta se los requiera.</p>
        <p>Cuando el EMPLEADOR no suministre directamente las herramientas de trabajo, al pagar el
            salario reintegrara al trabajador los gastos que hubiese hecho para allegarse de éstas a fin de no
            retrasar el trabajo.</p>

        <p><span class="text-bold">VIGÉSIMA TERCERA.- MOVILIDAD DE LAS CONDICIONES DE TRABAJO.-</span>
            La EMPRESA y el TRABAJADOR convienen en que, por las necesidades del servicio, producción
            y productividad, existirá flexibilidad o movilidad en las actividades que desempeñará el
            TRABAJADOR, por lo que se podrá modificar, sin menoscabo del salario, cualquier condición de trabajo o la
            modalidad de la prestación del servicio, tales como el horario, distribución de la jornada
            semanal de trabajo, etc. Por lo anterior, el TRABAJADOR otorga desde ahora su consentimiento
            y conformidad expresa con esta posibilidad de cambios, que constituye una condición
            especial de la relación de trabajo.</p>

        <p><span class="text-bold">VIGÉSIMA CUARTA.- TRABAJO ANTE TERCEROS.-</span>
            Considerando que la EMPRESA le presta sus servicios a terceros, y que estos pudieran
            ser a través o por conducto del TRABAJADOR, las partes convienen expresamente que
            siempre se considerará que el TRABAJADOR esta prestando sus servicios para la EMPRESA
            conforme a este contrato, y que será la EMPRESA quién pagará su salario y a quien
            estará subordinado el TRABAJADOR, por lo que la relación laboral continuará exclusivamente
            entre ambos.</p>

        <p><span class="text-bold">VIGÉSIMA QUINTA.- NORMATIVIDAD INTERNA.-</span>
            El TRABAJADOR manifiesta conocer el contenido del Reglamento interior de trabajo
            de la EMPRESA, se obliga expresamente a cumplir con él y manifiesta haber recibido
            una copia del mismo. Se obliga a cumplir también con las circulares y órdenes verbales o
            escritas para la seguridad y protección de su persona y de sus bienes, así como de
            sus compañeros de trabajo y de los bienes de éstos.
        </p>

        <p><span class="text-bold">VIGÉSIMA SEXTA.- CAUSAS DE RESCISIÓN DE CONTRATO.-</span>
            Ambas partes, acuerdan que la EMPRESA tendrá la facultad de Rescindir el presente
            contrato sin responsabilidad, cuando se actualice alguna de las causales establecidas
            por el artículo 47 la Ley Federal del Trabajo, así como las expresamente acordadas en
            este contrato:
        </p>
        <p>I. Engañar el trabajador a la EMPRESA con certificados falsos o referencias en los que se
            atribuyan al trabajador capacidad, aptitudes o facultades de que carezca en términos establecidos
            por la Ley; II. Incurrir el trabajador, durante sus labores, en faltas de probidad u honradez, en actos
            de violencia, amagos, injurias o malos tratamientos en contra del personal directivo o administrativo
            de la empresa, su familia, o en contra de clientes y proveedores de la empresa, salvo que medie
            provocación o que obre en defensa propia; III. Cometer el trabajador contra alguno de sus
            compañeros, cualquiera de los actos enumerados en la fracción anterior, si como consecuencia de
            ellos se altera la disciplina del lugar en que se desempeña el trabajo; IV. Cometer el trabajador,
            fuera del servicio, contra el personal directivo, administrativo o su familia, alguno de los actos a que
            se refiere la fracción II, si son de tal manera graves que hagan imposible el cumplimiento de la
            relación de trabajo; V. Ocasionar el trabajador, intencionalmente, perjuicios materiales durante el
            desempeño de las labores o con motivo de ellas, en los edificios, obras, maquinaria, instrumentos,
            materias primas y demás objetos relacionados con el trabajo; VI. Ocasionar el trabajador los
            perjuicios de que habla la fracción anterior siempre que sean graves, sin dolo, pero con negligencia
            tal, que ella sea la causa única del perjuicio; VII. Comprometer el trabajador, por su imprudencia o
            descuido inexcusable, la seguridad del establecimiento o de las personas que se encuentren en él;
            VIII. Cometer el trabajador actos inmorales o de hostigamiento y/o acoso sexual contra cualquier
            persona en el establecimiento o lugar de trabajo; IX. Revelar el trabajador los secretos de
            fabricación o dar a conocer asuntos de carácter reservado, con perjuicio de la empresa; X. Tener el
            trabajador más de tres faltas de asistencia en un período de treinta días, sin permiso del patrón o
            sin causa justificada; XI. Desobedecer el trabajador al patrón o a sus representantes, sin causa
            justificada, siempre que se trate del trabajo contratado; XII. Negarse el trabajador a adoptar las
            medidas preventivas o a seguir los procedimientos indicados para evitar accidentes o
            enfermedades; XIII. Concurrir el trabajador a sus labores en estado de embriaguez o bajo la
            influencia de algún narcótico o droga enervante, salvo que, en este último caso, exista prescripción
            médica. Antes de iniciar su servicio, el trabajador deberá poner el hecho en conocimiento del
            patrón y presentar la prescripción suscrita por el médico; XIV. La sentencia ejecutoriada que
            imponga al trabajador una pena de prisión, que le impida el cumplimiento de la relación de trabajo;
            XIV Bis. La falta de documentos que exijan las leyes y reglamentos, necesarios para la prestación
            del servicio cuando sea imputable al trabajador y que exceda del periodo a que se refiere la
            fracción IV del artículo 43; XV. La falta a las cláusulas de confidencialidad y no competencia
            establecidas en el presente contrato; y XV. Las análogas a las establecidas en las fracciones
            anteriores, de igual manera graves y de consecuencias semejantes en lo que al trabajo se refiere.</p>

        <p>En tal caso, la EMPRESA dará aviso por escrito y de manera personal al TRABAJADOR de su
            decisión de rescindir el contrato, señalando la conducta o conductas que motivan su decisión, así
            como la fecha o fechas en que se cometieron. En caso de que no pudiera hacer dicha
            comunicación en el momento del despido, lo hará en términos del artículo cuarenta y siete de la
            Ley Federal del Trabajo.</p>

        <p><span class="text-bold">VIGÉSIMA SÉPTIMA.-</span>
            El TRABAJADOR manifiesta que todos y cada uno de los datos
            proporcionados al solicitar el empleo son absolutamente ciertos, bajo pena de incurrir en falta de
            probidad. Manifiesta también, que le han sigo explicadas sus funciones dentro de la EMPRESA,
            estar capacitado y contar con las aptitudes necesarias para el desarrollo de éstas.</p>

        <p><span class="text-bold">VIGÉSIMA OCTAVA.-</span>
            Todo lo que no está debidamente pactado en el presente contrato, se
            sujetará a la Ley Federal del Trabajo y demás disposiciones aplicables.
        </p>
        <p>Leído que fue por ambas partes el presente contrato, las partes lo firman manifestando su
            conformidad firmando al margen y al calce de este documento, entregándose un tanto al
            TRABAJADOR.
        </p>
        <br>
        <p>Santiago Miahuatlan, Puebla; a {{ $contrato->fecha_inicio }}.</p>
        <br>
        <table width="80%" style="text-align: center;" align="center">
            <tr>
                <td width="50%">LA EMPRESA y/o EMPLEADOR</td>
                <td width="50%">
                    CONSERFLOW, S.A. DE C.V.</td>
            </tr>
            <tr>
                <td width="50%">
                    <span style="color: white;">mmmmmmmm</span>
                    <br><br><br><br><br>
                    <div class="firma" style="vertical-align: top;">
                    </div>
                </td>
                <td width="50%"></td>
            </tr>
            <tr>
                <td>
                    <br><br><br>
                </td>
            </tr>
            <tr>
                <td width="50%">
                    <br><br><br><br><br>
                    <div class="firma">
                        Nombre, firma y <br>huella digital del pulgar derecho
                        <span style="color: white;">mmmmmmmm</span>
                    </div>
                </td>
                <td>
                    <div class="" style="vertical-align: top;">
                        <span style="vertical-align: top">EL TRABAJADOR</span>
                        <br><br><br><br><br>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
        $text = "{PAGE_NUM}";
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
