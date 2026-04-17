<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\MunicipalConfiguration;
use Illuminate\Support\Facades\Storage;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $municipalConfig = null;
        if (function_exists('tenant') && tenant()) {
            $config = MunicipalConfiguration::getSettings();
            $municipalConfig = [
                'official_name' => $config->official_name,
                'administration_period' => $config->administration_period,
                'primary_color' => $config->primary_color,
                'logo_url' => $config->logo_path ? tenant_asset($config->logo_path) : null,
                'shield_url' => $config->shield_path ? tenant_asset($config->shield_path) : null,
            ];
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? $request->user()->load('department', 'roles') : null,
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message')
            ],
            'municipal_config' => $municipalConfig,
        ];
    }
}
