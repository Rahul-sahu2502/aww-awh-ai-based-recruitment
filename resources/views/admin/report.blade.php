<div class="container mt-4">

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">ई-भर्ती रिपोर्ट</h5>
        </div>

        <div class="card-body">

            <pre style="font-family: inherit; white-space: pre-wrap;">
        {{ $outputText }}
                    </pre>

        </div>

        <div class="card-footer text-end">
            <button class="btn btn-success" onclick="copyReport()">
                Copy Report
            </button>
        </div>
    </div>

</div>

<script>
    function copyReport() {
        const text = `{{ $outputText }}`;
        navigator.clipboard.writeText(text);
        alert('Report copied successfully!');
    }
</script>