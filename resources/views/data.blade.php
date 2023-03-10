<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Masyarakat</title>
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
</head>
<body>
    <h2 class="title-table">Laporan Keluhan</h2>
<div style="display: flex; justify-content: center; margin-bottom: 0 30px">
    <a href="/logout" style="text-align: center">Logout</a> 
    <div style="margin: 0 10px"> | </div>
    <a href="/" style="text-align: center">Home</a>
</div>
<div style="display: flex; justify-content: flex-end; align-items: center;">
    {{--menggunakan method Get karena route untuk masuk ke halaman data ini menggunakan ::get--}}
    <form action="" method="GET">
        @csrf
        <input type="text" name="search" placeholder="Cari berdasarkan nama...">
        <button type="submit" class="btn-login" style="margin-top: -1px">Cari</button>
    </form>
    {{-- refresh balik lg ke route data karna nanti pas di klik refresh bersihin riwayat pencarian sebelumnya dan balikin lagi ke halaman data lagi --}}
    <a href="{{route('data')}}" style="margin-left: 10px; margin-top: -2px">Refresh</a>
    <a href="{{route('export-pdf')}}" style="margin-left: 10px; margin-top: -10px">Cetak PDF</a>
    <a href="{{route('export.excel')}}" style="margin-left: 10px; margin-top: -10px">Cetak Excel</a>
</div>
<div style="padding: 0 30px">
    <table>
        <thead>
        <tr>
            <th width="5%">No</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Telp</th>
            <th>Pengaduan</th>
            <th>Gambar</th>
            <th>Status Response</th>
            <th>Pesan Response</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
            @endphp
            @foreach ($reports as $report)
                
            <tr>
                {{-- menambahkan angka 1 dari $no di php--}}
                <td>{{$no++}}</td>
                <td>{{$report['nik']}}</td>
                <td>{{$report['nama']}}</td>
                {{--mengganti format no yg 08 jadi 628--}}
                @php
                // substr_replace : mengubah karakter string
                // punya 3 argumen. argumen ke-1 : data yg mau dimasukin string
                // argumen ke-2 : mulai dr index mana ubahnya
                // argumen ke-3 : sampai index mana diubahnya
                    $telp = substr_replace($report->no_telp, "62", 0, 1);
                @endphp
                {{--yg ditampilkan tag a dengan $telp (data no_telp 08 udah diubah jadi format 628)--}}
                @php
                // kalau uda di response di data reportnya, cht wa nya data dr response di tampilin
                if ($report->response) {
                    $pesanWa = 'Hallo' . $report->nama . '! pengaduan anda di' . $report->response['status'] . '. Berikut pesan untuk anda : '. $report->response['pesan'];
                }
                // kalau belum di response pengaduannya, cht wa nya kya gini
                else {
                    $pesanWa = 'Belum ada data response!';
                }
                @endphp
                <td><a href="https://wa.me/{{$telp}}?text={{$pesanWa}}" target="_blank">{{$telp}}</a></td>
                <td>{{$report['pengaduan']}}</td>
                <td>
                    {{-- menampilkan gambar full layar di tab baru --}}
                    <a href="../assets/image/{{$report->foto}}" target="_blank">
                    <img src="{{asset('assets/image/'.$report->foto)}}" width="120">
                    </a>
                </td>
                <td>
                    {{-- cek apakah data report ini sudah memiliki relasi dengan data dr with('response')--}}
                    @if ($report->response)
                    {{-- kalau ada hasil relasinya, tampilkan bagian status --}}
                        {{ $report->response['status'] }}
                    @else 
                    {{-- kalau ga ada tampilkan tanda ini --}}
                        -
                    @endif
                </td>
                <td>
                    {{-- cek apakah data report ini sudah memiliki relasi dengan data dr with('response')--}}
                    @if ($report->response)
                    {{-- kalau ada hasil relasinya, tampilkan bagian pesan --}}
                        {{ $report->response['pesan'] }}
                    @else 
                    {{-- kalau ga ada tampilkan tanda ini --}}
                        -
                    @endif
                </td>
                <td>
                        <form action="{{route('destroy', $report->id)}}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                    </form>
                    <a href="{{route('print-pdf', $report->id)}}" method="GET" style="margin-top: -33px; margin-right: 3px; margin-left: 5px;">
                        @csrf
                        <button class="submit">Print</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>