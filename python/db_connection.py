import mysql.connector
from contextlib import contextmanager
import os
from dotenv import load_dotenv

# Load environment variables from .env file
load_dotenv()

db_config = {
    'host': os.getenv('DB_HOST'),
    'user': os.getenv('DB_USERNAME'),
    'password': os.getenv('DB_PASSWORD'),
    'database': os.getenv('DB_DATABASE')
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
