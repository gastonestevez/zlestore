<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Concept;

class ConceptController extends Controller
{
    function show() {
        $concepts = Concept::all();
        return view('concepts.concepts', compact('concepts'));
    }

    function create(Request $request) {
        $concept= new Concept();
        $concept->name = $request->name;
        $concept->save();

        return back()->with('success', 'Concepto creado exitosamente');
    }

    function update(Request $request) {
        $concept = Concept::find($request->id);
        $concept->name = $request->name;
        $concept->save();

        return back()->with('success', 'Concepto actualizado exitosamente');
    }
    
    function delete(Request $request) {
        $concept = Concept::find($request->id);
        $concept->delete();

        return back()->with('success', 'Concepto eliminado exitosamente');
    }
}
