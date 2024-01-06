<?php

namespace App\Http\Controllers;

use App\Http\Requests\Reminder\IndexRequest;
use App\Models\Reminder;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    public function index(IndexRequest $request)
    {
        $filter = $request->validated();
        $limit = $filter['limit'] ?? 10;

        $data = Reminder::query()
            ->select('id', 'title', 'description', 'remind_at', 'event_at')
            ->where('user_id', Auth::id())
            ->whereNull('sent_at')
            ->orderBy('remind_at')
            ->limit($limit)
            ->get();

        return $this->success([
            'reminders' => $data,
            'limit' => $limit,
        ]);
    }
}
