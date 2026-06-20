<!DOCTYPE html>
<html lang="en">
<?php
include('fsession.php');
if (!isset($_SESSION['farmer_login_user'])) {
    header("location: ../index.php");
    exit();
}

// User info
$user_check = $_SESSION['farmer_login_user'];
$query4 = "SELECT * FROM farmerlogin WHERE email='$user_check'";
$ses_sq4 = mysqli_query($conn, $query4);
$row4 = mysqli_fetch_assoc($ses_sq4);

$para1 = $row4['farmer_id'];
$para2 = $row4['farmer_name'];

// RSS feeds - Agriculture focused
$rss_feeds = [
    "https://www.agrifarming.in/feed",
    "https://www.farmingindia.com/feed",
    "https://www.indianagriculture.org/feed",
    "https://agriculture.economictimes.indiatimes.com/feed"
];

// Function to fetch and filter articles
function getAllArticles($feeds){
    $allArticles = [];
    foreach($feeds as $feed_url){
        $rss = @simplexml_load_file($feed_url);
        if(!$rss) continue;

        foreach($rss->channel->item as $item){
            $title = (string)$item->title;
            $link = (string)$item->link;
            $pubDate = date("d M Y", strtotime((string)$item->pubDate));
            $description = (string)$item->description;

            if(isset($item->children('content', true)->encoded)){
                $description = (string)$item->children('content', true)->encoded;
            }

            // Clean description - remove HTML tags for excerpt
            $clean_desc = strip_tags($description);
            if(strlen($clean_desc) > 200) {
                $excerpt = substr($clean_desc, 0, 200) . '...';
            } else {
                $excerpt = $clean_desc;
            }

            $allArticles[] = [
                'title' => $title,
                'description' => $description,
                'excerpt' => $excerpt,
                'pubDate' => $pubDate,
                'link' => $link
            ];
        }
    }

    // Shuffle for variety and limit to 50
    shuffle($allArticles);
    return array_slice($allArticles, 0, 50);
}

// AJAX Load More
if(isset($_GET['load_more']) && $_GET['load_more'] == 1){
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $perPage = 9;
    $allArticles = getAllArticles($rss_feeds);
    $articles = array_slice($allArticles, ($page-1)*$perPage, $perPage);
    echo json_encode(['articles' => $articles]);
    exit;
}

