<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Excel;
use App\Exports\ReportsExport;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function exportPDF()
    {
        // ambil data dari database, ambil data yg akan ditampilkan pada pdf, bisa juga dengan where atau eloquent lainnya dan jangan gunakan pagination
        // jangan lupa konvert data jadi array dengan toArray()
        $data = Report::with('response')->get()->toArray();
        // kirim data yg diambil kepada view yg akan ditampilkan, kirim dengan inisial
        view()->share('reports', $data);
        // panggil view blade yang akan dicetak pdf serta data yg akan digunakan
        $pdf = PDF::loadView('print', $data)->setPaper('a4', 'landscape');
        // download PDF file dengan nama tertentu
        return $pdf->download('data_pengaduan_keseluruhan.pdf');
    }

    public function printPDF($id)
    {
        // ambil data dari database, ambil data yg akan ditampilkan pada pdf, bisa juga dengan where atau eloquent lainnya dan jangan gunakan pagination
        // jangan lupa konvert data jadi array dengan toArray()
        $data = Report::with('response')->where('id', $id)->get()->toArray();
         // kirim data yg diambil kepada view yg akan ditampilkan, kirim dengan inisial
        view()->share('reports', $data);
        // panggil view blade yang akan dicetak pdf serta data yg akan digunakan
        $pdf = PDF::loadView('print', $data);
        // download PDF file dengan nama tertentu
        return $pdf->download('data_pengaduan_keseluruhan.pdf');
    }

    public function exportExcel()
    {
        // nama file yang akan terdownload
        $file_name =
        'data_keseluruhan_pengaduan.xlsx';
        // memanggil file ReportsExport dan mendownloadnya dengan nama seperti $file_name
        return Excel::download(new ReportsExport, $file_name);
    }
    
    public function index()
    {
        $reports = Report::orderBy('created_at', 'DESC')->simplePaginate(2);
        return view('index', compact('reports'));
    }

    // Request $request ditambahkan karena pada halaman data ada fitur search nya dan akan mengambil teks yang diinput search
    public function data(Request $request)
    {
        // ambil data yang diinput ke input yang name nya search
        $search = $request->search;
        // where akan mencari data berdasarkan column nama
        // data yang diambil merupakan data yg 'LIKE' (terdapat) teks yang dimasukan ke input search
        // contoh : ngisi input search dengan 'fem'
        // bakal nyari ke db yg column nama nya ada isi 'fem' nya
        $reports = Report::with('response')->where('nama', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'DESC')->get();
        return view('data', compact('reports'));
    }

    public function dataPetugas(Request $request)
    {
        $search = $request->search;
        // with : ambil relasi (nama fungsi hasOne/hasMany/belongsTo di modelnya), ambil data dr relasi itu
        $reports = Report::with('response')->where('nama', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'DESC')->get();
        return view('data_petugas', compact('reports'));
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    public function auth(Request $request)
    {
        $request->validate([
            'email' => 'required|email:dns',
            'password' => 'required',
        ]);
           // ambil data dan simpan di variable
           $user = $request->only('email', 'password');
           // simpan data tersebut ke fitur auth dengan Auth::attempt
           // cek proses penyimpanan ke auth berhasil atau tidak lewat if else
           if (Auth::attempt($user)) {
            // nesting if, if bersarang, if didalam if
            // kalau data login uda masuk ke fitur Auth, dicek lagi pake if-else
            // kalau data Auth tersebut role nya admin maka masuk ke route data
            // kalau data Auth role nya petugas maka masuk ke route data.petugas
            if (Auth::user()->role == 'admin') {
                return redirect()->route('data');
            }elseif (Auth::user()->role == 'petugas') {
                return redirect()->route('data.petugas');
            }
        }else{
            return redirect()->back()->with('gagal', 'Gagal login, coba lagi!');
        }
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'nama' => 'required',
            'no_telp' => 'required|max:13',
            'pengaduan' => 'required|min:5',
            'foto' => 'required|image|mimes:jpeg,jpg,png,svg',
        ]); 
        //pindah foto ke folder public
        $path = public_path('assets/image/');
        $image = $request->file('foto');
        $imgName = rand() . '.' . $image->extension();
        $image->move($path, $imgName);

        //tambah data ke database
        Report::create([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'pengaduan' => $request->pengaduan,
            'foto' => $imgName,
        ]);
        
        return redirect()->back()->with('success', 'Berhasil menambahkan pengaduan baru!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // cari data yang dimaksud
        $data = Report::where('id', $id)->firstOrFail();
        // $data isinya nik sampe foto dr pengaduan
        // hapus data folder dari folder public : path . nama fotonya
        // nama fotonya diambil dari $data yg diatas trs ngambil dr column 'foto'
        $image = public_path('assets/image/'.$data['foto']);
        // udah nemu posisi fotonya tinggal dhpus pke unlink
        unlink($image);
        $data->delete();
        // hapus data dari database
        Response::where('report_id', $id)->delete();
        return redirect()->back();
        // setelahnya dikembalikan lg ke halaman awal
    }
}
