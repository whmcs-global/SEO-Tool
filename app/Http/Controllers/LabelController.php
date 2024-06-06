<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Label;

class LabelController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:labels',
        ]);

        $label = Label::create(['name' => $request->name]);

        return response()->json(['label' => $label], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $label = Label::find($id);
        $label->name = $request->name;
        $label->save();

        return redirect()->route('dashboard');
    }

    public function destroy($id)
    {
        $label = Label::find($id);
        $label->delete();

        return redirect()->route('dashboard');
    }
}
