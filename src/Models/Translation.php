<?php

namespace Ferdiunal\NovaTranslations\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\TranslationLoader\LanguageLine;

final class Translation extends LanguageLine
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'langs';

    protected $fillable = [
        'namespace',
        'group',
        'key',
        'text',
    ];

    public function getTranslation(string $locale, ?string $group = null): ?string
    {
        if ($group === '*' && ! isset($this->text[$locale])) {
            $fallback = config('app.fallback_locale');

            return $this->text[$fallback] ?? null;
        }

        return $this->text[$locale] ?? null;
    }
}
