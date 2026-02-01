# scripts/install-git-hooks.ps1
$root = git rev-parse --show-toplevel 2>$null
if (-not $root) { Write-Error "Not a git repository."; exit 1 }
$hooksDir = Join-Path $root ".git\hooks"
$githooksDir = Join-Path $root ".githooks"
if (-not (Test-Path $githooksDir)) { Write-Error "No .githooks directory found."; exit 1 }

# Copy the Bash pre-commit for Git on Windows (Git for Windows supports sh)
Copy-Item (Join-Path $githooksDir 'pre-commit') (Join-Path $hooksDir 'pre-commit') -Force
# Make sure file is executable (Git for Windows respects executable bit via MSYS)
# No direct chmod in PowerShell; set attributes
$target = Join-Path $hooksDir 'pre-commit'
if (Test-Path $target) {
    icacls $target /grant "${env:USERNAME}:(RX)" | Out-Null
}

Write-Output "Git hooks installed. pre-commit will run PHP lint. For PowerShell-only environments consider using .githooks/pre-commit.ps1 manually."