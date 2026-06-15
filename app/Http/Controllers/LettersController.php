<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LettersController extends Controller
{
    public function index()
    {
        $letterTypes = $this->letterTypes();
        $signatures = collect();

        if (Schema::hasTable('letter_signatures')) {
            $signatures = DB::table('letter_signatures')
                ->whereIn('letter_type', array_keys($letterTypes))
                ->get()
                ->keyBy('letter_type');
        }

        return view('hrms.master.letters.index', compact('letterTypes', 'signatures'));
    }

    public function storeSignature(Request $request)
    {
        if (!Schema::hasTable('letter_signatures')) {
            return redirect()
                ->route('letters.index')
                ->with('error', 'Please run the latest migrations before uploading signatures.');
        }

        $letterTypeKeys = implode(',', array_keys($this->letterTypes()));

        $request->validate([
            'letter_type' => 'required|in:' . $letterTypeKeys,
            'signature_image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $existingSignature = DB::table('letter_signatures')
            ->where('letter_type', $request->letter_type)
            ->first();

        $targetDirectory = 'uploads/letter-signatures';
        $publicPath = public_path($targetDirectory);

        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }

        $file = $request->file('signature_image');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $fileSize = $file->getSize();
        $fileName = Str::slug($request->letter_type) . '-' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $targetDirectory . '/' . $fileName;

        $file->move($publicPath, $fileName);

        if ($existingSignature && $existingSignature->signature_path && File::exists(public_path($existingSignature->signature_path))) {
            File::delete(public_path($existingSignature->signature_path));
        }

        DB::table('letter_signatures')->updateOrInsert(
            ['letter_type' => $request->letter_type],
            [
                'signature_path' => $filePath,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'updated_at' => now(),
                'created_at' => $existingSignature->created_at ?? now(),
            ]
        );

        return redirect()
            ->route('letters.index')
            ->with('success', $this->letterTypes()[$request->letter_type] . ' signature saved successfully.');
    }

    private function letterTypes(): array
    {
        return [
            'offer_letter' => 'Offer Letter',
            'promotion_letter' => 'Promotion Letter',
            'termination_letter' => 'Termination Letter',
            'hike_letter' => 'Hike Letter',
            'appointment_letter' => 'Appointment Letter',
            'memo_letter' => 'Memo Letter',
            'invoice_template' => 'Invoice Template',
            'payroll_template' => 'Payroll Template',
        ];
    }
}
