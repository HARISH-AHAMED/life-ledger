<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

include("db.php");
include("functions.php");

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT monthly_limit FROM users WHERE id = $user_id");
$row = $result->fetch_assoc();
$monthly_limit = $row['monthly_limit'];
$show_limit_modal = is_null($monthly_limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeLedger - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<!-- Custom Modal -->
<!-- <div id="monthlyLimitModal" class="custom-modal">
  <div class="custom-modal-content">
    <form id="limitForm" method="POST" action="set_limit.php">
      <div class="custom-modal-header">
        <h5 class="modal-title">Set Your Monthly Expense Limit</h5>
        <span class="close-btn" onclick="closeModal()">&times;</span>
      </div>
      <div class="custom-modal-body">
        <input type="number" step="0.01" name="monthly_limit" class="form-control" placeholder="Enter your monthly limit" required>
      </div>
      <div class="custom-modal-footer">
        <button type="submit" class="btn btn-primary">Save Limit</button>
      </div>
    </form>
  </div>
</div> -->



<!-- Navbar -->
<nav class="navbar navbar-expand-sm navbar-dark bg-danger px-3">
    <a class="navbar-brand" href="#">LifeLedger</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <div class="navbar-nav">
            <a class="nav-link" href="index.html">Home</a>
            <a class="nav-link active" href="dashboard.php">Dashboard</a>
            <a class="nav-link" href="settings.php">Settings</a>
            <a class="nav-link" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<!-- Dashboard Content -->
<div class="container my-5 dashboard-content">

    <!-- Welcome Section -->
    <div class="mb-5 p-4 bg-white rounded shadow-sm">
        <h2>Welcome, <?php echo $_SESSION['user_name']; ?>!</h2>
        <p>Email: <?php echo $_SESSION['user_email']; ?></p>
    </div>

    <!-- Expense Section -->
    <div class="mb-5 p-4 bg-white rounded shadow-sm">
        <h3>Expense Tracking</h3>
        <form action="add_expense.php" method="POST" class="row g-3 mt-3">
            <div class="col-md-4">
                <input type="text" name="expense_description" class="form-control" placeholder="Description" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="expense_amount" class="form-control" placeholder="Amount" required>
            </div>
            <div class="col-md-3">
                <input type="date" name="expense_date" class="form-control" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-danger w-100">Add Expense</button>
            </div>
        </form>

        <h4 class="mt-5 text-light">Current Expenses</h4>
        <div class="table-responsive mt-3">
            <table class="table table-dark table-bordered table-hover expense-table">
                <thead class="table-danger text-dark">
                    <tr>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $expenses = getExpenses($user_id);
                foreach ($expenses as $expense) {
                    echo "<tr>
                            <td>{$expense['description']}</td>
                            <td>\${$expense['amount']}</td>
                            <td>{$expense['date']}</td>
                            <td><a href='#' class='btn btn-sm btn-outline-danger delete-btn' data-id='{$expense['id']}'>Delete</a></td>
                        </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Budget Overview -->
    <div class="mb-5 p-4 bg-white rounded shadow-sm">
        <h3>Your Budget Overview</h3>
        <?php
        $total_expenses = getTotalExpenses($user_id);
        $remaining_budget = $monthly_limit - $total_expenses;
        ?>
        <p><strong>Monthly Expense Limit:</strong> $<?php echo $monthly_limit; ?></p>
        <p><strong>Total Expenses:</strong> $<?php echo $total_expenses; ?></p>
        <p><strong>Remaining Budget:</strong> $<?php echo $remaining_budget; ?></p>
    </div>

    <!-- Chart Section -->
    <div class="mb-5 p-4 bg-white rounded shadow-sm">
        <h3>Expense Overview (Graph)</h3>
        <div class="chart-wrapper" style="height: 400px;">
            <canvas id="expenseChart"></canvas>
        </div>
        <script>
            const ctx = document.getElementById('expenseChart').getContext('2d');
            const expenseChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Monthly Limit', 'Expenses', 'Remaining'],
                    datasets: [{
                        label: 'Budget Overview',
                        data: [<?php echo $monthly_limit; ?>, <?php echo $total_expenses; ?>, <?php echo $remaining_budget; ?>],
                        backgroundColor: ['rgba(75,192,192,0.2)', 'rgba(255,99,132,0.2)', 'rgba(153,102,255,0.2)'],
                        borderColor: ['rgba(75,192,192,1)', 'rgba(255,99,132,1)', 'rgba(153,102,255,1)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    </div>

    <!-- Email Summary -->
    <div class="mb-5 p-4 bg-white rounded shadow-sm">
        <h3>Receive Weekly Email Summary</h3>
        <form action="send_weekly_email.php" method="POST" class="row g-3 mt-3">
            <div class="col-md-9">
                <input type="email" name="user_email" class="form-control" value="<?php echo $_SESSION['user_email']; ?>" readonly>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success w-100">Subscribe</button>
            </div>
        </form>
    </div>

</div>

<!-- Modal Trigger -->
<?php if ($monthly_limit == 0): ?>
    <script>
      window.addEventListener('DOMContentLoaded', () => {
        const myModal = document.getElementById('monthlyLimitModal');
        if (myModal) {
          myModal.style.display = 'flex'; // Show the custom modal
        }
      });
    </script>
<?php endif; ?>


<!-- Delete Button AJAX -->
<script>
document.querySelectorAll(".delete-btn").forEach(button => {
    button.addEventListener("click", (e) => {
        e.preventDefault();
        const expenseId = button.getAttribute("data-id");
        if (confirm("Are you sure you want to delete this expense?")) {
            fetch("delete_expense.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "expense_id=" + encodeURIComponent(expenseId)
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    button.closest("tr").remove();
                }
            })
            .catch(err => {
                console.error("Error:", err);
            });
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
