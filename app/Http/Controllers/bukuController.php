<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class bukuController extends Controller
{
    // Method untuk mengambil semua buku
   public function index()
{
    $bukus = Buku::with('kategori')->get();
    return response()->json($bukus, 200);
}


    // Method untuk menyimpan data buku baru
    public function store(Request $request)
    {
        // Validasi input dari request
        $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'harga' => 'required|numeric|min:1000', // Harga minimal Rp 1.000
            'stok' => 'required|integer|min:1',
            'kategori_id' => 'required|exists:kategoris,id',
        ]);

        // Membuat instance baru dari model Buku
        $buku = new Buku();
        $buku->judul = $request->judul;
        $buku->penulis = $request->penulis;
        $buku->harga = $request->harga;
        $buku->stok = $request->stok;
        $buku->kategori_id = $request->kategori_id;
        $buku->save();

        // Mengembalikan response JSON setelah berhasil menyimpan buku
        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data' => $buku
        ], 201); // Kode 201 untuk Created
    }

    // Method untuk mengambil buku berdasarkan ID
    public function show($id)
    {
        $buku = Buku::with('kategori')->findOrFail($id); // Mengambil buku beserta kategori
        return response()->json($buku, 200); // Response dengan status 200 (OK)
    }

    // Method untuk memperbarui data buku berdasarkan ID
    public function update(Request $request, $id)
    {
        // Validasi input untuk update
        $request->validate([
            'judul' => 'sometimes|required|string|max:255',
            'penulis' => 'sometimes|required|string|max:255',
            'harga' => 'sometimes|required|numeric|min:1000',
            'stok' => 'sometimes|required|integer|min:0',
            'kategori_id' => 'sometimes|required|exists:kategoris,id',
        ]);
    
        // Cari buku berdasarkan ID, jika tidak ditemukan akan melempar 404
        $buku = Buku::findOrFail($id);
        
        // Update data buku
        $buku->update($request->only(['judul', 'penulis', 'harga', 'stok', 'kategori_id']));

        // Response berhasil dengan status 200 (OK)
        return response()->json([
            'message' => 'Buku berhasil diperbarui',
            'data' => $buku
        ], 200);
    }

    // Method untuk menghapus buku berdasarkan ID
    public function destroy($id)
    {
        Buku::destroy($id); // Menghapus buku
        return response()->json([
            'message' => 'Buku berhasil dihapus'
        ], 204); // Kode 204 untuk No Content
    }

    // Method untuk pencarian buku berdasarkan judul atau kategori
    public function search(Request $request)
    {
        $query = $request->input('query'); // Ambil query pencarian dari request

        // Pencarian berdasarkan judul atau kategori
        $bukus = Buku::where('judul', 'LIKE', "%$query%")
            ->orWhereHas('kategori', function ($queryKategori) use ($query) {
                $queryKategori->where('nama_kategori', 'LIKE', "%$query%");
            })
            ->get();

        // Response hasil pencarian
        return response()->json($bukus, 200); // Response dengan status 200 (OK)
    }

    public function indexView(){
        $bukus = Buku::with('kategori')->get(); // Ambil semua buku beserta kategori
        return view('index', compact('bukus')); // Kembali ke view index.blade.php
    }

}
