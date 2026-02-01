# .githooks/pre-commit.ps1 - For Windows users (PowerShell)
try {
    $root = (git rev-parse --show-toplevel) 2>$null
    if (-not $root) { Write-Output "Cannot find git repo root."; exit 0 }
    $script = Join-Path $root "scripts\php-lint.php"
    if (-not (Test-Path $script)) { Write-Output "Lint script not found: $script"; exit 0 }

    & php $script
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Pre-commit: PHP lint failed. Fix errors before committing."
        exit $LASTEXITCODE
    }
} catch {
    Write-Error "Pre-commit hook error: $_"
    exit 1
}
exit 0
