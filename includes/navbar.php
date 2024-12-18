<!-- navbar.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">My Store</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="/final_inv/admin_dashboard.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/final_inv/products/sell.php">Sell Product</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/final_inv/sales/index.php">Sales</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/final_inv/products/ins.php">Inventory</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/final_inv/employees/manage_employees.php">Employees</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/final_inv/suppliers/manage_suppliers.php">Suppliers</a>
        </li>
      </ul>
      
      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle"></i> User
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="../profile.php">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="/final_inv/cashier/logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Bootstrap Icons (optional, for user icon) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">