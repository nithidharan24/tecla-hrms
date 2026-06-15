<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\QuestionTemplate;
use App\Models\TemplateQuestion;
use App\Models\QuestionAnswer;
use Illuminate\Http\Request;

class QuestionTemplateController extends Controller
{
    public function index()
    {
        $templates = QuestionTemplate::with('questions.answers')->get();
        return view('hrms.master.question-template.index', compact('templates'));
    }

    public function create()
    {
        return view('hrms.master.question-template.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Please enter a template name',
        ]);

        $surveyFlow = $request->input('survey_flow', 'single_metric');
        $layout = $request->input('layout', 'all_in_one');
        $displayImage = $request->input('display_image', '#667eea');
        $questionsData = $request->input('questions', []);

        // Handle JSON string from frontend (if needed)
        if (is_string($questionsData)) {
            $questionsData = json_decode($questionsData, true) ?? [];
        }

        // Create template with safe defaults
        $template = QuestionTemplate::create([
            'name' => $validated['name'],
            'description' => $request->input('description'),
            'survey_flow' => $surveyFlow,
            'layout' => $layout,
            'display_image' => $displayImage,
            'total_questions' => is_array($questionsData) ? count($questionsData) : 0
        ]);

        if (is_array($questionsData) && count($questionsData) > 0) {
            foreach ($questionsData as $index => $questionData) {
                if (!isset($questionData['type']) || !isset($questionData['text'])) {
                    continue; // Skip invalid questions
                }

                $question = TemplateQuestion::create([
                    'template_id' => $template->id,
                    'type' => $questionData['type'],
                    'question_text' => $questionData['text'],
                    'sort_order' => $index,
                    'is_mandatory' => $questionData['is_mandatory'] ?? false,
                    'enable_comments' => $questionData['enable_comments'] ?? false
                ]);

                // Save options/answers if present
                if (isset($questionData['options']) && is_array($questionData['options'])) {
                    foreach ($questionData['options'] as $optionIndex => $optionLabel) {
                        QuestionAnswer::create([
                            'question_id' => $question->id,
                            'label' => $optionLabel,
                            'value' => $optionIndex,
                            'sort_order' => $optionIndex
                        ]);
                    }
                }
            }
        }

        return redirect()->route('templates.index')->with('success', 'Template created successfully');
    }

    public function edit(QuestionTemplate $template)
    {
        $template->load('questions.answers');
        return view('hrms.master.question-template.edit', compact('template'));
    }


