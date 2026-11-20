<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $locale = $request->session()->get('locale', 'en');
        App::setLocale($locale);
        $settings = SiteSetting::query()->pluck('value', 'key');
        $brand = array_merge(config('golden-jaggery'), array_filter([
            'name' => $settings->get('brand_name'),
            'tagline' => $settings->get('tagline'),
            'location' => $settings->get('location'),
            'phone' => $settings->get('phone'),
            'email' => $settings->get('email'),
            'whatsapp_number' => $settings->get('whatsapp_number'),
            'bank_transfer_instructions' => $settings->get('bank_transfer_instructions'),
            'whatsapp_enabled' => $settings->has('whatsapp_enabled') ? filter_var($settings->get('whatsapp_enabled'), FILTER_VALIDATE_BOOL) : null,
        ], fn ($value) => $value !== null && $value !== ''));

        return [
            ...parent::share($request),
            'brand' => $brand,
            'siteContent' => [
                'hero_title' => $settings->get('hero_title'),
                'hero_subtitle' => $settings->get('hero_subtitle'),
                'story_title' => $settings->get('story_title'),
                'story_text' => $settings->get('story_text'),
            ],
            'locale' => $locale,
            'cartCount' => collect($request->session()->get('cart', []))->sum(),
            'flash' => [
                'success' => $request->session()->get('success'),
            ],
        ];
    }
}
