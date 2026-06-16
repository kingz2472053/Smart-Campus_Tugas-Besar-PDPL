<!DOCTYPE html>
<html>
<head>
    <title>Laporan Nilai</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #000; padding-bottom: 10px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Nilai Mahasiswa</h2>
        <h4>Mata Kuliah: {{ $courseName }}</h4>
    </div>

    <div class="info">
        <strong>Nama:</strong> {{ $studentName }}<br>
        <strong>NIM:</strong> {{ $nim }}<br>
        <strong>Tanggal Cetak:</strong> {{ date('d F Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="45%">Judul Tugas</th>
                <th width="25%">Waktu Pengumpulan</th>
                <th width="15%" class="text-center">Nilai</th>
                <th width="10%" class="text-center">Skor Maks</th>
            </tr>
        </thead>
        <tbody>
            {{-- Ubah looping menjadi $assignments --}}
            @foreach($assignments as $index => $assignment)
            @php
                $sub = $assignment->submissions->first();
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $assignment->title }}</td>
                <td>{{ $sub && $sub->submitted_at ? $sub->submitted_at->format('d M Y, H:i') : '-' }}</td>
                <td class="text-center">
                    <strong>{{ $sub && $sub->latestGrade ? $sub->latestGrade->result : '-' }}</strong>
                </td>
                <td class="text-center">{{ $assignment->max_score }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>