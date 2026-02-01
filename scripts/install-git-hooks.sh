#!/bin/sh
# scripts/install-git-hooks.sh
ROOT=$(git rev-parse --show-toplevel 2>/dev/null)
if [ -z "$ROOT" ]; then
    echo "Not a git repository."
    exit 1
fi
HOOKS_DIR="$ROOT/.git/hooks"
GITHOOKS_DIR="$ROOT/.githooks"

if [ ! -d "$GITHOOKS_DIR" ]; then
    echo "No .githooks directory found."
    exit 1
fi

# Copy hooks
cp "$GITHOOKS_DIR/pre-commit" "$HOOKS_DIR/pre-commit"
chmod +x "$HOOKS_DIR/pre-commit"

# If Windows PowerShell hook exists, leave it as .ps1 adjacent for manual use
if [ -f "$GITHOOKS_DIR/pre-commit.ps1" ]; then
    echo "PowerShell hook available at $GITHOOKS_DIR/pre-commit.ps1 (Windows users can configure this manually)."
fi

echo "Git hooks installed. pre-commit will run PHP lint."