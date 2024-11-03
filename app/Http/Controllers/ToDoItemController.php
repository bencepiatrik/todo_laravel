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
        $completed = $request->input('completed');

        $todos = ToDoItem::with('category')
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when(isset($completed), function ($query) use ($completed) {
                return $query->where('is_done', $completed);
            })
            ->whereNull('deleted_at')
            ->where('user_id', auth()->id())
            ->get();

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
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'share_with_user_id' => 'nullable|integer|exists:users,id',
        ]);

        $todo = ToDoItem::create([
            'name' => $data['name'],
            'category_id' => $data['category_id'],
            'description' => $data['description'],
            'user_id' => auth()->id(),
        ]);

        if (!empty($data['share_with_user_id'])) {
            $todo->sharedUsers()->attach($data['share_with_user_id']);
        }

        return redirect()->route('todos.index')->with('success', 'ToDo item created and shared successfully!');
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
        $toDoItem = ToDoItem::findOrFail($id);
        $email = $request->input('email');

        $user = User::where('email', $email)->first();

        if ($user && $user->id !== auth()->id()) {
            $toDoItem->sharedWith()->attach($user->id);

            return redirect()->route('todos.index')->with('success', 'ToDo item shared successfully!');
        }

        return redirect()->route('todos.index')->with('error', 'User not found or invalid share.');
    }

    public function unshare($id, $userId)
    {
        $toDoItem = ToDoItem::findOrFail($id);
        $toDoItem->sharedWith()->detach($userId);

        return redirect()->route('todos.index')->with('success', 'Sharing canceled successfully!');
    }

}
