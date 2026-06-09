@extends('layouts.admin.app')

@section('title', 'Site Settings')

@section('content')
    <section>
        <h2 style="margin-top: 0;">Site Settings</h2>
        <p>Phase C singleton settings form.</p>

        @if ($errors->any())
            <div style="margin-bottom: 1rem; padding: 1rem; border-radius: 0.75rem; background: #fee2e2; color: #991b1b;">
                Please fix the highlighted fields.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" style="display: grid; gap: 1rem;">
            @csrf
            @method('PUT')

            <label style="display: grid; gap: 0.35rem;">
                <span>Site name</span>
                <input type="text" name="site_name" value="{{ old('site_name', $settings->site_name) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Site tagline</span>
                <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings->site_tagline) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Primary logo path</span>
                <input type="text" name="primary_logo_path" value="{{ old('primary_logo_path', $settings->primary_logo_path) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Primary logo upload</span>
                <input type="file" name="primary_logo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Secondary logo path</span>
                <input type="text" name="secondary_logo_path" value="{{ old('secondary_logo_path', $settings->secondary_logo_path) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Secondary logo upload</span>
                <input type="file" name="secondary_logo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Primary color</span>
                <input type="text" name="primary_color" value="{{ old('primary_color', $settings->primary_color) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Accent color</span>
                <input type="text" name="accent_color" value="{{ old('accent_color', $settings->accent_color) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Contact email</span>
                <input type="email" name="contact_email" value="{{ old('contact_email', $settings->contact_email) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Contact phone</span>
                <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Facebook</span>
                <input type="url" name="social_links[facebook]" value="{{ old('social_links.facebook', data_get($settings->social_links, 'facebook')) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Instagram</span>
                <input type="url" name="social_links[instagram]" value="{{ old('social_links.instagram', data_get($settings->social_links, 'instagram')) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Global SEO title</span>
                <input type="text" name="global_seo_title" value="{{ old('global_seo_title', $settings->global_seo_title) }}">
            </label>

            <label style="display: grid; gap: 0.35rem;">
                <span>Global SEO description</span>
                <textarea name="global_seo_description" rows="4">{{ old('global_seo_description', $settings->global_seo_description) }}</textarea>
            </label>

            <fieldset style="border:1px solid #e2e8f0;border-radius:0.5rem;padding:1rem 1.25rem;display:grid;gap:0.75rem;">
                <legend style="font-weight:600;padding:0 0.5rem;">Videos hero (YouTube URLs)</legend>
                <small style="color:#64748b;margin-top:-0.25rem;">Pega la URL de YouTube de cada página. Deja en blanco para usar la imagen estática de fondo.</small>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Home</span>
                    <input type="url" name="hero_video_url" value="{{ old('hero_video_url', $settings->hero_video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                    @error('hero_video_url')<small style="color:#991b1b;">{{ $message }}</small>@enderror
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>FanFest</span>
                    <input type="url" name="fanfest_hero_video_url" value="{{ old('fanfest_hero_video_url', $settings->fanfest_hero_video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                    @error('fanfest_hero_video_url')<small style="color:#991b1b;">{{ $message }}</small>@enderror
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Expedición Indiana</span>
                    <input type="url" name="expedition_hero_video_url" value="{{ old('expedition_hero_video_url', $settings->expedition_hero_video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                    @error('expedition_hero_video_url')<small style="color:#991b1b;">{{ $message }}</small>@enderror
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Patrocinadores</span>
                    <input type="url" name="sponsors_hero_video_url" value="{{ old('sponsors_hero_video_url', $settings->sponsors_hero_video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                    @error('sponsors_hero_video_url')<small style="color:#991b1b;">{{ $message }}</small>@enderror
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Estadio</span>
                    <input type="url" name="stadium_hero_video_url" value="{{ old('stadium_hero_video_url', $settings->stadium_hero_video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                    @error('stadium_hero_video_url')<small style="color:#991b1b;">{{ $message }}</small>@enderror
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Fuerzas Básicas (Academia)</span>
                    <input type="url" name="academy_hero_video_url" value="{{ old('academy_hero_video_url', $settings->academy_hero_video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                    @error('academy_hero_video_url')<small style="color:#991b1b;">{{ $message }}</small>@enderror
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Plantilla</span>
                    <input type="url" name="squad_hero_video_url" value="{{ old('squad_hero_video_url', $settings->squad_hero_video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                    @error('squad_hero_video_url')<small style="color:#991b1b;">{{ $message }}</small>@enderror
                </label>

                <label style="display: grid; gap: 0.35rem;">
                    <span>Noticias</span>
                    <input type="url" name="news_hero_video_url" value="{{ old('news_hero_video_url', $settings->news_hero_video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                    @error('news_hero_video_url')<small style="color:#991b1b;">{{ $message }}</small>@enderror
                </label>
            </fieldset>

            <label style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="hidden" name="maintenance_mode" value="0">
                <input type="checkbox" name="maintenance_mode" value="1" @checked(old('maintenance_mode', $settings->maintenance_mode))>
                <span>Maintenance mode</span>
            </label>

            <div>
                <button type="submit" class="admin-button">Save settings</button>
            </div>
        </form>
    </section>
@endsection
