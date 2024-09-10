import pandas as pd
from db_connection import connect_to_db
from columns import column_mapping  # Correct import statement

# Function to export products in chunks
def export_products_to_excel(chunk_size=1000, output_file="products.xlsx"):
    # Connect to the database
    db_conn = connect_to_db()
    try:
        if db_conn.is_connected():
            print("Successfully connected to the database")
            cursor = db_conn.cursor(dictionary=True)

            # Initialize an empty DataFrame
            all_products_df = pd.DataFrame()

            # Query total products count
            cursor.execute("SELECT COUNT(*) AS total FROM products")
            total_products = cursor.fetchone()["total"]
            print(f"Total products to export: {total_products}")


            # Get the list of columns to keep
            columns_to_keep = list(column_mapping.keys())

            # Iterate over chunks
            offset = 0
            while offset < total_products:
                # Fetch chunk of data with JOIN to include brand name
                cursor.execute(f"""
                    SELECT p.*, b.name as brand_name, p.slug
                    FROM products p
                    LEFT JOIN brands b ON p.brand_id = b.id
                    LIMIT {chunk_size} OFFSET {offset}
                """)
                products = cursor.fetchall()

                # Convert to DataFrame and filter columns
                chunk_df = pd.DataFrame(products)
                #print("Columns in chunk_df before filtering:", chunk_df.columns.tolist())

                # Generate URL column
                chunk_df['url'] = chunk_df['slug'].apply(lambda x: f"http://localhost/termek/{x}")

                chunk_df = chunk_df[columns_to_keep]
                #print("Columns in chunk_df after filtering:", chunk_df.columns.tolist())

                # Rename columns
                chunk_df.rename(columns=column_mapping, inplace=True)

                all_products_df = pd.concat([all_products_df, chunk_df], ignore_index=True)

                # Move offset
                offset += chunk_size
                print(f"Exported {offset}/{total_products} products...")

            # Write the entire DataFrame to a single sheet in the Excel file
            all_products_df.to_excel(output_file, sheet_name="Products", index=False)

            # Close the database connection
            cursor.close()
            db_conn.close()

            print(f"Export completed. File saved as {output_file}.")

    except mysql.connector.Error as err:
        print(f"Error: {err}")

if __name__ == "__main__":
    export_products_to_excel()
