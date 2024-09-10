import mysql.connector

# Database connection details
db_config = {
    'host': 'adobkft_db',        # Replace with your MySQL host (could be 'db' or 'mysql' in Docker)
    'user': 'root',             # Replace with your MySQL username
    'password': 'your_mysql_root_password', # Replace with your MySQL password
    'database': 'adobkft'  # Replace with your database name
}

try:
    # Establishing connection to the database
    conn = mysql.connector.connect(**db_config)

    if conn.is_connected():
        print("Successfully connected to the database")

        # Create a cursor to interact with the database
        cursor = conn.cursor()

        # Execute the query to count products
        cursor.execute("SELECT COUNT(*) FROM products")

        # Fetch and print the result
        product_count = cursor.fetchone()[0]
        print(f"Total products: {product_count}")

        # Close the cursor and connection
        cursor.close()
        conn.close()

except mysql.connector.Error as err:
    print(f"Error: {err}")
