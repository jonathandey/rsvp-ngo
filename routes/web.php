<?php

use Illuminate\Support\Facades\Route;

Route::get('event/{event:public_key}/ical', function (\App\Models\Event $event) {
    $icalEvent = \Spatie\IcalendarGenerator\Components\Event::create($event->name);

    $url = $event->getPublicUrl();

    if (request()->query('host_key') && request()->query('host_key') === $event->host_key) {
        $url = $event->getHostUrl();
    }

    $icalEvent->url($url);

    $description = $url;

    if ($event->description) {
        $description = $event->description . "\n\n" . $description;
    }

    if ($event->hasStartDayTime()) {
        $icalEvent->startsAt($event->startDateTime()->toDateTime());
        $icalEvent->endsAt($event->endDateTime()->toDateTime());
    } else {
        $icalEvent->fullDay();
    }

    $icalEvent->description($description);

    $cal = \Spatie\IcalendarGenerator\Components\Calendar::create($event->name)
        ->refreshInterval(60)
        ->appendProperty('GENERATOR', 'www.rsvp.ngo')
        ->event($icalEvent);

    $tmpFilename = tempnam(sys_get_temp_dir(), 'rsvp');
    file_put_contents($tmpFilename, $cal->toString());

    return response()->file(
        $tmpFilename,
        [
            'content-type' => 'text/Calendar',
        ]
    );

})->name('event.ical');

// Route::view('/', 'welcome');
//
// // Route::view('dashboard', 'dashboard')
// //     ->middleware(['auth', 'verified'])
// //     ->name('dashboard');
// //
// // Route::view('profile', 'profile')
// //     ->middleware(['auth'])
// //     ->name('profile');
// //
// // require __DIR__.'/auth.php';