// On load
$allArticles = getAllArticles($rss_feeds);
$initialCount = 9;
$firstArticles = array_slice($allArticles, 0, $initialCount);
$totalArticles = count($allArticles);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Kisan Post - Agriculture News Feed</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        /* MINIMALISTIC SQUARE THEME - LIGHT THEME WITH LIFTED BOXES */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --color-primary-dark: #0A3D0A;
            --color-accent-terracotta: #B85C38;
            --color-secondary-green: #4F772D;
            --color-bg-light: #F9F7F3;
            --color-text-dark: #1E293B;
            
            /* Derived colors for lifted effect */
            --bg-card: #FFFFFF;
            --bg-sidebar: #FFFFFF;
            --border-light: #E8EDF2;
            --text-muted: #64748B;
            --shadow-lifted: 0 8px 20px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.02);
            --shadow-lifted-hover: 0 12px 28px rgba(0,0,0,0.12), 0 4px 8px rgba(0,0,0,0.04);
            --shadow-card: 0 4px 12px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.02);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--color-bg-light);
            color: var(--color-text-dark);
            overflow-x: hidden;
        }

        /* SQUARE HEADER - Simple top bar without logo text */
        .site-header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-light);
            padding: 0;
            box-shadow: var(--shadow-lifted);
        }

        .header-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        /* MAIN LAYOUT - SQUARE GRID */
        .main-layout {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px 24px;
            display: flex;
            gap: 32px;
        }

        /* SIDEBAR - SQUARE DESIGN WITH LIFTED BOXES */
        .sidebar {
            width: 320px;
            flex-shrink: 0;
        }

        /* MAIN STATS BOX - Consolidated single box without sub-boxes */
        .stats-main-box {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            margin-bottom: 24px;
            box-shadow: var(--shadow-lifted);
            transition: all 0.2s ease;
        }

        .stats-main-box:hover {
            box-shadow: var(--shadow-lifted-hover);
        }

        .stats-header {
            background: var(--color-primary-dark);
            padding: 14px 20px;
            text-align: center;
        }

        .stats-header h2 {
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .stats-header h2 i {
            margin-right: 8px;
            color: var(--color-accent-terracotta);
        }

        /* Three stats displayed vertically or in a clean row */
        .stats-vertical {
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-light);
            padding-bottom: 16px;
        }

        .stat-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .stat-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            background: var(--color-bg-light);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border-light);
        }

        .stat-icon i {
            font-size: 24px;
            color: var(--color-primary-dark);
        }

        .stat-label-text {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-muted);
            letter-spacing: 0.3px;
        }

        .stat-number-large {
            font-size: 36px;
            font-weight: 800;
            color: var(--color-primary-dark);
            line-height: 1;
        }

        .stat-agri-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--color-secondary-green);
        }

        /* Alternative row layout for stats */
        .stats-row {
            padding: 20px;
            display: flex;
            justify-content: space-around;
            gap: 16px;
        }

        .stat-block {
            text-align: center;
            flex: 1;
            padding: 16px 8px;
            background: var(--color-bg-light);
            border: 1px solid var(--border-light);
        }

        .stat-block .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: var(--color-primary-dark);
        }

        .stat-block .stat-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 8px;
            letter-spacing: 0.5px;
        }

        .stat-block i {
            font-size: 24px;
            color: var(--color-secondary-green);
            margin-bottom: 8px;
            display: block;
        }

        /* FILTERS SECTION - LIFTED */
        .sidebar-section {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            margin-bottom: 24px;
            box-shadow: var(--shadow-lifted);
            transition: all 0.2s ease;
        }

        .sidebar-section:hover {
            box-shadow: var(--shadow-lifted-hover);
        }

        .section-title {
            padding: 14px 20px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-light);
            background: var(--color-bg-light);
        }

        .filter-list {
            padding: 12px 0;
        }

        .filter-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            border-left: 2px solid transparent;
            font-size: 0.9rem;
            color: var(--color-text-dark);
        }

        .filter-item:hover {
            background: var(--color-bg-light);
            color: var(--color-primary-dark);
        }

        .filter-item.active {
            border-left-color: var(--color-primary-dark);
            background: var(--color-bg-light);
            color: var(--color-primary-dark);
            font-weight: 500;
        }

        .filter-item i {
            width: 20px;
            font-size: 14px;
            color: var(--color-secondary-green);
        }

        .info-box {
            padding: 20px;
        }

        .info-box p {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .info-divider {
            height: 1px;
            background: var(--border-light);
            margin: 16px 0;
        }

        /* CONTENT AREA */
        .content-area {
            flex: 1;
            min-width: 0;
        }

        /* TOOLBAR - LIFTED */
        .toolbar {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            padding: 14px 20px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: var(--shadow-lifted);
        }

        .result-info {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .result-info i {
            color: var(--color-primary-dark);
            margin-right: 8px;
        }

        .result-info strong {
            color: var(--color-primary-dark);
        }

        /* NEWS GRID - SQUARE CARDS WITH LIFTED EFFECT */
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        /* SQUARE ARTICLE CARD - LIFTED */
        .article-card {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            transition: all 0.25s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            height: 100%;
            box-shadow: var(--shadow-card);
        }

        .article-card:hover {
            border-color: var(--color-primary-dark);
            transform: translateY(-3px);
            box-shadow: var(--shadow-lifted-hover);
        }

        .card-badge {
            padding: 6px 14px;
            background: var(--color-primary-dark);
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            display: inline-block;
            align-self: flex-start;
            margin: 16px 16px 0 16px;
            color: white;
        }

        /* FULL HEADING - NO CLAMP, SHOW FULL TITLE */
        .card-title {
            padding: 16px 16px 10px 16px;
            font-size: 1.15rem;
            font-weight: 700;
            line-height: 1.4;
            color: var(--color-text-dark);
        }

        .card-excerpt {
            padding: 0 16px 16px 16px;
            font-size: 0.85rem;
            line-height: 1.5;
            color: var(--text-muted);
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }

        .card-footer {
            padding: 14px 16px;
            border-top: 1px solid var(--border-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--color-bg-light);
        }

        .card-date {
            font-size: 0.7rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .card-date i {
            color: var(--color-secondary-green);
        }

        .read-link {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--color-primary-dark);
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.2s;
        }

        .read-link:hover {
            color: var(--color-accent-terracotta);
        }

        /* LOAD MORE BUTTON - LIFTED */
        .load-more-wrapper {
            text-align: center;
            margin-top: 16px;
        }

        .load-more-btn {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            padding: 14px 36px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--color-text-dark);
            cursor: pointer;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: var(--shadow-card);
        }

        .load-more-btn:hover {
            border-color: var(--color-primary-dark);
            color: var(--color-primary-dark);
            background: var(--color-bg-light);
            box-shadow: var(--shadow-lifted-hover);
            transform: translateY(-1px);
        }

        .load-more-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* EMPTY STATE */
        .empty-state {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            padding: 60px 20px;
            text-align: center;
            box-shadow: var(--shadow-card);
        }

        .empty-state i {
            font-size: 48px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        /* SKELETON LOADER */
        .skeleton-card {
            background: var(--bg-card);
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-card);
        }

        .skeleton-badge {
            width: 80px;
            height: 28px;
            background: var(--border-light);
            margin: 16px 16px 0 16px;
        }

        .skeleton-title {
            height: 52px;
            background: var(--border-light);
            margin: 12px 16px;
        }

        .skeleton-text {
            height: 60px;
            background: var(--border-light);
            margin: 0 16px 16px 16px;
        }

        .skeleton-footer {
            height: 52px;
            background: var(--border-light);
            margin-top: 16px;
        }

        /* RESPONSIVE */
        @media (max-width: 1000px) {
            .main-layout {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }
            
            .filter-list {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                padding: 16px;
            }
            
            .filter-item {
                padding: 8px 16px;
                border: 1px solid var(--border-light);
                background: var(--bg-card);
            }
            
            .filter-item.active {
                border-left-color: var(--color-primary-dark);
                border-top-color: var(--color-primary-dark);
            }

            .stats-row {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 768px) {
            .main-layout {
                padding: 24px 16px;
            }
            
            .news-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .toolbar {
                flex-direction: column;
                text-align: center;
            }
            
            .card-title {
                font-size: 1rem;
            }
            
            .stats-row {
                flex-direction: column;
                gap: 12px;
            }
            
            .stat-block {
                display: flex;
                align-items: center;
                justify-content: space-between;
                text-align: left;
                padding: 12px 16px;
            }
            
            .stat-block i {
                margin-bottom: 0;
                font-size: 20px;
            }
            
            .stat-block .stat-number {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            .stat-block {
                padding: 10px 14px;
            }
        }
    </style>
</head>

<body>

<?php include('fnav.php'); ?>

<!-- SIMPLE TOP BAR - No KISAN POST text -->
<header class="site-header">
    <div class="header-inner">
        <!-- Empty header - no logo text -->
    </div>
</header>

<!-- MAIN LAYOUT -->
<div class="main-layout">
    
    <!-- SIDEBAR -->
    <aside class="sidebar">
        
        <!-- MAIN STATS BOX - Consolidated single box with clean stats display -->
        <div class="stats-main-box">
            <div class="stats-header">
                <h2><i class="fas fa-chart-simple"></i> NEWS STATISTICS</h2>
            </div>
            <!-- Clean stats display as a unified block - no sub-boxes inside -->
            <div class="stats-row">
                <div class="stat-block">
                    <i class="fas fa-newspaper"></i>
                    <div class="stat-number" id="totalCount"><?= $totalArticles ?></div>
                    <div class="stat-label">TOTAL ARTICLES</div>
                </div>
                <div class="stat-block">
                    <i class="fas fa-eye"></i>
                    <div class="stat-number" id="shownCount"><?= count($firstArticles) ?></div>
                    <div class="stat-label">SHOWING</div>
                </div>
                <div class="stat-block">
                    <i class="fas fa-leaf"></i>
                    <div class="stat-number">AGRI</div>
                    <div class="stat-label">AGRICULTURE FEED</div>
                </div>
            </div>
        </div>
        
        <!-- FILTERS SECTION - LIFTED -->
        <div class="sidebar-section">
            <div class="section-title">
                <i class="fas fa-filter" style="margin-right: 8px;"></i> FILTERS
            </div>
            <div class="filter-list">
                <div class="filter-item active" data-filter="all">
                    <i class="fas fa-th-large"></i> All Updates
                </div>
                <div class="filter-item" data-filter="farming">
                    <i class="fas fa-seedling"></i> Farming Tips
                </div>
                <div class="filter-item" data-filter="crops">
                    <i class="fas fa-wheat-alt"></i> Crops & Harvest
                </div>
                <div class="filter-item" data-filter="market">
                    <i class="fas fa-chart-line"></i> Market & Pricing
                </div>
                <div class="filter-item" data-filter="technology">
                    <i class="fas fa-microchip"></i> Agri Tech
                </div>
            </div>
        </div>
        
        <!-- INFO SECTION - LIFTED -->
        <div class="sidebar-section">
            <div class="section-title">
                <i class="fas fa-info-circle" style="margin-right: 8px;"></i> INFO
            </div>
            <div class="info-box">
                <p>Real-time agriculture news from trusted sources including AgriFarming, Farming India, and Economic Times Agriculture.</p>
                <div class="info-divider"></div>
                <p><i class="fas fa-sync-alt" style="margin-right: 8px; color: var(--color-primary-dark);"></i> Updates fetched daily</p>
                <p><i class="fas fa-shield-alt" style="margin-right: 8px; color: var(--color-primary-dark);"></i> Verified sources only</p>
            </div>
        </div>
    </aside>
    
    <!-- CONTENT AREA -->
    <div class="content-area">
        
        <!-- TOOLBAR -->
        <div class="toolbar">
            <div class="result-info">
                <i class="fas fa-rss"></i>
                Latest agriculture news from India
            </div>
            <div class="result-info">
                <i class="fas fa-sync-alt"></i>
                Last updated: today
            </div>
        </div>
        
        <!-- NEWS GRID -->
        <div class="news-grid" id="newsContainer">
            <?php if(empty($firstArticles)): ?>
                <div class="empty-state">
                    <i class="fas fa-newspaper"></i>
                    <p>No articles found. Please check back later.</p>
                </div>
            <?php else: ?>
                <?php foreach($firstArticles as $a): ?>
                    <div class="article-card" data-link="<?= htmlspecialchars($a['link']) ?>">
                        <div class="card-badge">
                            <i class="fas fa-leaf" style="margin-right: 6px;"></i> AGRI
                        </div>
                        <div class="card-title">
                            <?= htmlspecialchars($a['title']) ?>
                        </div>
                        <div class="card-excerpt">
                            <?= htmlspecialchars($a['excerpt']) ?>
                        </div>
                        <div class="card-footer">
                            <div class="card-date">
                                <i class="far fa-calendar-alt"></i>
                                <span><?= $a['pubDate'] ?></span>
                            </div>
                            <a href="<?= htmlspecialchars($a['link']) ?>" target="_blank" class="read-link" onclick="event.stopPropagation()">
                                Read <i class="fas fa-arrow-right" style="margin-left: 4px; font-size: 10px;"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- LOAD MORE -->
        <?php if($totalArticles > $initialCount): ?>
            <div class="load-more-wrapper">
                <button class="load-more-btn" id="loadMoreBtn" data-page="2">
                    <i class="fas fa-plus" style="margin-right: 8px;"></i> LOAD MORE ARTICLES
                </button>
            </div>
        <?php endif; ?>
        
    </div>
</div>

<?php require('footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let isLoading = false;
    let currentPage = 2;
    let totalLoaded = <?= count($firstArticles) ?>;
    const totalArticles = <?= $totalArticles ?>;
    
    // Make article cards clickable (entire card opens link)
    $('.article-card').on('click', function(e) {
        if(!$(e.target).is('a') && !$(e.target).closest('a').length) {
            const link = $(this).data('link');
            if(link) window.open(link, '_blank');
        }
    });
    
    // Filter functionality
    $('.filter-item').on('click', function() {
        $('.filter-item').removeClass('active');
        $(this).addClass('active');
        
        const filter = $(this).data('filter');
        const articles = $('.article-card');
        
        if(filter === 'all') {
            articles.show();
        } else {
            articles.each(function() {
                const title = $(this).find('.card-title').text().toLowerCase();
                const excerpt = $(this).find('.card-excerpt').text().toLowerCase();
                const keywords = getKeywords(filter);
                let show = false;
                
                keywords.forEach(kw => {
                    if(title.includes(kw) || excerpt.includes(kw)) {
                        show = true;
                    }
                });
                
                $(this).toggle(show);
            });
        }
        
        // Update visible count
        const visibleCount = $('.article-card:visible').length;
        $('#shownCount').text(visibleCount);
    });
    
    function getKeywords(filter) {
        switch(filter) {
            case 'farming':
                return ['farming', 'farm', 'cultivation', 'agriculture', 'organic', 'sustainable', 'farmer', 'soil', 'irrigation', 'tillage', 'fertilizer'];
            case 'crops':
                return ['crop', 'harvest', 'planting', 'wheat', 'rice', 'maize', 'paddy', 'vegetable', 'fruit', 'pulses', 'sowing', 'yield'];
            case 'market':
                return ['market', 'price', 'mandi', 'sell', 'buy', 'trade', 'export', 'import', 'economy', 'msp', 'procurement', 'commodity'];
            case 'technology':
                return ['tech', 'digital', 'drone', 'sensor', 'app', 'software', 'automation', 'precision', 'innovation', 'startup', 'smart farming'];
            default:
                return [];
        }
    }
    
    // Load more functionality
    $('#loadMoreBtn').on('click', function() {
        if(isLoading) return;
        
        const button = $(this);
        isLoading = true;
        const page = button.data('page');
        
        button.html('<i class="fas fa-spinner fa-pulse" style="margin-right: 8px;"></i> LOADING...').prop('disabled', true);
        
        // Add skeleton loaders
        for(let i = 0; i < 3; i++) {
            $('#newsContainer').append(`
                <div class="skeleton-card">
                    <div class="skeleton-badge"></div>
                    <div class="skeleton-title"></div>
                    <div class="skeleton-text"></div>
                    <div class="skeleton-footer"></div>
                </div>
            `);
        }
        
        $.ajax({
            url: '<?= $_SERVER['PHP_SELF'] ?>',
            type: 'GET',
            data: { load_more: 1, page: page },
            dataType: 'json',
            timeout: 30000,
            success: function(res) {
                $('.skeleton-card').remove();
                
                if(res.articles && res.articles.length > 0) {
                    res.articles.forEach(function(a) {
                        const excerpt = a.excerpt || (a.description ? stripHtml(a.description).substring(0, 200) + '...' : 'No description available');
                        
                        $('#newsContainer').append(`
                            <div class="article-card" data-link="${escapeHtml(a.link)}">
                                <div class="card-badge">
                                    <i class="fas fa-leaf" style="margin-right: 6px;"></i> AGRI
                                </div>
                                <div class="card-title">
                                    ${escapeHtml(a.title)}
                                </div>
                                <div class="card-excerpt">
                                    ${escapeHtml(excerpt)}
                                </div>
                                <div class="card-footer">
                                    <div class="card-date">
                                        <i class="far fa-calendar-alt"></i>
                                        <span>${escapeHtml(a.pubDate)}</span>
                                    </div>
                                    <a href="${escapeHtml(a.link)}" target="_blank" class="read-link" onclick="event.stopPropagation()">
                                        Read <i class="fas fa-arrow-right" style="margin-left: 4px; font-size: 10px;"></i>
                                    </a>
                                </div>
                            </div>
                        `);
                        
                        totalLoaded++;
                    });
                    
                    $('#shownCount').text(totalLoaded);
                    $('#totalCount').text(totalArticles);
                    button.data('page', page + 1);
                    
                    if(totalLoaded >= totalArticles) {
                        button.html('<i class="fas fa-check" style="margin-right: 8px;"></i> ALL ARTICLES LOADED').prop('disabled', true);
                    } else {
                        button.html('<i class="fas fa-plus" style="margin-right: 8px;"></i> LOAD MORE ARTICLES').prop('disabled', false);
                    }
                } else {
                    button.html('<i class="fas fa-check" style="margin-right: 8px;"></i> ALL ARTICLES LOADED').prop('disabled', true);
                }
            },
            error: function() {
                $('.skeleton-card').remove();
                button.html('<i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i> TRY AGAIN').prop('disabled', false);
                setTimeout(() => {
                    button.html('<i class="fas fa-plus" style="margin-right: 8px;"></i> LOAD MORE ARTICLES');
                }, 2000);
            },
            complete: function() {
                isLoading = false;
                
                // Re-attach click handlers
                $('.article-card').off('click').on('click', function(e) {
                    if(!$(e.target).is('a') && !$(e.target).closest('a').length) {
                        const link = $(this).data('link');
                        if(link) window.open(link, '_blank');
                    }
                });
            }
        });
    });
    
    function stripHtml(html) {
        const tmp = document.createElement('DIV');
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || '';
    }
    
    function escapeHtml(str) {
        if(!str) return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
});
</script>

</body>
</html>