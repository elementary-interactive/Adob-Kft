#!/bin/bash

# Navigate to the script's directory
cd "${0%/*}"

# Activate the virtual environment
source venv/bin/activate

# Run the Python script with the output file argument
python export.py --output_file "$OUTPUT_FILE" --app_url "$APP_URL"

# Deactivate the virtual environment
deactivate
