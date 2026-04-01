# Use the official PHP 8.2 CLI image as base
FROM php:8.2-cli

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libmariadb-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy the project files to the container
COPY . .

# Expose port 8000
EXPOSE 8000

# Run the project using PHP's built-in server
# We use 0.0.0.0 to allow access from outside the container
CMD ["php", "-S", "0.0.0.0:8000", "-t", "."]
