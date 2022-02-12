<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\App;
use App\Models\Payment;

use Illuminate\Support\Facades\URL;

if (env('APP_ENV') === 'production') {
    URL::forceScheme('https');
}

/*
|--------------------------------------------------------------------------
| Add/Replace config .env
|--------------------------------------------------------------------------

APP_URL=https://domain
APP_ENV=production

|--------------------------------------------------------------------------
| Add/Replace config .env
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Locale //
    /*
    Route::get('/lang/{locale}', function ($locale) {
        if (! in_array($locale, ['en', 'ru'])) {
            abort(400);
        }
        App::setLocale($locale);
        return redirect()->route('home');
    });
    */
    use App\Http\Controllers\Service\LangController;
    Route::get('/lang/{locale}', [LangController::class, 'lang'])->name('lang');
    /*
    Route::get('/lang/{locale}', function ($locale) {
        //if (! in_array($locale, ['en', 'ru'])) {
        //    abort(400);
        //}
        //App::setLocale($locale);
        //Cookie::queue('lang', $locale);
        //return Cookie::get('lang');
        //return redirect()->back();
        //return redirect()->back()->withCookie(Cookie::make('lang', $locale, 100));
        //return redirect('/')->withCookie(Cookie::make('lang', $locale, 100));
    })->name('lang');
    */
    /*
    Route::get('/lang/{locale}', function ($locale) {
        if (! in_array($locale, ['en', 'ru'])) {
            abort(400);
        }
        Session::put('locale', $locale);
        return redirect()->back();
    })->name('lang');
    */
// Locale //
// Pages //
    //Route::middleware([App\Http\Middleware\HtmlMifier::class])->group(function () {
        Route::get('/', function () {
            return view('page/home');
        })->name('home');
        Route::get('/service', function () {
            return view('page/service');
        })->name('service');
        Route::get('/help', function () {
            return view('page/help');
        })->name('help');
        Route::get('/birtrhday', function () {
            return view('page/birtrhday');
        })->name('birtrhday');
        Route::get('/conditions', function () {
            return view('page/conditions');
        })->name('conditions');
        Route::get('/calendar', function () {
            return view('calendar/calendar');
        })->name('calendar');
        Route::view('/calendarlist', 'calendar.calendarlist')->name('calendarlist');
        Route::get('/contact', function () {
            return view('page/contact');
        })->name('contact');
    //});
// Pages //
// Search //
// Search //
// Cashe Clean //
    //Route::resource('/clean', [Service\CasheController::class, 'index'])->name('clean');
    use App\Http\Controllers\Service\CasheController;
    Route::get('/clean', [CasheController::class, 'clean'])->name('clean');
    use App\Http\Controllers\Service\KeyController;
    Route::get('/key', [KeyController::class, 'key'])->name('key');
    use App\Http\Controllers\Service\LinkController;
    Route::get('/link', [LinkController::class, 'link'])->name('link');
// Cashe Clean //
// Redirect Login //
    /*
    Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    */
    Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
        return redirect('/events');
    })->name('dashboard');
// Redirect Login //
// VerificationLogin //
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');

    use Illuminate\Foundation\Auth\EmailVerificationRequest;
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/');
    })->middleware(['auth', 'signed'])->name('verification.verify');

    use Illuminate\Http\Request;
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    use App\Models\Invoice;
    use App\Notifications\InvoicePaid;
    Route::get('/notification', function () {
        $invoice = Invoice::find(1);
    
        return (new InvoicePaid($invoice))
                    ->toMail($invoice->user);
    });
// VerificationLogin //
// Event //
    // Event List //
    Route::view('/list', 'activities.list')->name('list');
    // Event List //
    // Event Detail //
    use App\Http\Controllers\User\EventController;
    Route::get('/event/view/{link}', [EventController::class, 'view'])->name('view');
    // Event Detail //
    // Activities Detail //
    use App\Http\Controllers\User\ActivitieController;
    Route::get('/activities/{id}', [ActivitieController::class, 'view'])->name('view');
    // Activities Detail //
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        // Static //
        //use App\Http\Controllers\User\EventController;
        //use App\Http\Controllers\User\GiftController;
        //use App\Http\Controllers\User\GuestController;
        Route::resource('event', User\EventController::class);
        Route::resource('gift', User\GiftController::class);
        Route::resource('guest', User\GuestController::class);
        // Static //
        // Ajax //
        //Route::resource('events.index', User\EventController::class);
        Route::view('/events', 'events.index')->name('personal-events');
        Route::view('/activities', 'activities.index')->name('personal-activities');
        Route::view('/agreement', 'admin.agreement')->name('agreement');
        // Ajax //
    });
// Event //
// System //
Route::get('/storage', function () { redirect('/'); })->name('storage');
Route::get('/storage/upload')->name('upload');
use App\Http\Controllers\LoaderController;
Route::post('/load', [LoaderController::class, 'ajax']);
use App\Http\Controllers\CookieController;
Route::post('/cookie', [CookieController::class, 'index']);
//Route::post('/load', [App\Http\Controllers\LoaderController::class, 'ajax']);
//Route::post('/cookie', [App\Http\Controllers\CookieController::class, 'index']);
// System //
