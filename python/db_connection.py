import mysql.connector
from contextlib import contextmanager

db_config = {
    'host': 'adobkft_db',        # Replace with your MySQL host (could be 'db' or 'mysql' in Docker)
    'user': 'root',             # Replace with your MySQL username
    'password': 'your_mysql_root_password', # Replace with your MySQL password
    'database': 'adobkft'  # Replace with your database name
}

def connect_to_db():
    return mysql.connector.connect(**db_config)


@contextmanager
def db_connection():
    try:
        db_conn = connect_to_db()
        if not db_conn.is_connected():
            raise AssertionError("Could not connect to the database")
        print("Successfully connected to the database")
        cursor = db_conn.cursor(dictionary=True)
        yield cursor
    except mysql.connector.Error as err:
        raise AssertionError(f"Error: {err}")
    finally:
        try:
            cursor.close()
        except NameError:
            pass
        db_conn.close()
