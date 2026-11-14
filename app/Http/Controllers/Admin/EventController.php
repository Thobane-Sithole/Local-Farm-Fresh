<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::orderBy('starts_at')->paginate(20);

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        return view('admin.events.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:150'],
            'description'      => ['nullable', 'string', 'max:2000'],
            'location'         => ['required', 'string', 'max:200'],
            'starts_at'        => ['required', 'date', 'after:now'],
            'poster_url'       => ['nullable', 'url'],
            'poster_public_id' => ['nullable', 'string'],
        ]);

        Event::create($data);

        return redirect()->route('admin.events.index')->with('status', 'Event created.');
    }

    public function edit(Event $event): View
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:150'],
            'description'      => ['nullable', 'string', 'max:2000'],
            'location'         => ['required', 'string', 'max:200'],
            'starts_at'        => ['required', 'date'],
            'poster_url'       => ['nullable', 'url'],
            'poster_public_id' => ['nullable', 'string'],
        ]);

        $event->update($data);

        return redirect()->route('admin.events.index')->with('status', 'Event updated.');
    }

    public function destroy(Event $event, CloudinaryService $cloudinary): RedirectResponse
    {
        if ($event->poster_public_id) {
            $cloudinary->delete($event->poster_public_id);
        }

        $event->delete();

        return back()->with('status', 'Event deleted.');
    }
}
