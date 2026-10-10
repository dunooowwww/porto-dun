<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            // Login saja; tidak ada registrasi publik. Akun dibuat lewat `php artisan make:filament-user`.
            // Halaman login Filament sudah punya rate limit bawaan (5 percobaan per menit).
            ->login()
            ->brandName('Dun #12')
            ->favicon(asset('images/mascot-head.png'))
            // Palet merah manual: shade 600 = #E10600 (red) dan 700 = #B80500 (red-dark) dari design.md.
            // Color::hex() menghasilkan merah yang terlalu pucat sehingga tombol tampil salmon.
            ->colors([
                'primary' => [
                    50 => '#fff1f0',
                    100 => '#ffe0dd',
                    200 => '#ffc6c0',
                    300 => '#ff9d93',
                    400 => '#ff6455',
                    500 => '#f8321f',
                    600 => '#e10600',
                    700 => '#b80500',
                    800 => '#970b03',
                    900 => '#7c1009',
                    950 => '#450300',
                ],
            ])
            // Inter versi lokal bawaan Filament (tanpa CDN).
            ->font('Inter Variable', provider: LocalFontProvider::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->navigationGroups([
                'Konten',
                'Pengaturan',
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
