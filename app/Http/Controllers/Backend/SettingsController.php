<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\EmailConfiguration;
use App\Models\GeneralSetting;
use App\Models\LogoSetting;
use App\Models\PusherSetting;
    use App\Models\VideoSetting;
use Illuminate\Support\Facades\Storage;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Session;

class SettingsController extends Controller
{
    use ImageUploadTrait;



    public function index()
{
    $generalSettings = GeneralSetting::first();
    $emailSettings = EmailConfiguration::first();
    $logoSetting = LogoSetting::first();
    $pusherSetting = PusherSetting::first();
    
    return view('admin.settings.index', compact(
        'generalSettings', 
        'emailSettings', 
        'logoSetting', 
        'pusherSetting'
    ));
}

public function generalSettingUpdate(Request $request)
{
    $request->validate([
        'site_name'       => 'required|string|max:255',
        'contact_email'   => 'required|email|max:255',
        'contact_phone'   => 'required|string|max:20',
        'contact_address' => 'required|string|max:255',
        'map'             => 'nullable|url|max:500',
        'currency_icon'   => 'nullable|string|max:10',
    ]);

    $existing = GeneralSetting::firstOrNew(['id' => 1]);

    $data = [
        'site_name'       => $request->site_name,
        'sub_site_name'   => $request->sub_site_name,
        'contact_email'   => $request->contact_email,
        'contact_phone'   => $request->contact_phone,
        'contact_address' => $request->contact_address,
        'map'             => $request->map,
        'working_hours'   => $request->working_hours,
        'currency_name'   => $request->currency_name,
        'currency_icon'   => $request->currency_icon,
    ];

    $changed = false;
    foreach ($data as $key => $value) {
        if ($existing->$key !== $value) {
            $existing->$key = $value;
            $changed = true;
        }
    }

    if ($changed) {
        $existing->save();
        Session::flash('messageType', 'success');
        Session::flash('message', 'Settings updated successfully!');
    } else {
        Session::flash('messageType', 'info');
        Session::flash('message', 'No changes detected.');
    }

    return redirect()->back();
}

    

    public function logoSettingUpdate(Request $request)
    {
        $request->validate([
            'logo' => ['image', 'max:3000'],
           
        ]);

        $logoPath = $this->updateImage($request, 'logo', 'uploads', $request->old_logo);
     
        LogoSetting::updateOrCreate(
            ['id' => 1],
            [
                'logo' =>  (!empty($logoPath)) ? $logoPath : $request->old_logo,
            ]
        );

        Session::flash('messageType', 'success');
        Session::flash('message', 'Updated Successfully!');

        return redirect()->back();
    }

    /** Pusher settings update */
    function pusherSettingUpdate(Request $request) : RedirectResponse {
        $validatedData = $request->validate([
            'pusher_app_id' => ['required'],
            'pusher_key' => ['required'],
            'pusher_secret' => ['required'],
            'pusher_cluster' => ['required'],
        ]);

        PusherSetting::updateOrCreate(
            ['id' => 1],
            $validatedData
        );

        Session::flash('messageType', 'success');
        Session::flash('message', 'Updated Successfully!');

        return redirect()->back();
    }


// Add this method to your SettingsController

public function videoSettingUpdate(Request $request)
{
    $request->validate([
        'video' => ['nullable', 'mimetypes:video/mp4,video/quicktime,video/x-msvideo', 'max:512000'],
        'fallback_image' => ['nullable', 'image', 'max:3000'],
        'remove_video' => ['nullable', 'boolean'],
    ]);

    $videoSetting = VideoSetting::firstOrNew(['id' => 1]);

    // Handle explicit video removal
    if ($request->has('remove_video') && $request->remove_video) {
        if ($videoSetting->video_path) {
            $this->deleteVideo($videoSetting->video_path);
            $videoSetting->video_path = null;
        }
    }
    // Handle video upload
    elseif ($request->hasFile('video')) {
        // Delete old video if exists
        if ($videoSetting->video_path) {
            $this->deleteVideo($videoSetting->video_path);
        }
        
        $videoFile = $request->file('video');
        $videoName = time() . '_' . $videoFile->getClientOriginalName();
        $videoPath = 'event/video/' . $videoName;
        
        // Create directory if it doesn't exist
        if (!file_exists(public_path('event/video'))) {
            mkdir(public_path('event/video'), 0777, true);
        }
        
        $videoFile->move(public_path('event/video'), $videoName);
        $videoSetting->video_path = $videoPath;
    }
    
    // Handle fallback image upload
    if ($request->hasFile('fallback_image')) {
        // Delete old image if exists
        if ($videoSetting->fallback_image_path) {
            $this->deleteImage($videoSetting->fallback_image_path);
        }
        
        $imagePath = $this->updateImage($request, 'fallback_image', 'uploads', $videoSetting->fallback_image_path);
        $videoSetting->fallback_image_path = $imagePath;
    }
    
    $videoSetting->is_active = $request->has('is_active');
    $videoSetting->save();

    Session::flash('messageType', 'success');
    Session::flash('message', 'Video settings updated successfully!');

    return redirect()->back();
}

protected function deleteImage($path)
{
    if ($path && file_exists(public_path($path))) {
        unlink(public_path($path));
    }
}

protected function deleteVideo($path)
{
    if ($path && file_exists(public_path($path))) {
        unlink(public_path($path));
    }
}
public function emailConfigSettingUpdate(Request $request)
{
    $request->validate([
        'email' => ['required', 'email'],
        'host' => ['required', 'max:200'],
        'username' => ['required', 'max:200'],
        'password' => ['required', 'max:200'],
        'port' => ['required', 'max:200'],
        'encryption' => ['required', 'max:200'],
    ]);

    EmailConfiguration::updateOrCreate(
        ['id' => 1],
        [
            'email' => $request->email,
            'host' => $request->host,
            'username' => $request->username,
            'password' => $request->password,
            'port' => $request->port,
            'encryption' => $request->encryption,
        ]
    );

    // Update .env file
    $this->updateEnvFile($request);

    Session::flash('messageType', 'success');
    Session::flash('message', 'Email configuration updated successfully!');

    return redirect()->back();
}

protected function updateEnvFile($request)
{
    $envPath = base_path('.env');
    
    if (file_exists($envPath)) {
        $envContent = file_get_contents($envPath);
        
        $updates = [
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => $request->host,
            'MAIL_PORT' => $request->port,
            'MAIL_USERNAME' => $request->username,
            'MAIL_PASSWORD' => $request->password,
            'MAIL_ENCRYPTION' => $request->encryption,
            'MAIL_FROM_ADDRESS' => $request->email,
            'MAIL_FROM_NAME' => config('app.name', 'Your Site Name'),
        ];
        
        foreach ($updates as $key => $value) {
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $envContent
            );
        }
        
        file_put_contents($envPath, $envContent);
        
        // Clear config cache
        Artisan::call('config:clear');
    }
}
}