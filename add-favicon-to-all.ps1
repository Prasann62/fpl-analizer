# PowerShell script to add favicon to all PHP pages
$phpFiles = Get-ChildItem -Path "e:\f" -Filter "*.php" -File

$faviconInclude = '  <?php include ''favicon-meta.php''; ?>'

foreach ($file in $phpFiles) {
    # Skip the favicon-meta.php itself and other utility files
    if ($file.Name -in @('favicon-meta.php', 'api.php', 'logout.php', 'navbar.php', 'sidebar.php', 'save_manager.php', 'db_update.php')) {
        Write-Host "Skipping: $($file.Name)" -ForegroundColor Yellow
        continue
    }
    
    $content = Get-Content $file.FullName -Raw
    
    # Check if favicon is already included
    if ($content -match 'favicon-meta\.php') {
        Write-Host "Already has favicon: $($file.Name)" -ForegroundColor Green
        continue
    }
    
    # Check if file has a <head> section
    if ($content -match '<head>') {
        # Find the position after viewport meta tag or after <head>
        if ($content -match '(?s)(<meta name="viewport"[^>]*>)') {
            $newContent = $content -replace '(<meta name="viewport"[^>]*>)', "`$1`r`n$faviconInclude"
            Set-Content -Path $file.FullName -Value $newContent -NoNewline
            Write-Host "Added favicon to: $($file.Name)" -ForegroundColor Cyan
        }
        elseif ($content -match '(?s)(<head>)') {
            $newContent = $content -replace '(<head>\s*)', "`$1`r`n$faviconInclude`r`n"
            Set-Content -Path $file.FullName -Value $newContent -NoNewline
            Write-Host "Added favicon to: $($file.Name) (after <head>)" -ForegroundColor Cyan
        }
    }
    else {
        Write-Host "No <head> tag found in: $($file.Name)" -ForegroundColor Red
    }
}

Write-Host "`nFavicon update complete!" -ForegroundColor Green
