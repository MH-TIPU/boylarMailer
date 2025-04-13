<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: {{ $template->name }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .toolbar {
            background-color: #f8f9fa;
            padding: 10px 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .toolbar h1 {
            font-size: 18px;
            margin: 0;
        }
        .toolbar-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background-color: #6e48aa;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-outline {
            background-color: transparent;
            color: #6c757d;
            border: 1px solid #6c757d;
        }
        .preview-container {
            height: calc(100vh - 60px);
            overflow: auto;
        }
        .device-selector {
            display: flex;
            gap: 5px;
            margin-right: 15px;
        }
        .device-btn {
            padding: 5px 10px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .device-btn.active {
            background-color: #e9ecef;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <h1>{{ $template->name }}</h1>
        
        <div class="d-flex align-items-center">
            <div class="device-selector">
                <button class="device-btn active" onclick="changeDevice('desktop')">Desktop</button>
                <button class="device-btn" onclick="changeDevice('tablet')">Tablet</button>
                <button class="device-btn" onclick="changeDevice('mobile')">Mobile</button>
            </div>
            
            <div class="toolbar-actions">
                <a href="{{ route('templates.edit', $template) }}" class="btn btn-primary">Edit Template</a>
                <a href="{{ route('templates.index') }}" class="btn btn-outline">Back to Templates</a>
            </div>
        </div>
    </div>
    
    <div class="preview-container" id="preview-container">
        <iframe id="preview-frame" srcdoc="{{ $template->content }}" style="width: 100%; height: 100%; border: none;"></iframe>
    </div>

    <script>
        function changeDevice(device) {
            const frame = document.getElementById('preview-frame');
            const container = document.getElementById('preview-container');
            const buttons = document.querySelectorAll('.device-btn');
            
            // Reset active state
            buttons.forEach(btn => btn.classList.remove('active'));
            
            // Set active device button
            document.querySelector(`.device-btn[onclick*="${device}"]`).classList.add('active');
            
            // Adjust preview size based on device
            switch(device) {
                case 'desktop':
                    frame.style.width = '100%';
                    container.style.padding = '0';
                    break;
                case 'tablet':
                    frame.style.width = '768px';
                    container.style.padding = '20px';
                    break;
                case 'mobile':
                    frame.style.width = '375px';
                    container.style.padding = '20px';
                    break;
            }
            
            // Center the frame
            container.style.textAlign = device === 'desktop' ? 'left' : 'center';
        }
    </script>
</body>
</html>