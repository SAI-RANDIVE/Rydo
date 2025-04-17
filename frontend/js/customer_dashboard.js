document.addEventListener('DOMContentLoaded', function() {
    const customerName = localStorage.getItem('customerName') || "User";
    document.getElementById('customer-name').innerText = customerName;
});

function bookRide() {
    document.getElementById('ride-status').innerHTML = "<p>Searching for nearby drivers...</p>";

    setTimeout(() => {
        document.getElementById('ride-status').innerHTML = "<p>Driver found! Your ride is on the way.</p>";
    }, 3000);
}
