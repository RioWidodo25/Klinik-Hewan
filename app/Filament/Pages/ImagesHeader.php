<?php

namespace App\Filament\Pages;

use App\Models\FooterSetting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ImagesHeader extends Page
{
    use \Filament\Forms\Concerns\InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static string $view = 'filament.pages.images-header';

    protected static ?string $navigationLabel = 'Images Header';

    protected static ?string $navigationGroup = 'Website Settings';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $settings = FooterSetting::getSettings();

        $this->form->fill([
            'blog_header_image' => $settings->blog_header_image,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Blog Header Image')
                    ->description('Gambar header untuk halaman blog')
                    ->schema([
                        FileUpload::make('blog_header_image')
                            ->label('Gambar Header Blog')
                            ->disk('public')
                            ->directory('blog/header')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '21:9',
                                '3:1',
                            ])
                            ->maxSize(2048)
                            ->helperText('Upload gambar untuk header halaman blog. Rekomendasi: 1920x600px. Max: 2MB')
                            ->columnSpanFull(),
                    ]),

                Section::make('Gallery Header Image')
                    ->description('Gambar header untuk halaman gallery (Coming Soon)')
                    ->schema([
                        FileUpload::make('gallery_header_image')
                            ->label('Gambar Header Gallery')
                            ->disk('public')
                            ->directory('gallery/header')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '21:9',
                                '3:1',
                            ])
                            ->maxSize(2048)
                            ->helperText('Upload gambar untuk header halaman gallery. Rekomendasi: 1920x600px. Max: 2MB')
                            ->columnSpanFull()
                            ->disabled(),
                    ]),

                Section::make('Petshop Header Image')
                    ->description('Gambar header untuk halaman petshop (Coming Soon)')
                    ->schema([
                        FileUpload::make('petshop_header_image')
                            ->label('Gambar Header Petshop')
                            ->disk('public')
                            ->directory('petshop/header')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '21:9',
                                '3:1',
                            ])
                            ->maxSize(2048)
                            ->helperText('Upload gambar untuk header halaman petshop. Rekomendasi: 1920x600px. Max: 2MB')
                            ->columnSpanFull()
                            ->disabled(),
                    ]),
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
            ->title('Images Header berhasil disimpan')
            ->success()
            ->send();
    }
}
