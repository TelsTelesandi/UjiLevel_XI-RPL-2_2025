<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Design - Aplikasi Ekskul</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Modern Design Styles */
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Modern Navigation */
        .nav-modern {
            background: linear-gradient(90deg, #2c3e50 0%, #3498db 100%);
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .nav-brand {
            font-size: 1.5rem;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .nav-modern .nav-list {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-modern .nav-link {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-modern .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem 0;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: #3498db;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .stat-icon i {
            color: white;
            font-size: 1.5rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* Modern Table */
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .modern-table th {
            background-color: #f8f9fa;
            padding: 1rem;
            font-weight: 600;
            text-align: left;
            border-bottom: 2px solid #e9ecef;
        }

        .modern-table td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .modern-table tr:last-child td {
            border-bottom: none;
        }

        .modern-table tr:hover td {
            background-color: #f8f9fa;
        }

        /* Action Buttons */
        .btn-modern {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }

        .btn-success-modern {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Footer */
        .footer {
            background: #2c3e50;
            color: white;
            padding: 1.5rem 0;
            margin-top: auto;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-links {
            display: flex;
            gap: 1.5rem;
        }

        .footer-link {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: #3498db;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-modern .nav-list {
                flex-direction: column;
                gap: 1rem;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .footer-links {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Navigation -->
        <nav class="nav-modern">
            <div class="container">
                <ul class="nav-list">
                    <li><a href="#" class="nav-brand">Aplikasi Ekskul</a></li>
                    <li><a href="#" class="nav-link">Dashboard</a></li>
                    <li><a href="#" class="nav-link">Events</a></li>
                    <li><a href="#" class="nav-link">Profile</a></li>
                    <li><a href="#" class="nav-link">Logout</a></li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <!-- Stats Grid -->
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i>📊</i>
                        </div>
                        <div class="stat-number">150</div>
                        <div class="stat-label">Total Events</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i>👥</i>
                        </div>
                        <div class="stat-number">1,250</div>
                        <div class="stat-label">Active Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i>✅</i>
                        </div>
                        <div class="stat-number">85%</div>
                        <div class="stat-label">Approval Rate</div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="table-container">
                    <h2 class="mb-4">Recent Events</h2>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Organizer</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Tech Workshop</td>
                                <td>John Doe</td>
                                <td>2024-03-15</td>
                                <td><span class="badge bg-success">Approved</span></td>
                                <td>
                                    <button class="btn-modern btn-primary-modern">View</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Sports Tournament</td>
                                <td>Jane Smith</td>
                                <td>2024-03-20</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>
                                    <button class="btn-modern btn-success-modern">Approve</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Art Exhibition</td>
                                <td>Mike Johnson</td>
                                <td>2024-03-25</td>
                                <td><span class="badge bg-info">In Progress</span></td>
                                <td>
                                    <button class="btn-modern btn-primary-modern">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <div class="footer-copyright">
                        © 2024 Aplikasi Ekskul. All rights reserved.
                    </div>
                    <div class="footer-links">
                        <a href="#" class="footer-link">About</a>
                        <a href="#" class="footer-link">Contact</a>
                        <a href="#" class="footer-link">Privacy Policy</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html> 