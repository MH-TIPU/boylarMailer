<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template Builder</title>
    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        #gjs {
            height: 100vh;
            border: none;
        }
    </style>
</head>
<body>
    <div id="gjs"></div>
    <div id="email-editor"></div>
    <script src="https://unpkg.com/grapesjs"></script>
    <script type="module" src="/resources/js/EmailEditor.jsx"></script>
    <script>
        console.log('Initializing GrapesJS...');
        const editor = grapesjs.init({
            container: '#gjs',
            height: '100%',
            width: 'auto',
            fromElement: true,
            storageManager: {
                type: 'local',
                autosave: true,
                autoload: true,
                stepsBeforeSave: 1,
            },
            panels: {
                defaults: [
                    {
                        id: 'layers',
                        el: '.panel__right',
                        resizable: true,
                    },
                    {
                        id: 'panel-switcher',
                        el: '.panel__switcher',
                        buttons: [
                            {
                                id: 'show-layers',
                                active: true,
                                label: 'Layers',
                                command: 'show-layers',
                                togglable: false,
                            },
                            {
                                id: 'show-style',
                                label: 'Styles',
                                command: 'show-styles',
                                togglable: false,
                            },
                            {
                                id: 'show-preview',
                                label: 'Preview',
                                command: 'preview',
                                togglable: true,
                            },
                        ],
                    },
                    {
                        id: 'basic-actions',
                        el: '.panel__basic-actions',
                        buttons: [
                            {
                                id: 'save',
                                className: 'btn-save',
                                label: 'Save',
                                command: 'save-template',
                            },
                        ],
                    },
                ],
            },
            blockManager: {
                appendTo: '#blocks',
                blocks: [
                    {
                        id: 'text',
                        label: 'Text',
                        content: '<div>Insert your text here</div>',
                    },
                    {
                        id: 'image',
                        label: 'Image',
                        content: '<img src="https://via.placeholder.com/150" alt="Placeholder Image" />',
                    },
                    {
                        id: 'button',
                        label: 'Button',
                        content: '<button class="btn">Click Me</button>',
                    },
                    {
                        id: 'columns',
                        label: 'Columns',
                        content: '<div class="row"><div class="col">Column 1</div><div class="col">Column 2</div></div>',
                    },
                    {
                        id: 'header',
                        label: 'Header',
                        content: '<header><h1>Header</h1></header>',
                    },
                    {
                        id: 'footer',
                        label: 'Footer',
                        content: '<footer><p>Footer</p></footer>',
                    },
                ],
            },
        });

        // Add save command
        editor.Commands.add('save-template', {
            run(editor) {
                const html = editor.getHtml();
                const css = editor.getCss();
                console.log('HTML:', html);
                console.log('CSS:', css);
                alert('Template saved! Check the console for HTML and CSS.');
            },
        });

        console.log('GrapesJS initialized successfully.');
    </script>
</body>
</html>