public function update(Request $request, QuestionTemplate $template)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'survey_flow' => 'nullable|string',
        'layout' => 'nullable|string',
        'display_image' => 'nullable|string',
    ]);

    $questionsData = $request->input('questions', []);
    
    // Handle JSON string from frontend
    if (is_string($questionsData)) {
        $questionsData = json_decode($questionsData, true) ?? [];
    }

    // Update template
    $template->update($validated);
    
    // Delete all existing questions and recreate
    $template->questions()->delete();
    
    if (is_array($questionsData) && count($questionsData) > 0) {
        foreach ($questionsData as $index => $questionData) {
            if (!isset($questionData['type']) || !isset($questionData['text'])) {
                continue; // Skip invalid questions
            }

            $question = TemplateQuestion::create([
                'template_id' => $template->id,
                'type' => $questionData['type'],
                'question_text' => $questionData['text'],
                'sort_order' => $index,
                'is_mandatory' => $questionData['is_mandatory'] ?? false,
                'enable_comments' => $questionData['enable_comments'] ?? false
            ]);

            // Save options/answers if present
            if (isset($questionData['options']) && is_array($questionData['options'])) {
                foreach ($questionData['options'] as $optionIndex => $optionLabel) {
                    QuestionAnswer::create([
                        'question_id' => $question->id,
                        'label' => $optionLabel,
                        'value' => $optionIndex,
                        'sort_order' => $optionIndex
                    ]);
                }
            }
        }
    }

    $template->update(['total_questions' => count($questionsData)]);

    return redirect()->route('templates.index')->with('success', 'Template updated successfully');
}


    public function show(QuestionTemplate $template)
    {
        $template->load('questions.answers');
        return view('hrms.master.question-template.show', compact('template'));
    }

    public function destroy(QuestionTemplate $template)
    {
        $template->delete();
        return redirect()->route('templates.index')->with('success', 'Template deleted successfully');
    }

    // Questions (for edit page)
    public function addQuestion(Request $request, QuestionTemplate $template)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:template_questions,id',
            'type' => 'required|in:yes_no,nps,star,rating_scale,single,multiple,comment,date',
            'question_text' => 'required|string',
            'is_mandatory' => 'boolean',
            'enable_comments' => 'boolean',
            'options' => 'nullable|array'
        ]);

        // Check if editing existing question
        if (isset($validated['id'])) {
            $question = TemplateQuestion::find($validated['id']);
            if ($question && $question->template_id == $template->id) {
                // Update existing question
                $question->update([
                    'type' => $validated['type'],
                    'question_text' => $validated['question_text'],
                    'is_mandatory' => $validated['is_mandatory'] ?? false,
                    'enable_comments' => $validated['enable_comments'] ?? false
                ]);

                // Delete existing answers and recreate
                $question->answers()->delete();
                
                // Save options if provided
                if (isset($validated['options']) && is_array($validated['options'])) {
                    foreach ($validated['options'] as $index => $option) {
                        QuestionAnswer::create([
                            'question_id' => $question->id,
                            'label' => is_array($option) ? ($option['label'] ?? $option) : $option,
                            'value' => $index,
                            'sort_order' => $index
                        ]);
                    }
                }

                return response()->json(['success' => true, 'message' => 'Question updated successfully']);
            }
        }

        // Create new question
        $question = TemplateQuestion::create([
            'template_id' => $template->id,
            'sort_order' => $template->questions()->max('sort_order') + 1,
            'type' => $validated['type'],
            'question_text' => $validated['question_text'],
            'is_mandatory' => $validated['is_mandatory'] ?? false,
            'enable_comments' => $validated['enable_comments'] ?? false
        ]);

        // Save options if provided
        if (isset($validated['options']) && is_array($validated['options'])) {
            foreach ($validated['options'] as $index => $option) {
                QuestionAnswer::create([
                    'question_id' => $question->id,
                    'label' => is_array($option) ? ($option['label'] ?? $option) : $option,
                    'value' => $index,
                    'sort_order' => $index
                ]);
            }
        }

        $template->update(['total_questions' => $template->questions()->count()]);

        return response()->json(['success' => true, 'message' => 'Question added successfully']);
    }

    public function deleteQuestion(TemplateQuestion $question)
    {
        $template = $question->template;
        $question->delete();
        $template->update(['total_questions' => $template->questions()->count()]);
        return response()->json(['success' => true]);
    }

    // Get question data for editing
    public function getQuestion(TemplateQuestion $question)
    {
        $question->load('answers');
        return response()->json([
            'success' => true,
            'question' => [
                'id' => $question->id,
                'type' => $question->type,
                'question_text' => $question->question_text,
                'is_mandatory' => $question->is_mandatory,
                'enable_comments' => $question->enable_comments,
                'options' => $question->answers->pluck('label')->toArray()
            ]
        ]);
    }

    // Answers (for edit page)
    public function addAnswer(Request $request, TemplateQuestion $question)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
        ]);

        QuestionAnswer::create([
            'question_id' => $question->id,
            'label' => $validated['label'],
            'value' => $question->answers()->count(),
            'sort_order' => $question->answers()->max('sort_order') + 1
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteAnswer(QuestionAnswer $answer)
    {
        $answer->delete();
        return response()->json(['success' => true]);
    }
}
