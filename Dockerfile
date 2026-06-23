FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    mariadb-server \
    python3 \
    python3-pip \
    python-is-python3 \
    libmariadb-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Install Python ML libraries (using --break-system-packages since we are in a container)
RUN pip install --no-cache-dir --break-system-packages \
    tensorflow-cpu \
    numpy \
    pandas \
    scikit-learn \
    joblib \
    flask \
    flask-cors \
    matplotlib

# Copy project files
COPY . /var/www/html/

# Pre-train the yield prediction model during build so it is cached and loads instantly
RUN python /var/www/html/farmer/ML/yield_prediction/yield_prediction.py

# Configure Apache for Hugging Face Spaces (port 7860)
RUN sed -i 's/Listen 80/Listen 7860/g' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:7860>/g' /etc/apache2/sites-available/000-default.conf

# Setup permissions for non-root execution (Hugging Face default user 1000)
RUN mkdir -p /var/lib/mysql /var/run/mysqld /var/log/mysql /var/run/apache2 /var/log/apache2 /var/lock/apache2 && \
    chown -R 1000:1000 /var/lib/mysql /var/run/mysqld /var/log/mysql /var/run/apache2 /var/log/apache2 /var/lock/apache2 /var/www/html && \
    chmod -R 777 /var/lib/mysql /var/run/mysqld /var/log/mysql /var/run/apache2 /var/log/apache2 /var/lock/apache2 /var/www/html

# Expose port 7860
EXPOSE 7860

# Set user and entrypoint
USER 1000
COPY entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
