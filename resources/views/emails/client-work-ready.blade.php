<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Il tuo nuovo lavoro fotografico è disponibile</title>
</head>
<body style="margin:0;padding:0;background:#f4f4ed;color:#111111;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f4ed;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                    style="max-width:620px;background:#ffffff;border:1px solid #e5e5dc;">
                    <tr>
                        <td style="padding:36px;">
                            <p style="margin:0 0 28px;font-size:14px;">MICHELE MARIANI FOTOGRAFO</p>

                            <h1 style="margin:0 0 20px;font-size:32px;line-height:1.2;font-weight:500;">
                                Ciao {{ $client->customer->name }},<br>il tuo nuovo lavoro è disponibile.
                            </h1>

                            <p style="margin:0 0 28px;font-size:16px;line-height:1.6;">
                                {{ $client->description }}
                            </p>

                            @if ($accessUrl)
                                <a href="{{ $accessUrl }}"
                                    style="display:inline-block;background:#111111;color:#ffffff;text-decoration:none;padding:14px 20px;font-size:16px;">
                                    Visualizza il lavoro
                                </a>

                                <p style="margin:28px 0 0;font-size:13px;line-height:1.6;color:#5a5a5a;">
                                    Se si tratta di un collegamento all'area privata, resterà valido per 7 giorni.
                                </p>
                            @else
                                <p style="margin:0;font-size:14px;line-height:1.6;color:#5a5a5a;">
                                    Michele ti contatterà con le indicazioni necessarie per consultarlo.
                                </p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
