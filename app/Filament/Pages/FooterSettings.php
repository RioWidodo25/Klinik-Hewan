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
                            ->helperText('Teks yang akan ditampilkan di bagian About di footer'),
                    ]),

                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('contact_phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->helperText(''),

                        TextInput::make('contact_email')
                            ->label('Email')
                            ->email(),

                        Textarea::make('contact_address')
                            ->label('Alamat')
                            ->rows(3),

                        TextInput::make('whatsapp_number')
                            ->label('Nomor WhatsApp (Floating Button)')
                            ->tel()
                            ->default('+62')
                            ->helperText('Format: (858xxx) tanpa spasi')
                            ->maxLength(15),
                    ])
                    ->columns(2),

                Section::make('Social Media Links')
                    ->schema([
                        TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->url(),

                        TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url(),

                        TextInput::make('tiktok_url')
                            ->label('TikTok')
                            ->url(),

                        TextInput::make('youtube_url')
                            ->label('YouTube')
                            ->url(),
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
