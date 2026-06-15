<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class FontMasterController extends Controller
{
    // Show font selection page
    public function index()
    {
        $font = DB::table('font_master')->first();
        $selectedFont = $font ? $font->font_family : 'Poppins';

        // ✅ Full list of popular Google Fonts
        $availableFonts = [
            'Poppins',
            'Roboto',
            'Inter',
            'Open Sans',
            'Lato',
            'Montserrat',
            'Nunito',
            'Rubik',
            'Raleway',
            'Work Sans',
            'Source Sans 3',
            'Ubuntu',
            'Noto Sans',
            'Merriweather',
            'Playfair Display',
            'Oswald',
            'Kanit',
            'Jost',
            'Manrope',
            'Quicksand',
        ];

        return view('fontmaster.index', compact('selectedFont', 'availableFonts'));
    }

    // Update font in DB
    public function update(Request $request)
    {
        $request->validate([
            'font_family' => 'required|string',
        ]);

        DB::table('font_master')->updateOrInsert(
            ['id' => 1],
            ['font_family' => $request->font_family, 'updated_at' => now()]
        );

        return redirect()->back()->with('success', 'Font family updated successfully!');
    }

    // Get currently selected font globally
    public static function getFontFamily()
    {
        $font = DB::table('font_master')->first();
        return $font ? $font->font_family : 'Poppins';
    }

    // Generate Google Fonts link dynamically
    public static function getGoogleFontLink($font)
    {
        $fontName = str_replace(' ', '+', $font);
        return "https://fonts.googleapis.com/css2?family={$fontName}:wght@300;400;500;600;700&display=swap";
    }
}
