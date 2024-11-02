<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
</head>
<body>

<h1>Edit Category</h1>

<!-- Edit Category Form -->
<form action="{{ route('categories.update', $category->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label for="name">Category Name:</label>
    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required><br><br>

    <button type="submit">Update Category</button>
</form>

<!-- Link back to Categories list -->
<p><a href="{{ route('categories.index') }}">Back to Categories</a></p>

</body>
</html>
