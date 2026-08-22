const clientLogoUploadForm = document.querySelector('[data-client-logo-upload]');

if (clientLogoUploadForm) {
    const fileInput = clientLogoUploadForm.querySelector('[data-client-logo-files]');
    const selection = clientLogoUploadForm.querySelector('[data-client-logo-selection]');
    const progress = clientLogoUploadForm.querySelector('[data-client-logo-progress]');
    const progressTrack = progress.querySelector('.admin-upload-track');
    const progressBar = progress.querySelector('[data-client-logo-progress-bar]');
    const status = progress.querySelector('[data-client-logo-status]');
    const count = progress.querySelector('[data-client-logo-count]');
    const detail = progress.querySelector('[data-client-logo-detail]');
    const error = progress.querySelector('[data-client-logo-error]');
    const submitButton = clientLogoUploadForm.querySelector('[data-client-logo-submit]');
    const csrfToken = clientLogoUploadForm.querySelector('input[name="_token"]').value;
    const maxFiles = 1000;
    const maxFileBytes = 4 * 1024 * 1024;
    const maxBatchFiles = 8;
    const acceptedTypes = ['image/jpeg', 'image/png', 'image/webp'];

    const formatBytes = bytes => new Intl.NumberFormat('it-IT', {
        style: 'unit',
        unit: bytes >= 1024 * 1024 ? 'megabyte' : 'kilobyte',
        unitDisplay: 'short',
        maximumFractionDigits: 1,
    }).format(bytes / (bytes >= 1024 * 1024 ? 1024 * 1024 : 1024));

    const selectedFiles = () => Array.from(fileInput.files || []);

    function updateSelection() {
        const files = selectedFiles();
        const totalBytes = files.reduce((total, file) => total + file.size, 0);
        selection.textContent = files.length
            ? `${files.length} file selezionati · ${formatBytes(totalBytes)}`
            : 'Nessun file selezionato.';
    }

    function validateFiles(files) {
        if (!files.length) {
            throw new Error('Seleziona almeno un logo.');
        }

        if (files.length > maxFiles) {
            throw new Error(`Puoi caricare al massimo ${maxFiles} loghi per volta.`);
        }

        const invalidFile = files.find(file => file.size > maxFileBytes || !acceptedTypes.includes(file.type));

        if (invalidFile) {
            throw new Error(`${invalidFile.name} non è JPEG, PNG o WebP oppure supera 4 MB.`);
        }
    }

    function setProgress(percentage, completed, total, uploadedBytes, totalBytes) {
        progressBar.style.width = `${percentage}%`;
        progressTrack.setAttribute('aria-valuenow', String(percentage));
        count.textContent = `${completed} / ${total}`;
        detail.textContent = `${percentage}% · ${formatBytes(uploadedBytes)} di ${formatBytes(totalBytes)}`;
    }

    function uploadBatch(files, onProgress) {
        return new Promise((resolve, reject) => {
            const request = new XMLHttpRequest();
            const formData = new FormData();
            formData.append('_token', csrfToken);
            files.forEach(file => formData.append('logos[]', file, file.name));

            request.open('POST', clientLogoUploadForm.action);
            request.setRequestHeader('Accept', 'application/json');
            request.responseType = 'json';
            request.upload.addEventListener('progress', event => {
                if (event.lengthComputable) {
                    onProgress(event.loaded);
                }
            });
            request.addEventListener('load', () => {
                if (request.status >= 200 && request.status < 300) {
                    resolve(request.response || {});
                    return;
                }

                const messages = Object.values(request.response?.errors || {}).flat();
                reject(new Error(messages[0] || request.response?.message || 'Caricamento non riuscito.'));
            });
            request.addEventListener('error', () => reject(new Error('Connessione interrotta durante il caricamento.')));
            request.send(formData);
        });
    }

    fileInput.addEventListener('change', updateSelection);

    clientLogoUploadForm.addEventListener('submit', async event => {
        event.preventDefault();

        const files = selectedFiles();

        try {
            validateFiles(files);
        } catch (validationError) {
            progress.hidden = false;
            error.textContent = validationError.message;
            error.hidden = false;
            return;
        }

        const totalBytes = files.reduce((total, file) => total + file.size, 0);
        let uploadedBytes = 0;
        let uploadedFiles = 0;

        progress.hidden = false;
        error.hidden = true;
        submitButton.disabled = true;
        fileInput.disabled = true;
        status.textContent = 'Caricamento in corso';
        setProgress(0, 0, files.length, 0, totalBytes);

        try {
            for (let index = 0; index < files.length; index += maxBatchFiles) {
                const batch = files.slice(index, index + maxBatchFiles);
                const batchBytes = batch.reduce((total, file) => total + file.size, 0);

                await uploadBatch(batch, currentBytes => {
                    const currentTotal = uploadedBytes + currentBytes;
                    const percentage = Math.min(99, Math.round((currentTotal / totalBytes) * 100));
                    setProgress(percentage, uploadedFiles, files.length, currentTotal, totalBytes);
                });

                uploadedBytes += batchBytes;
                uploadedFiles += batch.length;
                setProgress(
                    Math.round((uploadedBytes / totalBytes) * 100),
                    uploadedFiles,
                    files.length,
                    uploadedBytes,
                    totalBytes,
                );
            }

            status.textContent = 'Caricamento completato';
            window.location.reload();
        } catch (uploadError) {
            status.textContent = 'Caricamento interrotto';
            error.textContent = `${uploadError.message} I loghi già completati sono stati salvati.`;
            error.hidden = false;
            submitButton.disabled = false;
            fileInput.disabled = false;
        }
    });

    updateSelection();
}
