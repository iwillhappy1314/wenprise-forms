#!/bin/bash

# Wenprise Forms Test Runner
# This script runs all tests for the Wenprise Forms plugin

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== Wenprise Forms Test Suite ===${NC}"
echo ""

# Check if PHPUnit is available
if ! command -v phpunit &> /dev/null; then
    echo -e "${RED}PHPUnit is not installed or not in PATH${NC}"
    echo "Please install PHPUnit: https://phpunit.de/getting-started.html"
    exit 1
fi

# Check if WordPress test environment is set up
if [ -z "$WP_TESTS_DIR" ]; then
    echo -e "${YELLOW}WP_TESTS_DIR environment variable not set${NC}"
    echo "Setting default: /tmp/wordpress-tests-lib"
    export WP_TESTS_DIR="/tmp/wordpress-tests-lib"
fi

if [ ! -d "$WP_TESTS_DIR" ]; then
    echo -e "${RED}WordPress test library not found at $WP_TESTS_DIR${NC}"
    echo "Please run: bin/install-wp-tests.sh"
    exit 1
fi

# Get the directory of this script
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PLUGIN_DIR="$(dirname "$DIR")"

# Change to plugin directory
cd "$PLUGIN_DIR"

echo -e "${GREEN}Running all tests...${NC}"
echo ""

# Run specific test suites with descriptive output
echo -e "${YELLOW}1. Form Validation Tests${NC}"
phpunit tests/test-form-validation.php --colors=always

echo ""
echo -e "${YELLOW}2. Security Tests${NC}"
phpunit tests/test-security.php --colors=always

echo ""
echo -e "${YELLOW}3. Datastore Tests${NC}"
phpunit tests/test-datastores.php --colors=always

echo ""
echo -e "${YELLOW}4. Input Controls Tests${NC}"
phpunit tests/test-controls.php --colors=always

echo ""
echo -e "${YELLOW}5. Integration Tests${NC}"
phpunit tests/test-integration.php --colors=always

echo ""
echo -e "${YELLOW}6. Performance Tests${NC}"
phpunit tests/test-performance.php --colors=always

echo ""
echo -e "${YELLOW}7. Original Tests${NC}"
phpunit tests/test-csrf.php tests/test-editor.php tests/test-group.php tests/test-upload.php --colors=always

echo ""
echo -e "${GREEN}=== Test Suite Complete ===${NC}"

# Run all tests together for coverage
echo ""
echo -e "${YELLOW}Running complete test suite...${NC}"
phpunit --colors=always

exit_code=$?

if [ $exit_code -eq 0 ]; then
    echo -e "${GREEN}All tests passed!${NC}"
else
    echo -e "${RED}Some tests failed. Exit code: $exit_code${NC}"
fi

exit $exit_code