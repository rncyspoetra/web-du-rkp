<?php

namespace App\Filament\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\Component;
use Filament\Pages\Auth\Login as AuthLogin;

class Login extends AuthLogin
{
  public function mount(): void
  {
    parent::mount();

    if (app()->environment('local')) {
      $this->form->fill([
        'name' => 'username',
        'password' => '',
        'remember' => true,
      ]);
    }
  }

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        $this->getLoginFormComponent(),
        $this->getPasswordFormComponent(),
        $this->getRememberFormComponent(),
      ])
      ->statePath('data');
  }

  protected function getLoginFormComponent(): Component
  {
    return TextInput::make('login')
      ->label('Username')
      ->required()
      ->autocomplete()
      ->autofocus();
  }

  protected function getCredentialsFromFormData(array $data): array
  {
    $login_type = 'name';

    return [
      $login_type => $data['login'],
      'password' => $data['password'],
    ];
  }
}
