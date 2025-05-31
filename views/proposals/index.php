<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Proposal - Event Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-xl font-bold text-gray-800">Event Management System</h1>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="./index.php?action=dashboard" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-home mr-1"></i>Dashboard
                    </a>
                    <a href="./index.php?action=logout" class="text-red-600 hover:text-red-900 px-3 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">Manajemen Proposal</h2>
                    <button onclick="location.href='./index.php?action=submit_proposal'" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Proposal Baru
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex space-x-4">
                    <select id="statusFilter" onchange="filterProposals()" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                    <input type="text" id="searchInput" placeholder="Cari proposal..." 
                           onkeyup="filterProposals()"
                           class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 flex-1">
                </div>
            </div>

            <!-- Proposals List -->
            <div class="px-6 py-4">
                <div id="proposals-list" class="space-y-4">
                    <!-- Proposals will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Preview Modal -->
    <div id="pdfModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="absolute inset-4 bg-white rounded-lg flex flex-col">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold" id="pdfTitle">Preview Proposal</h3>
                <button onclick="closePdfModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex-1 p-4">
                <iframe id="pdfViewer" class="w-full h-full" frameborder="0"></iframe>
            </div>
        </div>
    </div>

    <script>
        let allProposals = [];

        // Function to load proposals
        async function loadProposals() {
            try {
                const response = await fetch('./index.php?action=get_proposals');
                allProposals = await response.json();
                filterProposals();
            } catch (error) {
                console.error('Error loading proposals:', error);
            }
        }

        // Function to filter proposals
        function filterProposals() {
            const statusFilter = document.getElementById('statusFilter').value;
            const searchFilter = document.getElementById('searchInput').value.toLowerCase();
            
            const filteredProposals = allProposals.filter(proposal => {
                const matchesStatus = !statusFilter || proposal.status === statusFilter;
                const matchesSearch = !searchFilter || 
                    proposal.judul_proposal.toLowerCase().includes(searchFilter) ||
                    proposal.deskripsi.toLowerCase().includes(searchFilter);
                return matchesStatus && matchesSearch;
            });

            displayProposals(filteredProposals);
        }

        // Function to display proposals
        function displayProposals(proposals) {
            const proposalsList = document.getElementById('proposals-list');
            proposalsList.innerHTML = '';

            if (proposals.length === 0) {
                proposalsList.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-folder-open text-4xl mb-2"></i>
                        <p>Tidak ada proposal yang ditemukan</p>
                    </div>
                `;
                return;
            }

            proposals.forEach(proposal => {
                const statusColor = {
                    'menunggu': 'bg-yellow-100 text-yellow-800',
                    'disetujui': 'bg-green-100 text-green-800',
                    'ditolak': 'bg-red-100 text-red-800'
                }[proposal.status] || 'bg-gray-100 text-gray-800';

                const proposalCard = `
                    <div class="border rounded-lg p-6 hover:shadow-lg transition-shadow bg-white">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-xl font-semibold text-gray-800">${proposal.judul_proposal}</h3>
                                    <span class="px-3 py-1 rounded-full text-sm ${statusColor}">
                                        ${proposal.status}
                                    </span>
                                </div>
                                <p class="text-gray-600 mt-2">${proposal.deskripsi}</p>
                                <div class="mt-4 grid grid-cols-2 gap-4 text-sm text-gray-500">
                                    <div>
                                        <i class="far fa-calendar mr-2"></i>
                                        <span class="font-medium">Tanggal:</span>
                                        <br>${proposal.tanggal_mulai} - ${proposal.tanggal_selesai}
                                    </div>
                                    <div>
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        <span class="font-medium">Tempat:</span>
                                        <br>${proposal.tempat}
                                    </div>
                                    <div>
                                        <i class="fas fa-users mr-2"></i>
                                        <span class="font-medium">Jumlah Peserta:</span>
                                        <br>${proposal.peserta} orang
                                    </div>
                                    <div>
                                        <i class="fas fa-money-bill-wave mr-2"></i>
                                        <span class="font-medium">Anggaran:</span>
                                        <br>Rp ${Number(proposal.anggaran).toLocaleString('id-ID')}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button onclick="viewPdf(${proposal.id})" 
                                    class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-file-pdf mr-2"></i>Lihat PDF
                            </button>
                        </div>
                    </div>
                `;
                proposalsList.innerHTML += proposalCard;
            });
        }

        // Function to view PDF
        function viewPdf(id) {
            const proposal = allProposals.find(p => p.id === id);
            if (!proposal) return;

            const pdfUrl = `./public/uploads/proposals/${proposal.file_proposal}`;
            document.getElementById('pdfTitle').textContent = proposal.judul_proposal;
            document.getElementById('pdfViewer').src = pdfUrl;
            document.getElementById('pdfModal').classList.remove('hidden');
        }

        // Function to close PDF modal
        function closePdfModal() {
            document.getElementById('pdfModal').classList.add('hidden');
            document.getElementById('pdfViewer').src = '';
        }

        // Load proposals when page loads
        document.addEventListener('DOMContentLoaded', loadProposals);
    </script>
</body>
</html> 