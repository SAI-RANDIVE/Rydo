function handleGoogleLogin(googleUser) {
    const profile = googleUser.getBasicProfile();
    const email = profile.getEmail();
    const name = profile.getName();
    const google_id = profile.getId();
    const profile_pic = profile.getImageUrl();

    // Ask user if they are a customer or a driver
    const role = confirm("Are you a driver? Click OK for Driver, Cancel for Customer") ? "driver" : "customer";

    fetch("./backend/auth/google_auth.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `google_id=${google_id}&name=${name}&email=${email}&profile_pic=${profile_pic}&role=${role}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            window.location.href = data.redirect; // Redirect based on role
        } else {
            alert("Google Login Failed!");
        }
    })
    .catch(error => console.error("Error:", error));
}
