<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

# laravel crud api example 

It consist some common operations like create, edit , delete, update 
with implementation of image upload and update  

## Create project
```
$ composer create-project --prefer-dist laravel/laravel crud-api
```
## API routes
```
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('category', 'CategoryController');
Route::apiResource('product', 'ProductController');
```
## Create Model With migration and factory
```
$ php artisan make:model Category -mf
$ php artisan make:model Product -mf
```
## Category Model
```
<?php

namespace App;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    // change created_at & updated_at format using Carbon
    public function getCreatedAtAttribute($attr)
    {
        return Carbon::parse($attr)->format('d.m.Y H:i');
    }

    public function getUpdatedAtAttribute($attr)
    {
        return Carbon::parse($attr)->format('d.m.Y H:i');
    }
    // Relationship one to many
    public function product()
    {
    	return $this->hasMany('App\Product');
    }
}

```
## Product Model
```
namespace App;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name','price','category_id'];

    public function getCreatedAtAttribute($attr)
    {
        return Carbon::parse($attr)->format('d.m.Y H:i');
    }

    public function getUpdatedAtAttribute($attr)
    {
        return Carbon::parse($attr)->format('d.m.Y H:i');
    }
    //One To Many (Inverse)
    public function category()
    {
    	return $this->belongsTo('App\Category');
    }
}

```
## Create Categories Table
```
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');	
            $table->timestamps();
        });
    }
```
## Create Products Table with foreign key
```
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('image');	
            $table->string('name');	
            $table->double('price');	
            $table->timestamps();
            $table->bigInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }
```

```
$ php artisan migrate
```
## Database seeding with ‘faker’
```
$factory->define(Category::class, function (Faker $faker) {
    return [
        "name" => $faker->word
    ];
});

```
```

use Illuminate\Database\Seeder;
use App\Category;

class CategoryTableSeeder extends Seeder
{
    public function run()
    {
        factory(Category::Class, 10)->create();
    }
}

```
```
$ php artisan db:seed
```
## Create Resources
```
$ php artisan make:resource CategoryResource
$ php artisan make:resource CategoryResourceCollection --collection

$ php artisan make:resource ProductResource
$ php artisan make:resource ProductResourceCollection --collection
```
## Create Controllers
```
$ php artisan make:controller CategoryController

$ php artisan make:controller ProductController
```
## Category Controller
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryResourceCollection;

class CategoryController extends Controller
{
    
    /*
    *   @return CategoryResource
    */
    public function show(Category $category): CategoryResource{
        return new CategoryResource($category);
    }
    /*
    *   @return CategoryResourceCollection
    */
    public function index(): CategoryResourceCollection{
        $category = Category::orderBy('id', 'desc')->get();
        return new CategoryResourceCollection($category);
    }
    /*
    *   @return CategoryResource
    */
    public function store(Request $request): CategoryResource{

        $request->validate(['name' => 'required']);
        $category = Category::create($request->all());
        return(new CategoryResource($category));
    }
    /*
    *   @return CategoryResource
    */
    public function update(Category $category, Request $request): CategoryResource{
        $category->update($request->all());
        return(new CategoryResource($category));
    }
    /*
    *   @return 
    */
    public function destroy(Category $category){
        $category->delete();
		return response()->json();
    }
}


```
## Product Controller
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductResourceCollection;
use App\Product;

class ProductController extends Controller
{
    
    /*
    *   @return ProductResource
    */
    public function show(Product $product): ProductResource{
        return new ProductResource($product);
    }
    /*
    *   @return ProductResourceCollection
    */
    public function index(): ProductResourceCollection{
        $product = Product::with('category')->orderBy('id', 'desc')->get();
        return new ProductResourceCollection($product);
    }
    /*
    *   @return ProductResource
    */
    public function store(Product $product, Request $request): ProductResource{

        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'category_id' => 'required'
        ]);
        
        //
        if($request->hasFile('image'))
        {
            $image_name = time().'_'.rand(999,9999).'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $image_name);
            $product->image = 'http://localhost/crud-api/public/images/'.$image_name;

        }else{     

            $product->image = 'http://localhost/crud-api/public/images/image-placeholder.png';
            
        }
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->save();

        return(new ProductResource($product));
    }
    /*
    *   @return ProductResource METHOD POST with Param _method PUT
        https://url/id?_method=put
    */
    public function update(Product $product, Request $request): ProductResource{

        if($request->hasFile('image'))
        {
            $image_name = time().'_'.rand(999,9999).'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $image_name);
            $product->image = 'http://localhost/crud-api/public/images/'.$image_name;
        }
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->save();

        return(new ProductResource($product));

    }
    /*
    *   @return 
    */
    public function destroy(Product $product){
        $product->delete();
		return response()->json();
    }
}

```
# Send Telegram Notification
First create a telegram bot. To do this, send a message to [@BotFather](https://telegram.me/botfather).

## Telegram Bot API packages
```
$ composer require irazasyed/telegram-bot-sdk

```
## Create notification controller
```
$ php artisan make:controller SendNotification
```
```
<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Telegram\Bot\Laravel\Facades\Telegram;

class SendNotification extends Controller
{
    //
    public function ToTelegram(Request $request){
                
        Telegram::sendMessage([
            'chat_id' => env('TELEGRAM_CHANNEL_ID', '***************'),
            'parse_mode' => 'HTML',
            'text' => $request->text,
        ]);
    }
   
}

```



Add entry to 'config/app.php'
```
 'Telegram' => Telegram\Bot\Laravel\Facades\Telegram::class,

```
Publish configuration file
```
php artisan vendor:publish --provider="Telegram\Bot\Laravel\TelegramServiceProvider"

```
Add telegram token in 'config/telegram.php'

```
    'bots' => [
        'common' => [
            'username'  => 'username',
            'token' => env('TELEGRAM_BOT_TOKEN', 'telegram token'),
            'commands' => [
//                Acme\Project\Commands\MyTelegramBot\BotCommand::class
            ],
        ],
```
## Send Mail
```
use Illuminate\Support\Facades\Mail;
....

 public function SendMail(Request $request){

        $data = [
          "email" =>  $request->email,
          "msg" =>  $request->message
        ];
        Mail::send([], [], function($message) use ($data) {
          $message->from('example', 'name');
          $message->to($data['email']);
          $message->subject('New Message');
          $message->setBody($data['message'], 'text/html');
        });

    }
```

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[British Software Development](https://www.britishsoftware.co)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- [UserInsights](https://userinsights.com)
- [Fragrantica](https://www.fragrantica.com)
- [SOFTonSOFA](https://softonsofa.com/)
- [User10](https://user10.com)
- [Soumettre.fr](https://soumettre.fr/)
- [CodeBrisk](https://codebrisk.com)
- [1Forge](https://1forge.com)
- [TECPRESSO](https://tecpresso.co.jp/)
- [Runtime Converter](http://runtimeconverter.com/)
- [WebL'Agence](https://weblagence.com/)
- [Invoice Ninja](https://www.invoiceninja.com)
- [iMi digital](https://www.imi-digital.de/)
- [Earthlink](https://www.earthlink.ro/)
- [Steadfast Collective](https://steadfastcollective.com/)
- [We Are The Robots Inc.](https://watr.mx/)
- [Understand.io](https://www.understand.io/)
- [Abdel Elrafa](https://abdelelrafa.com)
- [Hyper Host](https://hyper.host)
- [Appoly](https://www.appoly.co.uk)
- [OP.GG](https://op.gg)

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

