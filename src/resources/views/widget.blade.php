<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Widget</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem; }
        .widget { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); width: 100%; max-width: 480px; }
        .widget h2 { text-align: center; margin-bottom: 1.5rem; color: #1e293b; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.25rem; font-size: 0.875rem; color: #374151; }
        .form-control { width: 100%; padding: 0.6rem 0.75rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.875rem; }
        .form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        textarea.form-control { resize: vertical; min-height: 100px; }
        .btn { display: block; width: 100%; padding: 0.7rem; background: #3b82f6; color: #fff; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer; }
        .btn:hover { background: #2563eb; }
        .btn:disabled { opacity: 0.6; cursor: not-allowed; }
        .alert { padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; display: none; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .field-error { color: #dc2626; font-size: 0.75rem; margin-top: 0.2rem; }
    </style>
</head>
<body>
    <div class="widget">
        <h2>Contact Us</h2>
        <div id="alert-success" class="alert alert-success">Your request has been submitted successfully!</div>
        <div id="alert-error" class="alert alert-error"></div>
        <form id="widget-form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" class="form-control" required>
                <p class="field-error" data-field="name"></p>
            </div>
            <div class="form-group">
                <label for="phone">Phone (E.164) *</label>
                <input type="tel" id="phone" name="phone" class="form-control" placeholder="+12025551234" required>
                <p class="field-error" data-field="phone"></p>
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-control" required>
                <p class="field-error" data-field="email"></p>
            </div>
            <div class="form-group">
                <label for="subject">Subject *</label>
                <input type="text" id="subject" name="subject" class="form-control" required>
                <p class="field-error" data-field="subject"></p>
            </div>
            <div class="form-group">
                <label for="body">Message *</label>
                <textarea id="body" name="body" class="form-control" required></textarea>
                <p class="field-error" data-field="body"></p>
            </div>
            <div class="form-group">
                <label for="files">Attachments</label>
                <input type="file" id="files" name="files[]" class="form-control" multiple>
                <p class="field-error" data-field="files"></p>
            </div>
            <button type="submit" class="btn" id="submit-btn">Send</button>
        </form>
    </div>

    <script>
        document.getElementById('widget-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn = document.getElementById('submit-btn');
            const successAlert = document.getElementById('alert-success');
            const errorAlert = document.getElementById('alert-error');

            // Reset
            successAlert.style.display = 'none';
            errorAlert.style.display = 'none';
            document.querySelectorAll('.field-error').forEach(el => el.textContent = '');

            btn.disabled = true;
            btn.textContent = 'Sending...';

            const formData = new FormData(this);

            try {
                const response = await fetch('/api/tickets', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    successAlert.style.display = 'block';
                    this.reset();
                } else if (response.status === 422 && data.errors) {
                    for (const [field, messages] of Object.entries(data.errors)) {
                        const errorEl = document.querySelector(`.field-error[data-field="${field}"]`);
                        if (errorEl) {
                            errorEl.textContent = messages[0];
                        }
                    }
                } else {
                    errorAlert.textContent = data.message || 'An error occurred. Please try again.';
                    errorAlert.style.display = 'block';
                }
            } catch (err) {
                errorAlert.textContent = 'Network error. Please try again.';
                errorAlert.style.display = 'block';
            } finally {
                btn.disabled = false;
                btn.textContent = 'Send';
            }
        });
    </script>
</body>
</html>
