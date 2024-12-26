from db_connection import db_connection

def test_db():
    try:
        with db_connection() as cursor:
            cursor.execute("SELECT DATABASE()")
            result = cursor.fetchone()
            print(f"Connected to database: {result['DATABASE()']}")
    except AssertionError as e:
        print(f"Test failed: {e}")

if __name__ == "__main__":
    test_db()
