<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Encryption\DecryptException;

class EnsureDecryptIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Ambil semua parameter yang ada di route (misal: id, criteria_id, dll)
        $parameters = $request->route()->parameters();

        foreach ($parameters as $name => $value) {
            // 2. Cek apakah nama parameter mengandung kata 'id' (case-insensitive)
            // Dan pastikan nilainya adalah string (bukan object)
            if (str_contains(strtolower($name), 'id') && is_string($value)) {
                try {
                    $decryptedValue = Crypt::decrypt($value);

                    // 3. Timpa nilai parameter yang terenkripsi dengan nilai asli
                    $request->route()->setParameter($name, $decryptedValue);
                } catch (DecryptException $e) {
                    // Jika gagal dekrip (berarti ID dimanipulasi), stop dan beri pesan
                    flash()->error('Akses ditolak! Token atau ID tidak valid.');
                    return redirect()->back();
                }
            }
        }

        return $next($request);
    }
}
