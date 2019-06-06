sudo chmod -R 777 storage
php artisan make:model Models/Invoice -a

use Illuminate\Support\Facades\Schema;
public function boot()
{
    Schema::defaultStringLength(191);
}

php artisan make:resource Invoice