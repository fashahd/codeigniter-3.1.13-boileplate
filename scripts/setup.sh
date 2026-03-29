set -e
echo "Setting up the CodeIgniter application..."

#Set git hooks
echo "Setting up git hooks..."
git config core.hooksPath .githooks
echo "Git hooks set up successfully."
echo "Setting Permissions..."
chmod +x .githooks/pre-commit
echo "Permissions set successfully."

# Install PHP dependencies using Composer
echo "Installing PHP dependencies using Composer..."
if [ ! -d "vendor" ]; then
    composer install
else
    echo "PHP dependencies already installed. Skipping Composer install."
fi

# Create .env file if it doesn't exist
if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    cp .env.example .env
    echo ".env file created successfully."
else
    echo ".env file already exists. Skipping .env creation."
fi

echo "Setup completed successfully."