    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Proposal - Event Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Submit Proposal</h2>
            
            <form action="index.php?action=submit_proposal" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Event</label>
                    <select name="event_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <!-- Event options will be loaded here -->
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Judul Proposal</label>
                    <input type="text" name="judul_proposal" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="deskripsi" required rows="3" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">File Proposal (PDF)</label>
                    <input type="file" name="file_proposal" required accept=".pdf"
                           class="mt-1 block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-md file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100">
                    <p class="mt-1 text-sm text-gray-500">Upload file PDF maksimal 5MB</p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="history.back()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Kembali
                    </button>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Submit Proposal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Load available events for the user
    document.addEventListener('DOMContentLoaded', function() {
        fetch('./index.php?action=get_user_events')
            .then(response => response.json())
            .then(events => {
                const select = document.querySelector('select[name="event_id"]');
                events.forEach(event => {
                    const option = document.createElement('option');
                    option.value = event.event_id;
                    option.textContent = event.judul_event;
                    select.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
    });
    </script>
</body>
</html> 