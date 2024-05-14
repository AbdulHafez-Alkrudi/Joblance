<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $name }}'s CV</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        header {
            text-align: center;
        }
        .section {
            margin-top: 20px;
        }
        .section-title {
            font-size: 20px;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Curriculum Vitae</h1>
        </header>
        <section class="personal-info section">
            <h2 class="section-title">Personal Information</h2>
            <p><strong>Name:</strong> {{ $name }}</p>
            <p><strong>Email:</strong> {{ $email }}</p>
            <!-- Add more personal details here -->
        </section>
        <section class="education section">
            <h2 class="section-title">Education</h2>
                <p><strong>{{ $education['degree'] }}</strong> - {{ $education['institution'] }} ({{ $education['year'] }})</p>
        </section>
        <!-- Add more sections like 'Work Experience', 'Skills', etc. -->
    </div>
</body>
</html>
