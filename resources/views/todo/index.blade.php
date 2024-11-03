<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo List</title>
</head>
<body>
<!-- Navbar Section -->
<nav>
    <a href="{{ route('todos.index') }}">Home</a> |
    <a href="{{ route('categories.index') }}">Categories</a> |
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</nav>

<h1>ToDo List</h1>

<!-- Filter Form -->
<form method="GET" action="{{ route('todos.index') }}" style="margin-bottom: 20px;">
    <label for="category">Filter by Category:</label>
    <select name="category" id="category" onchange="this.form.submit()">
        <option value="">All Categories</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <label for="completed">Filter by Completion Status:</label>
    <select name="completed" id="completed" onchange="this.form.submit()">
        <option value="">All Items</option>
        <option value="1" {{ request('completed') === '1' ? 'selected' : '' }}>Completed</option>
        <option value="0" {{ request('completed') === '0' ? 'selected' : '' }}>Not Completed</option>
    </select>
</form>

<br><br><br>

<!-- Active ToDo Items Table -->
<div>
    <h2>Active ToDo Items</h2>
    <table>
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
                <td><input type="checkbox" disabled {{ $todo->is_done ? 'checked' : '' }}></td>
                <td>{{ $todo->name }}</td>
                <td>{{ $todo->category->name ?? 'Uncategorized' }}</td>
                <td>{{ $todo->description }}</td>
                <td>
                    @foreach ($todo->sharedUsers as $user)
                        {{ $user->id }}@if (!$loop->last), @endif
                    @endforeach
                </td>
                <td>
                    <a href="{{ route('todos.edit', $todo->id) }}">Edit</a> |
                    <form action="{{ route('todos.destroy', $todo->id) }}" method="POST" style="display:inline;">
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
    <div>
        {{ $todos->links() }}
    </div>
</div>

<!-- New ToDo Item Form -->
<h2>Add New ToDo Item</h2>
<table>
    <form action="{{ route('todos.store') }}" method="POST">
        @csrf
        <tr>
            <td><input type="checkbox" name="is_done" value="1"></td>
            <td><input type="text" name="name" placeholder="New Task"></td>
            <td>
                <select name="category_id">
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="text" name="description" placeholder="Description"></td>
            <td><input type="text" name="shared_user_id" placeholder="User ID to share with"></td>
            <td><button type="submit">Add ToDo</button></td>
        </tr>
    </form>
</table>

<br><br><br>

<!-- Deleted ToDo Items Table -->
<h2>Deleted ToDo Items</h2>
<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
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
    @foreach($deletedTodos as $todo)
        <tr>
            <td>
                <input type="checkbox" {{ $todo->is_done ? 'checked' : '' }} disabled>
            </td>
            <td>{{ $todo->name }}</td>
            <td>{{ $todo->category ? $todo->category->name : 'No Category' }}</td>
            <td>{{ $todo->description }}</td>
            <td>
                <form action="{{ route('todos.restore', $todo->id) }}" method="POST" style="display:inline;">
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
