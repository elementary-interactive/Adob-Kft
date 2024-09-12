import pandas as pd
from contextlib import contextmanager
from db_connection import connect_to_db
from columns import column_mapping  # Correct import statement
import humanize


@contextmanager
def db_connection():
    try:
        db_conn = connect_to_db()
        if not db_conn.is_connected():
            raise AssertionError("Could not connect to the database")
        print("Successfully connected to the database")
        cursor = db_conn.cursor(dictionary=True)
        yield cursor
    # except mysql.connector.Error as err:
    #     raise AssertionError(f"Error: {err}")
    finally:
        try:
            cursor.close()
        except NameError:
            pass
        db_conn.close()


def size_format(bytes):
    return humanize.naturalsize(bytes, binary=True)

# Function to export products in chunks
def export_products_to_excel(chunk_size=20000, output_file="products.xlsx"):
    # Connect to the database
    with db_connection() as cursor:
        # Initialize an empty DataFrame
        all_products_df = pd.DataFrame()

        # Query total products count
        cursor.execute("SELECT COUNT(*) AS total FROM products")
        total_products = cursor.fetchone()["total"]
        print(f"Total products to export: {total_products}")

        # Get the list of columns to keep
        columns_to_keep = list(column_mapping.keys())

        # Iterate over chunks
        for offset in range(0, total_products, chunk_size):
            # Fetch chunk of data with JOIN to include brand name and image details
            cursor.execute(f"""
                SELECT p.*, b.name as brand_name, p.slug,
                       (SELECT COUNT(*) FROM media WHERE media.model_id = p.id) as image_count,
                       GROUP_CONCAT(media.file_name) as file_names,
                       GROUP_CONCAT(media.size) as sizes,
                       GROUP_CONCAT(media.mime_type) as mime_types
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN media ON media.model_id = p.id
                GROUP BY p.id
                LIMIT {chunk_size} OFFSET {offset}
            """)
            products = cursor.fetchall()

            # Convert to DataFrame and filter columns
            chunk_df = pd.DataFrame(products)
            #print("Columns in chunk_df before filtering:", chunk_df.columns.tolist())

            #hunk_df = chunk_df[columns_to_keep] # + ['image_count', 'file_names', 'sizes', 'mime_types']]
            #print("Columns in chunk_df after filtering:", chunk_df.columns.tolist())

            # Generate columns for image file names, sizes, and mime types
            #chunk_df['image_sizes'] = chunk_df['file_names'].apply(lambda x: x.split(",") if x else [])
            chunk_df['image_sizes'] = chunk_df.apply(
                lambda row: "; ".join(
                    [f"{name} ({size_format(int(size.strip()))})" for name, size in zip(row['file_names'].split(","), row['sizes'].split(","))]
                ) if row['file_names'] else '',
                axis=1
            )


            # Generate URL column
            chunk_df['url'] = chunk_df['slug'].apply(lambda x: f"http://localhost/termek/{x}")

            chunk_df = chunk_df[columns_to_keep]
            #print("Columns in chunk_df after filtering:", chunk_df.columns.tolist())

            # Convert status column to 0 or 1
            chunk_df['status'] = chunk_df['status'].apply(lambda x: '1' if x == 'A' else '0')

            # Rename columns
            chunk_df.rename(columns=column_mapping, inplace=True)

            all_products_df = pd.concat([all_products_df, chunk_df], ignore_index=True)

            print(f"Exported {len(all_products_df)}/{total_products} products...")

    # Write the entire DataFrame to a single sheet in the Excel file
    all_products_df.to_excel(output_file, sheet_name="termékek", index=False)

    print(f"Export completed. File saved as {output_file}.")


if __name__ == "__main__":
    export_products_to_excel()
