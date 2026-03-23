
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Local SQL Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 4px;
        }

        .console-text {
            font-family: 'Courier New', Courier, monospace;
        }
    </style>
</head>

<body class="bg-[#1e1e1e] text-gray-200 h-screen overflow-hidden">

    <div class="flex flex-col h-full">
        <div class="flex h-[60%] border-b border-[#333]">
            <div class="w-[70%] flex flex-col p-2 border-r border-[#333]">
                <div class="flex justify-between items-center mb-2 px-1">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">SQL Query Editor</span>
                    <button onclick="runQuery()"
                        class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-1 px-4 rounded transition">
                        Run (Cmd+Enter)
                    </button>
                </div>
                <textarea id="sqlInput"
                    class="flex-1 bg-[#121212] p-4 text-green-400 border border-[#444] rounded outline-none focus:border-blue-500 resize-none console-text"
                    placeholder="SELECT * FROM table_name;"></textarea>
            </div>

            <div class="w-[30%] flex flex-col p-2 bg-[#151515]">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 px-1">Console Output</span>
                <div id="consoleLog"
                    class="flex-1 overflow-y-auto p-2 border border-[#333] rounded bg-black text-sm console-text">
                    <div class="text-gray-600">> System ready...</div>
                </div>
            </div>
        </div>

        <div class="h-[40%] flex flex-col p-2 bg-[#1a1a1a]">
            <div class="flex justify-between items-center mb-2 px-1">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Result Set</span>
                <button id="exportBtn" onclick="exportToExcel()" style="display: none;"
                    class="bg-green-700 hover:bg-green-600 text-white text-[10px] font-bold py-1 px-3 rounded transition uppercase">
                    Export Excel (.xlsx)
                </button>
            </div>
            <div id="tableWrapper" class="flex-1 overflow-auto border border-[#333] rounded bg-[#121212]">
                <table id="resultTable" class="w-full text-left text-sm border-collapse">
                    <thead class="sticky top-0 bg-[#252525] text-gray-400 uppercase text-[10px]">
                        <tr id="tableHead"></tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-[#222]"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Store data globally for export
        let currentData = [];

        document.getElementById('sqlInput').addEventListener('keydown', function (e) {
            if ((e.metaKey || e.ctrlKey) && e.keyCode == 13) runQuery();
        });

        async function runQuery() {
            const query = document.getElementById('sqlInput').value;
            const log = document.getElementById('consoleLog');
            const exportBtn = document.getElementById('exportBtn');

            log.innerHTML += `<div class="text-blue-400 mt-2">Executing query...</div>`;
            exportBtn.style.display = 'none'; // Hide export until success

            try {
                const response = await fetch("/sql-execute", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ query: query })
                });

                const res = await response.json();

                // if (response.ok) {
                //     log.innerHTML += `<div class="text-green-500">> Success: ${res.message}</div>`;
                //     currentData = res.data; // Store for export
                //     renderData(res.data);
                //     if (res.data.length > 0) exportBtn.style.display = 'block';
                // } 
                if (response.ok) {
                    log.innerHTML += `<div class="text-green-500">> Success: ${res.message}</div>`;
                    currentData = res.data; // Full data for Excel
                    renderData(res.display_data); // Optimized data for Table
                    if (res.data.length > 0) exportBtn.style.display = 'block';
                } else {
                    log.innerHTML += `<div class="text-red-500">> Error: ${res.message}</div>`;
                }
            } catch (err) {
                log.innerHTML += `<div class="text-red-500">> Fatal: ${err.message}</div>`;
            }
            log.scrollTop = log.scrollHeight;
        }

        function renderData(data) {
            const head = document.getElementById('tableHead');
            const body = document.getElementById('tableBody');
            head.innerHTML = '';
            body.innerHTML = '';

            if (!data || data.length === 0) {
                body.innerHTML = '<tr><td class="p-4 text-gray-500">No results found.</td></tr>';
                return;
            }

            Object.keys(data[0]).forEach(key => {
                head.innerHTML += `<th class="p-2 border-r border-[#333] font-medium">${key}</th>`;
            });

            data.forEach(row => {
                let rowHtml = '<tr>';
                Object.values(row).forEach(val => {
                    rowHtml += `<td class="p-2 border-r border-[#222] text-gray-400">${val === null ? '<span class="text-gray-600">NULL</span>' : val}</td>`;
                });
                rowHtml += '</tr>';
                body.innerHTML += rowHtml;
            });
        }

        // Excel Export Function
        function exportToExcel() {
            if (currentData.length === 0) return;

            const ws = XLSX.utils.json_to_sheet(currentData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Query Results");

            // Generate filename with timestamp
            const filename = `query_export_${new Date().getTime()}.xlsx`;
            XLSX.writeFile(wb, filename);
        }
    </script>
</body>

</html>