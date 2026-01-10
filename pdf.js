document.getElementById('downloadPdf').onclick = function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.text("LifeLedger Expense Report", 10, 10);
    doc.text("Total Spent: ₹" + document.getElementById("totalSpent").innerText, 10, 20);
    doc.save("LifeLedger_Report.pdf");
  };
  