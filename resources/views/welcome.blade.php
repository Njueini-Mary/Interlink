<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Interlink Placement System</title>
</head>
<body>

    @include('components.navbar')

    <!-- Hero Section for Demo -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Connecting Students with Internship & Placement Opportunities</h1>
                <p class="hero-subtitle">Find the right opportunity for your skills, or discover talented students for your organization.</p>
                <div class="hero-buttons">
                    <a href="/about" class="btn btn-primary">How It Works</a>
                    <a href="/opportunities" class="btn btn-outline">Available Opportunities</a>
                </div>
                <img src="\images\75532.jpg" alt="Freelancing platform" class="hero-image">
            </div>
        </div>
    </section>

    @include('components.footer')
    
</body>
</html>