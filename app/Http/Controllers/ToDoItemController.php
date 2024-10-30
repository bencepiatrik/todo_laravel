<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ToDoItem;
use Illuminate\Http\Request;

class ToDoItemController extends Controller
{
    public function index(Request $request)
    {
        $toDoItems = ToDoItem::where('user_id', auth()->id())->paginate(10);
        return view('todo.index', compact('toDoItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $data['user_id'] = auth()->id();

        ToDoItem::create($data);

        return redirect()->route('todos.index')->with('success', 'ToDo item added successfully!');
    }

    public function show(ToDoItem $toDoItem)
    {
        $this->authorize('view', $toDoItem);
        return response()->json($toDoItem);
    }

    public function update(Request $request, ToDoItem $toDoItem)
    {
        $this->authorize('update', $toDoItem);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_done' => 'boolean',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $toDoItem->update($data);

        return response()->json($toDoItem, 200);
    }

    public function destroy($id)
    {
        $toDoItem = ToDoItem::find($id);

        if ($toDoItem->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $toDoItem->delete();

        return redirect()->route('todos.index')->with('success', 'ToDo item deleted successfully!');
    }

    public function restore($id)
    {
        $toDoItem = ToDoItem::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $toDoItem);

        $toDoItem->restore();

        return response()->json(['message' => 'Item restored'], 200);
    }
}
