#!/bin/bash

# --- CONFIG ---
PLUGIN_FILE="ardosia-plugin.php"

# --- READ CURRENT VERSION FROM PLUGIN HEADER ---
CURRENT_VERSION=$(grep -oP '^\s*\*\s*Version:\s*\K[0-9]+\.[0-9]+\.[0-9]+' "$PLUGIN_FILE")

# --- ASK FOR NEW VERSION ---
echo "Current version is $CURRENT_VERSION"
read -p "Enter new version (e.g., 1.0.3): " NEW_VERSION

# --- UPDATE VERSION IN PLUGIN HEADER ---
sed -i.bak -E "s/(\*\s*Version:\s*)${CURRENT_VERSION}/\1${NEW_VERSION}/" "$PLUGIN_FILE"

# --- UPDATE VERSION CONSTANT ---
sed -i.bak -E "s/(define\s*\(\s*'PPS_MODULES_VERSION',\s*')${CURRENT_VERSION}'/\1${NEW_VERSION}'/" "$PLUGIN_FILE"

# --- CLEANUP BACKUP FILE ---
rm "${PLUGIN_FILE}.bak"

# --- GIT COMMIT AND TAG ---
git add "$PLUGIN_FILE"
git commit -m "Release ${NEW_VERSION}"
git tag "${NEW_VERSION}"
git push origin main --tags

echo "✅ Version bumped to $NEW_VERSION and pushed to GitHub."