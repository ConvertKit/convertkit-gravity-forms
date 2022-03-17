# Build ACTIONS-FILTERS.md
php create-actions-filters-docs.php

# Generate .pot file
php -n $(which wp) i18n make-pot ../ ../languages/convertkit.pot

# Build ZIP file
rm ../convertkit-gravity-forms.zip
cd .. && zip -r convertkit-gravity-forms.zip . -x "*.git*" -x ".scripts/*" -x ".wordpress-org/*" -x "tests/*" -x "vendor/*" -x "*.distignore" -x "*.env.*" -x "*codeception.*" -x "composer.json" -x "composer.lock" -x "*.md" -x "log.txt" -x "phpcs.xml" -x "*.neon.*" -x "*.DS_Store"