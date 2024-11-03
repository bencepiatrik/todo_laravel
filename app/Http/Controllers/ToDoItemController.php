<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SharedItem;
use App\Models\ToDoItem;
use Illuminate\Http\Request;

class ToDoItemController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $categoryId = $request->input('category');
        $completed = $request->input('completed');

        $userTodosQuery = ToDoItem::with('category')
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when(isset($completed), function ($query) use ($completed) {
                return $query->where('is_done', $completed);
            })
            ->whereNull('deleted_at')
            ->where('user_id', auth()->id());

        $sharedTodosQuery = ToDoItem::with('category')
            ->whereHas('sharedItems', function ($query) {
                $query->where('shared_with_id', auth()->id());
            })
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when(isset($completed), function ($query) use ($completed) {
                return $query->where('is_done', $completed);
            })
            ->whereNull('deleted_at');

        $todos = $userTodosQuery->union($sharedTodosQuery)->paginate(5);

        $deletedTodos = ToDoItem::onlyTrashed()
            ->where('user_id', auth()->id())
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when(isset($completed), function ($query) use ($completed) {
                return $query->where('is_done', $completed);
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
            'category_id' => 'nullable|integer|exists:categories,id',
            'shared_with' => 'nullable|integer|exists:users,id',
        ]);

        $todo = ToDoItem::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'category_id' => $data['category_id'] ?? null,
            'user_id' => auth()->id(),
        ]);

        if (isset($data['shared_with'])) {
            SharedItem::create([
                'to_do_item_id' => $todo->id,
                'owner_id' => auth()->id(),
                'shared_with_id' => $data['shared_with'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('todos.index')->with('success', 'ToDo item created successfully!');
    }


    public function show(ToDoItem $toDoItem)
    {
        $this->authorize('view', $toDoItem);
        return response()->json($toDoItem);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'is_done' => 'nullable|boolean',
            'shared_with' => 'nullable|exists:users,id',
        ]);

        $todoItem = ToDoItem::findOrFail($id);
        $todoItem->update($data);

        if (!empty($data['shared_with'])) {
            $todoItem->sharedUsers()->detach();
            $todoItem->sharedUsers()->attach($data['shared_with']);
        } else {
            $todoItem->sharedUsers()->detach();
        }

        return redirect()->route('todos.index')->with('success', 'ToDo item updated successfully!');
    }



    public function edit($id)
    {
        $todoItem = ToDoItem::with('category', 'sharedUsers')->findOrFail($id);
        $sharedUserId = $todoItem->sharedUsers()->pluck('user_id')->first();
        $categories = Category::all();
        return view('todo.edit', compact('todoItem', 'categories', 'sharedUserId'));
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

        if ($toDoItem->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $toDoItem->restore();

        return redirect()->route('todos.index')->with('success', 'ToDo item restored successfully!');
    }


    public function toggleComplete($id)
    {
        $todo = ToDoItem::findOrFail($id);
        $todo->is_done = !$todo->is_done;
        $todo->save();

        return redirect()->route('todos.index')->with('success', 'ToDo item updated successfully!');
    }

    public function share(Request $request, $id)
    {
        $data = $request->validate(['shared_with' => 'required|integer|exists:users,id']);

        SharedItem::create([
            'to_do_item_id' => $id,
            'owner_id' => auth()->id(),
            'shared_with_id' => $data['shared_with'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('todos.index')->with('success', 'ToDo item shared successfully!');
    }

    public function unshare($id, $userId)
    {
        SharedItem::where('to_do_item_id', $id)
            ->where('shared_with_id', $userId)
            ->delete();

        return redirect()->route('todos.index')->with('success', 'ToDo item unshared successfully!');
    }
}
