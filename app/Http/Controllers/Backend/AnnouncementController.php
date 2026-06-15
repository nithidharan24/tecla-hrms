<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $active  = Announcement::active()->latest()->get();
        $expired = Announcement::where('expires_at', '<=', now())->latest()->limit(30)->get();
        $typeIcons = Announcement::$typeIcons;

        return view('hrms.admin.announcements.index', compact('active', 'expired', 'typeIcons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'    => 'required|string|max:50',
            'message' => 'required|string|max:300',
        ]);

        $data['icon']      = Announcement::$typeIcons[$data['type']] ?? 'fa-bullhorn';
        $data['posted_by'] = Auth::user()->name ?? 'Admin';
        $data['expires_at'] = now()->addHours(24);

        Announcement::create($data);

        return back()->with('success', 'Announcement posted. It will show for 24 hours.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement removed.');
    }
}
