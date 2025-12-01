<nav class="navbar-custom">    
    <ul class="list-unstyled topbar-nav float-right mb-0">  
        <li class="dropdown">
            <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <span class="ms-1 nav-user-name hidden-sm"><?php echo $staff_fullname; ?></span>
                <i data-feather="user" class="align-self-center icon-sm"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="change_password.php"><i data-feather="key" class="align-self-center icon-xs icon-dual mr-1"></i> Change Password</a>
                <div class="dropdown-divider mb-0"></div>
                <a class="dropdown-item" href="logout.php"><i data-feather="power" class="align-self-center icon-xs icon-dual mr-1"></i> Logout</a>
            </div>
        </li>
    </ul>
    <ul class="list-unstyled topbar-nav mb-0">                        
        <li>
            <button class="nav-link button-menu-mobile">
                <i data-feather="menu" class="align-self-center topbar-icon"></i>
            </button>
        </li> 
        <li class="creat-btn">
            <div class="nav-link">
                <span class="app-name font-weight-bold font-20">Staff Management System</span>
            </div>                                
        </li>                           
    </ul>
</nav>