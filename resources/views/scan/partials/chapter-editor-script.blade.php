<script>
    if (!window.chapterEditorBound) {
        window.chapterEditorBound = true;

        document.addEventListener('click', function (event) {
            const button = event.target.closest('[data-chapter-action]');
            if (!button) {
                return;
            }

            const editor = button.closest('[data-chapter-editor]');
            const input = editor?.querySelector('[data-chapter-input]');
            const message = editor?.querySelector('[data-chapter-message]');
            if (!editor || !input || !message) {
                return;
            }

            if (button.dataset.chapterAction === 'increment') {
                input.value = (parseFloat(input.value || '0') + 1).toFixed(1);
                return;
            }

            const formData = new FormData();
            formData.append('current_chapter', input.value);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            fetch(editor.dataset.updateUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                message.classList.remove('hidden');
                if (data.success) {
                    message.className = 'mt-2 text-sm text-green-500';
                    message.textContent = 'Chapter updated successfully';
                } else {
                    message.className = 'mt-2 text-sm text-red-500';
                    message.textContent = 'Error updating chapter';
                }

                setTimeout(() => message.classList.add('hidden'), 3000);
            })
            .catch(() => {
                message.classList.remove('hidden');
                message.className = 'mt-2 text-sm text-red-500';
                message.textContent = 'Error updating chapter';
                setTimeout(() => message.classList.add('hidden'), 3000);
            });
        });
    }
</script>
