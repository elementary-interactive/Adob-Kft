import mysql.connector

db_config = {
    'host': 'adobkft_db',        # Replace with your MySQL host (could be 'db' or 'mysql' in Docker)
    'user': 'root',             # Replace with your MySQL username
    'password': 'your_mysql_root_password', # Replace with your MySQL password
    'database': 'adobkft'  # Replace with your database name
}

def connect_to_db():
    return mysql.connector.connect(**db_config)
