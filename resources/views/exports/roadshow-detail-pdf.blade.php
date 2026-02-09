<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Detail Roadshow - {{ $kabupaten }}, {{ $provinsi }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 14px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DETAIL ROADSHOW PROMOSI</h1>
        <p>Wilayah: {{ $kabupaten }}, {{ $provinsi }}</p>
        <p>Tanggal Cetak: {{ now()->locale('id_ID')->isoFormat('D MMMM YYYY') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Sekolah</th>
                <th>Penanggung Jawab</th>
                <th>Program Studi</th>
                <th>Jumlah Alumni</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['tanggal'] }}</td>
                <td>{{ $item['nama_sekolah'] }}</td>
                <td>{{ $item['penanggungjawab'] }}</td>
                <td>{{ $item['prodi'] }}</td>
                <td>{{ $item['alumni'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        Laporan ini dicetak dari SIM-PROMOSI
    </div>
</body>
</html>