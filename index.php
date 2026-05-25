<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System - Gateway</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at 10% 20%, #1e1b4b 0%, #090d16 90%);
            color: #ffffff;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            overflow: hidden;
        }

        .gateway-container {
            width: 100%;
            max-width: 900px;
            padding: 20px;
            text-align: center;
        }

        .system-title {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 50%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 0.5px;
        }

        .system-subtitle {
            color: #94a3b8;
            font-size: 18px;
            margin-bottom: 50px;
        }

        .portal-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: inherit;
            display: block;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .portal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.03), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .portal-card:hover::before {
            transform: translateX(100%);
        }

        .portal-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(139, 92, 246, 0.2);
            border-color: rgba(139, 92, 246, 0.4);
        }

        .portal-card.admin-card:hover {
            box-shadow: 0 15px 40px rgba(59, 130, 246, 0.2);
            border-color: rgba(59, 130, 246, 0.4);
        }

        .portal-icon {
            font-size: 56px;
            margin-bottom: 24px;
            display: inline-block;
            transition: transform 0.3s ease;
        }

        .student-card .portal-icon {
            color: #c084fc;
            text-shadow: 0 0 20px rgba(192, 132, 252, 0.4);
        }

        .admin-card .portal-icon {
            color: #60a5fa;
            text-shadow: 0 0 20px rgba(96, 165, 250, 0.4);
        }

        .portal-card:hover .portal-icon {
            transform: scale(1.1);
        }

        .portal-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #ffffff;
        }

        .portal-desc {
            color: #94a3b8;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .btn-enter {
            border: none;
            padding: 12px 35px;
            font-weight: 700;
            border-radius: 12px;
            color: #ffffff;
            transition: all 0.3s ease;
            width: 100%;
        }

        .student-card .btn-enter {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .student-card:hover .btn-enter {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45);
        }

        .admin-card .btn-enter {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .admin-card:hover .btn-enter {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.45);
        }

        /* Ambient glowing background blur nodes */
        .glow-node {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.15;
        }

        .glow-1 {
            width: 300px;
            height: 300px;
            background: #8b5cf6;
            top: 10%;
            left: 15%;
        }

        .glow-2 {
            width: 400px;
            height: 400px;
            background: #3b82f6;
            bottom: 10%;
            right: 15%;
        }
    </style>
</head>
<body>

<div class="glow-node glow-1"></div>
<div class="glow-node glow-2"></div>

<div class="gateway-container">
    <h1 class="system-title">Student Management System</h1>
    <p class="system-subtitle">Choose your access portal to log in and manage or view profiles.</p>

    <div class="row g-4 justify-content-center">
        <!-- Student Card -->
        <div class="col-md-5">
            <a href="student/login.php" class="portal-card student-card">
                <div class="portal-icon">
                    <i class="fa fa-user-graduate"></i>
                </div>
                <div class="portal-name">Student Portal</div>
                <p class="portal-desc">
                    Access your personal grades reports, track your daily class attendance logs, and monitor overall academic performance metrics.
                </p>
                <button class="btn btn-enter">Login as Student</button>
            </a>
        </div>

        <!-- Admin Card -->
        <div class="col-md-5">
            <a href="login.php" class="portal-card admin-card">
                <div class="portal-icon">
                    <i class="fa fa-user-shield"></i>
                </div>
                <div class="portal-name">Admin Portal</div>
                <p class="portal-desc">
                    Manage student databases, register new enrollments, update details, record daily attendance, and assign academic subject marks.
                </p>
                <button class="btn btn-enter">Login as Admin</button>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
