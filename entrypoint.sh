#!/bin/bash
set -e

# Initialize MariaDB database directory if empty
if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo "Initializing MariaDB database directory..."
    mysql_install_db --datadir=/var/lib/mysql
fi

# Start MariaDB in background
echo "Starting MariaDB..."
mysqld --datadir=/var/lib/mysql --socket=/var/run/mysqld/mysqld.sock --port=3306 &

# Wait for MariaDB to start
echo "Waiting for MariaDB to start..."
until mysqladmin --socket=/var/run/mysqld/mysqld.sock ping --silent; do
    sleep 1
done
echo "MariaDB started successfully!"

# Import SQL database if not already imported
if ! mysql --socket=/var/run/mysqld/mysqld.sock -e "use agriculture_portal" 2>/dev/null; then
    echo "Creating database agriculture_portal..."
    mysql --socket=/var/run/mysqld/mysqld.sock -e "CREATE DATABASE agriculture_portal;"
    echo "Importing database schema and data..."
    mysql --socket=/var/run/mysqld/mysqld.sock agriculture_portal < "/var/www/html/db/agriculture_portal (Latest).sql"
    echo "Database import complete!"
else
    echo "Database already exists."
fi

# Start Apache in the foreground
echo "Starting Apache..."
apache2-foreground
