<?php

namespace App\Http\Controllers\Admin;

use App\Support\Media\StoresUploadedMedia;
use App\Support\Audit\RecordsAdminAudit;
use App\Domain\Settings\Models\SiteSetting;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\UpdateSiteSettingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => $this->settings(),
        ]);
    }

    public function update(UpdateSiteSettingRequest $request): RedirectResponse
    {
        $settings = $this->settings();
        $before = $settings->attributesToArray();
        $validated = $request->validated();

        $settings->update(Arr::except($validated, ['primary_logo', 'secondary_logo']));

        if ($request->hasFile('primary_logo')) {
            $media = StoresUploadedMedia::store($settings, $request->file('primary_logo'), 'primary_logo');
            $settings->update(['primary_logo_path' => $media->path]);
        }

        if ($request->hasFile('secondary_logo')) {
            $media = StoresUploadedMedia::store($settings, $request->file('secondary_logo'), 'secondary_logo');
            $settings->update(['secondary_logo_path' => $media->path]);
        }

        $settings->refresh();

        RecordsAdminAudit::updated('settings', $settings, $request, $before);

        return redirect()->route('admin.settings.edit');
    }

    private function settings(): SiteSetting
    {
        return SiteSetting::query()->firstOrCreate([], [
            'site_name' => 'Veraguas United FC',
            'site_tagline' => null,
            'primary_logo_path' => null,
            'secondary_logo_path' => null,
            'primary_color' => null,
            'accent_color' => null,
            'contact_email' => null,
            'contact_phone' => null,
            'social_links' => [],
            'global_seo_title' => null,
            'global_seo_description' => null,
            'maintenance_mode' => false,
        ]);
    }
}
