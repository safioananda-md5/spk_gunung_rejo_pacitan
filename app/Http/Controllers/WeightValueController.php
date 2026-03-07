<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\WeightValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WeightValueController extends Controller
{
    public function index()
    {
        return view('admin.weight_value');
    }

    public function getData(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $wight_values = WeightValue::orderBy('gap', 'asc')->get();

        return response()->json([
            'data' => $wight_values
        ]);
    }

    public function post(Request $request)
    {
        try {
            $request->validate(
                [
                    'gap' => 'required|numeric',
                    'weight' => 'required|numeric',
                ],
                [
                    'gap.required' => 'Nilai selisih wajib diisi!',
                    'gap.numeric' => 'Nilai selisih wajib angka!',
                    'weight.required' => 'Nilai bobot wajib diisi!',
                    'weight.numeric' => 'Nilai bobot wajib angka!',
                ]
            );

            // dd($request->all());
            DB::beginTransaction();
            WeightValue::create([
                'gap' => $request->gap,
                'weight' => $request->weight,
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Data berhasil ditambahkan.'
            ], 200);
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $allErrors = collect($errors)->flatten()->implode('<br> • ');
            return response()->json([
                'message' => 'Inputan Gagal! Periksa kembali isian Anda. <br> • ' . $allErrors
            ], 500);
        } catch (Throwable $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Inputan Gagal! Periksa kembali isian Anda. <br> ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();
            WeightValue::where('id', $request->id)->delete();
            DB::commit();
            return response()->json([
                'message' => 'Data berhasil dihapus.'
            ], 200);
        } catch (Throwable $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Penghapusan Gagal! <br> ' . $e->getMessage()
            ], 500);
        }
    }
}
