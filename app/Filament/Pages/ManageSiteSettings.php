<?php

namespace App\Filament\Pages;

use App\Filament\Support\Upload;
use App\Models\SiteSetting;
use App\Support\Icon;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

/**
 * Pengaturan Situs (PRD A2): satu halaman berisi tab Profil, Section, Kontak, File, SEO & Footer.
 * Setiap field disimpan sebagai satu baris di tabel site_settings (key → value).
 */
class ManageSiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Pengaturan Situs';

    protected static ?string $title = 'Pengaturan Situs';

    protected static ?string $slug = 'pengaturan-situs';

    protected static ?int $navigationSort = 1;

    /** Kolom file: file lama dihapus dari disk saat diganti. */
    protected const FILE_KEYS = ['cv_path', 'og_image'];

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::allCached();

        $this->form->fill(collect($this->settingKeys())
            ->mapWithKeys(fn (string $key) => [$key => $settings[$key] ?? null])
            ->all());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Pengaturan')
                    ->persistTabInQueryString()
                    ->tabs([
                        $this->profileTab(),
                        $this->sectionTab(),
                        $this->contactTab(),
                        $this->fileTab(),
                        $this->seoTab(),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            $this->getFormContentComponent(),
        ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make([
                    Action::make('save')
                        ->label('Simpan pengaturan')
                        ->submit('save')
                        ->keyBindings(['mod+s']),
                ])->sticky(),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $old = SiteSetting::allCached();

        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value);
        }

        foreach (self::FILE_KEYS as $key) {
            $previous = $old[$key] ?? null;

            if (filled($previous) && $previous !== ($data[$key] ?? null)) {
                Storage::disk('uploads')->delete($previous);
            }
        }

        Notification::make()
            ->title('Pengaturan tersimpan')
            ->success()
            ->send();
    }

    /**
     * Semua key yang dikelola halaman ini. Harus sama dengan nama field di form.
     *
     * @return array<int, string>
     */
    protected function settingKeys(): array
    {
        return [
            'display_name', 'full_name', 'role', 'city', 'hero_greeting', 'hero_tagline',
            'hero_description', 'hero_scroll_text', 'hero_tech_icons',
            'pit_wall_title', 'pit_wall_intro', 'race_title', 'race_intro',
            'garage_title', 'garage_intro', 'contact_title', 'contact_intro',
            'contact_email', 'contact_whatsapp', 'contact_linkedin', 'contact_github',
            'contact_instagram', 'contact_quote',
            'cv_path', 'og_image',
            'meta_title', 'meta_description', 'footer_text', 'not_found_title', 'not_found_text',
        ];
    }

    protected function profileTab(): Tab
    {
        return Tab::make('Profil')
            ->icon(Heroicon::OutlinedUser)
            ->columns(2)
            ->schema([
                TextInput::make('display_name')
                    ->label('Nama tampil')
                    ->helperText('Nama yang muncul besar di hero, misalnya "Dun".')
                    ->required()
                    ->maxLength(50),
                TextInput::make('full_name')
                    ->label('Nama lengkap')
                    ->helperText('Untuk SEO dan footer.')
                    ->required()
                    ->maxLength(100),
                TextInput::make('role')
                    ->label('Peran singkat')
                    ->placeholder('Information Systems Student & AI Builder')
                    ->maxLength(100),
                TextInput::make('city')
                    ->label('Kota')
                    ->placeholder('Yogyakarta, Indonesia')
                    ->maxLength(100),
                TextInput::make('hero_greeting')
                    ->label('Sapaan hero')
                    ->required()
                    ->maxLength(100),
                TextInput::make('hero_scroll_text')
                    ->label('Teks ajakan scroll')
                    ->maxLength(100),
                Textarea::make('hero_tagline')
                    ->label('Tagline')
                    ->helperText('1–2 baris, boleh lucu.')
                    ->rows(2)
                    ->maxLength(200)
                    ->columnSpanFull(),
                Textarea::make('hero_description')
                    ->label('Deskripsi hero')
                    ->helperText('1–2 kalimat.')
                    ->rows(3)
                    ->maxLength(400)
                    ->columnSpanFull(),
                Repeater::make('hero_tech_icons')
                    ->label('Ikon tech di hero')
                    ->helperText('Disarankan 4–6 item. Urutan bisa digeser.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->placeholder('Laravel')
                            ->required()
                            ->maxLength(40),
                        TextInput::make('icon')
                            ->label('Nama ikon (opsional)')
                            ->helperText('Nama ikon garis dari tabler.io/icons, misalnya "database" atau "code". Ikon logo merek (brand-…) tidak diizinkan. Kosongkan untuk teks saja.')
                            ->maxLength(60)
                            ->rule(fn () => function (string $attribute, mixed $value, Closure $fail) {
                                if (filled($value) && str_starts_with($value, 'brand-')) {
                                    $fail('Ikon logo merek tidak diizinkan (design.md: dilarang memakai logo merek asli). Pakai ikon garis biasa atau kosongkan.');
                                } elseif (filled($value) && ! Icon::exists($value)) {
                                    $fail('Ikon "'.$value.'" tidak ditemukan. Cek ejaan namanya di tabler.io/icons.');
                                }
                            }),
                    ])
                    ->columns(2)
                    ->maxItems(8)
                    ->addActionLabel('Tambah ikon')
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ]);
    }

    protected function sectionTab(): Tab
    {
        $section = fn (string $title, string $prefix, string $hint) => Section::make($title)
            ->description($hint)
            ->columns(2)
            ->schema([
                TextInput::make("{$prefix}_title")
                    ->label('Judul')
                    ->required()
                    ->maxLength(100),
                Textarea::make("{$prefix}_intro")
                    ->label('Intro')
                    ->rows(2)
                    ->maxLength(300),
            ]);

        return Tab::make('Section')
            ->icon(Heroicon::OutlinedSquares2x2)
            ->schema([
                $section('Pit Wall', 'pit_wall', 'Section About (bento grid 4 kartu).'),
                $section('Race Progress', 'race', 'Section timeline perjalanan.'),
                $section('Garage', 'garage', 'Section daftar project.'),
                $section('Box, Box', 'contact', 'Section kontak & form.'),
            ]);
    }

    protected function contactTab(): Tab
    {
        return Tab::make('Kontak')
            ->icon(Heroicon::OutlinedChatBubbleLeftRight)
            ->columns(2)
            ->schema([
                TextInput::make('contact_email')
                    ->label('Email')
                    ->helperText('Juga dipakai sebagai tujuan notifikasi pesan masuk.')
                    ->email()
                    ->maxLength(150),
                TextInput::make('contact_whatsapp')
                    ->label('WhatsApp')
                    ->helperText('Format 62…, tanpa + atau spasi. Contoh: 6281234567890.')
                    ->regex('/^62\d{8,13}$/')
                    ->validationMessages(['regex' => 'Nomor WhatsApp harus diawali 62 dan hanya berisi angka.'])
                    ->maxLength(16),
                TextInput::make('contact_linkedin')
                    ->label('LinkedIn (URL)')
                    ->url()
                    ->maxLength(255),
                TextInput::make('contact_github')
                    ->label('GitHub (URL)')
                    ->url()
                    ->maxLength(255),
                TextInput::make('contact_instagram')
                    ->label('Instagram (URL, opsional)')
                    ->url()
                    ->maxLength(255),
                Textarea::make('contact_quote')
                    ->label('Quote kecil')
                    ->rows(2)
                    ->maxLength(200),
            ]);
    }

    protected function fileTab(): Tab
    {
        return Tab::make('File')
            ->icon(Heroicon::OutlinedDocumentArrowUp)
            ->columns(2)
            ->schema([
                Upload::pdf('cv_path', 'files')
                    ->label('CV (PDF)')
                    ->helperText('Format PDF, maksimal 5 MB. Tombol "Download CV" hanya muncul jika file ini ada.')
                    ->downloadable(),
                Upload::image('og_image', 'seo')
                    ->label('OG image default')
                    ->helperText('Gambar saat link dibagikan di WhatsApp/LinkedIn. Ideal 1200×630 px, JPG/PNG/WebP, maks. 2 MB.'),
            ]);
    }

    protected function seoTab(): Tab
    {
        return Tab::make('SEO & Footer')
            ->icon(Heroicon::OutlinedMagnifyingGlass)
            ->columns(2)
            ->schema([
                TextInput::make('meta_title')
                    ->label('Meta title')
                    ->helperText('Judul di tab browser & hasil Google. Ideal ≤ 60 karakter.')
                    ->required()
                    ->maxLength(70),
                Textarea::make('meta_description')
                    ->label('Meta description')
                    ->helperText('1 kalimat untuk hasil Google. Ideal ≤ 160 karakter.')
                    ->rows(2)
                    ->maxLength(200),
                TextInput::make('footer_text')
                    ->label('Teks footer')
                    ->maxLength(200),
                TextInput::make('not_found_title')
                    ->label('Judul halaman 404')
                    ->maxLength(100),
                Textarea::make('not_found_text')
                    ->label('Teks halaman 404')
                    ->rows(2)
                    ->maxLength(300)
                    ->columnSpanFull(),
            ]);
    }
}
