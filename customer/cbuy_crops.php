<?php
include ('csession.php');
include ('../sql.php');

ini_set('memory_limit', '-1');

if(!isset($_SESSION['customer_login_user'])){
    header("location: ../index.php");
    exit;
} 

if(isset($_GET['refresh_cart'])) {
    renderCartTable();
    exit;
}

function renderCartTable() {
    if(!empty($_SESSION["shopping_cart"])) {
        $total = 0;
        foreach($_SESSION["shopping_cart"] as $keys => $values) {
            echo '<tr>
                    <td class="font-weight-bold" style="color: var(--green-dark); font-size: 1rem;">'.ucfirst($values["item_name"]).'</td>
                    <td><span class="qty-badge">'.$values["item_quantity"].' KG</span></td>
                    <td class="font-weight-bold" style="color: var(--terracotta);">₹ '.number_format($values["item_price"], 2).'</td>
                    <td class="text-right"><a href="cbuy_crops.php?action=delete&id='.$values["item_id"].'" class="remove-link"><i class="fas fa-trash-alt"></i> Remove</a></td>
                </tr>';
            $total += $values["item_price"];
        }
        $_SESSION['Total_Cart_Price'] = $total;
        echo '<tr><td colspan="4" class="p-0 border-0">
                <div class="total-section">
                    <div class="total-flex">
                        <div class="total-label">GRAND TOTAL</div>
                        <div class="total-amount">₹ '.number_format($total, 2).'</div>
                    </div>
                    <button id="checkout-button" class="btn-checkout" data-total="'.number_format($total, 2).'">
                        <i class="fas fa-lock"></i> PROCEED TO CHECKOUT
                    </button>
                </div>
                </td></tr>';
    } else {
        echo '<tr><td colspan="4" class="basket-empty text-center py-5">
                <i class="fas fa-shopping-basket fa-3x mb-3" style="color: var(--border);"></i>
                <h4 style="color: var(--muted);">Your cart is empty</h4>
                <p style="font-size: 0.85rem;">Start adding fresh produce from the market below</p>
                </td></tr>';
    }
}

