<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Código QR del empleado</title>
</head>

<body>

    <table width="100%">
        <tr>
            <td style="border: 40px solid;" width="34%" height="196px;">
                <img src="data:image/png;base64,'.{{ base64_encode(QrCode::format('png')->errorCorrection('M')->size(300)->generate($text)) }}.'"
                    width="196px" />
            </td>
            <td width="35%">
                <p style="text-align: center">&nbsp;&nbsp;{{ $nombre }}</p>
            </td>
            <td width="20%">&nbsp;</td>
        </tr>
    </table>
</body>

</html>
