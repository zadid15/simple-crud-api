<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BarangResource;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    //
    public function index()
    {
        $barang = Barang::latest()->paginate(5);
        return new BarangResource(true, 'List Data Barang', $barang);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required',
            'berat' => 'required',
            'poto' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $poto = $request->file('poto');
        $new_name = rand() . '.' . $poto->getClientOriginalExtension();
        $poto->move(public_path('storage/barangs'), $new_name);

        $barang = Barang::create([
            'nama_barang' => $request->nama_barang,
            'berat' => $request->berat,
            'poto' => $new_name,
        ]);

        return new BarangResource(true, 'Data Barang Berhasil Ditambahkan', $barang);
    }

    public function show($id)
    {
        //
        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json([
                'status' => false,
                'message' => 'Data Barang Tidak Ditemukan',
            ], 404);
        }
        return new BarangResource(true, 'Data Barang Ditemukan!', $barang);
    }

    public function update(Request $request, $id)
    {
        //
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required',
            'berat' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $barang = Barang::find($id);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $barang = Barang::find($id);
        if ($request->hasFile('poto')) {
            $poto = $request->file('poto');
            $poto->storeAs('public/barangs', $poto->hashName());

            Storage::delete('public/barangs/' . $barang->poto);

            $barang->update([
                'nama_barang' => $request->nama_barang,
                'berat' => $request->berat,
                'poto' => $poto->hashName(),
            ]);
        } else {
            $barang->update([
                'nama_barang' => $request->nama_barang,
                'berat' => $request->berat,
            ]);
        }

        return new BarangResource(true, 'Data Barang Berhasil Diupdate!', $barang);
    }

    public function destroy($id)
    {
        //
        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json([
                'status' => false,
                'message' => 'Data Barang Tidak Ditemukan',
            ], 404);
        }
        Storage::delete('public/barangs/' . $barang->poto);
        $barang->delete();
        return new BarangResource(true, 'Data Barang Berhasil Dihapus!', null);
    }
}
