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
    <form action="{{route('response.update', $reportId)}}" method="POST" style="width: 500px; margin: 50px auto; display: block;">
        @csrf
        @method('PATCH')
        <div class="input-card">
            <label for="status">Status :</label>
            {{-- cek apakah data $report yang dr compact itu adaan, maksudnya tuh where di db responses nya ada data yg punya report_id sama kata yg dikirim ke pat (report_id) --}}
            @if ($report)
            <select name="status" id="status">
                {{-- kalau ada --}}
                <option value="ditolak" {{ $report['status'] == 'ditolak' ? 'selected' : ''}}>ditolak</option>
                <option value="proses" {{ $report['status'] == 'proses' ? 'selected' : ''}}>proses</option>
                <option value="diterima" {{ $report['status'] == 'diterima' ? 'selected' : ''}}>diterima</option>
            </select>
            @else
            <select name="status" id="status">
                <option selected hidden disabled>Pilih Status</option>
                <option value="ditolak">ditolak</option>
                <option value="proses">proses</option>
                <option value="diterima">diterima</option>
            </select>
            @endif
        </div>
        <div class="input-card">
            <label for="pesan">Pesan :</label>
            <textarea name="pesan" id="pesan" rows="3">{{ $report ? $report['pesan'] : ''}} </textarea>
        </div>
        <button type="submit">Kirim Response</button>
    </form>
</body>
</html>