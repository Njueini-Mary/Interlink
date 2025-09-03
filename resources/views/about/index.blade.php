<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>About Us | Interlink Placement System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    @include('components.navbar')
    <header class="bg-indigo-600 text-white py-6">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold"></h1>
            <p class="text-indigo-100 text-lg">Empowering students, organizations, and universities with a unified platform for work-based and service-based learning opportunities.</p>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">About Us</h2>
            <p class="mb-4">Interlink is a centralized internship and placement management system for Kenyan universities, designed to connect talented students with organizations seeking interns and entry-level talent. Our system streamlines opportunity management, student-organization relationships, applications, and feedback in one integrated solution.</p>
            <p>Whether you're a student looking to showcase your skills or an organization searching for the perfect candidate for your opportunity, our platform offers the tools and features you need to succeed.</p>
        </section>

        <section class="mb-12 grid md:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-indigo-700 mb-4">For Students</h3>
                <ul class="list-disc pl-5 space-y-2 mb-4">
                    <li>Register, create a profile, and showcase your skills and academic background</li>
                    <li>Browse and filter verified internship and attachment opportunities</li>
                    <li>Apply, track your applications, and receive feedback—all in one place</li>
                    <li>Build a portfolio and gain real-world experience for your future career</li>
                </ul>
                <span class="block font-semibold mt-4 mb-2">Key Features</span>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Personalized dashboard</li>
                    <li>Profile and portfolio management</li>
                    <li>Application history and feedback</li>
                    <li>Direct communication with organizations</li>
                </ul>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-indigo-700 mb-4">For Organizations</h3>
                <ul class="list-disc pl-5 space-y-2 mb-4">
                    <li>Create a detailed organization profile</li>
                    <li>Post and manage internship/attachment opportunities</li>
                    <li>Review applications, select candidates, and provide feedback</li>
                    <li>Build a network of reliable student talent for future hiring</li>
                </ul>
                <span class="block font-semibold mt-4 mb-2">Key Features</span>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Opportunity management dashboard</li>
                    <li>Real-time application tracking</li>
                    <li>Access to a diverse pool of students</li>
                    <li>Detailed reporting and analytics</li>
                </ul>
            </div>
        </section>

        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">Why Interlink?</h2>
            <ul class="list-disc pl-5 space-y-2">
                <li>Centralized, digital solution for all stakeholders</li>
                <li>Smart filtering and matching for better outcomes</li>
                <li>Streamlined, transparent application and feedback process</li>
                <li>Modern, user-friendly interface</li>
                <li>Designed for flexibility and scalability</li>
            </ul>
        </section>

        <section class="bg-indigo-50 rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Get Started</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="font-semibold text-lg mb-2">For Students</h3>
                    <ol class="list-decimal pl-5 space-y-2">
                        <li>Register and select "Student" as your role</li>
                        <li>Complete your profile and upload your resume</li>
                        <li>Start browsing and applying for opportunities</li>
                    </ol>
                </div>
                <div>
                    <h3 class="font-semibold text-lg mb-2">For Organizations</h3>
                    <ol class="list-decimal pl-5 space-y-2">
                        <li>Register and select "Organization" as your role</li>
                        <li>Complete your organization profile</li>
                        <li>Post your first opportunity and review applications</li>
                    </ol>
                </div>
            </div>
            <div class="mt-6 text-center">
                <a href="/register" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">Create Your Account Now</a>
            </div>
        </section>
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between">
                <div class="mb-6 md:mb-0">
                    <h3 class="text-xl font-bold mb-2">Interlink Placement System</h3>
                    <p class="text-gray-400">Connecting students, organizations, and universities for a brighter future.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Contact Us</h4>
                    <p class="text-gray-400">support@interlink.com</p>
                    <p class="text-gray-400">+254 (714) 464-999</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                <p>&copy; 2025 Interlink Placement System. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>