<!-- navbar.php -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css">
<style>
    /* Additional styling */
    .nav-wrapper {
        background: linear-gradient(145deg, #00796b, #80E3A2);
    }

    .brand-logo {
        height: 30;
    }

    .content {
        text-align: center;
        margin-top: 50px;
    }

    .content img {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
    }

    .content h5 {
        color: #00796b;
    }
</style>
<nav>
    <div class="nav-wrapper">
        <!--<a href="home" class="brand-logo center">
            <img class="brand-logo" src="/assets/rotaract.png" alt="community club logo">
        </a>-->
        <ul id="nav-mobile" class="left hide-on-med-and-down">
            <li><a href="home">Home</a></li>
            <li><a href="donation">Donation</a></li>
            <li><a href="event">Events</a></li>
            <?php if ($_SESSION['USER_TYPE'] == 0 && $_SESSION['USER_ID']!=-1): ?>
                <li><a class="dropdown-trigger" href="#!" data-target="adminDropdown">Shop<i class="material-icons right"></i></a></li>
                <!-- Dropdown Structure -->
                <ul id="adminDropdown" class="dropdown-content">
                    <li><a href="shop">Shop</a></li>
                    <li><a href="showCategoryTree">Show Category Tree</a></li>
                </ul>
            <?php elseif ($_SESSION['USER_TYPE'] == 1 || $_SESSION['USER_ID']==-1): ?>
                <li><a href="shop">Shop</a></li>
            <?php endif; ?>
        </ul>

        <ul id="nav-mobile" class="right hide-on-med-and-down">
            <?php if ($_SESSION['USER_ID'] != -1): ?>
                <!-- Dropdown Trigger -->
                <li><a href="cartHistory">Cart History</a></li>
                <?php if ($_SESSION['USER_ID'] == 1): ?>
                    <li><a href="donationAdmin">Donations Admin</a></li>
                <?php endif; ?>
                <li><a href="updateUser">Welcome, <?php echo htmlspecialchars($_SESSION['USERNAME']); ?></a></li>
                <li><a href="logout">Logout</a></li>
            <?php else: ?>
                <li><a href="login">Login</a></li>
            <?php endif; ?>
        </ul>

    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const elems = document.querySelectorAll('.dropdown-trigger');
        M.Dropdown.init(elems, {
            coverTrigger: false, // Ensures dropdown is positioned correctly
            constrainWidth: false // Allows the dropdown to resize if needed
        });
    });
</script>