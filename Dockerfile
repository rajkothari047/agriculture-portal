FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    mariadb-server \
    python3 \
    python3-pip \
    python-is-python3 \
    libmariadb-dev \
    wget \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Install Python ML libraries + gdown for Google Drive downloads
RUN pip install --no-cache-dir --break-system-packages \
    tensorflow-cpu \
    numpy \
    pandas \
    scikit-learn \
    joblib \
    flask \
    flask-cors \
    matplotlib \
    gdown

# Copy project files
COPY . /var/www/html/

# Download Disease Detection models folder from Google Drive
RUN mkdir -p /var/www/html/farmer/ML/DiseaseDetection/models && \
    gdown --folder "https://drive.google.com/drive/folders/1zam9yBqIW9BkWzDoyRIY7egN-wTIfznr" \
    -O /var/www/html/farmer/ML/DiseaseDetection/models

# Download compressed yield prediction model files from Google Drive
RUN mkdir -p /var/www/html/farmer/ML/yield_prediction && \
    gdown --id 1c9WVUHTO1_cc_HsopfKHY4n17Ryjuwj7 \
    -O /var/www/html/farmer/ML/yield_prediction/yield_model_compressed.pkl && \
    gdown --id 1g0EDXXW9dKZVKWgXkbiBmFMV06jrpd8d \
    -O /var/www/html/farmer/ML/yield_prediction/encoder_compressed.pkl

# Configure Apache for Hugging Face Spaces port 7860
RUN sed -i 's/Listen 80/Listen 7860/g' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:7860>/g' /etc/apache2/sites-available/000-default.conf

# Copy and prepare entrypoint
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Set permissions for Hugging Face non-root user
RUN mkdir -p /var/lib/mysql /var/run/mysqld /var/log/mysql /var/run/apache2 /var/log/apache2 /var/lock/apache2 && \
    chown -R 1000:1000 /var/lib/mysql /var/run/mysqld /var/log/mysql /var/run/apache2 /var/log/apache2 /var/lock/apache2 /var/www/html /entrypoint.sh && \
    chmod -R 777 /var/lib/mysql /var/run/mysqld /var/log/mysql /var/run/apache2 /var/log/apache2 /var/lock/apache2 /var/www/html /entrypoint.sh

# Expose Hugging Face Spaces port
EXPOSE 7860

# Run as Hugging Face default user
USER 1000

# Start MariaDB + Apache
ENTRYPOINT ["/entrypoint.sh"]