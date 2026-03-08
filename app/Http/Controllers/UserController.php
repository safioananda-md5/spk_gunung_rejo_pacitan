<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        $userEdit = [];
        foreach ($users as $user) {
            $userEdit[$user->id] = [
                'NAME' => $user->name,
                'EMAIL' => $user->email,
                'ROLE' => $user->role,
                'NORT' => $user->rt,
                'NORW' => $user->rw,
            ];
        }
        return view('admin.user', compact(['users', 'userEdit']));
    }

    public function store(Request $request)
    {
        try {
            $request->validate(
                [
                    'name' => 'required',
                    'email' => 'required|email',
                    'role' => 'required',
                    'password' => 'required',
                ],
                [
                    'name.required' => 'Nama User wajib diisi.',
                    'email.required' => 'Email User wajib diisi.',
                    'email.email' => 'Format Email tidak sesuai.',
                    'role.required' => 'Role User wajib diisi.',
                    'password.required' => 'Password User wajib diisi.',
                ]
            );
            DB::beginTransaction();
            if ($request->role == 'rt') {
                if (isset($request->rt) && isset($request->rw)) {
                    User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'email_verified_at' => Carbon::now(),
                        'password' => $request->password,
                        'role' => $request->role,
                        'rt' => $request->rt,
                        'rw' => $request->rw,
                    ]);
                } else {
                    throw new Exception('Nomor RT & RW wajib diisi.');
                }
            } else {
                User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'email_verified_at' => Carbon::now(),
                    'password' => $request->password,
                    'role' => $request->role,
                ]);
            }

            DB::commit();
            flash()->success('Data user berhasil tambahkan.');
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

    public function edit(Request $request)
    {
        try {
            $request->validate(
                [
                    'id_edit' => 'required',
                    'name_edit' => 'required',
                    'email_edit' => 'required|email',
                    'role_edit' => 'required',
                ],
                [
                    'id_edit.required' => 'User tidak ditemukan.',
                    'name_edit.required' => 'Nama User wajib diisi.',
                    'email_edit.required' => 'Email User wajib diisi.',
                    'email_edit.email' => 'Format Email tidak sesuai.',
                    'role_edit.required' => 'Role User wajib diisi.',
                ]
            );

            $user = User::where('id', $request->id_edit)->firstOrFail();
            DB::beginTransaction();
            if ($request->role_edit == 'rt') {
                if (isset($request->rt_edit) && isset($request->rw_edit)) {
                    $user->update([
                        'name' => $request->name_edit,
                        'email' => $request->email_edit,
                        'email_verified_at' => Carbon::now(),
                        'role' => $request->role_edit,
                        'rt' => $request->rt_edit,
                        'rw' => $request->rw_edit,
                    ]);
                } else {
                    throw new Exception('Nomor RT & RW wajib diisi.');
                }
            } else {
                $user->update([
                    'name' => $request->name_edit,
                    'email' => $request->email_edit,
                    'email_verified_at' => Carbon::now(),
                    'role' => $request->role_edit,
                ]);
            }

            if (isset($request->password_edit)) {
                $user->update([
                    'password' => $request->password_edit,
                ]);
            }
            DB::commit();
            flash()->success('Data user berhasil diperbarui.');
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
