const clientForm = document.querySelector('[data-client-form]');

if (clientForm) {
    const fileInput = clientForm.querySelector('[data-gallery-files]');
    const uploader = clientForm.querySelector('[data-gallery-uploader]');
    const submitButton = clientForm.querySelector('[data-client-submit]');
    const progressTrack = clientForm.querySelector('.admin-upload-track');
    const progressBar = clientForm.querySelector('[data-upload-progress]');
    const title = clientForm.querySelector('[data-upload-title]');
    const count = clientForm.querySelector('[data-upload-count]');
    const detail = clientForm.querySelector('[data-upload-detail]');
    const error = clientForm.querySelector('[data-upload-error]');
    const pauseButton = clientForm.querySelector('[data-upload-pause]');
    const resumeButton = clientForm.querySelector('[data-upload-resume]');
    const cancelButton = clientForm.querySelector('[data-upload-cancel]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const maxFiles = 1000;
    const maxFileBytes = 4 * 1024 * 1024;
    const maxBatchFiles = 8;
    const maxBatchBytes = 32 * 1024 * 1024;
    let files = [];
    let uploadSession = null;
    let paused = false;
    let activeRequest = null;
    let running = false;

    const formatBytes = bytes => new Intl.NumberFormat('it-IT', {
        style: 'unit',
        unit: bytes >= 1024 * 1024 ? 'megabyte' : 'kilobyte',
        unitDisplay: 'short',
        maximumFractionDigits: 1,
    }).format(bytes / (bytes >= 1024 * 1024 ? 1024 * 1024 : 1024));

    const digest = async value => {
        const data = new TextEncoder().encode(value);
        const hash = await crypto.subtle.digest('SHA-256', data);
        return Array.from(new Uint8Array(hash), byte => byte.toString(16).padStart(2, '0')).join('');
    };

    async function prepareFiles() {
        const selected = Array.from(fileInput.files || []).sort((a, b) => {
            const first = a.webkitRelativePath || a.name;
            const second = b.webkitRelativePath || b.name;
            return first.localeCompare(second, 'it', { numeric: true }) || a.size - b.size || a.lastModified - b.lastModified;
        });

        if (selected.length > maxFiles) {
            throw new Error(`Puoi caricare al massimo ${maxFiles} immagini per volta.`);
        }

        const invalid = selected.find(file => file.size > maxFileBytes || !['image/jpeg', 'image/png', 'image/webp'].includes(file.type));

        if (invalid) {
            throw new Error(`${invalid.name} non è JPEG, PNG o WebP oppure supera 4 MB.`);
        }

        files = [];

        for (let index = 0; index < selected.length; index += 1) {
            const file = selected[index];
            const fingerprint = await digest([
                file.webkitRelativePath || file.name,
                file.size,
                file.lastModified,
                file.type,
                index,
            ].join('\u0000'));
            files.push({ file, fingerprint, position: index });
        }
    }

    function showUploader() {
        uploader.hidden = false;
        error.hidden = true;
    }

    function showError(message) {
        showUploader();
        error.textContent = message;
        error.hidden = false;
        title.textContent = 'Caricamento interrotto';
    }

    function updateProgress(uploadedBytes = 0, currentBytes = 0, bytesPerSecond = 0) {
        const totalBytes = uploadSession?.expected_bytes || files.reduce((total, entry) => total + entry.file.size, 0);
        const completed = uploadSession?.uploaded_files || 0;
        const percentage = totalBytes ? Math.min(100, Math.round(((uploadedBytes + currentBytes) / totalBytes) * 100)) : 0;
        progressBar.style.width = `${percentage}%`;
        progressTrack.setAttribute('aria-valuenow', String(percentage));
        count.textContent = `${completed} / ${files.length}`;
        const speed = bytesPerSecond > 0 ? ` · ${formatBytes(bytesPerSecond)}/s` : '';
        detail.textContent = `${percentage}% · ${formatBytes(uploadedBytes + currentBytes)} di ${formatBytes(totalBytes)}${speed}`;
    }

    function renderValidationErrors(payload) {
        let container = clientForm.querySelector('[data-form-errors]');

        if (!container) {
            container = document.createElement('div');
            container.className = 'admin-errors';
            container.dataset.formErrors = '';
            clientForm.querySelector('.admin-actions').before(container);
        }

        const messages = Object.values(payload.errors || {}).flat();
        container.replaceChildren(...messages.map(message => {
            const paragraph = document.createElement('p');
            paragraph.textContent = message;
            return paragraph;
        }));
        container.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    async function saveMetadata() {
        title.textContent = 'Salvataggio dati del lavoro';
        const formData = new FormData(clientForm);
        formData.set('defer_notification', '1');
        const response = await fetch(clientForm.action, {
            method: 'POST',
            headers: { Accept: 'application/json' },
            body: formData,
        });
        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            if (response.status === 422) {
                renderValidationErrors(payload);
            }
            throw new Error(payload.message || 'Non è stato possibile salvare i dati del lavoro.');
        }

        if (!clientForm.querySelector('input[name="_method"]')) {
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'PUT';
            clientForm.append(method);
            clientForm.action = payload.redirect_url;
            history.replaceState({}, '', payload.redirect_url);
        }

        return payload;
    }

    async function openSession(metadata) {
        title.textContent = 'Preparazione della gallery';
        const manifest = files.map(entry => ({
            fingerprint: entry.fingerprint,
            name: entry.file.name,
            size: entry.file.size,
            position: entry.position,
        }));
        const response = await fetch(metadata.upload_url, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                manifest,
                notification_requested: metadata.notification_requested,
            }),
        });
        const payload = await response.json().catch(() => ({}));

        if (response.status === 409 && payload.session) {
            uploadSession = payload.session;
            cancelButton.hidden = false;
            throw new Error(payload.message);
        }

        if (!response.ok) {
            throw new Error(payload.message || 'Non è stato possibile avviare il caricamento.');
        }

        uploadSession = payload;
        cancelButton.hidden = false;
        updateProgress(uploadSession.uploaded_bytes);
    }

    function nextBatch() {
        const uploaded = new Set(uploadSession.uploaded_fingerprints || []);
        const pending = files.filter(entry => !uploaded.has(entry.fingerprint));
        const batch = [];
        let bytes = 0;

        for (const entry of pending) {
            if (batch.length >= maxBatchFiles || (batch.length && bytes + entry.file.size > maxBatchBytes)) {
                break;
            }
            batch.push(entry);
            bytes += entry.file.size;
        }

        return batch;
    }

    function sendBatch(batch) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            batch.forEach(entry => {
                formData.append('files[]', entry.file, entry.file.name);
                formData.append('fingerprints[]', entry.fingerprint);
            });

            const xhr = new XMLHttpRequest();
            const startedAt = performance.now();
            activeRequest = xhr;
            xhr.open('POST', uploadSession.batch_url);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.upload.addEventListener('progress', event => {
                if (event.lengthComputable) {
                    const elapsedSeconds = Math.max(0.1, (performance.now() - startedAt) / 1000);
                    updateProgress(uploadSession.uploaded_bytes, event.loaded, event.loaded / elapsedSeconds);
                }
            });
            xhr.addEventListener('load', () => {
                activeRequest = null;
                let payload = {};
                try {
                    payload = JSON.parse(xhr.responseText || '{}');
                } catch (exception) {
                    reject(new Error('Il server ha restituito una risposta non valida.'));
                    return;
                }
                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve(payload);
                } else {
                    reject(new Error(payload.message || 'Un batch non è stato caricato.'));
                }
            });
            xhr.addEventListener('error', () => {
                activeRequest = null;
                reject(new Error('Connessione interrotta durante il caricamento.'));
            });
            xhr.addEventListener('abort', () => {
                activeRequest = null;
                reject(new DOMException('Caricamento in pausa.', 'AbortError'));
            });
            xhr.send(formData);
        });
    }

    const wait = milliseconds => new Promise(resolve => setTimeout(resolve, milliseconds));

    async function uploadBatchWithRetry(batch) {
        let lastError;

        for (let attempt = 1; attempt <= 3; attempt += 1) {
            try {
                return await sendBatch(batch);
            } catch (exception) {
                if (exception.name === 'AbortError' || paused) {
                    throw exception;
                }
                lastError = exception;
                title.textContent = `Nuovo tentativo ${attempt} di 3`;
                await wait(attempt * 1500);
            }
        }

        throw lastError;
    }

    async function uploadPending() {
        running = true;
        pauseButton.hidden = false;
        resumeButton.hidden = true;
        title.textContent = 'Caricamento gallery';

        while (!paused) {
            const batch = nextBatch();
            if (!batch.length) {
                break;
            }
            uploadSession = await uploadBatchWithRetry(batch);
            updateProgress(uploadSession.uploaded_bytes);
        }

        running = false;

        if (paused) {
            title.textContent = 'Caricamento in pausa';
            pauseButton.hidden = true;
            resumeButton.hidden = false;
            return;
        }

        title.textContent = 'Finalizzazione gallery';
        const response = await fetch(uploadSession.complete_url, {
            method: 'POST',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });
        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(payload.message || 'Non è stato possibile finalizzare la gallery.');
        }

        progressBar.style.width = '100%';
        progressTrack.setAttribute('aria-valuenow', '100');
        count.textContent = `${files.length} / ${files.length}`;
        title.textContent = 'Gallery completata';
        pauseButton.hidden = true;
        cancelButton.hidden = true;
        fileInput.value = '';
        files = [];

        if (payload.notification_error) {
            error.textContent = payload.notification_error;
            error.hidden = false;
            submitButton.disabled = false;
            return;
        }

        window.location.assign(payload.redirect_url);
    }

    clientForm.addEventListener('submit', async event => {
        if (!fileInput?.files?.length || running) {
            return;
        }

        event.preventDefault();
        showUploader();
        submitButton.disabled = true;
        paused = false;

        try {
            await prepareFiles();
            updateProgress();
            const metadata = await saveMetadata();
            await openSession(metadata);
            await uploadPending();
        } catch (exception) {
            if (exception.name !== 'AbortError') {
                showError(exception.message);
            }
            running = false;
            submitButton.disabled = false;
            if (uploadSession && uploadSession.status === 'active') {
                resumeButton.hidden = false;
                pauseButton.hidden = true;
            }
        }
    });

    fileInput?.addEventListener('change', () => {
        showUploader();
        const selected = Array.from(fileInput.files || []);
        title.textContent = `${selected.length} immagini selezionate`;
        count.textContent = `0 / ${selected.length}`;
        detail.textContent = formatBytes(selected.reduce((total, file) => total + file.size, 0));
    });

    pauseButton?.addEventListener('click', () => {
        paused = true;
        activeRequest?.abort();
    });

    resumeButton?.addEventListener('click', async () => {
        if (!uploadSession || running) {
            return;
        }
        paused = false;
        error.hidden = true;
        submitButton.disabled = true;
        try {
            await uploadPending();
        } catch (exception) {
            showError(exception.message);
            submitButton.disabled = false;
        }
    });

    cancelButton?.addEventListener('click', async () => {
        if (!uploadSession || !window.confirm('Annullare il caricamento e rimuovere i file temporanei?')) {
            return;
        }
        paused = true;
        activeRequest?.abort();
        const response = await fetch(uploadSession.cancel_url, {
            method: 'DELETE',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });
        if (response.ok) {
            uploadSession = null;
            uploader.hidden = true;
            submitButton.disabled = false;
        }
    });

    window.addEventListener('beforeunload', event => {
        if (running) {
            event.preventDefault();
            event.returnValue = '';
        }
    });
}
