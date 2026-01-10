
const ctx = document.getElementById('expenseChart').getContext('2d');
const expenseChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ['Food', 'Transport', 'Shopping', 'Utilities'],
    datasets: [{
      label: '₹',
      data: [300, 150, 400, 250], // Dynamically pull from PHP/DB
      backgroundColor: ['#f87171', '#34d399', '#60a5fa', '#fbbf24']
    }]
  }
});

