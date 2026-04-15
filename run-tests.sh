#!/bin/bash

# Jankx UX Extension Test Runner
# Usage: ./run-tests.sh [options]

set -e

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo "🧪 Running Jankx UX Extension Tests..."

# Check if composer dependencies are installed
if [ ! -d "vendor" ]; then
    echo "📦 Installing dependencies..."
    composer install --dev
fi

# Run PHPUnit tests
echo ""
echo "Running PHPUnit tests..."
./vendor/bin/phpunit --testdox

# Check exit code
if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}✅ All tests passed!${NC}"
    exit 0
else
    echo ""
    echo -e "${RED}❌ Some tests failed.${NC}"
    exit 1
fi
