<!-- Simple Footer -->
<footer class="footer">
    <div class="footer-content">
        <div class="footer-brand">
            <img src="logo.png" alt="FPL Logo" class="footer-logo">
            <span class="footer-title">FPL<span class="text-primary">Master</span></span>
        </div>
        <div class="footer-text">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> FPL Master. All rights reserved.</p>
        </div>
    </div>
</footer>

<style>
.footer {
    margin-left: 250px;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border-top: 1px solid var(--border-color);
    padding: 1.5rem 2rem;
    transition: margin-left 0.3s ease;
    box-shadow: 0 -1px 2px 0 rgba(0, 0, 0, 0.05);
}

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    max-width: 100%;
}

.footer-brand {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.footer-logo {
    height: 24px;
    width: 24px;
    object-fit: contain;
}

.footer-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -0.02em;
}

.footer-text {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .footer {
        margin-left: 0;
        padding: 1.25rem 1rem;
    }
    
    .footer-content {
        flex-direction: column;
        text-align: center;
    }
}
</style>
