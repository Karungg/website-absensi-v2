<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Formulir Cuti Tahunan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0 50px;
        }

        .centered {
            text-align: center;
        }

        .section-title {
            margin-bottom: 10px;
        }

        .line {
            border-bottom: 1px solid black;
        }

        .signature div {
            text-align: center;
        }

        table {
            width: 100%;
        }

        table tr td {
            padding: 5px 0;
        }

        .end {
            text-align: right
        }
    </style>
</head>

<body>
    <div class="centered">
        <h3>FORMULIR CUTI TAHUNAN</h3>
    </div>

    <div class="header">
        <table>
            <tr>
                <td>Nama Pegawai</td>
                <td>: {{ $request->user->name }}</td>
            </tr>
            <tr>
                <td>NPK</td>
                <td>: {{ $request->user->nip }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>: {{ $request->user->position->title }}</td>
            </tr>
            <tr>
                <td>Tgl. Masuk kerja</td>
                <td>: {{ $request->user->date_of_entry->format('d M Y') }}</td>
            </tr>
            <tr>
                <td>Kategori Ajuan</td>
                <td>:
                    @if ($request->type == \App\Enum\TypeRequest::Leave)
                        Cuti
                    @elseif($request->type == \App\Enum\TypeRequest::Sick)
                        Sakit
                    @else
                        Izin
                    @endif
                </td>

            </tr>
            <tr>
                <td>Sisa Cuti</td>
                <td>: {{ $request->user->leave_allowance }} Hari</td>
            </tr>
            <tr>
                <td>Cuti yang diambil</td>
                <td>: {{ $leaveAmount }} Hari
                    ({{ $request->start_date->format('d M Y') . ' Sampai ' . $request->end_date->format('d M Y') }})
                </td>
            </tr>
            <tr>
                <td>( {{ $request->location == 'Dalam Kota' ? 'X' : '' }} ) Dalam Kota</td>
                <td></td>
            </tr>
            <tr>
                <td>( {{ $request->location != 'Dalam Kota' ? 'X' : '' }} ) Luar Kota</td>
                <td>: {{ $request->location != 'Dalam Kota' ? $request->location : '' }}</td>
            </tr>
        </table>
    </div>

    <div style="width: 100%;">
        <div style="display: inline-block; width: 49%; vertical-align: top;">
            <p>Menyetujui</p>
            <img src="data:image/png;base64, {{ base64_encode(QrCode::size(100)->generate(route('annountcement', $request->id))) }}"
                alt="QR Code">
            <p>( {{ $request->user->hasRole('employee') ? $headOfDivisionName : $resourceOrDirector }} )</p>
        </div>
        <div style="display: inline-block; width: 49%; text-align: right; vertical-align: top;">
            <p>Bogor, {{ $request->updated_at->format('d M Y') }}</p>
            <br><br>
            <p>( {{ $request->user->name }} )</p>
        </div>
    </div>

    <div class="line"></div>

    <div class="section">
        <p>Catatan Atasan : </p>
        <div style="height: 60px;"></div>
    </div>

    {{-- @if (!$request->user->roles('employee'))
        <div class="end">
            <p>Mengetahui</p>
            <p>( Gatot Sumargono )</p>
        </div>
    @endif --}}

    <div class="line"></div>

    <div class="section">
        <p>Catatan Khusus Bagian Personalia:</p>
        <div style="height: 60px;"></div>
    </div>

    <div class="end">
        <img src="data:image/png;base64, {{ base64_encode(QrCode::size(100)->generate(route('annountcement', $request->id))) }}"
            alt="QR Code">
        <p>( Kepala Balai )</p>
    </div>

    {{-- @hasanyrole(['employee', 'headOfDivision'])
        <div class="end">
            <img src="data:image/png;base64, {{ base64_encode(QrCode::size(100)->generate(route('annountcement', $request->id))) }}"
                alt="QR Code">
            <p>( SDM )</p>
        </div>
    @endhasanyrole --}}
</body>

</html>
