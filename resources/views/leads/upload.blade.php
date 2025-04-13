<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Leads</title>
</head>
<body>
    <h1>Upload Leads via CSV</h1>
    <form action="{{ route('leads.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="csv_file">Choose CSV File:</label>
        <input type="file" name="csv_file" id="csv_file" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>