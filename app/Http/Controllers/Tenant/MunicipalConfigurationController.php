<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\MunicipalConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MunicipalConfigurationController extends Controller
{
    public function edit()
    {
        $settings = MunicipalConfiguration::getSettings();
        
        return Inertia::render('Premium/Configuration', [
            'settings' => $settings,
            'logo_url' => $settings->logo_path ? Storage::url($settings->logo_path) : null,
            'shield_url' => $settings->shield_path ? Storage::url($settings->shield_path) : null,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'official_name' => 'required|string|max:100',
            'administration_period' => 'nullable|string|max:50',
            'primary_color' => 'required|string|size:7',
            'logo' => 'nullable|image|mimes:jpeg,png|max:2048',
            'shield' => 'nullable|image|mimes:jpeg,png|max:2048',
        ]);

        $settings = MunicipalConfiguration::getSettings();

        $updateData = [
            'official_name' => $validated['official_name'],
            'administration_period' => $validated['administration_period'],
            'primary_color' => $validated['primary_color'],
        ];

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::delete($settings->logo_path);
            }
            $updateData['logo_path'] = $request->file('logo')->store('municipal_assets', 'public');
        }

        if ($request->hasFile('shield')) {
            if ($settings->shield_path) {
                Storage::delete($settings->shield_path);
            }
            $updateData['shield_path'] = $request->file('shield')->store('municipal_assets', 'public');
        }

        $settings->update($updateData);

        return redirect()->back()->with('message', 'Configuración actualizada exitosamente.');
    }
}
