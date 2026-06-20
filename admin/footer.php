<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Farmer Registration - KisanMitra Portal</title>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"/>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
<style>
/* Define the colors based on the requested theme */
:root {
    --color-primary-dark: #0A3D0A; /* Deep Neem Green Footer Background */
    --color-accent-terracotta: #B85C38; /* Terracotta Accent Color (used for hover/Mitra) */
    --color-secondary-green: #4e9f3d; /* Bright Green Logo Accent */
    --color-text-white: #f1f5f9; /* White text for contrast */
}

/* Footer Container Styles */
.new-footer {
    background-color: var(--color-primary-dark);
    color: var(--color-text-white); 
    padding-top: 4rem;
    padding-bottom: 2rem;
}

/* FIX: Ensure content has 3px side spacing on large screens (Lg and up) */
@media (min-width: 992px) {
    /* Set the container padding to 3px on the sides */
    .new-footer .container {
        padding-left: 3px !important; 
        padding-right: 3px !important; 
    }
    
    /* Ensure the row uses the container's full width */
    .new-footer .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
        width: 100%;
    }
    
    /* The trick: Use the container's 3px padding for the edge spacing, and 
       Bootstrap's default 15px gutter for internal column spacing. */
    .new-footer .row > div {
        padding-left: 15px; /* Default gutter padding */
        padding-right: 15px;
    }
    
    /* Override padding for the outermost column edges to match the 3px gap */
    .new-footer .row > div:first-child {
        padding-left: 3px !important; 
        padding-right: 15px !important;
    }
    .new-footer .row > div:last-child {
        padding-left: 15px !important;
        padding-right: 3px !important; 
    }
}

/* General Link Styles */
.new-footer a {
    color: var(--color-text-white); 
    transition: color 0.2s;
    text-decoration: none;
    /* UPDATED: Increased link text size */
    font-size: 0.95rem; 
    line-height: 1.8;
    display: block; 
}

/* Link Hover State */
.new-footer a:hover {
    color: var(--color-accent-terracotta) !important;
}

/* Heading Styles: White horizontal line spanning full column width */
.footer-heading {
    /* Headings remain at 1.125rem (18px) */
    font-size: 1.125rem;
    font-weight: bold;
    color: var(--color-text-white);
    margin-bottom: 0.5rem; 
}

/* Full-width white horizontal separator */
.heading-separator {
    border-bottom: 1px solid var(--color-text-white); 
    margin-bottom: 1rem; 
    width: 100%; 
    height: 1px; 
    display: block;
}

/* Social Icon Container (Horizontal Alignment) */
.footer-social {
    display: flex;
    flex-direction: row; 
    align-items: center;
}

/* Social Icon Link Styles */
.footer-social a {
    color: var(--color-text-white); 
    margin-right: 1.25rem; 
    margin-bottom: 0; 
    display: inline-block;
}

.footer-social a:hover {
    color: var(--color-accent-terracotta);
}

/* Copyright Section Styles */
.footer-copyright {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    margin-top: 48px;
    padding-top: 32px;
}

.footer-copyright p {
    /* UPDATED: Increased copyright text size from 0.875rem to 0.9rem */
    font-size: 0.9rem; 
    color: rgba(255, 255, 255, 0.5); 
}
</style>
</head>
<body>

<footer id="contact" class="new-footer">
    <div class="container"> 
        <div class="row">
            
            <div class="col-12 col-lg-3 mb-4 mb-lg-0">
                <a href="../index.php" class="d-flex align-items-center mb-3" style="color: white; text-decoration: none;">
                    <span style="font-size: 1.5rem; color: var(--color-secondary-green); margin-right: 0.5rem;">🌱</span>
                    <span class="h4 font-weight-bolder mb-0">
                        <span style="color: var(--color-text-white);">Kisan</span><span style="color: var(--color-accent-terracotta);">Mitra</span>
                    </span>
                </a>
                <p class="text-sm" style="color: var(--color-text-white);">
                    Dedicated to the prosperity of the Indian farmer and the health of the Indian consumer.
                </p>
            </div>

            <div class="col-6 col-md-3 col-lg-3 mb-4 mb-lg-0">
                <h5 class="footer-heading">Quick Links</h5>
                <span class="heading-separator"></span> 
                <ul class="list-unstyled">
                    <li><a href="../index.php#vision">Our Story</a></li>
                    <li><a href="../index.php#offerings">Platform Services</a></li>
                    <li><a href="../index.php#impact">Impact</a></li>
                    <li><a href="../index.php#benefits">Features</a></li>
                    <li><a href="../index.php#contact">Help & Support</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-3 col-lg-3 mb-4 mb-lg-0">
                <h5 class="footer-heading">User Access</h5>
                <span class="heading-separator"></span> 
                <ul class="list-unstyled">
                    <li><a href="../customer/cregister.php">Customer Account</a></li>
                    <li><a href="../customer/clogin.php">Customer Login</a></li>
                    <li><a href="../admin/alogin.php">Admin Panel</a></li>
                </ul>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <h5 class="footer-heading">Get in Touch</h5>
                <span class="heading-separator"></span> 
                <p class="text-sm" style="color: var(--color-text-white); margin-bottom: 1rem;">Pune, Maharashtra, India.</p>
                
                <div class="footer-social">
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin fa-lg"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram fa-lg"></i></a>
                </div>
            </div>

        </div>

        <div class="row text-center footer-copyright">
            <div class="col-12">
                <p>&copy; <span id="current-year"></span> KisanMitra. All rights reserved. Crafted with care in India.</p>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.staticfile.org/jquery/3.6.3/jquery.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
<script src="https://cdn.staticfile.org/markdown-it/13.0.1/markdown-it.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" 
integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://use.fontawesome.com/ee1c3da296.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>

<script>
    // Sets the current year in the copyright notice
    $(document).ready(function() {
        $('#current-year').text(new Date().getFullYear());
    });
</script>

</body>
</html>