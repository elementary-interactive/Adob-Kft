import mysql.connector
import pandas as pd

db_config = {
    'host': 'adobkft_db',        # Replace with your MySQL host (could be 'db' or 'mysql' in Docker)
    'user': 'root',             # Replace with your MySQL username
    'password': 'your_mysql_root_password', # Replace with your MySQL password
    'database': 'adobkft'  # Replace with your database name
}
# Database connection
def connect_to_db():
    return mysql.connector.connect(**db_config)

# Function to export products in chunks
def export_products_to_excel(chunk_size=1000, output_file="products.xlsx"):
    # Connect to the database
    db_conn = connect_to_db()
    try:
        if db_conn.is_connected():
            print("Successfully connected to the database")
            cursor = db_conn.cursor(dictionary=True)

            # Initialize Excel writer
            writer = pd.ExcelWriter(output_file, engine='openpyxl')

            # Query total products count
            cursor.execute("SELECT COUNT(*) AS total FROM products")
            total_products = cursor.fetchone()["total"]
            print(f"Total products to export: {total_products}")

            # Iterate over chunks
            offset = 0
            while offset < total_products:
                # Fetch chunk of data
                cursor.execute(f"SELECT * FROM products LIMIT {chunk_size} OFFSET {offset}")
                products = cursor.fetchall()

                # Convert to DataFrame
                df = pd.DataFrame(products)

                # Append the chunk to Excel file
                df.to_excel(writer, sheet_name=f"Chunk_{offset // chunk_size + 1}", index=False)

                # Move offset
                offset += chunk_size
                print(f"Exported {offset}/{total_products} products...")

            # Save and close the Excel file
            writer.close()

            # Close the database connection
            cursor.close()
            db_conn.close()

            print(f"Export completed. File saved as {output_file}.")

    except mysql.connector.Error as err:
        print(f"Error: {err}")

if __name__ == "__main__":
    export_products_to_excel()
