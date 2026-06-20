# نشر Chronorex Express

## متطلبات السيرفر
- PHP 8.3+
- Composer
- MySQL 8.0+ (أو SQLite للتطوير)
- Cron (للأوامر المجدولة)

## خطوات النشر

```bash
# 1. نقل الملفات إلى السيرفر
# 2. تثبيت الاعتماديات
composer install --no-dev --optimize-autoloader

# 3. إعداد البيئة
cp .env.example .env
# عدّل .env بقاعدة البيانات والـ URL

# 4. إنشاء الـ APP_KEY
php artisan key:generate

# 5. تشغيل الهجرات والبذور
php artisan migrate --seed

# 6. ربط التخزين
php artisan storage:link

# 7. تحسين الأداء
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. ضبط الصلاحيات
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## الأوامر المجدولة (Cron)

أضف هذا السطر إلى crontab:

```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### المهام المجدولة حالياً:
| الأمر | التوقيت | الوصف |
|-------|---------|-------|
| `alerts:high-expenses` | يومياً 00:00 | فحص المصروفات العالية |
| `backup:database` | يومياً 02:00 | نسخ احتياطي لقاعدة البيانات |

## النسخ الاحتياطي
- المخزن: `storage/app/backups/`
- مدة الاحتفاظ: 30 يوماً
- للتشغيل اليدوي: `php artisan backup:database`
