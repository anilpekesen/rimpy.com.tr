<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Panelde yapılan her kalıcı değişikliği kaydeder. Bir içerik kaybolduğunda
 * ya da beklenmedik bir değişiklik olduğunda "kim, ne zaman, neyi" sorusunun
 * cevabı buradan okunur.
 */
class ActivityLogger
{
    public static function log(
        string $event,
        ?Model $subject = null,
        ?string $description = null,
        array $properties = [],
    ): void {
        // Kayıt tutmak asıl işi asla engellememeli.
        try {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'event' => $event,
                'subject_type' => $subject ? $subject::class : null,
                'subject_id' => $subject?->getKey(),
                'description' => $description,
                'properties' => $properties ?: null,
                'ip_address' => request()->ip(),
                'user_agent' => str(request()->userAgent())->limit(500)->toString(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function created(Model $subject, ?string $description = null): void
    {
        self::log('created', $subject, $description);
    }

    public static function updated(Model $subject, ?string $description = null): void
    {
        // Yalnızca gerçekten değişen alanları sakla; eski/yeni değer çiftiyle.
        $changes = [];

        foreach ($subject->getChanges() as $key => $new) {
            if (in_array($key, ['updated_at', 'password', 'remember_token', 'two_factor_secret'], true)) {
                continue;
            }

            $changes[$key] = [
                'from' => self::compact($subject->getOriginal($key)),
                'to' => self::compact($new),
            ];
        }

        self::log('updated', $subject, $description, $changes ? ['changes' => $changes] : []);
    }

    public static function deleted(Model $subject, ?string $description = null): void
    {
        self::log('deleted', $subject, $description);
    }

    /** Uzun metinleri kayıt tablosunu şişirmeden özetler. */
    private static function compact(mixed $value): mixed
    {
        if (is_string($value) && mb_strlen($value) > 200) {
            return mb_substr($value, 0, 200).'…';
        }

        if (is_array($value)) {
            return '['.count($value).' öğe]';
        }

        return $value;
    }

    /** Yapılandırmadaki saklama süresini aşan kayıtları siler. */
    public static function prune(): int
    {
        $days = (int) config('cms.security.activity_log_days', 180);

        return ActivityLog::where('created_at', '<', now()->subDays($days))->delete();
    }
}
