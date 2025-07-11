<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
require_once 'config/database.php';

// Track visitor
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

$stmt = $pdo->prepare("INSERT INTO visitors (ip_address, user_agent) VALUES (?, ?)");
$stmt->execute([$ip_address, $user_agent]);

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_posts FROM posts");
$total_posts = $stmt->fetch()['total_posts'];

$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $stmt->fetch()['total_users'];

// Get recent visitors
$stmt = $pdo->query("SELECT * FROM visitors ORDER BY visit_time DESC LIMIT 10");
$recent_visitors = $stmt->fetchAll();

// Get recent posts
$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC LIMIT 5");
$recent_posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CMS Sederhana | Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <!-- FullCalendar CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Custom CSS -->
  <style>
    /* Dark Mode Styles */
    body {
      background-color: #ffffff !important;
      color: #333333 !important;
    }
    
    /* Navbar Dark */
    .main-header {
      background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
      border-bottom: 1px solid #404040 !important;
    }
    
    .navbar-light .navbar-nav .nav-link {
      color: #ffffff !important;
    }
    
    .navbar-light .navbar-nav .nav-link:hover {
      color: #4a90e2 !important;
    }
    
    /* Sidebar Dark */
    .main-sidebar {
      background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%) !important;
    }
    
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link {
      color: #b8c7ce !important;
      background-color: transparent !important;
    }
    
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link:hover {
      color: #ffffff !important;
      background-color: rgba(74, 144, 226, 0.2) !important;
    }
    
    .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
      color: #ffffff !important;
      background-color: #4a90e2 !important;
    }
    
    .brand-link {
      background: rgba(44, 62, 80, 0.8) !important;
      color: #ffffff !important;
    }
    
    .user-panel {
      border-bottom: 1px solid #404040 !important;
    }
    
    .user-panel .info a {
      color: #ffffff !important;
    }
    
    /* Content Wrapper - White Background */
    .content-wrapper {
      background-color: #ffffff !important;
    }
    
    .content-header {
      background-color: #f8f9fa !important;
      border-bottom: 1px solid #dee2e6 !important;
    }
    
    .content-header h1 {
      color: #333333 !important;
    }
    
    /* Cards Dark */
    .card {
      background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
      border: 1px solid #404040 !important;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
    }
    
    .card-header {
      background: rgba(44, 62, 80, 0.8) !important;
      border-bottom: 1px solid #404040 !important;
      color: #ffffff !important;
    }
    
    .card-title {
      color: #ffffff !important;
    }
    
    .card-body {
      background: transparent !important;
      color: #e0e0e0 !important;
    }
    
    /* Welcome Section Dark */
    .welcome-section {
      background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%) !important;
      color: white !important;
      padding: 20px !important;
      border-radius: 10px !important;
      margin-bottom: 20px !important;
      box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3) !important;
    }
    
    /* Info Boxes Dark */
    .small-box {
      background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
      border: 1px solid #404040 !important;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3) !important;
    }
    
    .small-box .inner h3 {
      color: #ffffff !important;
    }
    
    .small-box .inner p {
      color: #b8c7ce !important;
    }
    
    .small-box .icon {
      color: rgba(255, 255, 255, 0.3) !important;
    }
    
    .small-box-footer {
      background: rgba(44, 62, 80, 0.8) !important;
      color: #4a90e2 !important;
      border-top: 1px solid #404040 !important;
    }
    
    .small-box-footer:hover {
      background: rgba(74, 144, 226, 0.2) !important;
      color: #ffffff !important;
    }
    
    /* Tables Dark */
    .table {
      background: transparent !important;
      color: #e0e0e0 !important;
    }
    
    .table thead th {
      background: rgba(44, 62, 80, 0.8) !important;
      color: #ffffff !important;
      border-bottom: 2px solid #404040 !important;
    }
    
    .table tbody tr {
      background: transparent !important;
    }
    
    .table tbody tr:hover {
      background: rgba(74, 144, 226, 0.1) !important;
    }
    
    .table tbody td {
      border-top: 1px solid #404040 !important;
      color: #e0e0e0 !important;
    }
    
    /* Buttons Dark */
    .btn-info {
      background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
      border: none !important;
      color: #ffffff !important;
      box-shadow: 0 2px 8px rgba(23, 162, 184, 0.3) !important;
    }
    
    .btn-info:hover {
      background: linear-gradient(135deg, #138496 0%, #117a8b 100%) !important;
      transform: translateY(-1px) !important;
      box-shadow: 0 4px 12px rgba(23, 162, 184, 0.4) !important;
    }
    
    .btn-secondary {
      background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
      border: none !important;
      color: #ffffff !important;
      box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3) !important;
    }
    
    .btn-secondary:hover {
      background: linear-gradient(135deg, #5a6268 0%, #495057 100%) !important;
      transform: translateY(-1px) !important;
      box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4) !important;
    }
    
    /* Calendar Dark */
    .fc {
      background: transparent !important;
    }
    
    .fc-toolbar {
      background: rgba(44, 62, 80, 0.8) !important;
      color: #ffffff !important;
      border-radius: 8px 8px 0 0 !important;
    }
    
    .fc-toolbar-title {
      color: #ffffff !important;
    }
    
    .fc-button {
      background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%) !important;
      border: none !important;
      color: #ffffff !important;
    }
    
    .fc-button:hover {
      background: linear-gradient(135deg, #357abd 0%, #2a5f9e 100%) !important;
    }
    
    .fc-button:disabled {
      background: #6c757d !important;
      color: #b8c7ce !important;
    }
    
    .fc-daygrid-day {
      background: rgba(44, 62, 80, 0.5) !important;
      border: 1px solid #404040 !important;
    }
    
    .fc-daygrid-day:hover {
      background: rgba(74, 144, 226, 0.1) !important;
    }
    
    .fc-daygrid-day-number {
      color: #e0e0e0 !important;
    }
    
    .fc-day-today {
      background: rgba(74, 144, 226, 0.2) !important;
    }
    
    .fc-event {
      background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%) !important;
      border: none !important;
      color: #ffffff !important;
    }
    
    /* Footer Dark */
    .main-footer {
      background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
      color: #b8c7ce !important;
      border-top: 1px solid #404040 !important;
    }
    
    .main-footer a {
      color: #4a90e2 !important;
    }
    
    .main-footer a:hover {
      color: #ffffff !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .welcome-section {
        padding: 15px !important;
      }
      
      .welcome-section h2 {
        font-size: 1.5rem !important;
      }
    }
    
    /* Animation for cards */
    .dashboard-card {
      transition: transform 0.3s, box-shadow 0.3s !important;
    }
    
    .dashboard-card:hover {
      transform: translateY(-5px) !important;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4) !important;
    }
    
    .calendar-container {
      background: transparent !important;
      padding: 20px !important;
      border-radius: 10px !important;
    }
    
    .fc-event {
      cursor: pointer !important;
    }
    
    /* Direct Chat Dark Theme */
    .direct-chat-primary .right > .direct-chat-text {
      background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%) !important;
      border-color: #4a90e2 !important;
      color: #ffffff !important;
    }
    
    .direct-chat-primary .right > .direct-chat-text::before,
    .direct-chat-primary .right > .direct-chat-text::after {
      border-left-color: #4a90e2 !important;
    }
    
    .direct-chat-text {
      background: rgba(44, 62, 80, 0.8) !important;
      border: 1px solid #404040 !important;
      color: #e0e0e0 !important;
    }
    
    .direct-chat-text::before,
    .direct-chat-text::after {
      border-right-color: rgba(44, 62, 80, 0.8) !important;
    }
    
    .direct-chat-name {
      color: #4a90e2 !important;
      font-weight: 600 !important;
    }
    
    .direct-chat-timestamp {
      color: #b8c7ce !important;
      font-size: 0.85rem !important;
    }
    
    .direct-chat-messages {
      background: transparent !important;
      overflow-y: auto !important;
    }
    
    .direct-chat-msg {
      margin-bottom: 15px !important;
    }
    
    .direct-chat-img {
      border: 2px solid #404040 !important;
    }
    
    .direct-chat-infos {
      margin-bottom: 5px !important;
    }
    
    .card-footer {
      background: rgba(44, 62, 80, 0.8) !important;
      border-top: 1px solid #404040 !important;
    }
    
    .card-footer .form-control {
      background: rgba(50, 50, 50, 0.8) !important;
      border: 2px solid rgba(100, 100, 100, 0.3) !important;
      color: #ffffff !important;
      border-radius: 20px !important;
    }
    
    .card-footer .form-control:focus {
      background: rgba(60, 60, 60, 0.9) !important;
      border-color: #4a90e2 !important;
      box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25) !important;
    }
    
    .card-footer .form-control::placeholder {
      color: #b0b0b0 !important;
    }
    
    .card-footer .btn-primary {
      background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%) !important;
      border: none !important;
      border-radius: 20px !important;
      padding: 8px 20px !important;
    }
    
    .card-footer .btn-primary:hover {
      background: linear-gradient(135deg, #357abd 0%, #2a5f9e 100%) !important;
      transform: translateY(-1px) !important;
    }
    
    .badge-primary {
      background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%) !important;
      color: #ffffff !important;
    }
    
    .btn-tool {
      background: transparent !important;
      border: none !important;
      color: #b8c7ce !important;
    }
    
    .btn-tool:hover {
      color: #4a90e2 !important;
      background: rgba(74, 144, 226, 0.1) !important;
    }
    
    /* Direct Chat Compact Styles */
    .direct-chat-messages {
      height: 180px !important;
      font-size: 0.92rem !important;
      padding: 10px 8px !important;
    }
    .direct-chat-msg, .direct-chat-msg.right {
      margin-bottom: 10px !important;
    }
    .direct-chat-img {
      width: 28px !important;
      height: 28px !important;
      font-size: 14px !important;
      min-width: 28px !important;
      min-height: 28px !important;
      margin-right: 6px !important;
    }
    .direct-chat-text {
      font-size: 0.95rem !important;
      padding: 6px 12px !important;
      border-radius: 12px !important;
    }
    .direct-chat-name {
      font-size: 0.95rem !important;
    }
    .direct-chat-timestamp {
      font-size: 0.85rem !important;
    }
    .direct-chat .card-footer .form-control {
      font-size: 0.95rem !important;
      padding: 6px 10px !important;
      border-radius: 14px !important;
    }
    .direct-chat .btn-primary {
      padding: 6px 16px !important;
      font-size: 0.95rem !important;
      border-radius: 14px !important;
    }
    .direct-chat .input-group {
      min-height: 36px !important;
    }
    .direct-chat .card-header, .direct-chat .card-footer {
      padding-top: 8px !important;
      padding-bottom: 8px !important;
    }
    .direct-chat .card-title {
      font-size: 1.05rem !important;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="auth/logout.php">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="dashboard.php" class="brand-link">
      <span class="brand-text font-weight-light">CMS Sederhana</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="#" class="d-block"><?php echo $_SESSION['username']; ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/posts.php" class="nav-link">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>Posts</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/users.php" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>Users</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Welcome Section -->
        <div class="welcome-section">
          <h2><i class="fas fa-user-circle mr-2"></i>Selamat Datang di Risma Depa Yulianawati</h2>
          <p class="mb-0">Selamat datang di CMS Sederhana. Anda dapat mengelola konten dan pengguna dari sini.</p>
        </div>

        <!-- Info boxes -->
        <div class="row">
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-info">
              <span class="info-box-icon"><i class="fas fa-cogs"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">CPU Traffic</span>
                <span class="info-box-number">10<small>%</small></span>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-danger">
              <span class="info-box-icon"><i class="fas fa-thumbs-up"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Likes</span>
                <span class="info-box-number">41,410</span>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-success">
              <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Sales</span>
                <span class="info-box-number">760</span>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-warning">
              <span class="info-box-icon"><i class="fas fa-users"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">New Members</span>
                <span class="info-box-number">2,000</span>
              </div>
            </div>
          </div>
        </div>
        <!-- /.row -->

        <!-- Monthly Recap Report, Goal Completion, and Direct Chat -->
        <div class="row">
          <div class="col-md-6">
            <!-- Monthly Recap Report -->
            <div class="card">
              <div class="card-header border-transparent">
                <h3 class="card-title">Monthly Recap Report</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex">
                  <p class="d-flex flex-column">
                    <span class="text-bold text-lg">Sales: 1 Jan, 2014 - 30 Jul, 2014</span>
                  </p>
                </div>
                <div class="position-relative mb-4">
                  <canvas id="areaChart" height="100"></canvas>
                </div>
                <div class="d-flex flex-row justify-content-end">
                  <span class="mr-2">
                    <i class="fas fa-square text-primary"></i> This year
                  </span>
                  <span>
                    <i class="fas fa-square text-gray"></i> Last year
                  </span>
                </div>
              </div>
              <div class="card-footer">
                <div class="row">
                  <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                      <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 17%</span>
                      <h5 class="description-header">$35,210.43</h5>
                      <span class="description-text">TOTAL REVENUE</span>
                    </div>
                  </div>
                  <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                      <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i> 0%</span>
                      <h5 class="description-header">$10,390.90</h5>
                      <span class="description-text">TOTAL COST</span>
                    </div>
                  </div>
                  <div class="col-sm-3 col-6">
                    <div class="description-block border-right">
                      <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> 20%</span>
                      <h5 class="description-header">$24,813.53</h5>
                      <span class="description-text">TOTAL PROFIT</span>
                    </div>
                  </div>
                  <div class="col-sm-3 col-6">
                    <div class="description-block">
                      <span class="description-percentage text-danger"><i class="fas fa-caret-down"></i> 18%</span>
                      <h5 class="description-header">1200</h5>
                      <span class="description-text">GOAL COMPLETIONS</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <!-- Goal Completion -->
            <div class="card mb-3">
              <div class="card-header">
                <h3 class="card-title">Goal Completion</h3>
              </div>
              <div class="card-body">
                <div class="progress-group">
                  Add Products to Cart
                  <span class="float-right"><b>160</b>/200</span>
                  <div class="progress progress-sm">
                    <div class="progress-bar bg-primary" style="width: 80%"></div>
                  </div>
                </div>
                <div class="progress-group">
                  Complete Purchase
                  <span class="float-right"><b>310</b>/400</span>
                  <div class="progress progress-sm">
                    <div class="progress-bar bg-danger" style="width: 77.5%"></div>
                  </div>
                </div>
                <div class="progress-group">
                  Visit Premium Page
                  <span class="float-right"><b>480</b>/800</span>
                  <div class="progress progress-sm">
                    <div class="progress-bar bg-success" style="width: 60%"></div>
                  </div>
                </div>
                <div class="progress-group">
                  Send Inquiries
                  <span class="float-right"><b>250</b>/500</span>
                  <div class="progress progress-sm">
                    <div class="progress-bar bg-warning" style="width: 50%"></div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Direct Chat Widget -->
            <div class="card direct-chat direct-chat-primary">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-comments mr-1"></i>
                  Direct Chat
                </h3>
                <div class="card-tools">
                  <span title="3 New Messages" class="badge badge-primary">3</span>
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="direct-chat-messages" style="height: 180px;">
                  <!-- Message. Default to the left -->
                  <div class="direct-chat-msg">
                    <div class="direct-chat-infos clearfix">
                      <span class="direct-chat-name float-left">
                        <i class="fas fa-user-shield mr-1"></i>Admin
                      </span>
                      <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                    </div>
                    <span class="direct-chat-img bg-primary text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;border-radius:50%;font-size:20px;">
                      <i class="fas fa-user-shield"></i>
                    </span>
                    <div class="direct-chat-text">
                      Apakah ada yang bisa saya bantu?
                    </div>
                  </div>

                  <!-- Message to the right -->
                  <div class="direct-chat-msg right">
                    <div class="direct-chat-infos clearfix">
                      <span class="direct-chat-name float-right">
                        <i class="fas fa-user mr-1"></i>User
                      </span>
                      <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                    </div>
                    <span class="direct-chat-img bg-success text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;border-radius:50%;font-size:20px;">
                      <i class="fas fa-user"></i>
                    </span>
                    <div class="direct-chat-text">
                      Ya, saya ingin bertanya tentang fitur baru
                    </div>
                  </div>

                  <!-- Message. Default to the left -->
                  <div class="direct-chat-msg">
                    <div class="direct-chat-infos clearfix">
                      <span class="direct-chat-name float-left">
                        <i class="fas fa-user-shield mr-1"></i>Admin
                      </span>
                      <span class="direct-chat-timestamp float-right">23 Jan 2:10 pm</span>
                    </div>
                    <span class="direct-chat-img bg-primary text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;border-radius:50%;font-size:20px;">
                      <i class="fas fa-user-shield"></i>
                    </span>
                    <div class="direct-chat-text">
                      Silakan, fitur apa yang ingin Anda tanyakan?
                    </div>
                  </div>

                  <!-- Message to the right -->
                  <div class="direct-chat-msg right">
                    <div class="direct-chat-infos clearfix">
                      <span class="direct-chat-name float-right">
                        <i class="fas fa-user mr-1"></i>User
                      </span>
                      <span class="direct-chat-timestamp float-left">23 Jan 2:15 pm</span>
                    </div>
                    <span class="direct-chat-img bg-success text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;border-radius:50%;font-size:20px;">
                      <i class="fas fa-user"></i>
                    </span>
                    <div class="direct-chat-text">
                      Bagaimana cara menambah post baru?
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <form action="#" method="post">
                  <div class="input-group">
                    <input type="text" name="message" placeholder="Type Message ..." class="form-control">
                    <span class="input-group-append">
                      <button type="button" class="btn btn-primary">Send</button>
                    </span>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Posts -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-clock mr-1"></i>
                  Recent Posts
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table m-0">
                    <thead>
                      <tr>
                        <th>Title</th>
                        <th>Created At</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($recent_posts as $post): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td><?php echo date('d M Y H:i', strtotime($post['created_at'])); ?></td>
                        <td>
                          <a href="pages/post_form.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-info">
                            <i class="fas fa-edit"></i> Edit
                          </a>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- /.card-body -->
              <div class="card-footer clearfix">
                <a href="pages/posts.php" class="btn btn-sm btn-secondary float-right">View All Posts</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Visitors -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-users mr-1"></i>
                  Daftar Pengunjung Terakhir
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table m-0">
                    <thead>
                      <tr>
                        <th>IP Address</th>
                        <th>Browser</th>
                        <th>Waktu Kunjungan</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($recent_visitors as $visitor): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($visitor['ip_address']); ?></td>
                        <td><?php echo htmlspecialchars($visitor['user_agent']); ?></td>
                        <td><?php echo date('d M Y H:i', strtotime($visitor['visit_time'])); ?></td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <strong>Copyright &copy; 2024 <a href="#">CMS Sederhana</a>.</strong>
    All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        locale: 'id',
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            day: 'Hari'
        },
        events: [
            {
                title: 'Meeting',
                start: '2024-03-20',
                color: '#007bff'
            },
            {
                title: 'Deadline Project',
                start: '2024-03-25',
                color: '#dc3545'
            }
        ],
        eventClick: function(info) {
            alert('Event: ' + info.event.title);
        }
    });
    calendar.render();
    
    // Chat functionality for the direct chat below Goal Completion
    const directChat = document.querySelector('.direct-chat');
    if (directChat) {
      const chatForm = directChat.querySelector('form');
      const chatInput = directChat.querySelector('input[name="message"]');
      const chatMessages = directChat.querySelector('.direct-chat-messages');
      const sendButton = directChat.querySelector('.btn-primary');

      function sendMessage() {
        const message = chatInput.value.trim();
        if (message) {
          const currentTime = new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
          });
          const messageHTML = `
            <div class="direct-chat-msg right">
              <div class="direct-chat-infos clearfix">
                <span class="direct-chat-name float-right"><i class="fas fa-user mr-1"></i>You</span>
                <span class="direct-chat-timestamp float-left">${currentTime}</span>
              </div>
              <span class="direct-chat-img bg-success text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;border-radius:50%;font-size:20px;">
                <i class="fas fa-user"></i>
              </span>
              <div class="direct-chat-text">
                ${message}
              </div>
            </div>
          `;
          chatMessages.insertAdjacentHTML('beforeend', messageHTML);
          chatInput.value = '';
          chatMessages.scrollTop = chatMessages.scrollHeight;
          setTimeout(() => {
            const responseHTML = `
              <div class="direct-chat-msg">
                <div class="direct-chat-infos clearfix">
                  <span class="direct-chat-name float-left"><i class="fas fa-user-shield mr-1"></i>Admin</span>
                  <span class="direct-chat-timestamp float-right">${new Date().toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                  })}</span>
                </div>
                <span class="direct-chat-img bg-primary text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;border-radius:50%;font-size:20px;">
                  <i class="fas fa-user-shield"></i>
                </span>
                <div class="direct-chat-text">
                  Terima kasih atas pesannya! Tim kami akan segera merespons.
                </div>
              </div>
            `;
            chatMessages.insertAdjacentHTML('beforeend', responseHTML);
            chatMessages.scrollTop = chatMessages.scrollHeight;
          }, 2000);
        }
      }

      if (chatForm) {
        chatForm.addEventListener('submit', function(e) {
          e.preventDefault();
          sendMessage();
        });
      }
      if (sendButton) {
        sendButton.addEventListener('click', sendMessage);
      }
      if (chatInput) {
        chatInput.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') {
            sendMessage();
          }
        });
      }
    }
});

// Chart.js Area Chart
const ctxArea = document.getElementById('areaChart').getContext('2d');
const areaChart = new Chart(ctxArea, {
  type: 'line',
  data: {
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
    datasets: [
      {
        label: 'This year',
        data: [50, 60, 70, 80, 60, 75, 90],
        backgroundColor: 'rgba(74,144,226,0.2)',
        borderColor: 'rgba(74,144,226,1)',
        fill: true,
        tension: 0.4
      },
      {
        label: 'Last year',
        data: [30, 40, 50, 60, 40, 55, 70],
        backgroundColor: 'rgba(200,200,200,0.2)',
        borderColor: 'rgba(200,200,200,1)',
        fill: true,
        tension: 0.4
      }
    ]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true } }
  }
});
</script>
</body>
</html> 