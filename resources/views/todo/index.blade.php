<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo List</title>
</head>
<body>

<!-- Navbar -->
<nav>
    <a href="{{ route('todos.index') }}">Home</a> |
    <a href="{{ route('categories.index') }}">Categories</a> |
    <!-- Logout Link -->
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
    <!-- Logout Form (Hidden) -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</nav>

<h1>ToDo List</h1>

<!-- Success Message -->
@if (session('success'))
    <div style="color: green;">{{ session('success') }}</div>
@endif

<!-- Category Filter Form -->
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
    <noscript><button type="submit">Filter</button></noscript>
</form>

<!-- ToDo Items Table -->
<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <thead>
    <tr>
        <th>Complete</th>
        <th>Task Name</th>
        <th>Category</th>
        <th>Description</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($todos as $todo)
        <tr>
            <!-- Completion Checkbox -->
            <td>
                <form action="{{ route('todos.toggleComplete', $todo->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <input type="checkbox" onchange="this.form.submit()" {{ $todo->is_done ? 'checked' : '' }}>
                </form>
            </td>

            <!-- Task Details -->
            <td>{{ $todo->name }}</td>
            <td>{{ $todo->category ? $todo->category->name : 'No Category' }}</td>
            <td>{{ $todo->description }}</td>

            <!-- Edit and Delete Buttons -->
            <td>
                <a href="{{ route('todos.edit', $todo->id) }}" style="margin-right: 10px;">Edit</a>
                <form action="{{ route('todos.destroy', $todo->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach

    <!-- Add New ToDo Item Row -->
    <tr>
        <form action="{{ route('todos.store') }}" method="POST">
            @csrf
            <td></td>
            <td><input type="text" name="name" placeholder="New Task" required></td>
            <td>
                <select name="category_id">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="text" name="description" placeholder="Description"></td>
            <td>
                <button type="submit">Add ToDo</button>
            </td>
        </form>
    </tr>

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

    </tbody>
</table>

</body>
</html>
