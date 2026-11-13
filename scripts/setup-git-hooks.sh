#!/usr/bin/env sh
# Points git at the versioned hooks in .githooks/ (pre-push runs the test suite).
set -e

cd "$(dirname "$0")/.."
git config core.hooksPath .githooks
chmod +x .githooks/*
echo "Git hooks enabled: .githooks/pre-push will run the test suite before each push."
