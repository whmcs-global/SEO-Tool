<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keyword;
use Illuminate\Support\Facades\Validator;


class KeywordController extends Controller
{

    public function dashboard()
    {
        if (auth()->user()->hasRole('admin')) {
            $keywords = Keyword::with('user')->get();
        } else {
            $keywords = Keyword::where('user_id', auth()->id())->get();
        }
        return view('dashboard', compact('keywords'));
    }

    public function create()
    {
        return view('keyword.create');
    }

    public function edit(Keyword $keyword)
    {
        return view('keyword.edit', compact('keyword'));
    }

    public function update(Request $request, Keyword $keyword)
    {
        $request->validate([
            'keyword' => 'required|string',
        ]);
        $keyword->keyword = $request->keyword;
        $keyword->save();
        return redirect()->route('dashboard');
    }


    public function store(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'keywords' => 'required|array',
                'keywords.*' => 'required|string|unique:keywords,keyword,NULL,id,user_id,' . auth()->id(),
            ], [
                'keywords.required' => 'Please enter at least one keyword',
                'keywords.*.required' => 'Please enter a keyword',
                'keywords.*.unique' => 'The keyword has already been added.',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $keywords = $request->keywords;
            foreach ($keywords as $keywordValue) {
                $keyword = new Keyword();
                $keyword->user_id = auth()->id();
                $keyword->keyword = $keywordValue;
                $keyword->save();
            }

            return response()->json(['success' => 'Keywords saved successfully'], 200);
        }

    public function destroy(Keyword $keyword)
    {
        $keyword->delete();
        return redirect()->back();
    }
}
