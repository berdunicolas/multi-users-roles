
## Crear tabla de customers

``` console
php artisan make:migration create_customers_table

```


## Editar migracion customer

``` php
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

```

## correr migracion

```
php artisan migrate
```

## Define el modelo Customer

``` php
class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];
```

# Configurar sanctum

## Configurar guards 
En el archivo `config/auth.php` agrega los guards personalizados para customer, y define customers en providers.

``` php
'guards' => [
    'user' => [
        'driver' => 'sanctum',
        'provider' => 'users',
    ],

    'customer' => [
        'driver' => 'sanctum',
        'provider' => 'customers',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],

    'customers' => [
        'driver' => 'eloquent',
        'model' => App\Models\Customer::class,
    ],
],
```


## Crear Middleware 
Middleware personalizado de sanctum para las rutas
```
php artisan make:middleware SanctumGuardMiddleware
```

## Editar Middleware
``` php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\Guard;

class SanctumGuardMiddleware
{
    public function handle(Request $request, Closure $next, $guard)
    {
        // Cambia dinámicamente el guard
        config(['sanctum.guard' => $guard]);

        // Usa el middleware estándar de Sanctum
        return app(\Illuminate\Auth\Middleware\Authenticate::class)
            ->handle($request, $next, 'sanctum');
    }
}
```
