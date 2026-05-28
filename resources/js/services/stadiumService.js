import apiClient from '@/services/apiClient';

const stadiumService = {
    getStadium: () => apiClient.get('/stadium'),
};

export default stadiumService;

export function normalizeStadium(data) {
    const meta = data.metadata ?? {};

    return {
        hero: {
            title:       meta.hero_title       ?? 'ESTADIO',
            highlight:   meta.hero_highlight   ?? data.name?.toUpperCase() ?? 'ATALAYA',
            description: meta.hero_description ?? '',
            imageUrl:    data.hero_image_path  ?? 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=1600&q=80',
        },
        info: {
            name:        data.name       ?? '',
            subtitle:    data.subtitle   ?? '',
            location:    data.location   ?? '',
            capacity:    data.capacity   ?? '',
            address:     data.address    ?? '',
            venueType:   data.venue_type ?? '',
            actionLabel: meta.info_action_label ?? 'CÓMO LLEGAR',
            actionHref:  meta.info_action_href  ?? 'https://maps.google.com',
        },
        map: {
            title:       meta.map_title        ?? 'Ubicación del estadio',
            description: meta.map_description  ?? '',
            pinLabel:    meta.map_pin_label    ?? (data.name ?? 'ESTADIO').toUpperCase(),
            actionLabel: meta.map_action_label ?? 'ABRIR EN GOOGLE MAPS',
            actionHref:  meta.map_action_href  ?? 'https://maps.google.com',
            embedUrl:    data.map_embed_url    ?? null,
        },
        zones:    data.zones    ?? [],
        matchday: data.matchday ?? [],
        rules:    data.rules    ?? [],
        cta: {
            title:       meta.cta_title        ?? '',
            description: meta.cta_description  ?? '',
            actionLabel: meta.cta_action_label ?? 'COMPRAR BOLETOS',
            actionHref:  meta.cta_action_href  ?? '/boletos',
        },
    };
}
