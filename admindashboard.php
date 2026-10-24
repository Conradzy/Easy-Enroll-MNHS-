<?php
include 'db_connection.php';

$enrolled = $pdo->query("SELECT COUNT(*) as count FROM studentaccount WHERE enrollmentstatus = 'enrolled'")->fetch()['count'];
$pending = $pdo->query("SELECT COUNT(*) as count FROM studentaccount WHERE enrollmentstatus = 'pending'")->fetch()['count'];
$total = $pdo->query("SELECT COUNT(*) as count FROM studentaccount")->fetch()['count'];

// Chart data for October 2024
$year = 2026;
$month = 2;
$query = "SELECT DAY(date_created) as day, enrollmentstatus, COUNT(*) as count FROM studentaccount WHERE MONTH(date_created) = :month AND YEAR(date_created) = :year GROUP BY day, enrollmentstatus";
$stmt = $pdo->prepare($query);
$stmt->execute(['month' => $month, 'year' => $year]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pendingCounts = array_fill(1, 28, 0);
$enrolledCounts = array_fill(1, 28, 0);

foreach ($results as $row) {
    $day = $row['day'];
    if ($row['enrollmentstatus'] == 'pending') {
        $pendingCounts[$day] = $row['count'];
    } elseif ($row['enrollmentstatus'] == 'enrolled') {
        $enrolledCounts[$day] = $row['count'];
    }
}

$chartData = [['Day', 'Pending', 'Enrolled']];
for ($day = 1; $day <= 28; $day++) {
    $chartData[] = [$day, $pendingCounts[$day], $enrolledCounts[$day]];
}

// Grade level data for Pie Chart from lpi table
$gradeQuery = "SELECT gradelvl_enroll, COUNT(*) as count FROM lpi WHERE gradelvl_enroll IN (7, 8, 9, 10, 11, 12) GROUP BY gradelvl_enroll ORDER BY gradelvl_enroll";
$gradeStmt = $pdo->query($gradeQuery);
$gradeResults = $gradeStmt->fetchAll(PDO::FETCH_ASSOC);

$pieData = [['Grade Level', 'Count']];
foreach ($gradeResults as $row) {
    $pieData[] = ['Grade ' . $row['gradelvl_enroll'], (int)$row['count']];
}

// SHS Strand data for Bar Chart from lshs table
$strandQuery = "SELECT shs_strand, COUNT(*) as count FROM lshs WHERE shs_strand IN ('GAS', 'STEM', 'ICT', 'AFA', 'ABM') GROUP BY shs_strand ORDER BY shs_strand";
$strandStmt = $pdo->query($strandQuery);
$strandResults = $strandStmt->fetchAll(PDO::FETCH_ASSOC);

$barData = [['Strand', 'Count']];
$strandCounts = ['GAS' => 0, 'STEM' => 0, 'ICT' => 0, 'AFA' => 0, 'ABM' => 0];
foreach ($strandResults as $row) {
    $strandCounts[$row['shs_strand']] = (int)$row['count'];
}
foreach ($strandCounts as $strand => $count) {
    $barData[] = [$strand, $count];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="icon" href="adminicons/logomnhs.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      google.charts.setOnLoadCallback(drawPieChart);
      google.charts.setOnLoadCallback(drawBarChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable(<?php echo json_encode($chartData); ?>);

        var options = {
          title: 'Enrollment Status for February 2026',
          curveType: 'function',
          legend: { position: 'bottom' },
          hAxis: {title: 'Day'},
          vAxis: {title: 'Number of Accounts'}
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }

      // Pie Chart for Grade Level Distribution
      function drawPieChart() {
        var pieData = google.visualization.arrayToDataTable(<?php echo json_encode($pieData); ?>);

        var pieOptions = {
          title: 'Grade Level Distribution',
          pieHole: 0.4,
        };

        var pieChart = new google.visualization.PieChart(document.getElementById('donutchart'));
        pieChart.draw(pieData, pieOptions);
      }

      // Bar Chart for SHS Strand Distribution
      function drawBarChart() {
        var barData = google.visualization.arrayToDataTable(<?php echo json_encode($barData); ?>);

        var barOptions = {
          title: 'SHS Strand Distribution',
          hAxis: {title: 'Count', minValue: 0},
          vAxis: {title: 'Strand'}
        };

        var barChart = new google.visualization.BarChart(document.getElementById('barchart'));
        barChart.draw(barData, barOptions);
      }

      // Mini Calendar
      function generateCalendar(elementId) {
        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth(); // 0-11
        const today = now.getDate();

        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const firstDay = new Date(year, month, 1).getDay(); // 0 = Sunday

        let html = '<table class="table table-bordered text-center mb-0">';
        html += '<thead><tr>';
        const weekdays = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        weekdays.forEach(d => html += `<th>${d}</th>`);
        html += '</tr></thead><tbody><tr>';

        // Empty cells before first day
        for (let i = 0; i < firstDay; i++) html += '<td></td>';

        for (let day = 1; day <= daysInMonth; day++) {
            const cellClass = day === today ? 'bg-primary text-white rounded' : '';
            html += `<td class="${cellClass}">${day}</td>`;
            if ((day + firstDay) % 7 === 0) html += '</tr><tr>';
        }

        html += '</tr></tbody></table>';
        document.getElementById(elementId).innerHTML = html;
      }

      // Initialize calendar on page load
      document.addEventListener('DOMContentLoaded', function() {
        generateCalendar('mini-calendar');
      });
    </script>
    <style>
        /* Prevent page scrolling and keep layout within the viewport */
        html,
        body {
            height: 100%;
            
            /* disable page scrollbars */
            background-color: #e7e7e7ff;
            font-family: 'Poppins', sans-serif;
        }

        * {
            font-family: 'Poppins', sans-serif !important;
        }


        .form-section-title {
            font-weight: 600;
            margin-top: 1.5rem;
        }

        .muted-note {
            font-size: .95rem;
            white-space: pre-line;
        }



        #content {
            transition: margin-left 0.3s;
            height: 100vh;
            overflow: auto;
        }

        /* Dashboard layout helpers */
        .dashboard-main-row {
            align-items: stretch;
        }

        .dashboard-card {
            height: 100%;
        }

        /* Slightly reduce vertical padding to lower card height (~15px total) */
        .dashboard-card .card-body {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        @media (min-width: 768px) {
            .kpi-col,
            .stats-col {
                display: flex;
            }
            .kpi-col > .row {
                flex: 1 1 auto;
            }
        }

    </style>
</head>

<body>
    <div class="d-flex">
        <?php include 'sidebar_admin.php'; ?>
       

        <div class="container d-flex flex-column">
            <h2 class="mt-5 m-2" style="font-weight: bold; color: #0a397eff;">Welcome Admin!</h2>
           

            <!-- Cards + Statistics layout -->
            <div class="container mt-4">
                <div class="row g-3 dashboard-main-row">
                    <!-- LEFT: 4 cards in 2x2 inside col-md-5 -->
                    <div class="col-12 col-md-5 kpi-col">
                        <div class="row g-3 w-100">
                            <div class="col-12 col-sm-6">
                                <div class="card dashboard-card h-60">
                                    <div class="card-body">
                                        <h6 class="card-title mt-2 mb-3">Accounts Registered</h6>
                                        <h2 class="mb-0 mt-5"
                                            style="font-weight: bold; font-size: 70px; color:#1d4584;">
                                            <strong><?php echo $total; ?></strong>
                                        </h2>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="card dashboard-card h-60">
                                    <div class="card-body">
                                        <h6 class="card-title mt-2 mb-3">Students Enrolled</h6>
                                        <h2 class="mb-0 mt-5"
                                            style="font-weight: bold; font-size: 70px; color:#1d4584;">
                                            <strong><?php echo $enrolled; ?></strong>
                                        </h2>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="card dashboard-card h-60">
                                    <div class="card-body">
                                        <h6 class="card-title mt-2 mb-3">Pending Enrollees</h6>
                                        <h2 class="mb-0 mt-5"
                                            style="font-weight: bold; font-size: 70px; color:#1d4584;">
                                            <strong><?php echo $pending; ?></strong>
                                        </h2>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="card dashboard-card h-60">
                                    <div class="card-body">
                                        <h6 class="card-title mt-2 mb-3">Rejected Forms</h6>
                                        <h2 class="mb-0 mt-5"
                                            style="font-weight: bold; font-size: 70px; color:#1d4584;">
                                            <strong><?php echo $pending; ?></strong>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: statistics chart inside col-md-7 -->
                    <div class="col-12 col-md-7 stats-col">
                        <div class="card dashboard-card mb-4 w-100">
                            <div class="card-body">
                               
                                <div id="curve_chart" style="width: 100%; height: 386px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Three horizontal cards below the main dashboard -->
            <div class="container mt-4 mb-4">
                <div class="row g-3">
                    <!-- First Card: Calendar -->
                    <div class="col-12 col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title mb-3">Calendar</h6>
                                <div id="mini-calendar" class="border rounded p-3" style="width: 100%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Second Card: Bar Chart (SHS Strand Distribution) -->
                    <div class="col-12 col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div id="barchart" style="width: 100%; height: 300px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Third Card: Donut Chart (Grade Level Distribution) -->
                    <div class="col-12 col-md-5">
                        <div class="card h-100">
                            <div class="card-body">
                                <div id="donutchart" style="width: 100%; height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
    </div>

    <!-- load bootstrap JS (bundle includes Collapse) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</html>
