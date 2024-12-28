# Cómo Manejar Distintos Modelos de Usuario en Laravel con Sanctum 

Cuando se trabaja con aplicaciones complejas es común que se presenten distintos tipos de usuarios con distinta naturaleza. Separar esos tipos de usuarios en distintos modelos puede ser útil para distribuir de una mejor forma la responsabilidades. El desafío es lograr que se autentiquen con éxito.


# Migracion y modelo
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

> Nota: No hace falta de que tenga la misma estructura de users, este usuario puede tener otras credenciales de autenticacion y otro tipo de informacion.

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
