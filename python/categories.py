from db_connection import db_connection


def get_categories():
    # Connect to the database
    with db_connection() as cursor:
        # Query all categories
        cursor.execute("SELECT id, name, parent_id FROM categories")
        categories = cursor.fetchall()

        return categories

def build_category_tree(categories):
    # Create a dictionary to store categories by their ID
    category_dict = {category['id']: category for category in categories}

    # Create a hash map to store the full category path
    category_tree = {}

    def get_full_path(category_id):
        category = category_dict[category_id]
        if category['parent_id'] is None:
            return category['name']
        else:
            parent_path = get_full_path(category['parent_id'])
            return f"{parent_path}/{category['name']}"

    # Build the full path for each category
    for category in categories:
        category_tree[category['id']] = get_full_path(category['id'])

    return category_tree

# if __name__ == "__main__":
#     categories = get_categories()
#     category_tree = build_category_tree(categories)
#     for category_id, full_path in category_tree.items():
#         print(f"Category ID: {category_id}, Full Path: {full_path}")
