<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMeetingLogRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;

class MeetingLogController extends Controller
{
    public function store(StoreMeetingLogRequest $request, Client $client): RedirectResponse
    {
        $client->meetingLogs()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('crm.clients.show', $client)->with('success', 'Meeting log added successfully.');
    }
}