if(isset($_GET["action"]) && $_GET["action"] == "delete") {
    if(isset($_SESSION["shopping_cart"])) {
        foreach($_SESSION["shopping_cart"] as $keys => $values) {
            if($values["item_id"] == $_GET["id"]) {
                $cropToDelete = $values["item_name"];
                unset($_SESSION["shopping_cart"][$keys]);
                $_SESSION["shopping_cart"] = array_values($_SESSION["shopping_cart"]);
                mysqli_query($conn, "DELETE FROM `cart` WHERE `cropname` = '$cropToDelete'"); 
                header("location:cbuy_crops.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include ('cheader.php'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <style>
        :root {
            --green-dark:  #0A3D0A;
            --terracotta:  #B85C38;
            --green-mid:   #4F772D;
            --bg:          #F9F7F3;
            --text:        #1E293B;
            --muted:       #64748B;
            --border:      #E8E3DC;
            --white:       #FFFFFF;
            --terra-light: #FEF0E8;
            --green-light: #EDF4E5;
            --gold:        #C8960C;
            --gold-light:  #FDF4DC;
            --success:     #2E7D32;
            --shadow-sm:   0 2px 8px rgba(0,0,0,0.04);
            --shadow-md:   0 8px 24px rgba(0,0,0,0.08);
            --shadow-lg:   0 20px 32px -12px rgba(0,0,0,0.12);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
        }

        /* ── FULL WIDTH BANNER ── */
        .hero-banner {
            width: 100%;
            height: 320px;
            position: relative;
            overflow: hidden;
            background: var(--green-dark);
        }
        .hero-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 1;
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(10,61,10,0.4), rgba(10,61,10,0.92));
        }
        .hero-content {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 40px 5%;
            z-index: 2;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            padding: 6px 18px;
            border-radius: 0px;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            border: 1px solid rgba(255,255,255,0.25);
            margin-bottom: 15px;
        }
        .hero-badge i { color: #F4C542; }

        /* ── LAYOUT WRAPPER ── */
        .main-container {
            max-width: 1280px;
            margin: -50px auto 80px;
            padding: 0 24px;
            position: relative;
            z-index: 5;
        }

        /* ── REALISTIC MARKETPLACE CARD ── */
        .marketplace-card {
            background: var(--white);
            border-radius: 0px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--green-dark) 0%, #1a5a1a 100%);
            padding: 36px 32px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .card-header-custom::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 0px;
        }
        .card-header-custom::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255,215,0,0.08);
            border-radius: 0px;
        }
        .card-header-custom h2 {
            color: white;
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }
        .card-header-custom p {
            color: rgba(255,255,255,0.85);
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
        }

        /* ── ADD PRODUCE SECTION ── */
        .add-section {
            padding: 32px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(to bottom, #FEFCF9, #FDFBF7);
        }
        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: var(--green-dark);
            margin-bottom: 24px;
            font-size: 1.1rem;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--green-light);
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            align-items: end;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .form-group label {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--muted);
            letter-spacing: 0.8px;
        }
        .form-control-custom {
            padding: 14px 18px;
            border: 2px solid var(--border);
            border-radius: 0px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            background: var(--white);
            transition: all 0.2s;
        }
        .form-control-custom:focus {
            outline: none;
            border-color: var(--terracotta);
            box-shadow: 0 0 0 3px rgba(184,92,56,0.1);
        }
        .btn-add {
            background: linear-gradient(135deg, var(--terracotta), #9e4a2e);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 0px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(184,92,56,0.3);
        }
        .btn-add:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(184,92,56,0.4);
        }
        .btn-add:disabled {
            background: var(--border);
            cursor: not-allowed;
            box-shadow: none;
        }

        /* ── CART SECTION ── */
        .cart-section {
            padding: 32px;
            background: var(--white);
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }
        .cart-table thead th {
            text-align: left;
            padding: 16px 12px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 2px solid var(--border);
            letter-spacing: 0.5px;
        }
        .cart-table tbody td {
            padding: 18px 12px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }
        .qty-badge {
            background: var(--green-light);
            padding: 6px 14px;
            border-radius: 0px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--green-mid);
            display: inline-block;
        }
        .remove-link {
            color: #e53e3e;
            text-decoration: none;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 0px;
            background: #FEF0E8;
            transition: 0.2s;
        }
        .remove-link:hover {
            background: #ffe0d6;
        }
        .total-section {
            background: linear-gradient(135deg, var(--green-light), #E8F3E0);
            border-radius: 0px;
            padding: 20px 28px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            border: 1px solid rgba(79,119,45,0.15);
        }
        .total-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .total-amount {
            font-size: 2rem;
            font-weight: 800;
            color: var(--green-dark);
        }
        .btn-checkout {
            background: linear-gradient(135deg, var(--green-dark), #0d4f0d);
            color: white;
            border: none;
            padding: 14px 36px;
            border-radius: 0px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            box-shadow: 0 4px 12px rgba(10,61,10,0.3);
        }
        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(10,61,10,0.4);
        }
        .basket-empty {
            text-align: center;
            padding: 60px 20px;
        }

        /* Quick Stats Widget */
        .stats-widget {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 0;
            padding: 0 32px 32px;
            background: var(--white);
        }
        .stat-widget-item {
            background: linear-gradient(135deg, #FEFCF9, #FDFBF7);
            border: 1px solid var(--border);
            border-radius: 0px;
            padding: 20px 16px;
            text-align: center;
            transition: all 0.2s;
        }
        .stat-widget-item:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--green-light);
        }
        .stat-widget-item i {
            font-size: 1.8rem;
            color: var(--terracotta);
            margin-bottom: 12px;
        }
        .stat-widget-item .stat-num {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--green-dark);
        }
        .stat-widget-item .stat-label {
            font-size: 0.7rem;
            color: var(--muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container { padding: 0 16px; margin: -40px auto 60px; }
            .hero-banner { height: 240px; }
            .add-section, .cart-section { padding: 20px; }
            .form-grid { grid-template-columns: 1fr; gap: 15px; }
            .total-section { flex-direction: column; text-align: center; }
            .stats-widget { grid-template-columns: 1fr; padding: 0 20px 20px; gap: 12px; }
            .cart-table thead th { font-size: 0.6rem; padding: 12px 8px; }
            .cart-table tbody td { padding: 12px 8px; font-size: 0.8rem; }
            .total-amount { font-size: 1.5rem; }
            .btn-checkout { padding: 12px 24px; font-size: 0.8rem; }
        }

        @media (max-width: 480px) {
            .hero-content h1 { font-size: 1.6rem; }
            .card-header-custom h2 { font-size: 1.3rem; }
            .card-header-custom { padding: 24px 20px; }
            .stat-widget-item .stat-num { font-size: 1.2rem; }
        }

        /* ===== PAYMENT MODAL ===== */
        .pay-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(10,25,15,0.75);
            backdrop-filter: blur(6px);
            overflow-y: auto;
            padding: 32px 16px;
        }
        .pay-overlay.active { display: block; }

        .pay-overlay-inner {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            min-height: 100%;
        }

        .pay-modal {
            background: var(--white);
            border-radius: 0px;
            width: 100%;
            max-width: 880px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.4);
            overflow: hidden;
            animation: modalUp 0.38s cubic-bezier(.4,0,.2,1) both;
            margin: auto 0;
        }
        @keyframes modalUp {
            from { opacity:0; transform:translateY(30px) scale(0.97); }
            to   { opacity:1; transform:translateY(0) scale(1); }
        }

        .pmod-header {
            background: linear-gradient(135deg, #2D6A4F, #1B4332);
            padding: 22px 30px;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap;
        }
        .pmod-header-left { display: flex; align-items: center; gap: 14px; }
        .pmod-header-icon {
            width: 46px; height: 46px; border-radius: 0px;
            background: rgba(255,255,255,0.15);
            display: flex; align-items: center; justify-content: center; font-size: 1.4rem;
        }
        .pmod-header h4 { color: #fff; font-weight: 700; margin: 0; font-size: 1.15rem; }
        .pmod-header p  { color: rgba(255,255,255,0.68); margin: 2px 0 0; font-size: 0.8rem; }
        .pmod-close {
            width: 36px; height: 36px; border-radius: 0px;
            background: rgba(255,255,255,0.15); border: none; color: #fff;
            font-size: 1rem; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.2s; flex-shrink: 0;
        }
        .pmod-close:hover { background: rgba(255,255,255,0.3); }

        .pmod-body {
            display: grid;
            grid-template-columns: 340px 1fr;
        }

        .pmod-left {
            background: linear-gradient(160deg, #183326 0%, #0d1f14 100%);
            padding: 28px 24px;
            display: flex; flex-direction: column; gap: 22px;
        }

        .card-vis {
            background: linear-gradient(135deg, #2D6A4F 0%, #52B788 70%, #D4A017 140%);
            border-radius: 0px;
            padding: 22px 20px; color: #fff;
            position: relative; overflow: hidden;
            box-shadow: 0 14px 36px rgba(0,0,0,0.5);
        }
        .card-vis::before { content:''; position:absolute; top:-45px; right:-45px; width:180px; height:180px; border-radius:0px; background:rgba(255,255,255,0.07); }
        .card-vis::after  { content:''; position:absolute; bottom:-55px; left:-25px; width:160px; height:160px; border-radius:0px; background:rgba(255,255,255,0.05); }
        .card-vis-top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; position:relative; z-index:1; flex-wrap:wrap; gap:10px; }
        .card-chip { width:40px; height:30px; border-radius:0px; background:linear-gradient(135deg,#f6c90e,#c8960c); }
        .card-bank { font-size:0.72rem; font-weight:700; opacity:0.8; letter-spacing:2px; text-transform:uppercase; }
        .card-num { font-size:1.2rem; letter-spacing:3px; font-weight:700; font-family:monospace; margin-bottom:16px; position:relative; z-index:1; word-break:break-all; }
        .card-bot { display:flex; justify-content:space-between; position:relative; z-index:1; flex-wrap:wrap; gap:10px; }
        .card-bot-item span   { display:block; font-size:0.58rem; opacity:0.65; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px; }
        .card-bot-item strong { font-size:0.82rem; font-weight:700; }

        .os-title {
            font-size:0.68rem; font-weight:700; color: #52B788;
            text-transform:uppercase; letter-spacing:2px;
            display:flex; align-items:center; gap:7px; margin-bottom:10px;
            flex-wrap:wrap;
        }
        .os-title::after { content:''; flex:1; height:1px; background:rgba(255,255,255,0.1); }
        .os-row {
            display:flex; justify-content:space-between; align-items:center;
            padding:7px 0; border-bottom:1px solid rgba(255,255,255,0.07); font-size:0.83rem;
            flex-wrap:wrap; gap:5px;
        }
        .os-row .os-name  { color:rgba(255,255,255,0.72); }
        .os-row .os-price { color:#fff; font-weight:700; }
        .os-total {
            display:flex; justify-content:space-between; align-items:center;
            margin-top:12px; padding-top:12px;
            border-top:2px solid rgba(255,255,255,0.14);
            flex-wrap:wrap; gap:10px;
        }
        .os-total .tot-lbl { color:rgba(255,255,255,0.68); font-size:0.8rem; font-weight:600; }
        .os-total .tot-amt { color: #D4A017; font-size:1.25rem; font-weight:700; }

        .trust-badges { display:flex; flex-direction:column; gap:8px; }
        .trust-badge {
            display:flex; align-items:center; gap:10px;
            background:rgba(255,255,255,0.05); border-radius:0px; padding:9px 13px;
            border:1px solid rgba(255,255,255,0.08);
            flex-wrap:wrap;
        }
        .trust-badge i    { color: #52B788; font-size:0.85rem; width:14px; text-align:center; }
        .trust-badge span { color:rgba(255,255,255,0.62); font-size:0.76rem; }

        .pmod-right { padding: 26px 28px; background: #fff; }

        .fsec {
            font-size:0.67rem; font-weight:700; color: #2D6A4F;
            text-transform:uppercase; letter-spacing:2px;
            display:flex; align-items:center; gap:8px;
            margin-bottom:12px; margin-top:20px;
            flex-wrap:wrap;
        }
        .fsec:first-child { margin-top:0; }
        .fsec::after { content:''; flex:1; height:1px; background: var(--border); }

        .pf { margin-bottom:12px; }
        .pf label {
            display:block; font-weight:700; font-size:0.75rem; color:#4a5568;
            text-transform:uppercase; letter-spacing:0.4px; margin-bottom:5px;
        }
        .pf input, .pf select {
            width:100%; height:46px; border-radius:0px;
            border:2px solid var(--border); background:#fafcfb;
            padding:0 13px; font-size:0.9rem; font-family:'Inter', sans-serif;
            color: var(--text); transition:border-color 0.2s, box-shadow 0.2s;
        }
        .pf input:focus, .pf select:focus {
            outline:none; border-color: #2D6A4F;
            box-shadow:0 0 0 3px rgba(45,106,79,0.11); background:#fff;
        }
        .pf input.err, .pf select.err { border-color:#e53e3e; background:#fff5f5; }
        .pf .errmsg { display:none; color:#e53e3e; font-size:0.72rem; margin-top:3px; font-weight:600; }
        .pf input.err ~ .errmsg, .pf select.err ~ .errmsg { display:block; }

        .pfrow  { display:grid; grid-template-columns:1fr 1fr; gap:11px; }
        .pfrow3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:11px; }

        .card-icons { display:flex; gap:8px; margin-bottom:10px; align-items:center; flex-wrap:wrap; }
        .card-icons img { height:26px; border-radius:0px; border:1px solid var(--border); padding:2px 6px; background:#fff; object-fit:contain; }

        .btn-process {
            width:100%; height:52px; border-radius:0px; margin-top:18px;
            background:linear-gradient(135deg, #2D6A4F, #1B4332);
            color:#fff; border:none; font-weight:700; font-size:0.93rem;
            letter-spacing:1.5px; text-transform:uppercase;
            cursor:pointer; transition:transform 0.2s, box-shadow 0.2s;
            font-family:'Inter', sans-serif;
            display:flex; align-items:center; justify-content:center; gap:9px;
        }
        .btn-process:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(45,106,79,0.4); }
        .btn-process:disabled { background:#9cac9f; cursor:not-allowed; transform:none; box-shadow:none; }
        .btn-process .spin {
            display:none; width:18px; height:18px;
            border:2px solid rgba(255,255,255,0.3); border-top-color:#fff;
            border-radius:0px; animation:spin 0.7s linear infinite;
        }
        @keyframes spin { to { transform:rotate(360deg); } }

        @media(max-width: 768px) {
            .pay-overlay { padding: 16px; }
            .pmod-body { grid-template-columns: 1fr; }
            .pmod-left { padding: 20px 16px; }
            .pmod-right { padding: 20px 16px; }
            .pfrow, .pfrow3 { grid-template-columns: 1fr; }
            .pay-modal { border-radius: 0px; margin: 0; }
            .pmod-header { padding: 16px; }
        }

        .success-overlay {
            display:none; position:fixed; inset:0; z-index:10000;
            background:rgba(0,0,0,0.68); backdrop-filter:blur(8px);
            align-items:center; justify-content:center; padding:24px;
        }
        .success-overlay.active { display:flex; }
        .success-modal {
            background:#fff; border-radius:0px; padding:48px 36px;
            width:100%; max-width:440px; text-align:center;
            position:relative; overflow:hidden;
            box-shadow:0 40px 100px rgba(0,0,0,0.3);
            animation:modalUp 0.4s cubic-bezier(.4,0,.2,1) both;
        }
        .success-modal::before {
            content:''; position:absolute; top:0; left:0; right:0; height:5px;
            background:linear-gradient(90deg, #2D6A4F, #52B788, #D4A017);
        }
        .suc-icon {
            width:82px; height:82px; border-radius:0px;
            background:linear-gradient(135deg, #2D6A4F, #52B788);
            margin:0 auto 20px; display:flex; align-items:center; justify-content:center;
            box-shadow:0 12px 32px rgba(45,106,79,0.45);
            animation:popIn 0.5s cubic-bezier(.4,0,.2,1) 0.15s both;
        }
        @keyframes popIn { from{transform:scale(0.4);opacity:0;} to{transform:scale(1);opacity:1;} }
        .suc-icon i { font-size:2rem; color:#fff; }
        .success-modal h3 { font-weight:700; font-size:1.45rem; margin-bottom:8px; color:#111; }
        .suc-sub { color: var(--muted); font-size:0.9rem; margin-bottom:18px; line-height:1.6; }
        .suc-order-box {
            background:#f0f9f4; border:1px solid #D8F3DC; border-radius:0px;
            padding:11px 20px; display:inline-block; margin-bottom:22px;
        }
        .suc-order-box span   { display:block; font-size:0.68rem; color: #2D6A4F; text-transform:uppercase; letter-spacing:1.5px; font-weight:700; margin-bottom:2px; }
        .suc-order-box strong { font-size:0.98rem; color:#111; letter-spacing:2px; }
        .suc-details { display:flex; justify-content:center; gap:22px; margin-bottom:26px; flex-wrap:wrap; }
        .suc-detail-item span   { display:block; font-size:0.68rem; color: var(--muted); text-transform:uppercase; letter-spacing:1px; }
        .suc-detail-item strong { font-size:0.88rem; color:#111; }
        .btn-suc-done {
            background:linear-gradient(135deg, #2D6A4F, #1B4332);
            color:#fff; border:none; border-radius:0px;
            padding:14px 44px; font-weight:700; font-size:0.93rem;
            cursor:pointer; font-family:'Inter', sans-serif;
            transition:transform 0.2s, box-shadow 0.2s; letter-spacing:1px;
        }
        .btn-suc-done:hover { transform:translateY(-2px); box-shadow:0 10px 24px rgba(45,106,79,0.4); }
        
        @media (max-width: 576px) {
            .success-modal { padding: 32px 20px; margin: 0; border-radius: 0px; }
            .suc-icon { width: 70px; height: 70px; border-radius: 0px; }
            .suc-icon i { font-size: 1.6rem; }
            .success-modal h3 { font-size: 1.2rem; }
            .suc-sub { font-size: 0.8rem; }
            .suc-order-box { padding: 8px 16px; }
            .suc-details { flex-direction: column; gap: 12px; }
            .btn-suc-done { padding: 12px 32px; font-size: 0.85rem; }
        }
        
        .cdot { position:absolute; width:9px; height:9px; border-radius:0px; opacity:0; animation:cfFall 1.3s ease-out forwards; }
        @keyframes cfFall { 0%{opacity:1;transform:translateY(0) rotate(0);} 100%{opacity:0;transform:translateY(90px) rotate(360deg);} }
    </style>
</head>
<body>

<?php include('cnav.php'); ?>

<!-- FULL WIDTH BANNER -->
<section class="hero-banner">
    <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=2000&q=85" alt="Fresh Harvest">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <i class="fas fa-shopping-cart"></i> Direct from Farm
        </div>
        <h1 style="color: #fff; font-family: 'Playfair Display', serif; font-size: clamp(2rem, 5vw, 3rem);">
            Fresh <span style="color: #F4C542;">Marketplace</span>
        </h1>
        <p style="color: rgba(255,255,255,0.85);">
            <i class="fas fa-tractor"></i> Order fresh produce directly from local farmers
        </p>
    </div>
</section>

<div class="main-container">
    <div class="marketplace-card">
        <div class="card-header-custom">
            <h2>Farmers' Direct Market</h2>
            <p>Freshly harvested crops, straight to your cart</p>
        </div>

        <!-- Add Produce Section -->
        <div class="add-section">
            <div class="section-title">
                <i class="fas fa-plus-circle" style="color: var(--terracotta);"></i>
                <span>Add to Cart</span>
            </div>
            <form id="ajax-add-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Select Crop</label>
                        <select id="crops" name="crops" class="form-control-custom" required>
                            <option value="">-- Choose Crop --</option>
                            <?php 
                                $sql = "SELECT crop FROM production_approx WHERE quantity > 0 ORDER BY crop ASC";
                                $result = $conn->query($sql);
                                while($row = $result->fetch_assoc()){
                                    echo "<option value='".$row["crop"]."'>".ucfirst($row["crop"])."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <input type="hidden" name="tradeid" id="tradeid" value="">
                    <div class="form-group">
                        <label>Quantity (KG)</label>
                        <input type="number" id="quantity" name="quantity" class="form-control-custom" placeholder="Enter KG" step="1" min="1">
                    </div>
                    <div class="form-group">
                        <label>Total Price (₹)</label>
                        <input type="text" id="price" name="price" readonly class="form-control-custom" placeholder="0.00" style="background: var(--green-light); font-weight: 700;">
                    </div>
                    <div class="form-group">
                        <button class="btn-add" type="submit" id="add_btn" name="add_to_cart" disabled>
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Cart Section -->
        <div class="cart-section">
            <div class="section-title">
                <i class="fas fa-shopping-basket" style="color: var(--terracotta);"></i>
                <span>Your Shopping Cart</span>
            </div>
            <div class="table-responsive">
                <table class="cart-table">
                    <thead>
                        <tr><th>Produce</th><th>Quantity</th><th>Price</th><th class="text-right">Action</th></tr>
                    </thead>
                    <tbody id="cart-table-body">
                        <?php renderCartTable(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Stats Widget -->
        <div class="stats-widget">
            <div class="stat-widget-item">
                <i class="fas fa-tractor"></i>
                <div class="stat-num">50+</div>
                <div class="stat-label">Local Farmers</div>
            </div>
            <div class="stat-widget-item">
                <i class="fas fa-leaf"></i>
                <div class="stat-num">100%</div>
                <div class="stat-label">Organic Quality</div>
            </div>
            <div class="stat-widget-item">
                <i class="fas fa-truck"></i>
                <div class="stat-num">Free</div>
                <div class="stat-label">Delivery*</div>
            </div>
        </div>
    </div>
</div>

<?php require("footer.php"); ?>

<!-- PAYMENT MODAL -->
<div class="pay-overlay" id="payOverlay">
  <div class="pay-overlay-inner">
    <div class="pay-modal">
        <div class="pmod-header">
            <div class="pmod-header-left">
                <div class="pmod-header-icon"><i class="fas fa-leaf"></i></div>
                <div>
                    <h4>Secure Crop Payment</h4>
                    <p>AgriMarket · SSL Encrypted Transaction</p>
                </div>
            </div>
            <button class="pmod-close" id="closePayModal" title="Close">✕</button>
        </div>
        <div class="pmod-body">
            <div class="pmod-left">
                <div class="card-vis">
                    <div class="card-vis-top">
                        <div class="card-chip"></div>
                        <div class="card-bank">AgriPay</div>
                    </div>
                    <div class="card-num" id="cvCardNum">•••• •••• •••• ••••</div>
                    <div class="card-bot">
                        <div class="card-bot-item">
                            <span>Card Holder</span>
                            <strong id="cvHolder">YOUR NAME</strong>
                        </div>
                        <div class="card-bot-item">
                            <span>Expires</span>
                            <strong id="cvExpiry">MM / YY</strong>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="os-title"><i class="fas fa-receipt"></i> Order Summary</div>
                    <div id="orderRows"></div>
                    <div class="os-total">
                        <span class="tot-lbl">TOTAL PAYABLE</span>
                        <span class="tot-amt" id="cvTotal">Rs. 0.00</span>
                    </div>
                </div>
                <div class="trust-badges">
                    <div class="trust-badge"><i class="fas fa-lock"></i><span>256-bit SSL Encryption</span></div>
                    <div class="trust-badge"><i class="fas fa-shield-alt"></i><span>PCI DSS Compliant</span></div>
                    <div class="trust-badge"><i class="fas fa-leaf"></i><span>Verified AgriMarket Seller</span></div>
                    <div class="trust-badge"><i class="fas fa-headset"></i><span>24/7 Farmer Support</span></div>
                </div>
            </div>
            <div class="pmod-right">
                <div class="fsec"><i class="fas fa-user-circle"></i> Buyer Information</div>
                <div class="pfrow">
                    <div class="pf">
                        <label>First Name</label>
                        <input type="text" id="fFirst" placeholder="Rajesh">
                        <div class="errmsg">Required (min 2 chars)</div>
                    </div>
                    <div class="pf">
                        <label>Last Name</label>
                        <input type="text" id="fLast" placeholder="Kumar">
                        <div class="errmsg">Required (min 2 chars)</div>
                    </div>
                </div>
                <div class="pf">
                    <label>Email Address</label>
                    <input type="email" id="fEmail" placeholder="rajesh@example.com">
                    <div class="errmsg">Valid email required</div>
                </div>
                <div class="pfrow">
                    <div class="pf">
                        <label>Mobile Number</label>
                        <input type="tel" id="fPhone" placeholder="9XXXXXXXXX" maxlength="10">
                        <div class="errmsg">10-digit number required</div>
                    </div>
                    <div class="pf">
                        <label>Alternate / WhatsApp</label>
                        <input type="tel" id="fAlt" placeholder="Optional" maxlength="10">
                    </div>
                </div>
                <div class="fsec"><i class="fas fa-map-marker-alt"></i> Delivery Address</div>
                <div class="pf">
                    <label>Street / Village / Area</label>
                    <input type="text" id="fAddr" placeholder="Plot 12, Near Panchayat Office">
                    <div class="errmsg">Required</div>
                </div>
                <div class="pf">
                    <label>Landmark (optional)</label>
                    <input type="text" id="fLandmark" placeholder="Near water tank / school">
                </div>
                <div class="pfrow">
                    <div class="pf">
                        <label>City / Town</label>
                        <input type="text" id="fCity" placeholder="Nashik">
                        <div class="errmsg">Required</div>
                    </div>
                    <div class="pf">
                        <label>District</label>
                        <input type="text" id="fDistrict" placeholder="Nashik District">
                        <div class="errmsg">Required</div>
                    </div>
                </div>
                <div class="pfrow">
                    <div class="pf">
                        <label>State</label>
                        <select id="fState">
                            <option value="">-- Select --</option>
                            <option>Maharashtra</option><option>Uttar Pradesh</option>
                            <option>Punjab</option><option>Haryana</option>
                            <option>Madhya Pradesh</option><option>Gujarat</option>
                            <option>Rajasthan</option><option>Karnataka</option>
                            <option>Andhra Pradesh</option><option>Telangana</option>
                            <option>Tamil Nadu</option><option>Bihar</option>
                            <option>West Bengal</option><option>Odisha</option>
                            <option>Chhattisgarh</option><option>Jharkhand</option>
                            <option>Other</option>
                        </select>
                        <div class="errmsg">Required</div>
                    </div>
                    <div class="pf">
                        <label>PIN Code</label>
                        <input type="text" id="fPin" placeholder="400001" maxlength="6">
                        <div class="errmsg">6-digit PIN required</div>
                    </div>
                </div>
                <div class="fsec"><i class="fas fa-credit-card"></i> Card Details</div>
                <div class="card-icons">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/200px-Mastercard-logo.svg.png" alt="Mastercard">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/200px-Visa_Inc._logo.svg.png" alt="Visa">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/RuPay.svg/200px-RuPay.svg.png" alt="RuPay">
                </div>
                <div class="pf">
                    <label>Card Holder Name</label>
                    <input type="text" id="fCName" placeholder="As printed on card" maxlength="26">
                    <div class="errmsg">Min 3 characters required</div>
                </div>
                <div class="pf">
                    <label>Card Number</label>
                    <input type="text" id="fCNum" placeholder="0000  0000  0000  0000" maxlength="19">
                    <div class="errmsg">Enter valid 16-digit card number</div>
                </div>
                <div class="pfrow">
                    <div class="pf">
                        <label>Expiry Date</label>
                        <input type="text" id="fExp" placeholder="MM / YY" maxlength="7">
                        <div class="errmsg">Use MM/YY format (future date)</div>
                    </div>
                    <div class="pf">
                        <label>CVV / CVC</label>
                        <input type="password" id="fCvv" placeholder="•••" maxlength="3">
                        <div class="errmsg">3-digit CVV required</div>
                    </div>
                </div>
                <button class="btn-process" id="btnProcess">
                    <i class="fas fa-lock"></i>
                    <span id="btnTxt">PAY SECURELY</span>
                    <div class="spin" id="btnSpin"></div>
                </button>
            </div>
        </div>
    </div>
  </div>
</div>

<!-- SUCCESS MODAL -->
<div class="success-overlay" id="successOverlay">
    <div class="success-modal">
        <div class="suc-icon"><i class="fas fa-check"></i></div>
        <h3>Order Placed! </h3>
        <p class="suc-sub">Your crop order is confirmed.<br>It will be delivered to your address.</p>
        <div class="suc-order-box">
            <span>Order Reference</span>
            <strong id="sucOrderId">AGR-000000</strong>
        </div>
        <div class="suc-details">
            <div class="suc-detail-item">
                <span>Amount Paid</span>
                <strong id="sucAmt">Rs. 0.00</strong>
            </div>
            <div class="suc-detail-item">
                <span>Delivery By</span>
                <strong id="sucDel">—</strong>
            </div>
            <div class="suc-detail-item">
                <span>Payment</span>
                <strong>Card ✓</strong>
            </div>
        </div>
        <button class="btn-suc-done" id="btnSucDone">Continue Shopping</button>
    </div>
</div>

<script>
$(document).ready(function(){
    // Crop selection change - fetch available quantity and trade ID
    $('#crops').change(function(){
        var c = $(this).val();
        if(c && c.trim() !== '') {
            $.post('ccheck_quantity.php', {crops: c}, function(r){
                try {
                    var res = JSON.parse(r);
                    if(res && res.quantityR) {
                        $('#quantity').attr('placeholder', 'Max: ' + res.quantityR).attr('max', res.quantityR).val('');
                        $('#tradeid').val(res.TradeIdR || '');
                        $('#price').val('0');
                        $('#add_btn').prop('disabled', true);
                    } else {
                        $('#quantity').attr('placeholder', 'Enter KG').attr('max', '').val('');
                        $('#price').val('0');
                        $('#add_btn').prop('disabled', true);
                    }
                } catch(e) {
                    console.log('Parse error:', e);
                    $('#quantity').attr('placeholder', 'Enter KG').attr('max', '').val('');
                    $('#price').val('0');
                    $('#add_btn').prop('disabled', true);
                }
            }).fail(function() {
                console.log('AJAX error for ccheck_quantity.php');
                $('#add_btn').prop('disabled', true);
            });
        } else {
            $('#quantity').attr('placeholder', 'Enter KG').attr('max', '').val('');
            $('#price').val('0');
            $('#add_btn').prop('disabled', true);
        }
    });
    
    // Quantity change - calculate price
    $('#quantity').on('input change', function(){
        var qty = parseInt($(this).val());
        var crop = $('#crops').val();
        var max = parseInt($(this).attr('max'));
        
        // Validation
        if(isNaN(qty) || qty <= 0) {
            $('#price').val('0');
            $('#add_btn').prop('disabled', true);
            return;
        }
        
        if(max && qty > max) {
            alert('Only ' + max + ' KG available!');
            $(this).val(max);
            qty = max;
        }
        
        if(qty > 0 && crop && crop.trim() !== '') {
            $.post('ccheck_price.php', {crops: crop, quantity: qty}, function(r){
                var priceVal = parseInt(r);
                if(!isNaN(priceVal) && priceVal > 0) {
                    $('#price').val(priceVal);
                    $('#add_btn').prop('disabled', false);
                } else {
                    $('#price').val('0');
                    $('#add_btn').prop('disabled', true);
                }
            }).fail(function() {
                $('#price').val('0');
                $('#add_btn').prop('disabled', true);
            });
        } else {
            $('#price').val('0');
            $('#add_btn').prop('disabled', true);
        }
    });
    
    // Add to cart form submission
    $('#ajax-add-form').on('submit', function(e){
        e.preventDefault();
        var crop = $('#crops').val();
        var qty = $('#quantity').val();
        var price = $('#price').val();
        
        if(!crop || crop === '') {
            alert('Please select a crop');
            return;
        }
        if(!qty || parseInt(qty) <= 0) {
            alert('Please enter valid quantity');
            return;
        }
        if(!price || parseInt(price) <= 0) {
            alert('Invalid price calculation');
            return;
        }
        
        var btn = $('#add_btn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');
        
        $.ajax({
            url: 'cbuy_redirect.php',
            type: 'POST',
            data: $(this).serialize() + '&add_to_cart=1',
            success: function(response){
                setTimeout(function(){
                    $('#cart-table-body').load('cbuy_crops.php?refresh_cart=1', function(){
                        // Reattach checkout button handler after cart refresh
                    });
                }, 300);
                $('#ajax-add-form')[0].reset();
                $('#crops').val('');
                $('#quantity').val('');
                $('#price').val('0');
                $('#tradeid').val('');
                btn.html('<i class="fas fa-cart-plus"></i> Add to Cart').prop('disabled', true);
                $('#quantity').attr('placeholder', 'Enter KG').attr('max', '');
            },
            error: function(){
                alert('Error adding to cart. Please try again.');
                btn.html('<i class="fas fa-cart-plus"></i> Add to Cart').prop('disabled', false);
            }
        });
    });

    // Checkout button click
    $(document).on('click', '#checkout-button', function(){
        var total = $(this).data('total');
        $('#cvTotal').text('Rs. ' + total);
        $('#sucAmt').text('Rs. ' + total);
        var rows = '';
        $('#cart-table-body tr').each(function(){
            var name = $(this).find('td:eq(0)').text().trim();
            var price = $(this).find('td:eq(2)').text().trim();
            if(name && price && name.length > 1 && !$(this).find('td').hasClass('p-0')){
                rows += '<div class="os-row"><span class="os-name">' + name + '</span><span class="os-price">' + price + '</span></div>';
            }
        });
        $('#orderRows').html(rows || '<div class="os-row"><span class="os-name" style="opacity:.5">Items loading…</span></div>');
        $('#payOverlay').addClass('active');
        $('body').css('overflow', 'hidden');
        $('#payOverlay').scrollTop(0);
    });

    // Close payment modal
    function closePayModal(){
        $('#payOverlay').removeClass('active');
        $('body').css('overflow', '');
    }
    $('#closePayModal').on('click', closePayModal);
    $('#payOverlay').on('click', function(e){
        if($(e.target).is('#payOverlay') || $(e.target).is('.pay-overlay-inner')) closePayModal();
    });

    // Card preview updates
    $('#fCName').on('input', function(){
        $('#cvHolder').text($(this).val().toUpperCase() || 'YOUR NAME');
    });
    $('#fCNum').on('input', function(){
        var raw = $(this).val().replace(/\D/g, '').substring(0, 16);
        $(this).val(raw.replace(/(.{4})/g, '$1 ').trim());
        var d = '';
        for(var i = 0; i < 16; i++){
            if(i > 0 && i % 4 === 0) d += ' ';
            d += (i < raw.length) ? raw[i] : '•';
        }
        $('#cvCardNum').text(d);
    });
    $('#fExp').on('input', function(){
        var raw = $(this).val().replace(/\D/g, '').substring(0, 4);
        if(raw.length >= 3) raw = raw.substring(0, 2) + ' / ' + raw.substring(2);
        $(this).val(raw);
        $('#cvExpiry').text(raw || 'MM / YY');
    });

    // Validation helpers
    function v(id, cond){ $('#' + id).toggleClass('err', !cond); return cond; }
    function isEmail(s){ return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(s); }

    // Process payment
    $('#btnProcess').on('click', function(){
        var fn = $('#fFirst').val().trim(), ln = $('#fLast').val().trim();
        var em = $('#fEmail').val().trim(), ph = $('#fPhone').val().trim();
        var ad = $('#fAddr').val().trim(), ci = $('#fCity').val().trim();
        var di = $('#fDistrict').val().trim(), st = $('#fState').val();
        var pi = $('#fPin').val().trim();
        var cn = $('#fCName').val().trim(), nu = $('#fCNum').val().replace(/\s/g, '');
        var ex = $('#fExp').val().replace(/\s/g, ''), cv = $('#fCvv').val().trim();

        var ok = true;
        ok = v('fFirst', fn.length >= 2) && ok;
        ok = v('fLast', ln.length >= 2) && ok;
        ok = v('fEmail', isEmail(em)) && ok;
        ok = v('fPhone', /^\d{10}$/.test(ph)) && ok;
        ok = v('fAddr', ad.length >= 4) && ok;
        ok = v('fCity', ci.length >= 2) && ok;
        ok = v('fDistrict', di.length >= 2) && ok;
        ok = v('fState', st != '') && ok;
        ok = v('fPin', /^\d{6}$/.test(pi)) && ok;
        ok = v('fCName', cn.length >= 3) && ok;
        ok = v('fCNum', /^\d{16}$/.test(nu)) && ok;

        var exM = ex.match(/^(\d{2})\/(\d{2})$/);
        if(exM){
            var now = new Date(), eM = parseInt(exM[1]), eY = parseInt('20' + exM[2]);
            ok = v('fExp', eM >= 1 && eM <= 12 && (eY > now.getFullYear() || (eY === now.getFullYear() && eM >= (now.getMonth() + 1)))) && ok;
        } else { ok = v('fExp', false) && ok; }
        ok = v('fCvv', /^\d{3}$/.test(cv)) && ok;

        if(!ok){
            var firstErr = $('.pmod-right .err:first');
            if(firstErr.length){
                var top = firstErr.offset().top - $('#payOverlay').offset().top + $('#payOverlay').scrollTop() - 40;
                $('#payOverlay').animate({scrollTop: top}, 300);
            }
            return;
        }

        $('#btnTxt').hide();
        $('#btnSpin').show();
        $('#btnProcess').prop('disabled', true);

        // Save order data
        $.ajax({
            url: 'place_order.php',
            type: 'POST',
            data: {
                first_name: $('#fFirst').val(),
                last_name: $('#fLast').val(),
                email: $('#fEmail').val(),
                phone: $('#fPhone').val(),
                address: $('#fAddr').val(),
                city: $('#fCity').val(),
                district: $('#fDistrict').val(),
                state: $('#fState').val(),
                pincode: $('#fPin').val(),
                payment_method: 'Card'
            },
            success: function(res){ console.log("Saved:", res); },
            error: function(){ console.log("Error saving data"); }
        });

        setTimeout(function(){
            closePayModal();
            var oid = 'AGR-' + Math.floor(100000 + Math.random() * 900000);
            $('#sucOrderId').text(oid);
            var d = new Date();
            d.setDate(d.getDate() + 4);
            $('#sucDel').text('By ' + d.toLocaleDateString('en-IN', {day: 'numeric', month: 'short'}));
            $('#successOverlay').addClass('active');
            launchConfetti();
            $('#btnTxt').show();
            $('#btnSpin').hide();
            $('#btnProcess').prop('disabled', false);
        }, 2500);
    });

    $('#btnSucDone').on('click', function(){
        $('#successOverlay').removeClass('active');
        $('body').css('overflow', '');
        $('#cart-table-body').load('cbuy_crops.php?refresh_cart=1');
        $('#fFirst, #fLast, #fEmail, #fPhone, #fAlt, #fAddr, #fLandmark, #fCity, #fDistrict, #fPin, #fCName, #fCNum, #fExp, #fCvv').val('').removeClass('err');
        $('#fState').val('');
        $('#cvCardNum').text('•••• •••• •••• ••••');
        $('#cvHolder').text('YOUR NAME');
        $('#cvExpiry').text('MM / YY');
    });

    function launchConfetti(){
        var colors = ['#2D6A4F', '#E2725B', '#D4A017', '#52B788', '#fff', '#1B4332'];
        var m = document.querySelector('.success-modal');
        for(var i = 0; i < 30; i++){
            (function(i){
                setTimeout(function(){
                    var d = document.createElement('div');
                    d.className = 'cdot';
                    d.style.cssText = 'left:' + Math.random() * 100 + '%;top:' + (10 + Math.random() * 40) + '%;background:' + colors[Math.floor(Math.random() * colors.length)] + ';animation-delay:' + (Math.random() * 0.5) + 's;animation-duration:' + (0.9 + Math.random() * 0.7) + 's';
                    m.appendChild(d);
                    setTimeout(function(){ d.remove(); }, 2200);
                }, i * 55);
            })(i);
        }
    }
});
</script>
</body>
</html>