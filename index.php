<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    function toggleCoupleField() {
        const registrationType = document.getElementById("registration_type").value;
        const coupleField = document.getElementById("couple_field");
        if (registrationType === "couple") {
            coupleField.classList.remove("hidden");
        } else {
            coupleField.classList.add("hidden");
        }
    }

    function validateForm() {
        const name = document.getElementById("name").value;
        const email = document.getElementById("email").value;
        const phone = document.getElementById("phone").value;

        if (!name || !email || !phone) {
            alert("Please fill out all required fields.");
            return false;
        }

        // Basic email validation
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            alert("Please enter a valid email address.");
            return false;
        }

        // Basic phone number validation (adjust as needed)
        const phonePattern = /^\d{11}$/; // Assumes 11-digit phone number
        if (!phonePattern.test(phone)) {
            alert("Please enter a valid 11-digit phone number.");
            return false;
        }

        return true;
    }

    document.querySelector("form").addEventListener("submit", function() {
        const button = document.getElementById("submitButton");
        button.disabled = true;
        button.innerHTML = "Registering...";
    });
    </script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h3 class="text-2xl font-bold mb-3 text-center">THE GREAT COMMISSION MINISTRIES</h3>
        <h6 class="text-1xl font-bold mb-1 text-center">The Lost Mandate Premiere Attendance Registration Form</h6>
        <form action="register.php" method="post" onsubmit="return validateForm()">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700" aria-required="true">Name:</label>
                <input type="text" id="name" name="name" required aria-required="true"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div id="couple_field" class="mb-4 hidden">
                <label for="name2" class="block text-sm font-medium text-gray-700">Partner's Name:</label>
                <input type="text" id="name2" name="name2"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700" aria-required="true">Email:</label>
                <input type="email" id="email" name="email" required aria-required="true"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700" aria-required="true">Phone:</label>
                <input type="text" id="phone" name="phone" required aria-required="true"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="mb-4">
                <label for="event" class="block text-sm font-medium text-gray-700">Event:</label>
                <select id="event" name="event" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="The Lost Mandate - Premiere">The Lost Mandate - Premiere</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="registration_type" class="block text-sm font-medium text-gray-700">Registration
                    Type:</label>
                <select id="registration_type" name="registration_type" required onchange="toggleCoupleField()"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="single">Single (₦3,000)</option>
                    <option value="couple">Couple (₦5,000)</option>
                </select>
            </div>

            <button type="submit" id="submitButton"
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Register
            </button>

            <button type="reset"
                class="w-full bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 mt-4">
                Reset
            </button>

            <!-- <p class="text-sm text-gray-600 mt-4">
                By registering, you agree to our <a href="/privacy-policy"
                    class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>.
            </p> -->

            <br>
            <i>
                <p>For help, please contact: 08102745651, 08034370707</p>
            </i>
        </form>
    </div>
</body>

</html>