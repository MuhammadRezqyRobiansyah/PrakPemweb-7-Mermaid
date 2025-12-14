<footer>
    <div class="footer-content">
        <div class="footer-left">
            <h4>Thalassa Deep</h4>
            <p>Rasakan keajaiban bersantap di bawah laut, tempat keunggulan kuliner berpadu dengan keindahan samudra</p>
            <div class="social-icons">
                <!-- Instagram -->
                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                </svg>
                <!-- LinkedIn -->
                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none">
                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                    <rect x="2" y="9" width="4" height="12"></rect>
                    <circle cx="4" cy="4" r="2"></circle>
                </svg>
                <!-- X/Twitter -->
                <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </div>
        </div>
        <div class="footer-right">
            <div class="footer-col">
                <h5>Tautan Cepat</h5>
                <a href="home.php">Home</a>
                <a href="menu.php">Menu</a>
                <a href="reservasi.php">Reservasi</a>
            </div>
            <div class="footer-col">
                <h5>Info Kontak</h5>
                <p>123 Ocean Drive, Maria Bay</p>
                <p>08123456789</p>
                <p>info@thalassadeep.com</p>
            </div>
        </div>
    </div>
</footer>

<style>
    footer {
        background-color: #FFFFFF;
        color: #333;
        padding: 60px;
        font-size: 12px;
        margin-top: auto;
    }
    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
    }
    .footer-left {
        max-width: 400px;
    }
    .footer-left h4 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 16px;
        color: #000;
    }
    .footer-left p {
        color: #666;
        margin-bottom: 24px;
        line-height: 1.6;
    }
    .social-icons {
        display: flex;
        gap: 16px;
    }
    .social-icons svg {
        width: 20px;
        height: 20px;
        fill: #888;
        cursor: pointer;
        transition: fill 0.2s, transform 0.2s;
    }
    .social-icons svg:hover {
        fill: #000;
        transform: scale(1.2);
    }
    .footer-right {
        display: flex;
        gap: 80px;
    }
    .footer-col h5 {
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 16px;
        color: #000;
    }
    .footer-col p,
    .footer-col a {
        display: block;
        color: #666;
        margin-bottom: 8px;
        text-decoration: none;
    }
    .footer-col a:hover {
        color: #000;
    }
    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
            gap: 40px;
        }
        .footer-right {
            gap: 40px;
        }
    }
</style>