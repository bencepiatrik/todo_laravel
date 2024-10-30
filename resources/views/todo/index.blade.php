<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My ToDo List</title>
</head>
<body>

<h1>My ToDo List</h1>

<!-- Display ToDo Items -->
<table border="1" cellpadding="10" cellspacing="0">
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Category</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($toDoItems as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->name }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->category ? $item->category->name : 'No Category' }}</td>
            <td>{{ $item->is_done ? 'Completed' : 'Pending' }}</td>
            <td>
                <a href="{{ route('todos.edit', $item->id) }}">Edit</a> |
                <form action="{{ route('todos.destroy', $item->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6">No ToDo items found.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<!-- Pagination Links -->
{{ $toDoItems->links() }}

<!-- Add New ToDo Item -->
<h2>Add New ToDo</h2>
<form action="{{ route('todos.store') }}" method="POST">
    @csrf
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required><br><br>

    <label for="description">Description:</label>
    <textarea id="description" name="description"></textarea><br><br>

    <label for="category">Category:</label>
    <select id="category" name="category_id">
        <option value="">No Category</option>
        @foreach (\App\Models\Category::all() as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select><br><br>

    <button type="submit">Add ToDo</button>
</form>


</body>
</html>
