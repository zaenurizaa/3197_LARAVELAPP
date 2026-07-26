$exclude = 'vendor|node_modules|storage|\.git|public\\storage|bootstrap\\cache'
$files = Get-ChildItem -Recurse -File | Where-Object { $_.FullName -notmatch $exclude -and $_.Extension -in '.php','.json','.js','.css','.blade.php','.md','.env.example' }

"# Laravel Project Init / Context`n" | Out-File -FilePath INIT.md -Encoding utf8

foreach ($file in $files) {
    "## File: " + $file.FullName | Out-File -FilePath INIT.md -Append -Encoding utf8
    "```" + $file.Extension.TrimStart('.') | Out-File -FilePath INIT.md -Append -Encoding utf8
    Get-Content $file.FullName -Raw | Out-File -FilePath INIT.md -Append -Encoding utf8
    "````n" | Out-File -FilePath INIT.md -Append -Encoding utf8
}
