<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ToDoItem;
use Illuminate\Http\Request;

class ToDoItemController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $categoryId = $request->input('category');

        // Retrieve active ToDo items with optional category filter
        $todos = ToDoItem::with('category')
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->whereNull('deleted_at') // Exclude soft-deleted items
            ->where('user_id', auth()->id())
            ->get();

        // Retrieve soft-deleted ToDo items
        $deletedTodos = ToDoItem::onlyTrashed()
            ->where('user_id', auth()->id())
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->with('category')
            ->get();

        return view('todo.index', compact('todos', 'categories', 'deletedTodos'));
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

    public function update(Request $request, $id)
    {
        $toDoItem = ToDoItem::findOrFail($id);

        if ($toDoItem->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'is_done' => 'boolean',
        ]);

        $toDoItem->update($data);

        return redirect()->route('todos.index')->with('success', 'ToDo item updated successfully!');
    }


    public function edit($id)
    {
        $toDoItem = ToDoItem::findOrFail($id);

        // Ensure the user owns the item
        if ($toDoItem->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('todo.edit', compact('toDoItem'));
    }

    public function destroy($id)
    {
        $toDoItem = ToDoItem::findOrFail($id);

        if ($toDoItem->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $toDoItem->delete();

        return redirect()->route('todos.index')->with('success', 'ToDo item deleted successfully! You can restore it from the deleted items section.');
    }


    public function restore($id)
    {
        $toDoItem = ToDoItem::withTrashed()->findOrFail($id);

        // Ensure the authenticated user owns the ToDo item
        if ($toDoItem->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $toDoItem->restore();

        return redirect()->route('todos.index')->with('success', 'ToDo item restored successfully!');
    }


    public function toggleComplete($id)
    {
        // Find the ToDo item by its ID
        $todo = ToDoItem::findOrFail($id);

        // Toggle the 'is_done' attribute
        $todo->is_done = !$todo->is_done;
        $todo->save();

        // Redirect back with a success message
        return redirect()->route('todos.index')->with('success', 'ToDo item updated successfully!');
    }

}
