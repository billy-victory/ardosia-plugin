#!/bin/bash

# Ardosia Plugin Deployment Script
# This script packages only the essential files needed for the WordPress plugin

# Configuration
PLUGIN_NAME="ardosia-plugin"
VERSION=$(grep "Version:" ardosia-plugin.php | sed 's/.*Version: *\([0-9.]*\).*/\1/')
OUTPUT_DIR="../${PLUGIN_NAME}-${VERSION}"
SRC_DIR="."

# Ensure we're in the plugin directory
if [ ! -f "ardosia-plugin.php" ]; then
    echo "Error: Must be run from the plugin root directory"
    exit 1
fi

# Create output directory
echo "Creating deployment package for ${PLUGIN_NAME} v${VERSION}..."
rm -rf "${OUTPUT_DIR}"
mkdir -p "${OUTPUT_DIR}"

# Build the plugin assets
echo "Building assets with Vite..."
npm run build || pnpm run build || yarn build

# Copy only the essential files
echo "Copying essential plugin files..."

# Main plugin file
cp "${SRC_DIR}/ardosia-plugin.php" "${OUTPUT_DIR}/"
cp -R "${SRC_DIR}/inc" "${OUTPUT_DIR}/"
cp -R "${SRC_DIR}/vendor" "${OUTPUT_DIR}/"
cp -R "${SRC_DIR}/dist" "${OUTPUT_DIR}/"
cp "${SRC_DIR}/README.md" "${OUTPUT_DIR}/"

# Clean up any development files that might have been copied
find "${OUTPUT_DIR}" -name ".git*" -exec rm -rf {} \; 2>/dev/null || true
find "${OUTPUT_DIR}" -name "*.map" -exec rm -f {} \; 2>/dev/null || true

# Create zip file
echo "Creating zip archive..."
cd ..
zip -r "${PLUGIN_NAME}-${VERSION}.zip" "${PLUGIN_NAME}-${VERSION}" -x "*.DS_Store" -x "__MACOSX/*"

echo "Deployment package created: ${PLUGIN_NAME}-${VERSION}.zip"
echo "Package location: $(cd .. && pwd)/${PLUGIN_NAME}-${VERSION}.zip"