<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use App\Filament\Support\Upload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Info utama')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(120)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                // Isi slug otomatis selama slug masih kosong / masih mengikuti judul lama.
                                if (blank($get('slug')) || $get('slug') === Str::slug((string) $old)) {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label('Slug (alamat URL)')
                            ->helperText(fn (?string $state) => 'Otomatis dari judul, bisa diedit. URL: /projects/'.($state ?: '…'))
                            ->required()
                            ->alphaDash()
                            ->maxLength(140)
                            ->unique(ignoreRecord: true),
                        Textarea::make('excerpt')
                            ->label('Deskripsi singkat')
                            ->helperText('1 kalimat, tampil di daftar Garage.')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        TextInput::make('year')
                            ->label('Tahun')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100),
                        TagsInput::make('tech_stack')
                            ->label('Tech stack')
                            ->placeholder('Ketik lalu tekan Enter')
                            ->helperText('Contoh: Laravel, MySQL, Python.')
                            ->reorderable(),
                    ]),
                Section::make('Publikasi')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(ProjectStatus::class)
                            ->default(ProjectStatus::Draft)
                            ->helperText('Hanya project "Terbit" yang tampil di website.')
                            ->required()
                            ->native(false),
                        Toggle::make('is_featured')
                            ->label('Unggulan')
                            ->default(false),
                        TextInput::make('github_url')
                            ->label('Link GitHub')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('demo_url')
                            ->label('Link demo')
                            ->url()
                            ->maxLength(255),
                    ]),
                Section::make('Case study')
                    ->description('Urutan di halaman detail: Masalah → Peran & proses → Hasil.')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('problem')
                            ->label('Masalah yang diselesaikan')
                            ->rows(3),
                        Textarea::make('role')
                            ->label('Peran')
                            ->placeholder('Backend developer & project lead (tim 4 orang)')
                            ->rows(2),
                        RichEditor::make('process')
                            ->label('Proses')
                            ->toolbarButtons([
                                ['bold', 'italic', 'link'],
                                ['h2', 'h3'],
                                ['bulletList', 'orderedList', 'blockquote', 'codeBlock'],
                                ['undo', 'redo'],
                            ]),
                        Textarea::make('result')
                            ->label('Hasil')
                            ->helperText('Boleh pakai angka, tapi hanya yang benar-benar terukur.')
                            ->rows(3),
                    ]),
                Section::make('Gambar')
                    ->columnSpanFull()
                    ->schema([
                        Upload::image('thumbnail', 'projects')
                            ->label('Thumbnail'),
                        Repeater::make('images')
                            ->label('Galeri screenshot')
                            ->helperText('Geser untuk mengatur urutan.')
                            ->relationship()
                            ->orderColumn('sort')
                            ->schema([
                                Upload::image('path', 'projects/gallery')
                                    ->label('Gambar')
                                    ->required(),
                                TextInput::make('caption')
                                    ->label('Keterangan (opsional)')
                                    ->maxLength(150),
                            ])
                            ->columns(2)
                            ->grid(2)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah gambar')
                            ->collapsible()
                            ->itemLabel(fn (array $state) => $state['caption'] ?? null),
                    ]),
            ]);
    }
}
