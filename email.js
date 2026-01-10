emailjs.init("YOUR_EMAILJS_USER_ID");

function sendExpenseSummary(name, email, total) {
  const templateParams = {
    to_name: name,
    to_email: email,
    message: `You've spent ₹${total} this week. Time to check LifeLedger!`
  };

  emailjs.send('your_service_id', 'your_template_id', templateParams)
    .then(response => console.log("Email sent!", response))
    .catch(error => console.error("Email failed", error));
}
