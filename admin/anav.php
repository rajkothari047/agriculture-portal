<?php
// PHP Script: Ensure $para2 (Admin name) is defined
if (!isset($para2)) {
    $para2 = "Admin"; 
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>KisanMitra Admin</title>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    :root { 
        --color-primary-dark: #0A3D0A; 
        --color-accent-terracotta: #B85C38;
    }

    body { margin: 0; background-color: #f8fafc; overflow-x: hidden; }

    /* Navbar container */
    #main-header { 
        width: 100%; 
        background-color: var(--color-primary-dark); 
        box-shadow: 0 4px 12px rgba(0,0,0,0.3); 
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    /* Navbar Links (Top Level) */
    .nav-link-item {
        position: relative;
        color: white !important; 
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0.6rem 0.8rem;
        transition: all 0.2s;
        text-decoration: none !important;
        cursor: pointer;
    }

    /* Underline effect for TOP LEVEL links */
    .nav-item-container { position: relative; }
    
    .nav-item-container::after {
        content: '';
        position: absolute;
        height: 2px;
        width: 0;
        bottom: 0;
        left: 50%;
        background-color: var(--color-accent-terracotta);
        transition: all 0.3s ease-in-out;
    }

    .nav-item-container:hover::after {
        width: 100%;
        left: 0;
    }

    nav > .flex-1 .nav-link-item:hover {
        color: var(--color-accent-terracotta) !important;
    }

    /* Dropdown Logic */
    .dropdown { position: relative; }
    .dropdown-content { 
        position: absolute; 
        right: 0; top:100%; 
        background: white; 
        border-radius: 0.5rem; 
        border:1px solid #d1d5db; 
        opacity:0; visibility:hidden; 
        transform:translateY(5px); 
        transition:all 0.2s; 
        z-index:50; 
        min-width:180px;
        padding: 0.15rem 0; 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        overflow: hidden; 
    }
    .dropdown:hover .dropdown-content { 
        opacity:1; visibility:visible; transform:translateY(0); 
    }

    .dropdown-content a { 
        display:flex; align-items:center; gap:0.5rem; 
        padding:0.5rem 1rem;
        font-size:0.85rem;
        color:#374151 !important; /* Fixed: Prevents turning blue */
        text-decoration:none !important;
        transition: background 0.1s; 
        width: 100%;
        box-sizing: border-box;
    }
    
    .dropdown-content a:hover { 
        background:#f3f4f6; 
        color:#374151 !important; /* Fixed: Remains grey on hover */
    }

    /* Profile Dropdown button */
    .btn-profile-dropdown {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
        color: white !important; /* Ensures Admin name stays white */
        font-weight: 700;
        border-radius: 9999px;
        background-color: var(--color-accent-terracotta);
        cursor: pointer;
        border: none;
        transition: background-color 0.2s;
    }
    
    .btn-profile-dropdown:hover {
        background-color: #a34e2e; 
        color: white !important;
    }

    /* Logout button fix */
    .btn-logout { 
        display: flex;
        align-items:center;
        gap:0.5rem;
        padding:0.5rem 1rem; 
        font-size:0.85rem;
        color:#374151 !important;
        font-weight:500; 
        border-radius:0.5rem; 
        text-decoration:none !important;
        background-color:white;
        margin: 0.3rem 0.5rem !important;
        width: calc(100% - 1rem) !important;
    }
    .dropdown-content a.btn-logout:hover {
        background-color:#ef4444 !important;
        color:white !important;
    }
</style>
</head>
<body>

<header id="main-header">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 flex items-center w-full">

        <div class="flex items-center space-x-2">
            <a href="../index.php" class="flex items-center space-x-2 no-underline">
                <span class="text-2xl">🌱</span>
                <span class="text-2xl font-extrabold text-white">
                    Kisan<span class="font-extrabold" style="color: #B85C38;">Mitra</span>
                </span>
            </a>
        </div>

        <div class="flex-1 flex justify-center space-x-3">
            <div class="nav-item-container">
                <a href="afarmers.php" class="nav-link-item flex items-center gap-1">
                    <i data-lucide="users" class="w-4 h-4"></i> Farmers
                </a>
            </div>

            <div class="nav-item-container">
                <a href="acustomers.php" class="nav-link-item flex items-center gap-1">
                    <i data-lucide="user-check" class="w-4 h-4"></i> Customers
                </a>
            </div>

            <div class="nav-item-container">
                <a href="aproducedcrop.php" class="nav-link-item flex items-center gap-1">
                    <i data-lucide="archive" class="w-4 h-4"></i> Crop Stock
                </a>
            </div>

            <div class="nav-item-container">
                <a href="aviewmsg.php" class="nav-link-item flex items-center gap-1">
                    <i data-lucide="help-circle" class="w-4 h-4"></i> Queries
                </a>
            </div>
        </div>

        <div class="flex items-center space-x-2 ml-auto">
            <div class="relative dropdown">
                <button class="btn-profile-dropdown">
                    <i data-lucide="user" class="w-5 h-5"></i> <?php echo $para2 ?>
                </button>
                <div class="dropdown-content">
                    <a href="aprofile.php"><i data-lucide="user" class="w-4 h-4"></i> Profile</a>
                    <a href="alogout.php" class="btn-logout"><i data-lucide="log-out" class="w-4 h-4"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>
</header>

<script>
    // 1. Initialize Icons immediately
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // 2. High-Speed Pre-fetching
    // This starts downloading the linked page as soon as the user hovers, 
    // so when they click, the page is already in the browser cache.
    const links = document.querySelectorAll('a');
    links.forEach(link => {
        link.addEventListener('mouseenter', () => {
            const href = link.getAttribute('href');
            if(href && href !== '#' && !href.startsWith('http')) {
                const prefetch = document.createElement('link');
                prefetch.rel = 'prefetch';
                prefetch.href = href;
                document.head.appendChild(prefetch);
            }
        });
    });
</script>

</body>
</html>