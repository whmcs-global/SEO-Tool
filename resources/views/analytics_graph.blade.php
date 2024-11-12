<!-- resources/views/analytics_graph.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Graph</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #0066cc;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ number_format($report['totals']['date_range_0']['totalUsers']) }}</div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ number_format($report['totals']['date_range_0']['newUsers']) }}</div>
                <div class="stat-label">New Users</div>
            </div>
        </div>

        <div class="card">
            <canvas id="analyticsChart"></canvas>
        </div>
    </div>

    <script>
        // Prepare the data
        const data = @json($report['results']);

        // Sort data by date
        data.sort((a, b) => new Date(a.date) - new Date(b.date));

        // Create the chart
        const ctx = document.getElementById('analyticsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => {
                    const date = new Date(item.date);
                    return date.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric'
                    });
                }),
                datasets: [
                    {
                        label: 'Total Users',
                        data: data.map(item => item.totalUsers),
                        borderColor: '#0066cc',
                        backgroundColor: 'rgba(0, 102, 204, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'New Users',
                        data: data.map(item => item.newUsers),
                        borderColor: '#00cc66',
                        backgroundColor: 'rgba(0, 204, 102, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'User Analytics Overview'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    </script>
</body>
</html>
