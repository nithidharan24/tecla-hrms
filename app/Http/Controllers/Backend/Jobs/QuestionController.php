<?php

namespace App\Http\Controllers\Backend\Jobs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = DB::table('questions');
            
            // Apply search filter
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('question', 'LIKE', "%{$search}%")
                      ->orWhere('category', 'LIKE', "%{$search}%")
                      ->orWhere('option_a', 'LIKE', "%{$search}%")
                      ->orWhere('option_b', 'LIKE', "%{$search}%")
                      ->orWhere('option_c', 'LIKE', "%{$search}%")
                      ->orWhere('option_d', 'LIKE', "%{$search}%");
                });
            }
            
            // Apply category filter
            if ($request->has('category') && $request->category != '') {
                $query->where('category', $request->category);
            }
            
            // Apply date range filter
            if ($request->has('start_date') && $request->start_date != '') {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            
            if ($request->has('end_date') && $request->end_date != '') {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
            
            // Get unique categories for filter dropdown
            $categories = DB::table('questions')
                ->select('category')
                ->distinct()
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->orderBy('category')
                ->get();
            
            $questions = $query->orderBy('created_at', 'desc')->paginate(10);
            
            return view('hrms.Jobs.questions.index', compact('questions', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching questions: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load questions. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('hrms.Jobs.questions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'question' => 'required|string|max:1000', // Increased max length for questions
            'category' => 'required|string|max:255',
            'option_a' => 'required|string|max:500',
            'option_b' => 'required|string|max:500',
            'option_c' => 'required|string|max:500',
            'option_d' => 'required|string|max:500',
            'correct_answer' => 'required|string|in:A,B,C,D',
            'code_snippets' => 'nullable|string|max:5000', // Increased max length for code
            'answer_explanation' => 'nullable|string|max:2000', // Increased max length for explanation
            'video_link' => 'nullable|url|max:2048', // Max length for URL
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ], [
            'question.required' => 'The question field is required.',
            'question.string' => 'The question must be a string.',
            'question.max' => 'The question may not be greater than :max characters.',

            'category.required' => 'The category field is required.',
            'category.string' => 'The category must be a string.',
            'category.max' => 'The category may not be greater than :max characters.',

            'option_a.required' => 'Option A is required.',
            'option_a.string' => 'Option A must be a string.',
            'option_a.max' => 'Option A may not be greater than :max characters.',

            'option_b.required' => 'Option B is required.',
            'option_b.string' => 'Option B must be a string.',
            'option_b.max' => 'Option B may not be greater than :max characters.',

            'option_c.required' => 'Option C is required.',
            'option_c.string' => 'Option C must be a string.',
            'option_c.max' => 'Option C may not be greater than :max characters.',

            'option_d.required' => 'Option D is required.',
            'option_d.string' => 'Option D must be a string.',
            'option_d.max' => 'Option D may not be greater than :max characters.',

            'correct_answer.required' => 'The correct answer field is required.',
            'correct_answer.string' => 'The correct answer must be a string.',
            'correct_answer.in' => 'The correct answer must be one of A, B, C, or D.',

            'code_snippets.string' => 'Code snippets must be a string.',
            'code_snippets.max' => 'Code snippets may not be greater than :max characters.',

            'answer_explanation.string' => 'The answer explanation must be a string.',
            'answer_explanation.max' => 'The answer explanation may not be greater than :max characters.',

            'video_link.url' => 'The video link must be a valid URL.',
            'video_link.max' => 'The video link may not be greater than :max characters.',

            'question_image.image' => 'The uploaded file must be an image.',
            'question_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'question_image.max' => 'The image may not be greater than :max kilobytes.',
        ]);

        try {
            // Prepare the data for insertion
            $data = [
                'question' => trim($request->input('question')),
                'category' => trim($request->input('category')),
                'option_a' => trim($request->input('option_a')),
                'option_b' => trim($request->input('option_b')),
                'option_c' => trim($request->input('option_c')),
                'option_d' => trim($request->input('option_d')),
                'correct_answer' => trim($request->input('correct_answer')),
                'code_snippets' => $request->input('code_snippets'),
                'answer_explanation' => $request->input('answer_explanation'),
                'video_link' => $request->input('video_link'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            // Handle the image upload
            if ($request->hasFile('question_image')) {
                $file = $request->file('question_image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('uploads/questions');
                
                // Create the directory if it doesn't exist
                if (!File::isDirectory($destinationPath)) {
                    File::makeDirectory($destinationPath, 0777, true, true);
                }
                
                // Move the file to the public/uploads/questions directory
                $file->move($destinationPath, $fileName);
                
                // Store the relative path in the database
                $data['question_image'] = 'uploads/questions/' . $fileName;
            }

            // Insert the data into the 'questions' table
            DB::table('questions')->insert($data);

            return redirect()->route('Question.index')->with('success', 'Question added successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating question: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to add question. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $question = DB::table('questions')->where('id', $id)->first();
            if (!$question) {
                return redirect()->route('Question.index')->with('error', 'Question not found.');
            }
            return view('hrms.Jobs.questions.show', compact('question'));
        } catch (\Exception $e) {
            Log::error('Error fetching question for show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load question details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $question = DB::table('questions')->where('id', $id)->first();
            if (!$question) {
                return redirect()->route('Question.index')->with('error', 'Question not found.');
            }
            return view('hrms.Jobs.questions.edit', compact('question'));
        } catch (\Exception $e) {
            Log::error('Error fetching question for edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load question for editing. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string|max:1000',
            'category' => 'required|string|max:255',
            'option_a' => 'required|string|max:500',
            'option_b' => 'required|string|max:500',
            'option_c' => 'required|string|max:500',
            'option_d' => 'required|string|max:500',
            'correct_answer' => 'required|string|in:A,B,C,D',
            'code_snippets' => 'nullable|string|max:5000',
            'answer_explanation' => 'nullable|string|max:2000',
            'video_link' => 'nullable|url|max:2048',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'question.required' => 'The question field is required.',
            'question.string' => 'The question must be a string.',
            'question.max' => 'The question may not be greater than :max characters.',

            'category.required' => 'The category field is required.',
            'category.string' => 'The category must be a string.',
            'category.max' => 'The category may not be greater than :max characters.',

            'option_a.required' => 'Option A is required.',
            'option_a.string' => 'Option A must be a string.',
            'option_a.max' => 'Option A may not be greater than :max characters.',

            'option_b.required' => 'Option B is required.',
            'option_b.string' => 'Option B must be a string.',
            'option_b.max' => 'Option B may not be greater than :max characters.',

            'option_c.required' => 'Option C is required.',
            'option_c.string' => 'Option C must be a string.',
            'option_c.max' => 'Option C may not be greater than :max characters.',

            'option_d.required' => 'Option D is required.',
            'option_d.string' => 'Option D must be a string.',
            'option_d.max' => 'Option D may not be greater than :max characters.',

            'correct_answer.required' => 'The correct answer field is required.',
            'correct_answer.string' => 'The correct answer must be a string.',
            'correct_answer.in' => 'The correct answer must be one of A, B, C, or D.',

            'code_snippets.string' => 'Code snippets must be a string.',
            'code_snippets.max' => 'Code snippets may not be greater than :max characters.',

            'answer_explanation.string' => 'The answer explanation must be a string.',
            'answer_explanation.max' => 'The answer explanation may not be greater than :max characters.',

            'video_link.url' => 'The video link must be a valid URL.',
            'video_link.max' => 'The video link may not be greater than :max characters.',

            'question_image.image' => 'The uploaded file must be an image.',
            'question_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'question_image.max' => 'The image may not be greater than :max kilobytes.',
        ]);

        try {
            $data = [
                'question' => trim($request->question),
                'category' => trim($request->category),
                'option_a' => trim($request->option_a),
                'option_b' => trim($request->option_b),
                'option_c' => trim($request->option_c),
                'option_d' => trim($request->option_d),
                'correct_answer' => trim($request->correct_answer),
                'code_snippets' => $request->code_snippets,
                'answer_explanation' => $request->answer_explanation,
                'video_link' => $request->video_link,
                'updated_at' => Carbon::now(),
            ];

            // Handle image update
            if ($request->hasFile('question_image')) {
                $oldImage = DB::table('questions')->where('id', $id)->value('question_image');
                // Delete old image if it exists and is not the default placeholder
                if ($oldImage && File::exists(public_path($oldImage))) {
                    File::delete(public_path($oldImage));
                }
                $file = $request->file('question_image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('uploads/questions');
                if (!File::isDirectory($destinationPath)) {
                    File::makeDirectory($destinationPath, 0777, true, true);
                }
                $file->move($destinationPath, $fileName);
                $data['question_image'] = 'uploads/questions/' . $fileName;
            } else {
                // If no new image is uploaded, retain the existing one
                // This is important if the user just updates other fields
                // If the user wants to remove the image, they would need a separate checkbox/button
                // For now, we just don't update the 'question_image' field if no new file is provided.
                // The 'question_image' field is not unset from $data, it's just not added if no new file.
            }

            $updated = DB::table('questions')->where('id', $id)->update($data);

            if ($updated) {
                return redirect()->route('Question.index')->with('success', 'Question updated successfully.');
            } else {
                return redirect()->back()->withInput()->with('error', 'No changes were made or question not found.');
            }
        } catch (\Exception $e) {
            Log::error('Error updating question: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update question. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $question = DB::table('questions')->where('id', $id)->first();
            
            if (!$question) {
                return redirect()->route('Question.index')->with('error', 'Question not found.');
            }

            // Delete associated image before deleting the record
            if ($question->question_image && File::exists(public_path($question->question_image))) {
                File::delete(public_path($question->question_image));
            }
            
            $deleted = DB::table('questions')->where('id', $id)->delete();

            if ($deleted) {
                return redirect()->route('Question.index')->with('success', 'Question deleted successfully.');
            } else {
                return redirect()->route('Question.index')->with('error', 'Failed to delete question. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('Error deleting question: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete question. Please try again.');
        }
    }

    /**
     * Download questions as PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf()
    {
        try {
            $questions = DB::table('questions')->get();
            
            $generalSettings = DB::table('general_settings')->first();
            
            $logoSetting = DB::table('logo_settings')->first();
            
            $siteTitle = $generalSettings->site_name ?? 'Organization Name';
            $contactEmail = $generalSettings->contact_email ?? 'contact@example.com';
            $contactPhone = $generalSettings->contact_phone ?? '';
            
            // Get the creation date from the first question created (or use current date if no questions)
            $createdDate = $questions->isNotEmpty() 
                ? Carbon::parse($questions->first()->created_at)->format('d-m-Y')
                : Carbon::now()->format('d-m-Y');
            
            $totalQuestions = count($questions);
            
            $logoUrl = null;
            if ($logoSetting && $logoSetting->logo && File::exists(public_path('uploads/' . $logoSetting->logo))) {
                $logoPath = public_path('uploads/' . $logoSetting->logo);
                $logoData = base64_encode(file_get_contents($logoPath));
                $logoUrl = 'data:image/png;base64,' . $logoData;
            }
            
            $pdf = Pdf::loadView('hrms.Jobs.questions.pdf_questions', compact(
                'questions',
                'siteTitle',
                'contactEmail',
                'contactPhone',
                'createdDate',
                'totalQuestions',
                'logoUrl'
            ));
            
            return $pdf->download('question_paper_' . date('Y-m-d_H-i-s') . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF. Please try again.');
        }
    }
}