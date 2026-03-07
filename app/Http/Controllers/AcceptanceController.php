<?php

namespace App\Http\Controllers;

use Throwable;
use Carbon\Carbon;
use App\Models\Alternative;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CriteriaAlternative;
use Illuminate\Validation\ValidationException;

class AcceptanceController extends Controller
{
    public function index()
    {
        $uniqueDates = Alternative::onlyTrashed()
            ->get()
            ->groupBy(function ($item) {
                return $item->deleted_at->translatedFormat('d F Y,H:i:s') . '|' . $item->description;
            });

        return view('admin.penerimaan', compact(['uniqueDates']));
    }

    public function detail($date)
    {
        $uniqueDates = Alternative::onlyTrashed()
            ->get()
            ->groupBy(function ($item) {
                return $item->deleted_at->translatedFormat('d F Y,H:i:s') . '|' . $item->description;
            });

        $penerimaan = [];
        $formatted = '';
        foreach ($uniqueDates as $dateIndex => $items) {
            $part = explode('|', $dateIndex);
            if (Str::slug($part[0]) == $date) {
                $formatted = $dateIndex;
                foreach ($items as $alternative) {
                    $penerimaan[] = [
                        'id' => $alternative->id,
                        'name' => $alternative->name,
                        'value' => $alternative->value,
                    ];
                }
            }
        }
        usort($penerimaan, function ($a, $b) {
            $valCompare = $b['value'] <=> $a['value'];
            if ($valCompare === 0) {
                return $a['name'] <=> $b['name'];
            }

            return $valCompare;
        });

        foreach ($penerimaan as $index => &$item) {
            $item['rank'] = $index + 1;
        }
        return view('admin.penerimaan_detail', compact(['uniqueDates', 'date', 'formatted', 'penerimaan']));
    }

    public function delete(Request $request)
    {
        try {
            $request->validate(
                [
                    'id' => 'required',
                ],
                [
                    'id.required' => 'Data penerimaan tidak ditemukan!',
                ]
            );

            // dd($request->all());
            DB::beginTransaction();
            CriteriaAlternative::where('alternative_id', $request->id)->withTrashed()->forceDelete();
            Alternative::where('id', $request->id)->withTrashed()->forceDelete();
            DB::commit();
            flash()->success('Data penerimaan berhasil dihapus.');
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

    public function deleteall(Request $request)
    {
        try {
            $request->validate(
                [
                    'id' => 'required',
                ],
                [
                    'id.required' => 'Data penerimaan tidak ditemukan!',
                ]
            );

            $tanggal = Carbon::createFromLocaleFormat('d F Y,H:i:s', 'id', $request->id);

            // Sekarang kita ubah ke format yang diinginkan
            $output = $tanggal->format('Y-m-d H:i:s');
            DB::beginTransaction();
            $ALternatives = Alternative::where('deleted_at', $output)->withTrashed()->get();
            foreach ($ALternatives as $AL) {
                CriteriaAlternative::where('alternative_id', $AL->id)->withTrashed()->forceDelete();
                $AL->forceDelete();
            }
            DB::commit();
            flash()->success('Data penerimaan berhasil dihapus.');
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
