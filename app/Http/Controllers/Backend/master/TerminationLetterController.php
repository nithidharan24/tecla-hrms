<?php

namespace App\Http\Controllers\Backend\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use App\Models\GeneralSetting;
use App\Models\LogoSetting;

class TerminationLetterController extends Controller
{
    /**
     * Display a listing of the termination letter templates.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $templates = DB::table('termination_letter_templates')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return view('hrms.master.termination-letter.index', compact('templates'));
        } catch (\Exception $e) {
            Log::error('Error fetching termination letter templates: ' . $e->getMessage());
            return view('hrms.master.termination-letter.index', ['templates' => collect()])
                ->with('error', 'Failed to load templates. Please try again.');
        }
    }

    /**
     * Show the form for creating a new termination letter template.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('hrms.master.termination-letter.create');
    }

    /**
     * Store a newly created termination letter template in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:termination_letter_templates,name',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ], [
            'name.required' => 'Template name is required.',
            'name.unique' => 'A template with this name already exists.',
            'subject.required' => 'Subject is required.',
            'content.required' => 'Content is required.',
        ]);

        try {
            DB::table('termination_letter_templates')->insert([
                'name' => trim($request->name), // Trim whitespace
                'subject' => trim($request->subject), // Trim whitespace
                'content' => $request->content,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('termination-letter-templates.index')
                ->with('success', 'Termination letter template created successfully!');
                
        } catch (\Exception $e) {
            Log::error('Error creating termination letter template: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create template. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified termination letter template.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $template = DB::table('termination_letter_templates')->find($id);
            
            if (!$template) {
                return redirect()->route('termination-letter-templates.index')
                    ->with('error', 'Template not found.');
            }

            return view('hrms.master.termination-letter.edit', compact('template'));
            
        } catch (\Exception $e) {
            Log::error('Error fetching termination letter template for edit: ' . $e->getMessage());
            return redirect()->route('termination-letter-templates.index')
                ->with('error', 'Failed to load template for editing.');
        }
    }

    /**
     * Update the specified termination letter template in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:termination_letter_templates,name,' . $id,
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ], [
            'name.required' => 'Template name is required.',
            'name.unique' => 'A template with this name already exists.',
            'subject.required' => 'Subject is required.',
            'content.required' => 'Content is required.',
        ]);

        try {
            $updated = DB::table('termination_letter_templates')
                ->where('id', $id)
                ->update([
                    'name' => trim($request->name), // Trim whitespace
                    'subject' => trim($request->subject), // Trim whitespace
                    'content' => $request->content,
                    'updated_at' => now(),
                ]);

            if ($updated) {
                return redirect()->route('termination-letter-templates.index')
                    ->with('success', 'Termination letter template updated successfully!');
            } else {
                // If no rows were affected, it means the template wasn't found or no changes were made
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Template not found or no changes were made.');
            }
            
        } catch (\Exception $e) {
            Log::error('Error updating termination letter template: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update template. Please try again.');
        }
    }

    /**
     * Remove the specified termination letter template from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $template = DB::table('termination_letter_templates')->find($id);
            
            if (!$template) {
                return redirect()->route('termination-letter-templates.index')
                    ->with('error', 'Template not found.');
            }

            $deleted = DB::table('termination_letter_templates')->where('id', $id)->delete();
            
            if ($deleted) {
                return redirect()->route('termination-letter-templates.index')
                    ->with('success', 'Termination letter template deleted successfully!');
            } else {
                return redirect()->route('termination-letter-templates.index')
                    ->with('error', 'Failed to delete template. Please try again.');
            }
            
        } catch (\Exception $e) {
            Log::error('Error deleting termination letter template: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete template. Please try again.');
        }
    }

    /**
     * Get template for preview or other purposes
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $template = DB::table('termination_letter_templates')->find($id);
            
            if (!$template) {
                return response()->json(['error' => 'Template not found'], 404);
            }

            return response()->json(['template' => $template]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching termination letter template: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch template'], 500);
        }
    }

    /**
     * Preview the termination letter template.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function preview($id)
    {
        try {
            $template = DB::table('termination_letter_templates')->find($id);
            
            if (!$template) {
                return redirect()->route('termination-letter-templates.index')
                    ->with('error', 'Template not found.');
            }

            $settings = $this->getCompanySettings();

            // Dummy employee & termination for preview
            $employee = (object)[
                'firstname' => 'Jane',
                'lastname' => 'Doe',
                'employeeid' => 'EMP002',
                'email' => 'jane.doe@example.com',
                'phone' => '+1234567890',
                'joiningdate' => '01-01-2022',
                'department_name' => 'Sales',
                'designation_name' => 'Sales Executive',
                'profile_image' => 'default.png',
            ];

            $termination = (object)[
                'termination_type' => 'Resignation',
                'termination_date' => now()->addDays(15)->format('d-m-Y'),
                'notice_date' => now()->format('d-m-Y'),
                'reason' => 'Better Opportunity',
            ];

            $data = array_merge($settings, $this->getLetterSignatureData('termination_letter'), compact('employee', 'termination'));
            $data['emailSubject'] = $template->subject;

            if (!empty($settings['logo'])) {
                $data['logoPath'] = public_path(ltrim($settings['logo'], '/'));
                $data['logo'] = $settings['logo'];
            }

            // Convert admin-safe placeholders to Blade placeholders before rendering
            $content = str_replace('@{{', '{{', $template->content);

            $html = Blade::render($content, $data);

            return response($html)->header('Content-Type', 'text/html');

        } catch (\Exception $e) {
            Log::error('Error previewing termination letter template: ' . $e->getMessage());
            return redirect()->route('termination-letter-templates.index')
                ->with('error', 'Failed to preview template: ' . $e->getMessage());
        }
    }

    private function getCompanySettings(): array
    {
        $generalSettings = GeneralSetting::first();
        $logoSetting = LogoSetting::first();

        $logo = null;

        if ($logoSetting && !empty($logoSetting->logo)) {
            $logo = $logoSetting->logo;
        }

        if ($logo && !file_exists(public_path($logo))) {
            $possiblePaths = [
                $logo,
                'uploads/' . $logo,
                'storage/' . $logo,
                'images/' . $logo,
                'assets/images/' . $logo,
                'logo/' . $logo,
            ];

            foreach ($possiblePaths as $path) {
                if (file_exists(public_path($path))) {
                    $logo = $path;
                    break;
                }
            }

            if (!file_exists(public_path($logo))) {
                $logo = null;
            }
        }

        return [
            'companyName'    => $generalSettings->site_name ?? 'TECLA MEDIA',
            'companyEmail'   => $generalSettings->contact_email ?? '',
            'companyPhone'   => $generalSettings->contact_phone ?? '',
            'companyAddress' => $generalSettings->contact_address ?? '',
            'gm_name'        => $generalSettings->gm_name ?? 'A.SURENDER',
            'gm_title'       => $generalSettings->gm_title ?? 'GENERAL MANAGER',
            'logo'           => $logo,
        ];
    }

    private function getLetterSignatureData(string $letterType): array
    {
        $signatureData = [
            'offerSignaturePath' => null,
            'offerSignaturePublicPath' => null,
            'offerSignatureDataUri' => null,
        ];

        if (!Schema::hasTable('letter_signatures')) {
            return $signatureData;
        }

        $signature = DB::table('letter_signatures')
            ->where('letter_type', $letterType)
            ->first();

        if (!$signature || empty($signature->signature_path)) {
            return $signatureData;
        }

        $publicPath = public_path($signature->signature_path);

        if (!File::exists($publicPath)) {
            return $signatureData;
        }

        $mimeType = $signature->mime_type ?: File::mimeType($publicPath);

        return [
            'offerSignaturePath' => $signature->signature_path,
            'offerSignaturePublicPath' => $publicPath,
            'offerSignatureDataUri' => 'data:' . $mimeType . ';base64,' . base64_encode(File::get($publicPath)),
        ];
    }
}
