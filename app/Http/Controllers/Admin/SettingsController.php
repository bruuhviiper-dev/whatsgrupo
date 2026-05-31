<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'adsense_enabled'   => Setting::get('adsense_enabled', '0'),
            'adsense_client_id' => Setting::get('adsense_client_id'),
            'adsense_script'    => Setting::get('adsense_script'),
            'adsense_meta_tag'  => Setting::get('adsense_meta_tag'),
            'adsense_slot_auto' => Setting::get('adsense_slot_auto'),
            'favicon'           => Setting::get('favicon'),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'adsense_client_id' => 'nullable|string|max:100',
            'adsense_script'    => 'nullable|string',
            'adsense_meta_tag'  => 'nullable|string',
            'adsense_slot_auto' => 'nullable|string|max:50',
            'favicon'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:2048',
        ]);

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('settings', 'public');
            \App\Models\Setting::set('favicon', $path);
        }

        Setting::set('adsense_enabled',   $request->boolean('adsense_enabled') ? '1' : '0');
        Setting::set('adsense_client_id', $request->input('adsense_client_id'));
        Setting::set('adsense_script',    $request->input('adsense_script'));
        Setting::set('adsense_meta_tag',  $request->input('adsense_meta_tag'));
        Setting::set('adsense_slot_auto', $request->input('adsense_slot_auto'));

        return redirect()->route('admin.settings')->with('success', 'Configurações salvas com sucesso!');
    }

    public function downloadAdsTxt()
    {
        $clientId = Setting::get('adsense_client_id', 'ca-pub-XXXXXXXXXXXXXXXX');
        $content = "google.com, {$clientId}, DIRECT, f08c47fec0942fa0\n";

        return response($content, 200, [
            'Content-Type'        => 'text/plain',
            'Content-Disposition' => 'attachment; filename="ads.txt"',
        ]);
    }
}
