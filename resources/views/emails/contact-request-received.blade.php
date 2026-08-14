<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuova richiesta dal sito</title>
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
                                Nuova richiesta dal sito
                            </h1>

                            <p style="margin:0 0 8px;font-size:16px;line-height:1.6;"><strong>Nome:</strong> {{ $contactRequest->full_name }}</p>
                            <p style="margin:0 0 8px;font-size:16px;line-height:1.6;"><strong>Email:</strong> <a href="mailto:{{ $contactRequest->email }}">{{ $contactRequest->email }}</a></p>
                            <p style="margin:0 0 8px;font-size:16px;line-height:1.6;"><strong>Telefono:</strong> <a href="tel:{{ $contactRequest->phone }}">{{ $contactRequest->phone }}</a></p>
                            <p style="margin:0 0 24px;font-size:16px;line-height:1.6;"><strong>Progetto:</strong> {{ ucfirst($contactRequest->project_type) }}</p>

                            <h2 style="margin:0 0 10px;font-size:20px;font-weight:500;">Messaggio</h2>
                            <p style="margin:0 0 28px;font-size:16px;line-height:1.6;">{!! nl2br(e($contactRequest->message)) !!}</p>

                            @if ($contactRequest->project_type === 'matrimoni')
                                <h2 style="margin:0 0 10px;font-size:20px;font-weight:500;">Dettagli matrimonio</h2>
                                <p style="margin:0 0 8px;font-size:16px;line-height:1.6;"><strong>Data:</strong> {{ $contactRequest->wedding_date?->format('d/m/Y') }}</p>
                                <p style="margin:0 0 8px;font-size:16px;line-height:1.6;"><strong>Orario:</strong> {{ $contactRequest->wedding_time ?: 'Non indicato' }}</p>
                                <p style="margin:0 0 8px;font-size:16px;line-height:1.6;"><strong>Cerimonia:</strong> {{ ucfirst($contactRequest->ceremony_type) }}</p>
                                <p style="margin:0 0 8px;font-size:16px;line-height:1.6;"><strong>Location:</strong> {{ $contactRequest->reception_location }}</p>
                                <p style="margin:0 0 8px;font-size:16px;line-height:1.6;"><strong>Invitati:</strong> {{ $contactRequest->guest_count }}</p>
                                <p style="margin:0 0 8px;font-size:16px;line-height:1.6;"><strong>Richiesta:</strong> {{ ucfirst($contactRequest->request_type) }}</p>
                                <p style="margin:0 0 8px;font-size:16px;line-height:1.6;"><strong>Servizi aggiuntivi:</strong> {{ implode(', ', $contactRequest->additional_services ?: []) ?: 'Nessuno' }}</p>
                                <p style="margin:0 0 8px;font-size:16px;line-height:1.6;"><strong>Servizi premium:</strong> {{ implode(', ', $contactRequest->premium_services ?: []) ?: 'Nessuno' }}</p>
                                <p style="margin:0 0 8px;font-size:16px;line-height:1.6;"><strong>Come vi hanno conosciuto:</strong> {{ $contactRequest->referral_source }}</p>
                                <p style="margin:20px 0 8px;font-size:16px;line-height:1.6;"><strong>Il matrimonio che immaginano:</strong></p>
                                <p style="margin:0 0 28px;font-size:16px;line-height:1.6;">{!! nl2br(e($contactRequest->wedding_story)) !!}</p>
                            @endif

                            <p style="margin:28px 0 0;font-size:13px;line-height:1.6;color:#5a5a5a;">
                                Puoi rispondere direttamente a questa email per contattare {{ $contactRequest->full_name }}.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
