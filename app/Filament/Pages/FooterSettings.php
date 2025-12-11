<?php

namespace App\Filament\Pages;

use App\Models\FooterSetting;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class FooterSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static string $view = 'filament.pages.footer-settings';

    protected static ?string $navigationLabel = 'Footer Settings';

    protected static ?string $navigationGroup = 'Website Settings';

    protected static ?int $navigationSort = 5;

    public ?array $data = [];

    public function mount(): void
    {
        $settings = FooterSetting::getSettings();

        $this->form->fill([
            'about_text' => $settings->about_text,
            'contact_phone' => $settings->contact_phone,
            'contact_email' => $settings->contact_email,
            'contact_address' => $settings->contact_address,
            'google_maps_iframe' => $settings->google_maps_iframe,
            'instagram_url' => $settings->instagram_url,
            'facebook_url' => $settings->facebook_url,
            'tiktok_url' => $settings->tiktok_url,
            'youtube_url' => $settings->youtube_url,
            'whatsapp_number' => $settings->whatsapp_number,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('About')
                    ->schema([
                        Textarea::make('about_text')
                            ->label('Tentang Kami')
                            ->rows(4)
                            ->maxLength(500)
                            ->placeholder('Masukkan deskripsi singkat tentang klinik hewan...')
                            ->helperText('Teks yang akan ditampilkan di bagian About di footer'),
                    ]),

                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('contact_phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->placeholder('+62 812-3456-7890')
                            ->helperText(''),

                        TextInput::make('contact_email')
                            ->label('Email')
                            ->email()
                            ->placeholder('info@klinikhewan.com'),

                        Textarea::make('contact_address')
                            ->label('Alamat')
                            ->rows(3)
                            ->placeholder('Jl. Contoh No. 123, Kota, Provinsi 12345'),

                        TextInput::make('whatsapp_number')
                            ->label('Nomor WhatsApp (Floating Button)')
                            ->tel()
                            ->placeholder('628123456789')
                            ->helperText('Format: +628...')
                            ->maxLength(15),

                        Textarea::make('google_maps_iframe')
                            ->label('Google Maps Embed Code')
                            ->rows(5)
                            ->placeholder('<iframe src="https://www.google.com/maps/embed?pb=..." width="300" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Social Media Links')
                    ->schema([
                        TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->placeholder('https://instagram.com/username')
                            ->helperText('Link lengkap ke profil Instagram'),

                        TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->placeholder('https://facebook.com/pagename')
                            ->helperText('Link lengkap ke halaman Facebook'),

                        TextInput::make('tiktok_url')
                            ->label('TikTok')
                            ->url()
                            ->placeholder('https://tiktok.com/@username')
                            ->helperText('Link lengkap ke profil TikTok'),

                        TextInput::make('youtube_url')
                            ->label('YouTube')
                            ->url()
                            ->placeholder('https://youtube.com/@channelname')
                            ->helperText('Link lengkap ke channel YouTube'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = FooterSetting::first();

        if ($settings) {
            $settings->update($data);
        } else {
            FooterSetting::create($data);
        }

        Notification::make()
            ->title('Footer settings berhasil disimpan')
            ->success()
            ->send();
    }
}
