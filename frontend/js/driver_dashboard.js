document.addEventListener('DOMContentLoaded', function() {
    const driverName = localStorage.getItem('driverName') || "Driver";
    document.getElementById('driver-name').innerText = driverName;
});

function toggleOnlineStatus() {
    document.getElementById('request-list').innerHTML = "<p>Waiting for ride requests...</p>";

    setTimeout(() => {
        document.getElementById('request-list').innerHTML = "<p>New Ride Request! Pickup at XYZ Location.</p>";
    }, 5000);
}
