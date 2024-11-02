<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Category</title>
</head>
<body>

<h1>Add New Category</h1>

<!-- Form to add a new category -->
<form action="{{ route('categories.store') }}" method="POST">
    @csrf
    <label for="name">Category Name:</label>
    <input type="text" id="name" name="name" required><br><br>

    <button type="submit">Create Category</button>
</form>

<!-- Link to go back to the ToDo list -->
<p><a href="{{ route('todos.index') }}">Back to ToDo List</a></p>

</body>
</html>
