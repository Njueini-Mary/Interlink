<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Opportunity | Interlink Placement System</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  @include('layouts.navbar')

  <div class="container mx-auto py-8">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
      <h1 class="text-2xl font-bold mb-6">Edit Opportunity</h1>

      @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @if (isset($opportunity))
        <div class="bg-blue-100 text-blue-700 p-4 rounded mb-4">
          <p>Debug Info:</p>
          <ul>
            <li><strong>Opportunity ID:</strong> {{ $opportunity->id }}</li>
            <li><strong>Opportunity Name:</strong> {{ $opportunity->name }}</li>
            <li><strong>Organization ID:</strong> {{ $opportunity->organization_id }}</li>
          </ul>
        </div>
      @endif

    <form action="{{ route('admin.opportunities.update', $opportunity->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
          <label for="title" class="block text-gray-700">Opportunity Title</label>
          <input type="text" id="title" name="title" value="{{ old('title', $opportunity->title) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200" required>
        </div>

        <div class="mb-4">
          <label for="category_id" class="block text-gray-700">Category</label>
          <select id="category_id" name="category_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">
            <option value="">-- Select Category --</option>
            @foreach ($categories as $category)
              <option value="{{ $category->id }}" {{ old('category_id', $opportunity->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-4">
          <label for="description" class="block text-gray-700">Description</label>
          <textarea id="description" name="description" rows="5" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-blue-200">{{ old('description', $opportunity->description) }}</textarea>
        </div>

        <div class="flex justify-end">
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600">Update Opportunity</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>