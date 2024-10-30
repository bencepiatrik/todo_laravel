<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit ToDo Item</title>
</head>
<body>

<h1>Edit ToDo Item</h1>

<form action="{{ route('todos.update', $toDoItem->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label for="name">Name:</label>
    <input type="text" id="name" name="name" value="{{ $toDoItem->name }}" required><br><br>

    <label for="description">Description:</label>
    <textarea id="description" name="description">{{ $toDoItem->description }}</textarea><br><br>

    <label for="category">Category:</label>
    <select id="category" name="category_id">
        <option value="">No Category</option>
        @foreach (\App\Models\Category::all() as $category)
            <option value="{{ $category->id }}" {{ $toDoItem->category_id == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select><br><br>

    <label for="is_done">Status:</label>
    <input type="checkbox" id="is_done" name="is_done" {{ $toDoItem->is_done ? 'checked' : '' }}> Completed<br><br>

    <button type="submit">Update ToDo</button>
</form>

</body>
</html>
