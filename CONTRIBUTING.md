# Contributing

## Install Git Hooks (local)
To enable the pre-commit PHP lint hook locally, run one of the following from the repository root:

- Unix / Git Bash:

  ```sh
  sh scripts/install-git-hooks.sh
  ```

- Windows PowerShell:

  ```powershell
  .\scripts\install-git-hooks.ps1
  ```

The hook will run `php -l` on staged PHP files (or all PHP files if none are staged) and will block commits if syntax errors are found.

If you prefer, you can run the linter manually:

```sh
php scripts/php-lint.php
```
