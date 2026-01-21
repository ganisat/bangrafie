<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PengaturanController extends Controller
{
    public function index()
    {
        $defaults = [
            'target_harian' => 0,
            'target_bulanan' => 0,
            'default_margin_pct' => 20,
            'default_hpp_mode' => 'manual', // manual | per_produk (placeholder)
            'default_min_stok' => 10,
        ];

        $settings = $this->getSettings($defaults);

        $users = User::query()
            ->select('id','name','email','created_at')
            ->latest()
            ->get();

        return view('pages.pengaturan', compact('settings','users'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'target_harian' => 'nullable|integer|min:0',
            'target_bulanan' => 'nullable|integer|min:0',
            'default_margin_pct' => 'nullable|integer|min:0|max:100',
            'default_hpp_mode' => 'nullable|in:manual,per_produk',
            'default_min_stok' => 'nullable|integer|min:0|max:100000',
        ]);

        foreach ($validated as $key => $val) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => (string)($val ?? '')]
            );
        }

        return redirect()->route('pengaturan')->with('ok', 'Pengaturan berhasil disimpan.');
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'email' => 'required|email|max:120|unique:users,email',
            'password' => 'required|string|min:6|max:60',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('pengaturan')->with('ok', 'User toko berhasil ditambahkan.');
    }

    private function getSettings(array $defaults): array
    {
        $rows = Setting::query()
            ->whereIn('key', array_keys($defaults))
            ->get()
            ->pluck('value', 'key')
            ->toArray();

        $out = [];
        foreach ($defaults as $k => $def) {
            $out[$k] = array_key_exists($k, $rows) && $rows[$k] !== '' ? $rows[$k] : $def;
        }
        return $out;
    }
}
