<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo List</title>
</head>
<body>
<h1>ToDo List</h1>

<div>
    <a href="{{ route('todos.index') }}">Home</a> |
    <a href="{{ route('categories.index') }}">Categories</a> |
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        Logout
    </a>
</div>

<!-- Filter Section -->
<form action="{{ route('todos.index') }}" method="GET">
    <label for="category">Filter by Category:</label>
    <select name="category" id="category">
        <option value="">All Categories</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <label for="completed">Filter by Completion Status:</label>
    <select name="completed" id="completed">
        <option value="">All Items</option>
        <option value="1" {{ request('completed') == '1' ? 'selected' : '' }}>Completed</option>
        <option value="0" {{ request('completed') == '0' ? 'selected' : '' }}>Not Completed</option>
    </select>

    <button type="submit">Filter</button>
</form>

<!-- Active ToDo Items Table -->
<h2>Active ToDo Items</h2>
<table border="1">
    <thead>
    <tr>
        <th>Complete</th>
        <th>Task Name</th>
        <th>Category</th>
        <th>Description</th>
        <th>Shared With</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($todos as $todo)
        <tr>
            <td>
                <form action="{{ route('todos.toggleComplete', $todo->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="checkbox" onclick="this.form.submit()" {{ $todo->is_done ? 'checked' : '' }}>
                </form>
            </td>
            <td>{{ $todo->name }}</td>
            <td>{{ $todo->category->name ?? 'Uncategorized' }}</td>
            <td>{{ $todo->description }}</td>
            <td>
                @foreach ($todo->sharedUsers as $sharedUser)
                    User ID: {{ $sharedUser->id }}<br>
                @endforeach
            </td>
            <td>
                <a href="{{ route('todos.edit', $todo->id) }}">Edit</a>
                <form action="{{ route('todos.destroy', $todo->id) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<!-- Pagination Links -->
{{ $todos->links() }}

<!-- Add New ToDo Item Section -->
<h3>Add New ToDo Item</h3>
<form action="{{ route('todos.store') }}" method="POST">
    @csrf
    <input type="text" name="name" placeholder="New Task">
    <select name="category_id">
        <option value="">Select Category</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    </select>
    <input type="text" name="description" placeholder="Description">
    <input type="number" name="shared_with" placeholder="User ID to share with">
    <button type="submit">Add ToDo</button>
</form>

<!-- Deleted ToDo Items Table -->
<h2>Deleted ToDo Items</h2>
<table border="1">
    <thead>
    <tr>
        <th>Completed</th>
        <th>Task Name</th>
        <th>Category</th>
        <th>Description</th>
        <th>Restore</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($deletedTodos as $deletedTodo)
        <tr>
            <td>{{ $deletedTodo->is_done ? 'Yes' : 'No' }}</td>
            <td>{{ $deletedTodo->name }}</td>
            <td>{{ $deletedTodo->category->name ?? 'Uncategorized' }}</td>
            <td>{{ $deletedTodo->description }}</td>
            <td>
                <form action="{{ route('todos.restore', $deletedTodo->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit">Restore</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
