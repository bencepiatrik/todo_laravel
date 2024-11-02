<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
</head>
<body>

<h1>Categories</h1>

<!-- Success Message -->
@if (session('success'))
    <div style="color: green;">{{ session('success') }}</div>
@endif

<!-- List of Categories -->
<ul>
    @foreach ($categories as $category)
        <li>
            {{ $category->name }}
            <a href="{{ route('categories.edit', $category->id) }}">Edit</a>
            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Are you sure you want to delete this category?');">Delete</button>
            </form>
        </li>
    @endforeach
</ul>


<!-- Link to Add a New Category -->
<a href="{{ route('categories.create') }}">Add New Category</a>

<!-- Link back to ToDo List -->
<p><a href="{{ route('todos.index') }}">Back to ToDo List</a></p>

</body>
</html>
