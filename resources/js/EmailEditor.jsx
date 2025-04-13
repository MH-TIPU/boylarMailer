import React, { useRef } from 'react';
import ReactDOM from 'react-dom';
import EmailEditor from 'react-email-editor';

const EmailEditorComponent = () => {
    const emailEditorRef = useRef(null);

    const exportHtml = () => {
        emailEditorRef.current.editor.exportHtml((data) => {
            const { design, html } = data;
            console.log('exportHtml', html);
            alert('HTML exported! Check the console for details.');
        });
    };

    return (
        <div>
            <button onClick={exportHtml}>Export HTML</button>
            <EmailEditor ref={emailEditorRef} />
        </div>
    );
};

export default EmailEditorComponent;

if (document.getElementById('email-editor')) {
    ReactDOM.render(<EmailEditorComponent />, document.getElementById('email-editor'));
}