<?php

namespace App\Http\Controllers\backend\master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CustomizesiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customizations = $this->getAllCustomizations();
        return view('hrms.master.customize-site.index', compact('customizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hrms.master.customize-site.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:site_customizations,key',
            'value' => 'nullable|string',
            'type' => 'required|in:text,image,json',
            'description' => 'nullable|string|max:500',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {
            $value = $request->value;
            
            // Handle file upload for logo
            if ($request->hasFile('logo_file')) {
                $value = $this->uploadLogo($request->file('logo_file'));
            }

            // Validate JSON if type is json
            if ($request->type === 'json' && $value) {
                $decoded = json_decode($value);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return back()->withErrors(['value' => 'Invalid JSON format'])->withInput();
                }
            }

            $this->createCustomization([
                'key' => $request->key,
                'value' => $value,
                'type' => $request->type,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0
            ]);

            // Clear cache
            $this->clearCustomizationCache($request->key);

            return redirect()->route('customize-site.index')
                            ->with('success', 'Customization created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error creating customization: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $customization = $this->getCustomizationById($id);
        if (!$customization) {
            return redirect()->route('customize-site.index')
                           ->with('error', 'Customization not found.');
        }
        return view('hrms.master.customize-site.show', compact('customization'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $customization = $this->getCustomizationById($id);
        if (!$customization) {
            return redirect()->route('customize-site.index')
                           ->with('error', 'Customization not found.');
        }
        return view('hrms.master.customize-site.edit', compact('customization'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $customization = $this->getCustomizationById($id);
        if (!$customization) {
            return redirect()->route('customize-site.index')
                           ->with('error', 'Customization not found.');
        }

        $request->validate([
            'key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('site_customizations', 'key')->ignore($id)
            ],
            'value' => 'nullable|string',
            'type' => 'required|in:text,image,json',
            'description' => 'nullable|string|max:500',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {
            $value = $request->value;
            
            // Handle file upload for logo
            if ($request->hasFile('logo_file')) {
                // Delete old logo if exists
                if ($customization->type === 'image' && $customization->value) {
                    $this->deleteLogo($customization->value);
                }
                $value = $this->uploadLogo($request->file('logo_file'));
            }

            // Validate JSON if type is json
            if ($request->type === 'json' && $value) {
                $decoded = json_decode($value);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return back()->withErrors(['value' => 'Invalid JSON format'])->withInput();
                }
            }

            $this->updateCustomization($id, [
                'key' => $request->key,
                'value' => $value,
                'type' => $request->type,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0
            ]);

            // Clear cache
            $this->clearCustomizationCache($request->key);

            return redirect()->route('customize-site.index')
                            ->with('success', 'Customization updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error updating customization: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $customization = $this->getCustomizationById($id);
        if (!$customization) {
            return redirect()->route('customize-site.index')
                           ->with('error', 'Customization not found.');
        }

        try {
            // Delete logo file if exists
            if ($customization->type === 'image' && $customization->value) {
                $this->deleteLogo($customization->value);
            }

            $this->deleteCustomization($id);

            // Clear cache
            $this->clearCustomizationCache($customization->key);

            return redirect()->route('customize-site.index')
                            ->with('success', 'Customization deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('customize-site.index')
                           ->with('error', 'Error deleting customization: ' . $e->getMessage());
        }
    }

    /**
     * Logo Management Page
     */
    public function logoManagement()
    {
        $logos = $this->getLogoCustomizations();
        $currencies = $this->getCurrencyLogos();
        return view('hrms.master.customize-site.logo-management', compact('logos', 'currencies'));
    }

    /**
     * Update Main Logo
     */
    public function updateMainLogo(Request $request)
    {
        $request->validate([
            'main_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {
            $logoPath = $this->uploadLogo($request->file('main_logo'));
            
            $this->updateOrCreateCustomization('main_logo', $logoPath, 'image', 'Main site logo');

            // Clear cache
            $this->clearCustomizationCache('main_logo');

            return redirect()->back()->with('success', 'Main logo updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating main logo: ' . $e->getMessage());
        }
    }

    /**
     * Update Currency Logo
     */
    public function updateCurrencyLogo(Request $request)
    {
        $request->validate([
            'currency_code' => 'required|string|max:10|regex:/^[A-Z]{3,10}$/i',
            'currency_logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024'
        ]);

        try {
            $currencyCode = strtoupper($request->currency_code);
            $logoPath = $this->uploadLogo($request->file('currency_logo'), 'currencies');
            $key = 'currency_logo_' . strtolower($currencyCode);
            
            $this->updateOrCreateCustomization($key, $logoPath, 'image', 'Currency logo for ' . $currencyCode);

            // Clear cache
            $this->clearCustomizationCache($key);

            return redirect()->back()->with('success', "Currency logo for {$currencyCode} updated successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating currency logo: ' . $e->getMessage());
        }
    }

    /**
     * Delete Currency Logo
     */
    public function deleteCurrencyLogo($currencyCode)
    {
        try {
            $key = 'currency_logo_' . strtolower($currencyCode);
            $customization = $this->getCustomizationByKey($key);
            
            if ($customization) {
                $this->deleteLogo($customization->value);
                $this->deleteCustomizationByKey($key);
                
                // Clear cache
                $this->clearCustomizationCache($key);
            }

            return redirect()->back()->with('success', 'Currency logo deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting currency logo: ' . $e->getMessage());
        }
    }

    /**
     * Bulk Toggle Status
     */
    public function bulkToggleStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:site_customizations,id',
            'status' => 'required|boolean'
        ]);

        try {
            $customizations = DB::table('site_customizations')
                               ->whereIn('id', $request->ids)
                               ->get();

            DB::table('site_customizations')
              ->whereIn('id', $request->ids)
              ->update(['is_active' => $request->status, 'updated_at' => now()]);

            // Clear cache for affected customizations
            foreach ($customizations as $customization) {
                $this->clearCustomizationCache($customization->key);
            }
            
            return response()->json([
                'success' => true, 
                'message' => 'Status updated successfully for ' . count($request->ids) . ' items'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk Delete
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:site_customizations,id'
        ]);

        try {
            // Get customizations to delete files
            $customizations = DB::table('site_customizations')
                               ->whereIn('id', $request->ids)
                               ->get();
            
            // Delete files and clear cache
            foreach ($customizations as $customization) {
                if ($customization->type === 'image' && $customization->value) {
                    $this->deleteLogo($customization->value);
                }
                $this->clearCustomizationCache($customization->key);
            }
            
            // Delete records
            DB::table('site_customizations')->whereIn('id', $request->ids)->delete();
            
            return response()->json([
                'success' => true, 
                'message' => count($request->ids) . ' items deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error deleting items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Customizations
     */
    public function export()
    {
        try {
            $customizations = $this->getAllCustomizations();
            $exportData = [];

            foreach ($customizations as $customization) {
                $exportData[] = [
                    'key' => $customization->key,
                    'value' => $customization->value,
                    'type' => $customization->type,
                    'description' => $customization->description,
                    'is_active' => $customization->is_active,
                    'created_at' => $customization->created_at,
                    'updated_at' => $customization->updated_at
                ];
            }

            $filename = 'site_customizations_' . date('Y-m-d_H-i-s') . '.json';
            
            return response()->json($exportData)
                           ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error exporting customizations: ' . $e->getMessage());
        }
    }

    // ==================== DATABASE METHODS ====================

    /**
     * Get all customizations
     */
    private function getAllCustomizations()
    {
        return DB::table('site_customizations')
                 ->orderBy('created_at', 'desc')
                 ->get();
    }

    /**
     * Get customization by ID
     */
    private function getCustomizationById($id)
    {
        return DB::table('site_customizations')->where('id', $id)->first();
    }

    /**
     * Get customization by key
     */
    private function getCustomizationByKey($key)
    {
        return DB::table('site_customizations')->where('key', $key)->first();
    }

    /**
     * Get logo customizations
     */
    private function getLogoCustomizations()
    {
        return DB::table('site_customizations')
                 ->where('type', 'image')
                 ->where('key', 'like', '%logo%')
                 ->where('is_active', 1)
                 ->get();
    }

    /**
     * Get currency logos
     */
    private function getCurrencyLogos()
    {
        return DB::table('site_customizations')
                 ->where('key', 'like', 'currency_logo_%')
                 ->where('is_active', 1)
                 ->orderBy('key')
                 ->get();
    }

    /**
     * Create customization
     */
    private function createCustomization($data)
    {
        return DB::table('site_customizations')->insert(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now()
        ]));
    }

    /**
     * Update customization
     */
    private function updateCustomization($id, $data)
    {
        return DB::table('site_customizations')
                 ->where('id', $id)
                 ->update(array_merge($data, ['updated_at' => now()]));
    }

    /**
     * Update or create customization
     */
    private function updateOrCreateCustomization($key, $value, $type, $description)
    {
        $existing = $this->getCustomizationByKey($key);
        
        if ($existing) {
            // Delete old logo if updating
            if ($type === 'image' && $existing->value) {
                $this->deleteLogo($existing->value);
            }
            
            return DB::table('site_customizations')
                     ->where('key', $key)
                     ->update([
                         'value' => $value,
                         'type' => $type,
                         'description' => $description,
                         'updated_at' => now()
                     ]);
        } else {
            return DB::table('site_customizations')->insert([
                'key' => $key,
                'value' => $value,
                'type' => $type,
                'description' => $description,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Delete customization
     */
    private function deleteCustomization($id)
    {
        return DB::table('site_customizations')->where('id', $id)->delete();
    }

    /**
     * Delete customization by key
     */
    private function deleteCustomizationByKey($key)
    {
        return DB::table('site_customizations')->where('key', $key)->delete();
    }

    // ==================== FILE UPLOAD METHODS ====================

    /**
     * Upload logo
     */
    private function uploadLogo($file, $folder = 'logos')
    {
        $fileName = $folder . '_' . Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $fileName, 'public');
        return $path;
    }

    /**
     * Delete logo
     */
    private function deleteLogo($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    // ==================== CACHE METHODS ====================

    /**
     * Clear customization cache
     */
    private function clearCustomizationCache($key)
    {
        Cache::forget("site_customization_{$key}");
        Cache::forget('all_customizations');
        Cache::forget('currency_logos');
    }

    // ==================== STATIC HELPER METHODS ====================

    /**
     * Get customization value with caching
     */
    public static function getCustomizationValue($key, $default = null)
    {
        return Cache::remember("site_customization_{$key}", 3600, function () use ($key, $default) {
            $customization = DB::table('site_customizations')
                              ->where('key', $key)
                              ->where('is_active', 1)
                              ->first();
            
            return $customization ? $customization->value : $default;
        });
    }

    /**
     * Get main logo URL
     */
    public static function getMainLogo()
    {
        $logo = self::getCustomizationValue('main_logo');
        return $logo ? asset('storage/' . $logo) : asset('assets/images/default-logo.png');
    }

    /**
     * Get favicon URL
     */
    public static function getFavicon()
    {
        $favicon = self::getCustomizationValue('site_favicon');
        return $favicon ? asset('storage/' . $favicon) : asset('assets/images/favicon.ico');
    }

    /**
     * Get currency logo URL
     */
    public static function getCurrencyLogoUrl($currencyCode)
    {
        $key = 'currency_logo_' . strtolower($currencyCode);
        $logo = self::getCustomizationValue($key);
        return $logo ? asset('storage/' . $logo) : null;
    }

    /**
     * Get all currency logos
     */
    public static function getAllCurrencyLogos()
    {
        return Cache::remember('currency_logos', 3600, function () {
            $currencies = DB::table('site_customizations')
                           ->where('key', 'like', 'currency_logo_%')
                           ->where('is_active', 1)
                           ->get();
            
            $result = [];
            foreach ($currencies as $currency) {
                $currencyCode = str_replace('currency_logo_', '', $currency->key);
                $result[strtoupper($currencyCode)] = asset('storage/' . $currency->value);
            }
            
            return $result;
        });
    }

    /**
     * Get site title
     */
    public static function getSiteTitle($default = 'My Application')
    {
        return self::getCustomizationValue('site_title', $default);
    }

    /**
     * Get contact email
     */
    public static function getContactEmail($default = 'contact@example.com')
    {
        return self::getCustomizationValue('contact_email', $default);
    }

    /**
     * Get social links
     */
    public static function getSocialLinks()
    {
        $links = self::getCustomizationValue('social_links');
        return $links ? json_decode($links, true) : [];
    }

    /**
     * Get theme colors
     */
    public static function getThemeColors()
    {
        $colors = self::getCustomizationValue('theme_colors');
        return $colors ? json_decode($colors, true) : [];
    }
}