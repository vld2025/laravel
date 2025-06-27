<?php
namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'telefono',
        'indirizzo',
        'taglia_giacca',
        'taglia_pantaloni',
        'taglia_maglietta',
        'taglia_scarpe',
        'note_abbigliamento',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // Metodi helper per i ruoli
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }
    
    public function isUser(): bool
    {
        return $this->role === 'user';
    }
    
    public function canDeleteSensitiveData(): bool
    {
        return $this->isAdmin();
    }
    
    public function canViewAllData(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }
    
    // Metodi per Avatar
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }
        
        // Se non c'è avatar, ritorna null (useremo le iniziali)
        return '';
    }
    
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        return substr($initials, 0, 2); // Max 2 caratteri
    }
    
    public function getAvatarColorAttribute(): string
    {
        // Genera un colore basato sul nome
        $colors = [
            'bg-red-500',
            'bg-blue-500',
            'bg-green-500',
            'bg-yellow-500',
            'bg-purple-500',
            'bg-pink-500',
            'bg-indigo-500',
        ];
        
        $index = crc32($this->name) % count($colors);
        return $colors[$index];
    }
}
