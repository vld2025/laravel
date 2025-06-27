<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class EditProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    
    protected static string $view = 'filament.pages.edit-profile';
    
    protected static bool $shouldRegisterNavigation = false;
    
    protected static ?string $title = 'Modifica Profilo';
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'avatar' => auth()->user()->avatar,
            'telefono' => auth()->user()->telefono,
            'indirizzo' => auth()->user()->indirizzo,
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informazioni Personali')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->image()
                            ->directory('avatars')
                            ->imageEditor()
                            ->circleCropper()
                            ->maxSize(10240)
                            ->label('Foto Profilo')
                            ->helperText('Clicca per cambiare la tua foto profilo'),
                            
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Nome'),
                            
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->disabled()
                            ->label('Email'),
                            
                        Forms\Components\TextInput::make('telefono')
                            ->tel()
                            ->label('Telefono'),
                            
                        Forms\Components\Textarea::make('indirizzo')
                            ->rows(3)
                            ->label('Indirizzo'),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Cambia Password')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->password()
                            ->label('Password Attuale')
                            ->helperText('Lascia vuoto se non vuoi cambiare la password'),
                            
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->label('Nuova Password')
                            ->confirmed(),
                            
                        Forms\Components\TextInput::make('password_confirmation')
                            ->password()
                            ->label('Conferma Password'),
                    ])->columns(1),
            ])
            ->statePath('data');
    }
    
    public function save(): void
    {
        $data = $this->form->getState();
        
        $user = auth()->user();
        
        // Verifica password attuale se sta cercando di cambiarla
        if (!empty($data['password'])) {
            if (!Hash::check($data['current_password'] ?? '', $user->password)) {
                Notification::make()
                    ->title('Password attuale non corretta')
                    ->danger()
                    ->send();
                return;
            }
            
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        unset($data['current_password']);
        unset($data['password_confirmation']);
        
        $user->update($data);
        
        Notification::make()
            ->title('Profilo aggiornato con successo')
            ->success()
            ->send();
    }
}
