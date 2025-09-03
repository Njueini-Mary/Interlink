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
            <p class="text-indigo-100 mt-2">Connecting students with internship and placement opportunities</p>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <section class="mb-12">
            <h2 class="text-2xl font-bold mb-4">About Our Platform</h2>
            <p class="mb-4">Interlink is a centralized internship and placement management system for Kenyan universities, designed to connect talented students with organizations seeking interns and entry-level talent. Our system streamlines opportunity management, student-organization relationships, applications, and feedback in one integrated solution.</p>
            <p>Whether you're a student looking to showcase your skills or an organization searching for the perfect candidate for your opportunity, our platform offers the tools and features you need to succeed.</p>
        </section>

        <div class="grid md:grid-cols-2 gap-8">
            <section class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-indigo-700 mb-4">For Students</h2>
                
                <h3 class="font-semibold text-lg mt-6 mb-2">How It Works</h3>
                <ol class="list-decimal pl-5 space-y-2">
                    <li>Create your student profile highlighting your academic background and skills</li>
                    <li>Browse available internship and placement opportunities</li>
                    <li>Apply for opportunities that match your interests</li>
                    <li>Track your applications and manage your profile</li>
                    <li>Receive feedback from organizations</li>
                </ol>

                <h3 class="font-semibold text-lg mt-6 mb-2">Key Features for Students</h3>
                <ul class="list-disc pl-5 space-y-2">
                    <li>Personalized dashboard showing active applications and opportunities</li>
                    <li>Profile management tools for updating your academic and skill information</li>
                    <li>Access to organization information for better communication</li>
                    <li>Application history and feedback tracking</li>
                    <li>Portfolio building for future employment</li>
                </ul>

                <h3 class="font-semibold text-lg mt-6 mb-2">Benefits</h3>
                <ul class="list-disc pl-5 space-y-2">
                    <li>Find relevant internship and placement opportunities</li>
                    <li>Build long-term relationships with reputable organizations</li>
                    <li>Streamlined application and feedback process</li>
                    <li>Build a portfolio and track record for future employment</li>
                </ul>
            </section>

            <section class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-indigo-700 mb-4">For Organizations</h2>
                
                <h3 class="font-semibold text-lg mt-6 mb-2">How It Works</h3>
                <ol class="list-decimal pl-5 space-y-2">
                    <li>Create a detailed organization profile</li>
                    <li>Post your internship and placement opportunities</li>
                    <li>Review applications from qualified students</li>
                    <li>Select and onboard the best candidates</li>
                    <li>Track application progress and provide feedback</li>
                    <li>Build relationships with top student talent</li>
                </ol>

                <h3 class="font-semibold text-lg mt-6 mb-2">Key Features for Organizations</h3>
                <ul class="list-disc pl-5 space-y-2">
                    <li>Intuitive opportunity management dashboard</li>
                    <li>Access to a diverse pool of talented students</li>
                    <li>Real-time application tracking and updates</li>
                    <li>Detailed reports on application progress and feedback</li>
                </ul>

                <h3 class="font-semibold text-lg mt-6 mb-2">Benefits</h3>
                <ul class="list-disc pl-5 space-y-2">
                    <li>Find qualified student candidates quickly and efficiently</li>
                    <li>Reduce recruitment costs and administrative overhead</li>
                    <li>Scale your internship/placement programs as needed</li>
                    <li>Centralized opportunity management and communication</li>
                    <li>Build a network of reliable student talent for future hiring</li>
                </ul>
            </section>
        </div>

        <section class="mt-12 bg-indigo-50 rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Getting Started</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <h3 class="font-semibold text-lg mb-2">For Students</h3>
                    <ol class="list-decimal pl-5 space-y-2">
                        <li>Register for an account and select "Student" as your role</li>
                        <li>Complete your profile with academic details, skills, and resume</li>
                        <li>Set your availability and interests</li>
                        <li>Start browsing and applying for opportunities</li>
                    </ol>
                </div>
                <div>
                    <h3 class="font-semibold text-lg mb-2">For Organizations</h3>
                    <ol class="list-decimal pl-5 space-y-2">
                        <li>Register for an account and select "Organization" as your role</li>
                        <li>Complete your organization profile</li>
                        <li>Create your first opportunity with detailed requirements</li>
                        <li>Begin reviewing applications from students</li>
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
                    <p class="text-gray-400">Your all-in-one platform for student internships and placements</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Contact Us</h4>
                    <p class="text-gray-400">support@freelancehub.com</p>
                    <p class="text-gray-400">1-800-FREELANCE</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                <p>&copy; 2025 Interlink Placement System. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>