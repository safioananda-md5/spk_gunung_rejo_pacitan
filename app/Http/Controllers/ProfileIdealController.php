<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Criteria;
use App\Models\SubCriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProfileIdealController extends Controller
{
    public function index()
    {
        $criterias = Criteria::with('sub_criteria')->get();

        if ($criterias->count() == 0) {
            flash()->error('Data kriteria tidak tersedia!');
            return redirect(route(Auth::user()->role . '.dashboard'));
        }
        return view('admin.profile_ideal', compact('criterias'));
    }

    public function post(Request $request)
    {
        try {
            $request->validate([
                'ideal' => 'required|array',
                'ideal.*' => 'required',
            ], [
                'ideal.required' => 'Profile ideal wajib diisi!',
                'ideal.array' => 'Profile ideal wajib diisi!',
                'ideal.*.required' => 'Profile ideal untuk kriteria :attribute wajib diisi!',
            ]);
            // dd($request->all());
            DB::beginTransaction();

            $processedID = [];
            foreach ($request->ideal as $ideal) {
                $processedID[] = $ideal;
                SubCriteria::where('id', $ideal)->update([
                    'profile_ideal' => true,
                ]);
            }

            SubCriteria::whereNotIn('id', $processedID)->update([
                'profile_ideal' => null,
            ]);
            DB::commit();

            flash()->success('Profil Ideal berhasil diperbarui.');
            return redirect()->back();
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $allErrors = collect($errors)->flatten()->implode('<br> • ');
            flash()->error('Inputan Gagal! Periksa kembali isian Anda. <br> • ' . $allErrors);
            return redirect()->back();
        } catch (Throwable $e) {
            DB::rollback();
            flash()->error('Inputan Gagal! Periksa kembali isian Anda. <br> ' . $e->getMessage());
            return redirect()->back();
        }
    }
}
